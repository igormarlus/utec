<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Email — extensão do CI3 Email
 *
 * O CI3 força quoted-printable em emails HTML, quebrando URLs longas
 * (inserção de "=\r\n" no meio do href) e corrompendo textos com acentos.
 *
 * Este override converte caracteres não-ASCII em entidades HTML numéricas
 * (tornando o body puro ASCII) e retorna o string sem codificação QP.
 * ASCII puro é QP válido — qualquer client decodifica sem alteração.
 */
class MY_Email extends CI_Email {

    protected function _prep_quoted_printable($str)
    {
        // Converte caracteres fora do ASCII (U+0080..U+FFFF) para entidades
        // HTML numéricas: á → &#225;  ã → &#227;  ó → &#243; etc.
        // O body fica puro ASCII — sem bytes altos para o QP codificar,
        // sem soft-wraps "=\r\n" sendo inseridos no meio de URLs ou palavras.
        $str = preg_replace_callback(
            '/[\x{0080}-\x{FFFF}]/u',
            static function (array $m) {
                // mb_ord() é PHP 7.2+; unpack via UCS-4BE funciona no PHP 7.0+
                $codepoint = unpack('N', mb_convert_encoding($m[0], 'UCS-4BE', 'UTF-8'))[1];
                return '&#' . $codepoint . ';';
            },
            $str
        );

        return $str; // retorna sem codificação QP — body já é ASCII puro
    }
}
