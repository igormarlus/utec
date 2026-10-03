# Roadmap — Aceleração UTEC (Marcos e Igor)

Fonte: Google Doc "Planajemento Aceleração UTEC - Marcos e Igor"
(`1LkUBYF2gxZ5ltzzjBjIJWn5QeuqQ-Mz_0SRnIrwOkO0`). Data: 2026-10-03.

**Critério de priorização:** menor esforço primeiro (decisão de Igor em 2026-10-03).
Esforço: **P** ≈ dias · **M** ≈ 1–2 semanas · **G** > 2 semanas e/ou dependência regulatória/externa.

## Onda 1 — ganhos rápidos (reaproveitam o que já existe)

| Demanda | Base existente | Esforço | Agente |
|---|---|---|---|
| Exportar prontuário PDF / XLS / CSV | mPDF (`application/libraries/M_pdf.php`, mesmo padrão do manual PDF); dados em `agendamentos` | P | agente-clinico |
| Rótulos dos pacientes | — (tabela nova `pacientes_rotulos` + filtro na lista) | P | agente-clinico + agente-frontend |
| Informações adicionais do paciente | padrão de campos configuráveis de `especialidades_campos_config` / JSON | P | agente-clinico |
| Tempo médio de espera | precisa registrar chegada (check-in) em `agendamentos` | P/M | agente-clinico |
| Lista de espera / Encaixe | encaixe já permitido (`Disponibilidade_model::verificar_horario`); lista de espera nova usando `proximos_livres()` | M | agente-clinico (+ agente-whatsapp p/ avisar vaga) |
| Gráficos de exames | Chart.js já usado em `adm/marketing`; **depende** de exames terem resultado numérico | M | agente-clinico + agente-frontend |

**Primeira entrega sugerida:** exportar prontuário (PDF/CSV).

## Onda 2 — módulos novos de porte médio

| Demanda | Observação | Esforço | Agente |
|---|---|---|---|
| Prescrição: modelos + gerar/imprimir | tabela de modelos + mPDF | M | agente-clinico |
| Imagens padrão editáveis/traçáveis | editor canvas (ex.: fabric.js) salvando em `pacientes_arquivos` | M | agente-frontend + agente-clinico |
| Financeiro: entradas/saídas + formas de pagamento | inexistente (Mercado Pago atual é só da assinatura SaaS) | M | orquestrador → agente-clinico / agente-saas-billing |
| Orçamento / Estoque (produtos e baixa) | inexistente | M | agente-clinico |
| Chat interno | base no sino de avisos (`Notificacoes_model`); polling (host compartilhado, sem websocket) | M | agente-frontend |
| Chat suporte | é operação (precisa de pessoas); pode começar pelo WhatsApp/chatbot existente | P | agente-produto |
| Relacionamento (WhatsApp) | módulo já robusto (confirmação, lembrete, chatbot). Mapear o produto do Igor antes de integrar | ? | agente-whatsapp + agente-produto |

## Onda 3 — grandes / regulatórias / externas

| Demanda | Risco | Esforço |
|---|---|---|
| Importação de dados (pacientes, prontuários, agenda, financeiro) | começar por importador CSV com template; IA (Claude API) para mapear colunas depois. LGPD: dado sensível de saúde. *Importar pacientes via CSV pode subir para a onda 1, porque ajuda na venda (migração de concorrente).* | M→G |
| Prescrição: assinatura eletrônica | exige ICP-Brasil (CFM 2.299/2021) — integrar Memed/VIDaaS em vez de construir | G |
| Faturamento + Convênios | TISS/ANS | G |
| Telemedicina: vídeo + link do paciente | embed Jitsi/Daily, sem servidor próprio | M |
| Telemedicina: transcrição de áudio | speech-to-text, consentimento LGPD, custo por plano | G |
| Repasse (regras) | adiado por consenso | backlog |

## Premissas transversais
- Respeitar limites de plano (`produtos.max_*`) quando o recurso for diferencial de plano.
- LGPD em tudo que envolve dado clínico (importação, transcrição, exportação).
- Toda entrega visível ao usuário atualiza `Manual_conteudo.php` (CLAUDE.md §19).
- Pipeline por demanda: orquestrador → brainstorming (spec) → writing-plans → subagent-driven-development + TDD → migração em `adm/Dev.php` → code-review → verification → deploy pelo agente-dev-infra.
