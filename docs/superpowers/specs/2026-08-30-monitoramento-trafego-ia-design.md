# Monitoramento de Tráfego de IA — Design

**Data:** 2026-08-30
**Fonte:** `docs/monitoramento_geo_ia.md` (Parte 1 — Etapas 1 a 3)
**Escopo:** Captar, registrar, atribuir conversões e exibir em dashboard (cards, gráficos e relatórios) os acessos ao site vindos de assistentes de IA (ChatGPT, Gemini, Claude, Perplexity, Copilot, DeepSeek, Grok).
**Fora de escopo:** Parte 2 do documento (GEO / Brand Radar — consultar APIs de IA para medir menções da marca). Registrada como fase futura no fim deste spec.

---

## 1. Objetivo

Ao final da implementação, o administrador (nível 1) deve conseguir responder:

- Quantas pessoas vieram de IA? (hoje / 7 dias / 30 dias)
- Qual IA enviou mais acessos?
- Quais páginas estão sendo recomendadas pelas IAs?
- Quantos desses usuários converteram (trial, assinatura, WhatsApp, contato)?
- Qual IA gera maior taxa de conversão?
- Qual a receita atribuída à IA?

---

## 2. Princípios / restrições

Seguir `docs/monitoramento_geo_ia.md` — "Regras para implementação":

- Não migrar framework. CodeIgniter 3.1.10, PHP 7, MySQL, padrões atuais do projeto.
- Não modificar código não relacionado. Não remover funcionalidade existente.
- Reutilizar componentes e padrões existentes (`Padrao_model`, `adm/Dev.php`, tema Adminto, Chart.js já vendorizado).
- Migração idempotente e reversível.
- Não hardcodar fontes de IA — lista em arquivo de config fora do código.
- Não registrar a mesma landing de IA múltiplas vezes na mesma sessão.
- Separar aquisição (`ai_referrals` / `ai_conversions`) de visibilidade GEO (`geo_*`, fase futura).
- LGPD: nunca gravar IP puro (usar hash), não gravar dado pessoal, dashboard só para administrador, política de retenção.

### Decisões arquiteturais fechadas com o dono do produto

1. **Escopo:** apenas Parte 1 (Etapas 1–3). Parte 2 (GEO) fica documentada como fase futura.
2. **Captura:** método novo em `Padrao_model` (`track_ai_referral()`), chamado ao lado de `indexador()` nos construtores dos controllers públicos. Não ativar hooks do CI3, não criar `MY_Controller`.
3. **Conversões atribuídas:** trial 30 dias, assinatura paga, clique em WhatsApp, envio de formulário de contato.
4. **Dashboard:** nova seção de menu "Marketing" → "Tráfego de IA", em controller novo `adm/Marketing`, restrito a nível 1.
5. **Modelo de dados:** `ai_referrals` (first-touch, com colunas de conversão inline) + `ai_conversions` (todo evento de conversão, permite funil trial → assinatura). — "1A".
6. **Persistência de atribuição:** sessão CI (first-touch) + cookie `utec_air` de 90 dias para conversões cross-session; re-hidratação da sessão a partir do cookie quando necessário. — "2A".

---

## 3. Arquitetura / fluxo

```
Pageview público (Home / Blog / SEO landings)
   └─ Padrao_model->indexador()            (já existe)
   └─ Padrao_model->track_ai_referral()    (NOVO — logo após indexador)
        ├─ detect_ai_source(referrer, utm): ordem UTM → Referer → regras aux
        ├─ não é IA  → nada
        └─ é IA      → se sessão/linha já existe p/ session_id: só garante cookie
                       senão: INSERT em `ai_referrals` (1x por sessão)
                              session.ai_referral_id / session.ai_source
                              cookie utec_air (id + source, 90 dias)

Conversão (trial / assinatura / whatsapp / contato)
   └─ Padrao_model->mark_ai_conversion($type, $value, $reference_id, $meta)
        ├─ re-hidrata ai_referral_id a partir do cookie se a sessão perdeu
        ├─ sem referral atribuível → retorna silenciosamente
        ├─ dedup por reference_id em `ai_conversions`
        ├─ INSERT em `ai_conversions` (todo evento)
        └─ UPDATE `ai_referrals` SET converted=1, conversion_type, conversion_value,
             converted_at  — SOMENTE na 1ª conversão (não sobrescreve)

Admin (nível 1)
   └─ adm/Marketing (controller novo, espelha adm/Leads)
        ├─ index() / trafego_ia() → view com cards resolvidos server-side
        └─ api($rel) → JSON p/ os gráficos (summary/sources/pages/conversions/timeline)
   └─ Marketing_model → queries agregadas
   └─ includes/adm/menu.php → seção "Marketing" (só $menu_can_admin)
```

Tudo em `track_ai_referral()` e `mark_ai_conversion()` é defensivo (`try/catch`), nunca interrompe o request nem o fluxo de conversão. Se as tabelas não existirem (`table_exists`), os métodos retornam sem erro.

---

## 4. Banco de dados

Método novo em `application/controllers/adm/Dev.php`: **`migrar_monitoramento_ia()`**, protegido por `nivel == 1`, idempotente (usa `table_exists`, `ensure_column`, `run_sql`), com desfazer via `?desfazer=1` (DROP das duas tabelas). Link adicionado no `index()` do `Dev`.

### 4.1 `ai_referrals` — uma linha por sessão vinda de IA (first-touch)

| coluna | tipo | nota |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | |
| `session_id` | VARCHAR(100) NULL | `$this->session->session_id` |
| `ai_source` | VARCHAR(50) NULL | `chatgpt`, `gemini`, `claude`, `perplexity`, `copilot`, `deepseek`, `grok`, `outros` |
| `detection_method` | VARCHAR(20) NULL | `utm` \| `referer` \| `aux` |
| `landing_page` | VARCHAR(500) NULL | 1ª URL da sessão |
| `request_uri` | VARCHAR(500) NULL | |
| `referrer` | VARCHAR(500) NULL | |
| `utm_source` | VARCHAR(255) NULL | |
| `utm_medium` | VARCHAR(255) NULL | |
| `utm_campaign` | VARCHAR(255) NULL | |
| `utm_content` | VARCHAR(255) NULL | |
| `utm_term` | VARCHAR(255) NULL | |
| `user_agent` | VARCHAR(400) NULL | |
| `ip_hash` | VARCHAR(64) NULL | `hash('sha256', $ip . $chave)` — nunca IP puro |
| `id_user` | INT NULL | `usuarios.id` se logado no momento da captura |
| `converted` | TINYINT(1) NOT NULL DEFAULT 0 | |
| `conversion_type` | VARCHAR(50) NULL | tipo da 1ª conversão |
| `conversion_value` | DECIMAL(12,2) NULL | valor da 1ª conversão |
| `converted_at` | DATETIME NULL | |
| `created_at` | DATETIME NOT NULL | |

Índices: `idx_source (ai_source)`, `idx_created (created_at)`, `idx_session (session_id)`, `idx_converted (converted)`.

### 4.2 `ai_conversions` — todo evento de conversão

| coluna | tipo | nota |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | |
| `ai_referral_id` | BIGINT UNSIGNED NOT NULL | FK lógica → `ai_referrals.id` |
| `session_id` | VARCHAR(100) NULL | |
| `ai_source` | VARCHAR(50) NULL | desnormalizado p/ query rápida |
| `conversion_type` | VARCHAR(50) NOT NULL | `trial` \| `assinatura` \| `whatsapp` \| `contato` |
| `conversion_value` | DECIMAL(12,2) NULL | |
| `reference_id` | VARCHAR(100) NULL | ex.: `subscription_id` — usado para deduplicar |
| `meta` | VARCHAR(500) NULL | contexto curto, sem dado pessoal |
| `created_at` | DATETIME NOT NULL | |

Índices: `idx_referral (ai_referral_id)`, `idx_source (ai_source)`, `idx_type (conversion_type)`, `idx_created (created_at)`.

### 4.3 Retenção

Comentário no método de migração com a política sugerida (purge de `ai_referrals` / `ai_conversions` com `created_at` > 18 meses). Método opcional `purgar_monitoramento_ia($meses = 18)` em `Dev.php`, nível 1, sem execução automática nesta fase.

---

## 5. Config — `application/config/ai_sources.php`

Carregado com `$this->config->load('ai_sources', TRUE)`.

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['ai_sources'] = [
    'chatgpt'    => ['domains' => ['chatgpt.com','openai.com'],         'utm' => ['chatgpt.com','chatgpt']],
    'gemini'     => ['domains' => ['gemini.google.com'],                'utm' => ['gemini','gemini.google.com']],
    'claude'     => ['domains' => ['claude.ai'],                        'utm' => ['claude','claude.ai']],
    'perplexity' => ['domains' => ['perplexity.ai'],                    'utm' => ['perplexity','perplexity.ai']],
    'copilot'    => ['domains' => ['copilot.microsoft.com'],            'utm' => ['copilot']],
    'deepseek'   => ['domains' => ['chat.deepseek.com','deepseek.com'], 'utm' => ['deepseek']],
    'grok'       => ['domains' => ['grok.com'],                         'utm' => ['grok']],
];

// utm_medium que sempre conta como IA (fonte = 'outros' se não casar fonte específica)
$config['ai_medium_flags'] = ['ai-assistant','ai_assistant','ai'];
```

`bing.com` e `x.com` **não entram em `domains`** — o documento alerta para não classificar tráfego de busca (`google.com`, `bing.com`) como IA. Só contam por UTM explícita.

---

## 6. Detector + tracker — `Padrao_model`

Métodos novos (o model já é autoloaded em todo o projeto):

### `track_ai_referral()`
Chamado nos construtores de `Home.php` e `Blog.php`, logo após `$this->padrao_model->indexador()`.

1. Se `table_exists('ai_referrals')` for falso → retorna.
2. Lê `utm_*` de `$this->input->get()` e `referrer` de `$_SERVER['HTTP_REFERER']`.
3. `$r = $this->detect_ai_source($referrer, $utm)`. Se `!$r['is_ai']` → retorna.
4. Guard de sessão: se `session.ai_referral_id` já setado → garante cookie e retorna.
5. Guard de banco: se já existe linha para `session_id` → carrega id na sessão, garante cookie, retorna.
6. `INSERT` em `ai_referrals` com todos os campos truncados ao tamanho da coluna; `ip_hash` = `sha256($_SERVER['REMOTE_ADDR'] . $chave)`.
7. `session->set_userdata(['ai_referral_id' => $id, 'ai_source' => $r['source']])`.
8. `set_cookie('utec_air', $id.'|'.$r['source'], 90 dias)` (carregar o helper `cookie` do CI antes de usar `set_cookie` / `get_cookie`).

Nunca cria mais de uma linha por `session_id`. Não registra pageview repetido.

### `detect_ai_source($referrer, array $utm)`  *(privado)*
Retorna `['is_ai' => bool, 'source' => string|null, 'method' => 'utm'|'referer'|'aux'|null]`.

Ordem (documento — "Ordem de detecção recomendada"):

1. **UTM explícita:** para cada fonte, se `strtolower($utm['utm_source'])` está em `sources[x]['utm']` → `is_ai=true, source=x, method=utm`. Se `utm['utm_medium']` ∈ `ai_medium_flags` → `is_ai=true, source='outros', method=utm`.
2. **Referer:** `$ref = strtolower($referrer)`. Para cada fonte, se algum `domains[]` casa por `strpos($ref, $dominio) !== false` → `is_ai=true, source=x, method=referer`. (`strpos`, não comparação exata — subdomínios e caminhos.)
3. **Regras auxiliares:** ponto de extensão para identificadores próprios futuros de alguma IA. Vazio nesta fase.
4. Nada casou → `is_ai=false`.

### `mark_ai_conversion($type, $value = null, $reference_id = null, $meta = null)`
1. Se `table_exists('ai_conversions')` for falso → retorna.
2. `$refId = session.ai_referral_id`. Se vazio, tenta cookie `utec_air` (`explode('|')`), valida que a linha existe em `ai_referrals`, re-seta sessão.
3. Se ainda sem `$refId` → retorna (visita não atribuída a IA).
4. Se `$reference_id` informado e já existe em `ai_conversions` com o mesmo `conversion_type` → retorna (dedup — evita dupla contagem em retentativas de pagamento).
5. `INSERT` em `ai_conversions` (`ai_referral_id`, `session_id`, `ai_source` da sessão/linha, `conversion_type`, `conversion_value`, `reference_id`, `meta` truncado).
6. Se `ai_referrals.converted = 0` para `$refId` → `UPDATE` first-touch (`converted=1`, `conversion_type`, `conversion_value`, `converted_at = NOW()`).
7. Tudo em `try/catch`; qualquer exceção é engolida (log via `log_message('error', ...)` sem dado pessoal).

### `ai_sources_list()`
Lê o config e devolve os slugs (`array_keys($config['ai_sources'])` + `'outros'`). Usado pelo dashboard e pelos testes.

---

## 7. Instrumentação de conversões

Sem alterar assinatura de métodos existentes; só adiciona a chamada `mark_ai_conversion()` em pontos de sucesso já existentes.

| Conversão | Arquivo / ponto | Chamada |
|---|---|---|
| **Trial 30 dias** | `Home::iniciar_experiencia()` — após `create_operational_trial_signup` retornar `ok` | `mark_ai_conversion('trial', null, $subscription_id, 'plano:'.$plan_code)` |
| **Assinatura paga** | `Home::contratar()` OK **e** confirmação de pagamento em `assinatura_pagamento_pix()` / `assinatura_pagamento_cartao()` / `assinatura_pagamento_status()`, no mesmo ponto em que `register_cycle_payment(...)` é chamado | `mark_ai_conversion('assinatura', (float)$valor, $subscription_id, 'via:'.$metodo)` |
| **WhatsApp** | Novo endpoint `Home::track_evento()` (rota `e/track`), acionado por `navigator.sendBeacon('e/track?t=whatsapp&o=<slug>')` no `click` dos links `wa.me` de `index-front.php` e `public/contato.php` | `mark_ai_conversion('whatsapp', null, null, 'origem:'.$slug)` |
| **Formulário de contato** | `Home::track_evento()` com `t=contato` — disparado no `submit` do formulário de contato quando existir; por ora, cobre o CTA da página `contato` | `mark_ai_conversion('contato', null, null, $meta)` |

### `Home::track_evento()`
- Aceita apenas `t ∈ {whatsapp, contato}` (whitelist); qualquer outro valor → `204` sem ação.
- Rate-limit simples: no máximo 1 evento do mesmo `t` por sessão a cada 60s (flag em `session`).
- Responde `HTTP 204 No Content`, corpo vazio. Sem CSRF (GET idempotente por beacon).
- Não loga corpo nem dado pessoal.

Nenhum fluxo existente muda de comportamento — as chamadas são aditivas e silenciosas.

---

## 8. Dashboard admin

### Controller — `application/controllers/adm/Marketing.php`
Espelha `application/controllers/adm/Leads.php`:

```php
public function __construct() {
    parent::__construct();
    $this->load->model('Padrao_model');
    $this->load->model('adm/Marketing_model');
    $this->load->model('adm/Usuarios_model');
    $this->Usuarios_model->verSession();
    $usuario = $this->Padrao_model->get_usuario_logado();
    if ((int)$usuario->nivel !== 1) { redirect('adm/atendimento'); }
}
```

Métodos:
- `index()` → chama `trafego_ia()`.
- `trafego_ia()` → resolve `get_summary()` + dados iniciais dos gráficos (30 dias) e carrega a view.
- `api($rel)` → `header('Content-Type: application/json')` + `echo json_encode(...)`. `$rel ∈ {summary, sources, pages, conversions, timeline}`. Filtros lidos de `$this->input->get()`: `start_date`, `end_date`, `source`, `landing_page`, `converted`. Datas validadas (`DateTime::createFromFormat('Y-m-d', ...)`), fallback para últimos 30 dias.

### Model — `application/models/adm/Marketing_model.php`
Query Builder do CI, todos os métodos recebem `array $f` (filtros normalizados):

- `get_summary($f)` → `acessos_hoje`, `acessos_7d`, `acessos_30d`, `conversoes`, `taxa_conversao`, `receita_atribuida` (`SUM(ai_conversions.conversion_value)` para `conversion_type='assinatura'` no período).
- `get_by_source($f)` → `ai_source`, `total` (`GROUP BY ai_source ORDER BY total DESC`); fontes sem registro aparecem com 0 a partir de `ai_sources_list()`.
- `get_landing_pages($f)` → `landing_page`, `acessos`, `conversoes` (`LEFT JOIN` com `ai_conversions` agregada), `LIMIT 50`.
- `get_conversion_by_source($f)` → por fonte: `visitas`, `conversoes` (`SUM(converted)`), `taxa` (`ROUND(conversoes/visitas*100, 2)`), `receita`.
- `get_timeline($f)` → série diária `DATE(created_at)` → `acessos`, `conversoes`; preenche dias sem dado com 0 no PHP.

SQL base do documento ("SQL de relatórios") como referência das três primeiras queries.

### View — `application/views/adm/marketing/trafego_ia.php`
Mesmo shell das views admin:

```php
<body class="menu-position-side menu-side-left full-screen with-content-panel">
  <div class="all-wrapper with-side-panel solid-bg-all">
    <? include("includes/adm/search.php"); ?>
    <div class="layout-w">
      <? include("includes/adm/menu.php"); ?>
      <div class="content-w">
        <? include("includes/adm/top.php"); ?>
        <ul class="breadcrumb"> Painel / Marketing / Tráfego de IA </ul>
        <div class="content-i"><div class="content-box"> ... </div></div>
```

Estilo dos cards reutiliza `.saas-grid` / `.saas-stat-label` / `.saas-stat-value` de `adm/saas/index.php`.

Componentes:
- **Cards:** Acessos IA hoje · Acessos IA 7 dias · Acessos IA 30 dias · Conversões IA · Taxa de conversão IA · Receita atribuída à IA.
- **Filtro de período** (date range: 7 / 30 / 90 dias + intervalo custom); ao mudar, refaz `fetch` em `adm/marketing/api/*` e re-renderiza gráficos/tabelas.
- **Gráfico de linha** (Chart.js) — acessos IA por dia + linha secundária de conversões.
- **Gráfico de barras (ou donut)** — tráfego por fonte de IA (ChatGPT, Gemini, Claude, Perplexity, Copilot, DeepSeek, Grok, Outros).
- **Tabela** — landing pages mais acessadas (`página` · `acessos` · `conversões`).
- **Tabela** — conversão por origem (`fonte` · `visitas` · `conversões` · `taxa %` · `receita R$`).

Chart.js: `<script src="<?=base_url()?>bower_components/chart.js/dist/Chart.bundle.min.js"></script>` (já vendorizado).

### Menu — `includes/adm/menu.php`
Bloco `if($menu_can_admin){ ... }` **novo e independente**, inserido imediatamente após o `if($menu_can_admin){ $menu_sections[] = ['title' => 'Administracao', ...]; }` existente (por volta da linha 57):

```php
if($menu_can_admin){
    $menu_sections[] = [
        'title' => 'Marketing',
        'items' => [[
            'label' => 'Trafego de IA',
            'icon'  => 'os-icon-signal',
            'url'   => base_url().'adm/marketing/trafego_ia',
            'children' => [
                ['label' => 'Visao geral', 'url' => base_url().'adm/marketing/trafego_ia'],
            ],
        ]],
    ];
}
```

### Rotas — `application/config/routes.php`
```php
$route['adm/marketing']            = 'adm/marketing/index';
$route['adm/marketing/trafego_ia'] = 'adm/marketing/trafego_ia';
$route['adm/marketing/api/(:any)'] = 'adm/marketing/api/$1';
$route['e/track']                  = 'home/track_evento';
```

---

## 9. LGPD / privacidade

- `ip_hash` sempre; IP puro nunca gravado.
- `meta` e `landing_page` não recebem dado pessoal (sem query string de formulário, sem e-mail/telefone).
- Dashboard e endpoints `adm/marketing/*` restritos a nível 1.
- Política de retenção documentada no método de migração; `purgar_monitoramento_ia($meses)` disponível manualmente.
- Cookie `utec_air` guarda apenas `id|source` (sem PII).

---

## 10. Testes

Projeto sem PHPUnit. Método novo em `Dev.php`: **`testar_detector_ia()`** (nível 1) que roda os casos mínimos do documento e imprime tabela PASS/FAIL:

| # | Entrada | Esperado |
|---|---|---|
| 1 | `utm_source=chatgpt.com` | `is_ai=true`, `source=chatgpt`, `method=utm` |
| 2 | `Referer: https://chatgpt.com/` | `is_ai=true`, `source=chatgpt`, `method=referer` |
| 3 | `Referer: https://www.perplexity.ai/` | `is_ai=true`, `source=perplexity` |
| 4 | `Referer: https://www.google.com/search?q=...` | `is_ai=false` |
| 5 | sem referer e sem utm | `is_ai=false` |
| 6 | `utm_medium=ai-assistant` | `is_ai=true`, `source=outros`, `method=utm` |
| 7 | `Referer: https://bing.com/search?q=...` | `is_ai=false` (Bing não está em `domains`) |

Validação adicional: `php -l` em todos os arquivos alterados/criados; smoke test manual das URLs:
- `http://localhost/utec/adm/dev/migrar_monitoramento_ia`
- `http://localhost/utec/adm/dev/testar_detector_ia`
- `http://localhost/utec/adm/marketing/trafego_ia`
- `http://localhost/utec/?utm_source=chatgpt.com` → conferir linha em `ai_referrals`
- `http://localhost/utec/adm/marketing/api/summary`

---

## 11. Mapa de arquivos

| Arquivo | Ação | O que muda |
|---|---|---|
| `application/config/ai_sources.php` | Criar | Lista configurável de fontes de IA (domínios + UTMs) + `ai_medium_flags` |
| `application/models/Padrao_model.php` | Modificar | `track_ai_referral()`, `detect_ai_source()` (privado), `mark_ai_conversion()`, `ai_sources_list()` |
| `application/controllers/Home.php` | Modificar | `track_ai_referral()` no construtor; `track_evento()`; `mark_ai_conversion()` em trial e nos 4 pontos de pagamento/assinatura |
| `application/controllers/Blog.php` | Modificar | `track_ai_referral()` no construtor |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_monitoramento_ia()`, `testar_detector_ia()`, `purgar_monitoramento_ia()` + links no `index()` |
| `application/controllers/adm/Marketing.php` | Criar | Controller do dashboard (nível 1), `index/trafego_ia/api` |
| `application/models/adm/Marketing_model.php` | Criar | Queries agregadas (summary, sources, pages, conversions, timeline) |
| `application/views/adm/marketing/trafego_ia.php` | Criar | View do dashboard (cards + Chart.js + tabelas + filtro de período) |
| `includes/adm/menu.php` | Modificar | Nova seção "Marketing" → "Trafego de IA" (só `$menu_can_admin`) |
| `application/config/routes.php` | Modificar | Rotas `adm/marketing`, `adm/marketing/trafego_ia`, `adm/marketing/api/(:any)`, `e/track` |
| `application/views/index-front.php` | Modificar | `sendBeacon` no `click` dos links `wa.me` |
| `application/views/public/contato.php` | Modificar | `sendBeacon` no `click` do WhatsApp / submit do form de contato |

---

## 12. Ordem de implementação sugerida

1. **Migração + config** — `ai_sources.php`, `migrar_monitoramento_ia()` em `Dev.php`. Rodar e conferir tabelas.
2. **Detector + testes** — `detect_ai_source()` + `testar_detector_ia()`. Todos os 7 casos PASS.
3. **Tracker** — `track_ai_referral()` + chamada em `Home.php` e `Blog.php`. Smoke test com `?utm_source=chatgpt.com`.
4. **Conversões** — `mark_ai_conversion()` + `track_evento()` + instrumentação dos pontos de trial/assinatura + beacons nos `wa.me`.
5. **Dashboard** — `Marketing_model`, `adm/Marketing`, view, rotas, item de menu.
6. **Revisão** — `php -l` em tudo, smoke test das URLs, revisão de diff (nenhuma mudança fora do escopo).

---

## 13. Fase futura — GEO / Brand Monitor (fora deste spec)

Parte 2 do `docs/monitoramento_geo_ia.md`: tabelas `geo_projects`, `geo_prompts`, `geo_runs`, `geo_answers`, `geo_mentions`, `geo_citations`, `geo_competitors`; providers desacoplados (OpenAI / Gemini / Perplexity / Claude); execução via cron/worker (não por pageview); parser de menções e citações; métricas Prompt Coverage, Citation Rate, AI Share of Voice; GEO Score configurável (0–100). API keys sempre em variáveis de ambiente, nunca versionadas. Só iniciar após o tracker de tráfego estar estável em produção.
