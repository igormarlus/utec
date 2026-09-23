<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Funcoes puras de disponibilidade (horarios de atendimento do prestador).
 * Horas em 'HH:MM'; internamente minutos desde 00:00. '24:00' so como fim de faixa.
 * Sem acesso a banco — quem busca os dados e o Disponibilidade_model.
 */

if (!function_exists('utec_disp_duracoes_permitidas')) {
    function utec_disp_duracoes_permitidas() {
        return array(10, 15, 20, 30, 40, 45, 50, 60, 90, 120);
    }
}

if (!function_exists('utec_disp_nomes_dias')) {
    function utec_disp_nomes_dias() {
        return array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado');
    }
}

if (!function_exists('utec_disp_min')) {
    function utec_disp_min($hora) {
        if (!preg_match('/^(\d{1,2}):(\d{2})(:\d{2})?$/', trim((string)$hora), $m)) {
            return null;
        }
        $h = (int)$m[1];
        $i = (int)$m[2];
        if ($h === 24 && $i === 0) {
            return 1440;
        }
        if ($h > 23 || $i > 59) {
            return null;
        }
        return $h * 60 + $i;
    }
}

if (!function_exists('utec_disp_hhmm')) {
    function utec_disp_hhmm($minutos) {
        $minutos = (int)$minutos;
        return sprintf('%02d:%02d', (int)floor($minutos / 60), $minutos % 60);
    }
}

if (!function_exists('utec_disp_data_valida')) {
    function utec_disp_data_valida($data) {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string)$data, $m)) {
            return false;
        }
        return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
    }
}

if (!function_exists('utec_disp_faixas_pares')) {
    // [['HH:MM','HH:MM'], ...] -> [[ini_min, fim_min], ...] descartando invalidos
    function utec_disp_faixas_pares($pares) {
        $faixas = array();
        foreach ((array)$pares as $par) {
            $ini = utec_disp_min(isset($par[0]) ? $par[0] : '');
            $fim = utec_disp_min(isset($par[1]) ? $par[1] : '');
            if ($ini === null || $fim === null || $fim <= $ini) {
                continue;
            }
            $faixas[] = array($ini, $fim);
        }
        return $faixas;
    }
}

if (!function_exists('utec_disp_faixas_agendamentos')) {
    function utec_disp_faixas_agendamentos($horas, $duracao_min) {
        $faixas = array();
        foreach ((array)$horas as $hora) {
            $ini = utec_disp_min($hora);
            if ($ini === null) {
                continue;
            }
            $faixas[] = array($ini, $ini + (int)$duracao_min);
        }
        return $faixas;
    }
}

if (!function_exists('utec_disp_colide')) {
    function utec_disp_colide($ini, $fim, $faixas) {
        foreach ($faixas as $f) {
            if ($ini < $f[1] && $f[0] < $fim) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('utec_disp_gerar_slots')) {
    function utec_disp_gerar_slots($intervalos, $duracao_min) {
        $duracao_min = (int)$duracao_min;
        $slots = array();
        if ($duracao_min <= 0) {
            return array();
        }
        foreach (utec_disp_faixas_pares($intervalos) as $f) {
            for ($t = $f[0]; $t + $duracao_min <= $f[1]; $t += $duracao_min) {
                $slots[$t] = utec_disp_hhmm($t);
            }
        }
        ksort($slots);
        return array_values($slots);
    }
}

if (!function_exists('utec_disp_remover_ocupados')) {
    function utec_disp_remover_ocupados($slots, $horas_agendadas, $bloqueios_dia, $duracao_min) {
        $duracao_min = (int)$duracao_min;
        $faixas = array_merge(
            utec_disp_faixas_agendamentos($horas_agendadas, $duracao_min),
            utec_disp_faixas_pares($bloqueios_dia)
        );
        $livres = array();
        foreach ((array)$slots as $slot) {
            $ini = utec_disp_min($slot);
            if ($ini === null) {
                continue;
            }
            if (!utec_disp_colide($ini, $ini + $duracao_min, $faixas)) {
                $livres[] = $slot;
            }
        }
        return $livres;
    }
}

if (!function_exists('utec_disp_recortar_bloqueios_no_dia')) {
    function utec_disp_recortar_bloqueios_no_dia($bloqueios, $data) {
        if (!utec_disp_data_valida($data)) {
            return array();
        }
        $dia_ini = strtotime($data . ' 00:00:00');
        $dia_fim = strtotime('+1 day', $dia_ini);
        $pares = array();
        foreach ((array)$bloqueios as $b) {
            $ini = strtotime(isset($b['inicio']) ? (string)$b['inicio'] : '');
            $fim = strtotime(isset($b['fim']) ? (string)$b['fim'] : '');
            if ($ini === false || $fim === false || $fim <= $ini) {
                continue;
            }
            if ($fim <= $dia_ini || $ini >= $dia_fim) {
                continue;
            }
            $ini = max($ini, $dia_ini);
            $fim = min($fim, $dia_fim);
            $pares[] = array(
                utec_disp_hhmm((int)round(($ini - $dia_ini) / 60)),
                utec_disp_hhmm((int)round(($fim - $dia_ini) / 60)),
            );
        }
        return $pares;
    }
}

if (!function_exists('utec_disp_classificar_horario')) {
    function utec_disp_classificar_horario($hora, $intervalos, $horas_agendadas, $bloqueios_dia, $duracao_min) {
        $ini = utec_disp_min($hora);
        if ($ini === null || $ini >= 1440) {
            return 'fora_da_grade';
        }
        $fim = $ini + (int)$duracao_min;
        if (utec_disp_colide($ini, $fim, utec_disp_faixas_pares($bloqueios_dia))) {
            return 'bloqueado';
        }
        $cabe = false;
        foreach (utec_disp_faixas_pares($intervalos) as $f) {
            if ($ini >= $f[0] && $fim <= $f[1]) {
                $cabe = true;
                break;
            }
        }
        if (!$cabe) {
            return 'fora_da_grade';
        }
        if (utec_disp_colide($ini, $fim, utec_disp_faixas_agendamentos($horas_agendadas, $duracao_min))) {
            return 'ocupado';
        }
        return 'livre';
    }
}

if (!function_exists('utec_disp_filtrar_apos')) {
    function utec_disp_filtrar_apos($slots, $hora_limite) {
        $limite = utec_disp_min($hora_limite);
        if ($limite === null) {
            return array_values((array)$slots);
        }
        $saida = array();
        foreach ((array)$slots as $slot) {
            $m = utec_disp_min($slot);
            if ($m !== null && $m > $limite) {
                $saida[] = $slot;
            }
        }
        return $saida;
    }
}

if (!function_exists('utec_disp_validar_intervalos')) {
    function utec_disp_validar_intervalos($intervalos) {
        $faixas = array();
        foreach ((array)$intervalos as $iv) {
            $ini = utec_disp_min(isset($iv[0]) ? $iv[0] : '');
            $fim = utec_disp_min(isset($iv[1]) ? $iv[1] : '');
            if ($ini === null || $fim === null || $ini >= 1440) {
                return 'Horário inválido: use o formato HH:MM.';
            }
            if ($fim <= $ini) {
                return 'O fim do intervalo deve ser depois do início.';
            }
            if (utec_disp_colide($ini, $fim, $faixas)) {
                return 'Intervalos do mesmo dia não podem se sobrepor.';
            }
            $faixas[] = array($ini, $fim);
        }
        return '';
    }
}

if (!function_exists('utec_disp_normalizar_bloqueio')) {
    function utec_disp_normalizar_bloqueio($data_ini, $hora_ini, $data_fim, $hora_fim, $dia_inteiro) {
        $falha = array('ok' => false, 'inicio' => '', 'fim' => '', 'erro' => '');
        $data_ini = trim((string)$data_ini);
        $data_fim = trim((string)$data_fim) === '' ? $data_ini : trim((string)$data_fim);
        if (!utec_disp_data_valida($data_ini) || !utec_disp_data_valida($data_fim)) {
            $falha['erro'] = 'Informe datas válidas.';
            return $falha;
        }
        if ($dia_inteiro) {
            $inicio = $data_ini . ' 00:00:00';
            $fim = $data_fim . ' 23:59:59';
        } else {
            $hi = utec_disp_min($hora_ini);
            $hf = utec_disp_min($hora_fim);
            if ($hi === null || $hf === null || $hi >= 1440 || $hf >= 1440) {
                $falha['erro'] = 'Informe horários válidos (HH:MM).';
                return $falha;
            }
            $inicio = $data_ini . ' ' . utec_disp_hhmm($hi) . ':00';
            $fim = $data_fim . ' ' . utec_disp_hhmm($hf) . ':00';
        }
        if (strtotime($fim) <= strtotime($inicio)) {
            $falha['erro'] = 'O fim do bloqueio deve ser depois do início.';
            return $falha;
        }
        return array('ok' => true, 'inicio' => $inicio, 'fim' => $fim, 'erro' => '');
    }
}
