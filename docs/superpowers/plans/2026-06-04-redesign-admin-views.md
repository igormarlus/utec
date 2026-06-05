# Redesign Visual e Usabilidade — Views Admin

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesenhar visualmente as views de atendimento clínico (desktop + mobile) com paleta Esmeralda Médico, tipografia Outfit, layout C+B para agenda mobile, menu mobile funcional e bottom sheet de ações.

**Architecture:** Um arquivo CSS compartilhado `css/utec-redesign.css` carregado por todas as views afetadas fornece variáveis, componentes e responsividade. O CSS do Adminto não é editado — o novo CSS sobrepõe via especificidade. Para mobile, a agenda usa um bloco HTML separado (oculto no desktop) gerado pelo mesmo loop PHP com `data-*` attributes alimentando um bottom sheet em vanilla JS.

**Tech Stack:** PHP 7 / CodeIgniter 3, Bootstrap 4, jQuery, CSS custom properties, vanilla JS (bottom sheet), Google Fonts (Outfit)

---

## Mapa de Arquivos

| Ação | Arquivo | Responsabilidade |
|------|---------|-----------------|
| **Criar** | `css/utec-redesign.css` | Design system: variáveis, componentes, responsividade |
| **Modificar** | `includes/adm/menu.php` | Remover condição PHP mobile + estilo esmeralda no sidebar |
| **Modificar** | `includes/adm/top.php` | Top bar com fundo esmeralda + logo em quadrado branco |
| **Modificar** | `application/views/adm/usuarios/new/atendimentos.php` | Agenda: melhorias desktop + layout C+B mobile + bottom sheet |
| **Modificar** | `application/views/adm/usuarios/new/prontuario.php` | Prontuário: melhorias desktop + sticky save + grid mobile |
| **Modificar** | `application/views/adm/atendimento/atendimento.php` | Formulário novo agendamento: estilo esmeralda + mobile |
| **Modificar** | `application/views/adm/usuarios/new/lista.php` | Lista: avatares + botões com paleta esmeralda |

---

## Task 1: Criar `css/utec-redesign.css`

**Files:**
- Create: `css/utec-redesign.css`

- [ ] **Step 1: Criar o arquivo com variáveis, tipografia e todos os componentes**

```css
/* css/utec-redesign.css — UTecnologia Saúde Design System v1 */

@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&display=swap');

/* ── Variáveis ─────────────────────────────────────────────── */
:root {
  --ut-green-900: #052e16;
  --ut-green-800: #064e3b;
  --ut-green-600: #059669;
  --ut-green-400: #22c55e;
  --ut-green-50:  #f0fdf4;
  --ut-green-border: #6ee7b7;

  --ut-status-pending-bg:     #fee2e2;
  --ut-status-pending-text:   #b91c1c;
  --ut-status-active-bg:      #dcfce7;
  --ut-status-active-text:    #166534;
  --ut-status-done-bg:        #fef3c7;
  --ut-status-done-text:      #92400e;
  --ut-status-cancelled-bg:   #f1f5f9;
  --ut-status-cancelled-text: #475569;

  --ut-font: 'Outfit', system-ui, sans-serif;

  --ut-shadow-sm: 0 2px 8px rgba(5,46,22,.06);
  --ut-shadow-md: 0 6px 20px rgba(5,46,22,.12);
  --ut-shadow-lg: 0 12px 32px rgba(5,46,22,.18);

  --ut-radius-sm: 10px;
  --ut-radius-md: 14px;
  --ut-radius-lg: 18px;
}

/* ── Sidebar esmeralda ─────────────────────────────────────── */
.menu-w {
  background: linear-gradient(180deg, var(--ut-green-900) 0%, var(--ut-green-800) 100%) !important;
}
.menu-w .logo-w {
  border-bottom: 1px solid rgba(110,231,183,.15) !important;
}
.menu-w .main-menu > li > a {
  color: #a7f3d0 !important;
  font-family: var(--ut-font) !important;
}
.menu-w .main-menu > li > a:hover,
.menu-w .main-menu > li > a:focus {
  color: var(--ut-green-400) !important;
  background: rgba(34,197,94,.1) !important;
}
.menu-w .main-menu > li.selected > a,
.menu-w .main-menu > li.active > a {
  color: #fff !important;
  background: rgba(34,197,94,.2) !important;
}
.menu-w .sub-header > span {
  color: #6ee7b7 !important;
  font-family: var(--ut-font) !important;
  font-size: 10px !important;
  letter-spacing: .08em !important;
}
.menu-w .logged-user-name {
  color: #f0fdf4 !important;
  font-family: var(--ut-font) !important;
  font-weight: 700 !important;
}
.menu-w .logged-user-role {
  color: #6ee7b7 !important;
  font-family: var(--ut-font) !important;
}
.menu-w .logo-label {
  color: #f0fdf4 !important;
  font-family: var(--ut-font) !important;
  font-weight: 800 !important;
}
.menu-w .sub-menu a {
  color: #a7f3d0 !important;
  font-family: var(--ut-font) !important;
}
.menu-w .sub-menu a:hover {
  color: #fff !important;
  background: rgba(34,197,94,.12) !important;
}

/* Mobile menu esmeralda */
.menu-mobile {
  background: linear-gradient(180deg, var(--ut-green-900) 0%, var(--ut-green-800) 100%) !important;
}
.menu-mobile .main-menu > li > a {
  color: #a7f3d0 !important;
  font-family: var(--ut-font) !important;
}
.menu-mobile .sub-menu a {
  color: #a7f3d0 !important;
  font-family: var(--ut-font) !important;
  background: rgba(255,255,255,.06) !important;
}

/* ── Top bar esmeralda ─────────────────────────────────────── */
.top-bar {
  background: linear-gradient(135deg, var(--ut-green-900) 0%, var(--ut-green-800) 100%) !important;
  border-bottom: 1px solid rgba(110,231,183,.12) !important;
}
.top-bar .top-menu-controls .element-search input {
  background: rgba(255,255,255,.08) !important;
  border-color: rgba(110,231,183,.2) !important;
  color: #f0fdf4 !important;
}
.top-bar .top-menu-controls .element-search input::placeholder {
  color: rgba(167,243,208,.5) !important;
}
.top-bar .btn-white {
  background: rgba(255,255,255,.1) !important;
  border-color: rgba(110,231,183,.25) !important;
  color: #a7f3d0 !important;
}
.top-bar .btn-white:hover {
  background: rgba(34,197,94,.15) !important;
  color: #fff !important;
}
.top-bar .messages-notifications > i {
  color: #a7f3d0 !important;
}
.top-bar .logged-user-name,
.top-bar .logged-user-role {
  color: #f0fdf4 !important;
  font-family: var(--ut-font) !important;
}

/* ── Status pills ──────────────────────────────────────────── */
.ut-status-pill {
  border-radius: 99px;
  display: inline-block;
  font-family: var(--ut-font);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .04em;
  padding: 5px 11px;
  text-transform: uppercase;
  white-space: nowrap;
}
.ut-status-pill.pendente   { background: var(--ut-status-pending-bg);   color: var(--ut-status-pending-text); }
.ut-status-pill.atendimento{ background: var(--ut-status-active-bg);    color: var(--ut-status-active-text); }
.ut-status-pill.finalizado { background: var(--ut-status-done-bg);      color: var(--ut-status-done-text); }
.ut-status-pill.cancelado  { background: var(--ut-status-cancelled-bg); color: var(--ut-status-cancelled-text); }

/* ── Stat chips (agenda header) ────────────────────────────── */
.ut-stat-chip {
  border-radius: var(--ut-radius-md);
  padding: 12px 10px;
  text-align: center;
}
.ut-stat-chip .stat-value {
  display: block;
  font-family: var(--ut-font);
  font-size: 24px;
  font-weight: 800;
  line-height: 1;
}
.ut-stat-chip .stat-label {
  display: block;
  font-size: 10px;
  letter-spacing: .07em;
  margin-top: 4px;
  text-transform: uppercase;
}
.ut-stat-chip.total      { background: rgba(255,255,255,.08); }
.ut-stat-chip.total .stat-value { color: #fff; }
.ut-stat-chip.total .stat-label { color: #6ee7b7; }
.ut-stat-chip.pendentes  { background: rgba(239,68,68,.18); border: 1px solid rgba(239,68,68,.25); }
.ut-stat-chip.pendentes .stat-value { color: #fca5a5; }
.ut-stat-chip.pendentes .stat-label { color: #f87171; }
.ut-stat-chip.em-curso   { background: rgba(34,197,94,.2); border: 1px solid rgba(34,197,94,.3); }
.ut-stat-chip.em-curso .stat-value { color: #86efac; }
.ut-stat-chip.em-curso .stat-label { color: #4ade80; }
.ut-stat-chip.feitos     { background: rgba(245,158,11,.15); border: 1px solid rgba(245,158,11,.2); }
.ut-stat-chip.feitos .stat-value { color: #fcd34d; }
.ut-stat-chip.feitos .stat-label { color: #fbbf24; }

/* ── Mobile: agenda header ─────────────────────────────────── */
.ut-agenda-header {
  background: linear-gradient(160deg, var(--ut-green-900) 0%, var(--ut-green-800) 100%);
  display: none;
  padding: 14px 16px 26px;
}
.ut-agenda-header-top {
  align-items: center;
  display: flex;
  justify-content: space-between;
  margin-bottom: 14px;
}
.ut-agenda-brand {
  align-items: center;
  display: flex;
  gap: 10px;
}
.ut-agenda-brand-badge {
  align-items: center;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,.3);
  display: flex;
  height: 38px;
  justify-content: center;
  width: 38px;
}
.ut-agenda-brand-badge img {
  height: 28px;
  object-fit: contain;
  width: 28px;
}
.ut-agenda-brand-text small {
  color: #6ee7b7;
  display: block;
  font-family: var(--ut-font);
  font-size: 9px;
  letter-spacing: .07em;
  text-transform: uppercase;
}
.ut-agenda-brand-text strong {
  color: #ecfdf5;
  display: block;
  font-family: var(--ut-font);
  font-size: 16px;
  font-weight: 800;
  line-height: 1.1;
}
.ut-agenda-stats {
  display: flex;
  gap: 8px;
}
.ut-agenda-stats .ut-stat-chip { flex: 1; }

/* ── Mobile: active card ───────────────────────────────────── */
.ut-active-card {
  background: #fff;
  border: 1.5px solid var(--ut-green-border);
  border-radius: var(--ut-radius-lg);
  box-shadow: var(--ut-shadow-md);
  margin-bottom: 16px;
  padding: 16px;
}
.ut-active-card-header {
  align-items: center;
  display: flex;
  gap: 10px;
  margin-bottom: 12px;
}
.ut-active-card-avatar {
  align-items: center;
  background: var(--ut-green-900);
  border-radius: var(--ut-radius-sm);
  color: #fff;
  display: flex;
  flex-shrink: 0;
  font-family: var(--ut-font);
  font-size: 12px;
  font-weight: 800;
  height: 42px;
  justify-content: center;
  width: 42px;
}
.ut-active-card-name {
  color: #0f172a;
  font-family: var(--ut-font);
  font-size: 15px;
  font-weight: 800;
  margin: 0;
}
.ut-active-card-meta {
  color: var(--ut-green-600);
  font-size: 11px;
  font-weight: 600;
  margin: 0;
}
.ut-active-card-actions {
  display: grid;
  gap: 8px;
  grid-template-columns: 2fr 1fr;
}

/* ── Mobile: queue list ────────────────────────────────────── */
.ut-queue-list {
  background: #fff;
  border-radius: var(--ut-radius-lg);
  box-shadow: var(--ut-shadow-sm);
  margin-bottom: 16px;
  overflow: hidden;
}
.ut-queue-item {
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  display: flex;
  gap: 11px;
  padding: 13px 16px;
  transition: background .14s;
}
.ut-queue-item:last-child { border-bottom: none; }
.ut-queue-item:hover,
.ut-queue-item:active { background: var(--ut-green-50); }
.ut-queue-avatar {
  align-items: center;
  border-radius: var(--ut-radius-sm);
  color: #fff;
  display: flex;
  flex-shrink: 0;
  font-family: var(--ut-font);
  font-size: 11px;
  font-weight: 800;
  height: 34px;
  justify-content: center;
  width: 34px;
}
.ut-queue-name {
  color: #0f172a;
  font-family: var(--ut-font);
  font-size: 13px;
  font-weight: 700;
  margin: 0;
}
.ut-queue-meta {
  color: #94a3b8;
  font-size: 11px;
  margin: 0;
}
.ut-queue-chevron {
  color: #d1d5db;
  flex-shrink: 0;
  font-size: 18px;
  font-weight: 300;
  line-height: 1;
}

/* ── Mobile: section label ─────────────────────────────────── */
.ut-section-label {
  align-items: center;
  color: #64748b;
  display: flex;
  font-family: var(--ut-font);
  font-size: 10px;
  font-weight: 700;
  gap: 6px;
  letter-spacing: .08em;
  margin-bottom: 10px;
  text-transform: uppercase;
}
.ut-section-label.active { color: var(--ut-green-400); }
.ut-section-label-dot {
  background: var(--ut-green-400);
  border-radius: 50%;
  box-shadow: 0 0 0 3px rgba(34,197,94,.2);
  display: inline-block;
  flex-shrink: 0;
  height: 7px;
  width: 7px;
}

/* ── Bottom sheet ──────────────────────────────────────────── */
.ut-bottom-sheet {
  background: #fff;
  border-radius: 22px 22px 0 0;
  bottom: 0;
  box-shadow: 0 -8px 40px rgba(0,0,0,.18);
  left: 0;
  padding: 14px 16px 28px;
  position: fixed;
  right: 0;
  transform: translateY(100%);
  transition: transform .28s cubic-bezier(.4,0,.2,1);
  z-index: 1060;
}
.ut-bottom-sheet.is-open { transform: translateY(0); }
.ut-bottom-sheet-backdrop {
  background: rgba(5,46,22,.42);
  bottom: 0;
  left: 0;
  opacity: 0;
  pointer-events: none;
  position: fixed;
  right: 0;
  top: 0;
  transition: opacity .28s;
  z-index: 1059;
}
.ut-bottom-sheet-backdrop.is-visible {
  opacity: 1;
  pointer-events: auto;
}
.ut-bottom-sheet-handle {
  background: #e2e8f0;
  border-radius: 99px;
  height: 4px;
  margin: 0 auto 14px;
  width: 36px;
}
.ut-bottom-sheet-patient {
  align-items: center;
  display: flex;
  gap: 10px;
  margin-bottom: 14px;
}
.ut-bottom-sheet-avatar {
  align-items: center;
  border-radius: var(--ut-radius-sm);
  color: #fff;
  display: flex;
  flex-shrink: 0;
  font-family: var(--ut-font);
  font-size: 12px;
  font-weight: 800;
  height: 38px;
  justify-content: center;
  width: 38px;
}
.ut-bottom-sheet-name {
  color: #0f172a;
  display: block;
  font-family: var(--ut-font);
  font-size: 14px;
  font-weight: 700;
}
.ut-bottom-sheet-meta {
  color: #94a3b8;
  display: block;
  font-size: 11px;
}
.ut-bottom-sheet-actions {
  display: grid;
  gap: 9px;
  grid-template-columns: 1fr 1fr;
}
.ut-sheet-btn {
  border-radius: var(--ut-radius-sm);
  cursor: pointer;
  font-family: var(--ut-font);
  font-size: 13px;
  font-weight: 700;
  padding: 12px;
  text-align: center;
  text-decoration: none;
  transition: opacity .15s;
  display: block;
  border: 0;
  width: 100%;
}
.ut-sheet-btn:hover { opacity: .86; text-decoration: none; }
.ut-sheet-btn-prontuario { background: var(--ut-green-900); color: #fff; }
.ut-sheet-btn-iniciar    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe !important; }
.ut-sheet-btn-finalizar  { background: var(--ut-status-active-bg); color: var(--ut-status-active-text); border: 1px solid #86efac !important; }
.ut-sheet-btn-reabrir    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a !important; }
.ut-sheet-btn-remarcar   { background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0 !important; }
.ut-sheet-btn-cancelar   { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fecaca !important; }

/* ── Prontuário melhorias ──────────────────────────────────── */
.ut-sticky-save {
  background: #fff;
  border-top: 1px solid #e2e8f0;
  bottom: 0;
  box-shadow: 0 -4px 16px rgba(0,0,0,.08);
  padding: 12px 16px;
  position: sticky;
  z-index: 100;
}
.ut-action-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

/* ── Utilitários ───────────────────────────────────────────── */
.ut-font { font-family: var(--ut-font) !important; }
.ut-mobile-only { display: none !important; }
.ut-desktop-only { display: block !important; }

/* ── Responsividade ────────────────────────────────────────── */
@media (max-width: 767.98px) {
  .ut-agenda-header { display: block; }
  .ut-mobile-only  { display: block !important; }
  .ut-desktop-only { display: none !important; }

  /* Inputs: alvos de toque maiores */
  .form-control,
  select.form-control {
    min-height: 48px !important;
    font-size: 16px !important; /* evita zoom automático no iOS */
  }

  /* Prontuário: action grid 2×2 */
  .ut-action-grid {
    display: grid;
    gap: 10px;
    grid-template-columns: 1fr 1fr;
  }
  .ut-action-grid .btn,
  .ut-action-grid button {
    flex: unset;
    width: 100% !important;
  }

  /* Timeline: menos padding lateral */
  .timeline-list:before { left: 13px; }
  .timeline-item { padding-left: 38px; }
  .timeline-dot { height: 16px; left: 6px; width: 16px; }
}

@media (min-width: 768px) {
  .ut-mobile-only { display: none !important; }
}
```

- [ ] **Step 2: Verificar que o arquivo foi criado corretamente**

```bash
Get-Item "c:/htdocs/utec/css/utec-redesign.css" | Select-Object Length
```
Esperado: arquivo com tamanho > 5000 bytes.

- [ ] **Step 3: Commit**

```bash
git add css/utec-redesign.css
git commit -m "feat: design system CSS Esmeralda Médico (Outfit, variáveis, componentes)"
```

---

## Task 2: Corrigir `menu.php` — menu mobile + estilo esmeralda

**Files:**
- Modify: `includes/adm/menu.php:1` (remover condição PHP)
- Modify: `includes/adm/menu.php` (importar utec-redesign.css)

O menu atual está 100% oculto em dispositivos móveis por uma condição PHP `if(!$this->agent->is_mobile())`. Esta task remove essa condição e adiciona o import do CSS de design.

- [ ] **Step 1: Remover a condição PHP mobile**

Em `includes/adm/menu.php`, **remover** a linha 1:
```php
<? if(!$this->agent->is_mobile()){ ?>
```
E **remover** a última linha do arquivo:
```php
<? } ?>
```
O restante do arquivo não muda.

- [ ] **Step 2: Adicionar import do utec-redesign.css no bloco `<style>` existente**

Localizar o bloco `<style>` existente no arquivo (após o bloco de lógica PHP, antes do `<div class="menu-mobile">`). Adicionar **antes** do bloco `<style>` existente:

```php
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 3: Verificar no browser — menu mobile**

Abrir `http://localhost/utec/adm/atendimento` com DevTools em modo mobile (iPhone 12 Pro, 390px).

Cheklist:
- [ ] O botão hamburger ☰ aparece no topo
- [ ] Clicar no hamburger abre o menu lateral
- [ ] O menu tem fundo verde escuro `#052e16`
- [ ] Links do menu têm cor `#a7f3d0`
- [ ] Fechar o menu funciona
- [ ] No desktop (≥ 992px): sidebar aparece normalmente com estilo esmeralda

- [ ] **Step 4: Commit**

```bash
git add includes/adm/menu.php
git commit -m "fix: habilitar menu mobile (remover condição PHP) + estilo esmeralda"
```

---

## Task 3: Atualizar `top.php` — top bar esmeralda com logo

**Files:**
- Modify: `includes/adm/top.php`

- [ ] **Step 1: Adicionar import do utec-redesign.css**

Adicionar **antes** do `<style>` existente em `top.php`:
```php
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 2: Substituir o `.utec-top-brand` pela versão com logo em quadrado branco**

Localizar o bloco que renderiza a logo no top bar (`utec-top-brand`). Substituir o conteúdo do `<a class="utec-top-brand">` por:

```php
<a href="<?=base_url()?>adm/usuarios/dash" class="utec-top-brand" aria-label="UTecnologia Saude">
  <? if($top_logo_available){ ?>
    <span class="ut-agenda-brand-badge">
      <img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saude">
    </span>
    <span class="utec-top-brand-copy">
      <small>UTecnologia</small>
      <strong>Saude</strong>
    </span>
  <? } else { ?>
    <span class="ut-agenda-brand-badge" style="background:var(--ut-green-900);">
      <span style="color:#fff;font-family:var(--ut-font);font-size:12px;font-weight:900;">UT</span>
    </span>
    <span class="utec-top-brand-copy">
      <small>UTecnologia</small>
      <strong>Saude</strong>
    </span>
  <? } ?>
</a>
```

- [ ] **Step 3: Atualizar o `<style>` do top.php para usar variáveis esmeralda**

No bloco `<style>` existente, adicionar/substituir as regras do `.utec-top-brand`:

```css
.utec-top-brand {
  align-items: center;
  color: #f0fdf4;
  display: inline-flex;
  gap: 10px;
  margin-left: 10px;
  padding: 6px 12px 6px 8px;
  text-decoration: none;
  border-radius: 12px;
  transition: background .15s;
}
.utec-top-brand:hover,
.utec-top-brand:focus { color: #fff; text-decoration: none; background: rgba(34,197,94,.12); }
.utec-top-brand-copy small {
  color: #6ee7b7; font-size: 10px; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; display: block; margin-bottom: 1px;
}
.utec-top-brand-copy strong {
  color: #f0fdf4; font-family: var(--ut-font); font-size: 15px; font-weight: 800; display: block;
}
```

- [ ] **Step 4: Verificar no browser**

- [ ] Top bar tem fundo verde escuro em desktop e mobile
- [ ] Logo aparece em quadrado branco com borda arredondada
- [ ] Links de atalho têm estilo legível sobre fundo escuro
- [ ] Dropdown do usuário logado abre e fecha normalmente

- [ ] **Step 5: Commit**

```bash
git add includes/adm/top.php
git commit -m "feat: top bar esmeralda com logo em quadrado branco"
```

---

## Task 4: Redesenhar `atendimentos.php` — melhorias desktop

**Files:**
- Modify: `application/views/adm/usuarios/new/atendimentos.php`

Esta task foca nas melhorias do desktop (stats chips, tabela com hover, pills semânticos). A Task 5 adiciona o layout mobile.

- [ ] **Step 1: Adicionar import do utec-redesign.css no `<head>`**

Logo após os outros `<link>` CSS existentes, adicionar:
```html
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 2: Converter o bloco de 4 stat cards em chips esmeralda**

Localizar o bloco `<div class="row">` que contém os 4 `agenda-stat-card`. Substituir por:

```html
<div style="display:flex;gap:10px;margin-bottom:24px;padding:14px 20px;background:linear-gradient(135deg,var(--ut-green-900) 0%,var(--ut-green-800) 100%);border-radius:18px;">
  <div class="ut-stat-chip total" style="flex:1;">
    <span class="stat-value"><?=$metricas_agenda['total']?></span>
    <span class="stat-label">Total</span>
  </div>
  <div class="ut-stat-chip pendentes" style="flex:1;">
    <span class="stat-value"><?=$metricas_agenda['pendentes']?></span>
    <span class="stat-label">Pendentes</span>
  </div>
  <div class="ut-stat-chip em-curso" style="flex:1;">
    <span class="stat-value"><?=$metricas_agenda['em_atendimento']?></span>
    <span class="stat-label">Em curso</span>
  </div>
  <div class="ut-stat-chip feitos" style="flex:1;">
    <span class="stat-value"><?=$metricas_agenda['finalizados']?></span>
    <span class="stat-label">Feitos</span>
  </div>
</div>
```

- [ ] **Step 3: Substituir os pills de status da tabela por `.ut-status-pill`**

No loop da tabela, localizar:
```php
<span class="status-pill <?=$status_class?>"><?=$status_nome?></span>
```
Substituir por (mapear as classes antigas para as novas):
```php
<?php
  $ut_pill_class = 'pendente';
  if((int)$agenda->status === 1) $ut_pill_class = 'atendimento';
  if((int)$agenda->status === 2) $ut_pill_class = 'finalizado';
  if((int)$agenda->status === 3) $ut_pill_class = 'cancelado';
?>
<span class="ut-status-pill <?=$ut_pill_class?>"><?=$status_nome?></span>
```

- [ ] **Step 4: Adicionar hover state e fonte Outfit nas linhas da tabela**

No `<style>` existente do arquivo, adicionar ao final:

```css
.agenda-table tbody tr:hover {
  background: var(--ut-green-50) !important;
  transition: background .14s;
}
.agenda-table .patient-name,
.agenda-table .patient-subtitle {
  font-family: var(--ut-font);
}
.agenda-table .action-group .btn {
  font-family: var(--ut-font);
}
```

- [ ] **Step 5: Verificar no browser (desktop, ≥ 768px)**

- [ ] Stats aparecem como chips no banner verde esmeralda
- [ ] Hovering em linhas da tabela: fundo verde claro
- [ ] Pills de status com novas cores semânticas
- [ ] Funcionalidade de filtro e remarcar ainda funciona

- [ ] **Step 6: Commit**

```bash
git add application/views/adm/usuarios/new/atendimentos.php
git commit -m "feat: agenda desktop — stat chips esmeralda, pills semânticos, hover na tabela"
```

---

## Task 5: Redesenhar `atendimentos.php` — layout C+B mobile + bottom sheet

**Files:**
- Modify: `application/views/adm/usuarios/new/atendimentos.php`

Continuação da Task 4. Adiciona o layout C+B para mobile (agenda header + active card + fila + bottom sheet) e o JS do drawer.

- [ ] **Step 1: Armazenar resultados da query em array para uso duplo**

Localizar onde o loop da tabela começa:
```php
<? if($qr_agendamentos->num_rows() > 0){ foreach($qr_agendamentos->result() as $agenda){ ?>
```

Adicionar **antes** desse bloco (logo após o bloco de stats, antes da tabela):
```php
<?php $agenda_items = $qr_agendamentos->result(); ?>
```

Substituir o início do loop da tabela por:
```php
<? if(count($agenda_items) > 0){ foreach($agenda_items as $agenda){ ?>
```

E o final do loop:
```php
<? } } else { ?>
```
Permanece igual.

- [ ] **Step 2: Adicionar o header mobile esmeralda (oculto no desktop)**

**Logo antes** do `<div class="content-i">`, adicionar:

```html
<!-- MOBILE HEADER — oculto no desktop via .ut-mobile-only -->
<div class="ut-agenda-header ut-mobile-only">
  <div class="ut-agenda-header-top">
    <div class="ut-agenda-brand">
      <div class="ut-agenda-brand-badge">
        <img src="<?=base_url()?>img/logo-w.png" alt="UT">
      </div>
      <div class="ut-agenda-brand-text">
        <small><?=date('l, d M', strtotime($filtros['data_agenda'] ?: 'today'))?></small>
        <strong>Agenda clínica</strong>
      </div>
    </div>
    <div style="width:36px;height:36px;background:rgba(255,255,255,.1);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#a7f3d0;font-size:15px;">☰</div>
  </div>
  <div class="ut-agenda-stats">
    <div class="ut-stat-chip total"><span class="stat-value"><?=$metricas_agenda['total']?></span><span class="stat-label">Total</span></div>
    <div class="ut-stat-chip pendentes"><span class="stat-value"><?=$metricas_agenda['pendentes']?></span><span class="stat-label">Pendentes</span></div>
    <div class="ut-stat-chip em-curso"><span class="stat-value"><?=$metricas_agenda['em_atendimento']?></span><span class="stat-label">Em curso</span></div>
    <div class="ut-stat-chip feitos"><span class="stat-value"><?=$metricas_agenda['finalizados']?></span><span class="stat-label">Feitos</span></div>
  </div>
</div>
```

- [ ] **Step 3: Adicionar bloco mobile C+B após a tabela**

Após o fechamento do `.agenda-panel` (depois de `</div><!-- /.agenda-panel -->`), adicionar:

```html
<!-- ══ MOBILE AGENDA C+B ══ oculto no desktop ══════════════ -->
<div class="ut-mobile-only" style="padding:16px 14px 100px;">

  <?php
    $tem_ativo = false;
    foreach($agenda_items as $ag_check) {
      if((int)$ag_check->status === 1) { $tem_ativo = true; break; }
    }
  ?>

  <?php if($tem_ativo): ?>
  <div class="ut-section-label active">
    <span class="ut-section-label-dot"></span>
    Em atendimento agora
  </div>
  <?php foreach($agenda_items as $agenda):
    if((int)$agenda->status !== 1) continue;
    $ini_ativo = strtoupper(mb_substr(trim($agenda->paciente_nome),0,1,'UTF-8'));
  ?>
  <div class="ut-active-card">
    <div class="ut-active-card-header">
      <div class="ut-active-card-avatar"><?=$ini_ativo?></div>
      <div style="flex:1">
        <p class="ut-active-card-name"><?=htmlspecialchars($agenda->paciente_nome)?></p>
        <p class="ut-active-card-meta"><?=substr($agenda->hora_agenda,0,5)?> · <?=ucfirst($agenda->tipo)?><?=$agenda->prestador_nome ? ' · '.$agenda->prestador_nome : ''?></p>
      </div>
      <span class="ut-status-pill atendimento">Em atend.</span>
    </div>
    <div class="ut-active-card-actions">
      <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$agenda->id_paciente?>/<?=$agenda->id?>" class="ut-sheet-btn ut-sheet-btn-prontuario">📋 Prontuário</a>
      <a href="<?=base_url()?>adm/atendimento/set_status_agenda/<?=$agenda->id?>/<?=$agenda->status?>" class="ut-sheet-btn ut-sheet-btn-finalizar">✓ Finalizar</a>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php
    $tem_pendentes = false;
    foreach($agenda_items as $ag_check) {
      if((int)$ag_check->status === 0) { $tem_pendentes = true; break; }
    }
  ?>
  <?php if($tem_pendentes): ?>
  <div class="ut-section-label">Fila de espera</div>
  <div class="ut-queue-list">
    <?php foreach($agenda_items as $agenda):
      if((int)$agenda->status !== 0) continue;
      $ini = strtoupper(mb_substr(trim($agenda->paciente_nome),0,1,'UTF-8'));
      $tel = str_replace(['-',' ','+','(',')'], '', $agenda->paciente_telefone);
    ?>
    <div class="ut-queue-item"
         data-id="<?=$agenda->id?>"
         data-nome="<?=htmlspecialchars($agenda->paciente_nome, ENT_QUOTES)?>"
         data-ini="<?=$ini?>"
         data-hora="<?=substr($agenda->hora_agenda,0,5)?>"
         data-tipo="<?=htmlspecialchars($agenda->tipo, ENT_QUOTES)?>"
         data-status="<?=$agenda->status?>"
         data-prestador="<?=htmlspecialchars((string)$agenda->prestador_nome, ENT_QUOTES)?>"
         data-data="<?=$agenda->data_agenda?>"
         data-paciente-id="<?=$agenda->id_paciente?>"
         data-telefone="<?=$tel?>"
         onclick="utOpenSheet(this)">
      <div class="ut-queue-avatar" style="background:var(--ut-green-900);"><?=$ini?></div>
      <div style="flex:1;min-width:0;">
        <p class="ut-queue-name"><?=htmlspecialchars($agenda->paciente_nome)?></p>
        <p class="ut-queue-meta"><?=substr($agenda->hora_agenda,0,5)?> · <?=ucfirst($agenda->tipo)?></p>
      </div>
      <span class="ut-status-pill pendente" style="flex-shrink:0;">Pendente</span>
      <span class="ut-queue-chevron">›</span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php
    $finalizados_mobile = [];
    foreach($agenda_items as $ag) {
      if((int)$ag->status === 2) $finalizados_mobile[] = $ag;
    }
    if(count($finalizados_mobile)): ?>
  <div style="background:#fff;border-radius:var(--ut-radius-md);padding:13px 16px;display:flex;justify-content:space-between;align-items:center;box-shadow:var(--ut-shadow-sm);border:1px dashed #e2e8f0;cursor:pointer;"
       onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none';this.querySelector('.ut-fin-count').style.display='none';">
    <span style="font-size:12px;color:#94a3b8;font-family:var(--ut-font);"><?=count($finalizados_mobile)?> finalizados<span class="ut-fin-count"> · toque para ver</span></span>
    <span style="font-size:11px;color:var(--ut-green-600);font-weight:700;font-family:var(--ut-font);">Ver todos ›</span>
  </div>
  <div style="display:none;margin-top:8px;">
    <div class="ut-queue-list">
      <?php foreach($finalizados_mobile as $agenda):
        $ini = strtoupper(mb_substr(trim($agenda->paciente_nome),0,1,'UTF-8'));
      ?>
      <div class="ut-queue-item"
           data-id="<?=$agenda->id?>"
           data-nome="<?=htmlspecialchars($agenda->paciente_nome, ENT_QUOTES)?>"
           data-ini="<?=$ini?>"
           data-hora="<?=substr($agenda->hora_agenda,0,5)?>"
           data-tipo="<?=htmlspecialchars($agenda->tipo, ENT_QUOTES)?>"
           data-status="<?=$agenda->status?>"
           data-prestador="<?=htmlspecialchars((string)$agenda->prestador_nome, ENT_QUOTES)?>"
           data-data="<?=$agenda->data_agenda?>"
           data-paciente-id="<?=$agenda->id_paciente?>"
           onclick="utOpenSheet(this)">
        <div class="ut-queue-avatar" style="background:#6b7280;"><?=$ini?></div>
        <div style="flex:1;min-width:0;">
          <p class="ut-queue-name"><?=htmlspecialchars($agenda->paciente_nome)?></p>
          <p class="ut-queue-meta"><?=substr($agenda->hora_agenda,0,5)?> · <?=ucfirst($agenda->tipo)?></p>
        </div>
        <span class="ut-status-pill finalizado" style="flex-shrink:0;">Finalizado</span>
        <span class="ut-queue-chevron">›</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<!-- ══ FIM MOBILE AGENDA ════════════════════════════════════ -->

<!-- ══ BOTTOM SHEET ════════════════════════════════════════ -->
<div class="ut-bottom-sheet-backdrop" id="ut-sheet-backdrop"></div>
<div class="ut-bottom-sheet" id="ut-bottom-sheet">
  <div class="ut-bottom-sheet-handle"></div>
  <div class="ut-bottom-sheet-patient">
    <div class="ut-bottom-sheet-avatar" id="ut-sheet-avatar" style="background:var(--ut-green-900);">?</div>
    <div>
      <span class="ut-bottom-sheet-name" id="ut-sheet-nome">Paciente</span>
      <span class="ut-bottom-sheet-meta" id="ut-sheet-meta">–</span>
    </div>
  </div>
  <div class="ut-bottom-sheet-actions">
    <a href="#" class="ut-sheet-btn ut-sheet-btn-prontuario" id="ut-sheet-prontuario">📋 Prontuário</a>
    <a href="#" class="ut-sheet-btn ut-sheet-btn-iniciar" id="ut-sheet-status">▶ Iniciar</a>
    <button class="ut-sheet-btn ut-sheet-btn-remarcar" id="ut-sheet-remarcar-btn" type="button">📅 Remarcar</button>
    <a href="#" class="ut-sheet-btn ut-sheet-btn-cancelar" id="ut-sheet-cancelar" onclick="return confirm('Cancelar este agendamento?')">✕ Cancelar</a>
  </div>
</div>
<!-- ══ FIM BOTTOM SHEET ══════════════════════════════════════ -->
```

- [ ] **Step 4: Adicionar JS do bottom sheet antes de `</body>`**

Logo **antes** do `</body>` final, adicionar:

```html
<script>
/* ── UTec Bottom Sheet ─────────────────────────────────────── */
(function () {
  var sheet    = document.getElementById('ut-bottom-sheet');
  var backdrop = document.getElementById('ut-sheet-backdrop');
  if (!sheet || !backdrop) return;

  var BASE = '<?=base_url()?>';

  function openSheet(el) {
    var nome      = el.getAttribute('data-nome') || 'Paciente';
    var ini       = el.getAttribute('data-ini')  || nome.charAt(0).toUpperCase();
    var hora      = el.getAttribute('data-hora') || '';
    var tipo      = el.getAttribute('data-tipo') || '';
    var prestador = el.getAttribute('data-prestador') || '';
    var status    = parseInt(el.getAttribute('data-status') || '0', 10);
    var id        = el.getAttribute('data-id') || '';
    var pacId     = el.getAttribute('data-paciente-id') || '';
    var dataAg    = el.getAttribute('data-data') || '';
    var telefone  = el.getAttribute('data-telefone') || '';

    document.getElementById('ut-sheet-avatar').textContent = ini;
    document.getElementById('ut-sheet-nome').textContent   = nome;
    document.getElementById('ut-sheet-meta').textContent   = hora + ' · ' + tipo + (prestador ? ' · ' + prestador : '');

    var prontuarioBtn = document.getElementById('ut-sheet-prontuario');
    prontuarioBtn.href = BASE + 'adm/usuarios/prontuario/' + pacId + '/' + id;

    var statusBtn = document.getElementById('ut-sheet-status');
    statusBtn.href = BASE + 'adm/atendimento/set_status_agenda/' + id + '/' + status;
    if (status === 0) {
      statusBtn.textContent = '▶ Iniciar';
      statusBtn.className   = 'ut-sheet-btn ut-sheet-btn-iniciar';
    } else if (status === 1) {
      statusBtn.textContent = '✓ Finalizar';
      statusBtn.className   = 'ut-sheet-btn ut-sheet-btn-finalizar';
    } else {
      statusBtn.textContent = '↺ Reabrir';
      statusBtn.className   = 'ut-sheet-btn ut-sheet-btn-reabrir';
    }

    var cancelarBtn = document.getElementById('ut-sheet-cancelar');
    if (cancelarBtn && status !== 3) {
      cancelarBtn.href  = BASE + 'adm/atendimento/cancelar_agenda/' + id;
      cancelarBtn.style.display = '';
    } else if (cancelarBtn) {
      cancelarBtn.style.display = 'none';
    }

    /* Remarcar: preenche o form existente e fecha o sheet */
    var remarcarBtn = document.getElementById('ut-sheet-remarcar-btn');
    if (remarcarBtn) {
      remarcarBtn.onclick = function () {
        closeSheet();
        var idField   = document.getElementById('remarcar-id-agenda');
        var dataField = document.getElementById('remarcar-data');
        var horaField = document.getElementById('remarcar-hora');
        if (idField)   idField.value   = id;
        if (dataField) dataField.value = dataAg;
        if (horaField) horaField.value = hora;
        var box = document.getElementById('remarcacao-box');
        if (box) {
          box.style.display = 'block';
          box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      };
    }

    sheet.classList.add('is-open');
    backdrop.classList.add('is-visible');
    document.body.style.overflow = 'hidden';
  }

  function closeSheet() {
    sheet.classList.remove('is-open');
    backdrop.classList.remove('is-visible');
    document.body.style.overflow = '';
  }

  window.utOpenSheet = openSheet;
  backdrop.addEventListener('click', closeSheet);
})();
</script>
```

- [ ] **Step 5: Ocultar tabela e elementos desktop no mobile; filtro fica recolhível**

No `<style>` existente do arquivo, adicionar ao final:
```css
@media (max-width: 767.98px) {
  .agenda-panel { display: none; }
  .pac-search-card { display: none; }
  .ob-card { display: none; }
  .ut-stats-desktop { display: none; }
  /* Filtro: oculto por padrão no mobile, expansível */
  .agenda-filter-card { display: none; }
  .agenda-filter-card.ut-filter-open { display: block !important; }
}
```

Adicionar classe `ut-stats-desktop` no wrapper dos stat chips criado na Task 4:
```html
<div class="ut-stats-desktop" style="...">
```

Adicionar botão "Filtrar" no header mobile, **após** o bloco de stats dentro de `.ut-agenda-header`:
```html
<button type="button" id="ut-filter-toggle"
        style="width:100%;margin-top:10px;background:rgba(255,255,255,.1);border:1px solid rgba(110,231,183,.2);border-radius:var(--ut-radius-sm);color:#a7f3d0;font-family:var(--ut-font);font-size:12px;font-weight:700;padding:9px;cursor:pointer;">
  ⚙ Filtrar por data / profissional
</button>
```

Adicionar JS (antes do `</body>`, junto ao script do bottom sheet):
```javascript
/* Filtro mobile toggle */
var filterToggle = document.getElementById('ut-filter-toggle');
if (filterToggle) {
  filterToggle.addEventListener('click', function () {
    var fc = document.querySelector('.agenda-filter-card');
    if (fc) { fc.classList.toggle('ut-filter-open'); }
  });
}
```

- [ ] **Step 5b: Conectar o ☰ do header mobile ao menu Adminto**

Substituir o `<div>` estático do ☰ no header mobile (Step 2) por um elemento clicável que dispara o toggle do `.menu-mobile` do Adminto:

```html
<button type="button" id="ut-mobile-menu-btn"
        style="width:36px;height:36px;background:rgba(255,255,255,.1);border:0;border-radius:9px;color:#a7f3d0;font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">☰</button>
```

Adicionar JS (antes do `</body>`):
```javascript
/* Mobile menu trigger */
var mobileMenuBtn = document.getElementById('ut-mobile-menu-btn');
if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener('click', function () {
    var toggler = document.querySelector('.mobile-menu-button a, .menu-mobile-toggler');
    if (toggler) { toggler.click(); }
    else {
      /* Fallback: toggle classe diretamente */
      document.querySelector('.menu-mobile').classList.toggle('menu-open');
    }
  });
}
```

- [ ] **Step 6: Verificar no browser (mobile, < 768px)**

- [ ] Header esmeralda com logo e stats aparece no topo
- [ ] Atendimento ativo em destaque com botões "Prontuário" e "Finalizar"
- [ ] Fila de espera em lista compacta
- [ ] Tocar em item da fila abre o bottom sheet com nome, hora, tipo
- [ ] Botão "Iniciar" / "Finalizar" / "Reabrir" correto por status
- [ ] Bottom sheet fecha ao tocar no backdrop
- [ ] "Remarcar" preenche o form de remarcação e rola até ele
- [ ] "Cancelar" exibe confirmação e redireciona
- [ ] Finalizados aparecem colapsados, expandem ao tocar
- [ ] No desktop: tabela original intacta, mobile blocks ocultos

- [ ] **Step 7: Commit**

```bash
git add application/views/adm/usuarios/new/atendimentos.php
git commit -m "feat: agenda mobile — layout C+B, header esmeralda, bottom sheet de ações"
```

---

## Task 6: Atualizar `prontuario.php` — melhorias desktop + mobile

**Files:**
- Modify: `application/views/adm/usuarios/new/prontuario.php`

- [ ] **Step 1: Adicionar import do utec-redesign.css no `<head>`**

```html
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 2: Substituir pills de status da timeline por `.ut-status-pill`**

No loop da timeline, localizar:
```php
<span class="timeline-status <?=$status_class?>"><?=$status_nome?></span>
```

Substituir por:
```php
<?php
  $ut_tl_pill = 'pendente';
  if((int)$agenda->status === 1) $ut_tl_pill = 'atendimento';
  if((int)$agenda->status === 2) $ut_tl_pill = 'finalizado';
  if((int)$agenda->status === 3) $ut_tl_pill = 'cancelado';
?>
<span class="ut-status-pill <?=$ut_tl_pill?>"><?=$status_nome?></span>
```

Fazer o mesmo para o pill do card de atendimento ativo (variável `$status_card_class` / `$status_card_nome`):
```php
<?php
  $ut_card_pill = 'pendente';
  if((int)$dd_agenda->status === 1) $ut_card_pill = 'atendimento';
  if((int)$dd_agenda->status === 2) $ut_card_pill = 'finalizado';
  if((int)$dd_agenda->status === 3) $ut_card_pill = 'cancelado';
?>
<span class="ut-status-pill <?=$ut_card_pill?>"><?=$status_card_nome?></span>
```

- [ ] **Step 3: Envolver botões de ação do prontuário em `.ut-action-grid`**

Localizar o `<div class="timeline-actions">` dentro do form do atendimento ativo (`id="form"`). Substituir a classe:
```html
<div class="ut-action-grid" style="margin-top:18px;">
```
(mantendo todos os botões filhos sem alteração)

- [ ] **Step 4: Adicionar sticky save bar no mobile**

Localizar o botão "Salvar sem encerrar" dentro do form. Envolvê-lo num wrapper sticky apenas no mobile, adicionando **antes** do `<div class="ut-action-grid">`:

```html
<div class="ut-sticky-save ut-mobile-only">
  <button class="btn btn-block" type="submit" name="acao_status" value="salvar"
          style="background:var(--ut-green-900);color:#fff;font-family:var(--ut-font);font-weight:700;padding:13px;border-radius:var(--ut-radius-md);border:0;width:100%;">
    Salvar prontuário
  </button>
</div>
```

- [ ] **Step 5: Adicionar fonte Outfit nos títulos da timeline**

No `<style>` existente, adicionar ao final:
```css
.section-heading,
.timeline-date,
.timeline-section h6,
.element-header {
  font-family: var(--ut-font) !important;
}
.timeline-list:before {
  background: linear-gradient(180deg, var(--ut-green-border) 0%, #e2e8f0 100%);
}
.timeline-dot {
  border-color: var(--ut-green-600) !important;
  color: var(--ut-green-600) !important;
}
```

- [ ] **Step 6: Verificar no browser**

Desktop:
- [ ] Pills de status com cores esmeralda
- [ ] Títulos com Outfit
- [ ] Timeline com linha e dots esmaralda

Mobile:
- [ ] Botões de ação em grid 2×2
- [ ] Sticky "Salvar prontuário" fixo no rodapé ao preencher os campos
- [ ] Textareas com height adequado para digitação

- [ ] **Step 7: Commit**

```bash
git add application/views/adm/usuarios/new/prontuario.php
git commit -m "feat: prontuário — pills esmeralda, action grid mobile, sticky save"
```

---

## Task 7: Atualizar `atendimento.php` — formulário novo agendamento

**Files:**
- Modify: `application/views/adm/atendimento/atendimento.php`

- [ ] **Step 1: Adicionar import do utec-redesign.css no `<head>`**

```html
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 2: Atualizar o card de resumo do paciente com estilo esmeralda**

No `<style>` existente do arquivo, adicionar ao final:
```css
.booking-summary {
  background: linear-gradient(135deg, var(--ut-green-50) 0%, #ffffff 100%);
  border-color: var(--ut-green-border);
}
.booking-summary h6,
.booking-form-card h6,
.booking-form-card label {
  font-family: var(--ut-font);
}
.booking-form-card .btn-primary {
  background: var(--ut-green-900);
  border-color: var(--ut-green-900);
  font-family: var(--ut-font);
  font-weight: 700;
}
.booking-form-card .btn-primary:hover {
  background: var(--ut-green-800);
  border-color: var(--ut-green-800);
}
@media (max-width: 767.98px) {
  .booking-form-card .row > div {
    margin-bottom: 14px;
  }
  .booking-form-card .btn-primary {
    width: 100%;
    padding: 13px;
    font-size: 15px;
  }
}
```

- [ ] **Step 3: Verificar no browser (mobile e desktop)**

- [ ] Card de resumo com fundo verde claro e borda esmeralda
- [ ] Botão "Confirmar agendamento" full-width no mobile
- [ ] Inputs com altura adequada no mobile

- [ ] **Step 4: Commit**

```bash
git add application/views/adm/atendimento/atendimento.php
git commit -m "feat: novo agendamento — estilo esmeralda, botão full-width no mobile"
```

---

## Task 8: Atualizar `lista.php` — refinamentos esmeralda

**Files:**
- Modify: `application/views/adm/usuarios/new/lista.php`

- [ ] **Step 1: Adicionar import do utec-redesign.css no `<head>`**

```html
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
```

- [ ] **Step 2: Atualizar avatares para gradiente esmeralda**

No `<style>` existente, substituir o `.ul-avatar`:
```css
.ul-avatar {
  width: 46px; height: 46px; border-radius: 50%;
  background: linear-gradient(135deg, var(--ut-green-900), var(--ut-green-600));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-family: var(--ut-font); font-weight: 700; font-size: 18px; flex-shrink: 0;
  overflow: hidden;
}
```

- [ ] **Step 3: Atualizar botão Prontuário para cor esmeralda**

Substituir `.ul-btn-prontuario`:
```css
.ul-btn-prontuario {
  background: var(--ut-green-50);
  color: var(--ut-green-900);
  border-color: var(--ut-green-border);
}
.ul-btn-prontuario:hover {
  background: #dcfce7;
  color: var(--ut-green-800);
}
```

- [ ] **Step 4: Atualizar fonte dos nomes e títulos**

```css
.ul-header-title,
.ul-card-name,
.ul-search-input {
  font-family: var(--ut-font) !important;
}
```

- [ ] **Step 5: Verificar no browser**

- [ ] Avatares com gradiente verde escuro→verde médio
- [ ] Botão "Prontuário" em tom esmeralda
- [ ] Nomes em Outfit
- [ ] Filtro de nome funciona normalmente
- [ ] Desktop e mobile sem regressões visuais

- [ ] **Step 6: Commit**

```bash
git add application/views/adm/usuarios/new/lista.php
git commit -m "feat: lista usuários — avatares esmeralda, botão prontuário, fonte Outfit"
```

---

## Verificação Final

Após concluir todas as tasks, verificar em três contextos:

**Mobile (DevTools → iPhone 12 Pro, 390×844px):**
- [ ] Agenda: header esmeralda, card ativo, fila, bottom sheet funcional
- [ ] Menu: hamburger abre sidebar esmeralda
- [ ] Prontuário: grid de ações, sticky save, formulário legível
- [ ] Lista: cards responsivos

**Tablet (DevTools → iPad, 768px):**
- [ ] Tabela da agenda aparece (não o layout mobile)
- [ ] Menu sidebar visível
- [ ] Nenhum elemento overflow horizontal

**Desktop (1280px):**
- [ ] Sidebar esmeralda com menu completo
- [ ] Top bar esmeralda com logo
- [ ] Agenda: tabela com stat chips, pills coloridos, hover
- [ ] Prontuário: timeline com dots esmeralda
- [ ] Lista: avatares com gradiente

**Funcional:**
- [ ] Filtro da agenda filtra corretamente
- [ ] Remarcar abre o form de remarcação
- [ ] Cancelar exibe confirmação
- [ ] Upload de arquivos no prontuário funciona
- [ ] Login / logout sem regressão
