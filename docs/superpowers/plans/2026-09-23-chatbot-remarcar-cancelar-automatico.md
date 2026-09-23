# Chatbot — Remarcação e Cancelamento Automáticos — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** O paciente remarca (escolhendo dia e horário livres do mesmo profissional) e cancela a consulta sozinho pelo chatbot do WhatsApp, com antecedência mínima de 24h, sem IA.

**Architecture:** Funções puras novas no helper do WhatsApp (ids de clique, regra de 24h, paginação, textos) e no helper de disponibilidade (livres do dia com corte mínimo). `Disponibilidade_model` ganha `dias_com_vaga()` e corte opcional em `horarios_livres()`. `Whatsapp_model` ganha gravação atômica (`FOR UPDATE`) de remarcação/cancelamento. Uma library nova `Whatsapp_chatbot_agenda` conduz os fluxos com listas/botões; `Whatsapp_chatbot` só roteia para ela e mantém o fluxo antigo de solicitação como fallback.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MySQL/MariaDB (InnoDB), WhatsApp Cloud API (mensagens interativas list/button).

**Spec:** `docs/superpowers/specs/2026-09-23-chatbot-remarcar-cancelar-automatico-design.md`

## Global Constraints

- PHP 7.2: sem `match`, sem `fn`, sem typed properties, sem named args, sem `str_contains`, sem `?->`.
- Não modificar `system/`. Não usar `$_POST` direto.
- Lint: `/c/PHP/PHP7.2/php.exe -l <arquivo>`. Testes: `/c/PHP/PHP7.2/php.exe tests/<arquivo>.php` (exit 0 = passou). Nunca usar o `php` do PATH (é PHP 8).
- Antecedência mínima: **24 horas** (`utec_whatsapp_agenda_antecedencia_horas()` = 24). Janela de busca: **30 dias**. Lista de dias: até **10**. Horários por página: **9 + "Ver mais horários"** quando sobram mais de 10; senão até 10.
- Remarcação sempre com o mesmo `id_prestador`. Só consultas com `status = 0` são alteradas pelo bot.
- Motivo só no cancelamento, opcional (botão "Prefiro não informar"). Mínimo 3 caracteres quando digitado.
- Ids de clique: `rem:{id}:d:{Ymd}`, `rem:{id}:p:{Ymd}:{pagina}`, `rem:{id}:h:{Ymd}{Hi}`, `rem:{id}:ok:{Ymd}{Hi}`, `rem:{id}:dias`, `can:{id}:sem_motivo`, `can:{id}:ok`. Botão Voltar reutiliza o id existente `chat:paciente:voltar`.
- Sessões: `fluxo = 'agenda_remarcar'` (etapas `dia`, `hora`, `confirmar`), `fluxo = 'agenda_cancelar'` (etapas `motivo`, `confirmar`).
- Log em `whatsapp_notificacoes`: `tipo_notificacao = 'chatbot_remarcado'` (`status_confirmacao = 'confirmado'`) / `'chatbot_cancelado'` (`'cancelado'`), `status_envio = 'enviado'`, `wamid = ''` (não consome cota).
- Novo tipo de log da equipe `equipe_remarcado` deve ser excluído das consultas de "última resposta" junto com `equipe_confirmado` e `equipe_cancelado`.
- Flag nova `whatsapp.notificar_remarcacao_equipe_ativo` (env `WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE=1`), **desligada por padrão**. Template `agendamento_remarcado_equipe`.
- Falha em aviso interno ou WhatsApp à equipe nunca desfaz a gravação.
- Textos ao paciente em português com acentuação correta.
- Fluxo antigo `solicitacao/motivo` (`Whatsapp_chatbot::processar_sessao_motivo`) permanece intacto e é o fallback (< 24h, sem grade, sem vaga).
- Commits terminam com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.

## File Structure

| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/disponibilidade_helper.php` | Modificar | `utec_disp_aplicar_minimo()`, `utec_disp_livres_do_dia()` |
| `application/models/Disponibilidade_model.php` | Modificar | `horarios_livres()` com 4º parâmetro; `dias_com_vaga()`; consultas por período |
| `tests/disponibilidade_helper_test.php`, `tests/disponibilidade_source_test.php` | Modificar | Casos novos |
| `application/helpers/whatsapp_agendamento_helper.php` | Modificar | Funções puras `utec_whatsapp_agenda_*`, avisos e template de remarcação |
| `application/config/whatsapp.php` | Modificar | Flag de remarcação à equipe |
| `tests/whatsapp_chatbot_agenda_test.php` | Criar | Testes puros do helper |
| `application/models/Whatsapp_model.php` | Modificar | `remarcar_agendamento_chatbot()`, `cancelar_agendamento_chatbot()`, exclusão `equipe_remarcado` |
| `application/controllers/adm/Atendimento.php` | Modificar | Exclusão `equipe_remarcado` (1 linha) |
| `application/models/Notificacoes_model.php` | Modificar | `criar_aviso_chatbot_agenda()` |
| `application/libraries/Whatsapp_agendamento.php` | Modificar | `notificar_equipe()` aceita `remarcar` atrás do flag |
| `tests/whatsapp_chatbot_agenda_source_test.php` | Criar | Asserções de fonte |
| `tests/whatsapp_notificar_equipe_source_test.php` | Modificar | String de exclusão atualizada |
| `application/libraries/Whatsapp_chatbot_agenda.php` | Criar | Fluxos remarcar/cancelar |
| `application/libraries/Whatsapp_chatbot.php` | Modificar | Roteamento + fallback |
| `tests/whatsapp_chatbot_agenda_library_test.php` | Criar | Testes de comportamento com fakes |
| `docs/whatsapp-remarcacao-template-pendente.md` | Criar | Texto do template para a Meta |
| `application/libraries/Manual_conteudo.php` | Modificar | Tópicos no capítulo `whatsapp-confirmacao` |
| `CLAUDE.md` | Modificar | §10.3.1 |

---

### Task 1: Disponibilidade — corte mínimo e dias com vaga

**Files:**
- Modify: `application/helpers/disponibilidade_helper.php` (acrescentar no fim)
- Modify: `application/models/Disponibilidade_model.php`
- Test: `tests/disponibilidade_helper_test.php`, `tests/disponibilidade_source_test.php`

**Interfaces:**
- Produces:
  - `utec_disp_aplicar_minimo(array $slots, string $data, string $minimo_datetime): string[]` — mantém slots do dia `$data` com início `>=` `$minimo_datetime` (`'Y-m-d H:i'`); dia anterior ao mínimo → `[]`; dia posterior → todos.
  - `utec_disp_livres_do_dia(array $intervalos, int $duracao, array $horas_agendadas, array $bloqueios_brutos, string $data): string[]` — `$bloqueios_brutos = [['inicio'=>'Y-m-d H:i:s','fim'=>...], ...]`.
  - `Disponibilidade_model::horarios_livres($id_prestador, $data, $ignorar_agendamento_id = 0, $minimo_datetime = null)` — com `$minimo_datetime` aplica `utec_disp_aplicar_minimo`; sem ele mantém o comportamento atual.
  - `Disponibilidade_model::dias_com_vaga($id_prestador, $minimo_datetime, $dias = 30, $limite = 10, $ignorar_agendamento_id = 0)` → `['tem_grade'=>bool,'duracao'=>int,'dias'=>[['data'=>'Y-m-d','qtd'=>int], ...]]`.

- [ ] **Step 1: Testes que falham**

Em `tests/disponibilidade_helper_test.php`, antes do `echo` final:

```php
// --- corte minimo (regra de antecedencia)
$slotsDia = array('08:00', '08:30', '09:00', '09:30');
assertSameValue(array('09:00', '09:30'), utec_disp_aplicar_minimo($slotsDia, '2026-09-25', '2026-09-25 09:00'), 'minimo no mesmo dia inclui o horario exato');
assertSameValue(array(), utec_disp_aplicar_minimo($slotsDia, '2026-09-24', '2026-09-25 09:00'), 'dia antes do minimo fica vazio');
assertSameValue($slotsDia, utec_disp_aplicar_minimo($slotsDia, '2026-09-26', '2026-09-25 09:00'), 'dia depois do minimo fica inteiro');
assertSameValue($slotsDia, utec_disp_aplicar_minimo($slotsDia, '2026-09-25', 'invalido'), 'minimo invalido nao filtra');

// --- livres do dia (grade + agendados + bloqueios brutos)
assertSameValue(array('08:00', '09:30'), utec_disp_livres_do_dia(
    array(array('08:00', '10:00')), 30, array('08:30'),
    array(array('inicio' => '2026-09-25 09:00:00', 'fim' => '2026-09-25 09:30:00')), '2026-09-25'
), 'livres do dia combina agendado e bloqueio');
assertSameValue(array('08:00', '08:30', '09:00', '09:30'), utec_disp_livres_do_dia(
    array(array('08:00', '10:00')), 30, array(),
    array(array('inicio' => '2026-09-24 09:00:00', 'fim' => '2026-09-24 10:00:00')), '2026-09-25'
), 'bloqueio de outro dia nao afeta');
```

Em `tests/disponibilidade_source_test.php`, antes do `echo` final:

```php
$modelDisp = lerArquivo('application/models/Disponibilidade_model.php');
assertContains('function dias_com_vaga(', $modelDisp, 'model: dias_com_vaga');
assertContains('$minimo_datetime = null', $modelDisp, 'horarios_livres aceita corte minimo');
assertContains('BETWEEN', $modelDisp, 'agendamentos do periodo em uma consulta');
assertContains('utec_disp_livres_do_dia(', $modelDisp, 'model usa livres_do_dia');
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_helper_test.php`
Expected: fatal `Call to undefined function utec_disp_aplicar_minimo()`.

- [ ] **Step 3: Helper**

Acrescentar ao fim de `application/helpers/disponibilidade_helper.php`:

```php
if (!function_exists('utec_disp_aplicar_minimo')) {
    // Mantem os slots do dia $data que comecam em/depois de $minimo_datetime ('Y-m-d H:i').
    function utec_disp_aplicar_minimo($slots, $data, $minimo_datetime) {
        $ts = strtotime((string)$minimo_datetime);
        if ($ts === false) {
            return array_values((array)$slots);
        }
        $data_minima = date('Y-m-d', $ts);
        if ($data < $data_minima) {
            return array();
        }
        if ($data > $data_minima) {
            return array_values((array)$slots);
        }
        $limite = (int)date('G', $ts) * 60 + (int)date('i', $ts);
        $saida = array();
        foreach ((array)$slots as $slot) {
            $m = utec_disp_min($slot);
            if ($m !== null && $m >= $limite) {
                $saida[] = $slot;
            }
        }
        return $saida;
    }
}

if (!function_exists('utec_disp_livres_do_dia')) {
    function utec_disp_livres_do_dia($intervalos, $duracao_min, $horas_agendadas, $bloqueios_brutos, $data) {
        $slots = utec_disp_gerar_slots($intervalos, $duracao_min);
        return utec_disp_remover_ocupados(
            $slots,
            $horas_agendadas,
            utec_disp_recortar_bloqueios_no_dia($bloqueios_brutos, $data),
            $duracao_min
        );
    }
}
```

- [ ] **Step 4: Model**

Em `application/models/Disponibilidade_model.php`:

a) Substituir o método privado `bloqueios_do_dia()` inteiro por estes três métodos privados:

```php
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
```

b) Logo após o método privado `horas_agendadas()`, acrescentar:

```php
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
```

c) Substituir o método `horarios_livres()` inteiro por:

```php
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
```

d) Acrescentar o método público (logo após `horarios_livres()`):

```php
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
```

- [ ] **Step 5: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_helper_test.php` → `OK disponibilidade_helper_test`
Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → `OK disponibilidade_source_test`
Run: `/c/PHP/PHP7.2/php.exe -l` nos 2 arquivos PHP alterados → sem erros.

- [ ] **Step 6: Commit**

```bash
git add application/helpers/disponibilidade_helper.php application/models/Disponibilidade_model.php tests/disponibilidade_helper_test.php tests/disponibilidade_source_test.php
git commit -m "feat(horarios): corte minimo e dias com vaga para o chatbot

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 2: Funções puras do chatbot de agenda + flag

**Files:**
- Modify: `application/helpers/whatsapp_agendamento_helper.php` (acrescentar no fim; e alterar `utec_whatsapp_template_equipe_nome()` e `utec_whatsapp_componentes_equipe_template()`)
- Modify: `application/config/whatsapp.php` (acrescentar no fim)
- Test: `tests/whatsapp_chatbot_agenda_test.php` (criar)

**Interfaces:**
- Produces (todas globais, guardadas por `function_exists`):
  - `utec_whatsapp_agenda_antecedencia_horas(): int` (24)
  - `utec_whatsapp_agenda_minimo_datetime(int $agora_ts): string` — `'Y-m-d H:i'` de `agora + 24h`, arredondado **para cima** ao minuto
  - `utec_whatsapp_agenda_antecedencia_ok(string $data, string $hora, int $agora_ts): bool`
  - `utec_whatsapp_agenda_id_dia($id, $data)`, `utec_whatsapp_agenda_id_pagina($id, $data, $pagina)`, `utec_whatsapp_agenda_id_hora($id, $data, $hora)`, `utec_whatsapp_agenda_id_confirmar_remarcacao($id, $data, $hora)`, `utec_whatsapp_agenda_id_outros_dias($id)`, `utec_whatsapp_agenda_id_sem_motivo($id)`, `utec_whatsapp_agenda_id_confirmar_cancelamento($id)` → string
  - `utec_whatsapp_agenda_parse_id(string $payload): ?array` → `['fluxo'=>'remarcar'|'cancelar','acao'=>'dia'|'pagina'|'hora'|'confirmar'|'dias'|'sem_motivo','id_agendamento'=>int,'data'=>'Y-m-d'|'','hora'=>'H:i'|'','pagina'=>int]`
  - `utec_whatsapp_agenda_paginar_horarios(array $horas, int $pagina): array` → `['itens'=>string[],'tem_mais'=>bool]`
  - `utec_whatsapp_agenda_rotulo_dia(string $data): string` — `'Sex 25/09'`
  - `utec_whatsapp_agenda_texto_fallback(string $motivo): string`
  - `utec_whatsapp_agenda_texto_confirmar_remarcacao($data, $hora, $prestador)`, `utec_whatsapp_agenda_texto_remarcado($data, $hora, $prestador)`, `utec_whatsapp_agenda_texto_confirmar_cancelamento($data, $hora, $prestador)` → string
  - `utec_notificacoes_tipo_chatbot_agenda(string $acao): string` — `remarcar`→`whatsapp_chatbot_remarcado`, `cancelar`→`whatsapp_chatbot_cancelado`
  - `utec_notificacoes_mensagem_chatbot_agenda(string $acao, string $paciente_nome, array $dados): string` — `$dados` chaves `data_anterior`, `hora_anterior`, `data_nova`, `hora_nova`, `motivo`
  - `utec_whatsapp_template_equipe_nome('remarcar')` → `'agendamento_remarcado_equipe'`
  - `utec_whatsapp_componentes_equipe_template($contexto, 'remarcar')` → body com 4 parâmetros: paciente, profissional, data/hora anterior, data/hora nova (usa `data_anterior`/`hora_anterior` e `data_agenda`/`hora_agenda` de `$contexto`)
  - Config `$config['notificar_remarcacao_equipe_ativo']` (bool)

- [ ] **Step 1: Teste que falha**

Criar `tests/whatsapp_chatbot_agenda_test.php`:

```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertAgendaSame($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- antecedencia
$agora = strtotime('2026-09-22 10:00:30');
assertAgendaSame(24, utec_whatsapp_agenda_antecedencia_horas(), 'antecedencia 24h');
assertAgendaSame('2026-09-23 10:01', utec_whatsapp_agenda_minimo_datetime($agora), 'minimo arredonda para cima');
assertAgendaSame('2026-09-23 10:00', utec_whatsapp_agenda_minimo_datetime(strtotime('2026-09-22 10:00:00')), 'minimo exato');
assertAgendaSame(true, utec_whatsapp_agenda_antecedencia_ok('2026-09-23', '10:00:00', strtotime('2026-09-22 10:00:00')), 'exatamente 24h ok');
assertAgendaSame(false, utec_whatsapp_agenda_antecedencia_ok('2026-09-23', '09:59', strtotime('2026-09-22 10:00:00')), '23h59 nao ok');
assertAgendaSame(false, utec_whatsapp_agenda_antecedencia_ok('', '', $agora), 'data vazia nao ok');

// --- ids
assertAgendaSame('rem:812:d:20260925', utec_whatsapp_agenda_id_dia(812, '2026-09-25'), 'id dia');
assertAgendaSame('rem:812:p:20260925:2', utec_whatsapp_agenda_id_pagina(812, '2026-09-25', 2), 'id pagina');
assertAgendaSame('rem:812:h:202609251430', utec_whatsapp_agenda_id_hora(812, '2026-09-25', '14:30:00'), 'id hora');
assertAgendaSame('rem:812:ok:202609251430', utec_whatsapp_agenda_id_confirmar_remarcacao(812, '2026-09-25', '14:30'), 'id confirmar remarcacao');
assertAgendaSame('rem:812:dias', utec_whatsapp_agenda_id_outros_dias(812), 'id outros dias');
assertAgendaSame('can:812:sem_motivo', utec_whatsapp_agenda_id_sem_motivo(812), 'id sem motivo');
assertAgendaSame('can:812:ok', utec_whatsapp_agenda_id_confirmar_cancelamento(812), 'id confirmar cancelamento');

$p = utec_whatsapp_agenda_parse_id('rem:812:d:20260925');
assertAgendaSame(array('fluxo' => 'remarcar', 'acao' => 'dia', 'id_agendamento' => 812, 'data' => '2026-09-25', 'hora' => '', 'pagina' => 0), $p, 'parse dia');
$p = utec_whatsapp_agenda_parse_id('rem:812:p:20260925:3');
assertAgendaSame('pagina', $p['acao'], 'parse pagina acao');
assertAgendaSame(3, $p['pagina'], 'parse pagina numero');
$p = utec_whatsapp_agenda_parse_id('rem:812:h:202609251430');
assertAgendaSame(array('fluxo' => 'remarcar', 'acao' => 'hora', 'id_agendamento' => 812, 'data' => '2026-09-25', 'hora' => '14:30', 'pagina' => 0), $p, 'parse hora');
$p = utec_whatsapp_agenda_parse_id('rem:812:ok:202609251430');
assertAgendaSame('confirmar', $p['acao'], 'parse confirmar remarcacao');
assertAgendaSame('remarcar', $p['fluxo'], 'parse confirmar remarcacao fluxo');
$p = utec_whatsapp_agenda_parse_id('rem:812:dias');
assertAgendaSame('dias', $p['acao'], 'parse outros dias');
$p = utec_whatsapp_agenda_parse_id('can:812:sem_motivo');
assertAgendaSame(array('fluxo' => 'cancelar', 'acao' => 'sem_motivo', 'id_agendamento' => 812, 'data' => '', 'hora' => '', 'pagina' => 0), $p, 'parse sem motivo');
$p = utec_whatsapp_agenda_parse_id('can:812:ok');
assertAgendaSame('confirmar', $p['acao'], 'parse confirmar cancelamento');
assertAgendaSame('cancelar', $p['fluxo'], 'parse confirmar cancelamento fluxo');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:d:20260231'), 'data inexistente rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:h:202609252460'), 'hora invalida rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:0:dias'), 'id zero rejeitado');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:p:20260925:0'), 'pagina zero rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('chat:paciente:remarcar:812'), 'id do menu nao e da agenda');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('confirmar_agendamento:812'), 'botao do template nao e da agenda');

// --- paginacao
function horasTeste($n) { $h = array(); for ($i = 0; $i < $n; $i++) { $h[] = sprintf('%02d:%02d', 8 + intdiv($i, 2), ($i % 2) * 30); } return $h; }
assertAgendaSame(array('itens' => array(), 'tem_mais' => false), utec_whatsapp_agenda_paginar_horarios(array(), 1), 'paginacao vazia');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(9), 1);
assertAgendaSame(9, count($r['itens']), '9 itens'); assertAgendaSame(false, $r['tem_mais'], '9 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(10), 1);
assertAgendaSame(10, count($r['itens']), '10 cabem'); assertAgendaSame(false, $r['tem_mais'], '10 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(11), 1);
assertAgendaSame(9, count($r['itens']), '11 pagina 1'); assertAgendaSame(true, $r['tem_mais'], '11 tem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(11), 2);
assertAgendaSame(2, count($r['itens']), '11 pagina 2'); assertAgendaSame(false, $r['tem_mais'], '11 pagina 2 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 2);
assertAgendaSame(9, count($r['itens']), '25 pagina 2'); assertAgendaSame(true, $r['tem_mais'], '25 pagina 2 tem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 3);
assertAgendaSame(7, count($r['itens']), '25 pagina 3'); assertAgendaSame(false, $r['tem_mais'], '25 pagina 3 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 4);
assertAgendaSame(array(), $r['itens'], '25 pagina 4 vazia');

// --- rotulos e textos
assertAgendaSame('Sex 25/09', utec_whatsapp_agenda_rotulo_dia('2026-09-25'), 'rotulo sexta');
assertAgendaSame('Dom 27/09', utec_whatsapp_agenda_rotulo_dia('2026-09-27'), 'rotulo domingo');
assertAgendaSame('Sáb 26/09', utec_whatsapp_agenda_rotulo_dia('2026-09-26'), 'rotulo sabado');
assertAgendaSame('Remarcar para Sex 25/09 às 14:30 com Dra. Ana?', utec_whatsapp_agenda_texto_confirmar_remarcacao('2026-09-25', '14:30:00', 'Dra. Ana'), 'texto confirmar remarcacao');
assertAgendaSame('Remarcar para Sex 25/09 às 14:30?', utec_whatsapp_agenda_texto_confirmar_remarcacao('2026-09-25', '14:30', ''), 'texto sem profissional');
assertAgendaSame('Consulta remarcada para Sex 25/09 às 14:30 com Dra. Ana.', utec_whatsapp_agenda_texto_remarcado('2026-09-25', '14:30', 'Dra. Ana'), 'texto remarcado');
assertAgendaSame('Cancelar a consulta de Sex 25/09 às 14:30 com Dra. Ana?', utec_whatsapp_agenda_texto_confirmar_cancelamento('2026-09-25', '14:30', 'Dra. Ana'), 'texto confirmar cancelamento');
assertAgendaSame(true, strpos(utec_whatsapp_agenda_texto_fallback('prazo'), '24 horas') !== false, 'fallback prazo');
assertAgendaSame(true, utec_whatsapp_agenda_texto_fallback('sem_grade') !== '', 'fallback sem grade');
assertAgendaSame(true, strpos(utec_whatsapp_agenda_texto_fallback('sem_vaga'), '30 dias') !== false, 'fallback sem vaga');
assertAgendaSame('', utec_whatsapp_agenda_texto_fallback('outro'), 'fallback desconhecido');

// --- avisos internos
assertAgendaSame('whatsapp_chatbot_remarcado', utec_notificacoes_tipo_chatbot_agenda('remarcar'), 'tipo remarcado');
assertAgendaSame('whatsapp_chatbot_cancelado', utec_notificacoes_tipo_chatbot_agenda('cancelar'), 'tipo cancelado');
assertAgendaSame('', utec_notificacoes_tipo_chatbot_agenda('confirmar'), 'tipo fora do escopo');
assertAgendaSame(
    'Maria remarcou a consulta de 23/09/2026 às 14:00 para 25/09/2026 às 14:30 pelo WhatsApp.',
    utec_notificacoes_mensagem_chatbot_agenda('remarcar', 'Maria', array('data_anterior' => '2026-09-23', 'hora_anterior' => '14:00:00', 'data_nova' => '2026-09-25', 'hora_nova' => '14:30')),
    'mensagem remarcado'
);
assertAgendaSame(
    'Maria cancelou a consulta de 25/09/2026 às 14:30 pelo WhatsApp. Motivo: Viagem',
    utec_notificacoes_mensagem_chatbot_agenda('cancelar', 'Maria', array('data_anterior' => '2026-09-25', 'hora_anterior' => '14:30', 'motivo' => 'Viagem')),
    'mensagem cancelado com motivo'
);
assertAgendaSame(
    'O paciente cancelou a consulta de 25/09/2026 às 14:30 pelo WhatsApp. Sem motivo informado.',
    utec_notificacoes_mensagem_chatbot_agenda('cancelar', '', array('data_anterior' => '2026-09-25', 'hora_anterior' => '14:30', 'motivo' => '')),
    'mensagem cancelado sem motivo'
);

// --- template de remarcacao para a equipe
assertAgendaSame('agendamento_remarcado_equipe', utec_whatsapp_template_equipe_nome('remarcar'), 'template remarcado');
assertAgendaSame('agendamento_cancelado_equipe', utec_whatsapp_template_equipe_nome('cancelar'), 'template cancelado inalterado');
$comp = utec_whatsapp_componentes_equipe_template(array(
    'paciente_nome' => 'Maria', 'prestador_nome' => 'Dra. Ana',
    'data_anterior' => '2026-09-23', 'hora_anterior' => '14:00:00',
    'data_agenda' => '2026-09-25', 'hora_agenda' => '14:30:00',
), 'remarcar');
$textos = array();
foreach ($comp[0]['parameters'] as $par) { $textos[] = $par['text']; }
assertAgendaSame(array('Maria', 'Dra. Ana', '23/09/2026 as 14:00', '25/09/2026 as 14:30'), $textos, 'componentes remarcado');

echo "OK whatsapp_chatbot_agenda_test\n";
```

Nota: `intdiv` existe desde PHP 7.0.

- [ ] **Step 2: Rodar e ver falhar**

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_test.php`
Expected: fatal `Call to undefined function utec_whatsapp_agenda_antecedencia_horas()`.

- [ ] **Step 3: Alterar funções existentes do template da equipe**

Em `utec_whatsapp_template_equipe_nome()`, antes do `return '';` final:

```php
        if ($acao === 'remarcar') {
            return 'agendamento_remarcado_equipe';
        }
```

Em `utec_whatsapp_componentes_equipe_template()`, logo após o bloco `if ($acao === 'confirmar') { ... }` (antes de `$dataBr = ...`):

```php
        if ($acao === 'remarcar') {
            $anterior = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_anterior', ''))
                . ' as ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_anterior', ''));
            $nova = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_agenda', ''))
                . ' as ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_agenda', ''));
            return [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'paciente_nome', 'Paciente'))],
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'prestador_nome', 'Profissional'))],
                        ['type' => 'text', 'text' => $anterior],
                        ['type' => 'text', 'text' => $nova],
                    ],
                ],
            ];
        }
```

- [ ] **Step 4: Acrescentar as funções novas ao fim do helper**

Acrescentar ao fim de `application/helpers/whatsapp_agendamento_helper.php`:

```php
/*
 * Remarcacao / cancelamento automaticos pelo chatbot (perfil paciente).
 * Funcoes puras: ids dos cliques, regra de antecedencia, paginacao e textos.
 */

if (!function_exists('utec_whatsapp_agenda_antecedencia_horas')) {
    function utec_whatsapp_agenda_antecedencia_horas()
    {
        return 24;
    }
}

if (!function_exists('utec_whatsapp_agenda_minimo_datetime')) {
    function utec_whatsapp_agenda_minimo_datetime($agora_ts)
    {
        $ts = (int)$agora_ts + utec_whatsapp_agenda_antecedencia_horas() * 3600;
        $ts = (int)(ceil($ts / 60) * 60);
        return date('Y-m-d H:i', $ts);
    }
}

if (!function_exists('utec_whatsapp_agenda_antecedencia_ok')) {
    function utec_whatsapp_agenda_antecedencia_ok($data, $hora, $agora_ts)
    {
        $data = substr(trim((string)$data), 0, 10);
        $hora = substr(trim((string)$hora), 0, 5);
        if ($data === '' || $hora === '') {
            return false;
        }
        $ts = strtotime($data . ' ' . $hora);
        if ($ts === false) {
            return false;
        }
        return $ts - (int)$agora_ts >= utec_whatsapp_agenda_antecedencia_horas() * 3600;
    }
}

if (!function_exists('utec_whatsapp_agenda_ymd')) {
    function utec_whatsapp_agenda_ymd($data)
    {
        return str_replace('-', '', substr(trim((string)$data), 0, 10));
    }
}

if (!function_exists('utec_whatsapp_agenda_hi')) {
    function utec_whatsapp_agenda_hi($hora)
    {
        return str_replace(':', '', substr(trim((string)$hora), 0, 5));
    }
}

if (!function_exists('utec_whatsapp_agenda_id_dia')) {
    function utec_whatsapp_agenda_id_dia($id, $data)
    {
        return 'rem:' . (int)$id . ':d:' . utec_whatsapp_agenda_ymd($data);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_pagina')) {
    function utec_whatsapp_agenda_id_pagina($id, $data, $pagina)
    {
        return 'rem:' . (int)$id . ':p:' . utec_whatsapp_agenda_ymd($data) . ':' . (int)$pagina;
    }
}

if (!function_exists('utec_whatsapp_agenda_id_hora')) {
    function utec_whatsapp_agenda_id_hora($id, $data, $hora)
    {
        return 'rem:' . (int)$id . ':h:' . utec_whatsapp_agenda_ymd($data) . utec_whatsapp_agenda_hi($hora);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_confirmar_remarcacao')) {
    function utec_whatsapp_agenda_id_confirmar_remarcacao($id, $data, $hora)
    {
        return 'rem:' . (int)$id . ':ok:' . utec_whatsapp_agenda_ymd($data) . utec_whatsapp_agenda_hi($hora);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_outros_dias')) {
    function utec_whatsapp_agenda_id_outros_dias($id)
    {
        return 'rem:' . (int)$id . ':dias';
    }
}

if (!function_exists('utec_whatsapp_agenda_id_sem_motivo')) {
    function utec_whatsapp_agenda_id_sem_motivo($id)
    {
        return 'can:' . (int)$id . ':sem_motivo';
    }
}

if (!function_exists('utec_whatsapp_agenda_id_confirmar_cancelamento')) {
    function utec_whatsapp_agenda_id_confirmar_cancelamento($id)
    {
        return 'can:' . (int)$id . ':ok';
    }
}

if (!function_exists('utec_whatsapp_agenda_data_de_ymd')) {
    function utec_whatsapp_agenda_data_de_ymd($ymd)
    {
        if (!preg_match('/^(\d{4})(\d{2})(\d{2})$/', (string)$ymd, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
            return '';
        }
        return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
}

if (!function_exists('utec_whatsapp_agenda_hora_de_hi')) {
    function utec_whatsapp_agenda_hora_de_hi($hi)
    {
        if (!preg_match('/^(\d{2})(\d{2})$/', (string)$hi, $m) || (int)$m[1] > 23 || (int)$m[2] > 59) {
            return '';
        }
        return $m[1] . ':' . $m[2];
    }
}

if (!function_exists('utec_whatsapp_agenda_parse_id')) {
    function utec_whatsapp_agenda_parse_id($payload)
    {
        $p = trim((string)$payload);
        $base = ['fluxo' => '', 'acao' => '', 'id_agendamento' => 0, 'data' => '', 'hora' => '', 'pagina' => 0];

        if (preg_match('/^rem:(\d+):d:(\d{8})$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'dia', 'id_agendamento' => (int)$m[1], 'data' => utec_whatsapp_agenda_data_de_ymd($m[2])]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '') ? $r : null;
        }
        if (preg_match('/^rem:(\d+):p:(\d{8}):(\d{1,2})$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'pagina', 'id_agendamento' => (int)$m[1], 'data' => utec_whatsapp_agenda_data_de_ymd($m[2]), 'pagina' => (int)$m[3]]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '' && $r['pagina'] >= 1) ? $r : null;
        }
        if (preg_match('/^rem:(\d+):(h|ok):(\d{8})(\d{4})$/', $p, $m)) {
            $r = array_merge($base, [
                'fluxo' => 'remarcar',
                'acao' => $m[2] === 'h' ? 'hora' : 'confirmar',
                'id_agendamento' => (int)$m[1],
                'data' => utec_whatsapp_agenda_data_de_ymd($m[3]),
                'hora' => utec_whatsapp_agenda_hora_de_hi($m[4]),
            ]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '' && $r['hora'] !== '') ? $r : null;
        }
        if (preg_match('/^rem:(\d+):dias$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'dias', 'id_agendamento' => (int)$m[1]]);
            return $r['id_agendamento'] > 0 ? $r : null;
        }
        if (preg_match('/^can:(\d+):(sem_motivo|ok)$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'cancelar', 'acao' => $m[2] === 'ok' ? 'confirmar' : 'sem_motivo', 'id_agendamento' => (int)$m[1]]);
            return $r['id_agendamento'] > 0 ? $r : null;
        }
        return null;
    }
}

if (!function_exists('utec_whatsapp_agenda_paginar_horarios')) {
    function utec_whatsapp_agenda_paginar_horarios($horas, $pagina)
    {
        $horas = array_values((array)$horas);
        $pagina = max(1, (int)$pagina);
        $offset = 0;
        for ($p = 1; $p < $pagina; $p++) {
            if (count($horas) - $offset <= 10) {
                return ['itens' => [], 'tem_mais' => false];
            }
            $offset += 9;
        }
        if (count($horas) - $offset <= 10) {
            return ['itens' => array_slice($horas, $offset), 'tem_mais' => false];
        }
        return ['itens' => array_slice($horas, $offset, 9), 'tem_mais' => true];
    }
}

if (!function_exists('utec_whatsapp_agenda_rotulo_dia')) {
    function utec_whatsapp_agenda_rotulo_dia($data)
    {
        $ts = strtotime(substr(trim((string)$data), 0, 10));
        if ($ts === false) {
            return '';
        }
        $nomes = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        return $nomes[(int)date('w', $ts)] . ' ' . date('d/m', $ts);
    }
}

if (!function_exists('utec_whatsapp_agenda_quando')) {
    function utec_whatsapp_agenda_quando($data, $hora, $prestador)
    {
        $texto = utec_whatsapp_agenda_rotulo_dia($data) . ' às ' . substr(trim((string)$hora), 0, 5);
        $prestador = trim((string)$prestador);
        return $prestador !== '' ? $texto . ' com ' . $prestador : $texto;
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_confirmar_remarcacao')) {
    function utec_whatsapp_agenda_texto_confirmar_remarcacao($data, $hora, $prestador)
    {
        return 'Remarcar para ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '?';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_remarcado')) {
    function utec_whatsapp_agenda_texto_remarcado($data, $hora, $prestador)
    {
        return 'Consulta remarcada para ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '.';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_confirmar_cancelamento')) {
    function utec_whatsapp_agenda_texto_confirmar_cancelamento($data, $hora, $prestador)
    {
        return 'Cancelar a consulta de ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '?';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_fallback')) {
    function utec_whatsapp_agenda_texto_fallback($motivo)
    {
        $textos = [
            'prazo' => 'Faltam menos de 24 horas para a consulta, então a equipe vai analisar seu pedido.',
            'sem_grade' => 'Este profissional ainda não tem horários disponíveis para remarcação pelo WhatsApp.',
            'sem_vaga' => 'Não há horários livres nos próximos 30 dias.',
        ];
        $motivo = trim((string)$motivo);
        return isset($textos[$motivo]) ? $textos[$motivo] : '';
    }
}

if (!function_exists('utec_notificacoes_tipo_chatbot_agenda')) {
    function utec_notificacoes_tipo_chatbot_agenda($acao)
    {
        $acao = strtolower(trim((string)$acao));
        if ($acao === 'remarcar') {
            return 'whatsapp_chatbot_remarcado';
        }
        if ($acao === 'cancelar') {
            return 'whatsapp_chatbot_cancelado';
        }
        return '';
    }
}

if (!function_exists('utec_notificacoes_mensagem_chatbot_agenda')) {
    function utec_notificacoes_mensagem_chatbot_agenda($acao, $paciente_nome, $dados)
    {
        $nome = trim((string)$paciente_nome);
        $nome = $nome !== '' ? $nome : 'O paciente';
        $anterior = utec_whatsapp_formatar_data_br(utec_whatsapp_read($dados, 'data_anterior', ''))
            . ' às ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($dados, 'hora_anterior', ''));
        if (strtolower(trim((string)$acao)) === 'remarcar') {
            $nova = utec_whatsapp_formatar_data_br(utec_whatsapp_read($dados, 'data_nova', ''))
                . ' às ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($dados, 'hora_nova', ''));
            return $nome . ' remarcou a consulta de ' . $anterior . ' para ' . $nova . ' pelo WhatsApp.';
        }
        $motivo = trim((string)utec_whatsapp_read($dados, 'motivo', ''));
        $sufixo = $motivo !== '' ? ' Motivo: ' . utec_whatsapp_truncar_texto($motivo, 300) : ' Sem motivo informado.';
        return $nome . ' cancelou a consulta de ' . $anterior . ' pelo WhatsApp.' . $sufixo;
    }
}
```

- [ ] **Step 5: Flag**

Acrescentar ao fim de `application/config/whatsapp.php`:

```php

$whatsapp_env_remarcacao_equipe = getenv('WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE');
// Aviso WhatsApp a profissional/atendente quando o paciente remarca pelo chatbot.
// Manter FALSE ate o template 'agendamento_remarcado_equipe' ser aprovado na Meta
// (ver docs/whatsapp-remarcacao-template-pendente.md). Ligar com =1 no ambiente.
$config['notificar_remarcacao_equipe_ativo'] = ($whatsapp_env_remarcacao_equipe === '1' || $whatsapp_env_remarcacao_equipe === 'true');
```

- [ ] **Step 6: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_test.php` → `OK whatsapp_chatbot_agenda_test`
Run: `for t in tests/whatsapp_*test.php; do /c/PHP/PHP7.2/php.exe "$t" >/dev/null || echo "FALHOU $t"; done` → nenhuma linha `FALHOU`.
Run: `/c/PHP/PHP7.2/php.exe -l` no helper e na config → sem erros.

- [ ] **Step 7: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php application/config/whatsapp.php tests/whatsapp_chatbot_agenda_test.php
git commit -m "feat(chatbot): funcoes puras de remarcacao e cancelamento automaticos

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 3: Gravação atômica, aviso interno e WhatsApp à equipe

**Files:**
- Modify: `application/models/Whatsapp_model.php`
- Modify: `application/controllers/adm/Atendimento.php` (a única ocorrência de `NOT IN ('equipe_confirmado', 'equipe_cancelado')`)
- Modify: `application/models/Notificacoes_model.php`
- Modify: `application/libraries/Whatsapp_agendamento.php` (`notificar_equipe`)
- Modify: `tests/whatsapp_notificar_equipe_source_test.php:47`
- Test: `tests/whatsapp_chatbot_agenda_source_test.php` (criar)

**Interfaces:**
- Consumes: `Disponibilidade_model::verificar_horario($id_prestador, $data, $hora, $ignorar)` (existente); helper da Task 2.
- Produces:
  - `Whatsapp_model::remarcar_agendamento_chatbot($id_agendamento, $id_paciente, $data, $hora, $minimo_datetime, $telefone = '')` e `Whatsapp_model::cancelar_agendamento_chatbot($id_agendamento, $id_paciente, $minimo_datetime, $telefone = '')` → `['ok'=>bool,'ja_estava'=>bool,'motivo_falha'=>''|'prazo'|'ocupado'|'indisponivel'|'erro','agendamento'=>object|null,'anterior'=>object|null,'id_log'=>int]`. `anterior`/`agendamento` têm `id, id_paciente, id_prestador, id_user, data_agenda, hora_agenda, tipo, status, paciente_nome, prestador_nome, tenant_id`.
  - `Notificacoes_model::criar_aviso_chatbot_agenda($contexto, $acao, $dados, $id_evento)` → bool. `$contexto`: `tenant_id, id_agendamento, id_paciente, id_user, id_prestador, paciente_nome, id_whatsapp_notificacao`. `$dados`: como em `utec_notificacoes_mensagem_chatbot_agenda`.
  - `Whatsapp_agendamento::notificar_equipe($contexto, 'remarcar')` — só envia com o flag ligado; usa `data_anterior`/`hora_anterior` de `$contexto`.

- [ ] **Step 1: Teste de fonte que falha**

Criar `tests/whatsapp_chatbot_agenda_source_test.php`:

```php
<?php
function assertFonteContem($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function assertFonteNaoContem($needle, $haystack, $label) {
    if (strpos($haystack, $needle) !== false) {
        fwrite(STDERR, $label . ' — trecho nao deveria existir: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerFonte($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) { fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL); exit(1); }
    return file_get_contents($path);
}

$model = lerFonte('application/models/Whatsapp_model.php');
assertFonteContem('function remarcar_agendamento_chatbot(', $model, 'model remarcar');
assertFonteContem('function cancelar_agendamento_chatbot(', $model, 'model cancelar');
assertFonteContem('FOR UPDATE', $model, 'model trava linhas');
assertFonteContem('verificar_horario(', $model, 'model revalida horario');
assertFonteContem("'chatbot_remarcado'", $model, 'log remarcado');
assertFonteContem("'chatbot_cancelado'", $model, 'log cancelado');
assertFonteContem('trans_begin()', $model, 'model usa transacao');
assertFonteNaoContem("NOT IN ('equipe_confirmado', 'equipe_cancelado')", $model, 'model exclui equipe_remarcado');
assertFonteContem("NOT IN ('equipe_confirmado', 'equipe_cancelado', 'equipe_remarcado')", $model, 'model exclusao atualizada');

$atend = lerFonte('application/controllers/adm/Atendimento.php');
assertFonteContem("NOT IN ('equipe_confirmado', 'equipe_cancelado', 'equipe_remarcado')", $atend, 'agenda exclusao atualizada');

$notif = lerFonte('application/models/Notificacoes_model.php');
assertFonteContem('function criar_aviso_chatbot_agenda(', $notif, 'aviso interno chatbot agenda');
assertFonteContem('utec_notificacoes_mensagem_chatbot_agenda(', $notif, 'aviso usa mensagem pura');

$lib = lerFonte('application/libraries/Whatsapp_agendamento.php');
assertFonteContem("notificar_remarcacao_equipe_ativo", $lib, 'notificar_equipe respeita flag de remarcacao');
assertFonteContem("'equipe_remarcado'", $lib, 'notificar_equipe loga equipe_remarcado');

$cfg = lerFonte('application/config/whatsapp.php');
assertFonteContem("notificar_remarcacao_equipe_ativo", $cfg, 'flag na config');

echo "OK whatsapp_chatbot_agenda_source_test\n";
```

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_source_test.php` → FAIL `model remarcar — trecho ausente`.

- [ ] **Step 2: Exclusão de `equipe_remarcado`**

Substituir a string `NOT IN ('equipe_confirmado', 'equipe_cancelado')` por `NOT IN ('equipe_confirmado', 'equipe_cancelado', 'equipe_remarcado')` em:
- `application/models/Whatsapp_model.php` (3 ocorrências: `get_notificacao_por_agendamento`, `listar_agendamentos_chatbot`, `obter_agendamento_chatbot`)
- `application/controllers/adm/Atendimento.php` (1 ocorrência; localizar com grep — o arquivo mudou recentemente)
- `tests/whatsapp_notificar_equipe_source_test.php:47` (a variável `$exclusaoEquipe`)

Conferir com `grep -rn "equipe_cancelado')" application tests` que não sobrou ocorrência antiga.

- [ ] **Step 3: Gravação atômica no `Whatsapp_model`**

Acrescentar antes do método `tabela_log_existe()`:

```php
    public function remarcar_agendamento_chatbot($id_agendamento, $id_paciente, $data, $hora, $minimo_datetime, $telefone = '')
    {
        $resultado = ['ok' => false, 'ja_estava' => false, 'motivo_falha' => 'indisponivel', 'agendamento' => null, 'anterior' => null, 'id_log' => 0];
        $data = substr(trim((string)$data), 0, 10);
        $hora = substr(trim((string)$hora), 0, 5);
        $minimoTs = strtotime((string)$minimo_datetime);
        if ((int)$id_agendamento <= 0 || (int)$id_paciente <= 0 || $minimoTs === false
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || !preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return $resultado;
        }
        $this->load->model('Disponibilidade_model', 'disponibilidade_model');

        $this->db->trans_begin();
        $atual = $this->agendamento_chatbot_travado($id_agendamento, $id_paciente);
        if (!$atual) {
            $this->db->trans_rollback();
            return $resultado;
        }
        $resultado['anterior'] = $atual;
        $dataAtual = substr((string)$atual->data_agenda, 0, 10);
        $horaAtual = substr((string)$atual->hora_agenda, 0, 5);

        if ((int)$atual->status === 0 && $dataAtual === $data && $horaAtual === $hora) {
            $this->db->trans_rollback();
            $resultado['ok'] = true;
            $resultado['ja_estava'] = true;
            $resultado['motivo_falha'] = '';
            $resultado['agendamento'] = $atual;
            return $resultado;
        }
        if ((int)$atual->status !== 0) {
            $this->db->trans_rollback();
            return $resultado;
        }
        if (strtotime($dataAtual.' '.$horaAtual) < $minimoTs) {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'prazo';
            return $resultado;
        }
        if (strtotime($data.' '.$hora) < $minimoTs) {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'ocupado';
            return $resultado;
        }

        // Serializa remarcacoes do chatbot por profissional.
        $this->db->query("SELECT id FROM `usuarios` WHERE id = ".(int)$atual->id_prestador." FOR UPDATE");
        $verificacao = $this->disponibilidade_model->verificar_horario((int)$atual->id_prestador, $data, $hora, (int)$atual->id);
        if (empty($verificacao['tem_grade']) || $verificacao['situacao'] !== 'livre') {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'ocupado';
            return $resultado;
        }

        $update = ['data_agenda' => $data, 'hora_agenda' => $hora, 'data_hora_agenda' => $data.' '.$hora, 'status' => 0];
        if ($this->db->field_exists('id_user_alt', 'agendamentos')) {
            $update['id_user_alt'] = (int)$id_paciente;
        }
        $this->db->where('id', (int)$atual->id);
        $this->db->update('agendamentos', $update);

        $this->registrar_log([
            'id_agendamento' => (int)$atual->id,
            'tenant_id' => (int)$atual->tenant_id,
            'telefone_destino' => utec_whatsapp_normalizar_numero($telefone),
            'status_envio' => 'enviado',
            'status_confirmacao' => 'confirmado',
            'tipo_notificacao' => 'chatbot_remarcado',
            'respondido_em' => date('Y-m-d H:i:s'),
        ]);
        $idLog = (int)$this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'erro';
            return $resultado;
        }
        $this->db->trans_commit();

        $novo = clone $atual;
        $novo->data_agenda = $data;
        $novo->hora_agenda = $hora;
        $resultado['ok'] = true;
        $resultado['motivo_falha'] = '';
        $resultado['agendamento'] = $novo;
        $resultado['id_log'] = $idLog;
        return $resultado;
    }

    public function cancelar_agendamento_chatbot($id_agendamento, $id_paciente, $minimo_datetime, $telefone = '')
    {
        $resultado = ['ok' => false, 'ja_estava' => false, 'motivo_falha' => 'indisponivel', 'agendamento' => null, 'anterior' => null, 'id_log' => 0];
        $minimoTs = strtotime((string)$minimo_datetime);
        if ((int)$id_agendamento <= 0 || (int)$id_paciente <= 0 || $minimoTs === false) {
            return $resultado;
        }

        $this->db->trans_begin();
        $atual = $this->agendamento_chatbot_travado($id_agendamento, $id_paciente);
        if (!$atual) {
            $this->db->trans_rollback();
            return $resultado;
        }
        $resultado['anterior'] = $atual;
        if ((int)$atual->status === 3) {
            $this->db->trans_rollback();
            $resultado['ok'] = true;
            $resultado['ja_estava'] = true;
            $resultado['motivo_falha'] = '';
            $resultado['agendamento'] = $atual;
            return $resultado;
        }
        if ((int)$atual->status !== 0) {
            $this->db->trans_rollback();
            return $resultado;
        }
        if (strtotime(substr((string)$atual->data_agenda, 0, 10).' '.substr((string)$atual->hora_agenda, 0, 5)) < $minimoTs) {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'prazo';
            return $resultado;
        }

        $update = ['status' => 3];
        if ($this->db->field_exists('id_user_alt', 'agendamentos')) {
            $update['id_user_alt'] = (int)$id_paciente;
        }
        $this->db->where('id', (int)$atual->id);
        $this->db->update('agendamentos', $update);

        $this->registrar_log([
            'id_agendamento' => (int)$atual->id,
            'tenant_id' => (int)$atual->tenant_id,
            'telefone_destino' => utec_whatsapp_normalizar_numero($telefone),
            'status_envio' => 'enviado',
            'status_confirmacao' => 'cancelado',
            'tipo_notificacao' => 'chatbot_cancelado',
            'respondido_em' => date('Y-m-d H:i:s'),
        ]);
        $idLog = (int)$this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $resultado['motivo_falha'] = 'erro';
            return $resultado;
        }
        $this->db->trans_commit();

        $novo = clone $atual;
        $novo->status = 3;
        $resultado['ok'] = true;
        $resultado['motivo_falha'] = '';
        $resultado['agendamento'] = $novo;
        $resultado['id_log'] = $idLog;
        return $resultado;
    }

    private function agendamento_chatbot_travado($id_agendamento, $id_paciente)
    {
        if (!$this->tabela_possui_campos('agendamentos', ['id', 'id_paciente', 'id_prestador', 'id_user', 'data_agenda', 'hora_agenda', 'status'])) {
            return null;
        }
        $tenantSelect = $this->db->field_exists('tenant_id', 'usuarios') ? 'COALESCE(p.tenant_id, 0)' : '0';
        $query = $this->db->query(
            "SELECT a.id, a.id_paciente, a.id_prestador, a.id_user, a.data_agenda, a.hora_agenda, a.tipo, a.status,"
            ." p.nome AS paciente_nome, pr.nome AS prestador_nome, {$tenantSelect} AS tenant_id"
            ." FROM `agendamentos` a LEFT JOIN `usuarios` p ON p.id = a.id_paciente LEFT JOIN `usuarios` pr ON pr.id = a.id_prestador"
            ." WHERE a.id = ".(int)$id_agendamento." AND a.id_paciente = ".(int)$id_paciente." LIMIT 1 FOR UPDATE"
        );
        return $query && $query->num_rows() ? $query->row() : null;
    }
```

- [ ] **Step 4: Aviso interno no `Notificacoes_model`**

Acrescentar após `criar_solicitacao_chatbot()`:

```php
    public function criar_aviso_chatbot_agenda($contexto, $acao, $dados, $id_evento)
    {
        $tipo = utec_notificacoes_tipo_chatbot_agenda($acao);
        if ($tipo === '' || !$this->tabela_possui_campos([
            'tenant_id', 'id_usuario_destino', 'id_agendamento', 'id_whatsapp_notificacao',
            'id_whatsapp_chatbot_evento', 'tipo', 'titulo', 'mensagem', 'url', 'lida', 'criado_em'
        ])) {
            return false;
        }

        $idAgendamento = (int)utec_whatsapp_read($contexto, 'id_agendamento', 0);
        $idPaciente = (int)utec_whatsapp_read($contexto, 'id_paciente', 0);
        // Id da linha chatbot_* do log: unico por acao, evita colisao na chave (usuario, id_whatsapp_notificacao, tipo).
        $idLog = (int)utec_whatsapp_read($contexto, 'id_whatsapp_notificacao', 0);
        if ($idAgendamento <= 0 || $idLog <= 0) {
            return false;
        }

        $destinatarios = utec_notificacoes_destinatarios_agendamento(
            (int)utec_whatsapp_read($contexto, 'id_user', 0),
            (int)utec_whatsapp_read($contexto, 'id_prestador', 0)
        );
        if (empty($destinatarios)) {
            return true;
        }

        $titulo = strtolower(trim((string)$acao)) === 'remarcar' ? 'Consulta remarcada pelo paciente' : 'Consulta cancelada pelo paciente';
        $mensagem = utec_notificacoes_mensagem_chatbot_agenda($acao, utec_whatsapp_read($contexto, 'paciente_nome', ''), $dados);
        $url = $idPaciente > 0 ? 'adm/usuarios/prontuario/'.$idPaciente.'/'.$idAgendamento : 'adm/atendimento';
        $tenantId = (int)utec_whatsapp_read($contexto, 'tenant_id', 0);

        foreach ($destinatarios as $idUsuario) {
            $sql = "INSERT IGNORE INTO `{$this->table}`\n"
                . '(tenant_id, id_usuario_destino, id_agendamento, id_whatsapp_notificacao, id_whatsapp_chatbot_evento, tipo, titulo, mensagem, url, lida, criado_em) VALUES ('
                . $tenantId.', '.(int)$idUsuario.', '.$idAgendamento.', '.$idLog.', '.(int)$id_evento.', '
                . $this->db->escape($tipo).', '.$this->db->escape($titulo).', '
                . $this->db->escape($mensagem).', '.$this->db->escape($url).", 0, '".date('Y-m-d H:i:s')."')";
            if ($this->db->query($sql) === false) {
                return false;
            }
        }

        return true;
    }
```

- [ ] **Step 5: `notificar_equipe` com `remarcar`**

Em `application/libraries/Whatsapp_agendamento.php`, método `notificar_equipe()`:

a) Logo após o bloco `if (!$this->CI->config->item('notificar_equipe_ativo', 'whatsapp')) { return $resumo; }`, acrescentar:

```php
        if ($acao === 'remarcar' && !$this->CI->config->item('notificar_remarcacao_equipe_ativo', 'whatsapp')) {
            return $resumo;
        }
```

b) Substituir a linha
`$tipoNotificacao = $acao === 'cancelar' ? 'equipe_cancelado' : 'equipe_confirmado';`
por:

```php
        $tipoNotificacao = $acao === 'cancelar' ? 'equipe_cancelado' : ($acao === 'remarcar' ? 'equipe_remarcado' : 'equipe_confirmado');
        if ($acao === 'remarcar') {
            $agendamento->data_anterior = utec_whatsapp_read($contexto, 'data_anterior', '');
            $agendamento->hora_anterior = utec_whatsapp_read($contexto, 'hora_anterior', '');
        }
```

(A linha seguinte, `$componentes = utec_whatsapp_componentes_equipe_template($agendamento, $acao);`, já passa `$agendamento` — agora com `data_anterior`/`hora_anterior`.)

- [ ] **Step 6: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_source_test.php` → `OK whatsapp_chatbot_agenda_source_test`
Run: `for t in tests/whatsapp_*test.php tests/disponibilidade_*test.php; do /c/PHP/PHP7.2/php.exe "$t" >/dev/null || echo "FALHOU $t"; done` → nenhuma linha `FALHOU`.
Run: `/c/PHP/PHP7.2/php.exe -l` nos 4 arquivos PHP de aplicação alterados → sem erros.

- [ ] **Step 7: Commit**

```bash
git add application/models/Whatsapp_model.php application/controllers/adm/Atendimento.php application/models/Notificacoes_model.php application/libraries/Whatsapp_agendamento.php tests/whatsapp_chatbot_agenda_source_test.php tests/whatsapp_notificar_equipe_source_test.php
git commit -m "feat(chatbot): gravacao atomica de remarcacao/cancelamento e avisos a equipe

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 4: Library `Whatsapp_chatbot_agenda` + roteamento no `Whatsapp_chatbot`

**Files:**
- Create: `application/libraries/Whatsapp_chatbot_agenda.php`
- Modify: `application/libraries/Whatsapp_chatbot.php`
- Test: `tests/whatsapp_chatbot_agenda_library_test.php` (criar)

**Interfaces:**
- Consumes: helper (Task 2), `Disponibilidade_model::dias_com_vaga()` / `horarios_livres(..., $minimo)` (Task 1), `Whatsapp_model::remarcar_agendamento_chatbot()` / `cancelar_agendamento_chatbot()` / `obter_agendamento_chatbot()` / sessão (Task 3 e existentes), `Notificacoes_model::criar_aviso_chatbot_agenda()`, `Whatsapp_agendamento::notificar_equipe()` / `enviar_chatbot()`.
- Produces (`$this->CI->whatsapp_chatbot_agenda`):
  - `public $agora` (timestamp fixo em testes; `null` = `time()`)
  - `iniciar($perfil, $acao, $agendamento, $evento)` → resultado de envio, ou `['fallback'=>'prazo'|'sem_grade'|'sem_vaga','acao'=>..., 'id_agendamento'=>int]`
  - `processar_clique($perfil, $evento, $idEvento)` → resultado, `['fallback'=>...]` ou `['expirado'=>true]`
  - `receber_motivo($perfil, $sessao, $evento)` → resultado ou `['expirado'=>true]`
  - Resultado de envio: `['processado'=>bool,'reason'=>string,'wamid'=>string,'error'=>string]` + `id_agendamento` quando aplicável (mesmo formato de `Whatsapp_chatbot::responder_payload`).

- [ ] **Step 1: Teste de comportamento que falha**

Criar `tests/whatsapp_chatbot_agenda_library_test.php`:

```php
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
```

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_library_test.php`
Expected: fatal `failed to open stream ... Whatsapp_chatbot_agenda.php`.

- [ ] **Step 2: Criar a library**

`application/libraries/Whatsapp_chatbot_agenda.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Remarcacao e cancelamento automaticos pelo chatbot (perfil paciente).
 * Whatsapp_chatbot encaminha para ca a escolha da consulta, os cliques rem:/can:
 * e o texto do motivo na sessao agenda_cancelar/motivo.
 * Retorna o resultado do envio ou, para o chatbot tratar:
 *   ['fallback' => 'prazo|sem_grade|sem_vaga', 'acao' => ..., 'id_agendamento' => ...]
 *   ['expirado' => true]
 */
class Whatsapp_chatbot_agenda {

    protected $CI;
    public $agora = null;

    public function __construct($ci = null)
    {
        $this->CI = $ci ?: get_instance();
        if (!isset($this->CI->whatsapp_model)) {
            $this->CI->load->model('Whatsapp_model', 'whatsapp_model');
        }
        if (!isset($this->CI->disponibilidade_model)) {
            $this->CI->load->model('Disponibilidade_model', 'disponibilidade_model');
        }
        if (!isset($this->CI->notificacoes_model)) {
            $this->CI->load->model('Notificacoes_model', 'notificacoes_model');
        }
        if (!isset($this->CI->whatsapp_agendamento)) {
            $this->CI->load->library('whatsapp_agendamento');
        }
        if (isset($this->CI->load)) {
            $this->CI->load->helper('whatsapp_agendamento');
        }
    }

    public function iniciar($perfil, $acao, $agendamento, $evento)
    {
        $acao = $acao === 'cancelar' ? 'cancelar' : 'remarcar';
        if (!utec_whatsapp_agenda_antecedencia_ok($agendamento->data_agenda, $agendamento->hora_agenda, $this->agora())) {
            return $this->fallback('prazo', $acao, $agendamento);
        }
        if ($acao === 'cancelar') {
            $this->salvar_sessao($perfil, 'agenda_cancelar', 'motivo', ['id_agendamento' => (int)$agendamento->id], $evento);
            return $this->pedir_motivo($perfil, $agendamento);
        }
        return $this->enviar_dias($perfil, $agendamento, $evento, '');
    }

    public function processar_clique($perfil, $evento, $idEvento)
    {
        $clique = utec_whatsapp_agenda_parse_id(utec_whatsapp_read($evento, 'payload', ''));
        if (!$clique) {
            return ['expirado' => true];
        }
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot(
            $clique['id_agendamento'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']
        );
        if ($agendamento && $clique['fluxo'] === 'cancelar' && $clique['acao'] === 'confirmar' && (string)utec_whatsapp_read($agendamento, 'status', '') === '3') {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            return $this->texto($perfil, 'Essa consulta já está cancelada.', (int)$agendamento->id);
        }
        if (!$this->elegivel($agendamento, $perfil)) {
            return ['expirado' => true];
        }
        if (!utec_whatsapp_agenda_antecedencia_ok($agendamento->data_agenda, $agendamento->hora_agenda, $this->agora())) {
            return $this->fallback('prazo', $clique['fluxo'], $agendamento);
        }

        if ($clique['fluxo'] === 'cancelar') {
            if ($clique['acao'] === 'sem_motivo') {
                $this->salvar_sessao($perfil, 'agenda_cancelar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'motivo' => ''], $evento);
                return $this->pedir_confirmacao_cancelamento($perfil, $agendamento);
            }
            return $this->confirmar_cancelamento($perfil, $agendamento, $idEvento);
        }

        switch ($clique['acao']) {
            case 'dia':
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], 1, $evento, '');
            case 'pagina':
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], $clique['pagina'], $evento, '');
            case 'dias':
                return $this->enviar_dias($perfil, $agendamento, $evento, '');
            case 'hora':
                $this->salvar_sessao($perfil, 'agenda_remarcar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'data' => $clique['data'], 'hora' => $clique['hora']], $evento);
                return $this->botoes($perfil, utec_whatsapp_agenda_texto_confirmar_remarcacao($clique['data'], $clique['hora'], utec_whatsapp_read($agendamento, 'prestador_nome', '')), [
                    ['id' => utec_whatsapp_agenda_id_confirmar_remarcacao($agendamento->id, $clique['data'], $clique['hora']), 'title' => 'Confirmar'],
                    ['id' => utec_whatsapp_agenda_id_outros_dias($agendamento->id), 'title' => 'Outro dia'],
                    ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
                ], (int)$agendamento->id);
            case 'confirmar':
                return $this->confirmar_remarcacao($perfil, $agendamento, $clique, $evento, $idEvento);
        }
        return ['expirado' => true];
    }

    public function receber_motivo($perfil, $sessao, $evento)
    {
        $dados = json_decode((string)utec_whatsapp_read($sessao, 'dados_json', '{}'), true);
        $dados = is_array($dados) ? $dados : [];
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot(
            (int)utec_whatsapp_read($dados, 'id_agendamento', 0), $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']
        );
        if (!$this->elegivel($agendamento, $perfil)) {
            return ['expirado' => true];
        }
        $motivo = trim((string)utec_whatsapp_read($evento, 'text', ''));
        if ($this->tamanho($motivo) < 3) {
            return $this->pedir_motivo($perfil, $agendamento);
        }
        $this->salvar_sessao($perfil, 'agenda_cancelar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'motivo' => $motivo], $evento);
        return $this->pedir_confirmacao_cancelamento($perfil, $agendamento);
    }

    protected function enviar_dias($perfil, $agendamento, $evento, $prefixo)
    {
        $resposta = $this->CI->disponibilidade_model->dias_com_vaga(
            (int)$agendamento->id_prestador, $this->minimo(), 30, 10, (int)$agendamento->id
        );
        if (empty($resposta['tem_grade'])) {
            return $this->fallback('sem_grade', 'remarcar', $agendamento);
        }
        if (empty($resposta['dias'])) {
            return $this->fallback('sem_vaga', 'remarcar', $agendamento);
        }
        $rows = [];
        foreach ($resposta['dias'] as $dia) {
            $qtd = (int)$dia['qtd'];
            $rows[] = [
                'id' => utec_whatsapp_agenda_id_dia($agendamento->id, $dia['data']),
                'title' => utec_whatsapp_agenda_rotulo_dia($dia['data']),
                'description' => $qtd === 1 ? '1 horário livre' : $qtd.' horários livres',
            ];
        }
        $this->salvar_sessao($perfil, 'agenda_remarcar', 'dia', ['id_agendamento' => (int)$agendamento->id], $evento);
        $corpo = trim($prefixo.' Escolha o novo dia da consulta.');
        return $this->lista($perfil, $corpo, 'Ver dias', $rows, (int)$agendamento->id);
    }

    protected function enviar_horarios($perfil, $agendamento, $data, $pagina, $evento, $prefixo)
    {
        $resposta = $this->CI->disponibilidade_model->horarios_livres(
            (int)$agendamento->id_prestador, $data, (int)$agendamento->id, $this->minimo()
        );
        $livres = isset($resposta['livres']) ? $resposta['livres'] : [];
        $paginado = utec_whatsapp_agenda_paginar_horarios($livres, $pagina);
        if (empty($paginado['itens'])) {
            return $this->enviar_dias($perfil, $agendamento, $evento, 'Esse dia não tem mais horários livres.');
        }
        $rows = [];
        foreach ($paginado['itens'] as $hora) {
            $rows[] = ['id' => utec_whatsapp_agenda_id_hora($agendamento->id, $data, $hora), 'title' => $hora];
        }
        if ($paginado['tem_mais']) {
            $rows[] = ['id' => utec_whatsapp_agenda_id_pagina($agendamento->id, $data, (int)$pagina + 1), 'title' => 'Ver mais horários'];
        }
        $this->salvar_sessao($perfil, 'agenda_remarcar', 'hora', ['id_agendamento' => (int)$agendamento->id, 'data' => $data], $evento);
        $corpo = trim($prefixo.' Horários livres em '.utec_whatsapp_agenda_rotulo_dia($data).'.');
        return $this->lista($perfil, $corpo, 'Ver horários', $rows, (int)$agendamento->id);
    }

    protected function confirmar_remarcacao($perfil, $agendamento, $clique, $evento, $idEvento)
    {
        $r = $this->CI->whatsapp_model->remarcar_agendamento_chatbot(
            (int)$agendamento->id, (int)$perfil['id_usuario'], $clique['data'], $clique['hora'], $this->minimo(), $perfil['telefone']
        );
        if (empty($r['ok'])) {
            $falha = (string)utec_whatsapp_read($r, 'motivo_falha', 'erro');
            if ($falha === 'ocupado') {
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], 1, $evento, 'Esse horário acabou de ser ocupado. Escolha outro.');
            }
            if ($falha === 'prazo') {
                return $this->fallback('prazo', 'remarcar', $agendamento);
            }
            if ($falha === 'erro') {
                return $this->texto($perfil, 'Não foi possível remarcar agora. Tente novamente em instantes.', (int)$agendamento->id);
            }
            return ['expirado' => true];
        }

        $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
        $novo = utec_whatsapp_read($r, 'agendamento', $agendamento);
        $prestador = utec_whatsapp_read($agendamento, 'prestador_nome', '');
        if (!empty($r['ja_estava'])) {
            return $this->texto($perfil, 'Sua consulta já está marcada para '.utec_whatsapp_agenda_quando($novo->data_agenda, $novo->hora_agenda, $prestador).'.', (int)$agendamento->id);
        }
        $this->avisar_equipe('remarcar', $agendamento, $r, '', $idEvento);
        return $this->texto($perfil, utec_whatsapp_agenda_texto_remarcado($novo->data_agenda, $novo->hora_agenda, $prestador), (int)$agendamento->id);
    }

    protected function confirmar_cancelamento($perfil, $agendamento, $idEvento)
    {
        $motivo = '';
        $sessao = $this->CI->whatsapp_model->obter_sessao_chatbot($perfil['telefone']);
        if ($sessao && utec_whatsapp_read($sessao, 'fluxo', '') === 'agenda_cancelar') {
            $dados = json_decode((string)utec_whatsapp_read($sessao, 'dados_json', '{}'), true);
            if (is_array($dados) && (int)utec_whatsapp_read($dados, 'id_agendamento', 0) === (int)$agendamento->id) {
                $motivo = trim((string)utec_whatsapp_read($dados, 'motivo', ''));
            }
        }

        $r = $this->CI->whatsapp_model->cancelar_agendamento_chatbot((int)$agendamento->id, (int)$perfil['id_usuario'], $this->minimo(), $perfil['telefone']);
        if (empty($r['ok'])) {
            $falha = (string)utec_whatsapp_read($r, 'motivo_falha', 'erro');
            if ($falha === 'prazo') {
                return $this->fallback('prazo', 'cancelar', $agendamento);
            }
            if ($falha === 'erro') {
                return $this->texto($perfil, 'Não foi possível cancelar agora. Tente novamente em instantes.', (int)$agendamento->id);
            }
            return ['expirado' => true];
        }

        $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
        if (!empty($r['ja_estava'])) {
            return $this->texto($perfil, 'Essa consulta já está cancelada.', (int)$agendamento->id);
        }
        $this->avisar_equipe('cancelar', $agendamento, $r, $motivo, $idEvento);
        return $this->texto($perfil, 'Consulta cancelada. Se quiser remarcar depois, é só chamar aqui.', (int)$agendamento->id);
    }

    protected function avisar_equipe($acao, $agendamento, $r, $motivo, $idEvento)
    {
        $anterior = utec_whatsapp_read($r, 'anterior', null) ?: $agendamento;
        $novo = utec_whatsapp_read($r, 'agendamento', null) ?: $agendamento;
        $contexto = [
            'tenant_id' => (int)utec_whatsapp_read($anterior, 'tenant_id', 0),
            'id_agendamento' => (int)$agendamento->id,
            'id_paciente' => (int)utec_whatsapp_read($agendamento, 'id_paciente', 0),
            'id_user' => (int)utec_whatsapp_read($agendamento, 'id_user', 0),
            'id_prestador' => (int)utec_whatsapp_read($agendamento, 'id_prestador', 0),
            'paciente_nome' => utec_whatsapp_read($agendamento, 'paciente_nome', ''),
            'id_whatsapp_notificacao' => (int)utec_whatsapp_read($r, 'id_log', 0),
            'data_anterior' => substr((string)utec_whatsapp_read($anterior, 'data_agenda', ''), 0, 10),
            'hora_anterior' => substr((string)utec_whatsapp_read($anterior, 'hora_agenda', ''), 0, 5),
        ];
        $dados = [
            'data_anterior' => $contexto['data_anterior'],
            'hora_anterior' => $contexto['hora_anterior'],
            'data_nova' => substr((string)utec_whatsapp_read($novo, 'data_agenda', ''), 0, 10),
            'hora_nova' => substr((string)utec_whatsapp_read($novo, 'hora_agenda', ''), 0, 5),
            'motivo' => (string)$motivo,
        ];
        try {
            $this->CI->notificacoes_model->criar_aviso_chatbot_agenda($contexto, $acao, $dados, (int)$idEvento);
        } catch (Exception $e) {
            $this->log('aviso interno falhou: '.$e->getMessage());
        }
        try {
            $this->CI->whatsapp_agendamento->notificar_equipe($contexto, $acao);
        } catch (Exception $e) {
            $this->log('whatsapp equipe falhou: '.$e->getMessage());
        }
    }

    protected function pedir_motivo($perfil, $agendamento)
    {
        return $this->botoes($perfil, 'Se quiser, conte em uma mensagem o motivo do cancelamento. Ou toque em "Prefiro não informar".', [
            ['id' => utec_whatsapp_agenda_id_sem_motivo($agendamento->id), 'title' => 'Prefiro não informar'],
            ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
        ], (int)$agendamento->id);
    }

    protected function pedir_confirmacao_cancelamento($perfil, $agendamento)
    {
        return $this->botoes($perfil, utec_whatsapp_agenda_texto_confirmar_cancelamento($agendamento->data_agenda, $agendamento->hora_agenda, utec_whatsapp_read($agendamento, 'prestador_nome', '')), [
            ['id' => utec_whatsapp_agenda_id_confirmar_cancelamento($agendamento->id), 'title' => 'Sim, cancelar'],
            ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
        ], (int)$agendamento->id);
    }

    protected function fallback($motivo, $acao, $agendamento)
    {
        return ['fallback' => $motivo, 'acao' => $acao === 'cancelar' ? 'cancelar' : 'remarcar', 'id_agendamento' => (int)$agendamento->id];
    }

    protected function elegivel($agendamento, $perfil)
    {
        return $perfil['perfil'] === 'paciente' && $agendamento && (string)utec_whatsapp_read($agendamento, 'status', '') === '0';
    }

    protected function salvar_sessao($perfil, $fluxo, $etapa, $dados, $evento)
    {
        return $this->CI->whatsapp_model->salvar_sessao_chatbot(
            $perfil['telefone'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id'], $fluxo, $etapa, $dados,
            utec_whatsapp_read($evento, 'event_at', null), utec_whatsapp_read($evento, 'message_id', '')
        );
    }

    protected function lista($perfil, $corpo, $botao, $rows, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_lista('', 'Remarcar consulta', $corpo, $botao, [['rows' => $rows]]), $idAgendamento);
    }

    protected function botoes($perfil, $corpo, $botoes, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_botoes('', '', $corpo, $botoes), $idAgendamento);
    }

    protected function texto($perfil, $texto, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_texto('', $texto), $idAgendamento);
    }

    protected function enviar($perfil, $payload, $idAgendamento)
    {
        if (empty($payload)) {
            return ['processado' => false, 'reason' => 'payload_invalido', 'id_agendamento' => (int)$idAgendamento];
        }
        $envio = $this->CI->whatsapp_agendamento->enviar_chatbot($perfil['telefone'], $payload);
        return [
            'processado' => !empty($envio['sent']),
            'reason' => !empty($envio['reason']) ? $envio['reason'] : 'api_error',
            'wamid' => trim((string)utec_whatsapp_read($envio, 'wamid', '')),
            'error' => trim((string)utec_whatsapp_read($envio, 'error', '')),
            'id_agendamento' => (int)$idAgendamento,
        ];
    }

    protected function agora()
    {
        return $this->agora !== null ? (int)$this->agora : time();
    }

    protected function minimo()
    {
        return utec_whatsapp_agenda_minimo_datetime($this->agora());
    }

    protected function tamanho($texto)
    {
        return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    }

    protected function log($mensagem)
    {
        if (function_exists('log_message')) {
            log_message('error', '[whatsapp_chatbot_agenda] '.$mensagem);
        }
    }
}
```

- [ ] **Step 3: Roteamento no `Whatsapp_chatbot`**

Em `application/libraries/Whatsapp_chatbot.php`:

a) No construtor, antes do bloco `if (isset($this->CI->load)) { $this->CI->load->helper(...) }`, acrescentar:

```php
        if (!isset($this->CI->whatsapp_chatbot_agenda) && isset($this->CI->load)) {
            $this->CI->load->library('whatsapp_chatbot_agenda');
        }
```

b) Em `processar()`, substituir o bloco

```php
                if ($this->sessao_motivo_aberta($sessao)) {
                    $resultado = $this->processar_sessao_motivo($perfil, $sessao, $evento, $idEvento);
                } else {
                    $resultado = $this->processar_comando($perfil, $evento);
                }
```

por:

```php
                if ($this->sessao_motivo_aberta($sessao)) {
                    $resultado = $this->processar_sessao_motivo($perfil, $sessao, $evento, $idEvento);
                } elseif ($this->agenda_disponivel() && $perfil['perfil'] === 'paciente'
                    && utec_whatsapp_agenda_parse_id(utec_whatsapp_read($evento, 'payload', '')) !== null) {
                    $resultado = $this->tratar_retorno_agenda($perfil, $this->CI->whatsapp_chatbot_agenda->processar_clique($perfil, $evento, $idEvento), $evento);
                } elseif ($this->agenda_disponivel() && $this->sessao_agenda_motivo_aberta($sessao)
                    && trim((string)utec_whatsapp_read($evento, 'payload', '')) === ''
                    && !$this->extrair_comando($perfil['perfil'], $evento)) {
                    $resultado = $this->tratar_retorno_agenda($perfil, $this->CI->whatsapp_chatbot_agenda->receber_motivo($perfil, $sessao, $evento), $evento);
                } else {
                    $resultado = $this->processar_comando($perfil, $evento);
                }
```

c) Substituir o método `iniciar_solicitacao()` inteiro por estes dois métodos:

```php
    protected function iniciar_solicitacao($perfil, $comando, $evento)
    {
        $idAgendamento = (int)$comando['id_agendamento'];
        if ($idAgendamento <= 0) {
            return $this->responder_lista_consultas($perfil, $comando['nome']);
        }
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot($idAgendamento, $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']);
        if (!$this->agendamento_paciente_valido($agendamento, $perfil['perfil'])) {
            return $this->responder_texto($perfil['telefone'], 'Consulta nao encontrada ou indisponivel para esta solicitacao.');
        }

        if ($this->agenda_disponivel() && (string)utec_whatsapp_read($agendamento, 'status', '') === '0') {
            $retorno = $this->CI->whatsapp_chatbot_agenda->iniciar($perfil, $comando['nome'], $agendamento, $evento);
            $retorno = $this->tratar_retorno_agenda($perfil, $retorno, $evento);
            $retorno['id_agendamento'] = $idAgendamento;
            return $retorno;
        }
        return $this->iniciar_solicitacao_manual($perfil, $comando['nome'], $idAgendamento, $evento, '');
    }

    protected function iniciar_solicitacao_manual($perfil, $acao, $idAgendamento, $evento, $prefixo)
    {
        $this->CI->whatsapp_model->salvar_sessao_chatbot(
            $perfil['telefone'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id'], 'solicitacao', 'motivo',
            ['acao' => $acao === 'remarcar' ? 'remarcacao' : 'cancelamento', 'id_agendamento' => (int)$idAgendamento],
            utec_whatsapp_read($evento, 'event_at', null), utec_whatsapp_read($evento, 'message_id', '')
        );
        $resultado = $this->responder_texto($perfil['telefone'], trim($prefixo.' Informe o motivo da solicitacao com pelo menos 3 caracteres.'));
        $resultado['id_agendamento'] = (int)$idAgendamento;
        return $resultado;
    }

    protected function tratar_retorno_agenda($perfil, $retorno, $evento)
    {
        if (!empty($retorno['expirado'])) {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            $this->responder_texto($perfil['telefone'], 'Essa opção expirou. Escolha novamente no menu.');
            return $this->responder_menu($perfil);
        }
        if (!empty($retorno['fallback'])) {
            return $this->iniciar_solicitacao_manual(
                $perfil,
                utec_whatsapp_read($retorno, 'acao', 'remarcar'),
                (int)utec_whatsapp_read($retorno, 'id_agendamento', 0),
                $evento,
                utec_whatsapp_agenda_texto_fallback($retorno['fallback'])
            );
        }
        return $retorno;
    }

    protected function agenda_disponivel()
    {
        return isset($this->CI->whatsapp_chatbot_agenda);
    }

    protected function sessao_agenda_motivo_aberta($sessao)
    {
        return $sessao && utec_whatsapp_read($sessao, 'fluxo', '') === 'agenda_cancelar' && utec_whatsapp_read($sessao, 'etapa', '') === 'motivo';
    }
```

(A sessão antiga `solicitacao/motivo` e `processar_sessao_motivo()` continuam iguais. Consultas com status ≠ 0 que ainda passam em `agendamento_paciente_valido` — ex.: em andamento — seguem no fluxo manual.)

- [ ] **Step 4: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/whatsapp_chatbot_agenda_library_test.php` → `OK whatsapp_chatbot_agenda_library_test`
Run: `for t in tests/*test.php; do /c/PHP/PHP7.2/php.exe "$t" >/dev/null || echo "FALHOU $t"; done` → nenhuma linha `FALHOU` (inclui `whatsapp_chatbot_library_test.php` e `whatsapp_chatbot_test.php` existentes, que rodam sem a library de agenda e devem manter o comportamento antigo).
Run: `/c/PHP/PHP7.2/php.exe -l` nas 2 libraries → sem erros.

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Whatsapp_chatbot_agenda.php application/libraries/Whatsapp_chatbot.php tests/whatsapp_chatbot_agenda_library_test.php
git commit -m "feat(chatbot): paciente remarca e cancela sozinho pelo WhatsApp

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 5: Documentação, manual e template pendente

**Files:**
- Create: `docs/whatsapp-remarcacao-template-pendente.md`
- Modify: `application/libraries/Manual_conteudo.php` (capítulo `'slug' => 'whatsapp-confirmacao'`)
- Modify: `CLAUDE.md` (§10.3.1)

- [ ] **Step 1: Documento do template**

Criar `docs/whatsapp-remarcacao-template-pendente.md`:

```markdown
# Template pendente — agendamento_remarcado_equipe

Aviso por WhatsApp ao profissional e a quem cadastrou a consulta quando o
paciente remarca sozinho pelo chatbot. Enquanto não for aprovado, a
remarcação avisa a equipe só pelo sino (aviso interno).

- **Nome:** `agendamento_remarcado_equipe`
- **Categoria:** Utilidade (Utility)
- **Idioma:** pt_BR
- **Botões:** nenhum
- **Corpo (4 variáveis):**

> O paciente {{1}} remarcou a consulta com {{2}} pelo WhatsApp.
> Antes: {{3}}
> Agora: {{4}}

- **Exemplos para a Meta:** {{1}} Maria Souza · {{2}} Dra. Ana Lima · {{3}} 23/09/2026 as 14:00 · {{4}} 25/09/2026 as 14:30

## Depois da aprovação

1. No cPanel, definir a variável de ambiente `WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE=1`.
2. Remarcar uma consulta de teste pelo chatbot e conferir o log `whatsapp_notificacoes` com `tipo_notificacao = 'equipe_remarcado'` e `status_envio = 'enviado'`.

Os parâmetros são montados em `utec_whatsapp_componentes_equipe_template($contexto, 'remarcar')`.
```

- [ ] **Step 2: Manual**

Em `application/libraries/Manual_conteudo.php`, no capítulo `'slug' => 'whatsapp-confirmacao'`, acrescentar ao fim do array `'*' => array(...)` de `'topicos'` (após o item que começa com `'Se o envio falhar`):

```php
                        'O paciente também pode remarcar ou cancelar sozinho pelo chatbot, com pelo menos 24 horas de antecedência: ele escolhe o dia e o horário entre os horários livres do mesmo profissional (definidos em `Horários de atendimento`) e confirma. A agenda é atualizada na hora.',
                        'Quando o paciente remarca ou cancela pelo chatbot, quem marcou a consulta e o profissional recebem um aviso no sino (com o motivo do cancelamento, se ele informar). Com menos de 24 horas para a consulta, o pedido chega para a equipe analisar, como antes.',
```

Atualizar a chave `'atualizado_em'` desse capítulo para `'2026-09-23'`.

- [ ] **Step 3: CLAUDE.md**

Em §10.3.1, logo após o item **"Notificação WhatsApp à equipe (confirmação/cancelamento)"**, acrescentar:

```markdown
- **Remarcação/cancelamento automáticos pelo chatbot (2026-09-23):** no perfil paciente, `Cancelar`/`Remarcar` passam por `Whatsapp_chatbot_agenda` (library nova; `Whatsapp_chatbot` só roteia). Com ≥ 24h de antecedência, remarcar = lista de dias com vaga (até 10, janela de 30 dias, mesmo profissional, via `Disponibilidade_model::dias_com_vaga`) → lista de horários (9 + "Ver mais") → botão Confirmar; cancelar = motivo opcional ("Prefiro não informar") → "Sim, cancelar". Ids de clique `rem:{id}:d|p|h|ok|dias` e `can:{id}:sem_motivo|ok` (`utec_whatsapp_agenda_parse_id()`); sessões `agenda_remarcar` / `agenda_cancelar`. Gravação em `Whatsapp_model::remarcar_agendamento_chatbot()` / `cancelar_agendamento_chatbot()` com transação + `FOR UPDATE` (agendamento e usuário do profissional) e revalidação por `verificar_horario()`; loga `chatbot_remarcado` (confirmado) / `chatbot_cancelado` (cancelado) em `whatsapp_notificacoes` sem consumir cota. Avisos: sino (`Notificacoes_model::criar_aviso_chatbot_agenda`, tipos `whatsapp_chatbot_remarcado` / `whatsapp_chatbot_cancelado`) + WhatsApp à equipe no cancelamento (template aprovado); na remarcação só com `WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE=1` após aprovar `agendamento_remarcado_equipe` (`docs/whatsapp-remarcacao-template-pendente.md`). `equipe_remarcado` entra na exclusão das consultas de "última resposta". < 24h, profissional sem grade ou sem vaga → fluxo antigo de solicitação com motivo. Spec/plano: `docs/superpowers/specs/2026-09-23-chatbot-remarcar-cancelar-automatico-design.md`, `docs/superpowers/plans/2026-09-23-chatbot-remarcar-cancelar-automatico.md`.
```

- [ ] **Step 4: Rodar testes**

Run: `/c/PHP/PHP7.2/php.exe tests/manual_conteudo_test.php` → passa (contagem de capítulos não muda).
Run: `/c/PHP/PHP7.2/php.exe -l application/libraries/Manual_conteudo.php` → sem erros.

- [ ] **Step 5: Commit**

```bash
git add docs/whatsapp-remarcacao-template-pendente.md application/libraries/Manual_conteudo.php CLAUDE.md
git commit -m "docs(chatbot): manual, template pendente e CLAUDE.md da remarcacao automatica

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

## Deploy (agente-dev-infra, após o merge)

Arquivos runtime: `application/helpers/disponibilidade_helper.php`, `application/models/Disponibilidade_model.php`, `application/helpers/whatsapp_agendamento_helper.php`, `application/config/whatsapp.php`, `application/models/Whatsapp_model.php`, `application/controllers/adm/Atendimento.php`, `application/models/Notificacoes_model.php`, `application/libraries/Whatsapp_agendamento.php`, `application/libraries/Whatsapp_chatbot_agenda.php`, `application/libraries/Whatsapp_chatbot.php`, `application/libraries/Manual_conteudo.php`. Sem migração de banco. Healthcheck: home/`admin` 200, `webhooks/whatsapp` GET sem token 403. Smoke test com número de teste: remarcar, cancelar com e sem motivo, consulta < 24h.
