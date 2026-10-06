# Planejamento Aceleração UTEC — Andamento

Atualizado em: 06/10/2026
Documento de origem: Google Doc "Planajemento Aceleração UTEC - Marcos e Igor"
Critério de prioridade combinado: menor esforço primeiro (ganhos rápidos antes dos módulos grandes).

Legenda:
✅ Concluído (em produção) · 🟢 Pronto, aguardando publicação · 🔄 Em andamento · ⏳ Próximo / planejado · 💤 Adiado

---

## Resumo

- ✅ Exportar Prontuário (PDF, XLS, CSV) — em produção desde 03/10/2026
- ✅ Rótulos dos pacientes — publicado em 03/10/2026, aguardando validação online
- ✅ Informações adicionais do paciente (Ficha do paciente) — publicado em 05/10/2026, aguardando validação online
- ✅ Tempo médio de espera — publicado em 06/10/2026, aguardando validação online
- ⏳ Próximos da Onda 1: Lista de espera/Encaixe, Gráficos de exames

---

## Atendimento

### Tempo médio de espera — ✅ Concluído
- Desenvolvido, revisado e publicado em 06/10/2026. Falta a validação online (teste em produção) para fechar.
- Como vai funcionar: a recepção marca "Chegou" na agenda (check-in). O sistema registra automaticamente o horário de início e de fim do atendimento quando o profissional muda o status.
- Na agenda, cada paciente mostra algo como: "Chegou 14:05 · esperou 18 min · consulta 32 min".
- Em Relatórios clínicos: espera média, atraso médio (em relação ao horário marcado) e duração média, com filtro por período e por profissional, mais uma tabela por profissional.
- Dá para desfazer um check-in feito por engano (enquanto o atendimento não começou).
- Os horários são registrados tanto pelos botões da agenda quanto pelo formulário do prontuário (iniciar/finalizar/reabrir). Remarcar zera os horários; atendimentos cancelados ficam fora das médias.
- Quem pode marcar a chegada: clínica, profissional e recepção.
- Fica para depois: painel de fila ao vivo e aviso "você é o próximo" pelo WhatsApp.

### Lista de espera / Encaixe — ⏳ Próximo
- O encaixe já funciona hoje: a agenda avisa quando o horário está fora da grade ou ocupado, mas permite marcar.
- Falta a lista de espera: guardar quem quer um horário e avisar quando abrir vaga. Vai usar a busca de horários livres que já existe (a mesma do chatbot) e pode avisar pelo WhatsApp.
- Esforço estimado: médio (1 a 2 semanas).

---

## Prontuário

### Exportar Prontuário — PDF, XLS, CSV — ✅ Concluído
- Em produção desde 03/10/2026, testado online.
- Exporta o prontuário de um paciente por vez, direto da tela do prontuário (botão "Exportar"), em PDF, Excel (XLSX) ou CSV.
- Período opcional (de/até); sem período, sai o histórico completo.
- Conteúdo: dados do paciente, atendimentos (com os campos da especialidade do profissional), exames e a lista de arquivos anexados (só o nome/descrição, não o arquivo em si).
- Quem pode exportar: administrador, clínica e profissional. O colaborador (recepção) não exporta, por sigilo clínico.
- Toda exportação fica registrada (quem exportou, de qual paciente, formato e período), por conta da LGPD.
- Fora desta entrega: exportar vários pacientes de uma vez e assinatura eletrônica no PDF.

### Rótulos dos pacientes — ✅ Concluído
- Publicado em 03/10/2026. Falta a validação online (teste em produção) para fechar.
- Etiquetas coloridas por paciente, para organizar (ex.: VIP, Convênio, Retorno pendente) e para alertar (ex.: Gestante, Alérgico).
- Aparecem no topo do prontuário, na lista de pacientes (com filtro por rótulo ao lado da busca) e, os de alerta, na agenda do dia.
- Cada clínica tem o seu próprio catálogo de rótulos, com 8 cores fixas. O sistema já cria 5 sugestões iniciais.
- Quem gerencia o catálogo: clínica (e o profissional autônomo). Quem aplica nos pacientes: clínica, profissional e colaborador.
- Fora desta entrega: aplicar em vários pacientes de uma vez e regras automáticas (ex.: rótulo "inadimplente").

### Informações adicionais do paciente (Ficha do paciente) — ✅ Concluído
- Desenvolvido, revisado e publicado em 05/10/2026. Falta a validação online (teste em produção) para fechar.
- Ficha com três grupos:
  - Pessoal e responsável: nome social, sexo, estado civil, responsável (nome, parentesco, telefone, CPF) e contato de emergência.
  - Saúde básica: tipo sanguíneo, alergias, medicamentos em uso, comorbidades e observações.
  - Convênio: nome, plano, número da carteirinha e validade.
- Alergias aparecem em destaque no prontuário.
- O colaborador (recepção) vê e edita dados pessoais e de convênio, mas não edita os dados de saúde.
- O sistema registra quem alterou os dados de saúde e quando.
- Próxima etapa (Entrega B): campos configuráveis por clínica (cada clínica cria os seus próprios campos).

### Imagens padrões editáveis e traçáveis — ⏳ Planejado (Onda 2)
- Ideia: um editor de desenho sobre imagens padrão (corpo, arcada dentária etc.), salvando o resultado nos arquivos do paciente.
- Esforço estimado: médio.

### Prescrição — ⏳ Planejado
- Definir modelos e Gerar/Exportar para impressão: Onda 2 (esforço médio), reaproveitando o gerador de PDF já usado no prontuário e no manual.
- Assinatura eletrônica: Onda 3. Exige certificado ICP-Brasil (resolução CFM 2.299/2021). A recomendação é integrar com um serviço pronto (ex.: Memed, VIDaaS) em vez de construir do zero.

### Exames — Gráficos de exames — ⏳ Próximo (Onda 1)
- Os gráficos dependem de os exames terem resultado numérico registrado (ex.: glicose 98 mg/dL). Isso precisa ser definido antes.
- Esforço estimado: médio.

---

## Importação de dados gerais — ⏳ Planejado (Onda 3)
- Começar por um importador de planilha (CSV) com modelo pronto; depois usar IA para entender a planilha que o cliente mandar e mapear as colunas sozinha.
- Comentário: importar pacientes por CSV pode ser antecipado, porque ajuda na venda (cliente vindo de um concorrente).
- Cuidado com a LGPD: são dados sensíveis de saúde.

---

## Financeiro — ⏳ Planejado
- Entradas e saídas + Formas de pagamento: Onda 2 (esforço médio). Hoje não existe; o Mercado Pago atual é só para a assinatura do próprio sistema.
- Gerar orçamento e Estoque (produtos e baixa): Onda 2 (esforço médio).
- Faturamento e Convênios: Onda 3 — envolve o padrão TISS/ANS, esforço grande.
- 💤 Repasse (regras): adiado, como combinado.

---

## Chat Interno — ⏳ Planejado (Onda 2)
- Vai aproveitar a base do sino de avisos que já existe no sistema.
- Esforço estimado: médio.

## Chat suporte — ⏳ Planejado (Onda 2)
- É mais operação do que sistema (precisa de pessoas para atender). Pode começar pelo WhatsApp/chatbot que já temos.

## Relacionamento (WhatsApp)
- O módulo de WhatsApp já é robusto: confirmação de consulta com botões, lembrete automático, chatbot por perfil e remarcação/cancelamento automáticos pelo próprio paciente.
- Próximo passo: conhecer o produto de relacionamento citado no planejamento para decidir como integrar.

## Telemedicina — ⏳ Planejado (Onda 3)
- Videoconferência + link do paciente: usar um serviço pronto embutido (ex.: Jitsi ou Daily), sem servidor próprio. Esforço médio.
- Transcrição de áudio para texto: esforço grande. Precisa de consentimento do paciente (LGPD) e tem custo por uso, que deve entrar no preço do plano.

---

## Histórico de atualizações
- 06/10/2026 — Documento criado com o andamento até aqui (Exportar prontuário, Rótulos, Ficha do paciente, Tempo de espera).
- 06/10/2026 — Ficha do paciente marcada como publicada (05/10); Rótulos e Ficha aguardando validação online; Tempo de espera entrou em desenvolvimento.
- 06/10/2026 — Tempo médio de espera concluído e revisado (check-in, horários de início/fim, médias em Relatórios clínicos); aguardando publicação.
- 06/10/2026 — Tempo médio de espera publicado em produção. Rótulos, Ficha do paciente e Tempo de espera integrados à versão principal do sistema; os três aguardam validação online.
