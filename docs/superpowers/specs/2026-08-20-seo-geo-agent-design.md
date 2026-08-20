# Agente SEO/GEO recorrente — design

Data: 2026-08-20

## 1. Motivação

O projeto já tem uma metodologia de pesquisa de keywords validada em três sessões manuais:

- [seo-geo-auditoria-2026-06-02.md](../../seo-geo-auditoria-2026-06-02.md) — auditoria técnica + playbook GEO
- [seo-geo-expansao-keywords-2026-06-02.md](../../seo-geo-expansao-keywords-2026-06-02.md) — leitura do CSV de keywords, sprints por cluster
- [seo-keywords-addendum-2026-07-14.md](../../seo-keywords-addendum-2026-07-14.md) — consulta direta à API de autocomplete do Google, 4 landings criadas a partir de gaps

Essas sessões foram manuais e pontuais. O objetivo agora é transformar essa metodologia num agente que roda **sozinho, toda semana**, sem depender de o usuário lembrar de pedir.

## 2. Arquitetura

```
Windows Task Scheduler (semanal, local)
        │
        ▼
claude.exe -p "/seo-geo-agent"  (headless, allowedTools restrito)
        │
        ├─ lê docs/seo-geo-agente-ledger.md            (memória entre execuções)
        ├─ pesquisa via curl na Google Autocomplete API
        ├─ decide criar / recomendar / descartar (regras da seção 4)
        ├─ cria landing pages (view + controller + rota + sitemap)
        ├─ escreve .sql de artigos de blog (não executa)
        ├─ atualiza o ledger
        ├─ escreve relatório da rodada
        └─ envia email de notificação (só se houve algo novo)
```

Tudo roda **local**, sem cloud, sem necessidade de WAMP/MySQL ativo — o agente não faz nenhuma consulta ao banco (ver seção 5). Precisa apenas de: acesso ao filesystem do repo, internet para a Google Autocomplete API, e SMTP para o email.

## 3. Componentes

### 3.1 Skill `seo-geo-agent`

Novo diretório real (não symlink) `.claude/skills/seo-geo-agent/SKILL.md`. Autocontido — quando invocado via `claude -p "/seo-geo-agent"` a sessão começa do zero, sem contexto de conversa anterior. O `SKILL.md` deve conter:

- O método de consulta (curl na autocomplete API, com `sleep 1` entre chamadas — já documentado no addendum de jul/2026)
- A lista de clusters/especialidades a testar em rotação (ver seção 4.3)
- As regras de decisão (criar vs. recomendar vs. descartar — seção 4)
- Os templates estruturais de landing page e de artigo (baseados nos arquivos existentes)
- O caminho e formato exatos de cada arquivo de saída (seção 5)
- A regra dura: **nunca rodar `git add`, `git commit`, `git push`, nem qualquer comando de FTP/deploy**
- O passo de notificação por email (seção 6)

### 3.2 Ledger (`docs/seo-geo-agente-ledger.md`)

Arquivo único, reescrito (não versionado por data) a cada execução, com três seções:

1. **Keywords testadas** — termo, data do último teste, resultado (demanda confirmada / sem sinal), reteste sugerido em 4 semanas
2. **Páginas e artigos existentes** — landing pages (slug, especialidade/cluster, data de criação), artigos gerados via `.sql` (título, slug, data, se já foi aplicado no banco de produção — campo que o usuário atualiza manualmente após rodar o SQL)
3. **Descartes** — o que já foi avaliado e rejeitado, com motivo (ex.: `/alternativa-amplimed` — sem sinal de "alternativa"/"vs" no autocomplete, conteúdo especulativo)

O agente **lê este arquivo antes de pesquisar** para não retestar o que já foi testado há menos de 4 semanas nem sugerir de novo o que já foi descartado sem novo sinal.

### 3.3 Relatório por execução (`docs/seo-geo-agente-relatorio-YYYY-MM-DD.md`)

Segue o padrão dos `.md` existentes: o que foi testado, o que foi criado (com caminho de arquivo), o que foi só recomendado, o que foi descartado e por quê. É o arquivo que o usuário lê para saber exatamente o que revisar com `git status`/`git diff` antes de publicar.

### 3.4 Agendamento (Windows Task Scheduler)

Tarefa semanal (`schtasks /create`), padrão: **segunda-feira, 07:00, horário local**. Comando:

```
claude.exe -p "/seo-geo-agent" ^
  --allowedTools "Read Write Edit Glob Grep Bash(curl:*) Bash(git status:*) Bash(git diff:*) Bash(date:*) PowerShell(Send-MailMessage:*)" ^
  --disallowedTools "Bash(git add*) Bash(git commit*) Bash(git push*) Bash(git checkout*) Bash(git reset*) Bash(rm*)"
```
executado com `cwd` = `C:\htdocs\utec`, saída redirecionada para `docs/seo-geo-agente-log/run-YYYY-MM-DD.log`.

Não usa `--dangerously-skip-permissions` — a lista `--allowedTools` já cobre exatamente as ferramentas necessárias, e `--disallowedTools` funciona como cinto de segurança extra contra qualquer tentativa de commit/push. A sintaxe exata dos padrões `Bash(...)`/`PowerShell(...)` será validada durante a implementação (fase de plano/testes), já que pequenas variações de matching podem exigir ajuste.

## 4. Metodologia de pesquisa e regras de decisão

### 4.1 Fontes de consulta

- Google Autocomplete API (`curl "https://www.google.com/complete/search?client=firefox&hl=pt-BR&gl=br&q=<termo>"`), método já validado no addendum de jul/2026
- Lista de especialidades: lida estaticamente de `application/controllers/adm/Dev.php` (seed de `usuarios_especialidades`, 42 registros) — não via query ao banco, para não depender de MySQL ativo
- Lista de páginas existentes: lida de `application/config/routes.php` (rotas `seo_*`) e `application/views/public/seo/*.php`
- Lista de artigos existentes: lida do ledger (seção 3.2) + `docs/blog-posts-seed.sql` como base histórica

### 4.2 Regra de criação autônoma (landing page ou artigo)

Só cria quando **todas** as condições abaixo são verdadeiras:

1. Autocomplete confirma demanda real para o termo (retorno não-vazio, ou frequência de variações relacionadas)
2. Não canibaliza uma página/artigo existente (intenção claramente diferente da já coberta — mesma regra do documento de expansão: "criar nova URL apenas quando houver mudança real de intenção")
3. Não está na lista de descartes do ledger sem sinal novo
4. Está dentro do escopo atual do produto (especialidades de saúde já suportadas — não entra em estética/veterinária/laboratório sem decisão explícita do usuário, conforme já registrado no documento de expansão)
5. Respeita o limite de 5 landings + 5 artigos por execução

Quando há demanda mas alguma condição falha (ex.: cluster fora de escopo, ou confiança baixa), o agente **recomenda no relatório em vez de criar** — mesmo comportamento cauteloso já usado nas sessões manuais (ex.: caso Amplimed/Belasis/Ninsaude no addendum).

### 4.3 Rotação de temas por execução

Para não ficar preso sempre no mesmo cluster, cada execução varre, em rodízio:

- Especialidades da tabela sem landing ainda
- Reteste de concorrentes já mapeados + varredura de 2-3 nomes de concorrentes novos
- Variações semânticas ainda não absorvidas nas landings (`programa para...`, `gratis`, `na nuvem`)
- Contextos GEO de transição/custo (ex.: "quanto custa", "como migrar de planilha")

O `SKILL.md` mantém essa lista como checklist rotativo, marcando no ledger o que já foi coberto.

## 5. Formato dos arquivos gerados

### 5.1 Landing page nova

- View: `application/views/public/seo/{slug}.php` — segue a estrutura já padronizada nas páginas existentes (resposta curta no topo, recursos, para quem serve, comparativo, FAQ, CTA), usando uma página recente (ex.: `sistema-para-pediatria.php`) como modelo de tom e estrutura
- Controller: novo método `seo_{nome}()` em `application/controllers/Home.php`, seguindo o padrão dos métodos `seo_*` existentes (`$this->load->view(...)`)
- Rota: nova linha em `application/config/routes.php`, no bloco de rotas `seo_*`
- Sitemap: nova entrada `<url>` em `sitemap.xml` com `lastmod` da data de criação

### 5.2 Artigo de blog novo

- Arquivo `docs/seo-geo-agente-blog-YYYY-MM-DD.sql`, um `INSERT INTO blog_posts` por artigo, mesmas colunas e mesmo estilo de `docs/blog-posts-seed.sql` (título, slug, resumo, conteúdo HTML, meta_titulo, meta_descricao, autor, tempo_leitura, publicado=1, datas)
- **Nunca executado automaticamente** — o usuário roda via phpMyAdmin/mysql quando decidir publicar. Até lá, o ledger mantém o artigo marcado como "pendente de aplicação"; o usuário atualiza esse campo manualmente depois de rodar o `.sql`. Enquanto pendente, o agente não gera um artigo novo com o mesmo slug em execuções futuras.

### 5.3 O que o agente NUNCA faz

- `git add` / `git commit` / `git push`
- Deploy FTP ou qualquer publicação
- `INSERT`/`UPDATE` direto no banco de dados (mesmo local)
- Alterar `application/config/email.php` ou qualquer arquivo de credenciais

## 6. Notificação por email

Reaproveita a config SMTP já existente em `application/config/email.php` (host `mail.utecnologia.com.br`, conta `suporte@utecnologia.com.br`).

- **Só envia se a execução gerou algo novo**: pelo menos 1 landing criada, 1 artigo gerado, ou 1 recomendação nova relevante no relatório. Execuções "vazias" (nada novo, tudo já coberto pelo ledger) não disparam email.
- Mecanismo: comando `Send-MailMessage` do PowerShell, lendo host/porta/usuário/senha diretamente de `application/config/email.php` em tempo de execução (não duplica a senha em outro arquivo)
- Destinatário: `igor_marlus@yahoo.com.br`
- Assunto: `[UTec SEO/GEO] Novo relatório — YYYY-MM-DD`
- Corpo: resumo do que foi criado/recomendado nesta rodada + caminho do arquivo de relatório completo

## 7. Fora de escopo (por enquanto)

- Aplicar os `.sql` de artigos automaticamente no banco
- Commit/push/deploy automático
- Rodar como cloud agent (o usuário não tem ambiente cloud configurado)
- Consultar o Search Console ou outras fontes de dados pagas — só autocomplete gratuito, como hoje
- Editar páginas existentes automaticamente (o agente só cria páginas novas; ajustes em páginas existentes continuam sendo recomendação no relatório, não ação automática)
