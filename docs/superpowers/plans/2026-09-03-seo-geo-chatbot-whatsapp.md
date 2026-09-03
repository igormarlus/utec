# SEO/GEO Chatbot WhatsApp Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Posicionar o chatbot WhatsApp por perfil como diferencial verificavel na homepage e no hub de confirmacao por WhatsApp.

**Architecture:** Dois SVGs externos explicam o fluxo sem depender de screenshots ou imagens rasterizadas. A landing concentra a explicacao, FAQ e dados estruturados coerentes; a homepage ganha um card de recurso que encaminha ao hub. Nenhuma URL nova sera criada nesta fase.

**Tech Stack:** PHP 7, CodeIgniter 3, HTML/CSS inline, SVG nativo, JSON-LD, sitemap XML.

---

### Task 1: Criar SVGs acessiveis do fluxo

**Files:**
- Create: `img/seo/chatbot-whatsapp-perfis.svg`
- Create: `img/seo/chatbot-whatsapp-resumo.svg`

- [ ] **Step 1: Criar o SVG principal da landing**

Crie um SVG `viewBox="0 0 760 420"` com fundo claro, um balão `Paciente` contendo `Minhas consultas`, uma bifurcacao `Escolha de perfil`, um balão `Profissional` contendo `Agenda de hoje` e um painel `Agenda atualizada`. Inclua:

```xml
<title id="title">Fluxo do chatbot WhatsApp para clinicas</title>
<desc id="desc">Paciente e profissional escolhem seu perfil no WhatsApp e recebem funcoes adequadas ao seu acesso.</desc>
<svg role="img" aria-labelledby="title desc" ...>
```

Use somente formas, texto nativo e cores do site (`#0a2540`, `#007fa3`, `#e0f4f8`, `#10b981`).

- [ ] **Step 2: Criar o SVG compacto da homepage**

Crie `viewBox="0 0 440 230"` com tres etapas legiveis: `Mensagem`, `Perfil`, `Acao concluida`. Preserve `title`, `desc`, `role="img"` e a mesma paleta.

- [ ] **Step 3: Verificar os ativos**

Run: `Get-Content img\seo\chatbot-whatsapp-perfis.svg | Select-Object -First 8`

Run: `Get-Content img\seo\chatbot-whatsapp-resumo.svg | Select-Object -First 8`

Expected: ambos contem `<title>`, `<desc>`, `role="img"` e `viewBox`.

### Task 2: Atualizar o hub de WhatsApp

**Files:**
- Modify: `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php`

- [ ] **Step 1: Inserir estilos responsivos do novo bloco**

Adicione classes para `.chatbot-section`, `.chatbot-grid`, `.chatbot-copy`, `.chatbot-flow`, `.chatbot-profile-card` e `.chatbot-note`. A grade deve usar duas colunas em desktop e uma coluna abaixo de `900px`; o SVG deve ter `width:100%;height:auto;display:block`.

- [ ] **Step 2: Inserir a secao apos os recursos atuais**

Depois da secao de cards de recursos, inclua:

```html
<section class="chatbot-section" id="chatbot-whatsapp">
  <div class="wrap chatbot-grid">
    <div class="chatbot-copy">
      <p class="section-label" style="text-align:left">Autoatendimento por WhatsApp</p>
      <h2 style="text-align:left">Do lembrete ao <em>autoatendimento</em> pelo WhatsApp</h2>
      <p>Depois de confirmar a consulta, pacientes podem consultar proximos horarios e solicitar cancelamento ou remarcacao. Profissionais e atendentes acessam agenda e pendencias conforme o perfil cadastrado.</p>
      <a class="btn-primary" href="<?=base_url()?>experimentar">Testar o sistema por 30 dias</a>
      <p class="chatbot-note">O telefone precisa estar cadastrado. Para assuntos que exigem analise humana, a equipe continua disponivel.</p>
    </div>
    <div class="chatbot-flow">
      <img src="<?=base_url()?>img/seo/chatbot-whatsapp-perfis.svg" alt="Fluxo do chatbot WhatsApp com escolha de perfil entre paciente e profissional" width="760" height="420" loading="lazy">
    </div>
  </div>
</section>
```

Inclua tres cards textuais: `Paciente`, `Profissional e atendente` e `Administrador`, cada um descrevendo apenas comandos ja implementados.

- [ ] **Step 3: Atualizar FAQ visivel e JSON-LD correspondente**

Adicione perguntas visiveis e as mesmas perguntas no objeto `FAQPage`:

```text
O que o paciente consegue resolver pelo chatbot no WhatsApp?
Como o sistema sabe se a mensagem e de paciente ou profissional?
O chatbot substitui a recepcao da clinica?
```

As respostas devem afirmar que o acesso depende do cadastro, que paciente consulta horarios e solicita cancelamento/remarcacao, que profissional visualiza agenda e pendencias, e que casos fora do fluxo seguem para atendimento humano. Remova ou atualize a FAQ antiga que dizia que o recurso nao conduz conversa aberta.

- [ ] **Step 4: Adicionar links internos contextuais**

No final da secao, inclua links para `<?=base_url()?>sistema-para-clinicas` e `<?=base_url()?>sistema-para-consultorio-medico` com textos descritivos, sem repetir o CTA principal.

- [ ] **Step 5: Validar a view**

Run: `php -l application\views\public\seo\confirmacao-de-consulta-por-whatsapp.php`

Expected: `No syntax errors detected`.

### Task 3: Adicionar o diferencial na homepage

**Files:**
- Modify: `application/views/index-front.php`

- [ ] **Step 1: Inserir o card na grade de funcionalidades**

Adicione um card depois da funcionalidade de agenda:

```html
<a class="feature-card feature-card--chatbot" href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">
  <img src="<?=base_url()?>img/seo/chatbot-whatsapp-resumo.svg" alt="Mensagem, escolha de perfil e acao concluida no chatbot WhatsApp" width="440" height="230" loading="lazy">
  <div class="feature-icon" style="background:#ecfdf5;">💬</div>
  <h4>WhatsApp que atende por voce</h4>
  <p>Pacientes, profissionais e atendentes acessam opcoes adequadas ao seu perfil, sem tirar a equipe da rotina.</p>
  <span class="feature-link">Conhecer o WhatsApp para clinicas →</span>
</a>
```

- [ ] **Step 2: Adicionar estilos locais do card**

Defina `.feature-card--chatbot img` com borda, raio de `12px`, margem inferior de `18px` e `width:100%`. Defina `.feature-link` como texto em teal com peso `700`, sem alterar a grade existente.

- [ ] **Step 3: Atualizar `featureList` do JSON-LD**

Inclua `Chatbot WhatsApp por perfil para pacientes, profissionais e atendentes` na lista de recursos da homepage, somente se a lista existir no JSON-LD atual.

- [ ] **Step 4: Validar a view**

Run: `php -l application\views\index-front.php`

Expected: `No syntax errors detected`.

### Task 4: Publicacao e verificacao SEO/GEO

**Files:**
- Modify: `sitemap.xml`
- Verify: `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php`
- Verify: `application/views/index-front.php`

- [ ] **Step 1: Atualizar somente o `lastmod` da landing no sitemap**

Localize `https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp` e altere o `lastmod` do bloco para `2026-09-03`.

- [ ] **Step 2: Validar PHP e XML**

Run: `php -l application\views\public\seo\confirmacao-de-consulta-por-whatsapp.php`

Run: `php -l application\views\index-front.php`

Run: `[xml](Get-Content sitemap.xml)`

Expected: sem erros de sintaxe PHP e sem excecao no XML.

- [ ] **Step 3: Fazer smoke test local**

Abra `/confirmacao-de-consulta-por-whatsapp` e `/` em desktop e viewport mobile. Confirme que os SVGs carregam, que o FAQ visivel corresponde ao JSON-LD e que os CTAs apontam para `/experimentar` e para o hub corretamente.

- [ ] **Step 4: Validar dados estruturados apos publicacao**

No Rich Results Test, valide a URL publicada da landing. Corrija apenas divergencias entre o `FAQPage` e o FAQ visivel; nao adicione markup para conteudo que nao esteja na pagina.
