# Senha no cadastro + e-mails de acesso + "Esqueci minha senha" — Design

**Data:** 2026-09-28
**Status:** aprovado pelo dono do produto
**Domínios:** clínico (usuários/acesso), frontend (views públicas + login), dev-infra (deploy)

## 1. Problema

Usuários conseguem entrar no primeiro acesso mas não voltam na segunda visita.

Causa raiz no código:

- `/experimentar` (trial, principal porta de entrada) **gera a senha no servidor**
  (`Saas_model::create_operational_trial_signup()`, ~linha 751) e o formulário envia
  `senha` vazio (hidden em `public/experimentar.php`). O primeiro acesso é auto-login,
  então o usuário nunca vê nem escolhe a senha — só a recebe no e-mail de boas-vindas,
  que pode não ter sido lido ou ter caído no spam.
- Não existe "Esqueci minha senha": `Admin::esqueceuSenha()` carrega
  `adm/esqueceu-senha`, view inexistente, e nenhuma tela de login linka para ela.
- Login com senha errada faz `redirect('admin')` sem mensagem nenhuma — o usuário não
  sabe se errou login, senha, ou se o sistema falhou.
- `/assinar` já pede senha, mas não envia e-mail de boas-vindas.
- Usuários criados pela equipe (`adm/usuarios/cadastrar`) só têm acesso se quem cadastrou
  preencheu a senha e comunicou por fora.

## 2. Decisões (confirmadas com o dono)

| Tema | Decisão |
|------|---------|
| Redefinição | **Link por e-mail** com token de 1h. A senha atual só muda quando o dono do e-mail clica. Nunca se envia senha na redefinição. |
| E-mail de boas-vindas (auto-cadastro) | Inclui **login + a senha escolhida** pelo próprio usuário. |
| Escopo | Trial `/experimentar`, assinatura `/assinar`, usuários criados pela equipe (níveis 2–4), "Esqueci minha senha". |

## 3. Comportamento

### 3.1 Trial `/experimentar`

- Campos visíveis **Senha** e **Confirmar senha** (mín. 6 caracteres, `autocomplete="new-password"`).
- `create_operational_trial_signup()` usa `$data['senha']`/`$data['senha_confirmacao']`;
  valida com `utec_acesso_validar_senha()`; erro volta para `experimentar` com o flash
  `operational_trial_error` já existente.
- Não gera mais senha aleatória nem `senha_token` no trial (o token era só para "definir
  senha personalizada", que deixa de fazer sentido).
- Auto-login mantido.
- E-mail de boas-vindas: login, **senha escolhida**, data de fim do trial, botão
  "Entrar no sistema" (`/admin`) e link "Esqueci minha senha" (`/acesso/esqueci`).

### 3.2 Assinatura `/assinar`

- Adiciona **Confirmar senha**; `create_public_tenant_signup()` passa a validar via
  `utec_acesso_validar_senha()`.
- `Home::contratar()` envia o mesmo e-mail de boas-vindas (sem linha de trial; com
  nome do plano quando disponível). `create_public_tenant_signup()` passa a devolver
  `login`, `tenant_nome` e `senha` no resultado (a senha só trafega em memória, nunca
  é gravada em claro).

### 3.3 Usuários criados pela equipe (`adm/usuarios/cadastrar`, níveis 2–4)

Paciente (nível 5) fica fora — não tem portal.

- **Senha preenchida** e usuário tem e-mail válido → e-mail "Seu acesso foi criado" com
  login + senha + nome de quem cadastrou/estabelecimento.
- **Senha em branco** e e-mail válido → grava senha aleatória interna (hash), gera
  `senha_token` com validade de **7 dias** e envia e-mail com login + botão
  "Definir minha senha" (`/acesso/senha/{token}`).
- **Sem e-mail válido** → comportamento atual; se a senha também estiver em branco,
  flash de aviso: "Usuário criado sem e-mail e sem senha — ele não conseguirá entrar
  até você definir uma senha na edição."
- Login em branco (níveis 2–4) com e-mail válido → usa o e-mail como login (hoje o
  usuário fica sem login e nunca entra).
- Formulário (`new/cadastro.php`): texto de ajuda sob o campo senha:
  "Deixe em branco para o próprio usuário criar a senha pelo link enviado ao e-mail."

### 3.4 Esqueci minha senha

- Rotas: `acesso/esqueci` (GET, formulário) e `acesso/esqueci/enviar` (POST) →
  `Home::esqueci_senha()` / `Home::enviar_redefinicao()`.
- Formulário pede **e-mail ou login**. Busca usuário com
  `login = X OR email = X` (trim + lowercase no e-mail). Não filtra por `status`,
  porque `logar()` também não filtra — quem consegue entrar consegue redefinir. Só age se o usuário tiver
  e-mail válido e nível 1–4.
- **Mais de um usuário** para o mesmo e-mail: envia um e-mail por usuário (cada um com
  seu token), cada e-mail mostra o login correspondente. Limite: 5 usuários.
- Throttle: o token foi criado em `senha_token_expires - 60 min`; se isso foi há menos
  de 2 min, não reenvia (`utec_acesso_pode_reenviar()`). Um convite de equipe (7 dias)
  ainda válido não bloqueia o pedido — é substituído pelo token de 1h.
- Grava `senha_token` (64 hex, `random_bytes(32)`) e `senha_token_expires = NOW()+1h`.
  Um novo pedido substitui o token anterior.
- Resposta **sempre igual** (existindo ou não a conta):
  "Se houver uma conta com esses dados, enviamos um link para o e-mail cadastrado.
  O link vale por 1 hora." — não revela quais e-mails existem.
- Links "Esqueci minha senha" em: `adm/login.php`, modal de login e seção de login de
  `index-front.php`.
- `Admin::esqueceuSenha()` → `redirect('acesso/esqueci')`.

### 3.5 Tela de definir senha (existente, ajustes)

- `definir_senha()`/`salvar_senha()` continuam iguais no núcleo. Ajustes:
  - token inválido → redirect para `admin` (não `experimentar`) com flash;
  - validação via `utec_acesso_validar_senha()`;
  - após salvar, **sempre** grava a sessão do usuário do token (hoje, se outra pessoa
    estiver logada no navegador, a sessão antiga é mantida), com `usr => true` e
    redirect por nível (mesma regra de `Usuarios_model::logar()`);
  - texto "Prefere entrar com a senha provisória?" vira "Voltar para o login".

### 3.6 Login

- Falha de login (usuário inexistente ou senha errada) → flash
  "Usuário ou senha inválidos." exibido em `adm/login.php`, com o link
  "Esqueci minha senha" em destaque.
- `logar()` faz `trim()` no login.
- `adm/login.php` também exibe flash de sucesso ("Enviamos o link…") e de erro de token.

## 4. Arquitetura

| Unidade | Responsabilidade |
|---------|------------------|
| `application/helpers/acesso_helper.php` (novo) | Funções puras: `utec_acesso_validar_senha($senha, $conf)` → `['ok','msg']`; `utec_acesso_gerar_token()`; `utec_acesso_senha_aleatoria($len)`; `utec_acesso_pode_reenviar($expires, $agora, $validade_min, $intervalo_min)`; `utec_acesso_email_valido($email)`; montadores de HTML `utec_acesso_email_boas_vindas($d)`, `utec_acesso_email_equipe($d)`, `utec_acesso_email_redefinicao($d)` (layout comum via `utec_acesso_email_layout($titulo, $conteudo)`; todo dado dinâmico com `htmlspecialchars`). |
| `application/libraries/Email_acesso.php` (novo) | Envio: `boas_vindas($dados)`, `acesso_equipe($dados)`, `redefinicao($dados)`. Carrega `config/email`, monta com o helper, envia de `suporte@utecnologia.com.br`. Retorna bool; captura exceção e `log_message('error', 'email_acesso ...')` com o resultado de `print_debugger(['headers'])` em falha. Nunca lança. |
| `Home.php` | Remove `_enviar_email_boas_vindas()` (HTML migra para o helper); usa `Email_acesso`; novos `esqueci_senha()`/`enviar_redefinicao()`; ajustes em `definir_senha`/`salvar_senha`. |
| `Saas_model.php` | Trial usa senha do POST; assinatura valida confirmação e devolve dados para o e-mail. |
| `adm/Usuarios.php::cadastrar()` | Regras da seção 3.3. |
| `Usuarios_model::logar()` | Flash de erro + trim. |
| Views | `public/experimentar.php`, `public/assinar.php`, `public/esqueci-senha.php` (nova, mesmo visual de `definir-senha.php`), `public/definir-senha.php`, `adm/login.php`, `index-front.php`, `adm/usuarios/new/cadastro.php`. |
| `config/routes.php` | `acesso/esqueci`, `acesso/esqueci/enviar`. |
| `Manual_conteudo.php` | Tópico "Esqueci minha senha" / primeiro acesso no capítulo de acesso. |

**Sem migração:** reaproveita `usuarios.senha_token` e `usuarios.senha_token_expires`
(criadas para o trial). O código continua guardado por `field_exists`; sem as colunas,
"Esqueci minha senha" responde a mensagem genérica e loga erro. Antes do deploy,
confirmar que as colunas existem em produção.

## 5. Erros e segurança

- Falha de e-mail nunca bloqueia cadastro nem redefinição (só log).
- Tokens: 64 hex, uso único (zerado ao salvar), expiração 1h (redefinição) / 7 dias
  (convite da equipe). Consulta sempre com `$this->db->escape()`.
- Resposta de "esqueci" idêntica para conta existente/inexistente.
- Senha em claro aparece **somente** no e-mail de boas-vindas/equipe (decisão do dono);
  nunca em log, nunca na redefinição.
- Hoje o boas-vindas vai com cópia oculta (BCC) para `igor_marlus@yahoo.com.br`. Como
  agora o e-mail carrega a senha escolhida pelo cliente, o BCC **sai** do e-mail do
  cliente e vira um e-mail interno separado "Novo cadastro" (clínica, login, tipo,
  plano — **sem senha**), enviado pelo mesmo `Email_acesso::boas_vindas()`.

## 6. Testes

- `tests/acesso_helper_test.php` — validação de senha (curta, divergente, ok), token
  (64 hex, únicos), senha aleatória (tamanho/charset), `pode_reenviar` (bordas de 2 min),
  e-mail válido, e montadores de HTML (contêm login/senha/link esperados; escapam
  `<script>`; e-mail de redefinição **não** contém a palavra da senha).
- `tests/acesso_source_test.php` (estático, padrão do projeto) — `experimentar.php` não
  tem mais `type="hidden" name="senha"`; `create_operational_trial_signup` não gera
  senha aleatória; `adm/login.php` e `index-front.php` linkam `acesso/esqueci`;
  `Admin::esqueceuSenha` não carrega view inexistente; `routes.php` tem as rotas novas.
- Rodar com PHP 7.2 (`/c/PHP/PHP7.2/php.exe`) e `php -l` em todos os arquivos tocados.
- Verificação manual local: cadastro trial com senha → logout → login com a senha;
  esqueci senha → link (ver e-mail ou log) → nova senha → login; cadastro de colaborador
  com e sem senha.

## 7. Fora de escopo

- Troca de senha obrigatória no primeiro acesso.
- Login por WhatsApp/código.
- Diagnóstico de entregabilidade (SPF/DKIM) — **verificação** entra no deploy (checar
  log de produção por falhas de `email->send()`), correção de DNS fica para ação
  separada se necessário.
