# Manual de Ajuda v2 — Redesenho Visual Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Substituir o CSS/HTML básico do manual v1 (blocos com borda simples) por um design com gradiente azul→teal, lombada colorida, checklist com checkmark e capa de página inteira — nas 3 views (`manual_funcao.php`, `manual_pdf_capa.php`, `manual_pdf_conteudo.php`), sem alterar controller nem conteúdo.

**Architecture:** Puramente CSS/HTML em cima do que já funciona. Não toca em `Manual_conteudo.php` (texto) nem em `Usuarios.php` (controller, já com a correção de `error_reporting` em produção). As 7 imagens de capítulo já foram copiadas/criadas e mapeadas em `imagens/manual/` (sessão anterior, já commitado).

**Tech Stack:** PHP7 + CodeIgniter 3.1.10, mPDF v6 (CSS limitado — sem flexbox/grid/inline-block confiável, ver "Restrições verificadas" abaixo), navegador real pra tela web (CSS moderno completo liberado).

Spec: `docs/superpowers/specs/2026-09-22-manual-ajuda-v2-redesign-design.md`

---

## Restrições do mPDF verificadas nesta sessão (renderização real testada, não suposição)

Antes de escrever as views de PDF, foi gerado um PDF de teste isolado exercitando cada padrão CSS candidato e o resultado foi inspecionado visualmente (via leitura direta do PDF). Resultado:

- ✅ `background-image: linear-gradient(...)` funciona em `<div>` e em `<td>` (fundo de página inteira na capa E fundo de célula de tabela no cabeçalho do capítulo).
- ✅ `border-radius:50%` funciona em `<div>` simples (círculo decorativo na capa).
- ✅ `rgba(255,255,255,.22)` funciona como cor.
- ✅ `display:table`/`table-cell` funciona pra lombada + cabeçalho lado a lado.
- ✅ Checklist com `<span class="check">` (`display:inline-block`, `width`/`height`/`border-radius`/`background-color`) renderiza aceitável **dentro do fluxo de texto de um `<li>`** — pequeno selo verde arredondado, legível.
- ❌ `display:inline-block` em um `<div>` sozinho (badge circular do número do capítulo, fora de fluxo de texto) **não funciona** — vira um quadrado sem o texto centralizado dentro, quebra o layout. **Decisão: número do capítulo no PDF é um rótulo de texto simples ("CAPÍTULO 07"), sem círculo — só a capa mantém o círculo decorativo (sem texto dentro, esse funciona).**
- Todo o restante do CSS abaixo é cópia exata do que foi testado e confirmado, não invenção nova.

---

## Mapa de arquivos

| Arquivo | Ação |
|---|---|
| `application/views/adm/usuarios/manual_pdf_capa.php` | Reescrever (capa + bloco `<style>` compartilhado com o conteúdo) |
| `application/views/adm/usuarios/manual_pdf_conteudo.php` | Reescrever (cabeçalho gradiente + lombada + checklist) |
| `application/views/adm/usuarios/manual_funcao.php` | Reescrever (mesma linguagem visual, CSS de navegador completo) |

Nenhum arquivo novo, nenhuma migração, nenhuma mudança de controller.

---

### Task 1: Capa do PDF (`manual_pdf_capa.php`)

**Files:**
- Modify: `application/views/adm/usuarios/manual_pdf_capa.php`

- [ ] **Step 1: Substituir o arquivo inteiro**

```php
<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10.5pt; line-height:1.5; }
  h1 { margin:0; }

  .manual-pdf-capa { background-image: linear-gradient(160deg, #047bf8, #20c997); height: 255mm; padding: 40pt 34pt; color:#fff; }
  .manual-pdf-capa-marca { font-size:10pt; letter-spacing:3pt; text-transform:uppercase; font-weight:bold; opacity:.9; margin-bottom:130pt; }
  .manual-pdf-capa-circulo { width:34pt; height:34pt; border-radius:50%; background-color: rgba(255,255,255,.22); margin-bottom:14pt; }
  .manual-pdf-capa-titulo { font-size:30pt; font-weight:bold; line-height:1.15; margin-bottom:10pt; }
  .manual-pdf-capa-sub { font-size:12pt; opacity:.92; line-height:1.6; max-width:380pt; }
  .manual-pdf-capa-rodape { font-size:9pt; opacity:.75; margin-top:130pt; }

  table.manual-pdf-cabecalho { border-collapse:collapse; width:100%; }
  .manual-pdf-capitulo { margin-bottom:14pt; }
  .manual-pdf-lombada { background-color:#047bf8; width:10pt; }
  .manual-pdf-cabecalho-cel { background-image: linear-gradient(135deg, #047bf8, #20c997); padding:14pt 16pt; color:#fff; }
  .manual-pdf-numero { font-size:9pt; letter-spacing:2pt; text-transform:uppercase; color:rgba(255,255,255,.85); margin-bottom:4pt; }
  h2.manual-pdf-titulo { font-size:15pt; font-weight:bold; margin:0; color:#fff; }
  .manual-pdf-corpo { background-color:#fff; padding:14pt 16pt 6pt; border:0.5pt solid #e2e8f0; border-top:none; }
  .manual-pdf-resumo { color:#475569; margin:0 0 10pt; }
  .manual-pdf-print { width:100%; border:0.5pt solid #e2e8f0; margin-bottom:10pt; }
  .manual-pdf-checklist { margin:0 0 4pt; padding:0; list-style:none; }
  .manual-pdf-checklist li { margin-bottom:7pt; padding-left:2pt; }
  .manual-pdf-checklist .check { display:inline-block; width:13pt; height:13pt; line-height:13pt; text-align:center; border-radius:50%; background-color:#20c997; color:#fff; font-size:8pt; margin-right:5pt; }
</style>
<div class="manual-pdf-capa">
  <div class="manual-pdf-capa-marca">UTEC Saúde</div>
  <div class="manual-pdf-capa-circulo"></div>
  <div class="manual-pdf-capa-titulo"><?=$manual['title']?></div>
  <div class="manual-pdf-capa-sub"><?=$manual['subtitle']?></div>
  <div class="manual-pdf-capa-rodape">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
</div>
```

Nota: esse `<style>` é compartilhado — o controller (`Usuarios::manual_pdf()`, já existente, não mexer) chama `WriteHTML($html_capa)` antes de `WriteHTML($html_conteudo)` na mesma instância do mPDF, então as classes `.manual-pdf-cabecalho`/`.manual-pdf-corpo`/etc. definidas aqui (mesmo não usadas na capa) ficam disponíveis pro Task 2. Esse é o mesmo padrão já usado no v1 — não é novidade.

- [ ] **Step 2: Validar sintaxe**

Run: `C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_pdf_capa.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add application/views/adm/usuarios/manual_pdf_capa.php
git commit -m "feat(manual): redesenha capa do PDF com gradiente pagina inteira

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 2: Conteúdo do PDF (`manual_pdf_conteudo.php`)

**Files:**
- Modify: `application/views/adm/usuarios/manual_pdf_conteudo.php`

- [ ] **Step 1: Substituir o arquivo inteiro**

```php
<? $primeiro = true; $numero = 0; foreach($manual['capitulos'] as $capitulo){ $numero++; ?>
<div class="manual-pdf-capitulo"<?=$primeiro ? '' : ' style="page-break-before:always;"'?>>
  <table class="manual-pdf-cabecalho" cellspacing="0" cellpadding="0">
    <tr>
      <td class="manual-pdf-lombada"></td>
      <td class="manual-pdf-cabecalho-cel">
        <div class="manual-pdf-numero">CAPÍTULO <?=sprintf('%02d', $numero)?></div>
        <h2 class="manual-pdf-titulo"><?=$capitulo['titulo']?></h2>
      </td>
    </tr>
  </table>
  <div class="manual-pdf-corpo">
    <p class="manual-pdf-resumo"><?=$capitulo['resumo']?></p>
    <? if($capitulo['print']){ ?>
    <img class="manual-pdf-print" src="<?=FCPATH.'imagens/manual/'.$capitulo['print']?>">
    <? } ?>
    <ul class="manual-pdf-checklist">
      <? foreach($capitulo['topicos'] as $topico){ ?>
      <li><span class="check">&#10003;</span> <?=$topico?></li>
      <? } ?>
    </ul>
  </div>
</div>
<? $primeiro = false; } ?>
```

Importante: o título do capítulo continua sendo um `<h2>` (agora com a classe
`manual-pdf-titulo` sobrescrevendo a aparência) — isso é obrigatório, não
cosmético: o controller configura `$mpdf->h2toc = array('H2' => 0);` (Task 3
do plano v1, já em produção) pra montar o sumário automático a partir das
tags `<h2>`. Se o título virasse uma `<div>` comum, o sumário do PDF ficaria
vazio.

- [ ] **Step 2: Validar sintaxe**

Run: `C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_pdf_conteudo.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add application/views/adm/usuarios/manual_pdf_conteudo.php
git commit -m "feat(manual): redesenha capitulos do PDF com cabecalho gradiente e checklist

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 3: Verificação end-to-end do PDF (3 níveis + sumário)

Sem WAMP local disponível (mesma limitação do v1) — verificação via script
PHP que simula exatamente o que o controller faz, usando `C:\PHP\PHP7.2\php.exe`
(mPDF não roda em PHP 8).

**Files:** nenhum (só verificação)

- [ ] **Step 1: Criar o script de verificação (temporário, não commitar)**

Criar `_verify_v2_tmp.php` na raiz do projeto:

```php
<?php
define('BASEPATH', __DIR__ . '/application/');
define('APPPATH', __DIR__ . '/application/');
define('FCPATH', __DIR__ . '/');

require __DIR__ . '/application/libraries/Manual_conteudo.php';
require __DIR__ . '/application/libraries/M_pdf.php';

$titulos = [
    2 => ['titulo' => 'Manual do Estabelecimento', 'subtitulo' => 'Guia da clinica/estabelecimento para gerir equipe, pacientes, agenda e assinatura.', 'slug' => 'manual-estabelecimento-nivel-2'],
    3 => ['titulo' => 'Manual do Prestador', 'subtitulo' => 'Guia do profissional para atender pacientes, acompanhar agenda e operar dentro da clinica.', 'slug' => 'manual-prestador-nivel-3'],
    4 => ['titulo' => 'Manual do Colaborador', 'subtitulo' => 'Guia de secretaria e apoio operacional para agenda, pacientes e rotina compartilhada.', 'slug' => 'manual-colaborador-nivel-4'],
];

$manual_conteudo = new Manual_conteudo();
$falhas = 0;

foreach ([2, 3, 4] as $nivel) {
    $manual = [
        'level' => $nivel,
        'title' => $titulos[$nivel]['titulo'],
        'subtitle' => $titulos[$nivel]['subtitulo'],
        'pdf_title' => $titulos[$nivel]['titulo'] . ' - UTEC Saude',
        'pdf_slug' => $titulos[$nivel]['slug'],
        'versao' => Manual_conteudo::VERSAO,
        'gerado_em' => date('d/m/Y'),
        'capitulos' => $manual_conteudo->capitulos_por_nivel($nivel),
    ];

    ob_start();
    include __DIR__ . '/application/views/adm/usuarios/manual_pdf_capa.php';
    $html_capa = ob_get_clean();
    ob_start();
    include __DIR__ . '/application/views/adm/usuarios/manual_pdf_conteudo.php';
    $html_conteudo = ob_get_clean();

    $nivel_erro_anterior = error_reporting();
    error_reporting(0);
    $wrapper = new M_pdf();
    $mpdf = $wrapper->pdf;
    $mpdf->SetTitle($manual['pdf_title']);
    $mpdf->SetAuthor('UTec Saude');
    $mpdf->SetHTMLHeader('<div style="text-align:right;font-size:8pt;color:#64748b;border-bottom:0.5pt solid #e2e8f0;padding-bottom:4px;">'.$manual['title'].'</div>');
    $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:8pt;color:#94a3b8;border-top:0.5pt solid #e2e8f0;padding-top:4px;">Manual v'.$manual['versao'].' &middot; gerado em '.$manual['gerado_em'].' &middot; pagina {PAGENO} de {nb}</div>');
    $mpdf->h2toc = array('H2' => 0);
    $mpdf->WriteHTML($html_capa);
    $mpdf->WriteHTML('<tocpagebreak toc-preHTML="&lt;h1&gt;Sumario&lt;/h1&gt;" links="on" />');
    $mpdf->WriteHTML($html_conteudo);
    $mpdf->Output(__DIR__ . '/_verify_v2_nivel' . $nivel . '.pdf', 'F');
    error_reporting($nivel_erro_anterior);

    $qtdEsperada = ($nivel == 4) ? 11 : 12;
    $qtdReal = count($manual['capitulos']);
    $tamanho = filesize(__DIR__ . '/_verify_v2_nivel' . $nivel . '.pdf');
    $ok = ($qtdReal === $qtdEsperada) && ($tamanho > 50000);
    if (!$ok) $falhas++;
    echo "Nivel $nivel: capitulos=$qtdReal (esperado $qtdEsperada), arquivo=" . round($tamanho/1024) . "KB -> " . ($ok ? "OK" : "FALHA") . "\n";
}

echo $falhas === 0 ? "TODOS OS NIVEIS OK\n" : "$falhas FALHA(S)\n";
```

- [ ] **Step 2: Rodar e conferir tamanho/contagem**

Run: `C:\PHP\PHP7.2\php.exe _verify_v2_tmp.php`
Expected: `TODOS OS NIVEIS OK`, com os 3 níveis mostrando `OK` e tamanho de
arquivo maior que o v1 sem imagens (v1 sem imagens ficava ~106-111KB; os 7
capítulos com screenshot somam ~780KB de PNG brutos — o script já falha
sozinho se o arquivo final ficar abaixo de 50KB, mas verifique manualmente
se o tamanho subiu bem acima dos ~110KB do v1; se ficar quase igual, as
imagens não foram embutidas — confira o caminho
`FCPATH.'imagens/manual/'.$capitulo['print']` e se os arquivos existem em
`imagens/manual/`).

- [ ] **Step 3: Inspecionar visualmente o PDF do nível 2 (o mais completo, com o capítulo Equipe)**

Use a ferramenta de leitura de arquivo (Read) no arquivo gerado
`_verify_v2_nivel2.pdf` (raiz do projeto) e confira visualmente:
- Capa: gradiente cobrindo a página, título/subtítulo legíveis em branco.
- Sumário: lista de capítulos com número de página (confirma que o `h2toc`
  capturou os `<h2 class="manual-pdf-titulo">` — se o sumário aparecer vazio,
  o Task 2 tem um bug, volte lá e confirme que o título é mesmo uma tag `<h2>`).
- Pelo menos 2-3 páginas de capítulo: cabeçalho com gradiente + lombada azul
  à esquerda + rótulo "CAPÍTULO NN" + título; corpo com resumo, screenshot
  (nos capítulos que têm) e checklist com selo verde.

- [ ] **Step 4: Limpar os arquivos temporários**

```bash
rm -f _verify_v2_tmp.php _verify_v2_nivel2.pdf _verify_v2_nivel3.pdf _verify_v2_nivel4.pdf
```

Também conferir `git status --short application/third_party/mpdf/ttfontdata/`
— deve voltar vazio (nenhum arquivo de cache de fonte novo deveria aparecer,
já que as fontes `dejavusans`/`dejavusansB` já foram pré-aquecidas e
commitadas no v1). Se aparecer algo novo, `git checkout -- application/third_party/mpdf/ttfontdata/`
pra descartar (não é pra commitar cache gerado ad-hoc de verificação).

Sem commit nesta task — é só verificação. Se algo falhar, voltar nas Tasks 1/2
e corrigir antes de seguir.

---

### Task 4: Tela web (`manual_funcao.php`)

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
      .manual-hero { background:linear-gradient(135deg,#047bf8,#20c997); border-radius:24px; padding:36px 32px; color:#fff; box-shadow:0 20px 40px rgba(4,123,248,.18); }
      .manual-hero-label { font-size:11px; letter-spacing:.14em; text-transform:uppercase; opacity:.85; font-weight:700; margin-bottom:10px; }
      .manual-title { font-size:34px; line-height:1.1; font-weight:800; margin:0 0 10px; color:#fff; }
      .manual-copy { color:rgba(255,255,255,.92); font-size:15px; line-height:1.7; max-width:640px; }
      .manual-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:20px; }
      .manual-actions .btn-primary { background:#fff; color:#047bf8; border:none; font-weight:700; }
      .manual-actions .btn-outline-secondary { border-color:rgba(255,255,255,.5); color:#fff; }

      .manual-capitulo { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 10px 24px rgba(15,23,42,.06); }
      .manual-capitulo-head { background:linear-gradient(135deg,#047bf8,#20c997); display:flex; align-items:center; gap:14px; padding:18px 22px; color:#fff; border-left:10px solid #047bf8; }
      .manual-capitulo-badge { width:34px; height:34px; min-width:34px; border-radius:50%; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; }
      .manual-capitulo-head h6 { margin:0; font-size:16px; font-weight:800; color:#fff; }
      .manual-capitulo-body { padding:20px 22px 22px; }
      .manual-capitulo-resumo { color:#475569; margin:0 0 14px; line-height:1.7; }
      .manual-print { width:100%; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:16px; display:block; }
      .manual-checklist { list-style:none; margin:0; padding:0; color:#334155; }
      .manual-checklist li { display:flex; align-items:flex-start; gap:10px; margin-bottom:11px; line-height:1.6; }
      .manual-checklist li:last-child { margin-bottom:0; }
      .manual-check { width:18px; height:18px; min-width:18px; border-radius:50%; background:#20c997; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; margin-top:2px; }
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
                  <div class="manual-hero-label">Ajuda permanente</div>
                  <h1 class="manual-title"><?=$manual['title']?></h1>
                  <div class="manual-copy"><?=$manual['subtitle']?></div>
                  <div class="manual-actions">
                    <a href="<?=base_url()?>adm/usuarios/manual_pdf/<?=$manual['level']?>" class="btn btn-primary" target="_blank">Abrir PDF</a>
                    <a href="<?=base_url()?>adm/usuarios/dash" class="btn btn-outline-secondary">Voltar ao painel</a>
                  </div>
                </div>

                <? $numero = 0; foreach($manual['capitulos'] as $capitulo){ $numero++; ?>
                <div class="manual-capitulo">
                  <div class="manual-capitulo-head">
                    <div class="manual-capitulo-badge"><?=sprintf('%02d', $numero)?></div>
                    <h6><?=$capitulo['titulo']?></h6>
                  </div>
                  <div class="manual-capitulo-body">
                    <p class="manual-capitulo-resumo"><?=$capitulo['resumo']?></p>
                    <? if($capitulo['print']){ ?>
                    <img class="manual-print" src="<?=base_url().'imagens/manual/'.$capitulo['print']?>" alt="<?=$capitulo['titulo']?>">
                    <? } ?>
                    <ul class="manual-checklist">
                      <? foreach($capitulo['topicos'] as $topico){ ?>
                      <li><span class="manual-check">&#10003;</span><span><?=$topico?></span></li>
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

Run: `C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_funcao.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add application/views/adm/usuarios/manual_funcao.php
git commit -m "feat(manual): redesenha tela web com gradiente, badge e checklist

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01H4VDCGv468mtKLNGKNcM7Z"
```

---

### Task 5: Verificação final + deploy

**Files:** nenhum (verificação + deploy)

- [ ] **Step 1: `php -l` no conjunto completo**

Run:
```bash
C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_pdf_capa.php
C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_pdf_conteudo.php
C:\PHP\PHP7.2\php.exe -l application/views/adm/usuarios/manual_funcao.php
```
Expected: `No syntax errors detected` nos 3.

- [ ] **Step 2: Confirmar `git status` limpo (sem sobra de arquivo de teste)**

Run: `git status --short`
Expected: nada relacionado a `_verify_v2_*` ou `_test_*` — só os 3 arquivos já
commitados nas Tasks 1/2/4 (que devem aparecer como já commitados, não como
pendências).

- [ ] **Step 3: Deploy via FTP (skill `ftp`)**

Enviar pro servidor de produção:
```
application/views/adm/usuarios/manual_pdf_capa.php
application/views/adm/usuarios/manual_pdf_conteudo.php
application/views/adm/usuarios/manual_funcao.php
```

Usar `node .claude/skills/ftp/upload.js` com os 3 caminhos (mesmo padrão já
usado no deploy do v1 nesta sessão) — confirmar antes que é produção e que os
arquivos já passaram pela verificação da Task 3.

- [ ] **Step 4: Healthcheck básico pós-deploy**

Run:
```bash
curl -s -o /dev/null -w "manual/2 -> %{http_code}\n" https://utecnologia.com.br/adm/usuarios/manual/2 --max-time 15
curl -s -o /dev/null -w "manual_pdf/2 -> %{http_code}\n" https://utecnologia.com.br/adm/usuarios/manual_pdf/2 --max-time 15
```
Expected: `302` nos dois (redirect de sessão — sem erro fatal), igual ao
healthcheck já usado no deploy do v1.

- [ ] **Step 5: Pedir pro usuário confirmar visualmente**

Pedir que o usuário abra, logado, `adm/usuarios/manual/{2,3,4}` e
`adm/usuarios/manual_pdf/{2,3,4}` em produção e confirme que o novo visual
(gradiente, lombada, checklist, screenshots reais) aparece corretamente nos
3 níveis, e que o sumário do PDF funciona.
