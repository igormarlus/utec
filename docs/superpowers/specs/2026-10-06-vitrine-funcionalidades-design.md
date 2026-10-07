# Vitrine de funcionalidades (SEO/GEO — Entrega 1) — Design

Data: 2026-10-06 · Branch: `feat/vitrine-funcionalidades`
Status: aprovado no brainstorming · Entrega 2 (fora daqui): pesquisa de palavras-chave → 2 landings novas + 2–4 artigos de blog.

## Problema
As páginas de cadastro (`/experimentar`, `/assinar`), a home e as landings mostram listas de funcionalidades escritas
à mão e desatualizadas. Nenhuma cita WhatsApp (confirmação, lembrete, chatbot), horários de atendimento, prontuário
por especialidade, avisos internos, convite da equipe, manual de ajuda, nem as novidades de outubro (exportar
prontuário, rótulos, ficha do paciente, tempo de espera). `/assinar` ainda tem texto técnico ("Tenant criado com
owner principal…", "Base preparada para evoluir com WhatsApp…").

## Solução
1. **Catálogo único** `application/libraries/Funcionalidades_conteudo.php` (padrão `Manual_conteudo`: array estático, sem banco).
2. **Partial** `application/views/public/partials/funcionalidades.php` que renderiza o catálogo em dois formatos.
3. Uso do partial em `/experimentar`, `/assinar`, home e 5 landings; FAQ novas (HTML + JSON-LD); `sitemap.xml`, `llms.txt` e ledger atualizados.

## Catálogo — estrutura
Cada item: `id` (slug único), `grupo` (`agenda` | `prontuario` | `whatsapp` | `gestao`), `icone` (emoji), `titulo`,
`resumo` (≤ 90 caracteres, para cards), `descricao` (1–2 frases), `link` (rota pública existente ou `''`), `novo` (bool).
API: `Funcionalidades_conteudo::itens()`, `::por_grupo($grupo)`, `::por_ids(array $ids)` (mantém a ordem pedida, ignora ids inexistentes), `::grupos()` (`id => rótulo`: Agenda, Prontuário, WhatsApp, Gestão).

## Catálogo — itens (texto final)
| id | grupo | ícone | título | resumo | descrição | link | novo |
|---|---|---|---|---|---|---|---|
| agenda | agenda | 📅 | Agenda inteligente | Consultas por profissional, com filtros, remarcação e cancelamento na própria agenda. | Visão do dia, da semana e do mês por profissional. Remarque e cancele direto na agenda, sem retrabalho. | | não |
| horarios | agenda | 🕘 | Horários de atendimento | Grade semanal, duração da consulta e bloqueios, com sugestão de horários livres. | Cada profissional define dias, horários, duração da consulta e períodos bloqueados (férias, feriados). Ao agendar, o sistema sugere os horários livres e avisa quando é encaixe. | | não |
| tempo_espera | agenda | ⏱️ | Check-in e tempo de espera | A recepção marca a chegada e o sistema mede espera e duração da consulta. | Com um clique em "Chegou", o sistema registra a chegada do paciente e, ao iniciar e finalizar o atendimento, mostra quanto ele esperou e quanto durou a consulta. Os relatórios trazem as médias por profissional. | | sim |
| prontuario | prontuario | 📋 | Prontuário eletrônico | Histórico de atendimentos, evolução clínica e arquivos em um só lugar. | Registro de cada atendimento com histórico organizado, exames e documentos anexados, acessível de qualquer dispositivo. | sistema-prontuario-eletronico | não |
| prontuario_especialidade | prontuario | 🩺 | Prontuário por especialidade | Títulos e campos adaptados a fisioterapia, psicologia, odontologia e outras áreas. | Os campos do atendimento mudam conforme a especialidade do profissional — por exemplo, escala de dor na fisioterapia ou dente tratado na odontologia. | sistema-prontuario-eletronico | não |
| ficha_paciente | prontuario | 🗂️ | Ficha do paciente | Nome social, responsável, convênio e dados de saúde, com alergias em destaque. | Dados pessoais, responsável legal, contato de emergência, convênio e saúde básica (tipo sanguíneo, alergias, medicamentos e comorbidades). As alergias aparecem em destaque no prontuário. | | sim |
| exames | prontuario | 🔬 | Exames e arquivos | Solicite exames, acompanhe o retorno e anexe documentos ao paciente. | Checklist de exames por atendimento e arquivos do paciente (laudos, imagens, documentos) guardados no prontuário. | | não |
| exportar_prontuario | prontuario | 📤 | Exportar prontuário | PDF, Excel ou CSV por paciente, com período e registro de cada exportação. | Gere o prontuário de um paciente em PDF, Excel ou CSV, do histórico completo ou de um período. Cada exportação fica registrada (quem, quando, qual paciente), para auditoria, em linha com a LGPD. | | sim |
| whatsapp_confirmacao | whatsapp | ✅ | Confirmação de consulta pelo WhatsApp | O paciente confirma ou cancela pelo botão e a agenda se atualiza sozinha. | Ao agendar, o paciente recebe a mensagem com botões de confirmar e cancelar. A resposta aparece na agenda e a equipe é avisada. Envio pela API oficial da Meta. | confirmacao-de-consulta-por-whatsapp | não |
| whatsapp_lembrete | whatsapp | ⏰ | Lembrete automático | Lembrete pelo WhatsApp horas antes da consulta, sem ninguém disparar. | O sistema envia o lembrete ao paciente antes da consulta, pulando quem já confirmou. | confirmacao-de-consulta-por-whatsapp | não |
| chatbot | whatsapp | 💬 | Chatbot para paciente e profissional | O paciente remarca ou cancela sozinho; o profissional consulta a agenda pelo WhatsApp. | Pelo número cadastrado, o paciente vê as próximas consultas e, com 24 horas ou mais de antecedência, remarca para um horário livre ou cancela. Profissional e recepção consultam a agenda de hoje e de amanhã. | confirmacao-de-consulta-por-whatsapp | não |
| rotulos | gestao | 🏷️ | Rótulos e alertas de pacientes | Etiquetas coloridas (VIP, Convênio, Gestante, Alérgico) com filtro na lista. | Cada clínica cria seus rótulos. Os de alerta aparecem em destaque no prontuário e na agenda do dia. | | sim |
| avisos | gestao | 🔔 | Avisos internos | O sino avisa quando o paciente confirma, cancela ou remarca. | A equipe recebe avisos no sistema sobre as respostas dos pacientes, sem precisar conferir mensagem por mensagem. | | não |
| relatorios | gestao | 📊 | Relatórios clínicos | Atendimentos, exames e tempos médios por período e por profissional. | Indicadores de atendimentos finalizados e pendentes, exames, espera média, atraso e duração das consultas. | | não |
| equipe | gestao | 👥 | Equipe com acesso por perfil | Clínica, profissionais e recepção, cada um vendo só o que precisa. | Cadastre profissionais e colaboradores; o acesso chega por e-mail (com a senha ou um convite para criar a senha). Cada perfil enxerga apenas a sua parte da operação. | | não |
| manual | gestao | 📘 | Manual de ajuda | Guia por perfil, na tela e em PDF. | Manual com o passo a passo de cada perfil (clínica, profissional e recepção), atualizado a cada novidade. | | não |

Regras de texto: só o que existe hoje; sem jargão técnico (tenant, owner, endpoint, migração); português com acentos;
promessas com limites claros (24 horas no chatbot, "pela API oficial da Meta").

## Partial — `public/partials/funcionalidades.php`
Variáveis: `$func_formato` (`grade` | `lista`), `$func_itens` (array de itens), `$func_agrupar` (bool, só grade),
`$func_titulo` (opcional, h2). Grade: cards com ícone, título, resumo, selo "Novo" (`novo`), link "Saiba mais" quando
`link` ≠ ''; com agrupamento, um h3 por grupo. Lista: `<ul>` com ícone + título + resumo. CSS próprio com prefixo
`fx-` dentro do partial (bloco `<style>` uma vez por página via flag `$GLOBALS['fx_css_ok']`). Tudo escapado.
Acessível: links com texto, selo "Novo" com `aria-label`, emojis com `aria-hidden="true"`.

## Onde entra
- `/experimentar`: substitui os 5 cards da seção "Por que clínicas e profissionais escolhem a UTecnologia Saúde?" pela grade completa agrupada (16 itens), mantendo o título da seção e a faixa "30 dias grátis".
- `/assinar`: substitui os 3 bullets técnicos do card de plano pela lista com `agenda`, `prontuario`, `whatsapp_confirmacao`, `chatbot`, `relatorios`; acrescenta, após os planos, a grade completa agrupada com título "Tudo o que está incluído".
- Home (`index-front.php`): os 7 cards de funcionalidades viram: o card verde atual do WhatsApp com a ilustração (continua manual, em primeiro) + a grade com `prontuario`, `agenda`, `chatbot`, `ficha_paciente`, `tempo_espera`, `rotulos`, `relatorios`, `equipe` + link "Ver todas as funcionalidades" → `experimentar#funcionalidades` (âncora `id="funcionalidades"` na seção do `/experimentar`).
- Landings (bloco "Funcionalidades relacionadas" com o partial em grade, antes da seção de FAQ, + FAQ novas no HTML **e** no JSON-LD `FAQPage`):
  - `sistema-prontuario-eletronico`: itens `prontuario_especialidade`, `ficha_paciente`, `exportar_prontuario`, `rotulos`, `exames`; FAQ: "Consigo exportar o prontuário do paciente?" e "O sistema avisa quando o paciente tem alergia?".
  - `sistema-para-clinicas` e `software-para-clinicas`: `tempo_espera`, `rotulos`, `relatorios`, `whatsapp_confirmacao`, `equipe`; FAQ: "Dá para medir o tempo de espera dos pacientes?".
  - `sistema-para-consultorio-medico`: `tempo_espera`, `ficha_paciente`, `whatsapp_confirmacao`, `chatbot`, `horarios`; FAQ: "O paciente consegue remarcar sozinho?".
  - `casos-de-uso`: `whatsapp_confirmacao`, `chatbot`, `tempo_espera`, `rotulos`, `exportar_prontuario` (sem FAQ nova).
  Textos das respostas FAQ definidos no plano, coerentes com a tabela acima.
- `sitemap.xml`: `lastmod` = data do deploy para home, `experimentar`, `assinar` (se listadas) e as 5 landings.
- `llms.txt`: nova seção "## Funcionalidades" com uma linha por item (título: descrição curta), sem links para rotas inexistentes.
- `docs/seo-geo-agente-ledger.md`: registro desta atualização.

## Carregamento
Views públicas carregam a library via `get_instance()->load->library('funcionalidades_conteudo')` dentro do partial (as views chamam o partial com `$this->load->view('public/partials/funcionalidades', array(...))`). Nenhum controller muda.

## Testes (PHP 7.2, `tests/funcionalidades_*`)
- Catálogo: 16 itens, ids únicos, campos obrigatórios não vazios, `resumo` ≤ 90 caracteres, grupos válidos, 4 itens `novo`, todo `link` não vazio existe como chave em `application/config/routes.php`; `por_ids` preserva ordem e ignora id inexistente; nenhum texto contém "tenant", "owner", "endpoint", "migra".
- Partial: render com dados fictícios (grade agrupada e lista) sem notices, escapando `<script>`, selo "Novo" só nos itens marcados.
- Fonte: as 3 páginas + 5 landings incluem o partial; `/assinar` não contém mais "Tenant criado" nem "owner principal"; cada landing com FAQ nova tem a pergunta no HTML e no JSON-LD; `llms.txt` tem "## Funcionalidades".

## Entrega
Deploy (comparar servidor com a branch antes): library → partial → views (`experimentar`, `assinar`, 5 landings, home por último) → `sitemap.xml` → `llms.txt`. Atualizar `docs/produto/evolucao-30-dias-google-doc.md` (regra §20) e o ledger. Sem migração.
