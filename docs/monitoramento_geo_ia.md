# Monitoramento GEO / AI Referral Tracking

Este documento serve como especificação reutilizável para adicionar monitoramento de acessos vindos de assistentes de IA e preparar um projeto para evoluir futuramente para algo semelhante a um **Ahrefs Brand Radar**.

## Objetivos

O monitoramento deve responder a duas perguntas diferentes:

1. **Quais acessos reais ao site vieram de ferramentas de IA?**
2. **Em quais respostas de IA minha marca/domínio está sendo mencionado ou citado?**

Esses dois problemas são diferentes e devem ser tratados separadamente.

---

# Parte 1 — Monitoramento de acessos vindos de IA no próprio site

## O que é possível identificar

Quando um usuário clica em um link mostrado por uma IA, o site pode receber informações como:

- `Referer` HTTP;
- parâmetros UTM;
- URL de entrada;
- User-Agent;
- data/hora;
- IP anonimizado ou hash, se necessário;
- sessão;
- página acessada;
- conversão realizada depois da visita.

Nem toda visita de IA terá um referer identificável. Algumas plataformas, navegadores e configurações de privacidade podem remover ou reduzir essas informações.

Para ChatGPT Search, a OpenAI informa que links de referência podem incluir:

```text
utm_source=chatgpt.com
```

Portanto, o detector nunca deve depender somente do `HTTP_REFERER`. Ele deve analisar também UTMs.

---

## Fontes de IA a monitorar

Manter uma lista configurável semelhante a esta:

```php
$aiSources = [
    'chatgpt' => [
        'chatgpt.com',
        'openai.com'
    ],

    'gemini' => [
        'gemini.google.com'
    ],

    'claude' => [
        'claude.ai'
    ],

    'perplexity' => [
        'perplexity.ai'
    ],

    'copilot' => [
        'copilot.microsoft.com',
        'bing.com'
    ],

    'deepseek' => [
        'chat.deepseek.com',
        'deepseek.com'
    ],

    'grok' => [
        'grok.com',
        'x.com'
    ]
];
```

Essa configuração deve ficar fora do código principal para facilitar atualizações.

---

# Banco de dados

Criar uma tabela própria para entradas vindas de IA.

Exemplo MySQL:

```sql
CREATE TABLE ai_referrals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    session_id VARCHAR(100) NULL,

    ai_source VARCHAR(50) NULL,

    landing_page TEXT NULL,
    request_uri TEXT NULL,
    referrer TEXT NULL,

    utm_source VARCHAR(255) NULL,
    utm_medium VARCHAR(255) NULL,
    utm_campaign VARCHAR(255) NULL,
    utm_content VARCHAR(255) NULL,
    utm_term VARCHAR(255) NULL,

    user_agent TEXT NULL,

    ip_hash VARCHAR(64) NULL,

    converted TINYINT(1) NOT NULL DEFAULT 0,
    conversion_type VARCHAR(100) NULL,
    conversion_value DECIMAL(12,2) NULL,

    created_at DATETIME NOT NULL,

    INDEX idx_source (ai_source),
    INDEX idx_created (created_at),
    INDEX idx_session (session_id),
    INDEX idx_converted (converted)
);
```

Evitar armazenar IP puro se ele não for necessário. Para análise agregada, preferir hash.

Exemplo:

```php
$ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . APP_SECRET);
```

---

# Detector de tráfego de IA

Criar um serviço como:

```text
AiReferralDetector
```

Ele deverá receber:

```text
referrer
utm_source
utm_medium
utm_campaign
user_agent
request_uri
```

E retornar:

```php
[
    'is_ai' => true,
    'source' => 'chatgpt'
]
```

ou:

```php
[
    'is_ai' => false,
    'source' => null
]
```

---

## Ordem de detecção recomendada

### 1. UTM explícita

Prioridade maior.

Exemplo:

```php
if (($utmSource ?? '') === 'chatgpt.com') {
    return [
        'is_ai' => true,
        'source' => 'chatgpt'
    ];
}
```

Outras combinações possíveis:

```text
utm_source=chatgpt.com
utm_source=perplexity
utm_source=claude
utm_source=gemini
utm_medium=ai-assistant
```

---

### 2. Referer

Normalizar para lowercase e procurar pelos domínios configurados.

Exemplo:

```php
$referrer = strtolower($_SERVER['HTTP_REFERER'] ?? '');

if (str_contains($referrer, 'chatgpt.com')) {
    $source = 'chatgpt';
}
```

Não usar somente comparação exata de domínio, pois URLs podem conter caminhos e subdomínios.

---

### 3. Parâmetros auxiliares

Caso futuramente alguma IA adicione identificadores próprios, permitir regras adicionais.

---

# Middleware recomendado

Para Laravel:

```text
app/Http/Middleware/TrackAiReferral.php
```

Fluxo:

```text
Request
   ↓
TrackAiReferral
   ↓
AiReferralDetector
   ↓
É IA?
   ├─ não → segue request
   └─ sim
       ↓
   salva ai_referrals
       ↓
   grava session('ai_referral_id')
       ↓
   segue request
```

Exemplo conceitual:

```php
public function handle($request, Closure $next)
{
    $result = app(AiReferralDetector::class)->detect($request);

    if ($result['is_ai']) {
        $referral = AiReferral::create([
            'session_id' => session()->getId(),
            'ai_source' => $result['source'],
            'landing_page' => $request->fullUrl(),
            'request_uri' => $request->getRequestUri(),
            'referrer' => $request->headers->get('referer'),
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content' => $request->query('utm_content'),
            'utm_term' => $request->query('utm_term'),
            'user_agent' => $request->userAgent(),
            'ip_hash' => hash('sha256', $request->ip() . config('app.key')),
            'created_at' => now(),
        ]);

        session([
            'ai_referral_id' => $referral->id,
            'ai_source' => $result['source'],
        ]);
    }

    return $next($request);
}
```

Adaptar para a versão atual do Laravel do projeto.

---

# CodeIgniter 3

Em CI3, criar algo equivalente em:

```text
application/libraries/AiReferralTracker.php
```

ou utilizar um Hook executado antes/depois do controller.

Sugestão:

```text
application/hooks/AiReferralHook.php
```

O Hook deverá:

1. analisar `$_SERVER['HTTP_REFERER']`;
2. analisar `$_GET['utm_source']` e demais UTMs;
3. detectar a origem;
4. criar um ID na session;
5. registrar somente a primeira landing page da sessão.

Evitar criar um novo registro para cada pageview.

---

# Controle de sessão

Esse ponto é importante.

Um clique vindo do ChatGPT pode entrar em:

```text
/artigo/software-para-clinicas
```

Depois navegar por:

```text
/precos
/cadastro
/checkout
```

A origem deve continuar sendo ChatGPT.

Portanto, ao detectar a IA, gravar:

```text
session.ai_source
session.ai_referral_id
```

Toda conversão posterior poderá ser atribuída a essa origem.

---

# Conversões

Criar função genérica:

```text
markAiConversion()
```

Exemplo:

```php
markAiConversion(
    type: 'cadastro',
    value: null
);
```

ou:

```php
markAiConversion(
    type: 'assinatura',
    value: 99.90
);
```

Se existir `ai_referral_id` na sessão:

```sql
UPDATE ai_referrals
SET
    converted = 1,
    conversion_type = ?,
    conversion_value = ?
WHERE id = ?
```

Para sistemas mais robustos, criar tabela separada:

```text
ai_conversions
```

permitindo múltiplas conversões por sessão.

---

# Dashboard administrativo

Criar no painel do próprio sistema uma área:

```text
Marketing
  └── Tráfego de IA
```

Mostrar:

## Cards

```text
Acessos IA hoje
Acessos IA 7 dias
Acessos IA 30 dias
Conversões IA
Taxa de conversão IA
Receita atribuída à IA
```

## Tráfego por IA

```text
ChatGPT
Gemini
Claude
Perplexity
Copilot
DeepSeek
Grok
Outros
```

## Landing pages mais acessadas

Exemplo:

```text
/artigo/gestao-para-psicologos     87
/precos                            41
/software-fisioterapeuta           36
/                                  28
```

## Conversão por origem

```text
ChatGPT
  visitas: 180
  cadastros: 14
  conversão: 7,77%

Perplexity
  visitas: 42
  cadastros: 6
  conversão: 14,28%
```

---

# API interna

Criar endpoints administrativos como:

```text
GET /api/admin/ai-traffic/summary
GET /api/admin/ai-traffic/sources
GET /api/admin/ai-traffic/pages
GET /api/admin/ai-traffic/conversions
GET /api/admin/ai-traffic/timeline
```

Filtros:

```text
start_date
end_date
source
landing_page
converted
```

---

# SQL de relatórios

## Tráfego por fonte

```sql
SELECT
    ai_source,
    COUNT(*) total
FROM ai_referrals
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY ai_source
ORDER BY total DESC;
```

## Landing pages

```sql
SELECT
    landing_page,
    COUNT(*) total
FROM ai_referrals
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY landing_page
ORDER BY total DESC
LIMIT 50;
```

## Conversão

```sql
SELECT
    ai_source,
    COUNT(*) visits,
    SUM(converted) conversions,
    ROUND((SUM(converted) / COUNT(*)) * 100, 2) conversion_rate
FROM ai_referrals
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY ai_source;
```

---

# Google Analytics 4

O sistema próprio não substitui o GA4.

Usar os dois em paralelo.

O GA4 possui um canal padrão chamado:

```text
AI Assistant
```

E pode classificar sessões com:

```text
medium = ai-assistant
```

Também é possível criar grupos de canais personalizados para assistentes de IA.

Comparar periodicamente os dados do banco próprio com GA4 para encontrar diferenças.

---

# Parte 2 — Brand Radar próprio

Essa etapa é diferente de analisar cliques.

Objetivo:

```text
Descobrir se ChatGPT/Gemini/Perplexity/etc.
recomendam ou citam nossa marca.
```

---

# Estrutura sugerida

```text
geo_projects
geo_prompts
geo_runs
geo_answers
geo_mentions
geo_citations
geo_competitors
```

---

## geo_projects

```text
id
name
domain
brand
country
language
created_at
```

---

## geo_prompts

```text
id
project_id
prompt
category
intent
active
created_at
```

Exemplos:

```text
qual o melhor sistema para psicólogos?

software para profissional de saúde autônomo

sistema de gestão para fisioterapeuta

melhor software de gestão clínica
```

---

## geo_runs

```text
id
project_id
prompt_id
provider
model
executed_at
```

Providers:

```text
openai
gemini
perplexity
claude
```

---

## geo_answers

```text
id
run_id
answer
raw_json
created_at
```

---

## geo_mentions

```text
id
run_id
brand
mentioned
position
sentiment
context
```

---

## geo_citations

```text
id
run_id
domain
url
position
```

---

# Fluxo do GEO Tracker

```text
Prompt
   ↓
Provider API
   ↓
Resposta
   ↓
Parser
   ↓
Detecta marca
   ↓
Detecta domínio
   ↓
Detecta concorrentes
   ↓
Registra resultado
```

---

# Limitação importante

Não assumir que executar uma API reproduz exatamente aquilo que um usuário verá no ChatGPT, Gemini ou outro produto.

Os resultados podem variar por:

```text
modelo
localização
idioma
personalização
histórico
produto utilizado
busca web ativada ou não
momento da consulta
```

Portanto o tracker deve ser tratado como:

```text
amostragem de visibilidade
```

e não como medição completa de todas as respostas reais mostradas aos usuários.

---

# Métricas do Brand Radar próprio

## Prompt Coverage

```text
prompts onde marca apareceu
--------------------------- x 100
prompts executados
```

---

## Citation Rate

```text
respostas que citaram domínio
----------------------------- x 100
respostas analisadas
```

---

## AI Share of Voice

Exemplo simplificado:

```text
menções da nossa marca
------------------------------ x 100
menções de todas marcas medidas
```

---

# GEO Score

Criar score interno de 0 a 100.

Primeira versão:

```text
GEO Score =

30% Prompt Coverage
25% Citation Rate
20% Share of Voice
15% posição/prominência
10% sentimento/contexto
```

Os pesos devem ser configuráveis.

Não apresentar esse score como métrica oficial de Google/OpenAI/Ahrefs.

---

# Agendamento

Não executar centenas de prompts a cada pageview.

Executar em worker/cron.

Exemplo:

```text
1x por dia
```

ou:

```text
2x por semana
```

Dependendo do número de prompts e custo das APIs.

Fluxo:

```text
Cron
 ↓
Queue
 ↓
Prompt
 ↓
Provider
 ↓
Parser
 ↓
Banco
```

---

# Laravel

Estrutura sugerida:

```text
app/
 ├── Services/
 │    ├── AiReferralDetector.php
 │    └── Geo/
 │         ├── GeoMonitor.php
 │         ├── BrandDetector.php
 │         ├── CitationDetector.php
 │         └── Providers/
 │              ├── OpenAIProvider.php
 │              ├── GeminiProvider.php
 │              └── PerplexityProvider.php
 │
 ├── Jobs/
 │    └── RunGeoPrompt.php
 │
 ├── Models/
 │    ├── AiReferral.php
 │    ├── GeoProject.php
 │    ├── GeoPrompt.php
 │    ├── GeoRun.php
 │    ├── GeoMention.php
 │    └── GeoCitation.php
```

---

# Segurança

Nunca colocar API keys no código.

Usar `.env`:

```text
OPENAI_API_KEY=
GEMINI_API_KEY=
PERPLEXITY_API_KEY=
ANTHROPIC_API_KEY=
```

Nunca versionar `.env`.

---

# Privacidade / LGPD

Evitar coletar dados pessoais que não sejam necessários para análise de aquisição.

Recomendações:

```text
não armazenar IP puro sem necessidade
usar hash quando possível
não salvar conteúdo privado do usuário
criar política de retenção
restringir dashboard a administradores
```

---

# Instruções para Claude Code

Ao receber este arquivo, Claude Code deverá primeiro analisar a arquitetura do repositório.

Antes de alterar código:

1. identificar framework e versão;
2. localizar middleware/hooks atuais;
3. localizar sistema de autenticação administrativa;
4. identificar banco e padrão de migrations;
5. localizar dashboard administrativo;
6. verificar se GA4 já está instalado;
7. verificar sistema atual de sessão;
8. verificar padrão de services/repositories/models.

Depois implementar em etapas.

---

# Etapa 1

Implementar somente:

```text
AI referral tracking
```

Incluindo:

```text
migration
model
detector
middleware/hook
persistência da sessão
```

Não criar GEO crawler nessa etapa.

---

# Etapa 2

Criar dashboard:

```text
Tráfego de IA
```

com:

```text
total de acessos
gráfico por período
origens
landing pages
conversões
```

Seguir o layout já utilizado no projeto.

---

# Etapa 3

Integrar conversões existentes do sistema.

Exemplos:

```text
cadastro
lead
checkout
assinatura
contato WhatsApp
formulário enviado
```

Não quebrar os fluxos existentes.

---

# Etapa 4

Somente após o tracker de tráfego estar estável, implementar:

```text
GEO / Brand Monitor
```

com prompts configuráveis e providers desacoplados.

---

# Regras para implementação pelo Claude Code

- Não modificar código não relacionado.
- Não remover funcionalidades existentes.
- Reutilizar componentes e padrões existentes.
- Criar migrations reversíveis.
- Evitar dependências desnecessárias.
- Não hardcodar domínio, marca ou providers.
- Todas as fontes de IA devem ser configuráveis.
- Não registrar a mesma landing de IA múltiplas vezes durante a mesma sessão.
- Separar aquisição (`ai_referrals`) de visibilidade GEO (`geo_*`).
- Criar logs de erro sem armazenar dados pessoais desnecessários.
- Criar testes do detector quando o projeto possuir infraestrutura de testes.

---

# Casos de teste mínimos

## ChatGPT via UTM

Entrada:

```text
https://site.com/artigo?utm_source=chatgpt.com
```

Resultado:

```text
is_ai = true
source = chatgpt
```

---

## ChatGPT via Referer

```text
Referer: https://chatgpt.com/
```

Resultado:

```text
chatgpt
```

---

## Perplexity

```text
Referer: https://www.perplexity.ai/
```

Resultado:

```text
perplexity
```

---

## Google Search tradicional

```text
Referer: https://www.google.com/search?q=...
```

Resultado:

```text
is_ai = false
```

Não classificar todo tráfego `google.com` como Gemini.

---

## Acesso direto

Sem Referer e sem UTM:

```text
is_ai = false
```

---

# Resultado esperado

Ao final da implementação básica, o administrador deverá conseguir responder:

```text
Quantas pessoas vieram de IA?

Qual IA enviou mais acessos?

Quais páginas estão sendo recomendadas?

Quantos desses usuários converteram?

Qual IA gera maior taxa de conversão?
```

Na etapa GEO, deverá também responder:

```text
Em quais prompts minha marca aparece?

Qual IA menciona mais a marca?

Qual concorrente aparece mais?

Quais páginas do meu domínio são citadas?

Meu AI Share of Voice está aumentando ou diminuindo?
```
