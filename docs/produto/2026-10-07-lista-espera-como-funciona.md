# Lista de espera — Como funciona

Data: 07/10/2026 · Status: definido, em desenvolvimento
Parte do Planejamento Aceleração UTEC (Onda 1 — Atendimento: "Lista de espera / Encaixe")

---

## Para que serve

Quando a agenda de um profissional está cheia, a clínica costuma anotar num papel ou no WhatsApp quem "quer um horário antes, se abrir". Na prática essa lista se perde, e a vaga de um cancelamento fica vazia.

A Lista de espera resolve isso:
- a recepção registra no sistema quem quer um horário;
- quando um paciente cancela ou remarca, o sistema **avisa a equipe na hora** que abriu uma vaga;
- o aviso já mostra **quem da lista combina com aquele horário**;
- com um clique a recepção encaixa o paciente — e a agenda fica cheia de novo.

Resultado esperado: menos horários vazios, mais consultas por dia e paciente atendido mais cedo.

---

## Como funciona, passo a passo

### 1. Colocar um paciente na lista
Em **Agenda → Lista de espera → Adicionar à lista**:
- **Paciente** (obrigatório)
- **Profissional** (obrigatório) — ou "Qualquer profissional", se o paciente aceita ser atendido por quem tiver vaga
- **Preferências (opcionais):**
  - Turno: manhã, tarde, noite ou tanto faz
  - Dias da semana em que pode vir
  - "A partir de" (ex.: só pode depois do dia 15)
  - Observação livre

Quem não preencher as preferências entra como "tanto faz" e aparece para qualquer vaga daquele profissional.

### 2. Abriu uma vaga — o sistema avisa
Uma vaga abre quando uma consulta futura é **cancelada** ou **remarcada** (o horário antigo fica livre). Vale para todos os caminhos:
- cancelamento ou remarcação feitos pela recepção na agenda ou no calendário;
- paciente que toca em "Cancelar" na mensagem de confirmação do WhatsApp;
- paciente que cancela ou remarca sozinho pelo chatbot do WhatsApp.

Se houver alguém na lista para aquele profissional, aparece no **sino de avisos**:
> "Abriu vaga com Dr. Carlos em 12/10 às 14:00. 3 pacientes na lista de espera."

Recebem o aviso: **o profissional da vaga, o responsável pela clínica e a recepção (colaboradores)**.

O sistema não avisa quando o horário já passou, quando não há ninguém na lista, ou quando o mesmo cancelamento chega duas vezes.

### 3. Escolher quem encaixar
Ao clicar no aviso, abre a vaga com a lista de pacientes:
- **primeiro os compatíveis** (profissional, turno, dia e data batem com a vaga), marcados com o selo "Compatível";
- dentro de cada grupo, **quem espera há mais tempo aparece antes**;
- cada paciente mostra telefone (com link para o WhatsApp), preferências e há quantos dias espera.

A recepção liga ou chama o paciente para confirmar se ele quer o horário.

### 4. Encaixar
No paciente que aceitou, clique em **Agendar**: abre o formulário de agendamento de sempre, **já preenchido** com paciente, profissional, data e hora — inclusive com a opção "Enviar confirmação pelo WhatsApp". Ao salvar:
- a consulta é criada normalmente;
- o paciente sai da lista e vai para a aba **Agendados** (o histórico fica guardado).

Se outra pessoa da equipe já preencheu a vaga, a tela mostra **"Vaga já preenchida"**.

### 5. Tirar alguém da lista
Botão **Remover**, informando o motivo: desistiu, conseguiu horário por outro meio, ou outro. O paciente vai para a aba **Removidos**. Ninguém sai da lista sozinho por prazo.

---

## Onde aparece no sistema
- **Agenda → Lista de espera:** abas Aguardando, Agendados e Removidos, com o quadro "Como funciona" na própria tela.
- **Sino de avisos:** os avisos de vaga aberta.
- **Prontuário do paciente:** selo "Na lista de espera desde 01/10".

## Quem pode usar
| Perfil | O que faz |
|---|---|
| Estabelecimento (clínica) | Tudo: adiciona, edita, remove, recebe avisos e encaixa |
| Profissional | Tudo, dentro da própria clínica; recebe os avisos das vagas dele |
| Colaborador (recepção) | Tudo: é quem mais usa — recebe os avisos e faz o encaixe |
| Paciente | Não acessa a lista |

Cada clínica só vê a própria lista.

---

## O que fica para depois
- **Aviso automático ao paciente pelo WhatsApp** ("Abriu uma vaga, quer?" com botões, o primeiro que aceitar leva). Depende de aprovar um modelo de mensagem novo na Meta — é a evolução natural desta entrega.
- **Prioridade / urgência** na lista.
- **Botão "Ver vagas"** a qualquer momento, cruzando os horários livres com a lista.

## Tutorial para quem usa o sistema
- Quadro **"Como funciona"** dentro da tela da Lista de espera.
- Capítulo novo **"Lista de espera"** no Manual de ajuda (tela e PDF), com print da tela.
- Junto com esta entrega, os prints do manual serão atualizados com as novidades recentes (botão "Chegou", rótulos, ficha do paciente, exportar prontuário).
