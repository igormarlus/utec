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

**Arquivos de Pacientes**
- `pacientes_arquivos` — arquivos enviados por paciente (id_paciente, id_agendamento, arquivo, tipo, descricao)
- Armazenados em `uploads/pacientes/` com nome encriptado

**Integrações**
- `acessos` — analytics de pageviews (IP, navegador, página)
- `api_conv_fb` — eventos enviados ao Facebook Pixel
- `pi_whats_users` — usuários vinculados ao WhatsApp

**RPG (módulo educacional)**
- `rpg_personagens`, `rpg_personagens_atributos`
- `rpg_items`, `rpg_user_inventory`
- `rpg_locations`, `rpg_dialogos`, `rpg_progress`

---

## Níveis de Usuário (`usuarios.nivel`)

| Nível | Perfil           | Comportamento pós-login                         |
|-------|------------------|-------------------------------------------------|
| 1     | Administrador    | Redireciona para `adm/usuarios`                 |
| 2     | Recepcionista    | Redireciona para `adm/atendimento`              |
| 3     | Médico/Prestador | Redireciona para `adm/atendimento`              |
| 4     | Clínica/Empresa  | Redireciona para `adm/atendimento`              |
| 5     | Paciente         | Redireciona para `adm/usuarios` (lista pacientes)|

---

## Controllers

### Raiz (`application/controllers/`)

| Arquivo         | Rota         | Função                                              |
|-----------------|--------------|-----------------------------------------------------|
| `Home.php`      | `/`          | Landing page pública                                |
| `Admin.php`     | `/admin`     | Login e utilitários de migração                     |
| `User.php`      | `/user`      | Carrinho de compras e pedidos                       |

### Admin (`application/controllers/adm/`)

| Arquivo           | Rota                  | Função                                               |
|-------------------|-----------------------|------------------------------------------------------|
| `Usuarios.php`    | `/adm/usuarios`       | CRUD de usuários, prontuários, upload de fotos       |
| `Atendimento.php` | `/adm/atendimento`    | Agendamentos, prontuários, exames, status            |
| `Atencimento.php` | `/adm/atencimento`    | **Duplicata antiga** de Atendimento (manter ou remover) |
| `Produtos.php`    | `/adm/produtos`       | CRUD de produtos, categorias, pedidos                |

> **Atenção:** Existem dois controllers de atendimento: `Atendimento.php` (ativo/atual) e `Atencimento.php` (legado). Avaliar remoção do legado.

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
| `Produtos_model.php` | CRUD de produtos e categorias                               |

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
    └── atendimento/
        └── atendimento.php           # Formulário de atendimento (19KB)
```

> As views em `adm/usuarios/new/` são as ativas. As raiz de `adm/usuarios/` são legado.

---

## Frontend / CSS

- **Template:** Adminto (tema admin Bootstrap 4)
- **CSS principal:** carregado externamente de `https://rcatel.com/clicklinica/css/main.css?version=4.5.0`
  > **Problema:** dependência externa de terceiro — migrar para local em produção
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
| 🟡 Média    | ✅ Resolvido | `ereg_replace()` — substituído por `preg_replace()` / `str_replace()`       |
| 🟡 Média    | ✅ Resolvido | Controller duplicado `Atencimento.php` — renomeado para `.bak`               |
| 🟡 Média    | ✅ Resolvido | `$_POST` direto — substituído por `$this->input->post()` nos controllers     |
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
```

Todas as outras rotas seguem o padrão CI padrão: `controller/metodo/parametro`.

---

## Convenções do Projeto

- Controllers admin ficam em `application/controllers/adm/`
- Models admin ficam em `application/models/adm/`
- Views novas ficam em `application/views/adm/[modulo]/new/` quando reformuladas
- `Padrao_model` deve ser carregado em todos os controllers como model utilitário
- Sessão: `$this->session->userdata('id')`, `'nome'`, `'nivel'`, `'login'`, `'usr'`
- Redirect pós-login é por nível (ver tabela de níveis acima)

---

## Controller de Banco (Utilitário para Dev)

Se precisar rodar queries, criar tabelas ou fazer migrações durante o desenvolvimento, usar o controller `Admin.php` que já existe — adicionar métodos ali ou criar um controller `Dev.php` em `application/controllers/adm/Dev.php` com verificação de ambiente.

---

## Próximas Features Planejadas

*(Atualizar conforme o projeto evolui)*

- [ ] Hash de senha (password_hash / bcrypt)
- [ ] Migrar CSS do main.css para local
- [ ] Sanitização de inputs com `$this->input->post()`
- [ ] Dashboard com métricas clínicas (total de pacientes, agendamentos do dia, etc.)
- [ ] Notificações / lembretes de consulta via WhatsApp
- [ ] Relatórios PDF de prontuário
- [ ] Multi-clínica / multi-tenant
