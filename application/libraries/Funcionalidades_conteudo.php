<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Catálogo único das funcionalidades exibidas no site (cadastro, home, landings).
// Regra: só o que existe no sistema; sem jargão técnico. Testes em tests/funcionalidades_*.
class Funcionalidades_conteudo {

    public static function grupos() {
        return array('agenda' => 'Agenda', 'prontuario' => 'Prontuário', 'whatsapp' => 'WhatsApp', 'gestao' => 'Gestão');
    }

    public static function por_grupo($grupo) {
        $saida = array();
        foreach (self::itens() as $item) {
            if ($item['grupo'] === $grupo) { $saida[] = $item; }
        }
        return $saida;
    }

    public static function por_ids(array $ids) {
        $indice = array();
        foreach (self::itens() as $item) { $indice[$item['id']] = $item; }
        $saida = array();
        foreach ($ids as $id) {
            if (is_string($id) && isset($indice[$id])) { $saida[] = $indice[$id]; }
        }
        return $saida;
    }

    public static function itens() {
        return array(
            array(
                'id' => 'agenda', 'grupo' => 'agenda', 'icone' => '📅', 'titulo' => 'Agenda inteligente',
                'resumo' => 'Consultas por profissional, com filtros, remarcação e cancelamento na própria agenda.',
                'descricao' => 'Visão do dia, da semana e do mês por profissional. Remarque e cancele direto na agenda, sem retrabalho.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'horarios', 'grupo' => 'agenda', 'icone' => '🕘', 'titulo' => 'Horários de atendimento',
                'resumo' => 'Grade semanal, duração da consulta e bloqueios, com sugestão de horários livres.',
                'descricao' => 'Cada profissional define dias, horários, duração da consulta e períodos bloqueados (férias, feriados). Ao agendar, o sistema sugere os horários livres e avisa quando é encaixe.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'tempo_espera', 'grupo' => 'agenda', 'icone' => '⏱️', 'titulo' => 'Check-in e tempo de espera',
                'resumo' => 'A recepção marca a chegada e o sistema mede espera e duração da consulta.',
                'descricao' => 'Com um clique em "Chegou", o sistema registra a chegada do paciente e, ao iniciar e finalizar o atendimento, mostra quanto ele esperou e quanto durou a consulta. Os relatórios trazem as médias por profissional.',
                'link' => '', 'novo' => true,
            ),
            array(
                'id' => 'lista_espera', 'grupo' => 'agenda', 'icone' => '📝', 'titulo' => 'Lista de espera',
                'resumo' => 'A equipe é avisada quando abre uma vaga e encaixa quem espera.',
                'descricao' => 'Registre quem quer ser atendido antes. Quando uma consulta é cancelada ou remarcada, a equipe recebe um aviso com os pacientes que combinam com o horário e encaixa em poucos cliques.',
                'link' => '', 'novo' => true,
            ),
            array(
                'id' => 'prontuario', 'grupo' => 'prontuario', 'icone' => '📋', 'titulo' => 'Prontuário eletrônico',
                'resumo' => 'Histórico de atendimentos, evolução clínica e arquivos em um só lugar.',
                'descricao' => 'Registro de cada atendimento com histórico organizado, exames e documentos anexados, acessível de qualquer dispositivo.',
                'link' => 'sistema-prontuario-eletronico', 'novo' => false,
            ),
            array(
                'id' => 'prontuario_especialidade', 'grupo' => 'prontuario', 'icone' => '🩺', 'titulo' => 'Prontuário por especialidade',
                'resumo' => 'Títulos e campos adaptados a fisioterapia, psicologia, odontologia e outras áreas.',
                'descricao' => 'Os campos do atendimento mudam conforme a especialidade do profissional — por exemplo, escala de dor na fisioterapia ou dente tratado na odontologia.',
                'link' => 'sistema-prontuario-eletronico', 'novo' => false,
            ),
            array(
                'id' => 'ficha_paciente', 'grupo' => 'prontuario', 'icone' => '🗂️', 'titulo' => 'Ficha do paciente',
                'resumo' => 'Nome social, responsável, convênio e dados de saúde, com alergias em destaque.',
                'descricao' => 'Dados pessoais, responsável legal, contato de emergência, convênio e saúde básica (tipo sanguíneo, alergias, medicamentos e comorbidades). As alergias aparecem em destaque no prontuário.',
                'link' => '', 'novo' => true,
            ),
            array(
                'id' => 'exames', 'grupo' => 'prontuario', 'icone' => '🔬', 'titulo' => 'Exames e arquivos',
                'resumo' => 'Solicite exames, acompanhe o retorno e anexe documentos ao paciente.',
                'descricao' => 'Checklist de exames por atendimento e arquivos do paciente (laudos, imagens, documentos) guardados no prontuário.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'exportar_prontuario', 'grupo' => 'prontuario', 'icone' => '📤', 'titulo' => 'Exportar prontuário',
                'resumo' => 'PDF, Excel ou CSV por paciente, com período e registro de cada exportação.',
                'descricao' => 'Gere o prontuário de um paciente em PDF, Excel ou CSV, do histórico completo ou de um período. Cada exportação fica registrada (quem, quando, qual paciente), para auditoria, em linha com a LGPD.',
                'link' => '', 'novo' => true,
            ),
            array(
                'id' => 'whatsapp_confirmacao', 'grupo' => 'whatsapp', 'icone' => '✅', 'titulo' => 'Confirmação de consulta pelo WhatsApp',
                'resumo' => 'O paciente confirma ou cancela pelo botão e a agenda se atualiza sozinha.',
                'descricao' => 'Ao agendar, o paciente recebe a mensagem com botões de confirmar e cancelar. A resposta aparece na agenda e a equipe é avisada. Envio pela API oficial da Meta.',
                'link' => 'confirmacao-de-consulta-por-whatsapp', 'novo' => false,
            ),
            array(
                'id' => 'whatsapp_lembrete', 'grupo' => 'whatsapp', 'icone' => '⏰', 'titulo' => 'Lembrete automático',
                'resumo' => 'Lembrete pelo WhatsApp horas antes da consulta, sem ninguém disparar.',
                'descricao' => 'O sistema envia o lembrete ao paciente antes da consulta, pulando quem já confirmou.',
                'link' => 'confirmacao-de-consulta-por-whatsapp', 'novo' => false,
            ),
            array(
                'id' => 'chatbot', 'grupo' => 'whatsapp', 'icone' => '💬', 'titulo' => 'Chatbot para paciente e profissional',
                'resumo' => 'O paciente remarca ou cancela sozinho; o profissional consulta a agenda pelo WhatsApp.',
                'descricao' => 'Pelo número cadastrado, o paciente vê as próximas consultas e, com 24 horas ou mais de antecedência, remarca para um horário livre ou cancela. Profissional e recepção consultam a agenda de hoje e de amanhã.',
                'link' => 'chatbot-para-clinicas', 'novo' => false,
            ),
            array(
                'id' => 'rotulos', 'grupo' => 'gestao', 'icone' => '🏷️', 'titulo' => 'Rótulos e alertas de pacientes',
                'resumo' => 'Etiquetas coloridas (VIP, Convênio, Gestante, Alérgico) com filtro na lista.',
                'descricao' => 'Cada clínica cria seus rótulos. Os de alerta aparecem em destaque no prontuário e na agenda do dia.',
                'link' => '', 'novo' => true,
            ),
            array(
                'id' => 'avisos', 'grupo' => 'gestao', 'icone' => '🔔', 'titulo' => 'Avisos internos',
                'resumo' => 'O sino avisa quando o paciente confirma, cancela ou remarca.',
                'descricao' => 'A equipe recebe avisos no sistema sobre as respostas dos pacientes, sem precisar conferir mensagem por mensagem.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'relatorios', 'grupo' => 'gestao', 'icone' => '📊', 'titulo' => 'Relatórios clínicos',
                'resumo' => 'Atendimentos, exames e tempos médios por período e por profissional.',
                'descricao' => 'Indicadores de atendimentos finalizados e pendentes, exames, espera média, atraso e duração das consultas.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'equipe', 'grupo' => 'gestao', 'icone' => '👥', 'titulo' => 'Equipe com acesso por perfil',
                'resumo' => 'Clínica, profissionais e recepção, cada um vendo só o que precisa.',
                'descricao' => 'Cadastre profissionais e colaboradores; o acesso chega por e-mail (com a senha ou um convite para criar a senha). Cada perfil enxerga apenas a sua parte da operação.',
                'link' => '', 'novo' => false,
            ),
            array(
                'id' => 'manual', 'grupo' => 'gestao', 'icone' => '📘', 'titulo' => 'Manual de ajuda',
                'resumo' => 'Guia por perfil, na tela e em PDF.',
                'descricao' => 'Manual com o passo a passo de cada perfil (clínica, profissional e recepção), atualizado a cada novidade.',
                'link' => '', 'novo' => false,
            ),
        );
    }
}
