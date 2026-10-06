# Ledger do agente SEO/GEO — UTecnologia Saúde

Última atualização: 2026-10-06 (ciclo semanal, bloco 2 — ver `docs/seo-geo-agente-relatorio-2026-10-06.md`)

Rodízio (passo 2 do `SKILL.md`): último bloco coberto = **2. Concorrentes** (2026-10-06, incluindo o espaço chatbot/automação de WhatsApp). Próxima execução deve cobrir o bloco **3. Variações semânticas e GEO**.

Histórico do rodízio: bloco 1 em 2026-08-20 · bloco 2 em 2026-10-06.

---

## 1. Keywords testadas

| Termo | Data | Resultado | Reteste sugerido |
|---|---|---|---|
| `sistema para ginecologia`, `sistema para pediatria`, `sistema para psiquiatras`, `sistema fonoaudiologia` | 2026-07-14 | Demanda confirmada — landings criadas | — (já coberto) |
| `feegow`, `odontoclinic`, `shosp`, `clinica nas nuvens`, `amplimed`, `belasis`, `ninsaude` (marca, `alternativa X`, `X vs`, `X preço`) | 2026-10-06 | Marca: só navegacional (login, entrar, agenda, app). `alternativa X` e `X vs`: **vazio para todos** (2º reteste seguido). `X preço`: sinal em feegow ("feegow clinic preço") e ninsaude; amplimed/shosp/clinica nas nuvens só eco. Extras: "clinica nas nuvens reclame aqui", "shosp afya", "ninsaude codigo tuss" | 2027-01-06 |
| `iclinic`, `clinicorp`, `simples dental`, `prodoctor`, `doctoralia sistema`, `medplus sistema` (concorrentes novos) | 2026-10-06 | Marca forte só navegacional (login/entrar/agenda). `alternativa X`: vazio. Preço: "iclinic planos e preços", "afya iclinic preço", "simples dental valor/planos". doctoralia/medplus sistema: eco trivial | 2027-01-06 |
| `melhor sistema para clinica (medica/odontologica)`, `melhores sistemas para clinicas medicas`, `melhor sistema para consultorio medico`, `melhor software para clinica medica`, `melhor sistema para psicologos` | 2026-10-06 | **Demanda confirmada** (intenção comparativa genérica). Não criado: canibaliza o artigo pendente `software-medico-como-escolher-consultorio-clinica` e o `software-clinica-odontologica-como-escolher` — ver recomendação no relatório | 2027-01-06 |
| `comparativo sistemas para clinica`, `sistema para clinica barato` | 2026-10-06 | Vazio / eco trivial | 2027-01-06 |
| `chatbot para clinica(s)`, `chatbot para clinicas medicas/odontologicas`, `chatbot whatsapp para clinicas`, `chatbot para consultorio (medico/odontologico)`, `chatbot com ia para clinicas` | 2026-10-06 | **Demanda forte confirmada** — landing `chatbot-para-clinicas` criada (ver seção 2) | 2027-01-06 |
| `chatbot para psicologo` | 2026-10-06 | Falso positivo — sugestões são "chatbot psicólogo gratuito/online" (bot que faz papel de psicólogo), não ferramenta para o consultório | 2027-01-06 |
| `ia para clinicas`, `agente ia para clinicas`, `secretaria ia para clinicas`, `atendimento ia para clinicas` | 2026-10-06 | **Demanda forte**, mas o produto não tem IA no atendimento — só recomendação (decisão de produto) | 2027-01-06 |
| `agendamento pelo whatsapp` (+ automatico/online), `bot de agendamento whatsapp` | 2026-10-06 | Sinal real, mas o chatbot ainda não marca consulta nova — só recomendação; parte informativa já coberta pelo artigo `sistema-de-agendamento-com-whatsapp-o-que-da-para-automatizar` | quando o chatbot marcar consulta nova |
| `automação whatsapp para clinica`, `atendente virtual para clinica`, `secretaria virtual para consultorio` | 2026-10-06 | Vazio / eco trivial | 2027-01-06 |
| `ivix`, `docway`, `meupaciente` | 2026-07-14 | Sem sinal de autocomplete | 2026-10-14 |
| `sistema de faturamento para clinica`, `controle financeiro para clinica pequena`, `gestao de estoque para clinica odontologica`, `relatorio de atendimentos por profissional` | 2026-07-14 | Sem sinal — não são a forma como o usuário busca | 2026-10-14 |
| `sistema/software para`: dermatologia, cardiologia, ortopedia, otorrinolaringologia, cirurgia plástica, endocrinologia, geriatria, homeopatia, acupuntura, urologia, medicina estética, oncologia, pneumologia, reumatologia, gastroenterologia, neurologia | 2026-08-20 | Sem sinal relevante (vazio ou eco trivial do termo) | 2026-09-17 |
| `sistema para terapia ocupacional` | 2026-08-20 | Sinal encontrado, mas é falso positivo — sugestões são sobre "sistema vestibular/proprioceptivo/sensorial" (conceitos clínicos de TO), não sobre software | 2026-09-17 |
| `sistema para medicina do trabalho`, `software para medicina do trabalho`, `sistema para clinica de medicina do trabalho`, `software para medicina e segurança do trabalho` | 2026-08-20 | **Demanda forte confirmada** — sugestões incluem marcas concorrentes (SOC, ESO, Senior) | Landing criada — ver seção 2 |
| `sistema para clinica ocupacional` | 2026-08-20 | Sinal fraco (só eco do termo) | 2026-09-17 |
| `confirmação de consulta por whatsapp`, `mensagem de confirmação de consulta`, `lembrete de consulta`, `mensagem de lembrete de consulta`, `sistema de agendamento com whatsapp`, `whatsapp para clínicas`, `mensagem de confirmação de consulta odontológica` | 2026-08-31 | **Demanda confirmada** — clusters informacionais fortes (mensagem/lembrete/modelo) + intenção de ferramenta (`sistema de agendamento com whatsapp` + grátis/via/integrado). Landing + 7 artigos criados — ver seção 2. Sem sinal: "reduzir faltas", "no-show", "disparo de whatsapp", "confirmação de consulta automática" (usados só no corpo) | 2026-11-30 |
| `infectologia`, `alergologia e imunologia` | — | **Não testado ainda** (lote interrompido por timeout) | Próxima execução do bloco 1 |
| `software para clinica ocupacional`, `software exame admissional`, `sistema esocial medicina do trabalho` | — | **Não testado ainda** (lote interrompido por timeout) | Próxima execução do bloco 1 ou 3 |

## 2. Páginas e artigos existentes

### Landing pages (`application/views/public/seo/`)

sistema-para-clinicas, sistema-para-clinica-medica, sistema-prontuario-eletronico, sistema-para-psicologos, sistema-para-dentistas, software-para-clinicas-odontologicas, sistema-para-consultorio-medico, sistema-para-clinica-de-fisioterapia, alternativa-feegow, alternativa-odontoclinic, sistema-gratuito-para-clinicas, software-para-clinicas, sistema-para-clinica-oftalmologica, software-para-medicos, sistema-para-nutricionistas, sistema-para-ginecologia, sistema-para-pediatria, sistema-para-psiquiatria, sistema-para-fonoaudiologia, **sistema-para-medicina-do-trabalho (novo, 2026-08-20)**, **confirmacao-de-consulta-por-whatsapp (novo, 2026-08-31 — frente "WhatsApp para clínicas")**, alternativa-shosp, alternativa-clinica-nas-nuvens, casos-de-uso (2026-09-23), **chatbot-para-clinicas (novo, 2026-10-06 — rota, método `Home::seo_chatbot_para_clinicas()` e sitemap.xml; pendente de commit/deploy)**.

Especialidades da tabela `usuarios_especialidades` (42 total) ainda sem landing após esta execução: Acupuntura, Alergologia e Imunologia (não testado), Cardiologia, Cirurgia Cardiovascular (não testado), Cirurgia Geral (não testado), Cirurgia Plástica, Dermatologia, Endocrinologia e Metabologia, Gastroenterologia, Geriatria, Hematologia (não testado), Homeopatia, Infectologia (não testado), Medicina de Família e Comunidade (não testado), Medicina do Esporte (não testado), Medicina Estética, Medicina Intensiva (não testado), Medicina Legal (não testado), Nefrologia (não testado), Neurologia, Neurocirurgia (não testado), Oncologia, Ortopedia e Traumatologia, Otorrinolaringologia, Pneumologia, Proctologia (não testado), Radiologia e Diagnóstico por Imagem (não testado), Reumatologia, Terapia Ocupacional (falso positivo — sem demanda real de software), Urologia, Vascular e Angiologia (não testado).

### Artigos de blog

Publicados no banco (`docs/blog-posts-seed.sql`): "Gestão de clínica médica: 7 erros...", "LGPD para clínicas...", + demais do seed original (ver arquivo para lista completa).

Gerados como `.sql` pendente de aplicação:
- `docs/seo-geo-agente-blog-2026-08-20.sql` → "Sistema para clínica de medicina do trabalho: o que avaliar antes de contratar" (slug `sistema-para-clinica-de-medicina-do-trabalho`) — **pendente de aplicação**
- `docs/seo-geo-agente-blog-2026-08-20.sql` → "Software médico: como escolher para consultório ou clínica" (slug `software-medico-como-escolher-consultorio-clinica`) — **pendente de aplicação**
- `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql` → 7 artigos do cluster "confirmação/lembrete por WhatsApp" (slugs: `modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp`, `mensagem-de-lembrete-de-consulta-quando-enviar`, `como-fazer-mensagem-de-confirmacao-de-consulta-no-whatsapp`, `confirmacao-de-consulta-manual-ou-automatica`, `como-reduzir-faltas-de-pacientes-no-consultorio`, `o-que-fazer-quando-paciente-nao-confirma-consulta`, `mensagem-de-confirmacao-de-consulta-odontologica`) — **aplicado em produção 2026-09-02; landing + menu + rodapé + sitemaps no ar, sitemaps reenviados ao Google**
- `docs/seo-geo-blog-chatbot-whatsapp-2026-09-23.sql` → 5 artigos do cluster chatbot/automação WhatsApp (slugs: `chatbot-para-clinica-whatsapp-o-que-faz-e-o-que-nao-faz`, `sistema-de-agendamento-com-whatsapp-o-que-da-para-automatizar`, `enviar-mensagem-para-paciente-whatsapp-lgpd`, `paciente-remarcar-consulta-pelo-whatsapp`, `agenda-do-dia-pelo-whatsapp-para-medicos-e-clinicas`) — no `sitemap-blog.xml` segundo a auditoria de 2026-09-24 (inventário acrescentado em 2026-10-06)
- `docs/seo-geo-agente-blog-fase-1-2026-08-20.sql` → `como-migrar-da-planilha-para-sistema-clinico`, `quanto-custa-um-software-para-clinica`, `software-gratuito-para-clinicas-trial-vs-gratuito` — status de aplicação não registrado; conferir

## 3. Descartes (avaliado e rejeitado)

| Item | Motivo | Data |
|---|---|---|
| `/alternativa-amplimed`, `/alternativa-belasis`, `/alternativa-ninsaude` | Sem sinal de intenção comparativa ("alternativa"/"vs") no autocomplete — conteúdo especulativo | 2026-07-14 |
| Landing dedicada para Estética | Fora do escopo atual do produto — risco de dispersão do posicionamento saúde/clínica | 2026-06-02 |
| Landing dedicada para Veterinária | Fluxo clínico e comercial diferente — risco de página incoerente com o produto real | 2026-06-02 |
| Landing dedicada para Laboratório de análises clínicas | Necessidades muito específicas de software de laboratório | 2026-06-02 |
| Artigos/landings para keywords funcionais (faturamento, controle financeiro, estoque, relatório por profissional) | Sem sinal de autocomplete — ninguém busca o sistema por essas frases exatas | 2026-07-14 |
| Landing para Dermatologia, Cardiologia, Ortopedia, Otorrinolaringologia, Cirurgia Plástica, Endocrinologia, Geriatria, Homeopatia, Acupuntura, Urologia, Medicina Estética, Oncologia, Pneumologia, Reumatologia, Gastroenterologia, Neurologia | Sem sinal relevante de demanda por software específico da especialidade — clínicas dessas áreas parecem buscar pelo termo genérico ("sistema para clínica médica"), não por especialidade | 2026-08-20 |
| Landing para Terapia Ocupacional | Sinal de autocomplete é falso positivo (termos clínicos de TO, não software) | 2026-08-20 |
| `/alternativa-iclinic`, `/alternativa-clinicorp`, `/alternativa-simples-dental` e novos `/alternativa-amplimed`, `/alternativa-belasis`, `/alternativa-ninsaude` | 2º reteste sem nenhum sinal de "alternativa"/"vs" — buscas de concorrente são navegacionais (login). Reabrir só com sinal novo | 2026-10-06 |
| Landing "chatbot para psicólogos" | Falso positivo (bot que substitui psicólogo) e conflito ético com o posicionamento | 2026-10-06 |
| Landing/artigo de "automação WhatsApp" e "atendente/secretária virtual" | Sem sinal de autocomplete | 2026-10-06 |

> **Feito em 2026-10-06:** espaço chatbot/automação de WhatsApp auditado (landing `chatbot-para-clinicas` criada); os 2 artigos da "próxima leva" já tinham saído em 2026-09-23.
>
> **Para a próxima rodada (bloco 3 — variações semânticas e GEO):** testar `programa para clinica/consultorio`, `sistema para clinica gratis/gratuito`, `sistema para clinica na nuvem/web`, `como migrar de planilha`, `quanto custa sistema para clinica`, além dos itens ainda não testados `software para clinica ocupacional`, `software exame admissional`, `sistema esocial medicina do trabalho`.

> **Feito em 2026-10-06 (vitrine de funcionalidades):** catálogo único com 16 funcionalidades (fonte: Funcionalidades_conteudo) exibido em /experimentar, /assinar, home e nas landings de prontuário eletrônico, sistema e software para clínicas, consultório médico e casos de uso; 4 FAQ novas (exportar prontuário, alergias, tempo de espera, remarcação pelo WhatsApp); llms.txt com seção Funcionalidades. Próximo: Entrega 2 (pesquisa de palavras-chave → 2 landings novas + artigos).
