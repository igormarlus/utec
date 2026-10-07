# Vitrine de Funcionalidades (SEO/GEO — Entrega 1) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Um catálogo único de funcionalidades (16 itens) exibido em `/experimentar`, `/assinar`, home e 5 landings, com FAQ novas, `llms.txt`, `sitemap.xml` e ledger atualizados.

**Architecture:** `application/libraries/Funcionalidades_conteudo.php` (array estático, métodos estáticos) + partial `application/views/public/partials/funcionalidades.php` (grade/lista) chamado com `$this->load->view(...)`. A home usa o catálogo com a marcação de cards dela. Nenhum controller muda.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, HTML/CSS sem frameworks novos.

Spec: `docs/superpowers/specs/2026-10-06-vitrine-funcionalidades-design.md` · Branch: `feat/vitrine-funcionalidades`.

**Desvio aprovado da spec:** o partial recebe `$func_ids` (array de ids ou `null` = todos) em vez de `$func_itens` — as páginas não precisam carregar a library.

## Global Constraints
- PHP: testes e `php -l` só com `C:/PHP/PHP7.2/php.exe`. Sem sintaxe PHP 8.
- Textos dos itens: exatamente os da tabela da spec (seção "Catálogo — itens"); sem "tenant", "owner", "endpoint", "migra". `resumo` ≤ 90 caracteres (mb_strlen).
- Grupos e rótulos: `agenda` → Agenda, `prontuario` → Prontuário, `whatsapp` → WhatsApp, `gestao` → Gestão (nesta ordem).
- Itens `novo = true`: `tempo_espera`, `ficha_paciente`, `exportar_prontuario`, `rotulos` (exatamente 4).
- Toda chamada ao partial passa as 4 chaves `func_formato`, `func_ids`, `func_agrupar`, `func_titulo` (o CI3 reaproveita variáveis entre `load->view`, então não confiar em valor padrão).
- Toda saída escapada com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`; links via `base_url()`.
- Arquivos UTF-8 sem BOM; preservar line endings de cada arquivo existente.
- Testes: scripts em `tests/`, `exit(1)` na falha, imprimem `OK`.
- Commits: `git add <paths>` explícito (NUNCA `-u`/`-A` — outra sessão pode ter arquivos não commitados na pasta); mensagem termina com linha em branco + `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/libraries/Funcionalidades_conteudo.php` | Criar | Catálogo |
| `tests/funcionalidades_catalogo_test.php` | Criar | Testes do catálogo |
| `application/views/public/partials/funcionalidades.php` | Criar | Render grade/lista |
| `tests/funcionalidades_partial_test.php` | Criar | Render com dados reais |
| `tests/funcionalidades_source_test.php` | Criar | Fiação nas páginas |
| `application/views/public/experimentar.php`, `assinar.php` | Modificar | Cadastro |
| `application/views/index-front.php` | Modificar | Home |
| 5 landings em `application/views/public/seo/` | Modificar | Bloco + FAQ |
| `sitemap.xml`, `llms.txt`, `docs/seo-geo-agente-ledger.md`, `docs/produto/evolucao-30-dias-google-doc.md`, `CLAUDE.md` | Modificar | SEO/GEO e docs |

---

### Task 1: Catálogo

**Files:** Create `application/libraries/Funcionalidades_conteudo.php`, `tests/funcionalidades_catalogo_test.php`

**Interfaces — Produces:** `Funcionalidades_conteudo::grupos(): array`, `::itens(): array` (lista de arrays com `id, grupo, icone, titulo, resumo, descricao, link, novo`), `::por_grupo($grupo): array`, `::por_ids(array $ids): array` (ordem pedida, ignora ids inexistentes).

- [ ] **Step 1: Failing test** — `tests/funcionalidades_catalogo_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';

function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function igual($e, $a, $l) { if ($e !== $a) { falha($l . ' | esperado ' . var_export($e, true) . ' obtido ' . var_export($a, true)); } }

$itens = Funcionalidades_conteudo::itens();
igual(16, count($itens), '16 itens');
igual(array('agenda' => 'Agenda', 'prontuario' => 'Prontuário', 'whatsapp' => 'WhatsApp', 'gestao' => 'Gestão'), Funcionalidades_conteudo::grupos(), 'grupos');

$ids = array(); $novos = array();
$rotas = file_get_contents(__DIR__ . '/../application/config/routes.php');
foreach ($itens as $i) {
    foreach (array('id', 'grupo', 'icone', 'titulo', 'resumo', 'descricao') as $k) {
        if (!isset($i[$k]) || trim((string)$i[$k]) === '') { falha('campo vazio ' . $k . ' em ' . (isset($i['id']) ? $i['id'] : '?')); }
    }
    if (!array_key_exists('link', $i) || !array_key_exists('novo', $i)) { falha('link/novo ausente em ' . $i['id']); }
    if (in_array($i['id'], $ids, true)) { falha('id duplicado ' . $i['id']); }
    $ids[] = $i['id'];
    if (!array_key_exists($i['grupo'], Funcionalidades_conteudo::grupos())) { falha('grupo invalido ' . $i['id']); }
    if (mb_strlen($i['resumo'], 'UTF-8') > 90) { falha('resumo > 90 em ' . $i['id']); }
    if ($i['link'] !== '' && strpos($rotas, "\$route['" . $i['link'] . "']") === false) { falha('link sem rota: ' . $i['link']); }
    if ($i['novo'] === true) { $novos[] = $i['id']; }
    foreach (array('tenant', 'owner', 'endpoint', 'migra') as $proibido) {
        if (stripos($i['titulo'] . ' ' . $i['resumo'] . ' ' . $i['descricao'], $proibido) !== false) { falha('termo tecnico "' . $proibido . '" em ' . $i['id']); }
    }
}
sort($novos);
igual(array('exportar_prontuario', 'ficha_paciente', 'rotulos', 'tempo_espera'), $novos, '4 novidades');
igual(array('agenda', 'horarios', 'tempo_espera', 'prontuario', 'prontuario_especialidade', 'ficha_paciente', 'exames', 'exportar_prontuario',
    'whatsapp_confirmacao', 'whatsapp_lembrete', 'chatbot', 'rotulos', 'avisos', 'relatorios', 'equipe', 'manual'), $ids, 'ordem dos ids');

igual(3, count(Funcionalidades_conteudo::por_grupo('whatsapp')), 'grupo whatsapp');
$sel = Funcionalidades_conteudo::por_ids(array('rotulos', 'nao_existe', 'agenda'));
igual(array('rotulos', 'agenda'), array_map(function ($i) { return $i['id']; }, $sel), 'por_ids ordem e ignora inexistente');
igual(array(), Funcionalidades_conteudo::por_ids(array()), 'por_ids vazio');

echo "OK\n";
```
- [ ] **Step 2: Run** → falha (arquivo ausente).
- [ ] **Step 3: Implement** — `application/libraries/Funcionalidades_conteudo.php`. Cabeçalho e métodos:
```php
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
            // um array por linha da tabela da spec, NESTA ordem, com as chaves:
            // 'id', 'grupo', 'icone', 'titulo', 'resumo', 'descricao', 'link' ('' quando vazio), 'novo' (true/false)
        );
    }
}
```
Preencher `itens()` com os 16 itens copiando **literalmente** os textos da tabela "Catálogo — itens" da spec (`docs/superpowers/specs/2026-10-06-vitrine-funcionalidades-design.md`): coluna "link" vazia → `''`; "sim" → `true`, "não" → `false`. Exemplo do primeiro item:
```php
            array(
                'id' => 'agenda', 'grupo' => 'agenda', 'icone' => '📅', 'titulo' => 'Agenda inteligente',
                'resumo' => 'Consultas por profissional, com filtros, remarcação e cancelamento na própria agenda.',
                'descricao' => 'Visão do dia, da semana e do mês por profissional. Remarque e cancele direto na agenda, sem retrabalho.',
                'link' => '', 'novo' => false,
            ),
```
Exceção de link: se `application/config/routes.php` contiver a rota `chatbot-para-clinicas`, o item `chatbot` usa `'link' => 'chatbot-para-clinicas'` (landing dedicada); senão `confirmacao-de-consulta-por-whatsapp`.
- [ ] **Step 4: Run** → `OK`; `php -l`.
- [ ] **Step 5: Commit** — `feat(site): catalogo unico de funcionalidades`

---

### Task 2: Partial

**Files:** Create `application/views/public/partials/funcionalidades.php`, `tests/funcionalidades_partial_test.php`

**Interfaces — Consumes:** Task 1. **Produces:** `$this->load->view('public/partials/funcionalidades', array('func_formato' => 'grade'|'lista', 'func_ids' => array|null, 'func_agrupar' => bool, 'func_titulo' => string))`.

- [ ] **Step 1: Failing test** — `tests/funcionalidades_partial_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
function base_url($p = '') { return 'https://exemplo.test/' . $p; }
require __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';

function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function render($vars) {
    extract($vars);
    ob_start();
    include __DIR__ . '/../application/views/public/partials/funcionalidades.php';
    return ob_get_clean();
}
set_error_handler(function ($no, $str) { falha('notice no partial: ' . $str); });

$grade = render(array('func_formato' => 'grade', 'func_ids' => null, 'func_agrupar' => true, 'func_titulo' => 'Tudo o que está incluído'));
foreach (array('Agenda', 'Prontuário', 'WhatsApp', 'Gestão', 'Tudo o que está incluído', 'Agenda inteligente', 'Manual de ajuda') as $t) {
    if (strpos($grade, htmlspecialchars($t, ENT_QUOTES, 'UTF-8')) === false) { falha('grade sem: ' . $t); }
}
if (substr_count($grade, 'class="fx-novo"') !== 4) { falha('esperado 4 selos Novo, veio ' . substr_count($grade, 'class="fx-novo"')); }
if (strpos($grade, 'https://exemplo.test/sistema-prontuario-eletronico') === false) { falha('link saiba mais ausente'); }
if (substr_count($grade, '<style') !== 1) { falha('css deve sair uma vez'); }

$lista = render(array('func_formato' => 'lista', 'func_ids' => array('agenda', 'chatbot'), 'func_agrupar' => false, 'func_titulo' => ''));
if (strpos($lista, '<ul class="fx-lista"') === false) { falha('lista sem ul'); }
if (substr_count($lista, '<li') !== 2) { falha('lista deve ter 2 itens'); }
if (strpos($lista, '<style') !== false) { falha('css repetido na segunda chamada'); }
if (strpos($lista, '<h2') !== false) { falha('titulo vazio nao deve gerar h2'); }

echo "OK\n";
```
- [ ] **Step 2: Run** → falha.
- [ ] **Step 3: Implement** — `application/views/public/partials/funcionalidades.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!class_exists('Funcionalidades_conteudo')) { get_instance()->load->library('funcionalidades_conteudo'); }
$fx_formato = (isset($func_formato) && $func_formato === 'lista') ? 'lista' : 'grade';
$fx_itens = (isset($func_ids) && is_array($func_ids)) ? Funcionalidades_conteudo::por_ids($func_ids) : Funcionalidades_conteudo::itens();
$fx_agrupar = !empty($func_agrupar) && $fx_formato === 'grade';
$fx_titulo = isset($func_titulo) ? trim((string)$func_titulo) : '';
$fx_e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$fx_card = function ($i) use ($fx_e) {
    $h = '<div class="fx-card"><span class="fx-ico" aria-hidden="true">' . $fx_e($i['icone']) . '</span>'
        . '<h4 class="fx-titulo">' . $fx_e($i['titulo'])
        . ($i['novo'] ? ' <span class="fx-novo" aria-label="Novidade">Novo</span>' : '') . '</h4>'
        . '<p class="fx-resumo">' . $fx_e($i['resumo']) . '</p>';
    if ($i['link'] !== '') {
        $h .= '<a class="fx-link" href="' . $fx_e(base_url($i['link'])) . '">Saiba mais<span class="fx-sr"> sobre ' . $fx_e($i['titulo']) . '</span> →</a>';
    }
    return $h . '</div>';
};
if (empty($GLOBALS['fx_css_ok'])) { $GLOBALS['fx_css_ok'] = true; ?>
<style>
  .fx-wrap { margin: 8px 0 0; }
  .fx-wrap h2.fx-h2 { font-size: 22px; font-weight: 800; margin: 0 0 16px; }
  .fx-wrap h3.fx-grupo { font-size: 13px; letter-spacing: .06em; text-transform: uppercase; color: #64748b; margin: 18px 0 10px; }
  .fx-grade { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px; }
  .fx-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; }
  .fx-ico { font-size: 20px; display: block; margin-bottom: 6px; }
  .fx-titulo { font-size: 15px; font-weight: 700; margin: 0 0 4px; color: #0f172a; }
  .fx-novo { display: inline-block; font-size: 11px; font-weight: 800; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0 8px; vertical-align: middle; }
  .fx-resumo { font-size: 13px; line-height: 1.5; color: #475569; margin: 0; }
  .fx-link { display: inline-block; margin-top: 8px; font-size: 13px; font-weight: 700; color: #0f766e; }
  .fx-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
  .fx-lista { list-style: none; padding: 0; margin: 0; }
  .fx-lista li { margin: 0 0 8px; font-size: 14px; color: #334155; }
</style>
<?php } ?>
<div class="fx-wrap">
<?php if ($fx_titulo !== '') { ?><h2 class="fx-h2"><?= $fx_e($fx_titulo) ?></h2><?php } ?>
<?php if ($fx_formato === 'lista') { ?>
  <ul class="fx-lista">
  <?php foreach ($fx_itens as $fx_i) { ?>
    <li><span aria-hidden="true"><?= $fx_e($fx_i['icone']) ?></span> <strong><?= $fx_e($fx_i['titulo']) ?></strong> — <?= $fx_e($fx_i['resumo']) ?></li>
  <?php } ?>
  </ul>
<?php } elseif ($fx_agrupar) { ?>
  <?php foreach (Funcionalidades_conteudo::grupos() as $fx_gid => $fx_glabel) {
      $fx_doGrupo = array();
      foreach ($fx_itens as $fx_i) { if ($fx_i['grupo'] === $fx_gid) { $fx_doGrupo[] = $fx_i; } }
      if (empty($fx_doGrupo)) { continue; } ?>
    <h3 class="fx-grupo"><?= $fx_e($fx_glabel) ?></h3>
    <div class="fx-grade"><?php foreach ($fx_doGrupo as $fx_i) { echo $fx_card($fx_i); } ?></div>
  <?php } ?>
<?php } else { ?>
  <div class="fx-grade"><?php foreach ($fx_itens as $fx_i) { echo $fx_card($fx_i); } ?></div>
<?php } ?>
</div>
```
- [ ] **Step 4: Run** → partial e catálogo `OK`; `php -l`.
- [ ] **Step 5: Commit** — `feat(site): partial de funcionalidades (grade e lista)`

---

### Task 3: Páginas de cadastro

**Files:** Modify `application/views/public/experimentar.php`, `application/views/public/assinar.php`; Create `tests/funcionalidades_source_test.php`

- [ ] **Step 1: Failing test** — `tests/funcionalidades_source_test.php`:
```php
<?php
function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function ler($rel) { $p = __DIR__ . '/../' . $rel; if (!is_file($p)) { falha('ausente: ' . $rel); } return file_get_contents($p); }
function tem($agulha, $palheiro, $rotulo) { if (strpos($palheiro, $agulha) === false) { falha($rotulo . ' — ausente: ' . $agulha); } }
function naoTem($agulha, $palheiro, $rotulo) { if (strpos($palheiro, $agulha) !== false) { falha($rotulo . ' — nao deveria ter: ' . $agulha); } }

$exp = ler('application/views/public/experimentar.php');
tem("load->view('public/partials/funcionalidades'", $exp, 'experimentar usa partial');
tem('id="funcionalidades"', $exp, 'ancora funcionalidades');
naoTem('<strong>Seguro e 100% online</strong>', $exp, 'cards antigos removidos');

$ass = ler('application/views/public/assinar.php');
tem("load->view('public/partials/funcionalidades'", $ass, 'assinar usa partial');
naoTem('Tenant criado', $ass, 'sem texto tecnico');
naoTem('owner principal', $ass, 'sem texto tecnico');
tem('Tudo o que está incluído', $ass, 'secao completa no assinar');

echo "OK\n";
```
- [ ] **Step 2: Run** → falha.
- [ ] **Step 3a: `/experimentar`** — substituir o bloco inteiro `<div class="benefits-list"> … </div>` (do `<div class="benefits-list">` até o `</div>` que o fecha, logo antes de `<p class="benefits-note">`) por:
```php
                    <div class="benefits-list" id="funcionalidades">
                        <?php $this->load->view('public/partials/funcionalidades', array('func_formato' => 'grade', 'func_ids' => null, 'func_agrupar' => true, 'func_titulo' => '')); ?>
                    </div>
```
- [ ] **Step 3b: `/assinar`** — (1) substituir o `<ul class="plan-list"> … </ul>` (3 `<li>`) por:
```php
                        <?php $this->load->view('public/partials/funcionalidades', array('func_formato' => 'lista', 'func_ids' => array('agenda', 'prontuario', 'whatsapp_confirmacao', 'chatbot', 'relatorios'), 'func_agrupar' => false, 'func_titulo' => '')); ?>
```
(2) logo após o `<? } ?>` que fecha o bloco `<? if(count($planos) >= 3){ ?> … compare-card … ` (a sequência `            <? } ?>` seguida de `        </div>` / `    </div>` / `    <script>`), inserir:
```php
            <div class="compare-card">
                <?php $this->load->view('public/partials/funcionalidades', array('func_formato' => 'grade', 'func_ids' => null, 'func_agrupar' => true, 'func_titulo' => 'Tudo o que está incluído')); ?>
            </div>
```
- [ ] **Step 4: Run** → source + partial + catálogo `OK`; `php -l` nas duas views.
- [ ] **Step 5: Commit** — `feat(site): funcionalidades completas no cadastro (experimentar e assinar)`

---

### Task 4: Home

**Files:** Modify `application/views/index-front.php`, `tests/funcionalidades_source_test.php`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$home = ler('application/views/index-front.php');
tem("Funcionalidades_conteudo::por_ids(array('prontuario', 'agenda', 'chatbot', 'ficha_paciente', 'tempo_espera', 'rotulos', 'relatorios', 'equipe'))", $home, 'home usa catalogo');
tem('feature-card--whatsapp', $home, 'card do whatsapp mantido');
tem('experimentar#funcionalidades', $home, 'link ver todas');
naoTem('Exporte para PDF e tenha visão gerencial', $home, 'card antigo de relatorios removido');
```
- [ ] **Step 2: Run** → falha.
- [ ] **Step 3: Implement** — dentro de `<div class="features-grid">`: manter **apenas** o card `feature-card feature-card--whatsapp` (o `<div>` inteiro com o link e a imagem) como primeiro filho; remover os outros 6 cards; depois do card do WhatsApp inserir:
```php
            <?php
            if (!class_exists('Funcionalidades_conteudo')) { $this->load->library('funcionalidades_conteudo'); }
            foreach (Funcionalidades_conteudo::por_ids(array('prontuario', 'agenda', 'chatbot', 'ficha_paciente', 'tempo_espera', 'rotulos', 'relatorios', 'equipe')) as $fx_h) { ?>
            <div class="feature-card">
                <div class="feature-icon" style="background:#f8fafc;" aria-hidden="true"><?=htmlspecialchars($fx_h['icone'], ENT_QUOTES, 'UTF-8')?></div>
                <h4><?=htmlspecialchars($fx_h['titulo'], ENT_QUOTES, 'UTF-8')?><?php if ($fx_h['novo']) { ?> <span class="feature-novo">Novo</span><?php } ?></h4>
                <p><?=htmlspecialchars($fx_h['descricao'], ENT_QUOTES, 'UTF-8')?></p>
            </div>
            <?php } ?>
```
Depois do fechamento de `</div>` da `features-grid` (ainda dentro do `container`):
```php
        <p class="features-more"><a href="<?=base_url()?>experimentar#funcionalidades">Ver todas as funcionalidades →</a></p>
```
CSS (no `<style>` da home, logo após a regra `.feature-card p { ... }`):
```css
        .feature-novo { display:inline-block; font-size:11px; font-weight:800; color:#047857; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:999px; padding:0 8px; vertical-align:middle; }
        .features-more { text-align:center; margin:28px 0 0; font-weight:700; }
        .features-more a { color:#0f766e; }
```
- [ ] **Step 4: Run** → source test `OK`; `php -l application/views/index-front.php`.
- [ ] **Step 5: Commit** — `feat(site): home com destaques do catalogo de funcionalidades`

---

### Task 5: Landings (bloco + FAQ)

**Files:** Modify `application/views/public/seo/sistema-prontuario-eletronico.php`, `sistema-para-clinicas.php`, `software-para-clinicas.php`, `sistema-para-consultorio-medico.php`, `casos-de-uso.php`; `tests/funcionalidades_source_test.php`

**Bloco** (em cada landing, imediatamente antes da `<section …>` que contém `<div class="section-label">Perguntas frequentes</div>`): uma nova `<section>` copiando a abertura `<section …>` e o primeiro `<div>` interno (container) dessa seção de FAQ **sem** o `style="background…"`, contendo:
```php
        <div class="section-label">Funcionalidades relacionadas</div>
        <?php $this->load->view('public/partials/funcionalidades', array('func_formato' => 'grade', 'func_ids' => array(/* ids da landing */), 'func_agrupar' => false, 'func_titulo' => '')); ?>
```
ids por landing:
- `sistema-prontuario-eletronico`: `'prontuario_especialidade', 'ficha_paciente', 'exportar_prontuario', 'rotulos', 'exames'`
- `sistema-para-clinicas` e `software-para-clinicas`: `'tempo_espera', 'rotulos', 'relatorios', 'whatsapp_confirmacao', 'equipe'`
- `sistema-para-consultorio-medico`: `'tempo_espera', 'ficha_paciente', 'whatsapp_confirmacao', 'chatbot', 'horarios'`
- `casos-de-uso`: `'whatsapp_confirmacao', 'chatbot', 'tempo_espera', 'rotulos', 'exportar_prontuario'`

**FAQ nova** — em cada landing listada abaixo: (1) acrescentar um item ao fim da `.faq-list` reproduzindo **exatamente** a marcação do último `faq-item` existente naquela página (mesmas classes/`onclick`/chevron); (2) acrescentar o objeto correspondente ao fim do array `mainEntity` do `<script type="application/ld+json">` com `"@type": "FAQPage"` (vírgula após o objeto anterior; JSON válido).
- `sistema-prontuario-eletronico`:
  - P: `Consigo exportar o prontuário do paciente?` — R: `Sim. No prontuário de cada paciente há o botão Exportar, que gera o histórico em PDF, Excel ou CSV, completo ou de um período. Cada exportação fica registrada (quem exportou, quando e de qual paciente), como pede a LGPD. A exportação é feita pela clínica e pelos profissionais.`
  - P: `O sistema avisa quando o paciente tem alergia?` — R: `Sim. As alergias registradas na ficha do paciente aparecem em destaque no topo do prontuário. A clínica também pode marcar o paciente com rótulos de alerta, como Alérgico ou Gestante, que aparecem no prontuário e na agenda do dia.`
- `sistema-para-clinicas` e `software-para-clinicas`:
  - P: `Dá para medir o tempo de espera dos pacientes?` — R: `Sim. A recepção marca a chegada do paciente com um clique na agenda e o sistema registra o início e o fim do atendimento. Em Relatórios clínicos aparecem a espera média, o atraso em relação ao horário marcado e a duração média das consultas, por profissional e por período.`
- `sistema-para-consultorio-medico`:
  - P: `O paciente consegue remarcar sozinho?` — R: `Sim, pelo WhatsApp. Com 24 horas ou mais de antecedência, o paciente escolhe um novo dia e horário livres do mesmo profissional, ou cancela, direto no chatbot. Para isso o telefone precisa estar cadastrado e o profissional precisa ter os horários de atendimento configurados.`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$landings = array(
    'sistema-prontuario-eletronico' => array('Consigo exportar o prontuário do paciente?', 'O sistema avisa quando o paciente tem alergia?'),
    'sistema-para-clinicas' => array('Dá para medir o tempo de espera dos pacientes?'),
    'software-para-clinicas' => array('Dá para medir o tempo de espera dos pacientes?'),
    'sistema-para-consultorio-medico' => array('O paciente consegue remarcar sozinho?'),
    'casos-de-uso' => array(),
);
foreach ($landings as $slug => $perguntas) {
    $html = ler('application/views/public/seo/' . $slug . '.php');
    tem("load->view('public/partials/funcionalidades'", $html, $slug . ' usa partial');
    tem('Funcionalidades relacionadas', $html, $slug . ' bloco');
    foreach ($perguntas as $q) {
        if (substr_count($html, $q) < 2) { falha($slug . ' — pergunta deve estar no HTML e no JSON-LD: ' . $q); }
    }
    if (preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m)) {
        foreach ($m[1] as $json) { if (json_decode($json) === null) { falha($slug . ' — JSON-LD invalido'); } }
    }
}
```
(Atenção: alguns JSON-LD podem conter `<?=...?>` PHP; se `json_decode` falhar em bloco que **não** foi alterado nesta task, troque a validação por: só validar o bloco que contém `"FAQPage"`. Reportar se precisar.)
- [ ] **Step 2: Run** → falha.
- [ ] **Step 3: Implement** — conforme acima, nas 5 landings.
- [ ] **Step 4: Run** → todos `tests/funcionalidades_*` `OK`; `php -l` nas 5 landings.
- [ ] **Step 5: Commit** — `feat(seo): funcionalidades relacionadas e FAQ novas nas landings`

---

### Task 6: sitemap, llms.txt, ledger, docs

**Files:** Modify `sitemap.xml`, `llms.txt`, `docs/seo-geo-agente-ledger.md`, `docs/produto/evolucao-30-dias-google-doc.md`, `CLAUDE.md`, `tests/funcionalidades_source_test.php`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
require_once __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';
$llms = ler('llms.txt');
tem('## Funcionalidades', $llms, 'llms secao');
foreach (Funcionalidades_conteudo::itens() as $i) { tem($i['titulo'], $llms, 'llms item ' . $i['id']); }
tem('Funcionalidades_conteudo', ler('CLAUDE.md'), 'CLAUDE.md cita o catalogo');
```
(A `require_once` precisa de `BASEPATH`: acrescente `if (!defined('BASEPATH')) { define('BASEPATH', __DIR__); }` no topo do arquivo de teste.)
- [ ] **Step 2: Run** → falha.
- [ ] **Step 3a: `llms.txt`** — logo antes de `## Por especialidade`, inserir a seção:
```
## Funcionalidades

- **Agenda inteligente**: <descricao do item>
… (uma linha por item, na ordem do catálogo, com a `descricao` exata do catálogo)
```
- [ ] **Step 3b: `sitemap.xml`** — trocar `<lastmod>` para a data de hoje (`YYYY-MM-DD`) nas `<url>` de: `https://utecnologia.com.br/`, `/experimentar`, `/assinar`, `/sistema-para-clinicas`, `/sistema-prontuario-eletronico`, `/sistema-para-consultorio-medico`, `/software-para-clinicas`, `/casos-de-uso`. Não mexer nas demais.
- [ ] **Step 3c: ledger** — ao fim de `docs/seo-geo-agente-ledger.md`, parágrafo: `> **Feito em <data> (vitrine de funcionalidades):** catálogo único com 16 funcionalidades (fonte: Funcionalidades_conteudo) exibido em /experimentar, /assinar, home e nas landings de prontuário eletrônico, sistema e software para clínicas, consultório médico e casos de uso; 4 FAQ novas (exportar prontuário, alergias, tempo de espera, remarcação pelo WhatsApp); llms.txt com seção Funcionalidades. Próximo: Entrega 2 (pesquisa de palavras-chave → 2 landings novas + artigos).`
- [ ] **Step 3d: `docs/produto/evolucao-30-dias-google-doc.md`** — antes de `## Histórico de atualizações`, nova seção:
```
## Divulgação (site e buscadores)

### Vitrine de funcionalidades no site — 🟢 Pronto, aguardando publicação
- As páginas de cadastro (teste grátis e assinatura), a página inicial e as principais páginas de busca passaram a mostrar tudo o que o sistema já faz, incluindo WhatsApp (confirmação, lembrete e chatbot) e as novidades de outubro.
- Perguntas frequentes novas sobre exportar prontuário, alergias, tempo de espera e remarcação pelo WhatsApp.
- Próximo passo: pesquisa de palavras-chave para criar 2 páginas novas e artigos de blog.

---
```
e uma linha nova no Histórico: `- <data> — Vitrine de funcionalidades do site pronta (cadastro, página inicial e páginas de busca), aguardando publicação.`; atualizar `Atualizado em:`.
- [ ] **Step 3e: CLAUDE.md** — §9 Frontend, acrescentar o bullet: `- **Catálogo de funcionalidades do site:** \`application/libraries/Funcionalidades_conteudo.php\` (fonte única, 16 itens) + partial \`application/views/public/partials/funcionalidades.php\` (grade/lista) usado em /experimentar, /assinar, home e landings. Funcionalidade nova visível ao cliente → acrescentar aqui também (além do manual, §19).`
- [ ] **Step 4: Run** → todos `tests/funcionalidades_*` `OK`; xmllint não disponível: validar o sitemap com `C:/PHP/PHP7.2/php.exe -r "var_dump(simplexml_load_file('sitemap.xml') !== false);"` → `bool(true)`.
- [ ] **Step 5: Commit** — `docs(seo): llms.txt, sitemap, ledger e andamento da vitrine`

---

## Deploy (após revisão e ok do usuário)
1. Baixar do servidor e comparar com a branch: `experimentar.php`, `assinar.php`, `index-front.php`, as 5 landings, `sitemap.xml`, `llms.txt`.
2. Ordem: `Funcionalidades_conteudo.php` → `views/public/partials/funcionalidades.php` (pasta nova `partials/` — conferir que não existe arquivo com esse nome) → `experimentar.php` → `assinar.php` → 5 landings → `index-front.php` → `sitemap.xml` → `llms.txt`.
3. Baixar e `cmp`; healthcheck: home, `/experimentar`, `/assinar` e as 5 landings respondem 200; validar o JSON-LD das landings no Rich Results Test do Google.
