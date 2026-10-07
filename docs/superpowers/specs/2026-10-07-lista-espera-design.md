# Lista de espera — Design

Data: 2026-10-07 · Origem: roadmap `docs/produto/2026-10-03-roadmap-aceleracao-utec.md` (onda 1, "Lista de espera / Encaixe")
Status: aprovado no brainstorming · Doc de negócio: `docs/produto/2026-10-07-lista-espera-como-funciona.md`

## Objetivo
Guardar pacientes que querem um horário antes do disponível e, quando uma vaga abre (cancelamento ou
remarcação), avisar a equipe no sino com os pacientes compatíveis — a recepção liga/chama e encaixa com o
formulário de agendamento já preenchido.

Fora de escopo: aviso automático ao paciente por WhatsApp (entrega futura, exige template Meta), prioridade/urgência,
botão "Ver vagas" a qualquer momento, expiração automática de entradas, acesso do paciente (nível 5).

## Decisões
| Tema | Decisão |
|---|---|
| Quem avisa o paciente | A recepção (sistema só avisa a equipe) |
| Entrada | Paciente + profissional obrigatórios ("qualquer profissional" = `id_prestador` NULL); turno, dias da semana, "a partir de" e observação opcionais (vazio = tanto faz) |
| Gatilhos | Cancelamento e remarcação, nos 5 pontos (abaixo) |
| Destinatários do aviso | Profissional da vaga + estabelecimento (nível 2 raiz) + colaboradores (nível 4) da conta |
| Encaixe | Botão "Agendar" abre o formulário atual (`adm/atendimento/novo/{id_paciente}`) pré-preenchido; ao salvar, a entrada vira `agendado` |
| Saída manual | "Remover" com motivo (`desistiu` / `conseguiu_horario` / `outro`) |
| Disparo | Imediato, por library central chamada após a gravação; nunca bloqueia cancelamento/remarcação |
| Conta | Mesma raiz da árvore usada pelos rótulos (`Rotulos_model::conta_raiz`) |

## Dados — migração `Dev::migrar_lista_espera` (idempotente, nível 1)
`lista_espera` (InnoDB, utf8mb4):
- `id` INT PK AI, `id_conta INT NOT NULL`, `id_paciente INT NOT NULL`, `id_prestador INT NULL` (NULL = qualquer)
- `turno VARCHAR(10) NOT NULL DEFAULT ''` (`''` | `manha` | `tarde` | `noite`)
- `dias_semana VARCHAR(20) NOT NULL DEFAULT ''` (CSV de 0..6, 0 = domingo; `''` = qualquer)
- `a_partir_de DATE NULL`, `observacao VARCHAR(500) NULL`
- `status VARCHAR(12) NOT NULL DEFAULT 'aguardando'` (`aguardando` | `agendado` | `removido`)
- `motivo_saida VARCHAR(20) NULL`, `id_agendamento INT NULL`
- `criado_por INT NULL`, `criado_em DATETIME NOT NULL`, `atualizado_por INT NULL`, `atualizado_em DATETIME NULL`
- Índices: `(id_conta, status)`, `(id_paciente, status)`, `(id_prestador, status)`

`lista_espera_vagas` (registro de cada vaga aberta; dá id estável ao aviso e deduplica eventos repetidos):
- `id` INT PK AI, `id_conta INT NOT NULL`, `id_prestador INT NOT NULL`, `data_agenda DATE NOT NULL`, `hora_agenda VARCHAR(5) NOT NULL`
- `origem VARCHAR(30) NOT NULL` (`agenda_cancelar` | `agenda_remarcar` | `whatsapp_cancelar` | `chatbot_cancelar` | `chatbot_remarcar`)
- `id_agendamento_origem INT NOT NULL`, `criado_em DATETIME NOT NULL`
- `UNIQUE (id_agendamento_origem, data_agenda, hora_agenda)` — o mesmo agendamento liberando o mesmo horário avisa uma vez só

Duplicidade de entrada: não pode haver 2 linhas `aguardando` com o mesmo `id_paciente` + `id_prestador` (NULL conta como valor).
Validado no model (SELECT antes do INSERT) — sem UNIQUE no banco por causa do NULL e do histórico.

## Funções puras — `application/helpers/lista_espera_helper.php` (testadas)
- `utec_le_turnos()` → `['' => 'Tanto faz', 'manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite']`
- `utec_le_turno_da_hora($hora)` → `manha` (< 12:00), `tarde` (12:00–17:59), `noite` (≥ 18:00); hora inválida → `''`
- `utec_le_dias_normalizar($dias)` (array ou CSV) → CSV ordenado, sem repetição, só 0..6
- `utec_le_motivos_saida()` → `desistiu` Desistiu · `conseguiu_horario` Conseguiu horário · `outro` Outro
- `utec_le_normalizar($post)` → `['dados' => [...], 'erros' => [...]]`: paciente obrigatório (int > 0); prestador int > 0 ou NULL;
  turno fora da lista → `''`; `a_partir_de` AAAA-MM-DD válida ou NULL; observação trim, máx. 500 (mb)
- `utec_le_compativel($entrada, $vaga)` → bool. `$vaga` = `id_prestador`, `data_agenda`, `hora_agenda`. Casa quando:
  prestador NULL ou igual; turno `''` ou igual a `utec_le_turno_da_hora`; dias `''` ou contém `date('w', data)`;
  `a_partir_de` NULL ou ≤ data da vaga
- `utec_le_ordenar($entradas, $vaga)` → compatíveis primeiro; dentro de cada grupo, `criado_em` mais antigo primeiro
- `utec_le_deve_avisar($vaga, $agora, $qtd_aguardando)` → false se data/hora inválida, vaga ≤ agora ou `$qtd_aguardando` = 0
- `utec_le_dias_espera($criado_em, $hoje)` → int dias
- `utec_le_resumo_preferencias($entrada)` → "Tarde · Seg, Qua · a partir de 15/10" (vazio → "Sem preferência")
- `utec_le_mensagem_aviso($prestador_nome, $data, $hora, $qtd)` → "Abriu vaga com {nome} em dd/mm às HH:MM. N paciente(s) na lista de espera."

## Model — `application/models/Lista_espera_model.php`
Sem controle de acesso (quem chama impõe escopo); guardas `table_exists`.
- `disponivel()` — as 2 tabelas existem
- `listar($id_conta, $status)` — com nome/telefone do paciente e nome do profissional
- `buscar($id)`; `adicionar($dados, $id_usuario)` → `['ok', 'erro', 'id']` (erro "Paciente já está na lista de espera deste profissional.")
- `atualizar($id, $dados, $id_usuario)`; `remover($id, $motivo, $id_usuario)` (só se `aguardando`)
- `marcar_agendado($id, $id_paciente, $id_agendamento, $id_usuario)` — só se `aguardando` e mesmo paciente
- `aguardando_do_paciente($id_paciente)` — para o selo no prontuário
- `contar_aguardando_para($id_conta, $id_prestador)` — prestador igual ou NULL
- `registrar_vaga($vaga)` → id da vaga (INSERT IGNORE; se já existia, retorna 0 = não avisar de novo)
- `buscar_vaga($id_vaga)`; `horario_ocupado($id_prestador, $data, $hora)`; `aguardando_para($id_conta, $id_prestador)`
- `destinatarios_conta($id_conta, $id_prestador)` → ids únicos: prestador + nível 2 + nível 4 da árvore da conta. Desce só por nós de equipe (níveis 2–4, até 5 camadas) para não varrer pacientes

## Library — `application/libraries/Lista_espera_vagas.php`
(Nome diferente do controller `adm/Lista_espera` para não colidir a classe no CI.)
`vaga_do_agendamento($id_agendamento, $origem)` lê prestador/data/hora do agendamento e chama:
`vaga_aberta($id_prestador, $data, $hora, $origem, $id_agendamento_origem)`:
1. Model indisponível → return. Tudo em `try/catch (Throwable)` → `log_message('error', '[lista_espera] ...')`, nunca relança.
2. `id_conta = conta_raiz($id_prestador)`; `qtd = contar_aguardando_para(...)`; `utec_le_deve_avisar` falso → return.
3. Vaga ainda livre: `Lista_espera_model::horario_ocupado()` (agendamento do prestador na data e `LEFT(hora_agenda,5)` com `status IN (0,1,2)`). Não usa `verificar_horario()`, que responde `livre` para profissional sem grade.
4. `registrar_vaga` → 0 → return (evento repetido).
5. Para cada destinatário: INSERT IGNORE em `notificacoes_usuarios` com `tipo = 'lista_espera_vaga'`, `id_whatsapp_notificacao = id_vaga`
   (a chave única `(usuario, id_whatsapp_notificacao, tipo)` deduplica), `id_agendamento = id_agendamento_origem`,
   título "Abriu vaga — lista de espera", mensagem de `utec_le_mensagem_aviso`, `url = 'adm/lista_espera/vaga/{id_vaga}'`.
   Método novo `Notificacoes_model::criar_aviso_lista_espera($id_vaga, $destinatarios, $titulo, $mensagem, $url, $id_agendamento, $tenant_id)`.

## Pontos de disparo (chamam `vaga_aberta` só após a gravação bem-sucedida)
| Ponto | Arquivo | Vaga |
|---|---|---|
| Cancelar na agenda | `Atendimento::cancelar_agenda()` | data/hora do agendamento (ler a linha antes do UPDATE) |
| Remarcar na agenda/calendário | `Atendimento::remarcar_agenda()` | data/hora **antigas** (ler antes do UPDATE); só se mudou |
| Botão Cancelar do WhatsApp | `Webhooks::processar_resposta_agendamento()` | só quando `processado = true` e ação = cancelar; data/hora do contexto |
| Cancelar pelo chatbot | `Whatsapp_chatbot_agenda` (após `cancelar_agendamento_chatbot` ok) | data/hora do agendamento |
| Remarcar pelo chatbot | `Whatsapp_chatbot_agenda` (após `remarcar_agendamento_chatbot` ok) | data/hora antigas do `$agendamento` |

## Controller — `application/controllers/adm/Lista_espera.php` (rota padrão CI `adm/lista_espera`)
Níveis 1–4 (senão 403). Atalho: `adm/lista_espera?paciente={id}` pré-seleciona o paciente (botão no prontuário). Pacientes e prestadores sempre validados com `can_access_usuario`. Conta do logado via
`conta_raiz`; nível 1 usa a conta do paciente/prestador. Sem migração → tela com aviso, sem formulário.
- `index()` — abas Aguardando / Agendados / Removidos (`?aba=`), formulário "Adicionar" (busca de paciente igual à da agenda, select de prestadores visíveis + "Qualquer profissional"), quadro "Como funciona" (`<details>`).
- `salvar()` (POST) — adicionar ou editar (`id`); flash `le_ok` / `le_erro`; redirect para `voltar` se casar com `#^adm/[a-z0-9_/?=&-]*$#i`, senão `adm/lista_espera`.
- `remover($id)` (POST) — motivo obrigatório da lista.
- `vaga($id_vaga)` — cartão da vaga (profissional, data, hora); se `horario_ocupado` → "Vaga já preenchida" e sem botões; senão lista aguardando ordenada por `utec_le_ordenar`, com selo "Compatível". Botão "Agendar" → `adm/atendimento/novo/{id_paciente}?prestador=&data=&hora=&lista_espera={id}`. Vaga de outra conta → 403.

## Encaixe — `Atendimento::novo()` e `cadastrar()`
- `novo()`: lê `prestador`, `data`, `hora`, `lista_espera` da query string (validados: int, AAAA-MM-DD, HH:MM) e passa à view `adm/atendimento/atendimento.php`, que pré-preenche os campos e inclui `<input type="hidden" name="id_lista_espera">`.
- `cadastrar()`: após o INSERT ok, se `id_lista_espera` > 0 e o model está disponível → `marcar_agendado(id, id_paciente, agendamento_id, id_logado)`. Falha aqui não desfaz o agendamento (só log).

## Prontuário
`views/adm/usuarios/new/prontuario.php`: se `aguardando_do_paciente` retorna linhas → selo "Na lista de espera desde dd/mm" com link `adm/lista_espera`. Obtido pelo model na view (mesmo padrão dos rótulos).

## Menu
Item "Lista de espera" em `includes/adm/menu.php`, junto de Horários/Rótulos (níveis 1–4).

## Erros e degradação
- Sem as tabelas: tela avisa "Execute a migração adm/dev/migrar_lista_espera"; os 5 pontos não disparam; `novo/cadastrar` ignoram `id_lista_espera`.
- Qualquer exceção na library: log `[lista_espera]`, fluxo de cancelamento/remarcação segue.

## Testes
- `tests/lista_espera_helper_test.php` — turno da hora (limites 11:59/12:00/17:59/18:00), dias, normalização, compatibilidade (cada critério isolado + combinações), ordenação, `deve_avisar` (passado, agora, sem fila), resumo, mensagem, dias de espera.
- `tests/lista_espera_source_test.php` — estático: os 5 pontos chamam `vaga_aberta`; remarcar lê a data antiga antes do UPDATE; `cadastrar` chama `marcar_agendado`; o webhook só chama com `processado`.

## Documentação e conteúdo
- `Manual_conteudo.php`: capítulo novo "Lista de espera" (níveis 2, 3, 4) com passo a passo; `print` `lista-espera.png`.
- Prints novos em `imagens/manual/` (local, dados de teste): `lista-espera.png`, e regravar `agenda.png`, `prontuario.png`, `pacientes-cadastro.png` (hoje sem Chegou/rótulos/ficha/exportar).
- `Funcionalidades_conteudo.php`: item "Lista de espera".
- `docs/produto/evolucao-30-dias-google-doc.md`: status do item; CLAUDE.md (tabelas, controller, migração).
