# CLAUDE.md — UTecnologia Saúde

Documentação técnica e de negócio do projeto para uso nas sessões com Claude Code.

---

## 1. Visão Geral do Produto

**UTecnologia Saúde** é um sistema web de gestão clínica SaaS voltado para clínicas médicas, consultórios e profissionais de saúde independentes. Está em fase de desenvolvimento ativo com clientes reais interessados na aquisição.

- **URL de produção:** https://utecnologia.com.br/
- **Stack:** PHP 7 + CodeIgniter 3.1.10 + MySQL + Bootstrap 4 + jQuery
- **Ambiente local:** `c:\htdocs\utec` (WAMP/XAMPP)
- **Template base:** Adminto (tema admin Bootstrap 4, sendo modernizado gradualmente)
- **Dono/Desenvolvedor:** Igor Marlus Lessa de Barros

---

## 2. Modelo de Negócio SaaS

### 2.1 Posicionamento

UTecnologia Saúde é comercializado como **SaaS B2B** para o setor de saúde brasileiro. O produto visa reduzir o custo operacional de clínicas e consultórios ao centralizar agenda, prontuário, faturamento e comunicação em um único sistema acessível via browser.

### 2.2 Público-Alvo (ICP)

| Segmento | Perfil | Dor Principal |
|----------|--------|---------------|
| Clínica pequena (1–5 médicos) | Estabelecimento nível 2 + Prestadores nível 3 | Agenda manual, prontuário em papel/planilha |
| Profissional autônomo | Prestador nível 3 | Controle de pacientes e agenda isolados |
| Clínica média (5–20 profissionais) | Estabelecimento + equipe | Gestão de colaboradores e relatórios centralizados |

### 2.3 Planos Comerciais

Os planos são cadastrados em `produtos` com os campos SaaS e exibidos no checkout. Estrutura sugerida:

| Plano | `plan_code` | Profissionais | Colaboradores | Pacientes | Ciclo | Preço Ref. |
|-------|-------------|---------------|---------------|-----------|-------|-----------|
| Solo | `solo` | 1 | 2 | ilimitado | mensal | R$ 79/mês |
| Clínica | `clinica` | 5 | 10 | ilimitado | mensal | R$ 199/mês |
| Pro | `pro` | 20 | 50 | ilimitado | mensal | R$ 399/mês |
| Enterprise | `enterprise` | ilimitado | ilimitado | ilimitado | anual | negociado |

> Os valores acima são referência — o sistema já suporta qualquer combinação via campos `max_profissionais`, `max_colaboradores`, `max_pacientes`, `billing_interval`, `billing_interval_count`, `trial_days` e `setup_fee` na tabela `produtos`.

### 2.4 Fluxo Comercial

```
Lead → Landing page (/) → Interesse → Admin provisiona tenant (adm/saas)
     → Checkout Mercado Pago (Preapproval) → Assinatura ativa
     → Webhook MP atualiza status → Ciclos de cobrança registrados
     → Inadimplência → Bloqueio automático (pendente)
```

### 2.5 Modelo de Receita

- **Recorrência mensal/anual** via Mercado Pago (Preapproval API)
- **Taxa de implantação (setup_fee)** opcional por plano
- **Trial configurável** por plano (`trial_days`)
- Fase atual: provisionamento **manual pelo admin** — onboarding self-service é etapa futura

### 2.6 Diferenciais Competitivos

- Multi-tenant nativo: cada clínica é isolada com seu próprio `tenant_id`
- Árvore de acesso hierárquica (Estabelecimento → Prestador → Colaborador → Paciente)
- Prontuário + agenda + exames integrados em um único fluxo de atendimento
- Integração WhatsApp (chatbot externo via `db2`/`dbbot`)
- Módulo RPG educacional embutido (diferencial de engajamento)

---

## 3. Arquitetura da Aplicação

### 3.1 Estrutura de Diretórios

```
c:\htdocs\utec\
├── application/
│   ├── config/           # database, routes, session, mercadopago
│   ├── controllers/      # raiz (Home, Admin, User) + adm/ + rpg/
│   ├── models/           # raiz (Padrao_model, FbApi_model) + adm/ + rpg/
│   ├── views/            # adm/ (login, dash, usuarios/new/, saas/, atendimento/)
│   ├── libraries/        # M_pdf (mPDF), tcpdf/, mercadopago_saas
│   └── third_party/      # mPDF core
├── system/               # Core CodeIgniter 3.1.10 — NÃO MODIFICAR
├── bower_components/     # Bootstrap, Select2, FullCalendar, DataTables, etc.
├── css/                  # clicklinica-main.css (CSS principal internalizado)
├── js/                   # Scripts JavaScript customizados
├── imagens/              # Uploads: usuarios/, usuarios/min/, usuarios/des/, produtos/
├── uploads/              # Arquivos de pacientes: uploads/pacientes/ (nome encriptado)
└── index.php             # Entry point CI (mod_rewrite ativo — sem index.php na URL)
```

### 3.2 Decisão Arquitetural — CI3

O projeto usa **CodeIgniter 3.1.10** em produção. **Não migrar para CI4 ou outro framework** — produto em produção com clientes, reescrita fora de escopo. Toda adição deve respeitar APIs do CI3: `$this->db`, `$this->input->post()`, `$this->load->view()`, `$this->session->userdata()`.

---

## 4. Banco de Dados

### 4.1 Conexões (`application/config/database.php`)

| Chave | Banco | Host | Uso |
|-------|-------|------|-----|
| `default` | `utecnologiacom_db` | localhost | BD principal |
| `db2` | `chwtppbr_db` | localhost | Chatbot WhatsApp (local) |
| `dbbot` | `chwtppbr_db` | chatbot-whatsapp-br.com.br | Chatbot WhatsApp (remoto) |
| `dbpi` | `produtos_pi` | produtosinovadores.com.br | Produtos Inovadores (externo) |

- Driver: `mysqli`, Charset: `utf8mb4` (default), `utf8` (demais)
- `save_queries = TRUE` — manter em dev, **desativar em produção**

### 4.2 Tabelas Principais

**Usuários e Acesso**
- `usuarios` — todos os usuários (pacientes, médicos, admins, colaboradores)
- `usuarios_niveis` — tipos/perfis de acesso
- `usuarios.id_user` — vínculo operacional na árvore hierárquica
- `usuarios.nivel` — nível de acesso (1–5, ver seção 5)
- `usuarios.tenant_id` — vínculo ao tenant SaaS
- `usuarios.tenant_role` — papel no tenant (`owner`, `admin`, `provider`, `staff`, `patient`)
- `usuarios.onboarding_status` — situação de ativação no tenant
- `usuarios.saas` — flag `1` = habilita acesso ao módulo SaaS (`adm/saas`)

**Saúde e Agenda**
- `agendamentos` — consultas (liga paciente ↔ prestador); campos de prontuário: `atendimento_inicial`, `avaliacao`, `reavaliacao` (textareas genéricos)
- `exames` — catálogo de exames
- `usuarios_exames` — exames solicitados por agendamento
- `usuarios_exames_atendimento` — exames realizados por usuário
- `usuarios_especialidades` — catálogo de especialidades clínicas (id fixo 1–42, usado em `usuarios.especialidade` INT)

**Produtos e Pedidos**
- `produtos` — catálogo de planos/serviços
- `produtos_categorias` — categorias
- `carrinho` / `carrinho_hist` — carrinho ativo e histórico
- `pedidos` — pedidos finalizados
- `produtos.plan_code` — código comercial do plano (`solo`, `clinica`, `pro`, etc.)
- `produtos.billing_interval` / `billing_interval_count` — recorrência
- `produtos.trial_days` / `setup_fee` — trial e taxa de implantação
- `produtos.max_profissionais` / `max_colaboradores` / `max_pacientes` — limites do plano
- `pedidos.tenant_id` / `subscription_id` / `gateway_payment_id` — vínculo pagamento

**Arquivos de Pacientes**
- `pacientes_arquivos` — arquivos enviados (id_paciente, id_agendamento, arquivo, tipo, descricao)
- Armazenados em `uploads/pacientes/` com nome encriptado

**SaaS / Multi-tenant**
- `saas_tenants` — cadastro do tenant (clínica/consultório)
- `saas_subscriptions` — assinatura principal do tenant
- `saas_subscription_cycles` — ciclos de cobrança
- `saas_billing_events` — eventos financeiros e webhooks do gateway

**Integrações**
- `acessos` — analytics de pageviews (IP, navegador, página)
- `api_conv_fb` — eventos para Facebook Pixel (Conversions API)
- `pi_whats_users` — usuários vinculados ao WhatsApp

**RPG (módulo educacional)**
- `rpg_personagens`, `rpg_personagens_atributos`
- `rpg_items`, `rpg_user_inventory`
- `rpg_locations`, `rpg_dialogos`, `rpg_progress`

---

## 5. Níveis de Usuário

### 5.1 Tabela de Perfis

| Nível | Perfil | Redirect pós-login |
|-------|--------|--------------------|
| 1 | Administrador | `adm/usuarios` |
| 2 | Estabelecimento | `adm/atendimento` |
| 3 | Prestador | `adm/atendimento` |
| 4 | Colaborador | `adm/atendimento` |
| 5 | Paciente | `adm/usuarios` (lista pacientes) |

### 5.2 Regras de Escopo Clínico

O escopo é calculado por `Padrao_model::get_scope_user_ids()` usando a árvore de `id_user`:

- **Nível 1 — Administrador:** vê tudo sem restrição.
- **Nível 2 — Estabelecimento:** vê o próprio usuário + toda a árvore descendente (prestadores, colaboradores, pacientes vinculados).
- **Nível 3 — Prestador:** vê o próprio usuário + colaboradores e pacientes vinculados + pode herdar escopo do estabelecimento pai.
- **Nível 4 — Colaborador:** vê o próprio + irmãos do mesmo `id_user` + a cadeia acima até o estabelecimento.
- **Nível 5 — Paciente:** sem portal dedicado; escopo reduzido ao próprio registro.

### 5.3 Regras de Cadastro (`id_user`)

| Quem cria | Pode criar | Vínculo gerado |
|-----------|-----------|----------------|
| Admin (1) | Qualquer nível | Manual |
| Estabelecimento (2) | Prestador (3), Colaborador (4), Paciente (5) | `id_user` = id do estabelecimento |
| Prestador (3) | Colaborador (4), Paciente (5) | `id_user` = id do prestador |
| Colaborador (4) | Paciente (5) | `id_user` herdado do grupo |

### 5.4 Acesso ao Módulo SaaS

Para acessar `adm/saas`, o usuário precisa:
1. Ser nível 1, 2 ou 3
2. Ter `usuarios.saas = 1`

Verificado por `Padrao_model::can_access_saas_module()`. O Admin (nível 1) tem acesso irrestrito ao SaaS.

### 5.5 Bloqueio por Inadimplência

`Padrao_model::tenant_allows_access()` retorna `false` quando `saas_tenants.status != 1`. A lógica de bloqueio automático via webhook ainda está **pendente de implementação**.

---

## 6. Controllers

### 6.1 Raiz (`application/controllers/`)

| Arquivo | Rota | Função |
|---------|------|--------|
| `Home.php` | `/` | Landing page pública |
| `Admin.php` | `/admin` | Login + `logar_como/{id}` (admin nível 1) |
| `User.php` | `/user` | Carrinho, pedidos, MP legado |

### 6.2 Admin (`application/controllers/adm/`)

| Arquivo | Rota | Função |
|---------|------|--------|
| `Usuarios.php` | `/adm/usuarios` | CRUD usuários, prontuários, upload fotos |
| `Atendimento.php` | `/adm/atendimento` | Agendamentos, prontuários, exames, status |
| `Produtos.php` | `/adm/produtos` | CRUD planos, tipos de plano, assinaturas legadas |
| `Saas.php` | `/adm/saas` | Tenants, assinaturas, checkout MP, webhook |
| `Dev.php` | `/adm/dev` | Migrações e utilitários de desenvolvimento |
| `Especialidades.php` | `/adm/especialidades` | CRUD de campos extras por especialidade (nível 1 apenas) |

> `Atencimento.php` (com typo) foi renomeado para `.bak` — não é controller ativo.

### 6.3 Rotas Especiais (`application/config/routes.php`)

```php
$route['default_controller'] = 'home';
$route['locations'] = 'rpgLocations/index';
$route['webhooks/mercadopago'] = 'adm/saas/webhook_mercadopago';
```

---

## 7. Models

### 7.1 `Padrao_model` (base de todos os controllers)

Carregado obrigatoriamente em todos os controllers admin. Funções principais:

| Função | Descrição |
|--------|-----------|
| `get_by_id($id, $tabela)` | Busca um registro por ID |
| `get_qr($tabela, $where)` | Query genérica com condições |
| `del_by_id($id, $tabela)` | Delete por ID |
| `converte_data($data)` | Formata data BR ↔ MySQL |
| `indexador()` | Registra acesso (analytics) |
| `get_usuario_logado()` | Retorna row do usuário da sessão atual |
| `get_scope_user_ids($usuario)` | Retorna array de IDs visíveis pelo usuário (árvore) |
| `ids_to_sql_in($ids)` | Converte array de IDs para string SQL `IN(...)` |
| `expand_user_tree_ids($root_ids)` | Expande recursivamente a árvore de usuários |
| `sanitize_child_level($nivel, $usuario)` | Valida se o nível filho é permitido pelo pai |
| `get_allowed_child_levels($usuario)` | Retorna níveis que o usuário pode criar |
| `get_vinculo_options($nivel, $usuario)` | Opções de vínculo para o formulário de cadastro |
| `get_vinculo_default_id($nivel, $usuario)` | ID de vínculo padrão por nível |
| `resolve_vinculo_id($nivel, $post_id, $usuario)` | Resolve `id_user` no cadastro |
| `can_access_saas_module($usuario)` | Verifica acesso ao módulo SaaS |
| `tenant_allows_access($usuario)` | Verifica se o tenant está ativo (não bloqueado) |
| `get_logged_tenant()` | Retorna o tenant do usuário logado |
| `usuario_tem_saas($usuario)` | Verifica se `usuarios.saas = 1` |
| `infer_tenant_role_by_level($nivel, $is_owner)` | Mapeia nível → tenant_role |

### 7.2 `adm/Usuarios_model`

| Função | Descrição |
|--------|-----------|
| `logar($login, $senha)` | Autenticação (password_verify + fallback texto puro) |
| `verSession()` | Valida sessão ativa; redireciona para login se inválida |

### 7.3 `adm/Saas_model`

| Função | Descrição |
|--------|-----------|
| `has_schema()` | Verifica se as tabelas SaaS existem |
| `get_dashboard_data($viewer)` | Dados para o dashboard `adm/saas` |
| `get_tenant_detail($tenant_id, $viewer)` | Dados do tenant + equipe + assinatura |
| `provision_tenant($post, $viewer)` | Cria tenant + assinatura + ciclo inicial |

### 7.4 `FbApi_model`

Integração com Facebook Conversions API (eventos de pixel).

---

## 8. Views

### 8.1 Estrutura

```
application/views/
├── index-front.php                   # Landing page pública — hero com segmentação Clínica/Profissional
│                                     # Seções: hero, features, como funciona, especialidades, CTA, contato, login
│                                     # CTAs apontam para /experimentar?tipo=clinica e /experimentar?tipo=profissional
├── public/
│   ├── experimentar.php              # Formulário trial 30 dias (pré-seleciona tenant_tipo via ?tipo=)
│   ├── experimentar-sucesso.php      # Confirmação após trial criado
│   ├── assinar.php                   # Formulário de assinatura paga
│   ├── assinar-pagamento.php         # Checkout PIX / cartão Mercado Pago
│   └── assinar-sucesso.php           # Confirmação de pagamento
└── adm/
    ├── login.php                     # Tela de login
    ├── dash.php                      # Dashboard principal (51KB)
    ├── index.php                     # Página inicial admin
    ├── usuarios/
    │   ├── novo.php                  # LEGADO — não usar
    │   ├── lista.php                 # LEGADO — não usar
    │   └── new/                      # ← VIEWS ATIVAS (usar estas)
    │       ├── lista.php             # Lista de usuários
    │       ├── cadastro.php          # Cadastro por nível
    │       ├── edicao.php            # Edição de usuário
    │       ├── prontuario.php        # Prontuário do paciente
    │       ├── atendimentos.php      # Lista de atendimentos
    │       └── exames.php            # Gestão de exames
    ├── saas/
    │   ├── index.php                 # Dashboard operacional SaaS
    │   ├── tenant.php                # Detalhe do tenant, assinatura e equipe
    │   └── bloqueado.php             # Tela de tenant bloqueado por inadimplência
    └── atendimento/
        └── atendimento.php           # Formulário de atendimento (19KB)
```

---

## 9. Frontend

- **Template:** Adminto (Bootstrap 4)
- **CSS principal:** `css/clicklinica-main.css` (dependência externa internalizada)
- **Fontes:** Lato (Google Fonts) nas views admin; Inter na landing page
- **Bower Components:** Bootstrap, Select2, FullCalendar, Perfect Scrollbar, Slick Carousel, Dropzone, DateRangePicker, DataTables

---

## 10. Integrações Externas

### 10.1 Mercado Pago

- **Configuração:** `application/config/mercadopago.php`
- **Credenciais:** lidas de variáveis de ambiente (`MERCADOPAGO_ACCESS_TOKEN`, `MERCADOPAGO_PUBLIC_KEY`, `MERCADOPAGO_WEBHOOK_SECRET`) com fallback para valores hardcoded
- **Library SaaS:** `application/libraries/Mercadopago_saas.php` — usar esta para assinaturas recorrentes (Preapproval API), nunca repetir token no controller
- **Webhook:** `https://utecnologia.com.br/webhooks/mercadopago` → `adm/saas/webhook_mercadopago`
- **Moeda:** BRL
- **Back URLs:** todas apontam para `adm/saas` (sucesso, pendente, falha)

### 10.2 Facebook Conversions API

- Eventos enviados via `FbApi_model` + tabela `api_conv_fb`

### 10.3 WhatsApp Chatbot

- Banco `chwtppbr_db` (local: `db2`, remoto: `dbbot`)
- Usuários vinculados em `pi_whats_users`

### 10.4 Upload de Arquivos

- **Imagens de usuários:** `imagens/usuarios/` (original) + `/min/` (120×72) + `/des/` (300×210)
- **Imagens de produtos:** `imagens/produtos/`
- **Arquivos de pacientes:** `uploads/pacientes/` (nome encriptado)
- Biblioteca CI nativa: `upload` + `image_lib` (GD2)

### 10.5 Geração de PDF

- **mPDF** via `application/libraries/M_pdf.php`
- **TCPDF** via `application/libraries/tcpdf/`

---

## 11. Segurança

| Item | Status | Detalhe |
|------|--------|---------|
| Senhas | ✅ OK | `password_hash()` com migração suave (fallback texto puro no login) |
| SQL Injection | ✅ OK | Cast `(int)` em IDs de URL; `$this->input->post()` em formulários |
| CSS externo | ✅ OK | Internalizado em `css/clicklinica-main.css` |
| `$_POST` direto | ✅ OK | Substituído por `$this->input->post()` |
| `ereg_replace()` | ✅ OK | Substituído por `preg_replace()` / `str_replace()` |
| Webhook MP | ⚠️ Pendente | Ainda não validado com assinatura HMAC em produção |
| XSS nas views | ⚠️ Verificar | Outputs em views podem precisar de `htmlspecialchars()` |

### Notas da Migração de Senhas

- **Login:** `password_verify()` primeiro; se falhar, compara texto puro e rehasha
- **Cadastro/edição:** `password_hash()` direto
- **Troca de senha (`alterar()`):** `password_verify()` + aceita texto puro em fallback
- **"Acessar como":** apenas admin nível 1 via `/admin/logar_como/{id}`
- **Campo senha na edição:** se vazio, não atualiza a senha existente

---

## 12. Convenções do Projeto

- Controllers admin: `application/controllers/adm/`
- Models admin: `application/models/adm/`
- Views novas: `application/views/adm/[modulo]/new/` (ao modernizar um módulo)
- Views SaaS: `application/views/adm/saas/`
- `Padrao_model` carregado obrigatoriamente em todos os controllers
- Sessão: `$this->session->userdata('id')`, `'nome'`, `'nivel'`, `'login'`, `'usr'`
- Redirect pós-login por nível (ver seção 5.1)
- Mercado Pago: configuração centralizada em `application/config/mercadopago.php`
- Assinaturas recorrentes SaaS: usar `Mercadopago_saas.php`
- Não usar `$_POST` direto — sempre `$this->input->post()`
- Não modificar `system/` (core CI3)
- Não migrar para CI4 ou outro framework

---

## 13. Utilitário de Dev / Migrações

Controller: `application/controllers/adm/Dev.php`

| Rota | Função |
|------|--------|
| `adm/dev/migrar_fase1_saas` | Cria tabelas SaaS + adiciona colunas em `usuarios`, `produtos`, `pedidos`, `carrinho_hist` (idempotente) |
| `adm/dev/criar_tabela_arquivos_paciente` | Cria `pacientes_arquivos` |
| `adm/dev/migrar_especialidades` | Cria `usuarios_especialidades` (42 especialidades seed, IDs fixos 1–42), migra valores texto em `usuarios.especialidade` para IDs, altera coluna para `INT` (idempotente) |
| `adm/dev/migrar_fase2_prontuario_especialidades` | Cria `especialidades_campos_config` + `agendamentos.campos_extras` TEXT, insere config para 9 especialidades (idempotente) |

Para novas migrações: adicionar método em `Dev.php`, proteger com `nivel == 1` na sessão.

---

## 14. Débitos Técnicos

| Severidade | Status | Item |
|------------|--------|------|
| 🔴 Alta | ✅ Resolvido | Senhas em texto puro |
| 🔴 Alta | ✅ Resolvido | SQL injection em IDs de URL |
| 🔴 Alta | ✅ Resolvido | CSS de domínio externo |
| 🟡 Média | ✅ Resolvido | Base SaaS criada (tabelas + campos) |
| 🟡 Média | ✅ Resolvido | Mercado Pago centralizado |
| 🟡 Média | ✅ Resolvido | `ereg_replace()` removido |
| 🟡 Média | ✅ Resolvido | `$_POST` direto eliminado |
| 🟡 Média | ✅ Resolvido | Controller duplicado `Atencimento.php` removido |
| 🟡 Média | ✅ Resolvido | `usuarios.especialidade` normalizado — campo virou INT, tabela `usuarios_especialidades` criada com 42 especialidades |
| 🟡 Média | Pendente | Webhook MP validado ponta a ponta em produção |
| 🟡 Média | Pendente | Bloqueio automático de tenant por inadimplência |
| 🟡 Média | Pendente | Ciclos pagos baixados automaticamente via webhook |
| 🟢 Baixa | Pendente | Comentários `#` e código comentado — limpar gradualmente |
| 🟢 Baixa | Pendente | Textos em inglês nas views ("Start typing to search...", etc.) |

---

## 15. Roadmap de Produto

### 15.1 Funcionalidades Concluídas

- [x] Multi-clínica / multi-tenant (estrutura base)
- [x] Operação SaaS com dashboard e provisionamento manual
- [x] Checkout recorrente Mercado Pago (Preapproval)
- [x] Timeline de prontuário
- [x] Relatórios clínicos
- [x] Módulo comercial reposicionado para planos/assinaturas
- [x] Agenda com filtros operacionais
- [x] Checklist operacional de exames
- [x] Cancelamento e remarcação direto na agenda
- [x] Árvore de escopo de acesso por nível
- [x] Upload de arquivos de pacientes

### 15.2 Próximas Entregas (Prioridade Alta)

- [ ] Validar webhook Mercado Pago com assinatura HMAC em produção
- [ ] Baixar evento de cobrança para ciclo local (`saas_subscription_cycles`)
- [ ] Bloqueio / desbloqueio automático de tenant por inadimplência

### 15.3 Backlog

- [ ] Tela de configuração comercial (credenciais MP + parâmetros SaaS por tenant)
- [ ] Portal do cliente (tenant acompanha assinatura e faturas)
- [ ] Notificações / lembretes de consulta via WhatsApp
- [ ] Relatórios PDF de prontuário
- [ ] Onboarding self-service (cadastro de clínica sem intervenção admin)
- [ ] Controle de limites do plano em tempo real (max_profissionais, etc.)
- [ ] Dashboard de métricas para o admin (MRR, churn, tenants ativos)
- [ ] Prontuário adaptado por especialidade (labels/campos diferentes por tipo de profissional)

---

## 16. Operação SaaS — Passo a Passo

### Pré-requisito: Executar migração

Acesse `https://utecnologia.com.br/adm/dev/migrar_fase1_saas` logado como admin nível 1. Rota idempotente — pode ser re-executada para aplicar novas colunas.

### Configurar Mercado Pago

1. Abra `application/config/mercadopago.php`
2. Preferencialmente configure via variáveis de ambiente: `MERCADOPAGO_ACCESS_TOKEN`, `MERCADOPAGO_PUBLIC_KEY`, `MERCADOPAGO_WEBHOOK_SECRET`
3. Configure o webhook no painel MP: `https://utecnologia.com.br/webhooks/mercadopago`

### Criar Plano SaaS

1. Acesse `adm/produtos` → Cadastre o plano
2. Preencha: `plan_code`, ciclo, intervalo, trial, setup_fee, limites de profissionais/colaboradores/pacientes
3. Salve com status ativo

### Provisionar Tenant

1. Acesse `adm/saas` → seção "Provisionar clínica"
2. Escolha responsável base, nome comercial, tipo, plano, ciclo, valor
3. Clique em "Provisionar tenant" — o sistema cria: tenant + assinatura + primeiro ciclo + vincula `tenant_id`/`tenant_role`

### Gerar Checkout Recorrente

1. Abra o tenant em `adm/saas/tenant/{id}`
2. Clique em "Gerar checkout MP"
3. O sistema cria o Preapproval no MP e grava `gateway_subscription_id`, `checkout_url`
4. Redireciona para o checkout do Mercado Pago

### Acompanhar Assinatura

- `adm/saas` — visão geral de tenants
- `adm/saas/tenant/{id}` — equipe, assinatura, ciclos de cobrança
- O webhook MP atualiza `saas_subscriptions` e registra em `saas_billing_events`

---

## 17. Prontuário por Especialidade

### 17.1 Estrutura Atual do Prontuário

O prontuário de atendimento vive na tabela `agendamentos` com três campos de texto livre:

| Campo DB | Label atual | Placeholder atual |
|----------|-------------|-------------------|
| `atendimento_inicial` | Atendimento inicial | Queixa principal, contexto e primeiros registros |
| `avaliacao` | Avaliação | Avaliação clínica, hipóteses e condutas adotadas |
| `reavaliacao` | Reavaliação | Evolução, retorno ou observações complementares |

Esses campos são genéricos e servem bem para **Clínica Médica** e especialidades de consulta padrão.

A view que renderiza o form de atendimento ativo é `application/views/adm/usuarios/new/prontuario.php` (seção "Registro do atendimento em andamento", condicional `$id_agenda > 0`).

### 17.2 Mapeamento por Especialidade — Labels Propostos

A estratégia de **curto prazo** é manter os 3 campos do banco e apenas adaptar labels e placeholders conforme a especialidade do prestador logado. Zero mudança de banco, impacto visual imediato.

| Especialidade | `atendimento_inicial` | `avaliacao` | `reavaliacao` |
|---------------|-----------------------|-------------|---------------|
| **Clínica Médica** (padrão) | Queixa Principal | Avaliação Clínica / Hipóteses | Evolução / Retorno |
| **Fisioterapia** (id 10) | Queixa / Avaliação Postural | Evolução da Sessão / Técnicas Aplicadas | Resposta ao Tratamento / Próxima Sessão |
| **Psicologia** (id 36) | Demanda Apresentada | Evolução da Sessão | Observações / Encaminhamentos |
| **Odontologia** (id 28) | Queixa / Motivo | Procedimento(s) Realizado(s) | Prescrição / Retorno |
| **Psiquiatria** (id 37) | Queixa Principal / MSE | Avaliação / Hipótese Diagnóstica | Conduta / Ajuste Terapêutico |
| **Nutrição** (id 27) | Queixa / Anamnese Alimentar | Avaliação Nutricional / Condutas | Evolução / Plano Alimentar |
| **Pediatria** (id 33) | Queixa / Dados do Responsável | Exame Físico / Hipóteses | Conduta / Retorno |

### 17.3 Campos Específicos por Especialidade — Médio Prazo

Algumas especialidades precisam de campos que não cabem nos 3 textareas genéricos:

**Fisioterapia:**
- Escala de dor EVA (0–10) — campo numérico
- Região corporal tratada — select (Coluna Cervical, Lombar, Ombro, Joelho, etc.)
- Fase do tratamento — (Aguda / Subaguda / Crônica / Alta)

**Odontologia:**
- Dente(s) tratado(s) — numeração FDI (ex: 36, 47) — campo texto
- Anestesia utilizada — checkbox + qual (Articaína, Lidocaína, etc.)
- Material utilizado — texto livre (compósito, amálgama, cimento...)

**Psicologia:**
- Sessão nº — contador automático por paciente
- Modalidade — (Individual / Casal / Grupo / Online)

### 17.4 Estratégia de Implementação

**Fase 1 — Imediato: Labels dinâmicos por especialidade (sem mudança de banco)**
- Identificar o prestador vinculado ao agendamento (ou o usuário logado)
- Carregar `usuarios_especialidades.id` via JOIN com `usuarios`
- No controller `Atendimento::prontuario()`, passar `$especialidade_id` à view
- Na view, usar `switch($especialidade_id)` para definir labels e placeholders dos 3 campos

**Fase 2 — Campos extras JSON em `agendamentos`**
- Adicionar coluna `campos_extras` TEXT em `agendamentos` via `Dev.php`
- Criar tabela `especialidades_campos_config` com a definição de campos extras por `especialidade_id`: (`id`, `especialidade_id`, `campo_chave`, `campo_label`, `campo_tipo`, `campo_opcoes`, `ordem`)
- O form de atendimento renderiza os campos extras definidos para a especialidade
- Salvar como JSON em `agendamentos.campos_extras`
- MariaDB 10.11 (servidor de produção) tem suporte a `JSON_VALUE` e `JSON_SET`

**Fase 3 — Motor configurável (implementado)**
- Interface admin em `adm/especialidades` (nível 1) para CRUD de `especialidades_campos_config`
- Formulário: especialidade, chave, label, tipo (text/textarea/number/select/radio), opções (uma por linha → JSON), placeholder, ordem, obrigatório, status
- Fase 2 e 3 dependem de `adm/dev/migrar_fase2_prontuario_especialidades` ter sido rodada

### 17.5 Especialidades Cadastradas (`usuarios_especialidades`)

IDs fixos inseridos por `adm/dev/migrar_especialidades`. Relevantes para o prontuário:

| id | Especialidade | Prontuário tem particularidade? |
|----|---------------|--------------------------------|
| 6  | Cirurgia Plástica | Não (consulta padrão + campos de avaliação estética) |
| 7  | Clínica Médica | Não — é o padrão atual |
| 10 | Fisioterapia | **Sim** — sessões, EVA, região, técnica |
| 11 | Fonoaudiologia | Sim — evolução por sessão |
| 14 | Ginecologia e Obstetrícia | Sim — DUM, IG, exames específicos |
| 27 | Nutrição | Sim — anamnese alimentar, peso, IMC |
| 28 | Odontologia | **Sim** — dente, procedimento, anestesia |
| 33 | Pediatria | Sim — peso/altura, dados do responsável |
| 36 | Psicologia | **Sim** — sessão confidencial, modalidade |
| 37 | Psiquiatria | Sim — MSE, CID, ajuste medicamentoso |
