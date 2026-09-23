# Horários de Atendimento do Profissional — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Cada prestador (nível 3) passa a ter grade semanal, duração fixa de consulta e bloqueios pontuais; o sistema calcula horários livres e avisa (sem bloquear) a recepção ao agendar/remarcar fora da disponibilidade.

**Architecture:** Duas tabelas novas (`prestador_horarios`, `prestador_bloqueios`) + coluna `usuarios.duracao_atendimento_min`. Todo o cálculo de vagas fica em funções puras (`disponibilidade_helper.php`, testado por CLI). `Disponibilidade_model` busca dados e aplica o helper — é a interface que o chatbot vai consumir no futuro. Controller `adm/Horarios` entrega a tela de configuração e um endpoint JSON usado por um widget JS (`js/adm/disponibilidade.js`) acoplado aos formulários de agendamento/remarcação existentes.

**Tech Stack:** PHP 7.2 (produção PHP 7 — nada de sintaxe PHP 8), CodeIgniter 3.1.10, MySQL/MariaDB, Bootstrap 4, jQuery.

**Spec:** `docs/superpowers/specs/2026-09-22-horarios-atendimento-design.md`

## Global Constraints

- Não modificar `system/`. Não usar `$_POST` direto — sempre `$this->input->post()`/`get()`.
- Código compatível com PHP 7.2: sem `match`, sem arrow functions `fn`, sem typed properties, sem named args, sem `str_contains`.
- Lint com `/c/PHP/PHP7.2/php.exe -l <arquivo>`; testes com `/c/PHP/PHP7.2/php.exe tests/<arquivo>.php` (exit 0 = passou).
- Duração padrão quando `NULL`: **30 min**. Durações permitidas: **10, 15, 20, 30, 40, 45, 50, 60, 90, 120**.
- `dia_semana`: **0 = domingo … 6 = sábado** (mesmo valor de `date('w')`).
- Ocupam vaga: agendamentos com `status IN (0,1,2)`. `status = 3` libera.
- Prestador = usuário `nivel = 3`. Escopo de prestadores visíveis: `Padrao_model::get_visible_prestador_ids()` (nível 1 = todos os nível 3).
- Edição: nível 1 sempre; nível 2 se prestador visível; nível 3 só o próprio id; nível 4 nunca (só visualiza).
- Agenda manual: **apenas aviso**. `Atendimento::cadastrar()` e `Atendimento::remarcar_agenda()` NÃO mudam.
- Timezone do app: `America/Recife` (definido em `index.php`) — usar `date()` direto.
- Textos de UI em português com acentuação correta.
- Commits terminam com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.

## File Structure

| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/disponibilidade_helper.php` | Criar | Funções puras: slots, ocupação, bloqueios, classificação, validação |
| `tests/disponibilidade_helper_test.php` | Criar | Testes puros do helper |
| `application/controllers/adm/Dev.php` | Modificar | Método `migrar_horarios_atendimento()` |
| `application/models/Disponibilidade_model.php` | Criar | Leitura/gravação de grade, bloqueios, cálculo de livres |
| `tests/disponibilidade_source_test.php` | Criar | Asserções de fonte (model, controller, migração, views) |
| `application/controllers/adm/Horarios.php` | Criar | Tela, salvar, bloquear, desbloquear, endpoint JSON `livres` |
| `application/views/adm/horarios/index.php` | Criar | Tela de configuração |
| `includes/adm/menu.php` | Modificar | Item "Horários de atendimento" |
| `js/adm/disponibilidade.js` | Criar | Widget: lista livres + alerta de encaixe |
| `application/views/adm/atendimento/atendimento.php` | Modificar | Widget no form de novo agendamento |
| `application/views/adm/calendario/index.php` | Modificar | Widget no modal criar + remarcar |
| `application/views/adm/usuarios/new/atendimentos.php` | Modificar | Widget na remarcação (desktop + sheet mobile) |
| `application/libraries/Manual_conteudo.php` | Modificar | Capítulo "Horários de atendimento" |
| `tests/manual_conteudo_test.php` | Modificar | Contagem de capítulos 12→13 / 11→12 |
| `CLAUDE.md` | Modificar | Documentar tabelas, controller, model, migração |

---

### Task 1: Helper puro de disponibilidade (TDD)

**Files:**
- Create: `application/helpers/disponibilidade_helper.php`
- Test: `tests/disponibilidade_helper_test.php`

**Interfaces:**
- Produces (todas globais, guardadas por `function_exists`):
  - `utec_disp_duracoes_permitidas(): int[]`
  - `utec_disp_nomes_dias(): string[]` (índice 0..6)
  - `utec_disp_min(string $hora): ?int` — `'HH:MM'` ou `'HH:MM:SS'` → minutos; `'24:00'` → 1440; inválido → `null`
  - `utec_disp_hhmm(int $min): string`
  - `utec_disp_data_valida(string $data): bool` — `Y-m-d` real (checkdate)
  - `utec_disp_gerar_slots(array $intervalos, int $duracao): string[]` — `$intervalos = [['08:00','12:00'], ...]`
  - `utec_disp_remover_ocupados(array $slots, array $horas_agendadas, array $bloqueios_dia, int $duracao): string[]`
  - `utec_disp_recortar_bloqueios_no_dia(array $bloqueios, string $data): array` — `$bloqueios = [['inicio'=>'Y-m-d H:i:s','fim'=>'Y-m-d H:i:s'], ...]` → `[['HH:MM','HH:MM'], ...]` (fim pode ser `'24:00'`)
  - `utec_disp_classificar_horario(string $hora, array $intervalos, array $horas_agendadas, array $bloqueios_dia, int $duracao): string` — `bloqueado|fora_da_grade|ocupado|livre`
  - `utec_disp_filtrar_apos(array $slots, string $hora_limite): string[]` — mantém slots estritamente depois
  - `utec_disp_validar_intervalos(array $intervalos): string` — `''` = ok, senão mensagem
  - `utec_disp_normalizar_bloqueio(string $data_ini, string $hora_ini, string $data_fim, string $hora_fim, bool $dia_inteiro): array` → `['ok'=>bool,'inicio'=>string,'fim'=>string,'erro'=>string]`

- [ ] **Step 1: Escrever o teste que falha**

Criar `tests/disponibilidade_helper_test.php`:

```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/disponibilidade_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- conversões
assertSameValue(480, utec_disp_min('08:00'), 'min 08:00');
assertSameValue(555, utec_disp_min('09:15:00'), 'min com segundos');
assertSameValue(1440, utec_disp_min('24:00'), 'min 24:00');
assertSameValue(null, utec_disp_min('8h'), 'min invalido');
assertSameValue(null, utec_disp_min('25:00'), 'min hora > 24');
assertSameValue('09:05', utec_disp_hhmm(545), 'hhmm');
assertSameValue(true, utec_disp_data_valida('2026-09-22'), 'data valida');
assertSameValue(false, utec_disp_data_valida('2026-02-31'), 'data inexistente');
assertSameValue(true, in_array(50, utec_disp_duracoes_permitidas(), true), 'duracao 50 permitida');
assertSameValue('Domingo', utec_disp_nomes_dias()[0], 'dia 0 = domingo');

// --- geração de slots
assertSameValue(array('08:00', '08:30', '09:00', '09:30'),
    utec_disp_gerar_slots(array(array('08:00', '10:00')), 30), 'slots simples');
assertSameValue(array('08:00', '08:50', '09:40', '10:30'),
    utec_disp_gerar_slots(array(array('08:00', '12:00')), 50), 'slot parcial descartado');
assertSameValue(array('08:00', '08:30', '14:00', '14:30'),
    utec_disp_gerar_slots(array(array('14:00', '15:00'), array('08:00', '09:00')), 30), 'multiplos intervalos ordenados');
assertSameValue(array(), utec_disp_gerar_slots(array(array('10:00', '09:00')), 30), 'intervalo invertido ignorado');
assertSameValue(array(), utec_disp_gerar_slots(array(array('08:00', '10:00')), 0), 'duracao zero');

// --- remoção de ocupados
$slots = array('08:00', '08:30', '09:00', '09:30');
assertSameValue(array('09:00', '09:30'),
    utec_disp_remover_ocupados($slots, array('08:15'), array(), 30), 'agendamento fora do passo remove 2 slots');
assertSameValue(array('08:00', '08:30', '09:30'),
    utec_disp_remover_ocupados($slots, array('09:00:00'), array(), 30), 'agendamento com segundos');
assertSameValue(array('08:00', '08:30'),
    utec_disp_remover_ocupados($slots, array(), array(array('09:00', '10:00')), 30), 'bloqueio parcial');
assertSameValue(array(),
    utec_disp_remover_ocupados($slots, array(), array(array('00:00', '24:00')), 30), 'bloqueio dia inteiro');

// --- recorte de bloqueios
$ferias = array(array('inicio' => '2026-09-21 18:00:00', 'fim' => '2026-09-23 10:00:00'));
assertSameValue(array(array('18:00', '24:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-21'), 'recorte dia 1');
assertSameValue(array(array('00:00', '24:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-22'), 'recorte dia do meio');
assertSameValue(array(array('00:00', '10:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-23'), 'recorte ultimo dia');
assertSameValue(array(), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-24'), 'recorte fora');

// --- normalização de bloqueio
$b = utec_disp_normalizar_bloqueio('2026-09-22', '', '', '', true);
assertSameValue(true, $b['ok'], 'dia inteiro ok');
assertSameValue('2026-09-22 00:00:00', $b['inicio'], 'dia inteiro inicio');
assertSameValue('2026-09-22 23:59:59', $b['fim'], 'dia inteiro fim');
assertSameValue(array(array('00:00', '24:00')),
    utec_disp_recortar_bloqueios_no_dia(array($b), '2026-09-22'), 'dia inteiro cobre o dia todo');
$b = utec_disp_normalizar_bloqueio('2026-09-22', '14:00', '2026-09-22', '18:00', false);
assertSameValue('2026-09-22 14:00:00', $b['inicio'], 'bloqueio parcial inicio');
assertSameValue('2026-09-22 18:00:00', $b['fim'], 'bloqueio parcial fim');
$b = utec_disp_normalizar_bloqueio('2026-09-22', '18:00', '2026-09-22', '14:00', false);
assertSameValue(false, $b['ok'], 'bloqueio invertido rejeitado');
$b = utec_disp_normalizar_bloqueio('2026-02-31', '', '', '', true);
assertSameValue(false, $b['ok'], 'data invalida rejeitada');

// --- classificação
$grade = array(array('08:00', '12:00'));
assertSameValue('fora_da_grade', utec_disp_classificar_horario('07:30', $grade, array(), array(), 30), 'antes da grade');
assertSameValue('fora_da_grade', utec_disp_classificar_horario('11:45', $grade, array(), array(), 30), 'nao cabe inteiro');
assertSameValue('ocupado', utec_disp_classificar_horario('08:00', $grade, array('08:00'), array(), 30), 'ocupado');
assertSameValue('bloqueado', utec_disp_classificar_horario('10:00', $grade, array(), array(array('09:30', '10:30')), 30), 'bloqueado');
assertSameValue('bloqueado', utec_disp_classificar_horario('13:00', $grade, array(), array(array('00:00', '24:00')), 30), 'bloqueado tem precedencia');
assertSameValue('livre', utec_disp_classificar_horario('10:00', $grade, array('08:00'), array(), 30), 'livre');
assertSameValue('livre', utec_disp_classificar_horario('10:15', $grade, array(), array(), 30), 'livre fora do passo');

// --- filtro de horário atual
assertSameValue(array('09:00'), utec_disp_filtrar_apos(array('08:00', '08:30', '09:00'), '08:30'), 'filtrar apos');

// --- validação de intervalos
assertSameValue('', utec_disp_validar_intervalos(array(array('08:00', '12:00'), array('12:00', '13:00'))), 'intervalos encostados ok');
assertSameValue(true, utec_disp_validar_intervalos(array(array('08:00', '12:00'), array('11:00', '13:00'))) !== '', 'sobreposicao rejeitada');
assertSameValue(true, utec_disp_validar_intervalos(array(array('12:00', '08:00'))) !== '', 'invertido rejeitado');
assertSameValue(true, utec_disp_validar_intervalos(array(array('8h', '9h'))) !== '', 'formato rejeitado');
assertSameValue('', utec_disp_validar_intervalos(array()), 'dia vazio ok');

echo "OK disponibilidade_helper_test\n";
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_helper_test.php`
Expected: erro fatal `failed to open stream ... disponibilidade_helper.php`.

- [ ] **Step 3: Implementar o helper**

Criar `application/helpers/disponibilidade_helper.php`:

```php
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
```

- [ ] **Step 4: Rodar e ver passar**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_helper_test.php`
Expected: `OK disponibilidade_helper_test`, exit 0.
Run: `/c/PHP/PHP7.2/php.exe -l application/helpers/disponibilidade_helper.php` → `No syntax errors detected`.

- [ ] **Step 5: Commit**

```bash
git add application/helpers/disponibilidade_helper.php tests/disponibilidade_helper_test.php
git commit -m "feat(horarios): helper puro de calculo de disponibilidade

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 2: Migração + Disponibilidade_model

**Files:**
- Modify: `application/controllers/adm/Dev.php` (novo método logo após `migrar_lembrete_whatsapp()`)
- Create: `application/models/Disponibilidade_model.php`
- Test: `tests/disponibilidade_source_test.php`

**Interfaces:**
- Consumes: helper da Task 1.
- Produces (`Disponibilidade_model`, carregado como `$this->load->model('Disponibilidade_model', 'disponibilidade_model')`):
  - `schema_ok(): bool`
  - `get_config(int $id_prestador): array` → `['duracao'=>int, 'grade'=>[0..6 => [['HH:MM','HH:MM'],...]], 'tem_grade'=>bool]`
  - `salvar_config(int $id_prestador, int $duracao, array $grade): bool` — `$grade` mesmo formato de `get_config()['grade']`
  - `listar_bloqueios_futuros(int $id_prestador): array` de objetos (`id, inicio, fim, motivo`)
  - `adicionar_bloqueio(int $id_prestador, string $inicio, string $fim, string $motivo, int $id_user_cad): bool`
  - `remover_bloqueio(int $id_bloqueio, int $id_prestador): bool`
  - `horarios_livres(int $id_prestador, string $data, int $ignorar_agendamento_id = 0): array` → `['tem_grade'=>bool,'duracao'=>int,'livres'=>string[]]`
  - `verificar_horario(int $id_prestador, string $data, string $hora, int $ignorar_agendamento_id = 0): array` → `['tem_grade'=>bool,'situacao'=>string]`
  - `proximos_livres(int $id_prestador, string $a_partir_de, int $limite = 10): array` → `[['data'=>'Y-m-d','hora'=>'HH:MM'], ...]`

- [ ] **Step 1: Escrever o teste de fonte que falha**

Criar `tests/disponibilidade_source_test.php` (estilo "source assertion" do projeto — código com banco não é executado, só verificado por conteúdo):

```php
<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerArquivo($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) {
        fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL);
        exit(1);
    }
    return file_get_contents($path);
}

// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_horarios_atendimento()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `prestador_horarios`', $dev, 'tabela horarios');
assertContains('CREATE TABLE IF NOT EXISTS `prestador_bloqueios`', $dev, 'tabela bloqueios');
assertContains("'duracao_atendimento_min'", $dev, 'coluna duracao');

// Model
$model = lerArquivo('application/models/Disponibilidade_model.php');
foreach (array('function schema_ok(', 'function get_config(', 'function salvar_config(',
    'function listar_bloqueios_futuros(', 'function adicionar_bloqueio(', 'function remover_bloqueio(',
    'function horarios_livres(', 'function verificar_horario(', 'function proximos_livres(') as $fn) {
    assertContains($fn, $model, 'model: ' . $fn);
}
assertContains('status IN (0,1,2)', $model, 'model considera status ocupantes');
assertContains('trans_start()', $model, 'salvar_config em transacao');
assertContains("load->helper('disponibilidade')", $model, 'model carrega helper');

echo "OK disponibilidade_source_test\n";
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php`
Expected: `migracao existe — trecho ausente: function migrar_horarios_atendimento()`, exit 1.

- [ ] **Step 3: Adicionar a migração em `Dev.php`**

Inserir logo após o fechamento de `migrar_lembrete_whatsapp()` (usa os helpers privados já existentes `run_sql()` e `ensure_column()`):

```php
	function migrar_horarios_atendimento(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->run_sql("CREATE TABLE IF NOT EXISTS `prestador_horarios` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_prestador` INT NOT NULL,
			`dia_semana` TINYINT NOT NULL,
			`hora_inicio` TIME NOT NULL,
			`hora_fim` TIME NOT NULL,
			`created_at` DATETIME NULL,
			INDEX `idx_prest_dia` (`id_prestador`, `dia_semana`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `prestador_horarios` verificada');
		$this->run_sql("CREATE TABLE IF NOT EXISTS `prestador_bloqueios` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_prestador` INT NOT NULL,
			`inicio` DATETIME NOT NULL,
			`fim` DATETIME NOT NULL,
			`motivo` VARCHAR(150) NULL,
			`id_user_cad` INT NULL,
			`created_at` DATETIME NULL,
			INDEX `idx_prest_inicio` (`id_prestador`, `inicio`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `prestador_bloqueios` verificada');
		$this->ensure_column('usuarios', 'duracao_atendimento_min', "INT NULL DEFAULT NULL", $logs);

		echo '<h3>Migração: horários de atendimento</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 4: Criar `application/models/Disponibilidade_model.php`**

```php
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

    private function bloqueios_do_dia($id_prestador, $data)
    {
        $dia_ini = $data.' 00:00:00';
        $dia_fim = date('Y-m-d', strtotime('+1 day', strtotime($data))).' 00:00:00';
        $qr = $this->db->query(
            "SELECT inicio, fim FROM prestador_bloqueios
             WHERE id_prestador = ".(int)$id_prestador."
               AND inicio < ".$this->db->escape($dia_fim)."
               AND fim > ".$this->db->escape($dia_ini)
        );
        $brutos = array();
        foreach ($qr->result() as $row) {
            $brutos[] = array('inicio' => $row->inicio, 'fim' => $row->fim);
        }
        return utec_disp_recortar_bloqueios_no_dia($brutos, $data);
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

    public function horarios_livres($id_prestador, $data, $ignorar_agendamento_id = 0)
    {
        $cfg = $this->get_config($id_prestador);
        $res = array('tem_grade' => $cfg['tem_grade'], 'duracao' => $cfg['duracao'], 'livres' => array());
        $hoje = date('Y-m-d');
        if (!$cfg['tem_grade'] || !utec_disp_data_valida($data) || $data < $hoje) {
            return $res;
        }
        $dia = (int)date('w', strtotime($data));
        $slots = utec_disp_gerar_slots($cfg['grade'][$dia], $cfg['duracao']);
        $slots = utec_disp_remover_ocupados(
            $slots,
            $this->horas_agendadas($id_prestador, $data, $ignorar_agendamento_id),
            $this->bloqueios_do_dia($id_prestador, $data),
            $cfg['duracao']
        );
        if ($data === $hoje) {
            $slots = utec_disp_filtrar_apos($slots, date('H:i'));
        }
        $res['livres'] = $slots;
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
```

- [ ] **Step 5: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → `OK disponibilidade_source_test`
Run: `/c/PHP/PHP7.2/php.exe -l application/models/Disponibilidade_model.php` e `/c/PHP/PHP7.2/php.exe -l application/controllers/adm/Dev.php` → sem erros.
Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_helper_test.php` → continua OK.

- [ ] **Step 6: Commit**

```bash
git add application/controllers/adm/Dev.php application/models/Disponibilidade_model.php tests/disponibilidade_source_test.php
git commit -m "feat(horarios): migracao e Disponibilidade_model

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 3: Controller `adm/Horarios` + tela + menu

**Files:**
- Create: `application/controllers/adm/Horarios.php`
- Create: `application/views/adm/horarios/index.php`
- Modify: `includes/adm/menu.php` (bloco `if(!$menu_is_patient){ ... }`, logo após o item "Calendário")
- Modify: `tests/disponibilidade_source_test.php`

**Interfaces:**
- Consumes: `Disponibilidade_model` (Task 2), helper (Task 1), `Padrao_model::get_usuario_logado()`, `get_visible_prestador_ids()`, `ids_to_sql_in()`.
- Produces (rotas pelo roteamento padrão do CI, sem rota explícita):
  - `GET adm/horarios?id_prestador=N` — tela
  - `POST adm/horarios/salvar` — campos `id_prestador`, `duracao`, `dias[d][atende]`, `dias[d][ini][]`, `dias[d][fim][]`
  - `POST adm/horarios/bloquear` — `id_prestador`, `data_inicio`, `hora_inicio`, `data_fim`, `hora_fim`, `dia_inteiro`, `motivo`
  - `POST adm/horarios/desbloquear/{id}` — `id_prestador`
  - `GET adm/horarios/livres?id_prestador=&data=&ignorar=&hora=` → JSON `{"tem_grade":bool,"duracao":int,"livres":["HH:MM"],"situacao":"livre|ocupado|fora_da_grade|bloqueado"}` (`situacao` só quando `hora` informado; 403 `{"erro":"forbidden"}` fora do escopo)

- [ ] **Step 1: Estender o teste de fonte (falha)**

Acrescentar antes do `echo` final de `tests/disponibilidade_source_test.php`:

```php
// Controller
$ctrl = lerArquivo('application/controllers/adm/Horarios.php');
foreach (array('function index(', 'function salvar(', 'function bloquear(', 'function desbloquear(',
    'function livres(', 'function pode_editar(', 'function pode_ver(') as $fn) {
    assertContains($fn, $ctrl, 'controller: ' . $fn);
}
assertContains('utec_disp_validar_intervalos(', $ctrl, 'controller valida intervalos');
assertContains('utec_disp_normalizar_bloqueio(', $ctrl, 'controller normaliza bloqueio');
assertContains('get_visible_prestador_ids(', $ctrl, 'controller usa escopo de prestadores');
assertContains("set_status_header(403)", $ctrl, 'endpoint livres responde 403');

// View e menu
$view = lerArquivo('application/views/adm/horarios/index.php');
assertContains('name="duracao"', $view, 'view tem duracao');
assertContains('dias[', $view, 'view tem grade');
assertContains('adm/horarios/bloquear', $view, 'view tem form de bloqueio');
assertContains('htmlspecialchars(', $view, 'view escapa saida');
$menu = lerArquivo('includes/adm/menu.php');
assertContains("adm/horarios'", $menu, 'menu aponta para horarios');
```

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → FAIL `Arquivo ausente: application/controllers/adm/Horarios.php`.

- [ ] **Step 2: Criar o controller**

`application/controllers/adm/Horarios.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horarios extends CI_Controller {

    private $usuario;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url', 'disponibilidade'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->load->model('Disponibilidade_model', 'disponibilidade_model');
        $this->usuarios_model->verSession();

        $this->usuario = $this->padrao_model->get_usuario_logado();
        $nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
        if ($nivel < 1 || $nivel > 4) {
            show_error('Acesso restrito.', 403);
        }
    }

    private function nivel()
    {
        return (int)$this->usuario->nivel;
    }

    private function prestadores_visiveis()
    {
        if ($this->nivel() === 3) {
            return $this->db->query("SELECT id, nome FROM usuarios WHERE id = ".(int)$this->usuario->id." LIMIT 1")->result();
        }
        if ($this->nivel() === 1) {
            return $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC")->result();
        }
        $ids = $this->padrao_model->get_visible_prestador_ids($this->usuario);
        if (empty($ids)) {
            return array();
        }
        return $this->db->query(
            "SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (".$this->padrao_model->ids_to_sql_in($ids).") ORDER BY nome ASC"
        )->result();
    }

    private function ids_visiveis()
    {
        $ids = array();
        foreach ($this->prestadores_visiveis() as $p) {
            $ids[] = (int)$p->id;
        }
        return $ids;
    }

    private function pode_ver($id_prestador)
    {
        $id_prestador = (int)$id_prestador;
        return $id_prestador > 0 && in_array($id_prestador, $this->ids_visiveis(), true);
    }

    private function pode_editar($id_prestador)
    {
        $id_prestador = (int)$id_prestador;
        switch ($this->nivel()) {
            case 1:
            case 2:
                return $this->pode_ver($id_prestador);
            case 3:
                return $id_prestador === (int)$this->usuario->id;
            default:
                return false;
        }
    }

    private function voltar($id_prestador)
    {
        redirect('adm/horarios?id_prestador='.(int)$id_prestador);
    }

    public function index()
    {
        $prestadores = $this->prestadores_visiveis();
        $id_prestador = (int)$this->input->get('id_prestador');
        if (!$this->pode_ver($id_prestador)) {
            $id_prestador = !empty($prestadores) ? (int)$prestadores[0]->id : 0;
        }

        $dados['prestadores'] = $prestadores;
        $dados['id_prestador'] = $id_prestador;
        $dados['schema_ok'] = $this->disponibilidade_model->schema_ok();
        $dados['config'] = $id_prestador > 0 ? $this->disponibilidade_model->get_config($id_prestador) : null;
        $dados['bloqueios'] = $id_prestador > 0 ? $this->disponibilidade_model->listar_bloqueios_futuros($id_prestador) : array();
        $dados['pode_editar'] = $id_prestador > 0 && $this->pode_editar($id_prestador);
        $dados['duracoes'] = utec_disp_duracoes_permitidas();
        $dados['nomes_dias'] = utec_disp_nomes_dias();
        $dados['flash_ok'] = $this->session->flashdata('ok');
        $dados['flash_error'] = $this->session->flashdata('error');
        $this->load->view('adm/horarios/index', $dados);
    }

    public function salvar()
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if (!$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para editar os horários deste profissional.', 403);
            return;
        }
        $duracao = (int)$this->input->post('duracao');
        if (!in_array($duracao, utec_disp_duracoes_permitidas(), true)) {
            $this->session->set_flashdata('error', 'Duração de consulta inválida.');
            $this->voltar($id_prestador);
            return;
        }

        $dias = $this->input->post('dias');
        $dias = is_array($dias) ? $dias : array();
        $nomes = utec_disp_nomes_dias();
        $grade = array();
        for ($d = 0; $d <= 6; $d++) {
            $grade[$d] = array();
            if (empty($dias[$d]['atende'])) {
                continue;
            }
            $inis = isset($dias[$d]['ini']) ? (array)$dias[$d]['ini'] : array();
            $fins = isset($dias[$d]['fim']) ? (array)$dias[$d]['fim'] : array();
            foreach ($inis as $k => $ini) {
                $ini = trim((string)$ini);
                $fim = isset($fins[$k]) ? trim((string)$fins[$k]) : '';
                if ($ini === '' && $fim === '') {
                    continue;
                }
                $grade[$d][] = array($ini, $fim);
            }
            $erro = utec_disp_validar_intervalos($grade[$d]);
            if ($erro !== '') {
                $this->session->set_flashdata('error', $nomes[$d].': '.$erro);
                $this->voltar($id_prestador);
                return;
            }
        }

        if ($this->disponibilidade_model->salvar_config($id_prestador, $duracao, $grade)) {
            $this->session->set_flashdata('ok', 'Horários de atendimento salvos.');
        } else {
            $this->session->set_flashdata('error', 'Não foi possível salvar. Verifique se a migração de horários foi executada.');
        }
        $this->voltar($id_prestador);
    }

    public function bloquear()
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if (!$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para bloquear horários deste profissional.', 403);
            return;
        }
        $b = utec_disp_normalizar_bloqueio(
            (string)$this->input->post('data_inicio', true),
            (string)$this->input->post('hora_inicio', true),
            (string)$this->input->post('data_fim', true),
            (string)$this->input->post('hora_fim', true),
            (bool)$this->input->post('dia_inteiro')
        );
        if (!$b['ok']) {
            $this->session->set_flashdata('error', $b['erro']);
        } elseif ($this->disponibilidade_model->adicionar_bloqueio(
            $id_prestador, $b['inicio'], $b['fim'], (string)$this->input->post('motivo', true), (int)$this->usuario->id
        )) {
            $this->session->set_flashdata('ok', 'Bloqueio cadastrado.');
        } else {
            $this->session->set_flashdata('error', 'Não foi possível cadastrar o bloqueio.');
        }
        $this->voltar($id_prestador);
    }

    public function desbloquear($id_bloqueio = 0)
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if ($this->input->method() !== 'post' || !$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para remover este bloqueio.', 403);
            return;
        }
        if ($this->disponibilidade_model->remover_bloqueio((int)$id_bloqueio, $id_prestador)) {
            $this->session->set_flashdata('ok', 'Bloqueio removido.');
        } else {
            $this->session->set_flashdata('error', 'Bloqueio não encontrado.');
        }
        $this->voltar($id_prestador);
    }

    public function livres()
    {
        $id_prestador = (int)$this->input->get('id_prestador');
        $this->output->set_content_type('application/json');
        if (!$this->pode_ver($id_prestador)) {
            $this->output->set_status_header(403)->set_output(json_encode(array('erro' => 'forbidden')));
            return;
        }
        $data = (string)$this->input->get('data', true);
        $ignorar = (int)$this->input->get('ignorar');
        $hora = trim((string)$this->input->get('hora', true));

        $res = $this->disponibilidade_model->horarios_livres($id_prestador, $data, $ignorar);
        if ($hora !== '') {
            $verif = $this->disponibilidade_model->verificar_horario($id_prestador, $data, $hora, $ignorar);
            $res['situacao'] = $verif['situacao'];
        }
        $this->output->set_output(json_encode($res));
    }
}
```

- [ ] **Step 3: Criar a view `application/views/adm/horarios/index.php`**

Mesma casca de `application/views/adm/whatsapp/index.php` (head, includes de `search.php`, `menu.php`, `top.php`, scripts no rodapé):

```php
<!DOCTYPE html>
<html>
<head>
  <title>Horários de atendimento</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .hr-shell { max-width: 1120px; }
    .hr-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .hr-panel h5 { font-weight:800; color:#0f172a; margin-bottom:4px; }
    .hr-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
    .hr-dia { display:flex; gap:14px; align-items:flex-start; padding:12px 0; border-top:1px solid #eef2f7; flex-wrap:wrap; }
    .hr-dia:first-of-type { border-top:0; }
    .hr-dia-nome { width:130px; font-weight:700; color:#0f172a; padding-top:6px; }
    .hr-intervalos { display:flex; flex-direction:column; gap:8px; flex:1; min-width:240px; }
    .hr-intervalo { display:flex; gap:8px; align-items:center; }
    .hr-intervalo input { max-width:130px; }
    .hr-dia.is-off .hr-intervalos { opacity:.4; pointer-events:none; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="hr-shell">
            <h4 style="font-weight:800;color:#0f172a;">Horários de atendimento</h4>
            <p class="hr-sub">Defina quando o profissional atende e quanto dura cada consulta. A agenda usa essas informações para sugerir horários livres.</p>

            <? if($flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div><? } ?>
            <? if($flash_error){ ?><div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas de horários ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_horarios_atendimento</code>.</div><? } ?>

            <? if(empty($prestadores)){ ?>
              <div class="alert alert-info">Nenhum profissional disponível para configurar.</div>
            <? } else { ?>

            <? if(count($prestadores) > 1){ ?>
            <form method="get" action="<?=base_url()?>adm/horarios" class="hr-panel" style="padding:16px 22px;">
              <label style="font-weight:700;">Profissional</label>
              <select name="id_prestador" class="form-control" onchange="this.form.submit()" style="max-width:420px;">
                <? foreach($prestadores as $p){ ?>
                  <option value="<?=(int)$p->id?>" <?=(int)$p->id === (int)$id_prestador ? 'selected' : ''?>><?=htmlspecialchars($p->nome)?></option>
                <? } ?>
              </select>
            </form>
            <? } ?>

            <? if(!$pode_editar){ ?><div class="alert alert-info">Você pode consultar estes horários, mas apenas o profissional ou o estabelecimento podem alterá-los.</div><? } ?>
            <? $dis = $pode_editar ? '' : 'disabled'; ?>

            <form method="post" action="<?=base_url()?>adm/horarios/salvar" class="hr-panel" id="form-grade">
              <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
              <h5>Duração da consulta</h5>
              <p class="hr-sub">Tempo que cada agendamento ocupa na agenda deste profissional.</p>
              <select name="duracao" class="form-control" style="max-width:200px;" <?=$dis?>>
                <? foreach($duracoes as $min){ ?>
                  <option value="<?=$min?>" <?=$config && (int)$config['duracao'] === (int)$min ? 'selected' : ''?>><?=$min?> minutos</option>
                <? } ?>
              </select>

              <h5 style="margin-top:24px;">Grade semanal</h5>
              <p class="hr-sub">Marque os dias de atendimento e informe um ou mais intervalos (ex.: 08:00–12:00 e 14:00–18:00).</p>
              <? for($d = 0; $d <= 6; $d++){
                   $ivs = $config ? $config['grade'][$d] : array();
                   $atende = !empty($ivs);
                   if(!$atende){ $ivs = array(array('', '')); } ?>
                <div class="hr-dia <?=$atende ? '' : 'is-off'?>" data-dia="<?=$d?>">
                  <div class="hr-dia-nome">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input hr-atende" id="atende-<?=$d?>" name="dias[<?=$d?>][atende]" value="1" <?=$atende ? 'checked' : ''?> <?=$dis?>>
                      <label class="custom-control-label" for="atende-<?=$d?>"><?=htmlspecialchars($nomes_dias[$d])?></label>
                    </div>
                  </div>
                  <div class="hr-intervalos">
                    <? foreach($ivs as $iv){ ?>
                      <div class="hr-intervalo">
                        <input type="time" class="form-control form-control-sm" name="dias[<?=$d?>][ini][]" value="<?=htmlspecialchars($iv[0])?>" <?=$dis?>>
                        <span>até</span>
                        <input type="time" class="form-control form-control-sm" name="dias[<?=$d?>][fim][]" value="<?=htmlspecialchars($iv[1])?>" <?=$dis?>>
                        <? if($pode_editar){ ?><button type="button" class="btn btn-sm btn-link text-danger hr-remover" title="Remover intervalo">✕</button><? } ?>
                      </div>
                    <? } ?>
                    <? if($pode_editar){ ?><div><button type="button" class="btn btn-sm btn-outline-secondary hr-adicionar">+ intervalo</button></div><? } ?>
                  </div>
                </div>
              <? } ?>

              <? if($pode_editar){ ?>
              <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;">
                <button type="submit" class="btn btn-primary">Salvar horários</button>
                <button type="button" class="btn btn-outline-secondary" id="hr-copiar-segunda">Copiar segunda para seg–sex</button>
              </div>
              <? } ?>
            </form>

            <div class="hr-panel">
              <h5>Bloqueios</h5>
              <p class="hr-sub">Férias, feriados, congressos ou qualquer período em que o profissional não atende.</p>
              <? if(empty($bloqueios)){ ?>
                <p class="text-muted" style="font-size:13px;">Nenhum bloqueio futuro.</p>
              <? } else { ?>
                <table class="table table-sm">
                  <thead><tr><th>Início</th><th>Fim</th><th>Motivo</th><th></th></tr></thead>
                  <tbody>
                  <? foreach($bloqueios as $b){ ?>
                    <tr>
                      <td><?=date('d/m/Y H:i', strtotime($b->inicio))?></td>
                      <td><?=date('d/m/Y H:i', strtotime($b->fim))?></td>
                      <td><?=htmlspecialchars((string)$b->motivo)?></td>
                      <td class="text-right">
                        <? if($pode_editar){ ?>
                        <form method="post" action="<?=base_url()?>adm/horarios/desbloquear/<?=(int)$b->id?>" style="display:inline" onsubmit="return confirm('Remover este bloqueio?')">
                          <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                        <? } ?>
                      </td>
                    </tr>
                  <? } ?>
                  </tbody>
                </table>
              <? } ?>

              <? if($pode_editar){ ?>
              <form method="post" action="<?=base_url()?>adm/horarios/bloquear" id="form-bloqueio" style="margin-top:12px;">
                <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
                <div class="row">
                  <div class="col-md-3"><label>Data início</label><input type="date" name="data_inicio" class="form-control" required></div>
                  <div class="col-md-2 hr-hora"><label>Hora início</label><input type="time" name="hora_inicio" class="form-control"></div>
                  <div class="col-md-3"><label>Data fim</label><input type="date" name="data_fim" class="form-control"></div>
                  <div class="col-md-2 hr-hora"><label>Hora fim</label><input type="time" name="hora_fim" class="form-control"></div>
                  <div class="col-md-2 d-flex align-items-end">
                    <div class="custom-control custom-checkbox" style="margin-bottom:8px;">
                      <input type="checkbox" class="custom-control-input" id="dia-inteiro" name="dia_inteiro" value="1">
                      <label class="custom-control-label" for="dia-inteiro">Dia inteiro</label>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:10px;">
                  <div class="col-md-8"><label>Motivo (opcional)</label><input type="text" name="motivo" maxlength="150" class="form-control" placeholder="Ex.: férias, congresso"></div>
                  <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-primary">Adicionar bloqueio</button></div>
                </div>
                <small class="text-muted">Deixe a data fim vazia para bloquear só a data de início.</small>
              </form>
              <? } ?>
            </div>
            <? } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
<script>
$(function(){
  function linhaIntervalo(dia, ini, fim){
    return $('<div class="hr-intervalo">'
      + '<input type="time" class="form-control form-control-sm" name="dias['+dia+'][ini][]">'
      + '<span>até</span>'
      + '<input type="time" class="form-control form-control-sm" name="dias['+dia+'][fim][]">'
      + '<button type="button" class="btn btn-sm btn-link text-danger hr-remover" title="Remover intervalo">✕</button>'
      + '</div>').find('input').eq(0).val(ini || '').end().eq(1).val(fim || '').end().end();
  }
  $(document).on('change', '.hr-atende', function(){
    $(this).closest('.hr-dia').toggleClass('is-off', !this.checked);
  });
  $(document).on('click', '.hr-adicionar', function(){
    var $dia = $(this).closest('.hr-dia');
    $(this).parent().before(linhaIntervalo($dia.data('dia')));
  });
  $(document).on('click', '.hr-remover', function(){
    var $ivs = $(this).closest('.hr-intervalos');
    if($ivs.find('.hr-intervalo').length > 1){ $(this).closest('.hr-intervalo').remove(); }
    else { $(this).closest('.hr-intervalo').find('input').val(''); }
  });
  $('#hr-copiar-segunda').on('click', function(){
    var $seg = $('.hr-dia[data-dia="1"]');
    var pares = $seg.find('.hr-intervalo').map(function(){
      var $i = $(this).find('input');
      return [[$i.eq(0).val(), $i.eq(1).val()]];
    }).get();
    var atende = $seg.find('.hr-atende').prop('checked');
    for(var d = 2; d <= 5; d++){
      var $dia = $('.hr-dia[data-dia="'+d+'"]');
      $dia.find('.hr-atende').prop('checked', atende).trigger('change');
      $dia.find('.hr-intervalo').remove();
      var $add = $dia.find('.hr-adicionar').parent();
      $.each(pares, function(_, p){ $add.before(linhaIntervalo(d, p[0], p[1])); });
    }
  });
  $('#dia-inteiro').on('change', function(){
    $('#form-bloqueio .hr-hora').toggle(!this.checked);
  });
});
</script>
</body>
</html>
```

- [ ] **Step 4: Adicionar item de menu**

Em `includes/adm/menu.php`, dentro de `if(!$menu_is_patient){`, logo após o bloco do item `'label' => 'Calendário'`:

```php
	$menu_operacao_items[] = [
		'label' => 'Horários de atendimento',
		'icon' => 'os-icon-clock',
		'url' => base_url().'adm/horarios',
		'children' => [
			['label' => 'Grade e bloqueios', 'url' => base_url().'adm/horarios'],
		],
	];
```

- [ ] **Step 5: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → `OK disponibilidade_source_test`
Run: `/c/PHP/PHP7.2/php.exe -l` em `application/controllers/adm/Horarios.php`, `application/views/adm/horarios/index.php`, `includes/adm/menu.php` → sem erros.

- [ ] **Step 6: Commit**

```bash
git add application/controllers/adm/Horarios.php application/views/adm/horarios/index.php includes/adm/menu.php tests/disponibilidade_source_test.php
git commit -m "feat(horarios): tela de grade semanal, duracao e bloqueios

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 4: Widget JS + integração com agendamento e remarcação (somente aviso)

**Files:**
- Create: `js/adm/disponibilidade.js`
- Modify: `application/views/adm/atendimento/atendimento.php` (form `#form` de novo agendamento; scripts após linha ~232 `js/main.js`)
- Modify: `application/views/adm/calendario/index.php` (modal `#modal-criar` ~l.181–244; `#remarcar-sub` ~l.260; `calOpenRemarcar()` ~l.548)
- Modify: `application/views/adm/usuarios/new/atendimentos.php` (botão `.btn-remarcar` ~l.515; `.ut-queue-item` ~l.590 e ~l.635; `#remarcacao-box` ~l.422; handler `.btn-remarcar` ~l.712; sheet mobile ~l.836)
- Modify: `tests/disponibilidade_source_test.php`

**Interfaces:**
- Consumes: endpoint `GET adm/horarios/livres` (Task 3).
- Produces: `window.UtecDisponibilidade.attach(opts)` → `{ atualizar: function() }`. `opts`: `baseUrl` (string), `prestador` (function → id), `prestadorEl` (seletor opcional que dispara atualização no `change`), `data` (seletor do input date), `hora` (seletor do input time), `box` (seletor do container), `ignorar` (function opcional → id do agendamento).

- [ ] **Step 1: Estender o teste de fonte (falha)**

Acrescentar antes do `echo` final:

```php
$js = lerArquivo('js/adm/disponibilidade.js');
assertContains('UtecDisponibilidade', $js, 'widget exportado');
assertContains('adm/horarios/livres', $js, 'widget chama endpoint');
assertContains('encaixe', $js, 'widget avisa encaixe');
foreach (array('application/views/adm/atendimento/atendimento.php',
    'application/views/adm/calendario/index.php',
    'application/views/adm/usuarios/new/atendimentos.php') as $v) {
    $src = lerArquivo($v);
    assertContains('js/adm/disponibilidade.js', $src, $v . ' inclui widget');
    assertContains('UtecDisponibilidade.attach(', $src, $v . ' inicializa widget');
}
assertContains('data-prestador-id=', lerArquivo('application/views/adm/usuarios/new/atendimentos.php'), 'remarcacao conhece o prestador');
// cadastrar/remarcar continuam sem validacao de disponibilidade
$atend = lerArquivo('application/controllers/adm/Atendimento.php');
if (strpos($atend, 'disponibilidade') !== false) {
    fwrite(STDERR, 'Atendimento.php nao deve validar disponibilidade no servidor (spec: so aviso).' . PHP_EOL);
    exit(1);
}
```

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → FAIL `Arquivo ausente: js/adm/disponibilidade.js`.

- [ ] **Step 2: Criar `js/adm/disponibilidade.js`**

```js
/* Widget de disponibilidade do profissional: lista horarios livres e avisa encaixe.
 * Nunca impede o envio do formulario. */
(function (window, $) {
  'use strict';

  var MENSAGENS = {
    ocupado: 'Já existe agendamento neste horário para o profissional.',
    fora_da_grade: 'Horário fora da grade de atendimento do profissional.',
    bloqueado: 'O profissional bloqueou este horário.'
  };

  function attach(opts) {
    var $box = $(opts.box);
    var $data = $(opts.data);
    var $hora = $(opts.hora);
    if (!$box.length || !$data.length || !$hora.length) {
      return { atualizar: function () {} };
    }
    var $lista = $('<div class="utec-disp-livres" style="margin-top:8px;"></div>');
    var $alerta = $('<div class="alert alert-warning" style="display:none;margin:8px 0 0;font-size:13px;"></div>');
    $box.empty().append($lista).append($alerta);
    var seqLista = 0;
    var seqHora = 0;

    function idPrestador() { return parseInt(opts.prestador(), 10) || 0; }
    function idIgnorar() { return opts.ignorar ? (parseInt(opts.ignorar(), 10) || 0) : 0; }

    function buscar(hora) {
      return $.getJSON(opts.baseUrl + 'adm/horarios/livres', {
        id_prestador: idPrestador(),
        data: $data.val(),
        ignorar: idIgnorar(),
        hora: hora || ''
      });
    }

    function atualizarLista() {
      var seq = ++seqLista;
      $lista.empty();
      if (!idPrestador() || !$data.val()) { return; }
      buscar('').done(function (r) {
        if (seq !== seqLista || !r || !r.tem_grade) { return; }
        if (!r.livres.length) {
          $lista.append('<small class="text-muted">Sem horários livres nesta data.</small>');
          return;
        }
        $lista.append('<small class="text-muted d-block" style="margin-bottom:4px;">Horários livres (' + r.duracao + ' min):</small>');
        $.each(r.livres, function (_, h) {
          $('<button type="button" class="btn btn-sm btn-outline-success" style="margin:0 4px 4px 0;"></button>')
            .text(h)
            .on('click', function () { $hora.val(h).trigger('change'); })
            .appendTo($lista);
        });
      });
    }

    function verificarHora() {
      var seq = ++seqHora;
      var h = $hora.val();
      $alerta.hide();
      if (!h || !idPrestador() || !$data.val()) { return; }
      buscar(h).done(function (r) {
        if (seq !== seqHora || !r || !r.tem_grade || !r.situacao || r.situacao === 'livre') { return; }
        $alerta.text((MENSAGENS[r.situacao] || 'Horário fora da disponibilidade.') + ' Você pode salvar mesmo assim (encaixe).').show();
      });
    }

    function atualizar() { atualizarLista(); verificarHora(); }

    if (opts.prestadorEl) { $(opts.prestadorEl).on('change', atualizar); }
    $data.on('change', atualizar);
    $hora.on('change', verificarHora);

    return { atualizar: atualizar };
  }

  window.UtecDisponibilidade = { attach: attach };
})(window, jQuery);
```

- [ ] **Step 3: Integrar no novo agendamento (`atendimento/atendimento.php`)**

a) No `<form id="form" ...>`, logo após o `</div>` que fecha a `<div class="row">` dos campos Profissional/Tipo/Data/Horario (antes de `<div style="margin-top:18px;">` do checkbox WhatsApp), inserir:

```php
                  <div id="disp-novo"></div>
```

b) Logo após `<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>`, inserir:

```php
    <script src="<?=base_url()?>js/adm/disponibilidade.js?v=1"></script>
    <script>
      $(function(){
        var disp = UtecDisponibilidade.attach({
          baseUrl: '<?=base_url()?>',
          prestador: function(){ return $('#form [name=id_prestador]').val(); },
          prestadorEl: '#form [name=id_prestador]',
          data: '#form [name=data_agenda]',
          hora: '#form [name=hora_agenda]',
          box: '#disp-novo'
        });
        disp.atualizar();
      });
    </script>
```

- [ ] **Step 4: Integrar no calendário (`calendario/index.php`)**

a) No modal `#modal-criar`, logo após o `</div>` que fecha a `<div class="row">` que contém `#criar-data` e `#criar-hora` (antes do `form-group` do campo "Tipo"), inserir:

```html
          <div id="disp-criar" style="margin-bottom:12px;"></div>
```

b) Em `#remarcar-sub`, logo após o `</div>` que fecha a `<div class="row">` com `#remarcar-data`/`#remarcar-hora` (antes da div com o botão "Salvar remarcação"), inserir:

```html
            <div id="disp-remarcar"></div>
```

c) Logo após `<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>`, inserir:

```php
<script src="<?=base_url()?>js/adm/disponibilidade.js?v=1"></script>
```

d) No `<script>` inline principal, logo após a linha `var _acoesEvento = null;`, inserir:

```js
var dispCriar = null;
var dispRemarcar = null;
$(function(){
  dispCriar = UtecDisponibilidade.attach({
    baseUrl: BASE,
    prestador: function(){ return $('#criar-prestador').val(); },
    prestadorEl: '#criar-prestador',
    data: '#criar-data',
    hora: '#criar-hora',
    box: '#disp-criar'
  });
  $('#modal-criar').on('shown.bs.modal', function(){ dispCriar.atualizar(); });
  dispRemarcar = UtecDisponibilidade.attach({
    baseUrl: BASE,
    prestador: function(){ return _acoesEvento ? (_acoesEvento.extendedProps || {}).prestador_id : 0; },
    data: '#remarcar-data',
    hora: '#remarcar-hora',
    box: '#disp-remarcar',
    ignorar: function(){ return _acoesEvento ? _acoesEvento.id : 0; }
  });
});
```

Confirmar antes que `BASE` já está definido nesse script (é usado em `url: BASE+'adm/atendimento/buscar_paciente'`); se estiver definido depois deste ponto, mover o bloco para logo após a definição de `BASE`.

e) Em `calOpenRemarcar()`, como última linha antes do `}`:

```js
  if (dispRemarcar) { dispRemarcar.atualizar(); }
```

- [ ] **Step 5: Integrar na remarcação da lista (`usuarios/new/atendimentos.php`)**

a) No botão `.btn-remarcar` (~l.515), acrescentar o atributo após `data-hora="..."`:

```php
                                  data-prestador-id="<?=(int)$agenda->id_prestador?>"
```

b) Nos dois `<div class="ut-queue-item"` (~l.590 e ~l.635), acrescentar após `data-prestador="..."`:

```php
         data-prestador-id="<?=(int)$agenda->id_prestador?>"
```

c) No form de `#remarcacao-box`, logo após `<input type="hidden" name="id_agenda" id="remarcar-id-agenda">`, inserir (sem `name`: não vai no POST):

```html
                      <input type="hidden" id="remarcar-prestador-id">
```

e logo após o `</div>` que fecha a `<div class="row">` desse form, antes de `</form>`:

```html
                      <div id="disp-remarcar"></div>
```

d) Logo após `<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>` (~l.702):

```php
    <script src="<?=base_url()?>js/adm/disponibilidade.js?v=1"></script>
```

e) No handler `$(document).on('click', '.btn-remarcar', function(){` (~l.712), acrescentar após `$('#remarcar-hora').val(...)`:

```js
        $('#remarcar-prestador-id').val($(this).data('prestador-id'));
        if (window.dispRemarcar) { window.dispRemarcar.atualizar(); }
```

e no início do mesmo bloco `$(function(){ ... })`/script onde está esse handler, antes dele:

```js
      window.dispRemarcar = UtecDisponibilidade.attach({
        baseUrl: '<?=base_url()?>',
        prestador: function(){ return $('#remarcar-prestador-id').val(); },
        data: '#remarcar-data',
        hora: '#remarcar-hora',
        box: '#disp-remarcar',
        ignorar: function(){ return $('#remarcar-id-agenda').val(); }
      });
```

f) No sheet mobile (`remarcarBtn.onclick`, ~l.836), após `if (horaField) horaField.value = hora;`:

```js
        var prestField = document.getElementById('remarcar-prestador-id');
        if (prestField) prestField.value = el.getAttribute('data-prestador-id') || '';
        if (window.dispRemarcar) window.dispRemarcar.atualizar();
```

Conferir que a variável do elemento clicado nesse escopo se chama `el` (é a mesma usada em `el.getAttribute('data-prestador')` ~l.802); se tiver outro nome, usar esse.

- [ ] **Step 6: Rodar testes e lint**

Run: `/c/PHP/PHP7.2/php.exe tests/disponibilidade_source_test.php` → `OK disponibilidade_source_test`
Run: `/c/PHP/PHP7.2/php.exe -l` nas 3 views alteradas → sem erros.
Run: `node --check js/adm/disponibilidade.js` (se `node` disponível) → sem erros.

- [ ] **Step 7: Commit**

```bash
git add js/adm/disponibilidade.js application/views/adm/atendimento/atendimento.php application/views/adm/calendario/index.php application/views/adm/usuarios/new/atendimentos.php tests/disponibilidade_source_test.php
git commit -m "feat(horarios): sugestao de horarios livres e aviso de encaixe na agenda

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 5: Manual de ajuda + CLAUDE.md

**Files:**
- Modify: `application/libraries/Manual_conteudo.php` (novo capítulo logo após o capítulo `'slug' => 'agenda'`)
- Modify: `tests/manual_conteudo_test.php:33-35`
- Modify: `CLAUDE.md`

- [ ] **Step 1: Atualizar o teste do manual (falha)**

Em `tests/manual_conteudo_test.php`, trocar as linhas 33–35 por:

```php
assertSameValue(13, count($nivel2), 'Nivel 2 deve ver os 13 capitulos.');
assertSameValue(13, count($nivel3), 'Nivel 3 deve ver os 13 capitulos.');
assertSameValue(12, count($nivel4), 'Nivel 4 nao ve o capitulo de equipe.');
```

Run: `/c/PHP/PHP7.2/php.exe tests/manual_conteudo_test.php` → FAIL `Nivel 2 deve ver os 13 capitulos.`

- [ ] **Step 2: Adicionar o capítulo**

Em `Manual_conteudo::capitulos()`, logo após o array do capítulo `'slug' => 'agenda'` (que termina em `'atualizado_em' => '2026-09-07',\n            ),`):

```php
            array(
                'slug' => 'horarios-atendimento',
                'titulo' => 'Horários de atendimento',
                'icone' => 'os-icon-clock',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Cada profissional define em quais dias e horários atende, quanto dura cada consulta e os períodos bloqueados. A agenda usa isso para sugerir horários livres.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Horários de atendimento` no menu lateral.',
                        'Em `Duração da consulta`, escolha quanto tempo cada agendamento ocupa (ex.: 30 minutos).',
                        'Em `Grade semanal`, marque os dias de atendimento e informe um ou mais intervalos por dia, como 08:00 às 12:00 e 14:00 às 18:00. O botão `Copiar segunda para seg–sex` repete a grade de segunda nos demais dias úteis.',
                        'Em `Bloqueios`, cadastre férias, feriados ou qualquer período sem atendimento. Marque `Dia inteiro` para bloquear datas completas.',
                        'Ao agendar ou remarcar, o sistema mostra os horários livres do profissional na data escolhida. Se você escolher um horário ocupado, bloqueado ou fora da grade, aparece um aviso, mas o agendamento pode ser salvo como encaixe.',
                        'Consultas canceladas liberam o horário automaticamente.',
                    ),
                    2 => array('Configura os horários de todos os profissionais da clínica, escolhendo o profissional no topo da tela.'),
                    3 => array('Configura os próprios horários, duração da consulta e bloqueios.'),
                    4 => array('Consulta os horários dos profissionais para orientar os pacientes; alterações ficam com o profissional ou o estabelecimento.'),
                ),
                'atualizado_em' => '2026-09-22',
            ),
```

(Sem chave `print` — screenshots seguem pendentes conforme seção 19 do CLAUDE.md. `VERSAO` não muda: estrutura do array é a mesma.)

- [ ] **Step 3: Rodar testes**

Run: `/c/PHP/PHP7.2/php.exe tests/manual_conteudo_test.php` → passa (exit 0).
Run: `/c/PHP/PHP7.2/php.exe -l application/libraries/Manual_conteudo.php` → sem erros.

- [ ] **Step 4: Atualizar CLAUDE.md**

- Seção 4.2, bloco **Saúde e Agenda**, acrescentar:
  - `` `prestador_horarios` — grade semanal do prestador (`id_prestador`, `dia_semana` 0=dom…6=sáb, `hora_inicio`, `hora_fim`); vários intervalos por dia ``
  - `` `prestador_bloqueios` — períodos sem atendimento (`id_prestador`, `inicio`, `fim` DATETIME, `motivo`) ``
  - `` `usuarios.duracao_atendimento_min` — duração fixa da consulta do prestador (NULL = 30 min) ``
- Seção 6.2, tabela, nova linha: `` | `Horarios.php` | `/adm/horarios` | Grade semanal, duração e bloqueios do prestador (edita: 1, 2 no escopo, 3 o próprio; 4 só vê) + endpoint JSON `adm/horarios/livres` usado pela agenda | ``
- Seção 7, nova subseção `### 7.7 Disponibilidade_model` com a tabela de métodos da Task 2 (`get_config`, `salvar_config`, `listar_bloqueios_futuros`, `adicionar_bloqueio`, `remover_bloqueio`, `horarios_livres`, `verificar_horario`, `proximos_livres`) e a nota: "Cálculo puro em `application/helpers/disponibilidade_helper.php` (testes em `tests/disponibilidade_*`). Ocupam vaga agendamentos `status IN (0,1,2)`. Na agenda manual é só aviso (encaixe permitido). `proximos_livres()` é a interface prevista para o chatbot de IA marcar consultas."
- Seção 13, tabela, nova linha: `` | `adm/dev/migrar_horarios_atendimento` | Cria `prestador_horarios` + `prestador_bloqueios` e a coluna `usuarios.duracao_atendimento_min` (idempotente) | ``
- Seção 15.1, acrescentar: `- [x] Horários de atendimento por profissional (grade semanal, duração, bloqueios) com sugestão de horários livres e aviso de encaixe na agenda — base para o chatbot marcar consultas`

- [ ] **Step 5: Rodar a suíte completa**

```bash
for t in tests/disponibilidade_helper_test.php tests/disponibilidade_source_test.php tests/manual_conteudo_test.php; do /c/PHP/PHP7.2/php.exe "$t" || echo "FALHOU: $t"; done
```

Expected: três linhas `OK ...`/sem `FALHOU`.

- [ ] **Step 6: Commit**

```bash
git add application/libraries/Manual_conteudo.php tests/manual_conteudo_test.php CLAUDE.md
git commit -m "docs(horarios): capitulo do manual e documentacao do modulo

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

## Deploy (fora do escopo de implementação — responsabilidade do agente-dev-infra)

Arquivos runtime: `application/helpers/disponibilidade_helper.php`, `application/models/Disponibilidade_model.php`, `application/controllers/adm/Horarios.php`, `application/controllers/adm/Dev.php`, `application/views/adm/horarios/index.php`, `includes/adm/menu.php`, `js/adm/disponibilidade.js`, `application/views/adm/atendimento/atendimento.php`, `application/views/adm/calendario/index.php`, `application/views/adm/usuarios/new/atendimentos.php`, `application/libraries/Manual_conteudo.php`. Depois: rodar `adm/dev/migrar_horarios_atendimento` logado como nível 1 e healthcheck (`adm/horarios` 200, `adm/horarios/livres?id_prestador=0` → 403 JSON).
