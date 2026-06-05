# Redesign Visual e Usabilidade — Views Admin

**Data:** 2026-06-04  
**Escopo:** Melhorias visuais e de usabilidade nas views de atendimento clínico, para desktop e mobile  
**Stack:** PHP 7 + CodeIgniter 3 + Bootstrap 4 + jQuery (sem migração de framework)

---

## 1. Contexto e Motivação

O sistema é usado diariamente por médicos, recepcionistas e colaboradores de clínicas. Os problemas identificados:

- **Menu mobile completamente quebrado:** `menu.php` tem condição `if(!$this->agent->is_mobile())` que oculta toda a navegação em dispositivos móveis. Usuário mobile navega cego.
- **Agenda inutilizável no mobile:** tabela com 7 colunas e `min-width: 980px` força scroll horizontal.
- **Visual datado:** tipografia Lato sem hierarquia forte, cards sem profundidade, botões pequenos para toque.
- **Falta de identidade:** o sistema não reflete as cores da logomarca (azul + verde).

---

## 2. Decisões de Design (resultado do brainstorming)

| Aspecto | Decisão |
|---|---|
| Layout agenda mobile | C+B híbrido — atendimento ativo em destaque + fila compacta + bottom sheet de ações |
| Navegação mobile | Hamburger → sidebar deslizante (remover condição PHP, habilitar `.menu-mobile` do Adminto) |
| Paleta visual | Esmeralda Médico — verde floresta escuro no header, verde vivo como cor de ação |
| Tipografia | Outfit (Google Fonts, pesos 400/700/800) substituindo Lato nas views redesenhadas |
| Interpretação da logo | Interpretação 1: header verde floresta `#052e16`, logo em quadrado branco, verde `#22c55e` como acento |
| Escopo da melhoria | Desktop + mobile (não apenas mobile-only) |
| Estratégia de CSS | Arquivo compartilhado `css/utec-redesign.css` importado por todas as views afetadas |

---

## 3. Arquitetura — `css/utec-redesign.css`

Arquivo CSS novo carregado em todas as views do redesign. Não substitui o CSS do Adminto — adiciona camada de identidade esmeralda com especificidade suficiente para sobrepor onde necessário.

### 3.1 Variáveis de Design

```css
:root {
  /* Paleta Esmeralda Médico */
  --ut-green-900: #052e16;   /* header, textos escuros */
  --ut-green-800: #064e3b;   /* gradiente do header */
  --ut-green-600: #059669;   /* links, meta de consulta */
  --ut-green-400: #22c55e;   /* "em curso", ações positivas */
  --ut-green-50:  #f0fdf4;   /* fundo das páginas */
  --ut-green-border: #6ee7b7;

  /* Status semânticos */
  --ut-status-pending-bg:    #fee2e2;
  --ut-status-pending-text:  #b91c1c;
  --ut-status-active-bg:     #dcfce7;
  --ut-status-active-text:   #166534;
  --ut-status-done-bg:       #fef3c7;
  --ut-status-done-text:     #92400e;
  --ut-status-cancelled-bg:  #f1f5f9;
  --ut-status-cancelled-text:#475569;

  /* Tipografia */
  --ut-font: 'Outfit', sans-serif;

  /* Elevação */
  --ut-shadow-sm: 0 2px 8px rgba(5,46,22,.06);
  --ut-shadow-md: 0 6px 20px rgba(5,46,22,.12);
  --ut-shadow-lg: 0 12px 32px rgba(5,46,22,.18);
}
```

### 3.2 Componentes CSS Definidos

| Classe | Descrição |
|---|---|
| `.ut-top-bar` | Header mobile com logo, data, stats e hamburger |
| `.ut-stat-chip` | Chip de métrica dentro do header (Total/Pendentes/Em curso/Feitos) |
| `.ut-active-card` | Card do atendimento em curso — borda esmeralda, sombra azul-verde |
| `.ut-queue-item` | Linha da fila de espera — avatar iniciais + nome + status pill + `›` |
| `.ut-queue-list` | Contêiner da lista de espera |
| `.ut-bottom-sheet` | Drawer de ações deslizante (slide-up no mobile) |
| `.ut-bottom-sheet-handle` | Alça visual do drawer |
| `.ut-status-pill` | Pill de status semântico (usa variáveis de status) |
| `.ut-btn-primary` | Botão primário esmeralda |
| `.ut-btn-ghost` | Botão fantasma com borda |
| `.ut-section-label` | Label de seção em uppercase + letra pequena |

### 3.3 Regras Responsivas

```
≥ 992px (desktop): melhorias visuais aplicadas (cores, tipografia, sombras)
768–991px (tablet): layout fluido, tabela da agenda ainda visível
< 768px (mobile):  layout C+B da agenda ativo, tabela oculta, inputs maiores
```

---

## 4. Arquivos Modificados

### 4.1 `includes/adm/menu.php`

**Mudança crítica:** remover as linhas `<? if(!$this->agent->is_mobile()){ ?>` (linha 1) e `<? } ?>` (última linha).

**Mudança visual:** 
- Sidebar `.menu-w` recebe fundo `var(--ut-green-900)` com gradiente para `var(--ut-green-800)`
- Links com hover `var(--ut-green-400)` 
- Badge UT no `logo-element` com gradiente da marca
- Sub-headers de seção com cor `#6ee7b7`

### 4.2 `includes/adm/top.php`

- Top bar recebe fundo esmeralda `var(--ut-green-900)`
- Logo em quadrado branco `40×40px` com `border-radius: 10px` e `box-shadow`
- Shortcuts e notificações mantidos — só estilo

### 4.3 `atendimentos.php` — Agenda Clínica *(maior escopo)*

**Desktop:**
- Tabela existente mantida — recebe hover states, pills de status maiores, botões com ícones SVG
- Filtros: estilo esmeralda no card de filtros
- Stats: 4 chips coloridos substituem os `agenda-stat-card` atuais
- Busca de paciente: input com ícone de lupa esmeralda

**Mobile (< 768px):**
- `<thead>` oculto via CSS
- Tabela transformada em lista de itens via `display: block` por `<tr>`
- **Card ativo:** PHP adiciona classe `.ut-active-card` na `<tr>` quando `$agenda->status == 1` — recebe borda `var(--ut-green-border)` e sombra `var(--ut-shadow-md)` via CSS
- **Fila:** `.ut-queue-item` para os demais — toque abre `.ut-bottom-sheet`
- **Bottom sheet:** `position: fixed; bottom: 0` com 4 botões em grade 2×2 (Prontuário, Iniciar/Finalizar, Remarcar, Cancelar)
- **Finalizados:** colapsados por padrão, "Ver todos ›" expande

**JS novo:** `~40 linhas` para abrir/fechar bottom sheet, preencher dados do paciente selecionado, animar slide-up.

**`data-` attributes adicionados nos `<td>`:** `data-nome`, `data-status`, `data-id`, `data-href-prontuario` — usados pelo JS do bottom sheet.

### 4.4 `prontuario.php` — Prontuário do Paciente

**Desktop:**
- Timeline com `border-left` esmeralda, dots esmeralda
- Seções do prontuário (`atendimento_inicial`, `avaliacao`, `reavaliacao`) com label colorido por especialidade
- Textareas com `min-height: 120px`

**Mobile:**
- Botões de ação em grade 2×2 (em vez de flex-wrap)
- "Salvar sem encerrar" fixo no rodapé (`position: sticky; bottom: 0`) durante preenchimento
- Métricas rápidas: 2×2 em mobile (já existe, refinar)
- Upload de arquivos: campos em coluna única

### 4.5 `atendimento.php` — Novo Agendamento

- Estilo esmeralda no card de resumo do paciente
- Mobile: campos do form em coluna única, `min-height: 48px` nos inputs/selects, botão "Confirmar" full-width

### 4.6 `lista.php` — Lista de Usuários

- Avatares com gradiente esmeralda (`var(--ut-green-900)` → `var(--ut-green-600)`)
- Botão "Prontuário" com cor esmeralda
- Fonte Outfit nos nomes e cards
- Status pills com variáveis semânticas

---

## 5. Fonte Outfit

Importada no `utec-redesign.css` via `@import`:

```css
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&display=swap');
```

Aplicada com fallback: `font-family: var(--ut-font), system-ui, sans-serif;`

---

## 6. O Que Não Muda

- Toda a lógica PHP/CI3 (controllers, models, sessão)
- Estrutura de rotas e URLs
- CSS do Adminto (`clicklinica-main.css`) — não é editado
- `includes/adm/paciente/menu.php` — já tem mobile bem implementado, apenas recebe cores esmeralda
- Views fora do escopo (saas/, especialidades/, cadastro, edição)

---

## 7. Riscos e Mitigações

| Risco | Mitigação |
|---|---|
| Remover condição mobile no menu pode revelar bugs do Adminto | Testar em 3 breakpoints após a mudança |
| Bottom sheet JS conflitar com jQuery existente | Escrever em vanilla JS puro, sem depender de plugins |
| Outfit não carregar offline/sem internet | Fallback `system-ui` no font-family |
| Sombras esmeralda ficarem muito pesadas no desktop | Usar `var(--ut-shadow-sm)` como padrão, `md` só no card ativo |

---

## 8. Critério de Sucesso

- Menu mobile abre corretamente em dispositivos < 768px
- Agenda é 100% operável com 1 polegar (toque em item abre ações completas)
- Atendimento ativo visualmente separado e acionável sem scroll
- Desktop com identidade esmeralda consistente em todas as 4 views
- Nenhuma regressão funcional nas views não afetadas
