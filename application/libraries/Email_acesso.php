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
        } catch (Throwable $e) {
            log_message('error', 'email_acesso '.$tipo.' excecao para '.$para.': '.$e->getMessage());
        }
        return false;
    }
}
