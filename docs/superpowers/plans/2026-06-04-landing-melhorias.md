# Landing Page — Melhorias Cirúrgicas — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Adicionar conectores visuais entre os steps de "Como funciona" e uma seção de planos discreta (trial-first) entre depoimentos e o CTA final em `index-front.php`.

**Architecture:** Edições cirúrgicas em um único arquivo de view PHP com CSS inline. Nenhuma alteração de controller ou model — `$public_plans` já está disponível na view via `Home::base()`. A seção de planos usa loop PHP sobre `$public_plans` mais um card estático "Customizado".

**Tech Stack:** PHP 7, CodeIgniter 3, CSS puro, HTML. Sem JS adicional.

---

## Mapa de arquivos

| Arquivo | Ação | O que muda |
|---------|------|-----------|
| `application/views/index-front.php` | Modificar | +CSS connector (linhas ~327–337), +CSS plans section (antes de `</style>` linha 575), +HTML plans section (entre linha 1022 e 1024) |

---

## Task 1: Conector visual no "Como funciona"

**Files:**
- Modify: `application/views/index-front.php:329–335` (bloco `.step-number`)
- Modify: `application/views/index-front.php:327` (media query `max-width:700px`)

- [ ] **Step 1.1 — Adicionar `position:relative; z-index:1` ao `.step-number`**

Localize o bloco (linha ~329):
```css
        .step-number {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #fff; font-size: 22px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
```

Substitua por:
```css
        .step-number {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #fff; font-size: 22px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            position: relative; z-index: 1;
        }
```

- [ ] **Step 1.2 — Adicionar `.how-grid::before` após o bloco `.how-step p`**

Localize (linha ~337):
```css
        .how-step p { font-size: 14px; color: var(--muted); line-height: 1.65; max-width: 260px; margin: 0 auto; }
```

Substitua por:
```css
        .how-step p { font-size: 14px; color: var(--muted); line-height: 1.65; max-width: 260px; margin: 0 auto; }
        .how-grid::before {
            content: ''; position: absolute;
            top: 26px; left: calc(100% / 6); right: calc(100% / 6);
            height: 2px;
            background: linear-gradient(90deg, var(--brand-blue), var(--brand-green));
            z-index: 0;
        }
```

- [ ] **Step 1.3 — Ocultar o conector no mobile**

Localize (linha ~327):
```css
        @media(max-width:700px) { .how-grid { grid-template-columns: 1fr; } }
```

Substitua por:
```css
        @media(max-width:700px) { .how-grid { grid-template-columns: 1fr; } .how-grid::before { display: none; } }
```

- [ ] **Step 1.4 — Verificar visualmente no browser**

Abra `http://localhost/utec/` (ou o endereço local do projeto) e role até a seção "Comece a atender em 3 passos". Confira:
- Uma linha gradiente azul→verde aparece horizontalmente ligando os 3 círculos numerados
- Os números (1, 2, 3) ficam **sobre** a linha, não atrás
- Em tela estreita (< 700px) a linha não aparece

- [ ] **Step 1.5 — Commit**

```bash
git add application/views/index-front.php
git commit -m "feat: conector visual entre steps do Como Funciona"
```

---

## Task 2: CSS da seção de planos

**Files:**
- Modify: `application/views/index-front.php:574` (antes de `</style>`)

- [ ] **Step 2.1 — Inserir bloco CSS da seção de planos**

Localize (linha ~572–574):
```css
        /* ── UTILITY ── */
        .text-center { text-align: center; }
        .mt-4 { margin-top: 16px; }
    </style>
```

Substitua por:
```css
        /* ── UTILITY ── */
        .text-center { text-align: center; }
        .mt-4 { margin-top: 16px; }

        /* ── PLANS ── */
        .plans-section { padding: 72px 20px; background: var(--paper); }
        .plans-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-top: 40px;
        }
        @media(max-width: 900px) { .plans-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 560px) { .plans-grid { grid-template-columns: 1fr; } }
        .plan-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 24px 20px;
            display: flex; flex-direction: column; gap: 8px;
        }
        .plan-card--custom { border-style: dashed; }
        .plan-name { font-size: 18px; font-weight: 800; margin: 0; color: var(--text); }
        .plan-desc { font-size: 14px; color: var(--muted); line-height: 1.6; margin: 0; flex: 1; }
        .plan-price { font-size: 12px; color: var(--subtle); margin: 4px 0 0; }
        .btn-plan {
            display: block; text-align: center;
            padding: 10px 16px; border-radius: 999px;
            border: 1.5px solid var(--brand-blue); color: var(--brand-blue);
            font-size: 14px; font-weight: 600; text-decoration: none;
            transition: background .15s, color .15s; margin-top: 8px;
        }
        .btn-plan:hover { background: var(--brand-blue); color: #fff; }
        .btn-plan--wa { border-color: #25d366; color: #25d366; }
        .btn-plan--wa:hover { background: #25d366; color: #fff; }
    </style>
```

- [ ] **Step 2.2 — Verificar que a página não quebrou**

Abra `http://localhost/utec/` e confirme que a página carrega normalmente (sem erro PHP, sem layout quebrado). A nova seção ainda não aparece porque o HTML ainda não foi adicionado.

---

## Task 3: HTML da seção de planos

**Files:**
- Modify: `application/views/index-front.php:1022–1024` (entre fim do `social-section` e início do `cta-section`)

- [ ] **Step 3.1 — Inserir HTML da seção de planos**

Localize (linha ~1022–1027):
```html
</section>

<!-- ═══════════════════════════════════════════════════════
     CTA FINAL
═══════════════════════════════════════════════════════ -->
<section class="cta-section">
```

Substitua por:
```html
</section>

<!-- ═══════════════════════════════════════════════════════
     PLANOS (discreta — trial-first)
═══════════════════════════════════════════════════════ -->
<?php if(!empty($public_plans)): ?>
<section class="plans-section">
    <div class="container">
        <p class="section-label">Planos disponíveis</p>
        <h2 class="section-title">Escolha seu ponto de partida — todos com 30 dias grátis</h2>
        <p class="section-sub">Experimente sem compromisso. Você escolhe o plano certo depois de conhecer o sistema.</p>
        <div class="plans-grid">
            <?php
            $plan_descs = [
                'solo'    => 'Para quem atende individualmente',
                'clinica' => 'Para consultórios com equipe',
                'pro'     => 'Para clínicas em expansão',
            ];
            foreach($public_plans as $plano):
                $desc = isset($plan_descs[$plano->plan_code]) ? $plan_descs[$plano->plan_code] : htmlspecialchars(trim(strip_tags((string)$plano->especificacoes)));
            ?>
            <div class="plan-card">
                <h3 class="plan-name"><?=htmlspecialchars($plano->modelo)?></h3>
                <p class="plan-desc"><?=htmlspecialchars($desc)?></p>
                <p class="plan-price">a partir de R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?>/<?=htmlspecialchars($plano->billing_interval)?></p>
                <a href="<?=base_url()?>experimentar" class="btn-plan">Experimentar grátis →</a>
            </div>
            <?php endforeach; ?>
            <div class="plan-card plan-card--custom">
                <h3 class="plan-name">Clínica grande?</h3>
                <p class="plan-desc">Muitos profissionais? Montamos um plano sob medida para a sua realidade.</p>
                <p class="plan-price">Sob consulta</p>
                <a href="https://wa.me/5581983276882?text=Ol%C3%A1%2C+gostaria+de+saber+mais+sobre+planos+para+minha+cl%C3%ADnica" class="btn-plan btn-plan--wa" target="_blank" rel="noopener">Falar pelo WhatsApp →</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════
     CTA FINAL
═══════════════════════════════════════════════════════ -->
<section class="cta-section">
```

- [ ] **Step 3.2 — Verificar a seção no browser**

Abra `http://localhost/utec/` e role até após os depoimentos. Confirme:
- A seção aparece com fundo `#f8fafc` (cinza claro)
- Cards brancos com borda sutil, 4 colunas no desktop
- Nome do plano em destaque, preço em texto pequeno e cinza
- Botões outline azul para os planos, verde para o WhatsApp
- Card "Clínica grande?" com borda tracejada
- Em telas < 900px: 2 colunas; em < 560px: 1 coluna
- Se `$public_plans` estiver vazio, a seção inteira não renderiza (teste simulando `$public_plans = []` temporariamente se necessário)

- [ ] **Step 3.3 — Commit**

```bash
git add application/views/index-front.php
git commit -m "feat: seção de planos discreta na landing page (trial-first)"
```

---

## Self-Review

**Cobertura do spec:**
- [x] Conector visual entre steps (Task 1) → `::before` com gradiente
- [x] `.step-number` com `z-index:1` (Task 1.1)
- [x] Ocultar conector no mobile (Task 1.3)
- [x] Seção após depoimentos, antes do CTA (Task 3)
- [x] 4 cards: Solo, Clínica, Pro, Customizado (Task 3.1)
- [x] Preço em texto pequeno/muted (`.plan-price` com `font-size:12px; color:var(--subtle)`)
- [x] CTA = "Experimentar grátis" para planos, WhatsApp para Customizado
- [x] Grid responsivo 4→2→1 colunas (Task 2.1)
- [x] `$public_plans` já disponível, zero mudança no controller
- [x] Card Customizado estático com border dashed

**Placeholders:** nenhum.

**Consistência de nomes:** `.plan-card`, `.plan-name`, `.plan-desc`, `.plan-price`, `.btn-plan`, `.btn-plan--wa`, `.plan-card--custom` — usados de forma idêntica no CSS (Task 2) e no HTML (Task 3).
