<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';
require __DIR__ . '/../application/libraries/Whatsapp_chatbot.php';

function assertChatbotSame($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

class WhatsappChatbotFakeModel
{
    public $perfil = [];
    public $agendamentos = [];
    public $sessoes = [];
    public $eventosFinalizados = [];
    public $proximoEvento = 10;

    public function iniciar_evento_chatbot($messageId, $telefone, $tipo, $entrada)
    {
        return ['status' => 'processavel', 'id_evento' => $this->proximoEvento++, 'token_processamento' => 'token'];
    }

    public function finalizar_evento_chatbot($idEvento, $resultado, $idSessao, $idUsuario, $idAgendamento, $token)
    {
        $this->eventosFinalizados[] = compact('idEvento', 'resultado', 'idSessao', 'idUsuario', 'idAgendamento', 'token');
        return true;
    }

    public function resolver_perfil_chatbot($telefone)
    {
        return $this->perfil ?: ['telefone' => $telefone, 'perfil' => '', 'id_usuario' => 0, 'tenant_id' => 0];
    }

    public function obter_sessao_chatbot($telefone)
    {
        return isset($this->sessoes[$telefone]) ? $this->sessoes[$telefone] : null;
    }

    public function salvar_sessao_chatbot($telefone, $perfil, $idUsuario, $tenantId, $fluxo, $etapa, $dados, $origemEm, $origemEvento)
    {
        $this->sessoes[$telefone] = (object)[
            'id' => 44,
            'perfil' => $perfil,
            'id_usuario' => $idUsuario,
            'tenant_id' => $tenantId,
            'fluxo' => $fluxo,
            'etapa' => $etapa,
            'dados_json' => json_encode($dados),
        ];
        return true;
    }

    public function limpar_sessao_chatbot($telefone)
    {
        unset($this->sessoes[$telefone]);
        return true;
    }

    public function listar_agendamentos_chatbot($perfil, $idUsuario, $tenantId)
    {
        return $this->agendamentos;
    }

    public function obter_agendamento_chatbot($idAgendamento, $perfil, $idUsuario, $tenantId)
    {
        foreach ($this->agendamentos as $agendamento) {
            if ((int)$agendamento->id === (int)$idAgendamento) {
                return $agendamento;
            }
        }
        return null;
    }

    public function obter_plano_chatbot($perfil, $idUsuario, $tenantId)
    {
        return (object)['modelo' => 'Clinica', 'status' => 'active', 'data' => '2026-10-01'];
    }
}

class WhatsappChatbotFakeEnvio
{
    public $payloads = [];

    public function enviar_chatbot($telefone, $payload)
    {
        $this->payloads[] = ['telefone' => $telefone, 'payload' => $payload];
        return ['sent' => true, 'reason' => 'sent', 'wamid' => 'wamid.chatbot'];
    }
}

class WhatsappChatbotFakeNotificacoes
{
    public $solicitacoes = [];

    public function criar_solicitacao_chatbot($contexto, $acao, $motivo, $idEvento)
    {
        $this->solicitacoes[] = compact('contexto', 'acao', 'motivo', 'idEvento');
        return true;
    }
}

function novoChatbotDeTeste($perfil, $agendamentos = [])
{
    $modelo = new WhatsappChatbotFakeModel();
    $modelo->perfil = $perfil;
    $modelo->agendamentos = $agendamentos;
    $envio = new WhatsappChatbotFakeEnvio();
    $notificacoes = new WhatsappChatbotFakeNotificacoes();
    $ci = (object)[
        'whatsapp_model' => $modelo,
        'whatsapp_agendamento' => $envio,
        'notificacoes_model' => $notificacoes,
    ];

    return [new Whatsapp_chatbot($ci), $modelo, $envio, $notificacoes];
}

assertChatbotSame('✅ confirmado', utec_whatsapp_status_chatbot((object)['status' => 0, 'status_whatsapp' => 'enviado/confirmado']), 'Status confirmado deve usar rotulo seguro.');
assertChatbotSame('⏳ pendente', utec_whatsapp_status_chatbot((object)['status' => 0]), 'Status pendente deve usar rotulo seguro.');
assertChatbotSame('❌ cancelado', utec_whatsapp_status_chatbot((object)['status' => 3]), 'Status cancelado deve usar rotulo seguro.');
assertChatbotSame('https://wa.me/5581983276882', utec_whatsapp_chatbot_url_suporte(), 'Suporte deve apontar para o WhatsApp do dev.');
assertChatbotSame('Fale com o dev.', utec_whatsapp_chatbot_texto_suporte(), 'Suporte deve usar o texto aprovado.');
assertChatbotSame('whatsapp_chatbot_remarcacao', utec_notificacoes_tipo_solicitacao_chatbot('remarcacao'), 'Solicitacao de remarcacao deve ser aceita.');
assertChatbotSame('', utec_notificacoes_tipo_solicitacao_chatbot('confirmar'), 'Acoes fora do escopo nao devem criar solicitacao.');

$hojeTeste = date('Y-m-d');
$amanhaTeste = date('Y-m-d', strtotime('+1 day'));
$agenda = [];
for ($indice = 0; $indice < 11; $indice++) {
    $agenda[] = (object)[
        'id' => $indice + 1,
        'data_agenda' => $hojeTeste,
        'hora_agenda' => sprintf('%02d:00:00', 8 + $indice),
        'paciente_nome' => 'Paciente '.$indice,
        'prestador_nome' => 'Dra. Ana',
        'status' => 0,
        'status_whatsapp' => '',
    ];
}
list($chatbot, $modelo, $envio, $notificacoes) = novoChatbotDeTeste([
    'telefone' => '5581999999999',
    'perfil' => 'paciente',
    'id_usuario' => 7,
    'tenant_id' => 2,
], $agenda);

$resultadoAgenda = $chatbot->processar([
    'message_id' => 'wamid.chat.1',
    'from' => '5581999999999',
    'message_type' => 'interactive',
    'payload' => 'chat:paciente:proximas',
    'text' => '',
    'event_at' => '2026-09-02 10:00:00',
]);
assertChatbotSame(true, $resultadoAgenda['processado'], 'Comando fechado de paciente deve ser processado.');
assertChatbotSame('wamid.chatbot', $resultadoAgenda['wamid'], 'Resultado do chatbot deve manter o identificador da mensagem enviada.');
assertChatbotSame('encontrado', $resultadoAgenda['perfil_status'], 'Resultado do chatbot deve informar que o perfil foi localizado.');
assertChatbotSame(1, count($envio->payloads), 'Chatbot deve enviar uma resposta pelo dispatcher de agendamento.');
$textoAgenda = $envio->payloads[0]['payload']['text']['body'];
assertChatbotSame(true, strpos($textoAgenda, '08:00 - Dra. Ana - ⏳ pendente') !== false, 'Agenda do paciente deve expor somente horario, profissional e status.');
assertChatbotSame(false, strpos($textoAgenda, 'Paciente 0') !== false, 'Agenda do paciente nao deve expor outro dado sensivel.');
assertChatbotSame(10, substr_count($textoAgenda, '⏳ pendente'), 'Agenda deve limitar a dez linhas.');
assertChatbotSame(1, count($modelo->eventosFinalizados), 'Evento processado deve ser finalizado.');

list($chatbot, $modelo, $envio, $notificacoes) = novoChatbotDeTeste([
    'telefone' => '5581999999999',
    'perfil' => 'paciente',
    'id_usuario' => 7,
    'tenant_id' => 2,
], [(object)[
    'id' => 71,
    'data_agenda' => $amanhaTeste,
    'hora_agenda' => '14:30:00',
    'paciente_nome' => 'Maria',
    'prestador_nome' => 'Dra. Ana',
    'id_user' => 9,
    'id_prestador' => 3,
    'status' => 0,
    'status_whatsapp' => '',
]]);

$chatbot->processar([
    'message_id' => 'wamid.chat.2',
    'from' => '5581999999999',
    'message_type' => 'interactive',
    'payload' => 'chat:paciente:cancelar:71',
    'text' => '',
    'event_at' => '2026-09-02 10:01:00',
]);
assertChatbotSame('motivo', $modelo->sessoes['5581999999999']->etapa, 'Cancelar consulta valida deve abrir sessao de motivo.');

$chatbot->processar([
    'message_id' => 'wamid.chat.3',
    'from' => '5581999999999',
    'message_type' => 'text',
    'payload' => '',
    'text' => 'na',
    'event_at' => '2026-09-02 10:02:00',
]);
assertChatbotSame(0, count($notificacoes->solicitacoes), 'Motivo menor que tres caracteres nao pode criar solicitacao.');
assertChatbotSame(true, isset($modelo->sessoes['5581999999999']), 'Motivo invalido deve manter a sessao aberta.');

$chatbot->processar([
    'message_id' => 'wamid.chat.4',
    'from' => '5581999999999',
    'message_type' => 'interactive',
    'payload' => 'chat:paciente:proximas',
    'text' => '',
    'event_at' => '2026-09-02 10:03:00',
]);
assertChatbotSame(true, isset($modelo->sessoes['5581999999999']), 'Um menu nao deve reiniciar sessao de texto aberta.');

$chatbot->processar([
    'message_id' => 'wamid.chat.5',
    'from' => '5581999999999',
    'message_type' => 'text',
    'payload' => '',
    'text' => 'Preciso cancelar por viagem.',
    'event_at' => '2026-09-02 10:04:00',
]);
assertChatbotSame(1, count($notificacoes->solicitacoes), 'Motivo valido deve criar solicitacao interna.');
assertChatbotSame('cancelamento', $notificacoes->solicitacoes[0]['acao'], 'Cancelar deve criar tipo de solicitacao correto.');
assertChatbotSame(9, $notificacoes->solicitacoes[0]['contexto']['id_user'], 'Solicitacao deve incluir o criador do agendamento.');
assertChatbotSame(3, $notificacoes->solicitacoes[0]['contexto']['id_prestador'], 'Solicitacao deve incluir o prestador do agendamento.');
assertChatbotSame(false, isset($modelo->sessoes['5581999999999']), 'Solicitacao concluida deve encerrar a sessao.');

list($chatbot, $modelo, $envio) = novoChatbotDeTeste([
    'telefone' => '5581988887777',
    'perfil' => 'atendente',
    'id_usuario' => 8,
    'tenant_id' => 2,
]);
$resultadoPlano = $chatbot->processar([
    'message_id' => 'wamid.chat.6',
    'from' => '5581988887777',
    'message_type' => 'interactive',
    'payload' => 'chat:atendente:plano',
    'text' => '',
    'event_at' => '2026-09-02 10:05:00',
]);
assertChatbotSame(true, $resultadoPlano['processado'], 'Comando indisponivel deve devolver o menu permitido ao atendente.');
$menuAtendente = $envio->payloads[0]['payload']['interactive']['action']['sections'][0]['rows'];
assertChatbotSame(false, in_array('chat:atendente:plano', array_column($menuAtendente, 'id'), true), 'Atendente nao deve receber comando de plano.');

list($chatbot, $modelo, $envio) = novoChatbotDeTeste([], []);
$resultadoNaoCadastrado = $chatbot->processar([
    'message_id' => 'wamid.chat.7',
    'from' => '5581977776666',
    'message_type' => 'text',
    'payload' => '',
    'text' => 'Oi',
    'event_at' => '2026-09-02 10:06:00',
]);
assertChatbotSame(true, $resultadoNaoCadastrado['processado'], 'Remetente nao cadastrado deve receber orientacao.');
assertChatbotSame(true, strpos($envio->payloads[0]['payload']['text']['body'], 'https://utecnologia.com.br/') !== false, 'Remetente nao cadastrado deve receber link base do sistema.');

$codigoAgendamento = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_agendamento.php');
assertChatbotSame(true, strpos($codigoAgendamento, 'function enviar_chatbot(') !== false, 'Dispatcher deve expor envio exclusivo do chatbot.');
$inicioEnviarChatbot = strpos($codigoAgendamento, 'function enviar_chatbot(');
$fimEnviarChatbot = strpos($codigoAgendamento, 'protected function get_subscription_status_by_tenant', $inicioEnviarChatbot);
$corpoEnviarChatbot = substr($codigoAgendamento, $inicioEnviarChatbot, $fimEnviarChatbot - $inicioEnviarChatbot);
assertChatbotSame(false, strpos($corpoEnviarChatbot, 'validar_quota_tenant') !== false, 'Envio do chatbot nao deve aplicar quota.');

$codigoNotificacoes = file_get_contents(__DIR__ . '/../application/models/Notificacoes_model.php');
assertChatbotSame(true, strpos($codigoNotificacoes, 'function criar_solicitacao_chatbot(') !== false, 'Modelo deve registrar solicitacoes do chatbot.');
assertChatbotSame(true, strpos($codigoNotificacoes, 'id_whatsapp_chatbot_evento') !== false, 'Solicitacoes devem deduplicar pelo evento do chatbot.');
assertChatbotSame(true, strpos($codigoNotificacoes, 'utec_notificacoes_tipo_solicitacao_chatbot') !== false, 'Modelo deve validar somente os tipos fechados de solicitacao.');

$codigoWhatsappModel = file_get_contents(__DIR__ . '/../application/models/Whatsapp_model.php');
$inicioListarAgendamentos = strpos($codigoWhatsappModel, 'function listar_agendamentos_chatbot(');
$fimListarAgendamentos = strpos($codigoWhatsappModel, 'function obter_agendamento_chatbot(', $inicioListarAgendamentos);
$corpoListarAgendamentos = substr($codigoWhatsappModel, $inicioListarAgendamentos, $fimListarAgendamentos - $inicioListarAgendamentos);
assertChatbotSame(true, strpos($corpoListarAgendamentos, 'SELECT a.id, a.id_paciente, a.id_prestador, a.id_user,') !== false, 'Lista de agendamentos deve selecionar o criador para o contexto de notificacao.');

$inicioObterAgendamento = $fimListarAgendamentos;
$fimObterAgendamento = strpos($codigoWhatsappModel, 'function obter_plano_chatbot(', $inicioObterAgendamento);
$corpoObterAgendamento = substr($codigoWhatsappModel, $inicioObterAgendamento, $fimObterAgendamento - $inicioObterAgendamento);
assertChatbotSame(true, strpos($corpoObterAgendamento, 'SELECT a.id, a.id_paciente, a.id_prestador, a.id_user,') !== false, 'Detalhe do agendamento deve selecionar o criador para o contexto de notificacao.');

$codigoChatbot = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_chatbot.php');
assertChatbotSame(true, strpos($codigoChatbot, "'admin' => ['agenda', 'pendencias', 'cancelamentos'") !== false, 'Menu admin deve expor o comando Pendencias com grafia correta.');

echo "OK\n";
