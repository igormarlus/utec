<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('utec_whatsapp_read')) {
    function utec_whatsapp_read($source, $key, $default = '')
    {
        if (is_array($source)) {
            return isset($source[$key]) ? $source[$key] : $default;
        }
        if (is_object($source)) {
            return isset($source->$key) ? $source->$key : $default;
        }
        return $default;
    }
}

if (!function_exists('utec_whatsapp_normalizar_numero')) {
    function utec_whatsapp_normalizar_numero($numero)
    {
        return preg_replace('/\D+/', '', (string)$numero);
    }
}

if (!function_exists('utec_whatsapp_checkbox_marcado')) {
    function utec_whatsapp_checkbox_marcado($post)
    {
        $valor = utec_whatsapp_read($post, 'enviar_whatsapp_confirmacao', '');
        return in_array((string)$valor, ['1', 'on', 'true'], true);
    }
}

if (!function_exists('utec_whatsapp_config_ativa')) {
    function utec_whatsapp_config_ativa($config)
    {
        if (!$config) {
            return false;
        }

        $required = [
            'phone_number_id',
            'access_token',
            'template_name',
            'template_lang',
        ];

        if ((int)utec_whatsapp_read($config, 'status', 0) !== 1) {
            return false;
        }

        foreach ($required as $field) {
            if (trim((string)utec_whatsapp_read($config, $field, '')) === '') {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('utec_whatsapp_formatar_data_br')) {
    function utec_whatsapp_formatar_data_br($data)
    {
        $data = trim((string)$data);
        if ($data === '') {
            return '';
        }

        $timestamp = strtotime($data);
        if (!$timestamp) {
            return $data;
        }

        return date('d/m/Y', $timestamp);
    }
}

if (!function_exists('utec_whatsapp_formatar_hora_br')) {
    function utec_whatsapp_formatar_hora_br($hora)
    {
        $hora = trim((string)$hora);
        if ($hora === '') {
            return '';
        }

        if (preg_match('/^\d{2}:\d{2}/', $hora)) {
            return substr($hora, 0, 5);
        }

        $timestamp = strtotime($hora);
        if (!$timestamp) {
            return $hora;
        }

        return date('H:i', $timestamp);
    }
}
