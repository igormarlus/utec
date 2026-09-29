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
            ? 'display:inline-block;padding:14px 28px;background-color:#0f766e;background:linear-gradient(90deg,#0f766e,#f97316);color:#fff;font-size:15px;font-weight:700;border-radius:999px;text-decoration:none;'
            : 'display:inline-block;padding:12px 24px;background:#fff;border:1px solid #d1d5db;color:#374151;font-size:14px;font-weight:600;border-radius:999px;text-decoration:none;';
        $link = '<p style="margin:20px 0;"><a href="'.utec_acesso_h($url).'" style="'.$estilo.'">'.utec_acesso_h($texto).'</a></p>';
        if ($primario) {
            $link .= '<p style="margin:6px 0 0;font-size:12px;color:#64748b;word-break:break-all;">Se o botão não funcionar, copie e cole este endereço no navegador:<br>'.utec_acesso_h($url).'</p>';
        }
        return $link;
    }
}

if (!function_exists('utec_acesso_email_layout')) {
    function utec_acesso_email_layout($titulo, $conteudo)
    {
        return '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f6f8fb;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fb;padding:40px 20px;"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <tr><td style="background-color:#0f766e;background:linear-gradient(90deg,#0f766e,#f97316);padding:32px 40px;">
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
