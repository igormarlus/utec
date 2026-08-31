# Relatorio de Usuarios Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transformar `adm/usuarios` em um relatorio responsivo com linhas amplas, atividade contextual, indicadores operacionais/comerciais e fallback de avatar sem foto.

**Architecture:** A consulta de listagem em `Usuarios.php` passa a montar um conjunto mais rico de dados para a view, incluindo nome da especialidade e indicadores agregados por usuario. A view `application/views/adm/usuarios/new/lista.php` troca o layout de cards por linhas responsivas baseadas em grid, preservando os links e filtros atuais e usando pequenas funcoes puras para a logica de apresentacao mais sensivel.

**Tech Stack:** PHP 7, CodeIgniter 3, Bootstrap 4, jQuery, CSS inline da view, testes focados em PHP CLI

---

## Mapa de Arquivos

| Acao | Arquivo | Responsabilidade |
|---|---|---|
| **Criar** | `application/helpers/usuarios_relatorio_helper.php` | Funcoes puras para resolver atividade, status comercial e formatacao leve do relatorio |
| **Criar** | `tests/usuarios_relatorio_helper_test.php` | Testes CLI para a logica nova de apresentacao |
| **Modificar** | `application/controllers/adm/Usuarios.php` | Enriquecer consultas de `Index()` e `rel()` com especialidade e indicadores agregados |
| **Modificar** | `application/views/adm/usuarios/new/lista.php` | Novo layout de relatorio responsivo, acoes no inicio e avatar com icone |

## Task 1: Criar helper testavel do relatorio

**Files:**
- Create: `application/helpers/usuarios_relatorio_helper.php`
- Test: `tests/usuarios_relatorio_helper_test.php`

- [ ] **Step 1: Escrever o teste falhando para atividade e status comercial**

```php
<?php
require __DIR__ . '/../application/helpers/usuarios_relatorio_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . " esperado [" . $expected . "] mas recebeu [" . $actual . "]\n");
        exit(1);
    }
}

$prestador = array(
    'nivel' => 3,
    'profissao' => 'Clinico geral',
    'especialidade_nome' => 'Odontologia',
);
assertSameValue('Odontologia', utec_relatorio_resolve_atividade($prestador), 'atividade prestador');

$colaborador = array(
    'nivel' => 4,
    'profissao' => 'Atendente',
    'especialidade_nome' => 'Psicologia',
);
assertSameValue('Atendente', utec_relatorio_resolve_atividade($colaborador), 'atividade colaborador');

$semDados = array(
    'nivel' => 2,
    'profissao' => '',
    'especialidade_nome' => '',
);
assertSameValue('Nao informado', utec_relatorio_resolve_atividade($semDados), 'atividade vazia');

$trial = array(
    'tenant_status' => 1,
    'subscription_status' => 'trialing',
);
assertSameValue('Trial', utec_relatorio_resolve_plano_status($trial), 'status trial');

$paid = array(
    'tenant_status' => 1,
    'subscription_status' => 'authorized',
);
assertSameValue('Pago', utec_relatorio_resolve_plano_status($paid), 'status pago');

$blocked = array(
    'tenant_status' => 0,
    'subscription_status' => 'authorized',
);
assertSameValue('Bloqueado', utec_relatorio_resolve_plano_status($blocked), 'status bloqueado');

$none = array(
    'tenant_status' => null,
    'subscription_status' => '',
);
assertSameValue('', utec_relatorio_resolve_plano_status($none), 'status vazio');

echo "OK\n";
```

- [ ] **Step 2: Rodar o teste para confirmar falha**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `FAIL` ou erro de funcao indefinida para `utec_relatorio_resolve_atividade()`

- [ ] **Step 3: Implementar o helper minimo**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('utec_relatorio_read')) {
    function utec_relatorio_read($row, $key) {
        if (is_array($row)) {
            return isset($row[$key]) ? $row[$key] : null;
        }
        if (is_object($row)) {
            return isset($row->$key) ? $row->$key : null;
        }
        return null;
    }
}

if (!function_exists('utec_relatorio_resolve_atividade')) {
    function utec_relatorio_resolve_atividade($row) {
        $nivel = (int) utec_relatorio_read($row, 'nivel');
        $especialidade = trim((string) utec_relatorio_read($row, 'especialidade_nome'));
        $profissao = trim((string) utec_relatorio_read($row, 'profissao'));

        if ($nivel === 3 && $especialidade !== '') {
            return $especialidade;
        }
        if ($profissao !== '') {
            return $profissao;
        }
        return 'Nao informado';
    }
}

if (!function_exists('utec_relatorio_resolve_plano_status')) {
    function utec_relatorio_resolve_plano_status($row) {
        $tenantStatus = utec_relatorio_read($row, 'tenant_status');
        $subscriptionStatus = strtolower(trim((string) utec_relatorio_read($row, 'subscription_status')));

        if ($tenantStatus !== null && (int) $tenantStatus !== 1) {
            return 'Bloqueado';
        }
        if ($subscriptionStatus === 'trialing') {
            return 'Trial';
        }
        if (in_array($subscriptionStatus, array('authorized', 'active'), true)) {
            return 'Pago';
        }
        if ($tenantStatus !== null || $subscriptionStatus !== '') {
            return 'Sem plano';
        }
        return '';
    }
}
```

- [ ] **Step 4: Rodar o teste para confirmar sucesso**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add application/helpers/usuarios_relatorio_helper.php tests/usuarios_relatorio_helper_test.php
git commit -m "test: add helper tests for usuarios report rules"
```

## Task 2: Enriquecer a consulta de listagem no controller

**Files:**
- Modify: `application/controllers/adm/Usuarios.php`
- Test: `tests/usuarios_relatorio_helper_test.php`

- [ ] **Step 1: Escrever teste falhando para mapeamento de status comercial complementar**

Adicionar ao fim de `tests/usuarios_relatorio_helper_test.php`:

```php
$expiredTrial = array(
    'tenant_status' => 1,
    'subscription_status' => 'pending',
);
assertSameValue('Sem plano', utec_relatorio_resolve_plano_status($expiredTrial), 'status sem plano');
```

- [ ] **Step 2: Rodar o teste para verificar comportamento atual**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `FAIL` se o helper nao mapear `pending` como `Sem plano`

- [ ] **Step 3: Implementar o carregamento dos dados no controller**

Adicionar no `__construct()`:

```php
$this->load->helper('usuarios_relatorio');
```

Extrair uma funcao privada no controller:

```php
private function carregar_relatorio_usuarios($nivel = null)
{
    $dd_user = $this->padrao_model->get_usuario_logado();
    $scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
    $scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);
    $has_especialidades = $this->db->table_exists('usuarios_especialidades');
    $has_tenants = $this->db->field_exists('tenant_id', 'usuarios') && $this->db->table_exists('saas_tenants');
    $has_subscriptions = $this->db->table_exists('saas_subscriptions');

    $select = "u.*";
    $join = "";
    if ($has_especialidades) {
        $select .= ", ue.nome AS especialidade_nome";
        $join .= " LEFT JOIN usuarios_especialidades ue ON ue.id = u.especialidade";
    } else {
        $select .= ", '' AS especialidade_nome";
    }

    if ($has_tenants) {
        $select .= ", st.status AS tenant_status";
        $join .= " LEFT JOIN saas_tenants st ON st.id = u.tenant_id";
    } else {
        $select .= ", NULL AS tenant_status";
    }

    if ($has_subscriptions) {
        $select .= ", ss.status AS subscription_status";
        $join .= " LEFT JOIN saas_subscriptions ss ON ss.tenant_id = u.tenant_id AND ss.is_primary = 1";
    } else {
        $select .= ", '' AS subscription_status";
    }

    $where = array();
    if ($nivel !== null) {
        $where[] = "u.nivel = " . (int) $nivel;
    }
    if ((int) $dd_user->nivel !== 1) {
        $where[] = "u.id IN (" . $scope_sql . ")";
    }

    $sql = "SELECT " . $select . " FROM usuarios u" . $join;
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY u.nome ASC";

    $usuarios = $this->db->query($sql)->result();
    $user_ids = array();
    foreach ($usuarios as $usuario) {
        $user_ids[] = (int) $usuario->id;
    }

    $agendamentosPorPrestador = array();
    $pacientesPorPrestador = array();
    if (!empty($user_ids) && $this->db->table_exists('agendamentos')) {
        $ids_sql = implode(',', $user_ids);
        $agRows = $this->db->query(
            "SELECT id_prestador, COUNT(id) AS total_agendamentos, COUNT(DISTINCT id_paciente) AS total_pacientes, MAX(data_agenda) AS ultima_atividade
             FROM agendamentos
             WHERE id_prestador IN (" . $ids_sql . ")
             GROUP BY id_prestador"
        )->result();
        foreach ($agRows as $agRow) {
            $agendamentosPorPrestador[(int) $agRow->id_prestador] = $agRow;
        }
    }

    foreach ($usuarios as $usuario) {
        $usuario->total_agendamentos = 0;
        $usuario->total_pacientes = 0;
        $usuario->ultima_atividade = '';

        if ((int) $usuario->nivel === 3 && isset($agendamentosPorPrestador[(int) $usuario->id])) {
            $agg = $agendamentosPorPrestador[(int) $usuario->id];
            $usuario->total_agendamentos = (int) $agg->total_agendamentos;
            $usuario->total_pacientes = (int) $agg->total_pacientes;
            $usuario->ultima_atividade = (string) $agg->ultima_atividade;
        }
    }

    return array(
        'usuarios' => $usuarios,
        'usuario_logado' => $dd_user,
    );
}
```

Atualizar `Index()` e `rel($nivel)` para usar essa funcao e converter `usuarios` para um objeto que a view atual espera:

```php
$relatorio = $this->carregar_relatorio_usuarios(null);
$dados['usuarios'] = $relatorio['usuarios'];
$dados['usuario_logado'] = $relatorio['usuario_logado'];
```

- [ ] **Step 4: Rodar o teste para confirmar que o helper segue verde**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `OK`

- [ ] **Step 5: Fazer smoke check de sintaxe**

Run: `php -l application/controllers/adm/Usuarios.php`
Expected: `No syntax errors detected`

- [ ] **Step 6: Commit**

```bash
git add application/controllers/adm/Usuarios.php
git commit -m "feat: load richer data for usuarios report"
```

## Task 3: Migrar a view para linhas de relatorio responsivas

**Files:**
- Modify: `application/views/adm/usuarios/new/lista.php`
- Modify: `application/helpers/usuarios_relatorio_helper.php`
- Test: `tests/usuarios_relatorio_helper_test.php`

- [ ] **Step 1: Escrever teste falhando para label de contadores vazios**

Adicionar ao teste:

```php
assertSameValue('0', utec_relatorio_formatar_numero(0), 'contador zero');
assertSameValue('12', utec_relatorio_formatar_numero(12), 'contador inteiro');
```

- [ ] **Step 2: Rodar o teste e confirmar falha por funcao indefinida**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `FAIL` em `utec_relatorio_formatar_numero()`

- [ ] **Step 3: Implementar funcao minima de formatacao no helper**

Adicionar em `application/helpers/usuarios_relatorio_helper.php`:

```php
if (!function_exists('utec_relatorio_formatar_numero')) {
    function utec_relatorio_formatar_numero($valor) {
        return (string) ((int) $valor);
    }
}
```

- [ ] **Step 4: Reescrever a view para o novo layout**

Substituir o bloco principal da listagem por uma estrutura baseada em linhas:

```php
<?php $usuarios_lista = is_array($usuarios) ? $usuarios : $usuarios->result(); ?>
<div class="ul-report-list" id="ul-grid">
  <div class="ul-report-head d-none d-xl-grid">
    <span>Acoes</span>
    <span>Usuario</span>
    <span>Atividade</span>
    <span>Indicadores</span>
    <span>Contato</span>
    <span>Cadastro</span>
    <span>Status</span>
  </div>
  <?php foreach ($usuarios_lista as $u): ?>
    <?php
      $ativo = (int) $u->status === 1;
      $atividade = utec_relatorio_resolve_atividade($u);
      $plano = utec_relatorio_resolve_plano_status($u);
      $telefone_limpo = preg_replace('/[^0-9]/', '', (string) $u->telefone);
    ?>
    <div class="ul-report-row" data-nome="<?=mb_strtolower($u->nome, 'UTF-8')?>">
      <div class="ul-col ul-col-actions">
        <?php if ((int) $nivel === 5): ?><a href="<?=base_url()?>adm/usuarios/prontuario/<?=$u->id?>" class="ul-btn ul-btn-prontuario">Prontuario</a><?php endif; ?>
        <a href="<?=base_url()?>adm/usuarios/edicao/<?=$u->id?>" class="ul-btn ul-btn-edit">Editar</a>
        <?php if ($u->telefone): ?><a href="https://api.whatsapp.com/send?phone=55<?=$telefone_limpo?>" target="_blank" class="ul-btn ul-btn-whats">WhatsApp</a><?php endif; ?>
        <?php if ($this->session->userdata('nivel') == 1): ?><a href="<?=base_url()?>admin/logar_como/<?=$u->id?>" class="ul-btn ul-btn-acessar" target="_blank">Acessar</a><?php endif; ?>
      </div>
      <div class="ul-col ul-col-user">
        <div class="ul-avatar">
          <?php if ($u->img): ?>
            <img src="<?=base_url()?>imagens/usuarios/min/<?=$u->img?>" alt="<?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?>">
          <?php else: ?>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z" /></svg>
          <?php endif; ?>
        </div>
        <div>
          <strong class="ul-card-name"><?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?></strong>
          <div class="ul-card-sub">ID #<?=$u->id?><?php if ($nivel < 5 && $u->login): ?> · <?=htmlspecialchars($u->login, ENT_QUOTES, 'UTF-8')?><?php endif; ?></div>
        </div>
      </div>
      <div class="ul-col"><span class="ul-inline-label">Atividade</span><?=$atividade?></div>
      <div class="ul-col">
        <span class="ul-inline-label">Indicadores</span>
        <div class="ul-kpis">
          <span><?=utec_relatorio_formatar_numero($u->total_agendamentos)?> marcacoes</span>
          <span><?=utec_relatorio_formatar_numero($u->total_pacientes)?> pacientes</span>
          <?php if ($plano !== ''): ?><span><?=$plano?></span><?php endif; ?>
        </div>
      </div>
      <div class="ul-col">
        <span class="ul-inline-label">Contato</span>
        <div><?=htmlspecialchars((string) $u->telefone, ENT_QUOTES, 'UTF-8')?></div>
        <?php if (!empty($u->email)): ?><div><?=htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8')?></div><?php endif; ?>
      </div>
      <div class="ul-col"><span class="ul-inline-label">Cadastro</span><?php if ($u->dt_cadastro): ?><?=date('d/m/Y', strtotime($u->dt_cadastro))?><?php endif; ?></div>
      <div class="ul-col"><span class="ul-status-pill <?=$ativo ? 'ul-status-ativo' : 'ul-status-inativo'?>"><?=$ativo ? 'Ativo' : 'Inativo'?></span></div>
    </div>
  <?php endforeach; ?>
</div>
```

- [ ] **Step 5: Ajustar CSS e JS da busca na mesma view**

Adicionar estilos para `.ul-report-list`, `.ul-report-row`, `.ul-col-actions`, `.ul-inline-label`, `.ul-kpis`, `.ul-btn-whats` e atualizar o filtro para alternar `.ul-report-row` em vez de `.ul-card`.

- [ ] **Step 6: Rodar o teste do helper e smoke de sintaxe**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `OK`

Run: `php -l application/views/adm/usuarios/new/lista.php`
Expected: `No syntax errors detected`

- [ ] **Step 7: Commit**

```bash
git add application/views/adm/usuarios/new/lista.php application/helpers/usuarios_relatorio_helper.php tests/usuarios_relatorio_helper_test.php
git commit -m "feat: redesign usuarios report as responsive rows"
```

## Task 4: Revisar indicadores por perfil e finalizar verificacao

**Files:**
- Modify: `application/controllers/adm/Usuarios.php`
- Modify: `application/views/adm/usuarios/new/lista.php`
- Test: `tests/usuarios_relatorio_helper_test.php`

- [ ] **Step 1: Escrever teste falhando para visibilidade reduzida do atendente**

Adicionar ao teste:

```php
assertSameValue(false, utec_relatorio_mostra_plano_por_nivel(4), 'atendente sem plano');
assertSameValue(true, utec_relatorio_mostra_plano_por_nivel(3), 'profissional com plano');
```

- [ ] **Step 2: Rodar o teste e confirmar falha**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `FAIL` em `utec_relatorio_mostra_plano_por_nivel()`

- [ ] **Step 3: Implementar regra minima e aplicar na view**

Adicionar no helper:

```php
if (!function_exists('utec_relatorio_mostra_plano_por_nivel')) {
    function utec_relatorio_mostra_plano_por_nivel($nivel) {
        return in_array((int) $nivel, array(1, 2, 3), true);
    }
}
```

Na view, exibir o chip de plano apenas quando:

```php
<?php if ($plano !== '' && utec_relatorio_mostra_plano_por_nivel($u->nivel)): ?>
  <span><?=$plano?></span>
<?php endif; ?>
```

Opcionalmente esconder o bloco de indicadores para paciente:

```php
<?php if ((int) $u->nivel !== 5): ?>
```

- [ ] **Step 4: Rodar verificacoes finais**

Run: `php tests/usuarios_relatorio_helper_test.php`
Expected: `OK`

Run: `php -l application/helpers/usuarios_relatorio_helper.php`
Expected: `No syntax errors detected`

Run: `php -l application/controllers/adm/Usuarios.php`
Expected: `No syntax errors detected`

Run: `php -l application/views/adm/usuarios/new/lista.php`
Expected: `No syntax errors detected`

- [ ] **Step 5: Commit**

```bash
git add application/helpers/usuarios_relatorio_helper.php application/controllers/adm/Usuarios.php application/views/adm/usuarios/new/lista.php tests/usuarios_relatorio_helper_test.php
git commit -m "feat: finish usuarios report indicators by profile"
```

## Self-Review

- Spec coverage: o plano cobre layout responsivo, acoes no inicio, atividade, avatar sem foto, indicadores operacionais e status comercial.
- Sem placeholders: cada task tem arquivos, comando e implementacao concreta.
- Consistencia: a regra de apresentacao critica fica centralizada no helper para ser testada sem depender de bootstrap do CodeIgniter.

## Execution Handoff

Plan complete and saved to `docs/superpowers/plans/2026-08-31-relatorio-usuarios.md`. Two execution options:

**1. Subagent-Driven (recommended)** - I dispatch a fresh subagent per task, review between tasks, fast iteration

**2. Inline Execution** - Execute tasks in this session using executing-plans, batch execution with checkpoints

Which approach?
