<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Leads — UTecnologia Saúde</title>
    <link rel="stylesheet" href="<?= base_url('bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('bower_components/datatables/media/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/clicklinica-main.css') ?>">
    <style>
        .badge-pendente       { background:#6c757d; }
        .badge-contatado      { background:#0d6efd; }
        .badge-contatado_form { background:#0dcaf0; color:#000; }
        .badge-interessado    { background:#198754; }
        .badge-descartado     { background:#dc3545; }
        .badge-sem_form       { background:#ffc107; color:#000; }
        .stat-card { border-radius:10px; padding:16px 20px; color:#fff; margin-bottom:12px; }
        .whatsapp-btn { background:#25D366; color:#fff; border:none; border-radius:5px;
                        padding:4px 10px; font-size:12px; white-space:nowrap; }
        .whatsapp-btn:hover { background:#1da851; color:#fff; text-decoration:none; }
        .filter-bar { background:#f8f9fa; border-radius:8px; padding:16px; margin-bottom:20px; }
        tr.interessado { background:#f0fff4 !important; }
    </style>
</head>
<body>
<?php #$this->load->view('adm/leads/index.php'); // menu lateral ?>

<div class="content-page">
<div class="content">
<div class="container-fluid">

    <!-- Header -->
    <div class="row mt-3 mb-2">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="mdi mdi-account-multiple"></i> Gestão de Leads — PE</h4>
            <span class="text-muted small">Total: <strong><?= number_format($total) ?></strong> leads</span>
        </div>
    </div>

    <!-- Cards de stats -->
    <div class="row mb-3">
        <div class="col-md-2 col-6">
            <div class="stat-card" style="background:#6c757d;">
                <div style="font-size:22px;font-weight:bold;"><?= $stats['total'] ?></div>
                <div style="font-size:12px;">Total</div>
            </div>
        </div>
        <?php foreach($stats['por_status'] as $s): ?>
        <div class="col-md-2 col-6">
            <div class="stat-card badge-<?= $s->status_abordagem ?>" style="background:<?= [
                'pendente'=>'#6c757d','contatado'=>'#0d6efd','contatado_form'=>'#0dcaf0',
                'interessado'=>'#198754','descartado'=>'#dc3545','sem_form'=>'#ffc107'
            ][$s->status_abordagem] ?? '#333' ?>;">
                <div style="font-size:22px;font-weight:bold;"><?= $s->qtd ?></div>
                <div style="font-size:11px;"><?= str_replace('_',' ', $s->status_abordagem) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="col-md-2 col-6">
            <div class="stat-card" style="background:#198754;">
                <div style="font-size:22px;font-weight:bold;"><?= $stats['com_telefone'] ?></div>
                <div style="font-size:12px;">Com telefone</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card" style="background:#0a4fc4;">
                <div style="font-size:22px;font-weight:bold;"><?= $stats['com_email'] ?></div>
                <div style="font-size:12px;">Com e-mail</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-bar">
        <form method="GET" action="<?= site_url('adm/leads') ?>" class="form-row align-items-end">
            <div class="col-md-3 col-6 mb-2">
                <input type="text" name="busca" class="form-control form-control-sm"
                       placeholder="Buscar nome, e-mail, telefone..."
                       value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>">
            </div>
            <div class="col-md-2 col-6 mb-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todos os status</option>
                    <?php foreach(['pendente','contatado','contatado_form','interessado','descartado','sem_form'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($filtros['status']==$st)?'selected':'' ?>>
                        <?= ucfirst(str_replace('_',' ',$st)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <select name="especialidade" class="form-control form-control-sm">
                    <option value="">Todas especialidades</option>
                    <?php foreach($especialidades as $e): ?>
                    <option value="<?= $e->especialidade ?>" <?= ($filtros['especialidade']==$e->especialidade)?'selected':'' ?>>
                        <?= ucfirst($e->especialidade) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <select name="cidade" class="form-control form-control-sm">
                    <option value="">Todas cidades</option>
                    <?php foreach($cidades as $c): ?>
                    <option value="<?= $c->cidade ?>" <?= ($filtros['cidade']==$c->cidade)?'selected':'' ?>>
                        <?= $c->cidade ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 col-6 mb-2">
                <select name="tipo" class="form-control form-control-sm">
                    <option value="">Tipo</option>
                    <option value="clinica"      <?= ($filtros['tipo']=='clinica')?'selected':'' ?>>Clínica</option>
                    <option value="profissional" <?= ($filtros['tipo']=='profissional')?'selected':'' ?>>Profissional</option>
                </select>
            </div>
            <div class="col-md-2 col-6 mb-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm mr-1">Filtrar</button>
                <a href="<?= site_url('adm/leads') ?>" class="btn btn-outline-secondary btn-sm">Limpar</a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Especialidade</th>
                            <th>Cidade</th>
                            <th>Contato</th>
                            <th>Site</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($leads)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Nenhum lead encontrado.</td></tr>
                    <?php else: ?>
                    <?php foreach($leads as $lead): ?>
                    <tr class="<?= $lead->status_abordagem == 'interessado' ? 'interessado' : '' ?>">
                        <td class="text-muted small"><?= $lead->id ?></td>
                        <td>
                            <strong><?= htmlspecialchars($lead->nome) ?></strong>
                            <br><span class="badge badge-light"><?= $lead->tipo ?></span>
                        </td>
                        <td><?= ucfirst($lead->especialidade) ?></td>
                        <td><?= $lead->cidade ?></td>
                        <td style="white-space:nowrap;">
                            <?php if($lead->telefone): ?>
                                <?php
                                    $tel_limpo = preg_replace('/\D/', '', $lead->telefone);
                                    if(strlen($tel_limpo) <= 10) $tel_limpo = '55'.$tel_limpo;
                                    elseif(substr($tel_limpo,0,2) != '55') $tel_limpo = '55'.$tel_limpo;
                                ?>
                                <a href="https://wa.me/<?= $tel_limpo ?>?text=<?= urlencode('Olá! Sou Igor Barros da UTecnologia Saúde. Vi seu contato e gostaria de apresentar nosso sistema de gestão clínica com 30 dias grátis. Posso te contar mais?') ?>"
                                   target="_blank" class="whatsapp-btn d-inline-block mb-1">
                                   💬 <?= htmlspecialchars($lead->telefone) ?>
                                </a><br>
                            <?php endif; ?>
                            <?php if($lead->email): ?>
                                <small><a href="mailto:<?= htmlspecialchars($lead->email) ?>">
                                    ✉ <?= htmlspecialchars($lead->email) ?>
                                </a></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($lead->site): ?>
                                <a href="<?= htmlspecialchars($lead->site) ?>" target="_blank"
                                   class="text-primary small" title="<?= htmlspecialchars($lead->site) ?>">
                                   🌐 Ver site
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $lead->status_abordagem ?>">
                                <?= str_replace('_',' ', $lead->status_abordagem) ?>
                            </span>
                            <?php if($lead->observacoes): ?>
                                <br><small class="text-muted"><?= htmlspecialchars(mb_substr($lead->observacoes,0,40)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td style="white-space:nowrap;">
                            <button class="btn btn-xs btn-outline-secondary"
                                    onclick="abrirModal(<?= $lead->id ?>, '<?= addslashes($lead->nome) ?>', '<?= $lead->status_abordagem ?>', '<?= addslashes($lead->observacoes) ?>')">
                                ✏️ Status
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <?php if($total_paginas > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center">
            <?php for($p=1; $p<=$total_paginas; $p++): ?>
            <li class="page-item <?= $p==$pagina?'active':'' ?>">
                <a class="page-link" href="<?= site_url('adm/leads').'?pagina='.$p
                    .(!empty($filtros['status'])?'&status='.$filtros['status']:'')
                    .(!empty($filtros['especialidade'])?'&especialidade='.$filtros['especialidade']:'')
                    .(!empty($filtros['cidade'])?'&cidade='.$filtros['cidade']:'')
                    .(!empty($filtros['busca'])?'&busca='.urlencode($filtros['busca']):'') ?>">
                    <?= $p ?>
                </a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

</div><!-- container -->
</div><!-- content -->
</div><!-- content-page -->

<!-- Modal de status -->
<div class="modal fade" id="modalStatus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Atualizar Status</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p class="font-weight-bold" id="modal_nome"></p>
        <div class="form-group">
            <label>Status</label>
            <select id="modal_status" class="form-control">
                <option value="pendente">Pendente</option>
                <option value="contatado">Contatado (e-mail)</option>
                <option value="contatado_form">Contatado (formulário)</option>
                <option value="interessado">Interessado ⭐</option>
                <option value="descartado">Descartado</option>
                <option value="sem_form">Sem formulário</option>
            </select>
        </div>
        <div class="form-group">
            <label>Observações</label>
            <textarea id="modal_obs" class="form-control" rows="3" placeholder="Anotações sobre o contato..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarStatus()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url('bower_components/jquery/dist/jquery.min.js') ?>"></script>
<script src="<?= base_url('bower_components/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
<script>
var modalLeadId = null;

function abrirModal(id, nome, status, obs) {
    modalLeadId = id;
    document.getElementById('modal_nome').innerText = nome;
    document.getElementById('modal_status').value = status;
    document.getElementById('modal_obs').value = obs || '';
    $('#modalStatus').modal('show');
}

function salvarStatus() {
    $.post('<?= site_url("adm/leads/atualizar_status") ?>', {
        id: modalLeadId,
        status: document.getElementById('modal_status').value,
        observacoes: document.getElementById('modal_obs').value
    }, function(r) {
        if(r.ok) { location.reload(); }
        else { alert('Erro: ' + r.msg); }
    }, 'json');
}
</script>
</body>
</html>
