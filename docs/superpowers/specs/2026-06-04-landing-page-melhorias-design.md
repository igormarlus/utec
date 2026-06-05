# Design Spec — Melhorias Cirúrgicas na Landing Page

**Data:** 2026-06-04
**Arquivo alvo:** `application/views/index-front.php`
**Escopo:** ajustes pontuais sem redesign — paleta atual (azul/verde/roxo) mantida integralmente.

---

## 1. Contexto e Objetivo

A landing page atual tem estrutura sólida e boa cobertura de seções. O objetivo não é um redesign, mas corrigir pontos de fricção específicos e adicionar uma seção de planos que informe sem interferir no funil de trial.

**Funil desejado:** Visitante → Cria interesse no produto → Entende as opções → **Cadastra trial** → (depois) Converte em plano pago.

Os preços não devem ser o elemento central da decisão; a conversão inicial é sempre para o trial gratuito de 30 dias.

---

## 2. Segmentação de Público (mantida)

Os dois cards de segmento no hero (`segment-card clinica` e `segment-card profissional`) permanecem exatamente como estão — são o principal elemento de direcionamento de público da página. Nenhuma alteração de copy, layout ou CTA nessa área.

---

## 3. Melhoria 1 — Conectores visuais no "Como funciona"

**Seção:** `.how-section` (`#como-funciona`)

**Problema:** os 3 steps ficam soltos, sem indicação visual de sequência entre eles.

**Solução:** adicionar `::before` em `.how-grid` criando uma linha horizontal que passa pelos centros dos três círculos.

**Especificações:**
- Implementação: `.how-grid::before { content:''; position:absolute; top:26px; left:calc(50%/3); right:calc(50%/3); height:2px; background:linear-gradient(90deg, var(--brand-blue), var(--brand-green)); z-index:0; }` — ajustar `left`/`right` para alinhar com os centros dos círculos (que têm 52px e `.how-grid` já tem `position:relative`)
- Os `.step-number` já recebem `position:relative; z-index:1` para ficarem sobre a linha
- **Mobile** (`max-width: 700px`): `display:none` no `::before` (layout já é coluna única)

---

## 4. Melhoria 2 — Seção de planos discreta

**Posição:** entre `.social-section` (depoimentos) e `.cta-section` (CTA final).

**Título:** "Escolha seu ponto de partida — todos com 30 dias grátis"
**Subtítulo:** "Experimente sem compromisso. Você escolhe o plano certo depois de conhecer o sistema."

### 4.1 Cards de plano

Quatro cards em grid horizontal (4 colunas no desktop, 2 no tablet, 1 no mobile):

| Card | Nome | Descrição | CTA | Link |
|------|------|-----------|-----|------|
| Solo | Solo | Para quem atende individualmente | Experimentar grátis → | `/experimentar` |
| Clínica | Clínica | Para consultórios com equipe | Experimentar grátis → | `/experimentar` |
| Pro | Pro | Para clínicas em expansão | Experimentar grátis → | `/experimentar` |
| Customizado | Clínica grande? | Muitos profissionais? Montamos um plano sob medida | Falar pelo WhatsApp → | `https://wa.me/5581983276882?text=...` |

### 4.2 Tratamento de preço

O preço, quando exibido, deve aparecer apenas como texto pequeno e muted abaixo da descrição (ex: `a partir de R$ 79/mês`). Nenhum elemento de destaque visual (sem badges de "Mais popular", sem caixas coloridas, sem tamanho de fonte destacado). O preço é informativo, não persuasivo.

O card "Customizado" não exibe preço — apenas "Sob consulta".

### 4.3 Estilo visual

- Fundo da seção: `var(--paper)` (#f8fafc) — mesmo tom das seções alternadas já existentes
- Cards: fundo branco, borda 1px `var(--border)`, border-radius `var(--radius-md)`, sem sombra pesada
- CTA dos cards Solo/Clínica/Pro: botão outline (borda primária, texto primário) — não filled, para não competir com os CTAs principais do hero e do CTA final
- CTA do card Customizado: botão com cor do WhatsApp (#25d366)
- Nenhum card deve ter destaque de "recomendado" ou "popular" — todos equivalentes visualmente

### 4.4 Dados dos planos (PHP)

O controller `Home::base()` já chama `$this->saas_model->get_public_plans()` e passa o resultado como **`$public_plans`** para a view. **Nenhuma alteração no controller é necessária.**

O card "Customizado" é estático (HTML fixo, sem dado de banco). O link do WhatsApp segue o padrão já usado na página: `https://wa.me/5581983276882?text=Ol%C3%A1%2C+gostaria+de+saber+mais+sobre+planos+para+minha+cl%C3%ADnica`.

---

## 5. O que NÃO muda

- Paleta de cores (azul/verde/roxo atual)
- Header e navegação
- Hero (eyebrow, h1, trust badges, imagem do produto, cards de segmento)
- Seção de funcionalidades
- Seção de especialidades
- Depoimentos
- CTA final (`.cta-section`)
- Seção de contato
- Seção de login (mantida conforme pedido — o modal no header é mais rápido, mas a seção no rodapé fica para usuários que scrollam)
- Footer
- Modal de login
- WhatsApp flutuante
- Schema JSON-LD

---

## 6. Dados dinâmicos — checklist do controller

- [x] `Home::base()` já passa `$public_plans` para a view — nenhuma alteração no controller
- [ ] Os planos ativos no banco devem ter `status = 1` e `plan_code` preenchido (validar em staging)
- [ ] O card Customizado é estático — não depende de banco

---

## 7. Responsividade

| Breakpoint | Grid de planos | Conector steps |
|------------|---------------|----------------|
| > 900px | 4 colunas | visível |
| 560px–900px | 2 colunas | visível |
| < 560px | 1 coluna | oculto |

---

## 8. Fora de escopo

- Página `experimentar.php` — considerada adequada, sem alterações
- Outras views públicas
- Mudança de paleta de cores
- Mobile hamburger menu (pode ser avaliado em iteração futura)
- A/B testing ou analytics específico para a nova seção de planos
