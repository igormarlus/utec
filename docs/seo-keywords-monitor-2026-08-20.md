# Monitor de Keywords SEO — UTecnologia (2026-08-20)

> **Nota de infraestrutura:** este relatório não foi commitado automaticamente pelo routine cloud "SEO Keywords Monitor". A rotina rodou com sucesso (achados abaixo confirmados por ela), mas o `git push` do sandbox falhou com HTTP 403 ("Resource not accessible by integration") — um problema de permissão do GitHub App usado pelas rotinas Claude, separado do bloqueio de rede que afetava as 4 execuções anteriores (05/08 ×2, 10/08, 17/08). Esse relatório foi reconstruído e publicado manualmente numa sessão local do Claude Code, reproduzindo/completando a mesma pesquisa. **Ação pendente:** revisar a permissão de escrita do GitHub App para `igormarlus/utec` em claude.ai (conexões/integrações), senão os próximos runs semanais (toda segunda) vão continuar deixando o commit preso no sandbox.
>
> **Método usado (mudou nesta rodada):** o endpoint de autocomplete do Google (`google.com/complete/search`) está bloqueado pela política de egress dos sandboxes cloud desde 05/08. A partir desta rodada, o método passou a usar a tool `WebSearch` (busca live, não autocomplete) e a inferir sinal de demanda/concorrência pelos resultados retornados (títulos, snippets, domínios) — funcionou sem bloqueio. O routine já foi atualizado com essa instrução para as próximas execuções automáticas.

## Resumo executivo

Houve achado acionável esta semana. **ByDoctor** (bydoctor.com.br) emergiu como um concorrente indireto agressivo em conteúdo: está publicando páginas de comparação "X vs ByDoctor" contra pelo menos Feegow, Amplimed e "sistema para clínica médica" genérico — um padrão de SEO que a UTecnologia ainda não pratica. Além disso, **Clínica nas Nuvens** ganhou uma página de comparação de terceiros ("Clínica nas Nuvens vs. Clínica Completa") e **Shosp foi adquirida pela Afya** (grupo educacional/saúde de capital aberto), o que pode significar mais investimento em marketing desse concorrente à frente. Uma correção de cadastro: **Belasis não é concorrente de gestão clínica** — é um sistema para negócios de beleza (salões/estética), deve sair do watchlist. Em especialidades não atendidas, **Dermatologia** é a que tem sinal de demanda mais forte e validado (6 concorrentes diretos com página dedicada), candidata a nova landing seguindo o mesmo racional das 4 páginas criadas em jul/2026.

## Bloco 1 — Concorrentes

| Concorrente | Sinal encontrado | Novidade vs. rodada anterior (jul/2026) |
|---|---|---|
| **Feegow** | Forte, com conteúdo de comparação estabelecido (Capterra, ByDoctor) | Sem mudança — já mapeado |
| **Odontoclinic** | Resultados de busca dominados pela franquia/marca (empreenda.odontoclinic.com.br), pouco sinal do sistema de gestão em si | Sem mudança relevante |
| **Shosp** | Forte — sistema estabelecido, listado em Capterra/GetApp, comparações genéricas em inglês (SoftwareWorld) | **Novo:** [Afya anunciou aquisição da Shosp por R$ 5,98 milhões](https://medicinasa.com.br/afya-shosp/) — concorrente agora está sob grupo maior, potencial de mais investimento |
| **Clínica nas Nuvens** | Forte, com conteúdo próprio extenso | **Novo:** página de comparação de terceiro — ["Clínica nas Nuvens vs. Clínica Completa: Qual é o Melhor Sistema em 2025?"](https://clinicacompleta.com.br) publicada pelo concorrente Clínica Completa |
| **Amplimed** | Forte, com variação local confirmada anteriormente | **Novo:** [ByDoctor vs Amplimed: qual escolher em 2026?](https://bydoctor.com.br/vs/amplimed) |
| **Belasis** | Sinal de marca forte, mas **é sistema para negócios de beleza (salões/esmalteria), não gestão clínica de saúde** | **Correção:** remover do watchlist de concorrentes — cadastro anterior estava equivocado |
| **Ninsaude** | Forte, sem mudança | Sem sinal novo de "alternativa"/"vs" |

**Achado transversal novo:** **ByDoctor** (bydoctor.com.br) aparece de forma recorrente construindo páginas "X vs ByDoctor" — já confirmado contra Feegow e Amplimed, e também aparece em "sistema para clínica medica preço" com uma página `/alternativas/sistema-para-clinica-medica`. Não estava no radar do plano original (que rastreava Feegow, Odontoclinic, Shosp, Clínica nas Nuvens + Amplimed/Belasis/Ninsaude adicionados em jul/2026). Vale considerar ByDoctor como concorrente ativo a monitorar, dado o padrão agressivo de conteúdo comparativo.

## Bloco 2 — Variantes de landing pages

| Página / termo central | Sinal novo encontrado |
|---|---|
| sistema para clinicas / clinica medica | Nenhum concorrente novo; landscape dominado por Amplimed, TOTVS, iClinic, ByDoctor (ver Bloco 1) |
| sistema de prontuario eletronico | Nenhuma novidade; termo genérico, resultados de blogs educacionais (ProDoctor, Pixeon, Amigo Tech) |
| sistema para psicologos | Mercado consolidado em torno de **PsicoManager** e **Allminds** — nenhum dos dois estava no watchlist; ambos com presença forte de conteúdo |
| sistema para dentistas / software para clinica odontologica | Mercado dominado por **Simples Dental** ("maior software odontológico da América Latina") e **Clinicorp** — nenhum dos dois monitorado atualmente; vale avaliar se merecem entrar no watchlist de concorrentes, já que a página `/sistema-para-dentistas` da UTecnologia compete diretamente com eles |
| sistema para consultorio medico | Resultados majoritariamente de softwares hispanoamericanos/europeus (Medesk, Nimbo, Cegid) — pouca relevância direta para o mercado brasileiro |
| sistema para clinica de fisioterapia | Concorrentes: EffiClin, Sistema Clínica Total, Ninsaúde — nenhuma novidade |
| sistema para clinica oftalmologica | Concorrentes: Eyecare BI (especializado), Ninsaúde — nenhuma novidade |
| software para medicos | Termo muito genérico/internacional (Medesk, Huli, Flowww) — baixo valor de SEO direcionado |
| sistema para nutricionistas | Concorrentes: Mapple, DietoPro, Medesk — nenhuma novidade |
| sistema para ginecologia | Concorrentes recorrentes: iClinic, Amplimed, GestãoDS, HiDoctor, Clínica nas Nuvens — nenhuma novidade |
| sistema para pediatria | Concorrentes: iClinic, Ninsaúde, ProDoctor, Versatilis — nenhuma novidade |
| sistema para psiquiatria | Resultados dominados por players internacionais/espanhóis (Medilink, Carepatron, Nubimed) — mercado nacional menos consolidado, oportunidade não mudou |
| sistema para fonoaudiologia | Concorrentes: Clínica Ágil (30 mil profissionais, forte), SinapSYS, VBCOM, ProDoctor — nenhuma novidade |

**Leitura geral do Bloco 2:** nenhuma mudança que exija ajuste imediato de conteúdo nas 15 páginas já publicadas. O achado mais relevante é indireto: **Simples Dental**, **Clinicorp**, **PsicoManager** e **Allminds** são concorrentes de nicho fortes que não estão no watchlist formal (que hoje só rastreia sistemas multi-especialidade genéricos) — avaliar se vale rastrear especificamente para as páginas de dentistas e psicólogos.

## Bloco 3 — Especialidades não atendidas

| Especialidade | Sinal de demanda/concorrência |
|---|---|
| **Dermatologia** | **Forte** — 6 concorrentes com página dedicada: iClinic, Feegow, Clínica nas Nuvens, Versatilis, GestãoDS, Amplimed. Existe até player especializado (dermaDuo, "100% para clínicas dermatológicas e de estética") |
| **Cirurgia Plástica** | Moderado — GestãoDS e Ninsaúde têm conteúdo dedicado, mas resultados diluídos com tecnologia cirúrgica (não-software) |
| **Cardiologia** | Moderado — Ninsaúde, ClinicWeb e iClinic têm páginas, mas mercado se mistura com software especializado de laudo (ecocardiografia), não gestão clínica geral |
| **Urologia** | Sem sinal — busca dominada por equipamento cirúrgico (KARL STORZ, robótica da Vinci), nenhum resultado de sistema de gestão |
| **Ortopedia** | Sem sinal — busca dominada por dispositivos ortopédicos/próteses, nenhum resultado de sistema de gestão |
| **Endocrinologia** | Sem sinal — busca dominada por conteúdo anatômico/educacional sobre o sistema endócrino, nenhum resultado de software |

## Recomendação

1. **Maior prioridade:** avaliar criação de `/sistema-para-dermatologia` — é a especialidade não atendida com sinal mais forte e validado (mesmo padrão que justificou ginecologia/pediatria/psiquiatria/fono em jul/2026: múltiplos concorrentes diretos já disputando o termo).
2. **Corrigir watchlist:** remover Belasis (não é concorrente — sistema para negócios de beleza) e considerar adicionar **ByDoctor** (construindo conteúdo comparativo agressivo contra múltiplos concorrentes) ao Bloco 1 das próximas rodadas.
3. **Sem ação de conteúdo** necessária nas 15 landing pages já publicadas — nenhuma mudança de padrão de busca detectada no Bloco 2.
4. **Infraestrutura:** revisar permissão de push do GitHub App para o routine cloud (ver nota no topo), senão relatórios futuros continuarão presos em sandbox e exigindo reconstrução manual como esta.
