<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';
require __DIR__ . '/../application/libraries/Whatsapp_chatbot.php';
require __DIR__ . '/../application/libraries/Whatsapp_chatbot_agenda.php';

function assertLib($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

class AgendaFakeModel
{
    public $agendamentos = [];
    public $sessoes = [];
    public $remarcacoes = [];
    public $cancelamentos = [];
    public $resultadoRemarcar = null;
    public $resultadoCancelar = null;
    public $perfil = [];
    public $proximoEvento = 100;

    public function iniciar_evento_chatbot($m, $t, $tipo, $e) { return ['status' => 'processavel', 'id_evento' => $this->proximoEvento++, 'token_processamento' => 't']; }
    public function finalizar_evento_chatbot($a, $b, $c, $d, $e, $f) { return true; }
    public function resolver_perfil_chatbot($telefone) { return $this->perfil; }
    public function obter_sessao_chatbot($telefone) { return isset($this->sessoes[$telefone]) ? $this->sessoes[$telefone] : null; }
    public function salvar_sessao_chatbot($telefone, $perfil, $idUsuario, $tenantId, $fluxo, $etapa, $dados, $origemEm, $origemEvento)
    {
        $this->sessoes[$telefone] = (object)['id' => 55, 'fluxo' => $fluxo, 'etapa' => $etapa, 'dados_json' => json_encode($dados)];
        return true;
    }
    public function limpar_sessao_chatbot($telefone) { unset($this->sessoes[$telefone]); return true; }
    public function listar_agendamentos_chatbot($perfil, $idUsuario, $tenantId) { return array_values($this->agendamentos); }
    public function obter_agendamento_chatbot($id, $perfil, $idUsuario, $tenantId) { return isset($this->agendamentos[$id]) ? $this->agendamentos[$id] : null; }
    public function remarcar_agendamento_chatbot($id, $idPaciente, $data, $hora, $minimo, $telefone)
    {
        $this->remarcacoes[] = compact('id', 'idPaciente', 'data', 'hora', 'minimo', 'telefone');
        if ($this->resultadoRemarcar !== null) { return $this->resultadoRemarcar; }
        $anterior = $this->agendamentos[$id];
        $novo = clone $anterior; $novo->data_agenda = $data; $novo->hora_agenda = $hora;
        return ['ok' => true, 'ja_estava' => false, 'motivo_falha' => '', 'agendamento' => $novo, 'anterior' => $anterior, 'id_log' => 900];
    }
    public function cancelar_agendamento_chatbot($id, $idPaciente, $minimo, $telefone)
    {
        $this->cancelamentos[] = compact('id', 'idPaciente', 'minimo', 'telefone');
        if ($this->resultadoCancelar !== null) { return $this->resultadoCancelar; }
        $anterior = $this->agendamentos[$id];
        $novo = clone $anterior; $novo->status = 3;
        return ['ok' => true, 'ja_estava' => false, 'motivo_falha' => '', 'agendamento' => $novo, 'anterior' => $anterior, 'id_log' => 901];
    }
}

class AgendaFakeDisponibilidade
{
    public $temGrade = true;
    public $dias = [];
    public $livres = [];
    public $chamadas = [];
    public function dias_com_vaga($idPrestador, $minimo, $dias = 30, $limite = 10, $ignorar = 0)
    {
        $this->chamadas[] = ['dias_com_vaga', $idPrestador, $minimo, $ignorar];
        return ['tem_grade' => $this->temGrade, 'duracao' => 30, 'dias' => $this->dias];
    }
    public function horarios_livres($idPrestador, $data, $ignorar = 0, $minimo = null)
    {
        $this->chamadas[] = ['horarios_livres', $idPrestador, $data, $ignorar, $minimo];
        return ['tem_grade' => $this->temGrade, 'duracao' => 30, 'livres' => isset($this->livres[$data]) ? $this->livres[$data] : []];
    }
}

class AgendaFakeEnvio
{
    public $payloads = [];
    public $equipe = [];
    public function enviar_chatbot($telefone, $payload) { $this->payloads[] = $payload; return ['sent' => true, 'reason' => 'sent', 'wamid' => 'wamid.x']; }
    public function notificar_equipe($contexto, $acao) { $this->equipe[] = compact('contexto', 'acao'); return ['enviados' => 1, 'falhas' => 0, 'detalhes' => []]; }
}

class AgendaFakeNotificacoes
{
    public $avisos = [];
    public $solicitacoes = [];
    public function criar_aviso_chatbot_agenda($contexto, $acao, $dados, $idEvento) { $this->avisos[] = compact('contexto', 'acao', 'dados', 'idEvento'); return true; }
    public function criar_solicitacao_chatbot($contexto, $acao, $motivo, $idEvento) { $this->solicitacoes[] = compact('contexto', 'acao', 'motivo', 'idEvento'); return true; }
}

function novoCenario()
{
    $modelo = new AgendaFakeModel();
    $modelo->perfil = ['telefone' => '5581999999999', 'perfil' => 'paciente', 'id_usuario' => 7, 'tenant_id' => 2];
    $modelo->agendamentos[812] = (object)[
        'id' => 812, 'id_paciente' => 7, 'id_prestador' => 3, 'id_user' => 9,
        'data_agenda' => '2026-09-25', 'hora_agenda' => '14:00:00', 'tipo' => 'Consulta', 'status' => 0,
        'paciente_nome' => 'Maria', 'prestador_nome' => 'Dra. Ana', 'status_whatsapp' => '', 'tenant_id' => 2,
    ];
    $disp = new AgendaFakeDisponibilidade();
    $envio = new AgendaFakeEnvio();
    $notif = new AgendaFakeNotificacoes();
    $ci = (object)['whatsapp_model' => $modelo, 'disponibilidade_model' => $disp, 'whatsapp_agendamento' => $envio, 'notificacoes_model' => $notif];
    $agenda = new Whatsapp_chatbot_agenda($ci);
    $agenda->agora = strtotime('2026-09-22 10:00:00');
    $ci->whatsapp_chatbot_agenda = $agenda;
    $chatbot = new Whatsapp_chatbot($ci);
    return [$chatbot, $agenda, $modelo, $disp, $envio, $notif];
}

function eventoClique($payload, $n) { return ['message_id' => 'wamid.in.'.$n, 'from' => '5581999999999', 'message_type' => 'interactive', 'payload' => $payload, 'text' => '', 'event_at' => '2026-09-22 10:00:00']; }
function eventoTexto($texto, $n) { return ['message_id' => 'wamid.in.'.$n, 'from' => '5581999999999', 'message_type' => 'text', 'payload' => '', 'text' => $texto, 'event_at' => '2026-09-22 10:00:00']; }
function ultimo($envio) { return $envio->payloads[count($envio->payloads) - 1]; }
function linhasLista($payload) { return $payload['interactive']['action']['sections'][0]['rows']; }
function idsBotoes($payload) { $ids = []; foreach ($payload['interactive']['action']['buttons'] as $b) { $ids[] = $b['reply']['id']; } return $ids; }

// 1) Remarcar: menu -> escolha da consulta -> lista de dias
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$disp->dias = [['data' => '2026-09-24', 'qtd' => 3], ['data' => '2026-09-25', 'qtd' => 1]];
$chatbot->processar(eventoClique('chat:paciente:remarcar:812', 1));
$rows = linhasLista(ultimo($envio));
assertLib('rem:812:d:20260924', $rows[0]['id'], 'lista de dias usa id rem:d');
assertLib('Qui 24/09', $rows[0]['title'], 'rotulo do dia');
assertLib('3 horários livres', $rows[0]['description'], 'descricao plural');
assertLib('1 horário livre', $rows[1]['description'], 'descricao singular');
assertLib(['dias_com_vaga', 3, '2026-09-23 10:00', 812], $disp->chamadas[0], 'dias_com_vaga com minimo de 24h ignorando a propria consulta');
assertLib('agenda_remarcar', $modelo->sessoes['5581999999999']->fluxo, 'sessao de remarcacao');
assertLib('dia', $modelo->sessoes['5581999999999']->etapa, 'etapa dia');

// 2) Dia -> horarios com paginacao (11 livres => 9 + ver mais)
$disp->livres['2026-09-24'] = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '14:00', '14:30'];
$chatbot->processar(eventoClique('rem:812:d:20260924', 2));
$rows = linhasLista(ultimo($envio));
assertLib(10, count($rows), '9 horarios + ver mais');
assertLib('rem:812:h:202609240800', $rows[0]['id'], 'id do horario');
assertLib('rem:812:p:20260924:2', $rows[9]['id'], 'id da pagina seguinte');
assertLib('Ver mais horários', $rows[9]['title'], 'titulo ver mais');
$ultimaChamada = $disp->chamadas[count($disp->chamadas) - 1];
assertLib(['horarios_livres', 3, '2026-09-24', 812, '2026-09-23 10:00'], $ultimaChamada, 'horarios_livres com minimo');

// 3) Pagina 2
$chatbot->processar(eventoClique('rem:812:p:20260924:2', 3));
$rows = linhasLista(ultimo($envio));
assertLib(2, count($rows), 'pagina 2 com o restante');

// 4) Horario -> botoes de confirmacao
$chatbot->processar(eventoClique('rem:812:h:202609241430', 4));
$p = ultimo($envio);
assertLib(['rem:812:ok:202609241430', 'rem:812:dias', 'chat:paciente:voltar'], idsBotoes($p), 'botoes confirmar / outro dia / voltar');
assertLib('Remarcar para Qui 24/09 às 14:30 com Dra. Ana?', $p['interactive']['body']['text'], 'texto de confirmacao');
assertLib('confirmar', $modelo->sessoes['5581999999999']->etapa, 'etapa confirmar');

// 5) Confirmar -> grava, avisa, limpa sessao
$chatbot->processar(eventoClique('rem:812:ok:202609241430', 5));
assertLib(['id' => 812, 'idPaciente' => 7, 'data' => '2026-09-24', 'hora' => '14:30', 'minimo' => '2026-09-23 10:00', 'telefone' => '5581999999999'], $modelo->remarcacoes[0], 'model recebe a remarcacao');
assertLib('Consulta remarcada para Qui 24/09 às 14:30 com Dra. Ana.', ultimo($envio)['text']['body'], 'texto de sucesso');
assertLib('remarcar', $notif->avisos[0]['acao'], 'aviso interno de remarcacao');
assertLib('2026-09-25', $notif->avisos[0]['dados']['data_anterior'], 'aviso leva data anterior');
assertLib('2026-09-24', $notif->avisos[0]['dados']['data_nova'], 'aviso leva data nova');
assertLib(900, $notif->avisos[0]['contexto']['id_whatsapp_notificacao'], 'aviso usa id do log');
assertLib('remarcar', $envio->equipe[0]['acao'], 'notificar_equipe chamado com remarcar');
assertLib('14:00', $envio->equipe[0]['contexto']['hora_anterior'], 'contexto da equipe leva hora anterior');
assertLib(false, isset($modelo->sessoes['5581999999999']), 'sessao limpa apos remarcar');

// 6) Confirmar com horario ocupado -> reenvia horarios com aviso
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$disp->livres['2026-09-24'] = ['08:00', '09:00'];
$modelo->resultadoRemarcar = ['ok' => false, 'ja_estava' => false, 'motivo_falha' => 'ocupado', 'agendamento' => null, 'anterior' => null, 'id_log' => 0];
$chatbot->processar(eventoClique('rem:812:ok:202609241430', 6));
$p = ultimo($envio);
assertLib(true, strpos($p['interactive']['body']['text'], 'acabou de ser ocupado') !== false, 'aviso de horario ocupado');
assertLib('rem:812:h:202609240800', linhasLista($p)[0]['id'], 'reenvia a lista de horarios');
assertLib(0, count($notif->avisos), 'sem aviso quando falha');

// 7) Confirmar duas vezes -> "ja esta marcada", sem avisos
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$modelo->resultadoRemarcar = ['ok' => true, 'ja_estava' => true, 'motivo_falha' => '', 'agendamento' => $modelo->agendamentos[812], 'anterior' => $modelo->agendamentos[812], 'id_log' => 0];
$chatbot->processar(eventoClique('rem:812:ok:202609251400', 7));
assertLib('Sua consulta já está marcada para Sex 25/09 às 14:00 com Dra. Ana.', ultimo($envio)['text']['body'], 'idempotente');
assertLib(0, count($notif->avisos), 'sem aviso duplicado');
assertLib(0, count($envio->equipe), 'sem WhatsApp duplicado');

// 8) Consulta a menos de 24h -> fluxo antigo de solicitacao
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$modelo->agendamentos[812]->data_agenda = '2026-09-22';
$modelo->agendamentos[812]->hora_agenda = '18:00:00';
$chatbot->processar(eventoClique('chat:paciente:remarcar:812', 8));
assertLib('Faltam menos de 24 horas para a consulta, então a equipe vai analisar seu pedido. Informe o motivo da solicitacao com pelo menos 3 caracteres.', ultimo($envio)['text']['body'], 'fallback de prazo');
assertLib('solicitacao', $modelo->sessoes['5581999999999']->fluxo, 'sessao antiga de solicitacao');
assertLib(0, count($disp->chamadas), 'nao consulta disponibilidade');

// 9) Profissional sem grade -> fallback
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$disp->temGrade = false;
$chatbot->processar(eventoClique('chat:paciente:remarcar:812', 9));
assertLib(true, strpos(ultimo($envio)['text']['body'], 'ainda não tem horários') !== false, 'fallback sem grade');
assertLib('solicitacao', $modelo->sessoes['5581999999999']->fluxo, 'sem grade vira solicitacao');

// 10) Sem vagas em 30 dias -> fallback
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$disp->dias = [];
$chatbot->processar(eventoClique('chat:paciente:remarcar:812', 10));
assertLib(true, strpos(ultimo($envio)['text']['body'], '30 dias') !== false, 'fallback sem vaga');

// 11) Cancelar: motivo curto, motivo valido, confirmacao
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$chatbot->processar(eventoClique('chat:paciente:cancelar:812', 11));
assertLib(['can:812:sem_motivo', 'chat:paciente:voltar'], idsBotoes(ultimo($envio)), 'botoes do motivo');
assertLib('agenda_cancelar', $modelo->sessoes['5581999999999']->fluxo, 'sessao de cancelamento');
$chatbot->processar(eventoTexto('ab', 12));
assertLib(['can:812:sem_motivo', 'chat:paciente:voltar'], idsBotoes(ultimo($envio)), 'motivo curto pede de novo');
$chatbot->processar(eventoTexto('Viagem', 13));
$p = ultimo($envio);
assertLib(['can:812:ok', 'chat:paciente:voltar'], idsBotoes($p), 'botoes de confirmar cancelamento');
assertLib('Cancelar a consulta de Sex 25/09 às 14:00 com Dra. Ana?', $p['interactive']['body']['text'], 'texto confirmar cancelamento');
$chatbot->processar(eventoClique('can:812:ok', 14));
assertLib(812, $modelo->cancelamentos[0]['id'], 'model recebe o cancelamento');
assertLib('2026-09-23 10:00', $modelo->cancelamentos[0]['minimo'], 'cancelamento com minimo');
assertLib('Consulta cancelada. Se quiser remarcar depois, é só chamar aqui.', ultimo($envio)['text']['body'], 'texto cancelado');
assertLib('Viagem', $notif->avisos[0]['dados']['motivo'], 'aviso leva o motivo');
assertLib('cancelar', $envio->equipe[0]['acao'], 'equipe avisada do cancelamento');
assertLib(false, isset($modelo->sessoes['5581999999999']), 'sessao limpa apos cancelar');

// 12) Cancelar sem motivo
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$chatbot->processar(eventoClique('chat:paciente:cancelar:812', 15));
$chatbot->processar(eventoClique('can:812:sem_motivo', 16));
assertLib(['can:812:ok', 'chat:paciente:voltar'], idsBotoes(ultimo($envio)), 'sem motivo vai direto para confirmar');
$chatbot->processar(eventoClique('can:812:ok', 17));
assertLib('', $notif->avisos[0]['dados']['motivo'], 'aviso sem motivo');

// 13) Consulta ja cancelada
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$modelo->agendamentos[812]->status = 3;
$chatbot->processar(eventoClique('can:812:ok', 18));
assertLib('Essa consulta já está cancelada.', ultimo($envio)['text']['body'], 'cancelamento repetido');
assertLib(0, count($modelo->cancelamentos), 'nao grava de novo');

// 14) Clique de consulta inexistente -> expirado + menu
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$chatbot->processar(eventoClique('rem:999:d:20260924', 19));
$penultimo = $envio->payloads[count($envio->payloads) - 2];
assertLib('Essa opção expirou. Escolha novamente no menu.', $penultimo['text']['body'], 'expirado');
assertLib('list', ultimo($envio)['interactive']['type'], 'menu reenviado');

// 15) Voltar durante o fluxo limpa a sessao
list($chatbot, $agenda, $modelo, $disp, $envio, $notif) = novoCenario();
$chatbot->processar(eventoClique('chat:paciente:cancelar:812', 20));
$chatbot->processar(eventoClique('chat:paciente:voltar', 21));
assertLib(false, isset($modelo->sessoes['5581999999999']), 'voltar limpa a sessao');

echo "OK whatsapp_chatbot_agenda_library_test\n";
