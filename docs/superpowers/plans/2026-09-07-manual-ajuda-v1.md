# Manual de Ajuda ao Usuário v1 — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Substituir o manual de suporte atual (5 blocos de texto genérico por
nível, gerado em TCPDF) por um manual em capítulos reaproveitáveis — cobrindo
WhatsApp, SaaS, prontuário por especialidade e avisos internos — com
screenshots reais e PDF gerado em mPDF (capa, sumário, cabeçalho/rodapé).

**Architecture:** Um novo `Manual_conteudo` (library CI, sem tabela no banco)
guarda os 12 capítulos e resolve tópicos/print por nível. O controller
`adm/Usuarios.php` chama essa library uma única vez e alimenta tanto a tela
(`manual_funcao.php`) quanto o PDF (`manual_pdf_capa.php` +
`manual_pdf_conteudo.php`, via mPDF). Nenhuma migração de banco é necessária.

**Tech Stack:** PHP7 + CodeIgniter 3.1.10, mPDF v6 (`application/third_party/mpdf`,
já vendorizado), TCPDF removido do fluxo do manual, screenshots capturados via
Claude in Chrome contra o ambiente local (WAMP).

Spec de referência: `docs/superpowers/specs/2026-09-04-manual-ajuda-v1-design.md`

---

## Mapa de arquivos

| Arquivo | Ação |
|---|---|
| `application/libraries/Manual_conteudo.php` | Criar |
| `tests/manual_conteudo_test.php` | Criar |
| `application/libraries/M_pdf.php` | Modificar (corrigir default do construtor) |
| `application/controllers/adm/Usuarios.php` | Modificar (`manual()`, `manual_pdf()`, remove `build_manual_context()`) |
| `application/views/adm/usuarios/manual_funcao.php` | Modificar (reescrita) |
| `application/views/adm/usuarios/manual_pdf_capa.php` | Criar |
| `application/views/adm/usuarios/manual_pdf_conteudo.php` | Criar |
| `application/views/adm/usuarios/manual_funcao_pdf.php` | Remover (substituído pelos dois acima) |
| `imagens/manual/*.png` | Criar (9 arquivos, Task 7) |
| `CLAUDE.md` | Modificar (Task 8) |

---

### Task 1: `Manual_conteudo` — biblioteca de conteúdo do manual

**Files:**
- Create: `application/libraries/Manual_conteudo.php`
- Test: `tests/manual_conteudo_test.php`

- [ ] **Step 1: Escrever o teste (falhando)**

Criar `tests/manual_conteudo_test.php`:

```php
<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Manual_conteudo.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

function assertTrue($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$mc = new Manual_conteudo();

assertSameValue(1, Manual_conteudo::VERSAO, 'VERSAO do modelo deve ser 1.');

$nivel2 = $mc->capitulos_por_nivel(2);
$nivel3 = $mc->capitulos_por_nivel(3);
$nivel4 = $mc->capitulos_por_nivel(4);
$nivelInvalido = $mc->capitulos_por_nivel(99);

assertSameValue(12, count($nivel2), 'Nivel 2 deve ver os 12 capitulos.');
assertSameValue(12, count($nivel3), 'Nivel 3 deve ver os 12 capitulos.');
assertSameValue(11, count($nivel4), 'Nivel 4 nao ve o capitulo de equipe.');
assertSameValue(0, count($nivelInvalido), 'Nivel sem capitulos cadastrados retorna lista vazia.');

$slugsNivel4 = array_column($nivel4, 'slug');
assertTrue(!in_array('equipe', $slugsNivel4, true), 'Colaborador (nivel 4) nao deve ver o capitulo equipe.');

$slugsNivel2 = array_column($nivel2, 'slug');
assertTrue(in_array('equipe', $slugsNivel2, true), 'Estabelecimento (nivel 2) deve ver o capitulo equipe.');

$assinaturaNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'assinatura-pagamento') {
        $assinaturaNivel2 = $capitulo;
    }
}
assertTrue($assinaturaNivel2 !== null, 'Capitulo assinatura-pagamento deve existir para o nivel 2.');
assertTrue(
    in_array('A tela de assinatura mostra o historico de ciclos de cobranca, pagamentos confirmados e tentativas recentes.', $assinaturaNivel2['topicos'], true),
    'Topico comum (*) deve entrar na lista do nivel 2.'
);
assertTrue(
    in_array('E o Estabelecimento quem normalmente acompanha e quita a assinatura da operacao, em `Minha assinatura`.', $assinaturaNivel2['topicos'], true),
    'Topico especifico do nivel 2 deve entrar na lista.'
);

$boasVindasNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'boas-vindas') {
        $boasVindasNivel2 = $capitulo;
    }
}
assertSameValue('boas-vindas.png', $boasVindasNivel2['print'], 'Print do capitulo boas-vindas deve resolver para o arquivo unico.');

$acessoNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'acesso-hierarquia') {
        $acessoNivel2 = $capitulo;
    }
}
assertSameValue(null, $acessoNivel2['print'], 'Capitulo sem print cadastrado deve resolver para null.');

echo "OK\n";
```

- [ ] **Step 2: Rodar o teste e confirmar que falha**

Run: `php tests/manual_conteudo_test.php`
Expected: erro fatal (`Class 'Manual_conteudo' not found` ou arquivo inexistente), já que a biblioteca ainda não existe.

- [ ] **Step 3: Criar `application/libraries/Manual_conteudo.php`**

```php
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manual_conteudo {

    const VERSAO = 1;

    public function capitulos_por_nivel($nivel) {
        $nivel = (int)$nivel;
        $resultado = array();
        foreach ($this->capitulos() as $capitulo) {
            if (!in_array($nivel, $capitulo['niveis'], true)) {
                continue;
            }
            $resultado[] = array(
                'slug' => $capitulo['slug'],
                'titulo' => $capitulo['titulo'],
                'icone' => $capitulo['icone'],
                'resumo' => $capitulo['resumo'],
                'topicos' => $this->resolver_topicos($capitulo['topicos'], $nivel),
                'print' => $this->resolver_print(isset($capitulo['print']) ? $capitulo['print'] : null, $nivel),
                'atualizado_em' => $capitulo['atualizado_em'],
            );
        }
        return $resultado;
    }

    private function resolver_topicos($topicos, $nivel) {
        $resultado = array();
        if (isset($topicos['*'])) {
            $resultado = array_merge($resultado, $topicos['*']);
        }
        if (isset($topicos[$nivel])) {
            $resultado = array_merge($resultado, $topicos[$nivel]);
        }
        return $resultado;
    }

    private function resolver_print($print, $nivel) {
        if ($print === null) {
            return null;
        }
        if (!is_array($print)) {
            return $print;
        }
        if (isset($print[$nivel])) {
            return $print[$nivel];
        }
        if (isset($print['*'])) {
            return $print['*'];
        }
        return null;
    }

    private function capitulos() {
        return array(
            array(
                'slug' => 'boas-vindas',
                'titulo' => 'Boas-vindas',
                'icone' => 'os-icon-grid-squares-22',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O UTecnologia Saude centraliza agenda, prontuario, exames e comunicacao com o paciente em um unico sistema, acessado pelo navegador. Este manual explica o que cada perfil de acesso pode fazer no dia a dia.',
                'topicos' => array(
                    '*' => array(
                        'O sistema funciona 100% pelo navegador, sem instalacao - funciona em computador, tablet ou celular.',
                        'Cada perfil de acesso (Estabelecimento, Prestador, Colaborador) enxerga uma fatia diferente da operacao, de acordo com a hierarquia de cadastro.',
                    ),
                    2 => array('Perfil pensado para gestores da clinica, consultorio ou operacao principal - normalmente coordena prestadores, colaboradores e a configuracao geral da rotina.'),
                    3 => array('Perfil pensado para o profissional que atende pacientes e registra prontuario - pode atuar sozinho ou dentro de uma clinica.'),
                    4 => array('Perfil pensado para secretaria e apoio operacional - organiza agenda e cadastro de pacientes no dia a dia.'),
                ),
                'print' => 'boas-vindas.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'acesso-hierarquia',
                'titulo' => 'Acesso e hierarquia',
                'icone' => 'os-icon-users',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O acesso segue uma arvore: quem cadastra vira o vinculo de quem e cadastrado. Isso define automaticamente o que cada perfil consegue ver.',
                'topicos' => array(
                    '*' => array(
                        'Voce so ve pacientes, agendamentos e prontuarios vinculados a sua propria estrutura - nunca dados de outra clinica ou profissional fora da sua arvore.',
                    ),
                    2 => array(
                        'Visualiza a operacao inteira vinculada ao seu cadastro: prestadores, colaboradores e pacientes.',
                        'E quem define, na pratica, quem entra na sua estrutura ao cadastrar novos prestadores e colaboradores.',
                    ),
                    3 => array(
                        'Visualiza seus proprios pacientes e atendimentos, alem do que colaboradores vinculados a voce registrarem.',
                        'Quando esta dentro de uma clinica (nivel Estabelecimento), tambem enxerga o contexto compartilhado dessa clinica.',
                    ),
                    4 => array(
                        'Visualiza pacientes e agendamentos da operacao a que estiver vinculado - direto a uma clinica ou a um prestador.',
                        'Tem acesso ao que voce mesmo cadastrar e ao que a clinica ou profissional vinculado registrar na mesma operacao visivel.',
                    ),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'agenda',
                'titulo' => 'Agenda',
                'icone' => 'os-icon-grid',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A Agenda e o centro operacional do dia: nela voce inicia, finaliza, remarca e cancela atendimentos, alem de acompanhar quem confirmou presenca pelo WhatsApp.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Agenda` no menu lateral para ver os atendimentos do dia, semana ou mes.',
                        'Cada agendamento mostra o status (pendente, confirmado, cancelado) e, quando aplicavel, a etiqueta de confirmacao via WhatsApp.',
                    ),
                    2 => array('Acompanha a agenda de todos os prestadores vinculados a clinica em uma visao unica.'),
                    3 => array('Usa a Agenda para iniciar, finalizar ou remarcar os proprios atendimentos.'),
                    4 => array('Usa a Agenda para confirmar, remarcar e organizar os atendimentos do dia da operacao vinculada.'),
                ),
                'print' => 'agenda.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'pacientes-cadastro',
                'titulo' => 'Pacientes e cadastro',
                'icone' => 'os-icon-folder',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O cadastro de pacientes concentra dados de contato, historico e vinculo com a operacao. Um cadastro bem feito evita duplicidade e erro de contato.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Pacientes` para cadastrar um novo paciente ou localizar um ja existente.',
                        'Mantenha telefone (com WhatsApp) e nome completo atualizados - sao usados no envio de confirmacao de agendamento.',
                    ),
                    2 => array('Revisa a base ativa de pacientes de toda a clinica.'),
                    4 => array('Cadastra novos pacientes e localiza contatos rapidamente durante o atendimento telefonico ou presencial.'),
                ),
                'print' => 'pacientes-cadastro.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'prontuario',
                'titulo' => 'Prontuario',
                'icone' => 'os-icon-file-text',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O prontuario registra o atendimento em tres blocos - Atendimento inicial, Avaliacao e Reavaliacao. Os rotulos desses blocos mudam automaticamente conforme a especialidade do prestador, para refletir o vocabulario clinico de cada area.',
                'topicos' => array(
                    '*' => array(
                        'Abra o prontuario a partir do agendamento em andamento, na Agenda ou na ficha do paciente.',
                        'Especialidades como Fisioterapia, Psicologia, Odontologia, Psiquiatria, Nutricao e Pediatria tem rotulos e exemplos de preenchimento adaptados - por exemplo, Fisioterapia mostra "Queixa / Avaliacao Postural" onde a Clinica Medica mostra "Queixa Principal".',
                        'O conteudo digitado continua sendo texto livre; o que muda por especialidade e apenas o rotulo e o texto de apoio (placeholder) de cada campo.',
                    ),
                    3 => array('E o prestador quem preenche o prontuario durante o atendimento - registrar no mesmo dia mantem o historico clinico organizado.'),
                    2 => array('Acompanha o prontuario dos prestadores vinculados a clinica pela ficha do paciente ou pelos relatorios clinicos.'),
                    4 => array('Acessa o prontuario apenas dentro do escopo operacional liberado para a equipe vinculada.'),
                ),
                'print' => 'prontuario.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'exames',
                'titulo' => 'Exames',
                'icone' => 'os-icon-database',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Exames solicitados e realizados ficam vinculados ao agendamento e ao paciente, com um checklist operacional de acompanhamento.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Exames` para solicitar um exame do catalogo ou registrar um exame ja realizado.',
                        'O checklist mostra o que falta confirmar antes de fechar o atendimento.',
                    ),
                ),
                'print' => 'exames.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'whatsapp-confirmacao',
                'titulo' => 'Confirmacao de agendamento pelo WhatsApp',
                'icone' => 'os-icon-phone-15',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Ao criar um agendamento, e possivel marcar o envio automatico de uma mensagem de confirmacao pelo WhatsApp do paciente, com botoes para confirmar ou cancelar direto pelo celular.',
                'topicos' => array(
                    '*' => array(
                        'Marque a opcao "Enviar confirmacao pelo WhatsApp" ao criar o agendamento - o sistema dispara um template aprovado pela Meta com dois botoes: Confirmar e Cancelar.',
                        'A resposta do paciente atualiza a agenda automaticamente: confirmar mantem o horario, cancelar muda o status do agendamento e libera o horario.',
                        'A etiqueta "Confirmado via WhatsApp" ou "Cancelado via WhatsApp" aparece na Agenda e no prontuario do paciente, entao toda a equipe ve o que aconteceu sem precisar ligar para o paciente.',
                        'Se o envio falhar (numero invalido, sem WhatsApp etc.), o agendamento e salvo normalmente - a falha do WhatsApp nunca bloqueia o cadastro.',
                    ),
                    2 => array('Contas sem assinatura ativa tem um limite de disparos gratuitos por periodo (modo trial); depois disso a confirmacao por WhatsApp exige plano ativo.'),
                ),
                'print' => 'whatsapp-confirmacao.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'whatsapp-lembrete',
                'titulo' => 'Lembrete automatico pelo WhatsApp',
                'icone' => 'os-icon-zap',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Alem da confirmacao no momento do agendamento, o sistema envia automaticamente um lembrete ao paciente poucas horas antes da consulta.',
                'topicos' => array(
                    '*' => array(
                        'O lembrete e disparado automaticamente ate 7 horas antes do horario marcado, para agendamentos que ainda nao foram confirmados nem cancelados.',
                        'Paciente que ja confirmou a presenca, ou que recebeu a confirmacao ha pouco tempo, nao recebe o lembrete duplicado.',
                        'E um envio unico por agendamento - nao gera repeticao de mensagens para o paciente.',
                    ),
                ),
                'print' => 'whatsapp-confirmacao.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'avisos-internos',
                'titulo' => 'Avisos internos (sino de notificacoes)',
                'icone' => 'os-icon-signs-11',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Sempre que o paciente confirma ou cancela pelo WhatsApp, um aviso aparece no sino de notificacoes no topo do sistema para quem precisa saber.',
                'topicos' => array(
                    '*' => array(
                        'O sino no topo mostra os avisos nao lidos, mais recentes primeiro.',
                        'Clicar em um aviso marca como lido e leva direto para o agendamento relacionado.',
                        'Cada aviso e entregue individualmente a quem precisa saber - o prestador do agendamento e quem mais estiver na mesma operacao.',
                    ),
                ),
                'print' => 'avisos-internos.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'equipe',
                'titulo' => 'Equipe',
                'icone' => 'os-icon-users',
                'niveis' => array(2, 3),
                'resumo' => 'A tela de Equipe organiza quem faz parte da operacao: prestadores e colaboradores vinculados a clinica ou ao profissional.',
                'topicos' => array(
                    '*' => array('Acesse `Equipe` para cadastrar ou revisar prestadores e colaboradores.'),
                    2 => array(
                        'Organiza todos os profissionais e colaboradores que participam da operacao da clinica.',
                        'Manter o cadastro da equipe atualizado evita perda de visibilidade na agenda e nos pacientes.',
                    ),
                    3 => array('Cadastra colaboradores proprios para apoio na rotina de atendimento, quando necessario.'),
                ),
                'print' => 'equipe.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'assinatura-pagamento',
                'titulo' => 'Assinatura e pagamento',
                'icone' => 'os-icon-wallet-loaded',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A assinatura do plano e cobrada em ciclos (mensal ou anual) via Mercado Pago, com PIX ou cartao, direto pela area administrativa.',
                'topicos' => array(
                    '*' => array('A tela de assinatura mostra o historico de ciclos de cobranca, pagamentos confirmados e tentativas recentes.'),
                    2 => array(
                        'E o Estabelecimento quem normalmente acompanha e quita a assinatura da operacao, em `Minha assinatura`.',
                        'O pagamento pode ser feito por PIX ou cartao dentro da area administrativa, sem sair do sistema.',
                        'Em caso de pendencia de pagamento, o acesso ao modulo SaaS e bloqueado ate a regularizacao - o restante do sistema (agenda, prontuario) continua funcionando normalmente.',
                    ),
                    3 => array('Quando a operacao tem assinatura vinculada, o prestador pode acompanhar a situacao comercial pela central de pagamento, sem precisar entrar na area de gestao SaaS.'),
                    4 => array('Se fizer parte do fluxo interno da clinica, o colaborador pode consultar o status comercial da assinatura vinculada.'),
                ),
                'print' => 'assinatura-pagamento.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'boas-praticas-suporte',
                'titulo' => 'Boas praticas e suporte',
                'icone' => 'os-icon-life-buoy',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Um fechamento com boas praticas de uso do dia a dia e como pedir ajuda quando precisar.',
                'topicos' => array(
                    '*' => array(
                        'Registre atendimento e status da agenda no mesmo dia - mantem o historico clinico e comercial sempre confiavel.',
                        'Padronize observacoes de remarcacao e cancelamento para toda a equipe entender o historico.',
                        'Em caso de duvida, use os canais de suporte do UTecnologia Saude informados na tela de login.',
                    ),
                    2 => array('Centralize o cadastro de colaboradores no nivel Colaborador para facilitar a operacao compartilhada da clinica.'),
                ),
                'atualizado_em' => '2026-09-07',
            ),
        );
    }
}
```

- [ ] **Step 4: Rodar o teste e confirmar que passa**

Run: `php tests/manual_conteudo_test.php`
Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Manual_conteudo.php tests/manual_conteudo_test.php
git commit -m "feat(manual): cria Manual_conteudo com os 12 capitulos do modelo v1

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 2: Corrigir o default do construtor de `M_pdf`

`application/libraries/M_pdf.php` nunca foi usado por nenhum controller (0
ocorrências de `m_pdf`/`M_pdf` fora do próprio arquivo). O default atual do
construtor é a string literal `"'c', 'A4-L'"`, que vira o argumento `$mode`
inteiro do construtor real do mPDF (`function mPDF($mode='', $format='A4', ...)`)
em vez de ser interpretado como múltiplos parâmetros — nunca configura formato
A4 retrato do jeito que o nome sugere. Como não há nenhum uso hoje, corrigir é
seguro.

**Files:**
- Modify: `application/libraries/M_pdf.php`

- [ ] **Step 1: Ler o arquivo atual**

Conteúdo atual (para referência, já lido nesta sessão):

```php
<?php
  if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'/third_party/mpdf/mpdf.php';

class M_pdf {

    public $param;
    public $pdf;
    public function __construct($param = "'c', 'A4-L'")
    {
        $this->param =$param;
        $this->pdf = new mPDF($this->param);
    }
}
?>
```

- [ ] **Step 2: Substituir pelo construtor corrigido**

```php
<?php
  if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'/third_party/mpdf/mpdf.php';

class M_pdf {

    public $param;
    public $pdf;
    public function __construct($param = array('', 'A4', 0, '', 15, 15, 18, 18, 9, 9, 'P'))
    {
        $this->param = $param;
        $this->pdf = new mPDF(...$this->param);
    }
}
```

- [ ] **Step 3: Validar sintaxe**

Run: `php -l application/libraries/M_pdf.php`
Expected: `No syntax errors detected in application/libraries/M_pdf.php`

- [ ] **Step 4: Commit**

```bash
git add application/libraries/M_pdf.php
git commit -m "fix(pdf): corrige default do construtor de M_pdf para gerar A4 retrato

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 3: Controller — trocar `build_manual_context` por `Manual_conteudo` e TCPDF por mPDF

**Files:**
- Modify: `application/controllers/adm/Usuarios.php:787-938`

- [ ] **Step 1: Substituir os três métodos (`manual`, `manual_pdf`, `build_manual_context`)**

Localizar o bloco atual (linhas 787-938, já lido nesta sessão) e substituir
por:

```php
function manual($nivel=null){
	$dd_user = $this->padrao_model->get_usuario_logado();
	$manual = $this->manual_dados($nivel, $dd_user);
	if(!$manual){
		redirect('adm/usuarios/dash');
		return;
	}
	$dados['dd_user'] = $dd_user;
	$dados['manual'] = $manual;
	$this->load->view('adm/usuarios/manual_funcao', $dados);
}

function manual_pdf($nivel=null){
	$dd_user = $this->padrao_model->get_usuario_logado();
	$manual = $this->manual_dados($nivel, $dd_user);
	if(!$manual){
		redirect('adm/usuarios/dash');
		return;
	}
	$dados['manual'] = $manual;
	$html_capa = $this->load->view('adm/usuarios/manual_pdf_capa', $dados, true);
	$html_conteudo = $this->load->view('adm/usuarios/manual_pdf_conteudo', $dados, true);

	$this->load->library('m_pdf');
	$mpdf = $this->m_pdf->pdf;
	$mpdf->SetTitle($manual['pdf_title']);
	$mpdf->SetAuthor('UTec Saude');
	$mpdf->SetHTMLHeader('<div style="text-align:right;font-size:8pt;color:#64748b;border-bottom:0.5pt solid #e2e8f0;padding-bottom:4px;">'.$manual['title'].'</div>');
	$mpdf->SetHTMLFooter('<div style="text-align:center;font-size:8pt;color:#94a3b8;border-top:0.5pt solid #e2e8f0;padding-top:4px;">Manual v'.$manual['versao'].' &middot; gerado em '.$manual['gerado_em'].' &middot; pagina {PAGENO} de {nb}</div>');
	$mpdf->h2toc = array('H2' => 0);
	$mpdf->WriteHTML($html_capa);
	$mpdf->WriteHTML('<tocpagebreak toc-preHTML="&lt;h1&gt;Sumario&lt;/h1&gt;" links="on" />');
	$mpdf->WriteHTML($html_conteudo);
	$mpdf->Output($manual['pdf_slug'].'.pdf', 'I');
}

private function manual_dados($nivel=null, $dd_user=null){
	if(!$dd_user){
		$dd_user = $this->padrao_model->get_usuario_logado();
	}
	if(!$dd_user){
		return null;
	}
	$nivel = $nivel !== null ? (int)$nivel : (int)$dd_user->nivel;
	if(!in_array($nivel, [2,3,4], true)){
		return null;
	}

	$titulos = [
		2 => ['titulo' => 'Manual do Estabelecimento', 'subtitulo' => 'Guia da clinica/estabelecimento para gerir equipe, pacientes, agenda e assinatura.', 'slug' => 'manual-estabelecimento-nivel-2'],
		3 => ['titulo' => 'Manual do Prestador', 'subtitulo' => 'Guia do profissional para atender pacientes, acompanhar agenda e operar dentro da clinica.', 'slug' => 'manual-prestador-nivel-3'],
		4 => ['titulo' => 'Manual do Colaborador', 'subtitulo' => 'Guia de secretaria e apoio operacional para agenda, pacientes e rotina compartilhada.', 'slug' => 'manual-colaborador-nivel-4'],
	];

	$this->load->library('manual_conteudo');

	return [
		'level' => $nivel,
		'title' => $titulos[$nivel]['titulo'],
		'subtitle' => $titulos[$nivel]['subtitulo'],
		'pdf_title' => $titulos[$nivel]['titulo'].' - UTEC Saude',
		'pdf_slug' => $titulos[$nivel]['slug'],
		'versao' => Manual_conteudo::VERSAO,
		'gerado_em' => date('d/m/Y'),
		'capitulos' => $this->manual_conteudo->capitulos_por_nivel($nivel),
	];
}
```

Isso remove a antiga `build_manual_context()` (150 linhas de arrays fixos) e o
bloco TCPDF (`require_once APPPATH.'libraries/tcpdf/tcpdf.php'; new TCPDF(...)`).

- [ ] **Step 2: Validar sintaxe**

Run: `php -l application/controllers/adm/Usuarios.php`
Expected: `No syntax errors detected in application/controllers/adm/Usuarios.php`

- [ ] **Step 3: Commit**

```bash
git add application/controllers/adm/Usuarios.php
git commit -m "feat(manual): controller usa Manual_conteudo e mPDF em vez de TCPDF

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

(A verificação funcional deste controller depende das views das Tasks 4 e 5 —
rodar `php -l` aqui só garante ausência de erro de sintaxe; a Task 6 faz a
verificação end-to-end no navegador.)

---

### Task 4: Reescrever a tela do manual (`manual_funcao.php`)

**Files:**
- Modify: `application/views/adm/usuarios/manual_funcao.php`

- [ ] **Step 1: Substituir o arquivo inteiro**

```php
<!DOCTYPE html>
<html>
  <head>
    <title><?=$manual['title']?> | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .manual-shell { display:grid; gap:24px; }
      .manual-hero { background:linear-gradient(135deg, rgba(37,99,235,.10), rgba(15,118,110,.10)); border:1px solid #dbe7f3; border-radius:24px; padding:24px; box-shadow:0 14px 30px rgba(15,23,42,.05); }
      .manual-title { font-size:34px; line-height:1.08; color:#0f172a; font-weight:700; margin:0 0 10px; }
      .manual-copy { color:#475569; font-size:15px; line-height:1.75; max-width:840px; }
      .manual-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }
      .manual-capitulo { background:#fff; border:1px solid #e2e8f0; border-radius:20px; box-shadow:0 10px 24px rgba(15,23,42,.05); overflow:hidden; }
      .manual-capitulo-head { padding:18px 20px 0; display:flex; align-items:center; gap:10px; }
      .manual-capitulo-head i { font-size:20px; color:#2563eb; }
      .manual-capitulo-body { padding:12px 20px 20px; }
      .manual-capitulo-resumo { color:#475569; margin:6px 0 12px; }
      .manual-list { margin:0; padding-left:18px; color:#475569; }
      .manual-list li { margin-bottom:10px; line-height:1.7; }
      .manual-list li:last-child { margin-bottom:0; }
      .manual-print { width:100%; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:14px; display:block; }
      .manual-footer-nota { color:#94a3b8; font-size:13px; text-align:center; }
    </style>
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper with-side-panel solid-bg-all">
      <? include("includes/adm/search.php"); ?>
      <div class="layout-w">
        <? include("includes/adm/menu.php"); ?>
        <div class="content-w">
          <? include("includes/adm/top.php"); ?>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
            <li class="breadcrumb-item"><span>Manual</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="manual-shell">
                <div class="manual-hero">
                  <div class="label">Ajuda permanente</div>
                  <h1 class="manual-title"><?=$manual['title']?></h1>
                  <div class="manual-copy"><?=$manual['subtitle']?></div>
                  <div class="manual-actions">
                    <a href="<?=base_url()?>adm/usuarios/manual_pdf/<?=$manual['level']?>" class="btn btn-primary" target="_blank">Abrir PDF</a>
                    <a href="<?=base_url()?>adm/usuarios/dash" class="btn btn-outline-secondary">Voltar ao painel</a>
                  </div>
                </div>

                <? foreach($manual['capitulos'] as $capitulo){ ?>
                <div class="manual-capitulo">
                  <div class="manual-capitulo-head">
                    <i class="<?=$capitulo['icone']?>"></i>
                    <h6 class="element-header" style="margin-bottom:0;"><?=$capitulo['titulo']?></h6>
                  </div>
                  <div class="manual-capitulo-body">
                    <p class="manual-capitulo-resumo"><?=$capitulo['resumo']?></p>
                    <? if($capitulo['print']){ ?>
                    <img class="manual-print" src="<?=base_url().'imagens/manual/'.$capitulo['print']?>" alt="<?=$capitulo['titulo']?>">
                    <? } ?>
                    <ul class="manual-list">
                      <? foreach($capitulo['topicos'] as $topico){ ?>
                      <li><?=$topico?></li>
                      <? } ?>
                    </ul>
                  </div>
                </div>
                <? } ?>

                <div class="manual-footer-nota">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
```

- [ ] **Step 2: Validar sintaxe**

Run: `php -l application/views/adm/usuarios/manual_funcao.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add application/views/adm/usuarios/manual_funcao.php
git commit -m "feat(manual): tela do manual passa a iterar capitulos dinamicos

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 5: Views do PDF (capa + conteúdo em mPDF) e remoção da view antiga

**Files:**
- Create: `application/views/adm/usuarios/manual_pdf_capa.php`
- Create: `application/views/adm/usuarios/manual_pdf_conteudo.php`
- Delete: `application/views/adm/usuarios/manual_funcao_pdf.php`

- [ ] **Step 1: Criar `manual_pdf_capa.php`**

```php
<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10.5pt; line-height:1.5; }
  h1 { font-size:22pt; margin:0 0 8pt; color:#0f172a; }
  h2 { font-size:14pt; margin:0 0 8pt; color:#0f172a; }
  .manual-pdf-capa-marca { font-size:9pt; text-transform:uppercase; letter-spacing:2pt; color:#2563eb; font-weight:bold; margin-bottom:10pt; }
  .manual-pdf-capa-sub { color:#475569; font-size:12pt; margin-top:10pt; }
  .manual-pdf-capa-data { color:#94a3b8; font-size:9pt; margin-top:30pt; }
  .manual-pdf-capitulo { border:0.5pt solid #e2e8f0; border-radius:6pt; padding:10pt 12pt; margin-bottom:10pt; }
  .manual-pdf-resumo { color:#475569; margin:4pt 0 8pt; }
  .manual-pdf-print { width:100%; border:0.5pt solid #e2e8f0; margin-bottom:8pt; }
  .manual-pdf-lista { margin:0; padding-left:14pt; color:#475569; }
  .manual-pdf-lista li { margin-bottom:6pt; }
</style>
<div style="text-align:center; margin-top:80pt;">
  <div class="manual-pdf-capa-marca">UTEC Saude</div>
  <h1><?=$manual['title']?></h1>
  <div class="manual-pdf-capa-sub"><?=$manual['subtitle']?></div>
  <div class="manual-pdf-capa-data">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
</div>
```

- [ ] **Step 2: Criar `manual_pdf_conteudo.php`**

```php
<? $primeiro = true; foreach($manual['capitulos'] as $capitulo){ ?>
<div class="manual-pdf-capitulo"<?=$primeiro ? '' : ' style="page-break-before:always;"'?>>
  <h2><?=$capitulo['titulo']?></h2>
  <p class="manual-pdf-resumo"><?=$capitulo['resumo']?></p>
  <? if($capitulo['print']){ ?>
  <img class="manual-pdf-print" src="<?=FCPATH.'imagens/manual/'.$capitulo['print']?>">
  <? } ?>
  <ul class="manual-pdf-lista">
    <? foreach($capitulo['topicos'] as $topico){ ?>
    <li><?=$topico?></li>
    <? } ?>
  </ul>
</div>
<? $primeiro = false; } ?>
```

- [ ] **Step 3: Remover a view antiga**

```bash
git rm application/views/adm/usuarios/manual_funcao_pdf.php
```

- [ ] **Step 4: Validar sintaxe das duas views novas**

Run: `php -l application/views/adm/usuarios/manual_pdf_capa.php && php -l application/views/adm/usuarios/manual_pdf_conteudo.php`
Expected: `No syntax errors detected` para os dois arquivos.

- [ ] **Step 5: Commit**

```bash
git add application/views/adm/usuarios/manual_pdf_capa.php application/views/adm/usuarios/manual_pdf_conteudo.php
git commit -m "feat(manual): views de capa e conteudo do PDF em mPDF, remove view TCPDF antiga

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 6: Verificação end-to-end local (sem screenshots ainda)

Neste ponto os capítulos sem `print` cadastrado (ex.: `acesso-hierarquia`,
`boas-praticas-suporte`) já renderizam corretamente sem imagem (o `if` no
template cobre isso). Os capítulos com `print` vão referenciar arquivos que
ainda não existem — o objetivo aqui é confirmar que o restante do fluxo
(controller, mPDF, TOC, cabeçalho/rodapé) funciona antes de gastar tempo com
screenshot.

**Files:** nenhum (apenas verificação manual)

- [ ] **Step 1: `php -l` em todos os arquivos alterados**

Run:
```bash
php -l application/libraries/Manual_conteudo.php
php -l application/libraries/M_pdf.php
php -l application/controllers/adm/Usuarios.php
php -l application/views/adm/usuarios/manual_funcao.php
php -l application/views/adm/usuarios/manual_pdf_capa.php
php -l application/views/adm/usuarios/manual_pdf_conteudo.php
```
Expected: `No syntax errors detected` em todas as linhas.

- [ ] **Step 2: Confirmar que o WAMP local está no ar**

Run: `curl -s -o /dev/null -w "%{http_code}\n" http://localhost/utec/adm`
(ajustar a URL para o alias configurado no WAMP, se diferente de `/utec/`)
Expected: `200` ou `302` (redirect de sessão) — nunca erro de conexão.

- [ ] **Step 3: Logar como admin e abrir a tela do manual para os 3 níveis**

Login em `adm/123456` (admin nível 1), depois abrir manualmente no navegador:
- `adm/usuarios/manual/2`
- `adm/usuarios/manual/3`
- `adm/usuarios/manual/4`

Expected: os 12 (nível 2 e 3) ou 11 (nível 4) capítulos aparecem, com título,
resumo e tópicos corretos; capítulos com `print` mostram um ícone de imagem
quebrada (esperado, arquivo ainda não existe) em vez de travar a página.

- [ ] **Step 4: Abrir o PDF para os 3 níveis**

- `adm/usuarios/manual_pdf/2`
- `adm/usuarios/manual_pdf/3`
- `adm/usuarios/manual_pdf/4`

Expected: PDF abre no navegador (sem erro 500), com capa, sumário clicável
(gerado a partir dos `<h2>` de cada capítulo) e cabeçalho/rodapé com
paginação. Imagens ausentes aparecem como espaço vazio ou ícone quebrado, sem
quebrar a geração do PDF.

Se o PDF não gerar (tela em branco ou erro), depurar a chamada ao mPDF antes
de seguir — não prosseguir para a Task 7 com o motor de PDF quebrado.

- [ ] **Step 5: Registrar o resultado**

Sem commit nesta task (é só verificação). Se algo falhar, corrigir inline nas
Tasks 3-5 antes de continuar.

---

### Task 7: Captura de screenshots

Ambiente local, dados de teste — nunca produção.

**Files:**
- Create: `imagens/manual/boas-vindas.png`
- Create: `imagens/manual/agenda.png`
- Create: `imagens/manual/pacientes-cadastro.png`
- Create: `imagens/manual/prontuario.png`
- Create: `imagens/manual/exames.png`
- Create: `imagens/manual/whatsapp-confirmacao.png`
- Create: `imagens/manual/avisos-internos.png`
- Create: `imagens/manual/equipe.png`
- Create: `imagens/manual/assinatura-pagamento.png`

- [ ] **Step 1: Localizar um usuário de teste de cada nível**

Run:
```bash
mysql -h localhost -u utecnologiacom_userutec -p'14rPukFSD?vyab2d' utecnologiacom_db -e "SELECT id, nome, login, nivel FROM usuarios WHERE nivel IN (2,3,4) ORDER BY nivel, id LIMIT 20;"
```
Expected: lista de usuários de teste por nível. Anotar um `id` de nível 2, um
de nível 3 e um de nível 4.

- [ ] **Step 2: Logar como admin e assumir cada perfil**

No navegador (Claude in Chrome): login em `adm` com `adm/123456`, depois
navegar para `/admin/logar_como/{id}` com cada um dos 3 ids anotados no Step 1
para assumir a sessão daquele perfil.

- [ ] **Step 3: Capturar cada tela**

Para cada `id` assumido, navegar até a tela correspondente e capturar (recorte
da região relevante, não a tela inteira):

| Tela a navegar | Arquivo |
|---|---|
| `adm/usuarios/dash` | `boas-vindas.png` |
| `adm/atendimento` (Agenda) | `agenda.png` |
| `adm/usuarios` (lista de pacientes) | `pacientes-cadastro.png` |
| Prontuário de um atendimento em andamento | `prontuario.png` |
| `adm/usuarios/exames` | `exames.png` |
| Agendamento com etiqueta de confirmação/cancelamento via WhatsApp na Agenda | `whatsapp-confirmacao.png` |
| Sino de notificações aberto (topo) | `avisos-internos.png` |
| Tela de Equipe (nível 2 ou 3) | `equipe.png` |
| Tela de assinatura/pagamento | `assinatura-pagamento.png` |

Salvar cada captura em `imagens/manual/{arquivo}` (caminho absoluto do
projeto, ex.: `c:\htdocs\utec\imagens\manual\agenda.png`).

- [ ] **Step 4: Confirmar que os 9 arquivos existem**

Run: `ls imagens/manual/`
Expected: os 9 arquivos listados na tabela do Step 3.

- [ ] **Step 5: Reabrir a tela e o PDF dos 3 níveis para conferir as imagens**

Repetir a verificação da Task 6 (Steps 3 e 4) — agora as imagens devem
aparecer de verdade, tanto na tela (`base_url()+imagens/manual/...`) quanto no
PDF (`FCPATH.'imagens/manual/...'`).

- [ ] **Step 6: Commit dos assets**

```bash
git add imagens/manual/
git commit -m "feat(manual): adiciona screenshots dos 9 capitulos com print

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 8: Atualizar `CLAUDE.md` — governança do manual

**Files:**
- Modify: `CLAUDE.md`

- [ ] **Step 1: Adicionar uma nova seção "19. Manual de Ajuda ao Usuário"**

Inserir, logo após a seção 18 (Arquitetura de Agentes), no final do arquivo:

```markdown
## 19. Manual de Ajuda ao Usuário

Manual por perfil (níveis 2, 3 e 4) acessível em `adm/usuarios/manual/{nivel}`
(tela) e `adm/usuarios/manual_pdf/{nivel}` (PDF via mPDF). Conteúdo modelo v1:
`docs/superpowers/specs/2026-09-04-manual-ajuda-v1-design.md`.

- **Fonte do conteúdo:** `application/libraries/Manual_conteudo.php` — array
  estático de capítulos (sem tabela no banco), filtrado por nível via
  `capitulos_por_nivel($nivel)`. `Manual_conteudo::VERSAO` sobe só quando a
  ESTRUTURA do array mudar, não a cada capítulo novo.
- **Screenshots:** `imagens/manual/*.png`, capturados em ambiente local com
  dados de teste (nunca produção).
- **Regra de sincronização:** toda funcionalidade nova visível ao usuário
  final (WhatsApp, SaaS, prontuário, agenda etc.) só é considerada concluída
  quando `Manual_conteudo.php` tiver um capítulo ou tópico atualizado
  cobrindo ela. `agente-dev-infra`, por ser o único agente que publica em
  produção, verifica isso no fechamento de qualquer entrega com impacto
  visível ao usuário — mesmo portão que já usa para `php -l` e healthcheck
  pós-deploy.
```

- [ ] **Step 2: Atualizar a linha do `agente-dev-infra` na tabela da seção 18**

Localizar, na tabela de agentes da seção 18:

```
| `agente-dev-infra` | Migrações (`adm/Dev.php`), rotas, `config/`, deploy FTP, cron. Único que publica em produção. |
```

Substituir por:

```
| `agente-dev-infra` | Migrações (`adm/Dev.php`), rotas, `config/`, deploy FTP, cron. Único que publica em produção — inclui checar se `Manual_conteudo.php` (ver seção 19) foi atualizado antes de fechar uma entrega com impacto visível ao usuário. |
```

- [ ] **Step 3: Marcar o item no roadmap (seção 15.1, Funcionalidades Concluídas)**

Adicionar ao final da lista de "Funcionalidades Concluídas" (seção 15.1):

```
- [x] Manual de ajuda ao usuário v1 (níveis 2, 3 e 4) com capítulos reaproveitáveis, screenshots e PDF em mPDF
```

- [ ] **Step 4: Commit**

```bash
git add CLAUDE.md
git commit -m "docs(CLAUDE): documenta o manual de ajuda v1 e a regra de sincronizacao

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 9: Verificação final e handoff para deploy

**Files:** nenhum (apenas verificação + handoff)

- [ ] **Step 1: Rodar todos os testes de novo**

Run: `php tests/manual_conteudo_test.php`
Expected: `OK`

- [ ] **Step 2: `php -l` em todo o conjunto alterado**

Run:
```bash
php -l application/libraries/Manual_conteudo.php
php -l application/libraries/M_pdf.php
php -l application/controllers/adm/Usuarios.php
php -l application/views/adm/usuarios/manual_funcao.php
php -l application/views/adm/usuarios/manual_pdf_capa.php
php -l application/views/adm/usuarios/manual_pdf_conteudo.php
```
Expected: `No syntax errors detected` em todas.

- [ ] **Step 3: Revisão visual final local**

Abrir `adm/usuarios/manual/{2,3,4}` e `adm/usuarios/manual_pdf/{2,3,4}` mais
uma vez, agora com todo o conteúdo e as 9 imagens no lugar. Conferir
acentuação (UTF-8), quebras de página por capítulo no PDF e o sumário
clicável.

- [ ] **Step 4: Handoff para `agente-dev-infra`**

Passar a tarefa de deploy para o `agente-dev-infra` (skill `ftp`): subir os
arquivos alterados/criados desta lista —

```
application/libraries/Manual_conteudo.php
application/libraries/M_pdf.php
application/controllers/adm/Usuarios.php
application/views/adm/usuarios/manual_funcao.php
application/views/adm/usuarios/manual_pdf_capa.php
application/views/adm/usuarios/manual_pdf_conteudo.php
imagens/manual/*.png (9 arquivos)
```

E remover em produção (se existir) `application/views/adm/usuarios/manual_funcao_pdf.php`.

Healthcheck pós-deploy: home 200, `/admin` 200, e abrir
`adm/usuarios/manual_pdf/2` autenticado em produção uma vez para confirmar que
o PDF gera sem erro 500 (ambiente de produção pode ter configuração de PHP
diferente do WAMP local — validar antes de considerar a entrega concluída).
