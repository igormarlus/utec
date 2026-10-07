# Relatório do agente SEO/GEO — 2026-10-06

**Bloco do rodízio:** 2. Concorrentes (com auditoria do espaço chatbot/automação de WhatsApp, pendente desde o ledger de 2026-08-20)
**Método:** Google Autocomplete (`client=firefox&hl=pt-BR&gl=br`), 54 consultas em 4 lotes, 1 s entre chamadas
**Execução:** manual, via sessão do Claude Code (sem commit, deploy nem SQL aplicado)

---

## 1. O que foi testado

### Concorrentes já mapeados (reteste)

| Concorrente | Marca | `alternativa X` / `X vs` | `X preço` |
|---|---|---|---|
| Feegow | navegacional (login, entrar, agenda) | vazio | "feegow clinic preço" |
| Odontoclinic | mistura com clínicas físicas homônimas (Recife, Paulista) + "odontoclinic sistema" | vazio | — |
| Shosp | login, sistema, "shosp afya", telemedicina | vazio | eco |
| Clínica nas Nuvens | login, app, api, **"reclame aqui"** | — | eco |
| Amplimed | login, agenda, receitas | vazio | eco |
| Belasis | pro, app, booster | — | — |
| Ninsaúde | apolo, clinic, **preço**, código TUSS | vazio | sinal |

### Concorrentes novos

iClinic (Afya), Clinicorp, Simples Dental, ProDoctor, Doctoralia, Medplus. Todos com marca forte, mas **só navegacional**. `alternativa X` vazio para iClinic, Clinicorp e Simples Dental. Sinal de preço em "iclinic planos e preços", "afya iclinic preço" e "simples dental valor/planos".

### Intenção comparativa genérica

- **Com sinal:** "melhor sistema para clínica médica", "melhores sistemas para clínicas médicas", "melhor sistema para consultório médico", "melhor sistema para clínica odontológica", "melhor software para clínica médica" e "melhor sistema para psicólogos" (só eco).
- **Sem sinal:** "comparativo sistemas para clínica" (vazio) e "sistema para clínica barato" (eco).
- Também aparecem estética e veterinária, que ficam fora do escopo.

### Espaço chatbot / automação de WhatsApp

- **Com sinal forte:** "chatbot para clínica(s)", que se desdobra em médica, odontológica, "whatsapp para clínicas", "com ia para clínicas" e "consultório médico/odontológico".
- **Com sinal forte, mas sem recurso equivalente no produto:** "ia para clínicas", "agente ia para clínicas", "secretaria ia para clínicas" e "atendimento ia para clínicas".
- **Com sinal, mas o produto ainda não faz:** "agendamento pelo whatsapp (automático/online)" e "como criar um bot de agendamento no whatsapp".
- **Sem sinal:** "automação whatsapp para clínica", "atendente virtual para clínica" e "secretaria virtual para consultório".
- **Falso positivo:** "chatbot para psicólogo". As sugestões são "chatbot psicólogo gratuito/online", ou seja, um bot que faz o papel do psicólogo.

---

## 2. O que foi criado

**1 landing page, 0 artigos.**

### Landing `/chatbot-para-clinicas`

| Item | Arquivo |
|---|---|
| View | `application/views/public/seo/chatbot-para-clinicas.php` |
| Controller | `application/controllers/Home.php` → `seo_chatbot_para_clinicas()` |
| Rota | `application/config/routes.php` → `$route['chatbot-para-clinicas']` |
| Sitemap | `sitemap.xml` (monthly, 0.7, lastmod 2026-10-06) |

**Por que criar:**
- A demanda está confirmada, com 7 variações reais.
- A intenção comercial ("chatbot") é distinta da landing `confirmacao-de-consulta-por-whatsapp`, cujo title e H1 focam em confirmação e lembrete.
- O artigo `chatbot-para-clinica-whatsapp-o-que-faz-e-o-que-nao-faz` é informativo; a landing é comercial e linka para ele.
- O recurso está em produção desde 2026-09-23.

**Conteúdo:**
- Hero com um mock de conversa fiel ao fluxo real: menu "👋 Olá! Escolha uma opção abaixo." → dias livres → horário → confirmação.
- Seção com os 3 perfis e os comandos reais de `Whatsapp_chatbot::comandos_permitidos()`.
- 6 recursos e uma tabela "o que é automático e o que não é".
- FAQ com 7 perguntas e 3 blocos JSON-LD (SoftwareApplication, BreadcrumbList, FAQPage).

**Limitações declaradas na página:**
- O chatbot **não usa IA**, funciona por menus.
- **Não marca consulta nova.**
- Remarcação automática só com 24h ou mais e com grade cadastrada.
- Exige a Cloud API (não funciona com o WhatsApp do celular).
- Número em mais de um cadastro é bloqueado.

**Verificação:**
- `php -l` limpo em view, controller e routes; `sitemap.xml` é XML válido; os 3 JSON-LD são válidos.
- Render local isolado OK: 33 KB, sem notices.
- Teste pela URL local não foi possível: o site local inteiro devolve 500 hoje, inclusive a home e landings antigas (provável MySQL do WAMP parado). Não é problema da landing nova.

**Antes de publicar, conferir:**
- Preço "a partir de R$ 79/mês" e o limite de 3 disparos no trial, copiados da landing de WhatsApp; a auditoria de 2026-09-24 pede checar preços fixos no HTML contra os planos ativos.
- Se os 3 artigos linkados estão no ar: `chatbot-para-clinica-whatsapp-o-que-faz-e-o-que-nao-faz`, `paciente-remarcar-consulta-pelo-whatsapp` e `enviar-mensagem-para-paciente-whatsapp-lgpd`.

---

## 3. Recomendações (não criadas)

1. **"Melhor sistema para clínica médica" (demanda confirmada).** *(Corrigido na 2ª rodada do dia.)* O tema já está coberto por artigos no ar: `melhor-software-para-clinicas-como-avaliar` e `software-medico-como-escolher-para-consultorio-ou-clinica` (este também responde como `software-medico-como-escolher-consultorio-clinica`). Não há artigo pendente a reorientar. Recomendo:
   - incluir "melhor sistema para clínica médica" e "melhores sistemas para clínicas médicas" no title/H1 ou num H2 do `melhor-software-para-clinicas-como-avaliar`;
   - conferir se os dois slugs do artigo de software médico têm canonical único, para não dividir sinal.

2. **"IA para clínicas", "agente/secretária de IA" (demanda forte).** O produto não tem IA no atendimento, e criar página agora seria conteúdo enganoso. É uma decisão de produto (agente-produto). O `Disponibilidade_model::proximos_livres()` já é a interface prevista para um chatbot de IA marcar consultas. Se isso entrar no roadmap, essa keyword justifica uma landing própria.

3. **"Agendamento pelo WhatsApp" (sinal real).** Só faz sentido como landing quando o chatbot marcar consulta nova. Hoje o artigo `sistema-de-agendamento-com-whatsapp-o-que-da-para-automatizar` cobre a parte informativa.

4. **Preço de concorrentes.** Há busca por "feegow preço", "iclinic planos e preços", "simples dental valor" e "ninsaude preço". Sugestão: enriquecer `quanto-custa-um-software-para-clinica` (fase 1, 2026-08-20) com uma tabela de faixas de preço, **somente com preços públicos verificados e datados**. Não inventar valores.

5. **Landings `alternativa-*` existentes (Feegow, Odontoclinic, Shosp, Clínica nas Nuvens).** No segundo reteste seguido continua não havendo busca por "alternativa"/"vs". Vale olhar no Search Console para quais consultas elas aparecem, provavelmente "sistema X" ou "X preço". Se for o caso, ajustar title e H1 para essa intenção em vez de "alternativa a X".

6. **Links internos para a landing nova** (edição de páginas existentes fica fora do escopo automático):
   - link "Chatbot para clínicas" na seção `#chatbot-whatsapp` de `confirmacao-de-consulta-por-whatsapp.php`;
   - link no corpo do artigo `chatbot-para-clinica-whatsapp-o-que-faz-e-o-que-nao-faz`;
   - item no rodapé/menu da home.

7. **Inconsistência na landing de confirmação.** O hero diz "No dia anterior e na manhã do atendimento, o sistema envia o lembrete" e o passo 2 fala em "janelas que você definir". O card de recursos e o cron real enviam um lembrete único, até 7h antes, e as janelas D-1/manhã ainda estão pendentes (`docs/whatsapp-lembrete-templates-pendente.md`). Ajustar o hero e o passo 2 para "poucas horas antes".

8. **Achado de produto (fora do SEO).** No chatbot, a opção "💬 Atendimento" do **paciente** responde "Para assuntos sobre atendimento, fale com o dev." (`application/libraries/Whatsapp_chatbot.php:101`). Esse texto aparece para pacientes reais em produção. Encaminhar ao agente-whatsapp para trocar por algo como "fale com a recepção da clínica" e, se possível, mostrar o contato da clínica.

---

## 4. Descartes

| Item | Motivo |
|---|---|
| `/alternativa-iclinic`, `/alternativa-clinicorp`, `/alternativa-simples-dental` | Nenhum sinal de intenção comparativa |
| `/alternativa-amplimed`, `/alternativa-belasis`, `/alternativa-ninsaude` | 2º reteste sem sinal (mantém o descarte de 2026-07-14) |
| Landing "chatbot para psicólogos" | Falso positivo e conflito ético |
| "Automação WhatsApp para clínica", "atendente/secretária virtual" | Sem sinal |
| "Comparativo de sistemas para clínica" | Vazio |

Não foram retestados agora: `ivix`, `docway` e `meupaciente`, com reteste agendado para 2026-10-14.

---

## 5. Pendências para o Igor

- [ ] Revisar e commitar: a view nova, `Home.php`, `routes.php`, `sitemap.xml`, o ledger e este relatório.
- [ ] Deploy via agente-dev-infra: view, `Home.php`, `routes.php`, `sitemap.xml`. Subir a view antes da rota.
- [ ] Reenviar o sitemap no Search Console após o deploy.
- [ ] Recomendações 1, 6, 7 e 8 (rápidas e de impacto direto).

---

# Rodada 2 — bloco 3 (variações semânticas e GEO), 2026-10-06

Feita a pedido do Igor depois da publicação da landing do chatbot (deploy: view, `Home.php`, `routes.php` e `sitemap.xml` enviados por FTP, com os arquivos de produção conferidos contra o `main` antes; healthcheck: landing 200, home/admin 200, `adm/atendimento` 302). Para não interferir na sessão que está na branch `feat/vitrine-funcionalidades`, o trabalho foi feito na worktree `../utec-seo` (branch `seo/bloco3-2026-10-06`).

## Testado (28 consultas)

| Grupo | Resultado |
|---|---|
| `programa para clínica/consultório` | Demanda forte, mas é sinônimo de "sistema" e já é atendida pelas landings `sistema-*` |
| `sistema para clínica grátis/gratuito` | Demanda forte, já coberta por `sistema-gratuito-para-clinicas` |
| `na nuvem`, `online`, `sistema web` | Vazio / eco |
| `como migrar de planilha`, `quanto custa...`, `valor sistema...` | Vazio |
| `planilha para clínica`, `planilha de controle de pacientes` | **Demanda forte** (excel grátis, psicologia, modelo, dentista) |
| `modelo de prontuário` | **Demanda forte** (psicológico CRP/CFP/PDF/Word, médico, odontológico, fisioterapia) |
| `quanto tempo guardar prontuário` | **Demanda forte** (médico, odontológico, psicológico) |
| `ficha de anamnese` | Forte, mas dominado por estética e personal trainer |
| Medicina do trabalho (`clínica ocupacional`, `exame admissional`, `eSocial`) | Vazio |

## Criado: 4 artigos

Arquivo: `docs/seo-geo-agente-blog-2026-10-06.sql` (não executado; `id_categoria = 1`, conferir).

| Slug | Palavras | Link interno principal |
|---|---|---|
| `planilha-de-controle-de-pacientes` | 652 | `/sistema-para-clinicas`, WhatsApp |
| `modelo-de-prontuario-psicologico` | 647 | `/sistema-para-psicologos`, artigo de prontuário para psicólogos |
| `quanto-tempo-guardar-prontuario` | 601 | `/sistema-prontuario-eletronico` |
| `modelo-de-prontuario-medico` | 674 | `/sistema-prontuario-eletronico`, `/sistema-para-clinica-medica` |

**Fontes conferidas na rodada:**
- CFM 1.638/2002, art. 5º (itens obrigatórios);
- CFM 1.821/2007 (20 anos no papel, guarda permanente no eletrônico, NGS2);
- CFP 01/2009 com a 05/2010 (itens do registro, 5 anos, acesso integral);
- CFO 91/2009 e Lei 13.787/2018 (20 anos).

**Afirmações sobre o produto conferidas no código:**
- rótulos de psicologia em `utec_pront_rotulos()`, usados pelo prontuário real (`prontuario.php:606`);
- campos extras de psicologia em `Dev.php` (nº da sessão, modalidade, CID);
- exportação com auditoria.

**Formato GEO:** cada artigo responde a pergunta logo no primeiro parágrafo e cita a norma pelo número, e o de prazos traz uma tabela-resumo. É o que mais aumenta a chance de citação em respostas de IA.

**Nenhum artigo afirma certificação SBIS/NGS2.** O artigo de prazos orienta a perguntar ao fornecedor; se o UTecnologia tiver ou buscar certificação, vale citar.

## Publicação em duas etapas

1. Já no ar: `llms.txt` com a landing `chatbot-para-clinicas` (servidor conferido contra o `main` antes).
2. Preparado na branch `seo/bloco3-2026-10-06`, **sem subir**: `sitemap-blog.xml` com os 4 artigos e `llms.txt` com a seção "Guias de prontuário". Sobe só depois de o SQL ser aplicado, para o sitemap não apontar para 404.

## Recomendações novas

- **"Programa para consultório médico/clínica":** incluir o termo "programa" em title, H1 ou FAQ de `sistema-para-consultorio-medico`, `sistema-para-clinica-medica` e `sistema-gratuito-para-clinicas`, em vez de criar landings.
- **Fase 1 de 2026-08-20** (`como-migrar-da-planilha...`, `quanto-custa...`, `software-gratuito...trial-vs-gratuito`): continua sem aplicar e mira termos sem demanda. Sugestão: aplicar só o `software-gratuito-para-clinicas-trial-vs-gratuito` (há demanda para "gratuito"); o de planilha foi superado pelo artigo novo.
- **Próximas pautas** com demanda confirmada: modelo de prontuário odontológico, modelo de prontuário de fisioterapia, ficha de anamnese de fisioterapia/psicológica.
- **Planilha para download:** oferecer um `.xlsx` real do modelo de planilha (há busca por "excel grátis"); hoje o artigo traz a estrutura em tabela.
