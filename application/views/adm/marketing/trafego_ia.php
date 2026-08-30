<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<title>Trafego de IA | UTEC</title>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
	<link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
	<style>
		.mkt-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin-bottom:24px; }
		.mkt-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px 20px; box-shadow:0 10px 24px rgba(15,23,42,.05); }
		.mkt-label { color:#64748b; font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
		.mkt-value { color:#0f172a; font-size:26px; font-weight:700; margin-top:6px; }
		.mkt-panel { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px 20px; margin-bottom:22px; }
		.mkt-panel h6 { margin:0 0 14px; font-weight:700; }
		.mkt-table { width:100%; border-collapse:collapse; font-size:13px; }
		.mkt-table th, .mkt-table td { text-align:left; padding:8px 10px; border-bottom:1px solid #eef2f7; }
		.mkt-table th { color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:.04em; }
		.mkt-filter { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-bottom:20px; }
		.mkt-filter label { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:4px; }
		.mkt-filter input, .mkt-filter select { border:1px solid #cbd5e1; border-radius:8px; padding:7px 10px; font-size:13px; }
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
				<li class="breadcrumb-item"><span>Marketing</span></li>
				<li class="breadcrumb-item"><span>Trafego de IA</span></li>
			</ul>
			<div class="content-i">
				<div class="content-box">
					<div class="element-wrapper">
						<h6 class="element-header">Trafego de IA</h6>
						<p style="color:#64748b;font-size:13px;">Acessos vindos de assistentes de IA (ChatGPT, Gemini, Claude, Perplexity, Copilot, DeepSeek, Grok) e conversoes atribuidas a essas origens.</p>
					</div>

					<? if(!$schema_ok){ ?>
						<div class="alert alert-warning">
							As tabelas de monitoramento ainda nao existem. Execute
							<strong><a href="<?=base_url()?>adm/dev/migrar_monitoramento_ia"><?=base_url()?>adm/dev/migrar_monitoramento_ia</a></strong>.
						</div>
					<? } else { ?>

					<form method="get" action="<?=base_url()?>adm/marketing/trafego_ia" class="mkt-filter">
						<div>
							<label>De</label>
							<input type="date" name="start_date" value="<?=htmlspecialchars($summary['periodo'][0])?>">
						</div>
						<div>
							<label>Ate</label>
							<input type="date" name="end_date" value="<?=htmlspecialchars($summary['periodo'][1])?>">
						</div>
						<div>
							<label>Fonte</label>
							<select name="source">
								<option value="">Todas</option>
								<? foreach($sources as $s){ ?>
									<option value="<?=htmlspecialchars($s['source'])?>" <?=($filtros_raw['source'] === $s['source'] ? 'selected' : '')?>><?=htmlspecialchars(ucfirst($s['source']))?></option>
								<? } ?>
							</select>
						</div>
						<div>
							<button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
							<a href="<?=base_url()?>adm/marketing/trafego_ia" class="btn btn-link btn-sm">Limpar</a>
						</div>
					</form>

					<div class="mkt-grid">
						<div class="mkt-card"><div class="mkt-label">Acessos IA hoje</div><div class="mkt-value"><?=(int)$summary['acessos_hoje']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Acessos IA 7 dias</div><div class="mkt-value"><?=(int)$summary['acessos_7d']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Acessos IA 30 dias</div><div class="mkt-value"><?=(int)$summary['acessos_30d']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Conversoes IA (periodo)</div><div class="mkt-value"><?=(int)$summary['conversoes']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Taxa de conversao IA</div><div class="mkt-value"><?=number_format((float)$summary['taxa_conversao'], 2, ',', '.')?>%</div></div>
						<div class="mkt-card"><div class="mkt-label">Receita atribuida</div><div class="mkt-value">R$ <?=number_format((float)$summary['receita'], 2, ',', '.')?></div></div>
					</div>

					<div class="mkt-panel">
						<h6>Acessos e conversoes por dia (<?=htmlspecialchars($summary['periodo'][0])?> a <?=htmlspecialchars($summary['periodo'][1])?>)</h6>
						<canvas id="chartTimeline" height="90"></canvas>
					</div>

					<div class="mkt-panel">
						<h6>Trafego por fonte de IA</h6>
						<canvas id="chartSources" height="90"></canvas>
					</div>

					<div class="mkt-panel">
						<h6>Landing pages mais acessadas</h6>
						<table class="mkt-table">
							<thead><tr><th>Pagina</th><th>Acessos</th><th>Conversoes</th></tr></thead>
							<tbody>
								<? if(empty($pages)){ ?><tr><td colspan="3" style="color:#94a3b8;">Sem dados no periodo.</td></tr><? } ?>
								<? foreach($pages as $p){ ?>
									<tr><td><?=htmlspecialchars($p->landing_page)?></td><td><?=(int)$p->acessos?></td><td><?=(int)$p->conversoes?></td></tr>
								<? } ?>
							</tbody>
						</table>
					</div>

					<div class="mkt-panel">
						<h6>Conversao por origem</h6>
						<table class="mkt-table">
							<thead><tr><th>Fonte</th><th>Visitas</th><th>Conversoes</th><th>Taxa</th><th>Receita</th></tr></thead>
							<tbody>
								<? if(empty($conv)){ ?><tr><td colspan="5" style="color:#94a3b8;">Sem dados no periodo.</td></tr><? } ?>
								<? foreach($conv as $c){ ?>
									<tr>
										<td><?=htmlspecialchars(ucfirst($c->ai_source ? $c->ai_source : 'outros'))?></td>
										<td><?=(int)$c->visitas?></td>
										<td><?=(int)$c->conversoes?></td>
										<td><?=number_format((float)$c->taxa, 2, ',', '.')?>%</td>
										<td>R$ <?=number_format((float)$c->receita, 2, ',', '.')?></td>
									</tr>
								<? } ?>
							</tbody>
						</table>
					</div>

					<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?=base_url()?>bower_components/chart.js/dist/Chart.bundle.min.js"></script>
<script>
(function () {
<? if($schema_ok){ ?>
	var timeline = <?=json_encode($timeline)?>;
	var sources  = <?=json_encode(array_values(array_filter($sources, function($s){ return $s['total'] > 0; })))?>;

	var tl = document.getElementById('chartTimeline');
	if (tl && window.Chart) {
		new Chart(tl.getContext('2d'), {
			type: 'line',
			data: {
				labels: timeline.map(function (d) { return d.dia; }),
				datasets: [
					{ label: 'Acessos',    data: timeline.map(function (d) { return d.acessos; }),    borderColor: '#0f766e', backgroundColor: 'rgba(15,118,110,.12)', fill: true, lineTension: .3 },
					{ label: 'Conversoes', data: timeline.map(function (d) { return d.conversoes; }), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,.12)', fill: true, lineTension: .3 }
				]
			},
			options: { responsive: true, maintainAspectRatio: true, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
		});
	}

	var sc = document.getElementById('chartSources');
	if (sc && window.Chart) {
		new Chart(sc.getContext('2d'), {
			type: 'bar',
			data: {
				labels: sources.map(function (s) { return s.source; }),
				datasets: [{ label: 'Acessos', data: sources.map(function (s) { return s.total; }), backgroundColor: '#0f766e' }]
			},
			options: { responsive: true, maintainAspectRatio: true, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
		});
	}
<? } ?>
})();
</script>
</body>
</html>
