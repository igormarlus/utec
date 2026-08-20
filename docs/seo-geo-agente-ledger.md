# Ledger do agente SEO/GEO — UTecnologia Saúde

Última atualização: 2026-08-20 (execução manual de validação — ver `docs/seo-geo-agente-relatorio-2026-08-20.md`)

Rodízio (passo 2 do `SKILL.md`): último bloco coberto = **1. Especialidades sem landing**. Próxima execução deve cobrir o bloco **2. Concorrentes**.

---

## 1. Keywords testadas

| Termo | Data | Resultado | Reteste sugerido |
|---|---|---|---|
| `sistema para ginecologia`, `sistema para pediatria`, `sistema para psiquiatras`, `sistema fonoaudiologia` | 2026-07-14 | Demanda confirmada — landings criadas | — (já coberto) |
| `feegow sistema`, `sistema odontoclinic`, `sistema shosp`, `clinica nas nuvens sistema` | 2026-06-02 | Demanda forte confirmada — landings/comparativos parciais criados | 2026-09-14 (retestar) |
| `amplimed`, `belasis`, `ninsaude` (sistema/preço/alternativa) | 2026-07-14 | `sistema X`/`X preço` com sinal; `alternativa X`/`X vs` sem sinal | 2026-09-14 (retestar intenção comparativa) |
| `ivix`, `docway`, `meupaciente` | 2026-07-14 | Sem sinal de autocomplete | 2026-10-14 |
| `sistema de faturamento para clinica`, `controle financeiro para clinica pequena`, `gestao de estoque para clinica odontologica`, `relatorio de atendimentos por profissional` | 2026-07-14 | Sem sinal — não são a forma como o usuário busca | 2026-10-14 |
| `sistema/software para`: dermatologia, cardiologia, ortopedia, otorrinolaringologia, cirurgia plástica, endocrinologia, geriatria, homeopatia, acupuntura, urologia, medicina estética, oncologia, pneumologia, reumatologia, gastroenterologia, neurologia | 2026-08-20 | Sem sinal relevante (vazio ou eco trivial do termo) | 2026-09-17 |
| `sistema para terapia ocupacional` | 2026-08-20 | Sinal encontrado, mas é falso positivo — sugestões são sobre "sistema vestibular/proprioceptivo/sensorial" (conceitos clínicos de TO), não sobre software | 2026-09-17 |
| `sistema para medicina do trabalho`, `software para medicina do trabalho`, `sistema para clinica de medicina do trabalho`, `software para medicina e segurança do trabalho` | 2026-08-20 | **Demanda forte confirmada** — sugestões incluem marcas concorrentes (SOC, ESO, Senior) | Landing criada — ver seção 2 |
| `sistema para clinica ocupacional` | 2026-08-20 | Sinal fraco (só eco do termo) | 2026-09-17 |
| `infectologia`, `alergologia e imunologia` | — | **Não testado ainda** (lote interrompido por timeout) | Próxima execução do bloco 1 |
| `software para clinica ocupacional`, `software exame admissional`, `sistema esocial medicina do trabalho` | — | **Não testado ainda** (lote interrompido por timeout) | Próxima execução do bloco 1 ou 3 |

## 2. Páginas e artigos existentes

### Landing pages (`application/views/public/seo/`)

sistema-para-clinicas, sistema-para-clinica-medica, sistema-prontuario-eletronico, sistema-para-psicologos, sistema-para-dentistas, software-para-clinicas-odontologicas, sistema-para-consultorio-medico, sistema-para-clinica-de-fisioterapia, alternativa-feegow, alternativa-odontoclinic, sistema-gratuito-para-clinicas, software-para-clinicas, sistema-para-clinica-oftalmologica, software-para-medicos, sistema-para-nutricionistas, sistema-para-ginecologia, sistema-para-pediatria, sistema-para-psiquiatria, sistema-para-fonoaudiologia, **sistema-para-medicina-do-trabalho (novo, 2026-08-20)**.

Especialidades da tabela `usuarios_especialidades` (42 total) ainda sem landing após esta execução: Acupuntura, Alergologia e Imunologia (não testado), Cardiologia, Cirurgia Cardiovascular (não testado), Cirurgia Geral (não testado), Cirurgia Plástica, Dermatologia, Endocrinologia e Metabologia, Gastroenterologia, Geriatria, Hematologia (não testado), Homeopatia, Infectologia (não testado), Medicina de Família e Comunidade (não testado), Medicina do Esporte (não testado), Medicina Estética, Medicina Intensiva (não testado), Medicina Legal (não testado), Nefrologia (não testado), Neurologia, Neurocirurgia (não testado), Oncologia, Ortopedia e Traumatologia, Otorrinolaringologia, Pneumologia, Proctologia (não testado), Radiologia e Diagnóstico por Imagem (não testado), Reumatologia, Terapia Ocupacional (falso positivo — sem demanda real de software), Urologia, Vascular e Angiologia (não testado).

### Artigos de blog

Publicados no banco (`docs/blog-posts-seed.sql`): "Gestão de clínica médica: 7 erros...", "LGPD para clínicas...", + demais do seed original (ver arquivo para lista completa).

Gerados como `.sql` pendente de aplicação:
- `docs/seo-geo-agente-blog-2026-08-20.sql` → "Sistema para clínica de medicina do trabalho: o que avaliar antes de contratar" (slug `sistema-para-clinica-de-medicina-do-trabalho`) — **pendente de aplicação**
- `docs/seo-geo-agente-blog-2026-08-20.sql` → "Software médico: como escolher para consultório ou clínica" (slug `software-medico-como-escolher-consultorio-clinica`) — **pendente de aplicação**

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
