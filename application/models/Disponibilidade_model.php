<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Grade de atendimento, bloqueios e calculo de horarios livres do prestador.
 * Interface pensada para ser reutilizada pelo chatbot (proximos_livres / verificar_horario).
 * Sem schema (migracao nao rodada) -> tem_grade = false, nunca erro.
 */
class Disponibilidade_model extends CI_Model {

    const DURACAO_PADRAO = 30;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('disponibilidade');
    }

    public function schema_ok()
    {
        return $this->db->table_exists('prestador_horarios') && $this->db->table_exists('prestador_bloqueios');
    }

    public function get_duracao($id_prestador)
    {
        if (!$this->db->field_exists('duracao_atendimento_min', 'usuarios')) {
            return self::DURACAO_PADRAO;
        }
        $row = $this->db->query(
            "SELECT duracao_atendimento_min FROM usuarios WHERE id = ".(int)$id_prestador." LIMIT 1"
        )->row();
        $valor = $row ? (int)$row->duracao_atendimento_min : 0;
        return $valor > 0 ? $valor : self::DURACAO_PADRAO;
    }

    public function get_config($id_prestador)
    {
        $grade = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array());
        $cfg = array('duracao' => $this->get_duracao($id_prestador), 'grade' => $grade, 'tem_grade' => false);
        if (!$this->schema_ok()) {
            return $cfg;
        }
        $qr = $this->db->query(
            "SELECT dia_semana, hora_inicio, hora_fim FROM prestador_horarios
             WHERE id_prestador = ".(int)$id_prestador." ORDER BY dia_semana ASC, hora_inicio ASC"
        );
        foreach ($qr->result() as $row) {
            $dia = (int)$row->dia_semana;
            if ($dia < 0 || $dia > 6) {
                continue;
            }
            $cfg['grade'][$dia][] = array(substr($row->hora_inicio, 0, 5), substr($row->hora_fim, 0, 5));
            $cfg['tem_grade'] = true;
        }
        return $cfg;
    }

    public function salvar_config($id_prestador, $duracao, $grade)
    {
        $id_prestador = (int)$id_prestador;
        if (!$this->schema_ok() || $id_prestador <= 0) {
            return false;
        }
        $agora = date('Y-m-d H:i:s');
        $this->db->trans_start();
        if ($this->db->field_exists('duracao_atendimento_min', 'usuarios')) {
            $this->db->where('id', $id_prestador);
            $this->db->update('usuarios', array('duracao_atendimento_min' => (int)$duracao));
        }
        $this->db->where('id_prestador', $id_prestador);
        $this->db->delete('prestador_horarios');
        foreach ((array)$grade as $dia => $intervalos) {
            foreach ((array)$intervalos as $iv) {
                $this->db->insert('prestador_horarios', array(
                    'id_prestador' => $id_prestador,
                    'dia_semana' => (int)$dia,
                    'hora_inicio' => $iv[0],
                    'hora_fim' => $iv[1],
                    'created_at' => $agora,
                ));
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function listar_bloqueios_futuros($id_prestador)
    {
        if (!$this->schema_ok()) {
            return array();
        }
        return $this->db->query(
            "SELECT id, inicio, fim, motivo FROM prestador_bloqueios
             WHERE id_prestador = ".(int)$id_prestador." AND fim >= ".$this->db->escape(date('Y-m-d H:i:s'))."
             ORDER BY inicio ASC"
        )->result();
    }

    public function adicionar_bloqueio($id_prestador, $inicio, $fim, $motivo, $id_user_cad)
    {
        if (!$this->schema_ok()) {
            return false;
        }
        $motivo = trim((string)$motivo);
        return (bool)$this->db->insert('prestador_bloqueios', array(
            'id_prestador' => (int)$id_prestador,
            'inicio' => $inicio,
            'fim' => $fim,
            'motivo' => $motivo === '' ? null : mb_substr($motivo, 0, 150, 'UTF-8'),
            'id_user_cad' => (int)$id_user_cad,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function remover_bloqueio($id_bloqueio, $id_prestador)
    {
        if (!$this->schema_ok()) {
            return false;
        }
        $this->db->where('id', (int)$id_bloqueio);
        $this->db->where('id_prestador', (int)$id_prestador);
        $this->db->delete('prestador_bloqueios');
        return $this->db->affected_rows() > 0;
    }

    private function dia_seguinte($data)
    {
        return date('Y-m-d', strtotime('+1 day', strtotime($data)));
    }

    private function bloqueios_brutos($id_prestador, $inicio_datetime, $fim_datetime)
    {
        $qr = $this->db->query(
            "SELECT inicio, fim FROM prestador_bloqueios
             WHERE id_prestador = ".(int)$id_prestador."
               AND inicio < ".$this->db->escape($fim_datetime)."
               AND fim > ".$this->db->escape($inicio_datetime)
        );
        $brutos = array();
        foreach ($qr->result() as $row) {
            $brutos[] = array('inicio' => $row->inicio, 'fim' => $row->fim);
        }
        return $brutos;
    }

    private function bloqueios_do_dia($id_prestador, $data)
    {
        return utec_disp_recortar_bloqueios_no_dia(
            $this->bloqueios_brutos($id_prestador, $data.' 00:00:00', $this->dia_seguinte($data).' 00:00:00'),
            $data
        );
    }

    private function horas_agendadas($id_prestador, $data, $ignorar_agendamento_id)
    {
        $qr = $this->db->query(
            "SELECT hora_agenda FROM agendamentos
             WHERE id_prestador = ".(int)$id_prestador."
               AND data_agenda = ".$this->db->escape($data)."
               AND status IN (0,1,2)
               AND id <> ".(int)$ignorar_agendamento_id
        );
        $horas = array();
        foreach ($qr->result() as $row) {
            $horas[] = substr((string)$row->hora_agenda, 0, 5);
        }
        return $horas;
    }

    private function horas_agendadas_periodo($id_prestador, $inicio, $fim, $ignorar_agendamento_id)
    {
        $qr = $this->db->query(
            "SELECT data_agenda, hora_agenda FROM agendamentos
             WHERE id_prestador = ".(int)$id_prestador."
               AND data_agenda BETWEEN ".$this->db->escape($inicio)." AND ".$this->db->escape($fim)."
               AND status IN (0,1,2)
               AND id <> ".(int)$ignorar_agendamento_id
        );
        $mapa = array();
        foreach ($qr->result() as $row) {
            $mapa[substr((string)$row->data_agenda, 0, 10)][] = substr((string)$row->hora_agenda, 0, 5);
        }
        return $mapa;
    }

    public function horarios_livres($id_prestador, $data, $ignorar_agendamento_id = 0, $minimo_datetime = null)
    {
        $cfg = $this->get_config($id_prestador);
        $res = array('tem_grade' => $cfg['tem_grade'], 'duracao' => $cfg['duracao'], 'livres' => array());
        $hoje = date('Y-m-d');
        if (!$cfg['tem_grade'] || !utec_disp_data_valida($data) || $data < $hoje) {
            return $res;
        }
        $dia = (int)date('w', strtotime($data));
        $slots = utec_disp_livres_do_dia(
            $cfg['grade'][$dia],
            $cfg['duracao'],
            $this->horas_agendadas($id_prestador, $data, $ignorar_agendamento_id),
            $this->bloqueios_brutos($id_prestador, $data.' 00:00:00', $this->dia_seguinte($data).' 00:00:00'),
            $data
        );
        if ($minimo_datetime !== null) {
            $slots = utec_disp_aplicar_minimo($slots, $data, $minimo_datetime);
        } elseif ($data === $hoje) {
            $slots = utec_disp_filtrar_apos($slots, date('H:i'));
        }
        $res['livres'] = $slots;
        return $res;
    }

    public function dias_com_vaga($id_prestador, $minimo_datetime, $dias = 30, $limite = 10, $ignorar_agendamento_id = 0)
    {
        $cfg = $this->get_config($id_prestador);
        $res = array('tem_grade' => $cfg['tem_grade'], 'duracao' => $cfg['duracao'], 'dias' => array());
        $ts = strtotime((string)$minimo_datetime);
        if (!$cfg['tem_grade'] || $ts === false) {
            return $res;
        }
        $inicio = date('Y-m-d', $ts);
        $fim = date('Y-m-d', strtotime('+'.((int)$dias - 1).' day', strtotime($inicio)));
        $agendadas = $this->horas_agendadas_periodo($id_prestador, $inicio, $fim, $ignorar_agendamento_id);
        $bloqueios = $this->bloqueios_brutos($id_prestador, $inicio.' 00:00:00', $this->dia_seguinte($fim).' 00:00:00');
        for ($i = 0; $i < (int)$dias && count($res['dias']) < (int)$limite; $i++) {
            $data = date('Y-m-d', strtotime('+'.$i.' day', strtotime($inicio)));
            $dia = (int)date('w', strtotime($data));
            if (empty($cfg['grade'][$dia])) {
                continue;
            }
            $slots = utec_disp_livres_do_dia(
                $cfg['grade'][$dia],
                $cfg['duracao'],
                isset($agendadas[$data]) ? $agendadas[$data] : array(),
                $bloqueios,
                $data
            );
            $slots = utec_disp_aplicar_minimo($slots, $data, $minimo_datetime);
            if (!empty($slots)) {
                $res['dias'][] = array('data' => $data, 'qtd' => count($slots));
            }
        }
        return $res;
    }

    public function verificar_horario($id_prestador, $data, $hora, $ignorar_agendamento_id = 0)
    {
        $cfg = $this->get_config($id_prestador);
        $res = array('tem_grade' => $cfg['tem_grade'], 'situacao' => 'livre');
        if (!$cfg['tem_grade'] || !utec_disp_data_valida($data)) {
            return $res;
        }
        $dia = (int)date('w', strtotime($data));
        $res['situacao'] = utec_disp_classificar_horario(
            $hora,
            $cfg['grade'][$dia],
            $this->horas_agendadas($id_prestador, $data, $ignorar_agendamento_id),
            $this->bloqueios_do_dia($id_prestador, $data),
            $cfg['duracao']
        );
        return $res;
    }

    public function proximos_livres($id_prestador, $a_partir_de, $limite = 10)
    {
        $saida = array();
        if (!utec_disp_data_valida($a_partir_de) || !$this->get_config($id_prestador)['tem_grade']) {
            return $saida;
        }
        $base = strtotime($a_partir_de);
        for ($i = 0; $i < 30 && count($saida) < $limite; $i++) {
            $data = date('Y-m-d', strtotime('+'.$i.' day', $base));
            $r = $this->horarios_livres($id_prestador, $data);
            foreach ($r['livres'] as $hora) {
                $saida[] = array('data' => $data, 'hora' => $hora);
                if (count($saida) >= $limite) {
                    break;
                }
            }
        }
        return $saida;
    }
}
