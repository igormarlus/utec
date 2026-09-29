# Senha no cadastro + e-mails de acesso + Esqueci minha senha — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Usuário escolhe a própria senha no cadastro, recebe login + senha por e-mail, e recupera o acesso por "Esqueci minha senha" com link por e-mail.

**Architecture:** Funções puras (validação, token, throttle, HTML dos e-mails) em `application/helpers/acesso_helper.php`, testadas por scripts PHP em `tests/`. Envio concentrado na library `Email_acesso` (CI email + `config/email.php`), usada por `Home`, `adm/Usuarios`. Fluxo de redefinição reaproveita `usuarios.senha_token` / `senha_token_expires` e a tela `acesso/senha/{token}` já existentes — sem migração.

**Tech Stack:** PHP 7.2 (produção PHP 7) + CodeIgniter 3.1.10 + MySQL/MariaDB. Testes = scripts `php tests/x.php` que imprimem `OK` e saem com código 1 em falha.

**Spec:** `docs/superpowers/specs/2026-09-28-senha-cadastro-redefinicao-design.md`

## Global Constraints

- Sintaxe compatível com PHP 7.2: sem arrow functions, sem typed properties, sem `match`, sem `str_contains`.
- Rodar testes e lint com `/c/PHP/PHP7.2/php.exe` (o `php` do PATH é 8.2).
- Não modificar `system/`. Usar `$this->input->post()`, nunca `$_POST` em código novo.
- Senha mínima: **6 caracteres**. Mensagens: `A senha precisa ter pelo menos 6 caracteres.` / `As senhas não coincidem. Tente novamente.`
- Token: 64 hex (`bin2hex(random_bytes(32))`). Validade: **60 min** (redefinição), **7 dias** (convite da equipe). Throttle de reenvio: **2 min**.
- Expiração sempre calculada no MySQL (`DATE_ADD(NOW(), INTERVAL ...)`) — a checagem existente usa `NOW()` do MySQL e o fuso do PHP pode divergir.
- Resposta do "esqueci" sempre: `Se houver uma conta com esses dados, enviamos um link para o e-mail cadastrado. O link vale por 1 hora.`
- Senha em claro só no corpo dos e-mails de boas-vindas/equipe. Nunca em log, nunca no e-mail de redefinição, nunca no e-mail interno.
- Falha de envio de e-mail nunca bloqueia fluxo (só `log_message('error', ...)`).
- Remetente: `suporte@utecnologia.com.br` / `UTecnologia Saúde`. E-mail interno "Novo cadastro" para `igor_marlus@yahoo.com.br`.

## File Structure

| Arquivo | Ação | Responsabilidade |
|---------|------|------------------|
| `application/helpers/acesso_helper.php` | Criar | Funções puras: senha, token, throttle, e-mail válido, HTML dos 4 e-mails |
| `application/libraries/Email_acesso.php` | Criar | Envio via CI email; um método por e-mail |
| `application/views/public/esqueci-senha.php` | Criar | Formulário "Esqueci minha senha" |
| `application/controllers/Home.php` | Modificar | `esqueci_senha`, `enviar_redefinicao`, ajustes em `definir_senha`/`salvar_senha`, boas-vindas via library |
| `application/controllers/Admin.php` | Modificar | `esqueceuSenha()` → redirect |
| `application/models/adm/Usuarios_model.php` | Modificar | `logar()`: trim, rejeita vazio, flash de erro |
| `application/models/adm/Saas_model.php` | Modificar | Trial usa senha do usuário; assinatura valida confirmação; retornos p/ e-mail |
| `application/controllers/adm/Usuarios.php` | Modificar | `cadastrar()`: e-mail de acesso / convite |
| `application/config/routes.php` | Modificar | Rotas `acesso/esqueci*` |
| Views `adm/login.php`, `index-front.php`, `public/experimentar.php`, `public/assinar.php`, `public/experimentar-sucesso.php`, `public/definir-senha.php`, `adm/usuarios/new/cadastro.php`, `adm/usuarios/new/edicao.php` | Modificar | Campos, links, flashes |
| `application/libraries/Manual_conteudo.php` | Modificar | Tópico de primeiro acesso / esqueci senha |
| `tests/acesso_helper_test.php`, `tests/acesso_source_test.php` | Criar | Testes |

---

### Task 1: Helper `acesso_helper.php` (funções puras + HTML dos e-mails)

**Files:**
- Create: `application/helpers/acesso_helper.php`
- Test: `tests/acesso_helper_test.php`

**Interfaces:**
- Produces:
  - `utec_acesso_validar_senha(string $senha, string $confirmacao): array` → `['ok' => bool, 'msg' => string]`
  - `utec_acesso_gerar_token(): string` (64 hex)
  - `utec_acesso_senha_aleatoria(int $tamanho = 12): string`
  - `utec_acesso_email_valido($email): bool`
  - `utec_acesso_pode_reenviar($segundos_restantes, int $validade_min = 60, int $intervalo_min = 2): bool` — `$segundos_restantes` = `TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires)` ou `null`
  - `utec_acesso_email_boas_vindas(array $d): array` → `['assunto' => string, 'html' => string]`; `$d`: `nome`, `login`, `senha`, `tenant_nome`, `trial_fim` (d/m/Y ou ''), `link_login`, `link_esqueci`
  - `utec_acesso_email_equipe(array $d): array`; `$d`: `nome`, `login`, `senha` ('' quando convite), `link_definir` ('' quando tem senha), `link_login`, `link_esqueci`, `cadastrado_por`, `validade_dias`
  - `utec_acesso_email_redefinicao(array $d): array`; `$d`: `nome`, `login`, `link`, `validade_min`
  - `utec_acesso_email_novo_cadastro_interno(array $d): array`; `$d`: `tenant_nome`, `login`, `nome`, `origem` ('trial'|'assinatura'), `plano_nome`

- [ ] **Step 1: Write the failing test**

Create `tests/acesso_helper_test.php`:

```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/acesso_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) {
    if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); }
}

// --- validar senha
assertSameValue(array('ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres.'),
    utec_acesso_validar_senha('12345', '12345'), 'senha curta');
assertSameValue(array('ok' => false, 'msg' => 'As senhas não coincidem. Tente novamente.'),
    utec_acesso_validar_senha('123456', '123457'), 'senhas divergentes');
assertSameValue(array('ok' => true, 'msg' => ''), utec_acesso_validar_senha('123456', '123456'), 'senha ok');
assertSameValue(false, utec_acesso_validar_senha(null, null)['ok'], 'senha nula');

// --- token
$t1 = utec_acesso_gerar_token();
$t2 = utec_acesso_gerar_token();
assertTrue(preg_match('/^[a-f0-9]{64}$/', $t1) === 1, 'token 64 hex');
assertTrue($t1 !== $t2, 'tokens unicos');

// --- senha aleatoria
$s = utec_acesso_senha_aleatoria(16);
assertSameValue(16, strlen($s), 'senha aleatoria tamanho');
assertTrue(preg_match('/^[A-Za-z0-9]+$/', $s) === 1, 'senha aleatoria charset');
assertSameValue(12, strlen(utec_acesso_senha_aleatoria()), 'senha aleatoria default');

// --- email valido
assertSameValue(true, utec_acesso_email_valido(' ana@clinica.com.br '), 'email com espacos');
assertSameValue(false, utec_acesso_email_valido(''), 'email vazio');
assertSameValue(false, utec_acesso_email_valido(null), 'email nulo');
assertSameValue(false, utec_acesso_email_valido('ana@'), 'email invalido');

// --- throttle (validade 60, intervalo 2)
assertSameValue(true, utec_acesso_pode_reenviar(null), 'sem token');
assertSameValue(true, utec_acesso_pode_reenviar(-10), 'token expirado');
assertSameValue(false, utec_acesso_pode_reenviar(3600), 'token recem criado');
assertSameValue(false, utec_acesso_pode_reenviar(3481), 'token criado ha 1m59s');
assertSameValue(true, utec_acesso_pode_reenviar(3480), 'token criado ha 2min');
assertSameValue(true, utec_acesso_pode_reenviar(600), 'token antigo');
assertSameValue(true, utec_acesso_pode_reenviar(6 * 86400), 'convite de 7 dias nao bloqueia');

// --- e-mail boas-vindas
$bv = utec_acesso_email_boas_vindas(array(
    'nome' => 'Ana', 'login' => 'ana@clinica.com.br', 'senha' => 'Minha<Senha>1',
    'tenant_nome' => 'Clinica <b>Sol</b>', 'trial_fim' => '28/10/2026',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
));
assertTrue(strpos($bv['assunto'], 'Clinica <b>Sol</b>') !== false, 'assunto com nome da clinica (texto puro)');
assertTrue(strpos($bv['html'], 'ana@clinica.com.br') !== false, 'boas-vindas tem login');
assertTrue(strpos($bv['html'], 'Minha&lt;Senha&gt;1') !== false, 'boas-vindas tem senha escapada');
assertTrue(strpos($bv['html'], 'Clinica &lt;b&gt;Sol&lt;/b&gt;') !== false, 'boas-vindas escapa clinica');
assertTrue(strpos($bv['html'], '28/10/2026') !== false, 'boas-vindas tem fim do trial');
assertTrue(strpos($bv['html'], 'https://x/acesso/esqueci') !== false, 'boas-vindas tem link esqueci');
$bv2 = utec_acesso_email_boas_vindas(array(
    'nome' => 'Ana', 'login' => 'a@b.co', 'senha' => 'x', 'tenant_nome' => 'T', 'trial_fim' => '',
    'link_login' => 'L', 'link_esqueci' => 'E',
));
assertTrue(strpos($bv2['html'], 'Trial ativo') === false, 'sem trial nao mostra linha de trial');

// --- e-mail equipe (com senha)
$eq = utec_acesso_email_equipe(array(
    'nome' => 'Bia', 'login' => 'bia', 'senha' => 'abc123', 'link_definir' => '',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
    'cadastrado_por' => 'Clinica Sol', 'validade_dias' => 7,
));
assertTrue(strpos($eq['html'], 'abc123') !== false, 'equipe com senha mostra senha');
assertTrue(strpos($eq['html'], 'Clinica Sol') !== false, 'equipe mostra quem cadastrou');
assertTrue(strpos($eq['html'], 'Definir minha senha') === false, 'equipe com senha sem botao definir');

// --- e-mail equipe (convite)
$cv = utec_acesso_email_equipe(array(
    'nome' => 'Bia', 'login' => 'bia', 'senha' => '', 'link_definir' => 'https://x/acesso/senha/TOKEN',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
    'cadastrado_por' => 'Clinica Sol', 'validade_dias' => 7,
));
assertTrue(strpos($cv['html'], 'https://x/acesso/senha/TOKEN') !== false, 'convite tem link definir');
assertTrue(strpos($cv['html'], 'Definir minha senha') !== false, 'convite tem botao definir');
assertTrue(strpos($cv['html'], '7 dias') !== false, 'convite informa validade');
assertTrue(strpos($cv['html'], 'Senha:') === false, 'convite nao mostra linha de senha');

// --- e-mail redefinicao
$rd = utec_acesso_email_redefinicao(array(
    'nome' => 'Ana', 'login' => 'ana@clinica.com.br', 'link' => 'https://x/acesso/senha/TOK', 'validade_min' => 60,
));
assertTrue(strpos($rd['html'], 'https://x/acesso/senha/TOK') !== false, 'redefinicao tem link');
assertTrue(strpos($rd['html'], '1 hora') !== false, 'redefinicao informa validade');
assertTrue(strpos($rd['html'], 'Senha:') === false, 'redefinicao nunca mostra senha');
assertTrue(strpos($rd['html'], 'ana@clinica.com.br') !== false, 'redefinicao mostra login');

// --- e-mail interno
$in = utec_acesso_email_novo_cadastro_interno(array(
    'tenant_nome' => 'Clinica Sol', 'login' => 'ana@clinica.com.br', 'nome' => 'Ana',
    'origem' => 'trial', 'plano_nome' => 'Solo',
));
assertTrue(strpos($in['assunto'], 'Clinica Sol') !== false, 'interno assunto');
assertTrue(strpos($in['html'], 'Solo') !== false, 'interno plano');
assertTrue(stripos($in['html'], 'senha') === false, 'interno nunca fala de senha');

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_helper_test.php`
Expected: FAIL — `failed to open stream: No such file or directory` (helper não existe).

- [ ] **Step 3: Write the implementation**

Create `application/helpers/acesso_helper.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Funções puras de acesso: senha, token de redefinição e HTML dos e-mails.
 * Sem dependência de CI — testadas em tests/acesso_helper_test.php.
 */

if (!function_exists('utec_acesso_validar_senha')) {
    function utec_acesso_validar_senha($senha, $confirmacao)
    {
        $senha = (string)$senha;
        $confirmacao = (string)$confirmacao;
        if (strlen($senha) < 6) {
            return array('ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres.');
        }
        if ($senha !== $confirmacao) {
            return array('ok' => false, 'msg' => 'As senhas não coincidem. Tente novamente.');
        }
        return array('ok' => true, 'msg' => '');
    }
}

if (!function_exists('utec_acesso_gerar_token')) {
    function utec_acesso_gerar_token()
    {
        return bin2hex(random_bytes(32));
    }
}

if (!function_exists('utec_acesso_senha_aleatoria')) {
    function utec_acesso_senha_aleatoria($tamanho = 12)
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $max = strlen($chars) - 1;
        $senha = '';
        for ($i = 0; $i < (int)$tamanho; $i++) {
            $senha .= $chars[random_int(0, $max)];
        }
        return $senha;
    }
}

if (!function_exists('utec_acesso_email_valido')) {
    function utec_acesso_email_valido($email)
    {
        $email = trim((string)$email);
        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('utec_acesso_pode_reenviar')) {
    /**
     * $segundos_restantes = TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires) (MySQL) ou null.
     * Bloqueia só quando existe um token de redefinição criado há menos de $intervalo_min.
     * Tokens mais longos que a validade (convite de 7 dias) não bloqueiam.
     */
    function utec_acesso_pode_reenviar($segundos_restantes, $validade_min = 60, $intervalo_min = 2)
    {
        if ($segundos_restantes === null || $segundos_restantes === '') {
            return true;
        }
        $restantes = (int)$segundos_restantes;
        $validade = (int)$validade_min * 60;
        $limite = ((int)$validade_min - (int)$intervalo_min) * 60;
        if ($restantes > $validade) {
            return true;
        }
        return $restantes <= $limite;
    }
}

if (!function_exists('utec_acesso_h')) {
    function utec_acesso_h($valor)
    {
        return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('utec_acesso_botao')) {
    function utec_acesso_botao($url, $texto, $primario = true)
    {
        $estilo = $primario
            ? 'display:inline-block;padding:14px 28px;background:linear-gradient(90deg,#0f766e,#f97316);color:#fff;font-size:15px;font-weight:700;border-radius:999px;text-decoration:none;'
            : 'display:inline-block;padding:12px 24px;background:#fff;border:1px solid #d1d5db;color:#374151;font-size:14px;font-weight:600;border-radius:999px;text-decoration:none;';
        return '<p style="margin:20px 0;"><a href="'.utec_acesso_h($url).'" style="'.$estilo.'">'.utec_acesso_h($texto).'</a></p>';
    }
}

if (!function_exists('utec_acesso_email_layout')) {
    function utec_acesso_email_layout($titulo, $conteudo)
    {
        return '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f6f8fb;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fb;padding:40px 20px;"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <tr><td style="background:linear-gradient(90deg,#0f766e,#f97316);padding:32px 40px;">
    <p style="margin:0;font-size:13px;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.8);font-weight:700;">UTecnologia Saúde</p>
    <h1 style="margin:10px 0 0;color:#fff;font-size:24px;font-weight:800;">'.utec_acesso_h($titulo).'</h1>
  </td></tr>
  <tr><td style="padding:36px 40px;">'.$conteudo.'
    <p style="font-size:13px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:20px;margin-top:28px;">
      Dúvidas? Responda este e-mail ou fale no <a href="https://wa.me/5581983276882" style="color:#0f766e;">WhatsApp</a>.<br>
      UTecnologia Saúde — utecnologia.com.br
    </p>
  </td></tr>
</table></td></tr></table></body></html>';
    }
}

if (!function_exists('utec_acesso_caixa_credenciais')) {
    function utec_acesso_caixa_credenciais($login, $senha, $extra = '')
    {
        $html = '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1fdf9;border:1px solid #a7f3d0;border-radius:14px;padding:20px;margin:24px 0;"><tr><td>'
            .'<p style="margin:0 0 10px;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#0f766e;">Seus dados de acesso</p>'
            .'<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Login:</strong> '.utec_acesso_h($login).'</p>';
        if ((string)$senha !== '') {
            $html .= '<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Senha:</strong> <code style="background:#e0f2fe;padding:2px 8px;border-radius:6px;font-size:15px;">'.utec_acesso_h($senha).'</code></p>';
        }
        return $html.$extra.'</td></tr></table>';
    }
}

if (!function_exists('utec_acesso_email_boas_vindas')) {
    function utec_acesso_email_boas_vindas(array $d)
    {
        $trial = '';
        if (!empty($d['trial_fim'])) {
            $trial = '<p style="margin:10px 0 0;font-size:13px;color:#64748b;">Trial ativo até: <strong>'.utec_acesso_h($d['trial_fim']).'</strong></p>';
        }
        $conteudo = '<p style="font-size:16px;color:#334155;line-height:1.7;">Olá, <strong>'.utec_acesso_h($d['nome']).'</strong>!</p>'
            .'<p style="font-size:15px;color:#475569;line-height:1.7;">O ambiente <strong>'.utec_acesso_h($d['tenant_nome']).'</strong> foi criado com sucesso. Guarde este e-mail: ele tem os dados para você entrar novamente no sistema.</p>'
            .utec_acesso_caixa_credenciais($d['login'], $d['senha'], $trial)
            .utec_acesso_botao($d['link_login'], 'Entrar no sistema →')
            .'<p style="font-size:14px;color:#475569;line-height:1.7;">Esqueceu a senha? <a href="'.utec_acesso_h($d['link_esqueci']).'" style="color:#0f766e;font-weight:600;">Clique aqui para criar uma nova</a>.</p>';
        return array(
            'assunto' => 'Seu acesso UTecnologia Saúde está pronto — '.(string)$d['tenant_nome'],
            'html' => utec_acesso_email_layout('Seu acesso está pronto!', $conteudo),
        );
    }
}

if (!function_exists('utec_acesso_email_equipe')) {
    function utec_acesso_email_equipe(array $d)
    {
        $convite = (string)$d['senha'] === '';
        $intro = '<p style="font-size:16px;color:#334155;line-height:1.7;">Olá, <strong>'.utec_acesso_h($d['nome']).'</strong>!</p>'
            .'<p style="font-size:15px;color:#475569;line-height:1.7;"><strong>'.utec_acesso_h($d['cadastrado_por']).'</strong> criou seu acesso ao UTecnologia Saúde, sistema de agenda e prontuário da clínica.</p>';
        if ($convite) {
            $conteudo = $intro
                .utec_acesso_caixa_credenciais($d['login'], '')
                .'<p style="font-size:14px;color:#475569;line-height:1.7;">Para entrar, crie sua senha pelo botão abaixo. O link vale por '.(int)$d['validade_dias'].' dias.</p>'
                .utec_acesso_botao($d['link_definir'], 'Definir minha senha →')
                .'<p style="font-size:13px;color:#64748b;line-height:1.7;">Se o link expirar, use <a href="'.utec_acesso_h($d['link_esqueci']).'" style="color:#0f766e;">Esqueci minha senha</a> na tela de login.</p>';
        } else {
            $conteudo = $intro
                .utec_acesso_caixa_credenciais($d['login'], $d['senha'])
                .utec_acesso_botao($d['link_login'], 'Entrar no sistema →')
                .'<p style="font-size:14px;color:#475569;line-height:1.7;">Esqueceu a senha? <a href="'.utec_acesso_h($d['link_esqueci']).'" style="color:#0f766e;font-weight:600;">Clique aqui para criar uma nova</a>.</p>';
        }
        return array(
            'assunto' => 'Seu acesso ao UTecnologia Saúde foi criado',
            'html' => utec_acesso_email_layout('Seu acesso foi criado', $conteudo),
        );
    }
}

if (!function_exists('utec_acesso_email_redefinicao')) {
    function utec_acesso_email_redefinicao(array $d)
    {
        $min = (int)$d['validade_min'];
        $validade = $min === 60 ? '1 hora' : $min.' minutos';
        $conteudo = '<p style="font-size:16px;color:#334155;line-height:1.7;">Olá, <strong>'.utec_acesso_h($d['nome']).'</strong>!</p>'
            .'<p style="font-size:15px;color:#475569;line-height:1.7;">Recebemos um pedido para criar uma nova senha para o login <strong>'.utec_acesso_h($d['login']).'</strong>. Clique no botão abaixo — o link vale por '.$validade.' e só pode ser usado uma vez.</p>'
            .utec_acesso_botao($d['link'], 'Criar nova senha →')
            .'<p style="font-size:13px;color:#64748b;line-height:1.7;">Se você não pediu isso, ignore este e-mail: sua senha atual continua a mesma.</p>';
        return array(
            'assunto' => 'Criar nova senha — UTecnologia Saúde',
            'html' => utec_acesso_email_layout('Criar nova senha', $conteudo),
        );
    }
}

if (!function_exists('utec_acesso_email_novo_cadastro_interno')) {
    function utec_acesso_email_novo_cadastro_interno(array $d)
    {
        $conteudo = '<p style="font-size:15px;color:#475569;line-height:1.7;">Novo cadastro público ('.utec_acesso_h($d['origem']).').</p>'
            .'<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Clínica:</strong> '.utec_acesso_h($d['tenant_nome']).'</p>'
            .'<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Responsável:</strong> '.utec_acesso_h($d['nome']).'</p>'
            .'<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Login:</strong> '.utec_acesso_h($d['login']).'</p>'
            .'<p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Plano:</strong> '.utec_acesso_h($d['plano_nome']).'</p>';
        return array(
            'assunto' => 'Novo cadastro ('.(string)$d['origem'].') — '.(string)$d['tenant_nome'],
            'html' => utec_acesso_email_layout('Novo cadastro', $conteudo),
        );
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_helper_test.php && /c/PHP/PHP7.2/php.exe -l application/helpers/acesso_helper.php`
Expected: `OK` e `No syntax errors detected`.

- [ ] **Step 5: Commit**

```bash
git add application/helpers/acesso_helper.php tests/acesso_helper_test.php
git commit -m "feat(acesso): helper de senha, token e e-mails de acesso"
```

---

### Task 2: Library `Email_acesso`

**Files:**
- Create: `application/libraries/Email_acesso.php`
- Create: `tests/acesso_source_test.php` (começa aqui; tasks seguintes acrescentam asserções)

**Interfaces:**
- Consumes: todas as funções `utec_acesso_email_*` e `utec_acesso_email_valido` da Task 1.
- Produces (carregar com `$this->load->library('email_acesso')`, usar `$this->email_acesso->...`):
  - `boas_vindas(array $d): bool` — `$d`: `nome`, `login`, `senha`, `tenant_nome`, `trial_fim`, `plano_nome`, `origem`. Envia ao cliente + e-mail interno.
  - `acesso_equipe(array $d): bool` — `$d`: `email`, `nome`, `login`, `senha`, `token` ('' quando tem senha), `cadastrado_por`.
  - `redefinicao(array $d): bool` — `$d`: `email`, `nome`, `login`, `token`.

- [ ] **Step 1: Write the failing test**

Create `tests/acesso_source_test.php`:

```php
<?php
// Testes estáticos (leitura de código-fonte), mesmo padrão de tests/whatsapp_*_source_test.php.
$raiz = __DIR__ . '/..';

function src($caminho) {
    global $raiz;
    $arquivo = $raiz . '/' . $caminho;
    if (!file_exists($arquivo)) {
        fwrite(STDERR, 'Arquivo ausente: ' . $caminho . PHP_EOL);
        exit(1);
    }
    return file_get_contents($arquivo);
}
function assertSrc($cond, $label) {
    if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); }
}
// Corpo de uma função: do início da assinatura até a próxima declaração de função
// (cobre os estilos do projeto: "\tpublic function", "\tfunction", "\tprivate function", "function" na coluna 0).
function corpoFuncao($codigo, $assinatura) {
    $ini = strpos($codigo, $assinatura);
    if ($ini === false) { return ''; }
    $fim = strlen($codigo);
    foreach (array("\n\tpublic function ", "\n\tprivate function ", "\n\tprotected function ", "\n\tfunction ", "\nfunction ") as $marca) {
        $pos = strpos($codigo, $marca, $ini + 1);
        if ($pos !== false && $pos < $fim) { $fim = $pos; }
    }
    return substr($codigo, $ini, $fim - $ini);
}

// --- Task 2: library
$lib = src('application/libraries/Email_acesso.php');
assertSrc(strpos($lib, 'class Email_acesso') !== false, 'Library Email_acesso deve existir.');
foreach (array('function boas_vindas(', 'function acesso_equipe(', 'function redefinicao(') as $m) {
    assertSrc(strpos($lib, $m) !== false, 'Email_acesso deve ter ' . $m);
}
assertSrc(strpos($lib, 'bcc(') === false, 'E-mail com senha nao pode ir com BCC (spec secao 5).');
assertSrc(strpos($lib, "->clear(") !== false, 'Email_acesso deve limpar o estado entre envios.');
assertSrc(strpos($lib, "'acesso/senha/'") !== false, 'Links de token devem usar acesso/senha/{token}.');

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php`
Expected: FAIL — `Arquivo ausente: application/libraries/Email_acesso.php`.

- [ ] **Step 3: Write the implementation**

Create `application/libraries/Email_acesso.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * E-mails de acesso: boas-vindas (com a senha escolhida), acesso da equipe
 * (com senha ou convite para definir) e redefinição de senha (só link).
 * Nunca lança exceção nem bloqueia o fluxo — falha vai para o log sem o corpo
 * do e-mail (o corpo pode conter senha).
 */
class Email_acesso {

    const REMETENTE = 'suporte@utecnologia.com.br';
    const REMETENTE_NOME = 'UTecnologia Saúde';
    const INTERNO = 'igor_marlus@yahoo.com.br';
    const VALIDADE_CONVITE_DIAS = 7;
    const VALIDADE_REDEFINICAO_MIN = 60;

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('acesso');
    }

    public function boas_vindas(array $d)
    {
        $ok = $this->enviar('boas_vindas', $d['login'], utec_acesso_email_boas_vindas(array(
            'nome' => $d['nome'],
            'login' => $d['login'],
            'senha' => $d['senha'],
            'tenant_nome' => $d['tenant_nome'],
            'trial_fim' => isset($d['trial_fim']) ? $d['trial_fim'] : '',
            'link_login' => base_url().'admin',
            'link_esqueci' => base_url().'acesso/esqueci',
        )));
        $this->enviar('novo_cadastro_interno', self::INTERNO, utec_acesso_email_novo_cadastro_interno(array(
            'tenant_nome' => $d['tenant_nome'],
            'login' => $d['login'],
            'nome' => $d['nome'],
            'origem' => isset($d['origem']) ? $d['origem'] : '',
            'plano_nome' => isset($d['plano_nome']) ? $d['plano_nome'] : '',
        )));
        return $ok;
    }

    public function acesso_equipe(array $d)
    {
        $token = isset($d['token']) ? (string)$d['token'] : '';
        return $this->enviar('acesso_equipe', $d['email'], utec_acesso_email_equipe(array(
            'nome' => $d['nome'],
            'login' => $d['login'],
            'senha' => $token !== '' ? '' : (string)$d['senha'],
            'link_definir' => $token !== '' ? base_url().'acesso/senha/'.$token : '',
            'link_login' => base_url().'admin',
            'link_esqueci' => base_url().'acesso/esqueci',
            'cadastrado_por' => $d['cadastrado_por'],
            'validade_dias' => self::VALIDADE_CONVITE_DIAS,
        )));
    }

    public function redefinicao(array $d)
    {
        return $this->enviar('redefinicao', $d['email'], utec_acesso_email_redefinicao(array(
            'nome' => $d['nome'],
            'login' => $d['login'],
            'link' => base_url().'acesso/senha/'.$d['token'],
            'validade_min' => self::VALIDADE_REDEFINICAO_MIN,
        )));
    }

    protected function enviar($tipo, $para, array $mensagem)
    {
        $para = trim((string)$para);
        if (!utec_acesso_email_valido($para)) {
            log_message('error', 'email_acesso '.$tipo.': destinatario invalido');
            return false;
        }
        try {
            $this->CI->config->load('email');
            $this->CI->load->library('email');
            $this->CI->email->clear(true);
            $this->CI->email->initialize($this->CI->config->config);
            $this->CI->email->from(self::REMETENTE, self::REMETENTE_NOME);
            $this->CI->email->to($para);
            $this->CI->email->subject($mensagem['assunto']);
            $this->CI->email->message($mensagem['html']);
            if ($this->CI->email->send(false)) {
                return true;
            }
            log_message('error', 'email_acesso '.$tipo.' falhou para '.$para.': '.$this->CI->email->print_debugger(array('headers')));
        } catch (Exception $e) {
            log_message('error', 'email_acesso '.$tipo.' excecao para '.$para.': '.$e->getMessage());
        }
        return false;
    }
}
```

Nota: `send(false)` não limpa o estado automaticamente, para que `print_debugger()` ainda tenha o log em caso de falha; o `clear(true)` do início do próximo envio limpa.

- [ ] **Step 4: Run test to verify it passes**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php && /c/PHP/PHP7.2/php.exe -l application/libraries/Email_acesso.php`
Expected: `OK` e `No syntax errors detected`.

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Email_acesso.php tests/acesso_source_test.php
git commit -m "feat(acesso): library Email_acesso para boas-vindas, equipe e redefinicao"
```

---

### Task 3: Fluxo "Esqueci minha senha" + login com mensagem de erro

**Files:**
- Modify: `application/config/routes.php:68-70`
- Modify: `application/controllers/Home.php:479-561` (`definir_senha`, `salvar_senha`) + novos métodos
- Create: `application/views/public/esqueci-senha.php`
- Modify: `application/views/public/definir-senha.php:79`
- Modify: `application/controllers/Admin.php:51-53`
- Modify: `application/models/adm/Usuarios_model.php:11-34`
- Modify: `application/views/adm/login.php` (CSS + form 177-196)
- Modify: `application/views/index-front.php:677` e `:1235`
- Test: `tests/acesso_source_test.php`

**Interfaces:**
- Consumes: `Email_acesso::redefinicao(['email','nome','login','token'])`; `utec_acesso_gerar_token()`, `utec_acesso_pode_reenviar()`, `utec_acesso_email_valido()`, `utec_acesso_validar_senha()`.
- Produces: rotas `acesso/esqueci` (GET) e `acesso/esqueci/enviar` (POST); flash keys `login_error` e `esqueci_ok`/`esqueci_error`; `adm/login.php` exibe `login_error`, `operational_trial_error`, `operational_trial_ok`.

- [ ] **Step 1: Write the failing test**

Em `tests/acesso_source_test.php`, **antes** de `echo "OK\n";`, acrescente:

```php
// --- Task 3: esqueci minha senha + login
$rotas = src('application/config/routes.php');
assertSrc(strpos($rotas, "\$route['acesso/esqueci'] = 'home/esqueci_senha';") !== false, 'Rota acesso/esqueci ausente.');
assertSrc(strpos($rotas, "\$route['acesso/esqueci/enviar'] = 'home/enviar_redefinicao';") !== false, 'Rota acesso/esqueci/enviar ausente.');

$home = src('application/controllers/Home.php');
$enviar = corpoFuncao($home, 'public function enviar_redefinicao()');
assertSrc($enviar !== '', 'Home::enviar_redefinicao() ausente.');
assertSrc(strpos($enviar, 'DATE_ADD(NOW(), INTERVAL 60 MINUTE)') !== false, 'Expiracao da redefinicao deve ser calculada no MySQL (60 min).');
assertSrc(strpos($enviar, 'TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires)') !== false, 'Throttle deve usar o relogio do MySQL.');
assertSrc(strpos($enviar, 'utec_acesso_pode_reenviar(') !== false, 'Throttle ausente.');
assertSrc(substr_count($enviar, "set_flashdata('esqueci_ok'") === 1 && strpos($enviar, 'esqueci_msg_generica') !== false, 'Resposta do esqueci deve ser unica e generica.');

$salvar = corpoFuncao($home, 'public function salvar_senha()');
assertSrc(strpos($salvar, "if(!\$this->session->userdata('id'))") === false, 'salvar_senha deve sempre gravar a sessao do usuario do token.');
assertSrc(strpos($salvar, "'usr'   => true") !== false, 'Sessao apos salvar senha deve usar usr => true.');
assertSrc(strpos($salvar, 'utec_acesso_validar_senha(') !== false, 'salvar_senha deve validar via helper.');
assertSrc(strpos(corpoFuncao($home, 'public function definir_senha('), "redirect('experimentar')") === false, 'Token invalido deve voltar para o login, nao para experimentar.');

$admin = src('application/controllers/Admin.php');
assertSrc(strpos($admin, "adm/esqueceu-senha") === false, 'Admin::esqueceuSenha nao pode carregar view inexistente.');

$um = src('application/models/adm/Usuarios_model.php');
assertSrc(strpos($um, "set_flashdata('login_error'") !== false, 'Login invalido deve mostrar mensagem.');

$login = src('application/views/adm/login.php');
assertSrc(strpos($login, 'acesso/esqueci') !== false, 'Tela /admin deve linkar Esqueci minha senha.');
assertSrc(strpos($login, "flashdata('login_error')") !== false, 'Tela /admin deve exibir login_error.');

$front = src('application/views/index-front.php');
assertSrc(strpos($front, '<a href="#">Esqueci minha senha</a>') === false, 'Links Esqueci da landing nao podem apontar para #.');
assertSrc(substr_count($front, 'acesso/esqueci') >= 2, 'Landing deve linkar acesso/esqueci no modal e na secao de login.');

$view = src('application/views/public/esqueci-senha.php');
assertSrc(strpos($view, 'acesso/esqueci/enviar') !== false && strpos($view, 'name="identificacao"') !== false, 'View esqueci-senha incompleta.');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php`
Expected: FAIL — `Rota acesso/esqueci ausente.`

- [ ] **Step 3: Routes**

Em `application/config/routes.php`, logo após a linha `$route['acesso/salvar']       = 'home/salvar_senha';`, adicionar exatamente (um espaço antes do `=` — o teste busca a string literal):

```php
$route['acesso/esqueci'] = 'home/esqueci_senha';
$route['acesso/esqueci/enviar'] = 'home/enviar_redefinicao';
```

- [ ] **Step 4: Home — novos métodos e ajustes em definir/salvar**

Em `application/controllers/Home.php`, substituir o bloco inteiro de `public function definir_senha($token = '')` até o fim de `salvar_senha()` (linhas 481-561) por:

```php
	public function definir_senha($token = '')
	{
		$token = preg_replace('/[^a-f0-9]/i', '', (string)$token);
		if(strlen($token) !== 64){
			$this->session->set_flashdata('operational_trial_error', 'Link de definição de senha inválido ou expirado. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}
		$user = $this->db->query(
			"SELECT id, nome, email FROM usuarios
			 WHERE senha_token = ".$this->db->escape($token)."
			 AND senha_token_expires > NOW()
			 LIMIT 1"
		)->row();
		if(!$user){
			$this->session->set_flashdata('operational_trial_error', 'Este link já foi usado ou expirou. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}
		$dados['token'] = $token;
		$dados['nome']  = $user->nome;
		$dados['email'] = $user->email;
		$dados['flash_error'] = $this->session->flashdata('definir_senha_error');
		$this->load->view('public/definir-senha', $dados);
	}

	public function salvar_senha()
	{
		$this->load->helper('acesso');
		$token  = preg_replace('/[^a-f0-9]/i', '', (string)$this->input->post('token'));
		$nova   = (string)$this->input->post('nova_senha');
		$conf   = (string)$this->input->post('confirmar_senha');

		if(strlen($token) !== 64){
			$this->session->set_flashdata('operational_trial_error', 'Link inválido. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}
		$validacao = utec_acesso_validar_senha($nova, $conf);
		if(!$validacao['ok']){
			$this->session->set_flashdata('definir_senha_error', $validacao['msg']);
			redirect('acesso/senha/'.$token);
			return;
		}

		$user = $this->db->query(
			"SELECT id, nome, nivel, login FROM usuarios
			 WHERE senha_token = ".$this->db->escape($token)."
			 AND senha_token_expires > NOW()
			 LIMIT 1"
		)->row();
		if(!$user){
			$this->session->set_flashdata('operational_trial_error', 'Link expirado. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}

		$this->db->where('id', $user->id);
		$this->db->update('usuarios', [
			'senha'               => password_hash($nova, PASSWORD_DEFAULT),
			'senha_token'         => null,
			'senha_token_expires' => null,
		]);

		// Sempre entra como o dono do token (outra sessão aberta no navegador é substituída)
		$this->session->set_userdata([
			'usr'   => true,
			'id'    => $user->id,
			'nome'  => $user->nome,
			'nivel' => $user->nivel,
			'login' => $user->login,
		]);

		$this->session->set_flashdata('operational_trial_ok', 'Senha definida com sucesso! Bem-vindo(a) ao sistema.');
		$nivel = (int)$user->nivel;
		redirect(($nivel >= 2 && $nivel <= 4) ? 'adm/atendimento' : 'adm/usuarios');
	}

	// ── ESQUECI MINHA SENHA ─────────────────────────────────────────────

	public function esqueci_senha()
	{
		$dados['flash_ok']    = $this->session->flashdata('esqueci_ok');
		$dados['flash_error'] = $this->session->flashdata('esqueci_error');
		$this->load->view('public/esqueci-senha', $dados);
	}

	public function enviar_redefinicao()
	{
		$this->load->helper('acesso');
		$esqueci_msg_generica = 'Se houver uma conta com esses dados, enviamos um link para o e-mail cadastrado. O link vale por 1 hora.';
		$identificacao = trim((string)$this->input->post('identificacao'));

		if($identificacao === ''){
			$this->session->set_flashdata('esqueci_error', 'Informe seu e-mail ou usuário.');
			redirect('acesso/esqueci');
			return;
		}

		if($this->db->field_exists('senha_token', 'usuarios') && $this->db->field_exists('senha_token_expires', 'usuarios')){
			$usuarios = $this->db->query(
				"SELECT id, nome, login, email,
				        TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires) AS segundos_restantes
				   FROM usuarios
				  WHERE nivel BETWEEN 1 AND 4
				    AND (login = ? OR LOWER(email) = ?)
				  LIMIT 5",
				[$identificacao, strtolower($identificacao)]
			)->result();

			if(count($usuarios)){
				$this->load->library('email_acesso');
			}
			foreach($usuarios as $u){
				if(!utec_acesso_email_valido($u->email)){ continue; }
				if(!utec_acesso_pode_reenviar($u->segundos_restantes, 60, 2)){ continue; }
				$token = utec_acesso_gerar_token();
				$this->db->set('senha_token', $token);
				$this->db->set('senha_token_expires', 'DATE_ADD(NOW(), INTERVAL 60 MINUTE)', false);
				$this->db->where('id', (int)$u->id);
				$this->db->update('usuarios');
				$this->email_acesso->redefinicao([
					'email' => trim((string)$u->email),
					'nome'  => $u->nome,
					'login' => $u->login,
					'token' => $token,
				]);
			}
		}else{
			log_message('error', 'esqueci_senha: colunas senha_token/senha_token_expires ausentes em usuarios');
		}

		$this->session->set_flashdata('esqueci_ok', $esqueci_msg_generica);
		redirect('acesso/esqueci');
	}
```

- [ ] **Step 5: View `public/esqueci-senha.php`**

Create `application/views/public/esqueci-senha.php` (mesmo visual de `definir-senha.php`):

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Esqueci minha senha — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <style>
        :root {
            --ink:#172033; --muted:#667085; --line:#d0d8e4;
            --primary:#0f766e; --accent:#f97316;
            --ok-bg:#ecfdf3; --ok-text:#166534;
            --error-bg:#fef2f2; --error-text:#991b1b;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:system-ui,sans-serif; color:var(--ink);
            background:radial-gradient(circle at top left,rgba(15,118,110,.12),transparent 30%),
                        linear-gradient(180deg,#f8fafc,#eef4f7);
            min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
        }
        .card {
            background:#fff; border:1px solid var(--line); border-radius:24px;
            padding:40px 36px; width:100%; max-width:440px;
            box-shadow:0 20px 56px rgba(23,32,51,.1);
        }
        .logo { margin-bottom:28px; }
        h1 { font-size:26px; font-weight:800; margin-bottom:8px; }
        .sub { font-size:15px; color:var(--muted); line-height:1.6; margin-bottom:28px; }
        .alert { border-radius:12px; padding:12px 16px; font-size:14px; line-height:1.5; margin-bottom:20px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok-text); border:1px solid #bbf7d0; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }
        .field { display:grid; gap:6px; margin-bottom:16px; }
        label { font-size:12px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--muted); }
        input[type=text] {
            width:100%; border:1px solid #c9d3df; border-radius:12px;
            padding:12px 14px; font:inherit; color:var(--ink); background:#fff;
        }
        input[type=text]:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(15,118,110,.12); }
        .btn {
            width:100%; border:0; border-radius:999px;
            background:linear-gradient(90deg,var(--primary),var(--accent));
            color:#fff; padding:14px; font-size:15px; font-weight:700;
            cursor:pointer; box-shadow:0 12px 28px rgba(15,118,110,.2); margin-top:8px;
        }
        .note { font-size:13px; color:var(--muted); text-align:center; margin-top:20px; line-height:1.6; }
        .note a { color:var(--primary); font-weight:600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></div>

        <h1>Esqueci minha senha</h1>
        <p class="sub">Informe o e-mail ou o usuário que você usa para entrar. Enviaremos um link para você criar uma nova senha.</p>

        <?php if($flash_ok): ?>
        <div class="alert alert-ok"><?=htmlspecialchars((string)$flash_ok)?></div>
        <?php endif; ?>
        <?php if($flash_error): ?>
        <div class="alert alert-error"><?=htmlspecialchars((string)$flash_error)?></div>
        <?php endif; ?>

        <form method="post" action="<?=base_url()?>acesso/esqueci/enviar">
            <div class="field">
                <label for="identificacao">E-mail ou usuário</label>
                <input type="text" id="identificacao" name="identificacao" placeholder="seuemail@clinica.com.br" required autocomplete="username">
            </div>
            <button type="submit" class="btn">Enviar link →</button>
        </form>

        <p class="note">Não chegou? Confira a caixa de spam ou fale com a gente no <a href="https://wa.me/5581983276882">WhatsApp</a>.<br><a href="<?=base_url()?>admin">Voltar para o login</a></p>
    </div>
</body>
</html>
```

- [ ] **Step 6: `definir-senha.php` — texto do rodapé**

Em `application/views/public/definir-senha.php:79`, substituir:

```php
        <p class="note">Prefere entrar com a senha provisória? <a href="<?=base_url()?>admin">Ir para o login</a></p>
```

por:

```php
        <p class="note"><a href="<?=base_url()?>admin">Voltar para o login</a></p>
```

- [ ] **Step 7: `Admin::esqueceuSenha()`**

Em `application/controllers/Admin.php`, substituir:

```php
	function esqueceuSenha(){
		$this->load->view('adm/esqueceu-senha');
	}
```

por:

```php
	function esqueceuSenha(){
		redirect('acesso/esqueci');
	}
```

- [ ] **Step 8: `Usuarios_model::logar()` — trim, vazio, mensagem**

Em `application/models/adm/Usuarios_model.php`, substituir as linhas 13-34:

```php
		$login       = $this->input->post('login');
		$senha_input = $this->input->post('senha');

		$this->db->where('login', $login);
		$qr_login = $this->db->get('usuarios');

		if($qr_login->num_rows() > 0){
			$dd_user   = $qr_login->row();
			$senha_ok  = false;

			if(password_verify($senha_input, $dd_user->senha)){
				$senha_ok = true;
			} elseif($dd_user->senha === $senha_input) {
				// migração: senha ainda em texto puro → rehasha silenciosamente
				$this->db->where('id', $dd_user->id);
				$this->db->update('usuarios', ['senha' => password_hash($senha_input, PASSWORD_DEFAULT)]);
				$senha_ok = true;
			}

			if(!$senha_ok){
				redirect('admin');
			}
```

por:

```php
		$login       = trim((string)$this->input->post('login'));
		$senha_input = (string)$this->input->post('senha');
		$msg_erro    = 'Usuário ou senha inválidos.';

		if($login === '' || $senha_input === ''){
			$this->session->set_flashdata('login_error', $msg_erro);
			redirect('admin');
			return;
		}

		$this->db->where('login', $login);
		$qr_login = $this->db->get('usuarios');

		if($qr_login->num_rows() > 0){
			$dd_user   = $qr_login->row();
			$senha_ok  = false;
			$senha_db  = (string)$dd_user->senha;

			if($senha_db !== '' && password_verify($senha_input, $senha_db)){
				$senha_ok = true;
			} elseif($senha_db !== '' && $senha_db === $senha_input) {
				// migração: senha ainda em texto puro → rehasha silenciosamente
				$this->db->where('id', $dd_user->id);
				$this->db->update('usuarios', ['senha' => password_hash($senha_input, PASSWORD_DEFAULT)]);
				$senha_ok = true;
			}

			if(!$senha_ok){
				$this->session->set_flashdata('login_error', $msg_erro);
				redirect('admin');
				return;
			}
```

E no `}else{` final do mesmo método (linhas 51-53), substituir:

```php
		}else{
			redirect('admin');
		}
```

por:

```php
		}else{
			$this->session->set_flashdata('login_error', $msg_erro);
			redirect('admin');
		}
```

- [ ] **Step 9: `adm/login.php` — mensagens e link**

Em `application/views/adm/login.php`, antes de `@media (max-width: 860px) {` (linha 152), adicionar CSS:

```css
.login-alert {
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 16px;
}
.login-alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.login-alert-ok { background: #ecfdf3; color: #166534; border: 1px solid #bbf7d0; }
.login-forgot {
    display: block;
    text-align: right;
    margin: -4px 0 16px;
    font-size: 13px;
    color: #0ea5e9;
    font-weight: 600;
    text-decoration: none;
}
```

Substituir o bloco das linhas 179-189:

```php
        <p>Entre com suas credenciais para continuar na UTecnologia Saude.</p>
        <form action="<?=base_url()?>admin/logar" method="post">
          <div class="form-group">
            <label for="login">Usuario</label>
            <input id="login" type="text" name="login" placeholder="Digite seu usuario">
          </div>
          <div class="form-group">
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha">
          </div>
          <button type="submit" class="btn-login">Entrar</button>
```

por:

```php
        <p>Entre com suas credenciais para continuar na UTecnologia Saude.</p>
        <?php
          $login_error = $this->session->flashdata('login_error');
          $token_error = $this->session->flashdata('operational_trial_error');
          $flash_ok    = $this->session->flashdata('operational_trial_ok');
        ?>
        <?php if($login_error || $token_error){ ?>
          <div class="login-alert login-alert-error" role="alert">
            <?=htmlspecialchars((string)($login_error ?: $token_error))?>
            <?php if($login_error){ ?> <a href="<?=base_url()?>acesso/esqueci">Esqueci minha senha</a><?php } ?>
          </div>
        <?php } elseif($flash_ok){ ?>
          <div class="login-alert login-alert-ok"><?=htmlspecialchars((string)$flash_ok)?></div>
        <?php } ?>
        <form action="<?=base_url()?>admin/logar" method="post">
          <div class="form-group">
            <label for="login">E-mail ou usuario</label>
            <input id="login" type="text" name="login" placeholder="Digite seu e-mail ou usuario" required autocomplete="username">
          </div>
          <div class="form-group">
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha" required autocomplete="current-password">
          </div>
          <a class="login-forgot" href="<?=base_url()?>acesso/esqueci">Esqueci minha senha</a>
          <button type="submit" class="btn-login">Entrar</button>
```

- [ ] **Step 10: `index-front.php` — links**

Em `application/views/index-front.php`, nas duas ocorrências (linhas 677 e 1235), substituir:

```php
<div class="forgot"><a href="#">Esqueci minha senha</a></div>
```

por:

```php
<div class="forgot"><a href="<?=base_url()?>acesso/esqueci">Esqueci minha senha</a></div>
```

(Edite cada ocorrência separadamente, mantendo a indentação original de cada uma.)

- [ ] **Step 11: Run tests + lint**

Run:
```bash
/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php && \
for f in application/config/routes.php application/controllers/Home.php application/controllers/Admin.php application/models/adm/Usuarios_model.php application/views/public/esqueci-senha.php application/views/public/definir-senha.php application/views/adm/login.php application/views/index-front.php; do /c/PHP/PHP7.2/php.exe -l $f; done
```
Expected: `OK` e `No syntax errors detected` em todos.

- [ ] **Step 12: Commit**

```bash
git add application/config/routes.php application/controllers/Home.php application/controllers/Admin.php application/models/adm/Usuarios_model.php application/views/public/esqueci-senha.php application/views/public/definir-senha.php application/views/adm/login.php application/views/index-front.php tests/acesso_source_test.php
git commit -m "feat(acesso): esqueci minha senha por link e mensagem de login invalido"
```

---

### Task 4: Senha escolhida no trial e na assinatura + e-mail de boas-vindas

**Files:**
- Modify: `application/models/adm/Saas_model.php` — `create_public_tenant_signup()` (~559-724) e `create_operational_trial_signup()` (~726-916)
- Modify: `application/controllers/Home.php` — `iniciar_experiencia()` (91-175), `contratar()` (192-227), remover `_enviar_email_boas_vindas()` (724-790)
- Modify: `application/views/public/experimentar.php:452-460`
- Modify: `application/views/public/assinar.php:340-343`
- Modify: `application/views/public/experimentar-sucesso.php:92-93, 113`
- Test: `tests/acesso_source_test.php`

**Interfaces:**
- Consumes: `utec_acesso_validar_senha()`; `Email_acesso::boas_vindas(['nome','login','senha','tenant_nome','trial_fim','plano_nome','origem'])`.
- Produces: os dois métodos do model aceitam `$data['senha']` e `$data['senha_confirmacao']` e retornam, além do atual, `senha` (texto, só em memória), `nome_responsavel`, `plano_nome`, `trial_ends_at`. O trial **não** retorna mais `senha_gerada` nem `token`.

- [ ] **Step 1: Write the failing test**

Em `tests/acesso_source_test.php`, antes de `echo "OK\n";`, acrescente:

```php
// --- Task 4: trial e assinatura
$exp = src('application/views/public/experimentar.php');
assertSrc(strpos($exp, '<input type="hidden" name="senha"') === false, 'experimentar.php nao pode mais mandar senha oculta.');
assertSrc(strpos($exp, 'name="senha"') !== false && strpos($exp, 'name="senha_confirmacao"') !== false, 'experimentar.php precisa de senha + confirmacao.');

$ass = src('application/views/public/assinar.php');
assertSrc(strpos($ass, 'name="senha_confirmacao"') !== false, 'assinar.php precisa de confirmacao de senha.');

$saas = src('application/models/adm/Saas_model.php');
$trial = corpoFuncao($saas, 'function create_operational_trial_signup(');
assertSrc(strpos($trial, 'random_int(') === false, 'Trial nao pode mais gerar senha aleatoria.');
assertSrc(strpos($trial, 'utec_acesso_validar_senha(') !== false, 'Trial deve validar a senha do usuario.');
assertSrc(strpos($trial, "'senha_gerada'") === false, 'Trial nao retorna mais senha_gerada.');
$pub = corpoFuncao($saas, 'function create_public_tenant_signup(');
assertSrc(strpos($pub, 'utec_acesso_validar_senha(') !== false, 'Assinatura deve validar senha + confirmacao.');
assertSrc(strpos($pub, "'plano_nome'") !== false, 'Assinatura deve retornar plano_nome para o e-mail.');

$home = src('application/controllers/Home.php');
assertSrc(strpos($home, '_enviar_email_boas_vindas') === false, 'E-mail de boas-vindas deve usar a library Email_acesso.');
assertSrc(strpos(corpoFuncao($home, 'public function iniciar_experiencia()'), 'email_acesso->boas_vindas(') !== false, 'Trial deve enviar boas-vindas.');
assertSrc(strpos(corpoFuncao($home, 'public function contratar()'), 'email_acesso->boas_vindas(') !== false, 'Assinatura deve enviar boas-vindas.');
assertSrc(strpos(corpoFuncao($home, 'public function iniciar_experiencia()'), "'senha_confirmacao'") !== false, 'Trial deve repassar senha_confirmacao ao model.');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php`
Expected: FAIL — `experimentar.php nao pode mais mandar senha oculta.`

- [ ] **Step 3: View `experimentar.php`**

Substituir as linhas 452-460:

```php
                        <input type="hidden" name="senha" value="">
                        <input type="hidden" name="documento" value="">
                        <input type="hidden" name="observacoes" value="">
                    </div>

                    <div class="submit-row">
                        <button class="btn-submit" type="submit">Começar 30 dias grátis →</button>
                    </div>
                    <p style="font-size:12px;color:#667085;margin-top:12px;text-align:center;">Sem cartão de crédito · Acesso imediato · Você receberá as credenciais por e-mail</p>
```

por:

```php
                        <div class="field">
                            <label for="trial-senha">Crie sua senha</label>
                            <input type="password" id="trial-senha" name="senha" required minlength="6" autocomplete="new-password" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="field">
                            <label for="trial-senha-conf">Confirme a senha</label>
                            <input type="password" id="trial-senha-conf" name="senha_confirmacao" required minlength="6" autocomplete="new-password" placeholder="Repita a senha">
                        </div>
                        <input type="hidden" name="documento" value="">
                        <input type="hidden" name="observacoes" value="">
                    </div>

                    <div class="submit-row">
                        <button class="btn-submit" type="submit">Começar 30 dias grátis →</button>
                    </div>
                    <p style="font-size:12px;color:#667085;margin-top:12px;text-align:center;">Sem cartão de crédito · Acesso imediato · Você entra com seu e-mail e a senha que criou aqui</p>
```

E, no `<script>` existente da página (ou antes de `</body>` se não houver), adicionar a checagem de confirmação no cliente:

```html
<script>
(function(){
    var s = document.getElementById('trial-senha');
    var c = document.getElementById('trial-senha-conf');
    if(!s || !c) return;
    function checar(){ c.setCustomValidity(c.value !== s.value ? 'As senhas não coincidem.' : ''); }
    s.addEventListener('input', checar);
    c.addEventListener('input', checar);
})();
</script>
```

- [ ] **Step 4: View `assinar.php`**

Substituir as linhas 340-343:

```php
                        <div class="field">
                            <label>Senha inicial</label>
                            <input type="password" name="senha" required minlength="6">
                        </div>
```

por:

```php
                        <div class="field">
                            <label for="ass-senha">Crie sua senha</label>
                            <input type="password" id="ass-senha" name="senha" required minlength="6" autocomplete="new-password" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="field">
                            <label for="ass-senha-conf">Confirme a senha</label>
                            <input type="password" id="ass-senha-conf" name="senha_confirmacao" required minlength="6" autocomplete="new-password" placeholder="Repita a senha">
                        </div>
```

E antes de `</body>`:

```html
<script>
(function(){
    var s = document.getElementById('ass-senha');
    var c = document.getElementById('ass-senha-conf');
    if(!s || !c) return;
    function checar(){ c.setCustomValidity(c.value !== s.value ? 'As senhas não coincidem.' : ''); }
    s.addEventListener('input', checar);
    c.addEventListener('input', checar);
})();
</script>
```

- [ ] **Step 5: `experimentar-sucesso.php` — textos**

Linhas 92-93: trocar `Também enviamos suas credenciais e um link para definir sua\n senha personalizada para o e-mail cadastrado.` por `Também enviamos seu login e a senha que você criou para o e-mail cadastrado — guarde-o para entrar da próxima vez.`

Linha 113: trocar `Credenciais e link de acesso enviados para este e-mail.` por `Login e senha enviados para este e-mail.`

- [ ] **Step 6: Model — `create_public_tenant_signup()`**

Em `application/models/adm/Saas_model.php`, substituir (linhas ~570-581):

```php
		$senha = (string)$data['senha'];
		$observacoes = trim((string)$data['observacoes']);

		if($nome_responsavel === '' || $tenant_nome === '' || $email === '' || $plano_id <= 0 || $senha === ''){
			return ['ok' => false, 'msg' => 'Preencha nome do responsavel, nome da clinica, e-mail, senha e plano.'];
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return ['ok' => false, 'msg' => 'Informe um e-mail valido para continuar.'];
		}
		if(strlen($senha) < 6){
			return ['ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres.'];
		}
```

por:

```php
		$senha = isset($data['senha']) ? (string)$data['senha'] : '';
		$senha_confirmacao = isset($data['senha_confirmacao']) ? (string)$data['senha_confirmacao'] : '';
		$observacoes = trim((string)$data['observacoes']);

		if($nome_responsavel === '' || $tenant_nome === '' || $email === '' || $plano_id <= 0 || $senha === ''){
			return ['ok' => false, 'msg' => 'Preencha nome do responsavel, nome da clinica, e-mail, senha e plano.'];
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return ['ok' => false, 'msg' => 'Informe um e-mail valido para continuar.'];
		}
		$this->load->helper('acesso');
		$validacao_senha = utec_acesso_validar_senha($senha, $senha_confirmacao);
		if(!$validacao_senha['ok']){
			return ['ok' => false, 'msg' => $validacao_senha['msg']];
		}
```

E no `return` de sucesso do mesmo método (linhas ~716-723), substituir:

```php
			'login' => $email,
			'tenant_nome' => $tenant_nome,
		];
	}

	function create_operational_trial_signup($data){
```

por:

```php
			'login' => $email,
			'tenant_nome' => $tenant_nome,
			'nome_responsavel' => $nome_responsavel,
			'senha' => $senha,
			'plano_nome' => isset($plano->modelo) ? (string)$plano->modelo : '',
			'trial_ends_at' => $trial_ends_at,
		];
	}

	function create_operational_trial_signup($data){
```

- [ ] **Step 7: Model — `create_operational_trial_signup()`**

Após `$especialidade_id = ...` (linha ~739) adicionar:

```php
		$senha = isset($data['senha']) ? (string)$data['senha'] : '';
		$senha_confirmacao = isset($data['senha_confirmacao']) ? (string)$data['senha_confirmacao'] : '';
```

Substituir (linhas ~751-758):

```php
		// Senha gerada no servidor — não exigimos que o usuário defina no cadastro
		$chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$senha = '';
		for($i = 0; $i < 10; $i++) $senha .= $chars[random_int(0, strlen($chars)-1)];

		// Token para definição de senha posterior (expira em 7 dias)
		$token = bin2hex(random_bytes(32));
		$token_expires = date('Y-m-d H:i:s', strtotime('+7 days'));
```

por:

```php
		// Senha escolhida pelo próprio usuário no formulário
		$this->load->helper('acesso');
		$validacao_senha = utec_acesso_validar_senha($senha, $senha_confirmacao);
		if(!$validacao_senha['ok']){
			return ['ok' => false, 'msg' => $validacao_senha['msg']];
		}
```

Remover (linhas ~806-811):

```php
		if($this->db->field_exists('senha_token', 'usuarios')){
			$user_insert['senha_token'] = $token;
		}
		if($this->db->field_exists('senha_token_expires', 'usuarios')){
			$user_insert['senha_token_expires'] = $token_expires;
		}
```

No `return` de sucesso (linhas ~904-915), substituir:

```php
			'plano_valor' => $valor,
			'senha_gerada' => $senha,
			'token' => $token,
		];
```

por:

```php
			'plano_valor' => $valor,
			'nome_responsavel' => $nome_responsavel,
			'senha' => $senha,
			'plano_nome' => isset($plano->modelo) ? (string)$plano->modelo : '',
		];
```

Depois, confirme com `grep -n "senha_gerada\|\$token" application/models/adm/Saas_model.php` que não sobrou referência a `$token`/`senha_gerada` dentro de `create_operational_trial_signup()`.

- [ ] **Step 8: Controller Home — trial**

Em `iniciar_experiencia()`, no array `$signup_data` (linhas 95-105), adicionar após `'especialidade_id' => ...,`:

```php
			'senha'             => (string)$this->input->post('senha'),
			'senha_confirmacao' => (string)$this->input->post('senha_confirmacao'),
```

Substituir:

```php
		// Envia e-mail de boas-vindas com credenciais e link de definição de senha
		$this->_enviar_email_boas_vindas($result);
```

por:

```php
		// E-mail de boas-vindas com login + a senha escolhida (lembrete para a 2ª visita)
		$this->load->library('email_acesso');
		$this->email_acesso->boas_vindas([
			'nome'        => $result['nome_responsavel'],
			'login'       => $result['login'],
			'senha'       => $result['senha'],
			'tenant_nome' => $result['tenant_nome'],
			'trial_fim'   => !empty($result['trial_ends_at']) ? date('d/m/Y', strtotime($result['trial_ends_at'])) : '',
			'plano_nome'  => $result['plano_nome'],
			'origem'      => 'trial',
		]);
```

Na sessão de auto-login do mesmo método (linhas 118-124), trocar `'usr'   => $user,` por `'usr'   => true,` (padronização com `logar()`; `verSession()` só testa verdade).

- [ ] **Step 9: Controller Home — assinatura**

Em `contratar()`, logo após o bloco `if(!$result['ok']){ ... }` (linha 199), adicionar:

```php

		$this->load->library('email_acesso');
		$this->email_acesso->boas_vindas([
			'nome'        => $result['nome_responsavel'],
			'login'       => $result['login'],
			'senha'       => $result['senha'],
			'tenant_nome' => $result['tenant_nome'],
			'trial_fim'   => !empty($result['trial_ends_at']) ? date('d/m/Y', strtotime($result['trial_ends_at'])) : '',
			'plano_nome'  => $result['plano_nome'],
			'origem'      => 'assinatura',
		]);
```

- [ ] **Step 10: Remover `_enviar_email_boas_vindas()`**

Em `application/controllers/Home.php`, apagar o bloco do comentário `// ── E-MAIL BOAS-VINDAS ──...` até o `}` que fecha `_enviar_email_boas_vindas()` (linhas ~724-790). Confirme: `grep -n "_enviar_email_boas_vindas\|senha_gerada" application/` → sem resultados.

- [ ] **Step 11: Run tests + lint**

Run:
```bash
/c/PHP/PHP7.2/php.exe tests/acesso_helper_test.php && /c/PHP/PHP7.2/php.exe tests/acesso_source_test.php && \
for f in application/models/adm/Saas_model.php application/controllers/Home.php application/views/public/experimentar.php application/views/public/assinar.php application/views/public/experimentar-sucesso.php; do /c/PHP/PHP7.2/php.exe -l $f; done
```
Expected: `OK`, `OK`, e `No syntax errors detected` em todos.

- [ ] **Step 12: Commit**

```bash
git add application/models/adm/Saas_model.php application/controllers/Home.php application/views/public/experimentar.php application/views/public/assinar.php application/views/public/experimentar-sucesso.php tests/acesso_source_test.php
git commit -m "feat(acesso): usuario escolhe a senha no trial e na assinatura e recebe por e-mail"
```

---

### Task 5: Usuários criados pela equipe (e-mail de acesso ou convite)

**Files:**
- Modify: `application/controllers/adm/Usuarios.php` — `cadastrar()` (212-321)
- Modify: `application/views/adm/usuarios/new/cadastro.php:275-291`
- Modify: `application/views/adm/usuarios/new/edicao.php:72`
- Test: `tests/acesso_source_test.php`

**Interfaces:**
- Consumes: `utec_acesso_email_valido()`, `utec_acesso_senha_aleatoria(16)`, `utec_acesso_gerar_token()`; `Email_acesso::acesso_equipe(['email','nome','login','senha','token','cadastrado_por'])`.
- Produces: flash keys `cadastro_aviso` (warning) e `cadastro_ok` (sucesso) exibidas em `new/edicao.php`.

- [ ] **Step 1: Write the failing test**

Em `tests/acesso_source_test.php`, antes de `echo "OK\n";`, acrescente:

```php
// --- Task 5: usuarios criados pela equipe
$usu = src('application/controllers/adm/Usuarios.php');
$cad = corpoFuncao($usu, 'function cadastrar() {');
assertSrc($cad !== '', 'Usuarios::cadastrar() nao encontrado.');
assertSrc(strpos($cad, 'email_acesso->acesso_equipe(') !== false, 'cadastrar() deve enviar e-mail de acesso a equipe.');
assertSrc(strpos($cad, 'DATE_ADD(NOW(), INTERVAL 7 DAY)') !== false, 'Convite deve expirar em 7 dias pelo relogio do MySQL.');
assertSrc(strpos($cad, 'utec_acesso_senha_aleatoria(') !== false, 'Convite sem senha deve gravar senha aleatoria interna.');
assertSrc(strpos($cad, "'cadastro_aviso'") !== false, 'Sem e-mail e sem senha deve avisar quem cadastrou.');
assertSrc(strpos($cad, "'DATE_ADD(NOW(), INTERVAL 7 DAY)'") > strpos($cad, 'get_usuario_tenant_id('), 'set() do convite deve ficar depois das consultas intermediarias (logo antes do insert).');

$cadView = src('application/views/adm/usuarios/new/cadastro.php');
assertSrc(strpos($cadView, 'Deixe em branco para o próprio usuário criar a senha') !== false, 'Formulario deve explicar a senha em branco.');
$edView = src('application/views/adm/usuarios/new/edicao.php');
assertSrc(strpos($edView, "flashdata('cadastro_aviso')") !== false && strpos($edView, "flashdata('cadastro_ok')") !== false, 'Edicao deve exibir flashes do cadastro.');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php`
Expected: FAIL — `cadastrar() deve enviar e-mail de acesso a equipe.`

- [ ] **Step 3: Controller `cadastrar()`**

Em `application/controllers/adm/Usuarios.php`, substituir (linhas 250-256):

```php
	if($nivel < 5){
		$dd['login'] = $this->input->post('login');
		$senha_nova  = $this->input->post('senha');
		if($senha_nova){
			$dd['senha'] = password_hash($senha_nova, PASSWORD_DEFAULT);
		}
	}
```

por:

```php
	// Acesso do novo usuário (níveis 1-4): e-mail com a senha informada ou convite para definir
	$enviar_acesso = null;
	if($nivel < 5){
		$this->load->helper('acesso');
		$email_novo = trim((string)$this->input->post('email'));
		$login_novo = trim((string)$this->input->post('login'));
		if($login_novo === '' && utec_acesso_email_valido($email_novo)){
			$login_novo = strtolower($email_novo);
		}
		$dd['login'] = $login_novo;
		$senha_nova  = (string)$this->input->post('senha');
		$tem_token   = $this->db->field_exists('senha_token', 'usuarios') && $this->db->field_exists('senha_token_expires', 'usuarios');

		if($senha_nova !== ''){
			$dd['senha'] = password_hash($senha_nova, PASSWORD_DEFAULT);
			if(utec_acesso_email_valido($email_novo)){
				$enviar_acesso = ['senha' => $senha_nova, 'token' => ''];
			}
		}elseif(utec_acesso_email_valido($email_novo) && $tem_token){
			$token = utec_acesso_gerar_token();
			$dd['senha'] = password_hash(utec_acesso_senha_aleatoria(16), PASSWORD_DEFAULT);
			$dd['senha_token'] = $token;
			// A expiração é gravada logo antes do insert (ver abaixo)
			$enviar_acesso = ['senha' => '', 'token' => $token, 'expira_convite' => true];
		}else{
			$this->session->set_flashdata('cadastro_aviso', 'Usuário criado sem e-mail e sem senha — ele não conseguirá entrar até você definir uma senha aqui na edição.');
		}
	}
```

Por que a expiração não é gravada no bloco acima: `$this->db->set(..., false)` fica pendente no query builder até o próximo insert/update, e entre esse bloco e o insert há consultas intermediárias (`get_usuario_tenant_id()`, linha 274) que podem consumir ou limpar o estado do builder. Por isso o `set()` fica imediatamente antes do insert.

Depois, substituir (linha 302):

```php
	if ($this->db->insert('usuarios', $dd)) {
		$new_id    = $this->db->insert_id();
		$nivel_int = (int)$nivel;
```

por:

```php
	if ($enviar_acesso && !empty($enviar_acesso['expira_convite'])) {
		$this->db->set('senha_token_expires', 'DATE_ADD(NOW(), INTERVAL 7 DAY)', false);
	}
	if ($this->db->insert('usuarios', $dd)) {
		$new_id    = $this->db->insert_id();
		$nivel_int = (int)$nivel;
		if ($enviar_acesso) {
			$this->load->library('email_acesso');
			$enviado = $this->email_acesso->acesso_equipe([
				'email'          => trim((string)$dd['email']),
				'nome'           => $dd['nome'],
				'login'          => $dd['login'],
				'senha'          => $enviar_acesso['senha'],
				'token'          => $enviar_acesso['token'],
				'cadastrado_por' => isset($dd_user->nome) ? $dd_user->nome : 'Sua clínica',
			]);
			if ($enviado) {
				$this->session->set_flashdata('cadastro_ok', $enviar_acesso['token'] !== ''
					? 'Enviamos para '.trim((string)$dd['email']).' um link para o usuário criar a própria senha (vale por 7 dias).'
					: 'Enviamos o login e a senha para '.trim((string)$dd['email']).'.');
			} else {
				$this->session->set_flashdata('cadastro_aviso', 'Usuário criado, mas não conseguimos enviar o e-mail de acesso. Informe o login e a senha ao usuário ou peça para ele usar "Esqueci minha senha".');
			}
		}
```

- [ ] **Step 4: View `new/cadastro.php` — ajuda no campo senha e login**

Substituir (linhas 279 e 289):

```php
                                        <input type="text" name="login"  class="form-control" placeholder="Login de acesso" value="<?php #echo $usuario->login; ?>">
```

por:

```php
                                        <input type="text" name="login"  class="form-control" placeholder="Login de acesso (em branco = usa o e-mail)" value="<?php #echo $usuario->login; ?>">
```

e

```php
                                        <input type="password" name="senha"  class="form-control" placeholder="Senha de acesso" value="<?php #echo $usuario->login; ?>">
                                    </div>
```

por:

```php
                                        <input type="password" name="senha"  class="form-control" placeholder="Senha de acesso" autocomplete="new-password" value="">
                                        <small class="form-text text-muted">Deixe em branco para o próprio usuário criar a senha pelo link enviado ao e-mail. Se preencher, enviaremos o login e a senha para o e-mail informado.</small>
                                    </div>
```

- [ ] **Step 5: View `new/edicao.php` — flashes**

Em `application/views/adm/usuarios/new/edicao.php`, antes da linha 72 (`<h6 class="element-header">`), inserir:

```php
                      <?php $cadastro_ok = $this->session->flashdata('cadastro_ok'); $cadastro_aviso = $this->session->flashdata('cadastro_aviso'); ?>
                      <?php if($cadastro_ok){ ?>
                        <div class="alert alert-success"><?=htmlspecialchars((string)$cadastro_ok)?></div>
                      <?php } ?>
                      <?php if($cadastro_aviso){ ?>
                        <div class="alert alert-warning"><?=htmlspecialchars((string)$cadastro_aviso)?></div>
                      <?php } ?>
```

- [ ] **Step 6: Run tests + lint**

Run:
```bash
/c/PHP/PHP7.2/php.exe tests/acesso_source_test.php && \
for f in application/controllers/adm/Usuarios.php application/views/adm/usuarios/new/cadastro.php application/views/adm/usuarios/new/edicao.php; do /c/PHP/PHP7.2/php.exe -l $f; done
```
Expected: `OK` e `No syntax errors detected`.

- [ ] **Step 7: Commit**

```bash
git add application/controllers/adm/Usuarios.php application/views/adm/usuarios/new/cadastro.php application/views/adm/usuarios/new/edicao.php tests/acesso_source_test.php
git commit -m "feat(acesso): e-mail de acesso ou convite ao cadastrar usuario da equipe"
```

---

### Task 6: Manual, documentação e verificação ponta a ponta

**Files:**
- Modify: `application/libraries/Manual_conteudo.php:81-98` (capítulo `acesso-hierarquia`)
- Modify: `CLAUDE.md` (seções 6.1, 6.3 e 11)
- Test: `tests/manual_conteudo_test.php` (existente), todos `tests/acesso_*`

- [ ] **Step 1: Manual — tópicos de acesso**

Em `application/libraries/Manual_conteudo.php`, no capítulo `acesso-hierarquia`, substituir:

```php
                    '*' => array(
                        'Você só vê pacientes, agendamentos e prontuários vinculados à sua própria estrutura - nunca dados de outra clínica ou profissional fora da sua árvore.',
                    ),
```

por:

```php
                    '*' => array(
                        'Você só vê pacientes, agendamentos e prontuários vinculados à sua própria estrutura - nunca dados de outra clínica ou profissional fora da sua árvore.',
                        'Primeiro acesso: a senha é criada por você no cadastro, ou pelo link "Definir minha senha" que chega no seu e-mail quando alguém da clínica cadastra você. Guarde o e-mail de boas-vindas - ele traz seu login.',
                        'Esqueceu a senha? Na tela de login, clique em `Esqueci minha senha`, informe seu e-mail ou usuário e use o link que chega no e-mail (vale por 1 hora). Se não chegar, confira a caixa de spam.',
                    ),
```

E no capítulo `equipe`, trocar `'*' => array('Acesse \`Equipe\` para cadastrar ou revisar prestadores e colaboradores.'),` por:

```php
                    '*' => array(
                        'Acesse `Equipe` para cadastrar ou revisar prestadores e colaboradores.',
                        'Ao cadastrar alguém com e-mail, o sistema envia o acesso automaticamente: com a senha que você digitou ou, se deixar a senha em branco, com um link para a própria pessoa criar a senha (vale por 7 dias).',
                    ),
```

Atualizar `'atualizado_em'` dos dois capítulos para `'2026-09-28'`.

- [ ] **Step 2: CLAUDE.md**

- Seção 6.1, linha de `Home.php`: acrescentar "; `acesso/senha/{token}` (definir senha), `acesso/esqueci` (redefinição por link, token 1h)".
- Seção 6.3: acrescentar as rotas `acesso/esqueci` e `acesso/esqueci/enviar`.
- Seção 11, "Notas da Migração de Senhas": acrescentar
  - "**Cadastro:** trial e assinatura pedem senha + confirmação; o e-mail de boas-vindas (`Email_acesso::boas_vindas`) leva login + senha escolhida; e-mail interno 'Novo cadastro' sem senha."
  - "**Equipe:** `adm/usuarios/cadastrar` envia login+senha por e-mail, ou convite com token de 7 dias se a senha ficar em branco."
  - "**Esqueci minha senha:** `acesso/esqueci` → token de 60 min em `usuarios.senha_token` (expiração calculada no MySQL), resposta genérica, throttle de 2 min. Helper `acesso_helper.php`, testes `tests/acesso_*`."

- [ ] **Step 3: Rodar toda a suíte relevante**

Run:
```bash
for t in tests/acesso_helper_test.php tests/acesso_source_test.php tests/manual_conteudo_test.php; do echo "== $t"; /c/PHP/PHP7.2/php.exe $t || exit 1; done; /c/PHP/PHP7.2/php.exe -l application/libraries/Manual_conteudo.php
```
Expected: `OK` nos três e `No syntax errors detected`.

- [ ] **Step 4: Verificação manual local (WAMP, `http://localhost/utec/` ou o host local configurado)**

1. `/experimentar`: tentar enviar com senhas diferentes → bloqueio no navegador; com senha de 5 caracteres (removendo `minlength` pelo DevTools) → flash `A senha precisa ter pelo menos 6 caracteres.`
2. Cadastro trial válido → entra logado. Sair. Entrar em `/admin` com e-mail + senha criada → entra.
3. Senha errada em `/admin` → mensagem `Usuário ou senha inválidos.` + link.
4. `/acesso/esqueci` com o e-mail → mensagem genérica. Em `usuarios`, conferir `senha_token` preenchido e `senha_token_expires ≈ NOW()+1h` (`SELECT senha_token_expires, NOW() FROM usuarios WHERE email=...`). Repetir em < 2 min → token **não** muda.
5. Abrir `/acesso/senha/{token}` → definir nova senha → cai logado em `adm/atendimento`. Reabrir o mesmo link → volta para `/admin` com "link já foi usado ou expirou".
6. `/acesso/esqueci` com e-mail inexistente → mesma mensagem genérica.
7. Logado como estabelecimento, cadastrar colaborador com e-mail e **sem** senha → flash de envio na edição; `senha_token_expires ≈ NOW()+7 dias`. Com senha → flash "Enviamos o login e a senha". Sem e-mail e sem senha → aviso amarelo.
8. Se o SMTP local não enviar, confirmar em `application/logs/log-YYYY-MM-DD.php` a linha `email_acesso ... falhou` **sem** a senha no texto (`grep -n "email_acesso" application/logs/log-*.php`).

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Manual_conteudo.php CLAUDE.md
git commit -m "docs(acesso): manual e CLAUDE.md com primeiro acesso e esqueci minha senha"
```

- [ ] **Step 6: Pré-deploy (responsabilidade do agente-dev-infra, não executar sem aprovação)**

- Confirmar em produção que `usuarios.senha_token` e `usuarios.senha_token_expires` existem (`SHOW COLUMNS FROM usuarios LIKE 'senha_token%'`).
- Baixar o log de produção e procurar falhas de envio de e-mail recentes para avaliar entregabilidade (SPF/DKIM de `suporte@utecnologia.com.br`).
- Ordem de upload: `helpers/acesso_helper.php` e `libraries/Email_acesso.php` **antes** de `Home.php`, `adm/Usuarios.php` e `Saas_model.php` (eles carregam as dependências novas); views e `routes.php` por último.
- Healthcheck: `/` 200, `/admin` 200, `/acesso/esqueci` 200, `/experimentar` 200.
