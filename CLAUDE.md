# CLAUDE.md — UTecnologia Saúde

Documentação técnica do projeto para uso nas sessões com Claude Code.

---

## Visão Geral

**UTecnologia Saúde** é um sistema web de gestão clínica voltado para clínicas médicas e profissionais de saúde. Está em fase de desenvolvimento ativo com clientes interessados. O objetivo é ser um produto SaaS comercializável.

- **URL de produção:** https://utecnologia.com.br/
- **Stack:** PHP 5/7 + CodeIgniter 3.1.10 + MySQL + Bootstrap 4 + jQuery
- **Ambiente local:** `c:\htdocs\utec` (WAMP/XAMPP)
- **Template base:** Adminto (tema admin antigo, sendo modernizado)

---

## Arquitetura CodeIgniter 3

```
c:\htdocs\utec\
├── application/
│   ├── config/           # Configurações (database, routes, session, etc.)
│   ├── controllers/      # Controllers raiz + subpastas adm/ e rpg/
│   ├── models/           # Models raiz + subpastas adm/ e rpg/
│   ├── views/            # Views + subpastas adm/
│   ├── libraries/        # Wrappers de PDF (mPDF, TCPDF)
│   └── third_party/      # mPDF library
├── system/               # Core CodeIgniter 3.1.10 (não modificar)
├── bower_components/     # Frontend: Bootstrap, jQuery plugins, FullCalendar
├── imagens/              # Upload de imagens (usuarios/, produtos/)
├── js/                   # Scripts JavaScript customizados
└── index.php             # Entry point (mod_rewrite ativo, sem index.php na URL)
```

---

## Banco de Dados

### Conexões Configuradas (`application/config/database.php`)

| Chave      | Banco                  | Host                         | Uso                          |
|------------|------------------------|------------------------------|------------------------------|
| `default`  | `utecnologiacom_db`    | localhost                    | BD principal da aplicação    |
| `db2`      | `chwtppbr_db`          | localhost                    | Chatbot WhatsApp (local)     |
| `dbbot`    | `chwtppbr_db`          | chatbot-whatsapp-br.com.br   | Chatbot WhatsApp (remoto)    |
| `dbpi`     | `produtos_pi`          | produtosinovadores.com.br    | Produtos Inovadores (externo)|

- Driver: `mysqli`
- Charset: `utf8mb4` (conexão default), `utf8` (demais)
- `save_queries = TRUE` — manter em dev, desativar em produção

### Tabelas Principais (inferidas do código)

**Usuários e Acesso**
- `usuarios` — todos os usuários do sistema (pacientes, médicos, admins)
- `usuarios_niveis` — tipos/níveis de acesso
- `usuarios.tenant_id` — vínculo do usuário ao tenant SaaS
- `usuarios.tenant_role` — papel do usuário dentro do tenant (`owner`, `admin`, `provider`, `staff`, `patient`)
- `usuarios.onboarding_status` — situação de ativação do usuário no tenant

**Saúde e Agenda**
- `agendamentos` — consultas agendadas (liga paciente ↔ prestador)
- `exames` — catálogo de exames disponíveis
- `usuarios_exames` — exames solicitados por agendamento
- `usuarios_exames_atendimento` — exames realizados por usuário

**Produtos e Pedidos**
- `produtos` — catálogo de produtos/serviços
- `produtos_categorias` — categorias de produtos
- `carrinho` — carrinho de compras ativo
- `carrinho_hist` — histórico de carrinhos
- `pedidos` — pedidos finalizados
- `produtos.plan_code` — código comercial/técnico do plano SaaS
- `produtos.billing_interval` / `billing_interval_count` — recorrência do plano
- `produtos.trial_days` / `setup_fee` — trial e taxa de implantação
- `produtos.max_profissionais` / `max_colaboradores` / `max_pacientes` — limites comerciais do plano
- `pedidos.tenant_id` / `subscription_id` / `gateway_payment_id` — vínculo entre pedido, tenant e pagamento

**Arquivos de Pacientes**
- `pacientes_arquivos` — arquivos enviados por paciente (id_paciente, id_agendamento, arquivo, tipo, descricao)
- Armazenados em `uploads/pacientes/` com nome encriptado

**SaaS / Multi-clínica**
- `saas_tenants` — cadastro da clínica/consultório/profissional locatário da plataforma
- `saas_subscriptions` — assinatura principal do tenant (plano, ciclo, status, cobrança)
- `saas_subscription_cycles` — ciclos de cobrança da assinatura
- `saas_billing_events` — eventos financeiros e webhooks de gateway

**Integrações**
- `acessos` — analytics de pageviews (IP, navegador, página)
- `api_conv_fb` — eventos enviados ao Facebook Pixel
- `pi_whats_users` — usuários vinculados ao WhatsApp
- Mercado Pago — cobrança avulsa legada + assinatura recorrente SaaS

**RPG (módulo educacional)**
- `rpg_personagens`, `rpg_personagens_atributos`
- `rpg_items`, `rpg_user_inventory`
- `rpg_locations`, `rpg_dialogos`, `rpg_progress`

---

## Níveis de Usuário (`usuarios.nivel`)

| Nível | Perfil             | Comportamento pós-login                         |
|-------|--------------------|-------------------------------------------------|
| 1     | Administrador      | Redireciona para `adm/usuarios`                 |
| 2     | Estabelecimento    | Redireciona para `adm/atendimento`              |
| 3     | Prestador          | Redireciona para `adm/atendimento`              |
| 4     | Colaborador        | Redireciona para `adm/atendimento`              |
| 5     | Paciente           | Redireciona para `adm/usuarios` (lista pacientes)|

### Regras de Escopo por Nível

- **Nível 1 — Administrador**
  - Vê tudo de todos.
  - Não possui restrição de escopo clínico ou comercial.

- **Nível 2 — Estabelecimento**
  - Vê pacientes, agenda, exames e relatórios gerados por ele e por toda a árvore de usuários vinculados ao seu `id`.
  - Na prática, o escopo inclui o próprio usuário e todos os registros criados por usuários cujo `id_user` aponta para o estabelecimento, incluindo descendentes.

- **Nível 3 — Prestador**
  - Vê tudo o que cadastrou e tudo o que os colaboradores vinculados a ele registraram.
  - O escopo inclui o próprio prestador e sua árvore de usuários descendentes.

- **Nível 4 — Colaborador**
  - Vê o que cadastrou e também o que outros colaboradores vinculados ao mesmo `id_user` registraram.
  - O escopo inclui os colaboradores irmãos do mesmo vínculo e os registros descendentes dessa subárvore.

- **Nível 5 — Paciente**
  - Sem portal dedicado no momento.
  - Base preparada para futuras notificações de agendamento e evolução de relacionamento.

### Observações de Vínculo (`usuarios.id_user`)

- `usuarios.id_user` define o vínculo operacional do usuário dentro da árvore de acesso.
- O escopo clínico atual usa essa relação para filtrar:
  - lista de pacientes,
  - agenda clínica,
  - prontuário,
  - checklist de exames,
  - relatórios clínicos,
  - catálogo de planos,
  - tipos de plano,
  - assinaturas e detalhes das contratações.
- Regras práticas de cadastro:
  - `Administrador` pode definir manualmente o vínculo operacional no cadastro e na edição.
  - `Estabelecimento` cria `Prestador`, `Colaborador` e `Paciente` vinculados ao próprio estabelecimento.
  - `Prestador` cria `Colaborador` e `Paciente` vinculados ao próprio prestador.
  - `Colaborador` cria `Paciente` herdando o vínculo principal do grupo (`id_user` do estabelecimento ou prestador).
- O helper central dessas regras está em `Padrao_model.php`.

---

## Controllers

### Raiz (`application/controllers/`)

| Arquivo         | Rota         | Função                                              |
|-----------------|--------------|-----------------------------------------------------|
| `Home.php`      | `/`          | Landing page pública                                |
| `Admin.php`     | `/admin`     | Login e utilitários de migração                     |
| `User.php`      | `/user`      | Carrinho de compras, pedidos e integrações Mercado Pago legadas |

### Admin (`application/controllers/adm/`)

| Arquivo           | Rota                  | Função                                               |
|-------------------|-----------------------|------------------------------------------------------|
| `Usuarios.php`    | `/adm/usuarios`       | CRUD de usuários, prontuários, upload de fotos       |
| `Atendimento.php` | `/adm/atendimento`    | Agendamentos, prontuários, exames, status            |
| `Atencimento.php` | `/adm/atencimento`    | **Legado** renomeado para `.bak`                     |
| `Produtos.php`    | `/adm/produtos`       | CRUD de planos, tipos de plano e assinaturas legadas |
| `Saas.php`        | `/adm/saas`           | Operação SaaS: tenants, assinatura, checkout Mercado Pago e webhook |
| `Dev.php`         | `/adm/dev`            | Migrações e utilitários de desenvolvimento           |

> `Atencimento.php` não é mais controller ativo; o arquivo legado foi renomeado para `.bak`.

---

## Models

### Raiz (`application/models/`)

| Arquivo           | Uso                                                             |
|-------------------|-----------------------------------------------------------------|
| `Padrao_model.php` | Model base com helpers: `get_by_id`, `get_qr`, `del_by_id`, `converte_data`, `indexador` |
| `FbApi_model.php`  | Integração com Facebook Conversions API (eventos/pixels)       |

### Admin (`application/models/adm/`)

| Arquivo              | Uso                                                         |
|----------------------|-------------------------------------------------------------|
| `Usuarios_model.php` | Login (`logar`), validação de sessão (`verSession`), cadastro |
| `Produtos_model.php` | CRUD de planos e tipos de plano                             |
| `Saas_model.php`     | Provisionamento de tenant, leitura de dashboard SaaS, sincronização de cobrança |

### RPG (`application/models/rpg/`)

| Arquivo                | Uso                                             |
|------------------------|-------------------------------------------------|
| `Personagens_model.php`| Stats de personagem (força, HP, XP, etc.)       |
| `Armas_model.php`      | Inventário de armas e itens de consumo          |
| `Itens_model.php`      | Uso de itens (ex: poção de cura +20 HP)         |
| `Dialogos_model.php`   | Sistema de diálogos por localização             |
| `Locations_model.php`  | Mapa de localizações do mundo RPG               |

---

## Views

### Estrutura Principal

```
application/views/
├── index-front.php                   # Landing page pública (Inter font, gradiente azul/verde)
└── adm/
    ├── login.php                     # Tela de login (tema antigo MWS)
    ├── dash.php                      # Dashboard principal (51KB)
    ├── index.php                     # Página inicial admin
    ├── usuarios/
    │   ├── novo.php                  # Formulário novo usuário (legado)
    │   ├── lista.php                 # Lista usuários (legada)
    │   └── new/                      # Views modernizadas (USAR ESTAS)
    │       ├── lista.php             # Lista de usuários
    │       ├── cadastro.php          # Cadastro por nível
    │       ├── edicao.php            # Edição de usuário
    │       ├── prontuario.php        # Prontuário do paciente
    │       ├── atendimentos.php      # Lista de atendimentos
    │       └── exames.php            # Gestão de exames
    ├── saas/
    │   ├── index.php                 # Dashboard operacional SaaS
    │   └── tenant.php                # Detalhe do tenant, assinatura e equipe
    └── atendimento/
        └── atendimento.php           # Formulário de atendimento (19KB)
```

> As views em `adm/usuarios/new/` são as ativas. As raiz de `adm/usuarios/` são legado.

---

## Frontend / CSS

- **Template:** Adminto (tema admin Bootstrap 4)
- **CSS principal:** `css/clicklinica-main.css`
- Dependência externa principal já foi internalizada
- **Fonte:** Lato (Google Fonts) nas views admin; Inter na landing page
- **Bower Components:** Bootstrap, Select2, FullCalendar, Perfect Scrollbar, Slick Carousel, Dropzone, DateRangePicker, DataTables

---

## Upload de Imagens

- **Usuários:** `imagens/usuarios/` (original) + `imagens/usuarios/min/` (120×72) + `imagens/usuarios/des/` (300×210)
- **Produtos:** `imagens/produtos/`
- Usa biblioteca nativa CI `upload` + `image_lib` (GD2)

---

## Geração de PDF

- **mPDF** (via `application/libraries/M_pdf.php`)
- **TCPDF** (via `application/libraries/tcpdf/`)

---

## Problemas Conhecidos / Débitos Técnicos

| Severidade | Status | Problema                                                                             |
|------------|--------|--------------------------------------------------------------------------------------|
| 🔴 Alta     | ✅ Resolvido | Senhas em texto puro — `password_hash()` implementado com migração suave     |
| 🔴 Alta     | ✅ Resolvido | SQL injection — cast `(int)` em IDs de URL + `$this->input->post()` em forms |
| 🔴 Alta     | ✅ Resolvido | CSS de domínio externo — baixado para `css/clicklinica-main.css`             |
| 🟡 Média    | ✅ Resolvido | Base SaaS inicial criada — tenants, subscriptions, cycles e billing events   |
| 🟡 Média    | ✅ Resolvido | Mercado Pago centralizado em `application/config/mercadopago.php`            |
| 🟡 Média    | ✅ Resolvido | `ereg_replace()` — substituído por `preg_replace()` / `str_replace()`       |
| 🟡 Média    | ✅ Resolvido | Controller duplicado `Atencimento.php` — renomeado para `.bak`               |
| 🟡 Média    | ✅ Resolvido | `$_POST` direto — substituído por `$this->input->post()` nos controllers     |
| 🟡 Média    | Pendente | Webhook Mercado Pago ainda precisa ser validado em ambiente real               |
| 🟡 Média    | Pendente | Ciclos pagos / bloqueio automático por inadimplência ainda não estão completos |
| 🟢 Baixa   | Pendente | Muitos comentários `#` e código comentado — limpar gradualmente              |
| 🟢 Baixa   | Pendente | Views com textos em inglês ("Start typing to search...", "Projects", etc.)  |

### Notas da Migração de Senhas (importante para deploy)
- Login: testa `password_verify()` primeiro; se falhar, compara texto puro e rehasha automaticamente
- Cadastro e edição: geram `password_hash()` direto
- Troca de senha (`alterar()`): valida com `password_verify()` + aceita texto puro em fallback
- "Acessar como" na lista de usuários: substituído por `/admin/logar_como/{id}` (apenas admin nivel=1)
- Campo senha na tela de edição: deixado em branco — se vazio, não atualiza a senha existente

---

## Rotas Definidas (`application/config/routes.php`)

```php
$route['default_controller'] = 'home';
$route['locations'] = 'rpgLocations/index';
$route['webhooks/mercadopago'] = 'adm/saas/webhook_mercadopago';
```

Todas as outras rotas seguem o padrão CI padrão: `controller/metodo/parametro`.

---

## Convenções do Projeto

- Controllers admin ficam em `application/controllers/adm/`
- Models admin ficam em `application/models/adm/`
- Views novas ficam em `application/views/adm/[modulo]/new/` quando reformuladas
- Views do módulo SaaS ficam em `application/views/adm/saas/`
- `Padrao_model` deve ser carregado em todos os controllers como model utilitário
- Sessão: `$this->session->userdata('id')`, `'nome'`, `'nivel'`, `'login'`, `'usr'`
- Redirect pós-login é por nível (ver tabela de níveis acima)
- Configuração do Mercado Pago fica centralizada em `application/config/mercadopago.php`
- Para assinatura recorrente SaaS, usar `Mercadopago_saas.php` em vez de repetir token no controller

---

## Controller de Banco (Utilitário para Dev)

O controller padrão para migrações e setup local é `application/controllers/adm/Dev.php`.

Rotas úteis já criadas:
- `adm/dev/criar_tabela_arquivos_paciente`
- `adm/dev/migrar_fase1_saas`

`migrar_fase1_saas` é idempotente e:
- cria as tabelas `saas_tenants`, `saas_subscriptions`, `saas_subscription_cycles`, `saas_billing_events`
- adiciona campos SaaS em `usuarios`, `produtos`, `pedidos` e `carrinho_hist`
- pode ser executado novamente com segurança para completar colunas novas

---

## Próximas Features Planejadas

*(Atualizar conforme o projeto evolui)*

- [ ] Validar webhook Mercado Pago em ambiente real com eventos de assinatura
- [ ] Baixar evento de cobrança para ciclo local (`saas_subscription_cycles`)
- [ ] Bloqueio / desbloqueio automático de tenant por inadimplência
- [ ] Tela de configuração comercial com credenciais Mercado Pago e parâmetros SaaS
- [ ] Notificações / lembretes de consulta via WhatsApp
- [ ] Relatórios PDF de prontuário
- [ ] Portal simplificado para cliente/tenant acompanhar assinatura
- [x] Multi-clínica / multi-tenant (base estrutural)
- [x] Operação SaaS com dashboard e provisionamento manual
- [x] Checkout recorrente Mercado Pago por assinatura
- [x] Timeline de prontuário
- [x] Relatórios clínicos
- [x] Módulo comercial reposicionado para planos/assinaturas
- [x] Agenda com filtros operacionais
- [x] Checklist operacional de exames
- [x] Cancelamento e remarcação direto na agenda

---

## Passo a Passo da Nova Área SaaS

### 1. Executar a migração

1. Acesse `https://utecnologia.com.br/adm/dev/migrar_fase1_saas` logado como admin nível 1.
2. Confirme se a página retornou `OK` para criação das tabelas e colunas.
3. Sempre que a fase 1 evoluir com novas colunas, rode a mesma rota novamente.

### 2. Configurar o Mercado Pago

1. Abra `application/config/mercadopago.php`.
2. Revise:
   - `mercadopago_access_token`
   - `mercadopago_public_key`
   - `mercadopago_back_url_success`
   - `mercadopago_back_url_pending`
   - `mercadopago_back_url_failure`
3. No painel do Mercado Pago, configure o webhook para:
   - `https://utecnologia.com.br/webhooks/mercadopago`

### 3. Criar ou revisar os planos SaaS

1. Acesse `adm/produtos`.
2. Cadastre ou edite o plano.
3. Preencha os novos campos:
   - `Codigo do plano`
   - `Ciclo`
   - `Intervalo`
   - `Trial`
   - `Taxa setup`
   - limites de `Prof.`, `Colab.` e `Pac.`
4. Salve o plano com status ativo.

### 4. Provisionar a clínica / tenant

1. Acesse `adm/saas`.
2. Na seção `Provisionar clinica`, escolha:
   - `Responsavel base`
   - `Nome comercial do tenant`
   - `Tipo`
   - `Plano`
   - `Ciclo`, `Intervalo`, `Trial`, `Valor recorrente`
   - `Setup`, `Gateway`, `Referencia gateway`, `Documento`, contatos
3. Clique em `Provisionar tenant`.
4. O sistema irá:
   - criar o tenant
   - criar a assinatura
   - criar o primeiro ciclo
   - vincular `tenant_id` e `tenant_role` ao responsável e à árvore de usuários

### 5. Gerar o checkout recorrente

1. Abra o tenant em `adm/saas`.
2. Na tabela de assinaturas, clique em `Gerar checkout MP`.
3. O sistema cria o `Preapproval` no Mercado Pago e grava:
   - `gateway_subscription_id`
   - `gateway_reference`
   - `checkout_url`
   - status inicial da assinatura
4. O usuário será redirecionado para o checkout do Mercado Pago.

### 6. Acompanhar a assinatura

1. Volte em `adm/saas` para visão geral.
2. Abra o tenant para ver:
   - equipe vinculada
   - assinatura ativa/pendente/cancelada
   - ciclos de cobrança
3. O webhook do Mercado Pago atualiza o status em `saas_subscriptions` e registra evento em `saas_billing_events`.

### 7. Cuidados operacionais atuais

- O webhook já existe, mas ainda precisa ser validado ponta a ponta em produção.
- O status da assinatura já volta para o sistema, mas a lógica completa de bloqueio por inadimplência ainda não foi fechada.
- O provisionamento manual é o fluxo correto nesta fase; onboarding 100% automático ainda é etapa futura.
