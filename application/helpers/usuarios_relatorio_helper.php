<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('utec_relatorio_read')) {
    function utec_relatorio_read($row, $key) {
        if (is_array($row)) {
            return isset($row[$key]) ? $row[$key] : null;
        }
        if (is_object($row)) {
            return isset($row->$key) ? $row->$key : null;
        }
        return null;
    }
}

if (!function_exists('utec_relatorio_resolve_atividade')) {
    function utec_relatorio_resolve_atividade($row) {
        $nivel = (int) utec_relatorio_read($row, 'nivel');
        $especialidade = trim((string) utec_relatorio_read($row, 'especialidade_nome'));
        $profissao = trim((string) utec_relatorio_read($row, 'profissao'));

        if ($nivel === 3 && $especialidade !== '') {
            return $especialidade;
        }

        if ($profissao !== '') {
            return $profissao;
        }

        return 'Nao informado';
    }
}

if (!function_exists('utec_relatorio_resolve_plano_status')) {
    function utec_relatorio_resolve_plano_status($row) {
        $tenantStatus = utec_relatorio_read($row, 'tenant_status');
        $subscriptionStatus = strtolower(trim((string) utec_relatorio_read($row, 'subscription_status')));

        if ($tenantStatus !== null && (int) $tenantStatus !== 1) {
            return 'Bloqueado';
        }

        if (in_array($subscriptionStatus, array('trial', 'trialing'), true)) {
            return 'Trial';
        }

        if (in_array($subscriptionStatus, array('authorized', 'active'), true)) {
            return 'Pago';
        }

        if ($tenantStatus !== null || $subscriptionStatus !== '') {
            return 'Sem plano';
        }

        return '';
    }
}

if (!function_exists('utec_relatorio_formatar_numero')) {
    function utec_relatorio_formatar_numero($valor) {
        return (string) ((int) $valor);
    }
}

if (!function_exists('utec_relatorio_mostra_plano_por_nivel')) {
    function utec_relatorio_mostra_plano_por_nivel($nivel) {
        return in_array((int) $nivel, array(1, 2, 3), true);
    }
}

if (!function_exists('utec_relatorio_tem_foto_usuario')) {
    function utec_relatorio_tem_foto_usuario($row) {
        return trim((string) utec_relatorio_read($row, 'img')) !== '';
    }
}

if (!function_exists('utec_relatorio_inicial_usuario')) {
    function utec_relatorio_inicial_usuario($row) {
        $nome = trim((string) utec_relatorio_read($row, 'nome'));
        if ($nome === '') {
            return '?';
        }

        if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
            return mb_strtoupper(mb_substr($nome, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return strtoupper(substr($nome, 0, 1));
    }
}
