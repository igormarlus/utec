# Exportar prontuário (PDF, CSV, XLSX) — Design

Data: 2026-10-03 · Origem: roadmap `docs/produto/2026-10-03-roadmap-aceleracao-utec.md` (onda 1)
Status: aprovado no brainstorming

## Objetivo
Permitir que o profissional/clínica exporte o prontuário de **um paciente** — para entregar ao
paciente (pedido LGPD, segunda via, encaminhamento) ou analisar em planilha — em **PDF, CSV e XLSX**,
com filtro opcional de período.

Fora de escopo: exportação em massa, embutir os arquivos anexados, assinatura eletrônica no PDF.

## Decisões
| Tema | Decisão |
|---|---|
| Granularidade | Um paciente por vez, a partir da tela do prontuário |
| Conteúdo | Dados do paciente + atendimentos + exames + lista de arquivos (só metadados) |
| Período | `de`/`ate` opcionais; vazios = histórico inteiro |
| Quem exporta | Níveis 1, 2, 3, dentro do escopo. Nível 4 **não** (sigilo clínico) |
| Auditoria | Toda exportação registrada em `prontuario_exportacoes` |
| Formatos | PDF (mPDF), CSV (`;` + UTF-8 BOM), XLSX (gerador próprio, sem dependência) |

## Arquitetura

### Rota / controller
`Atendimento::exportar_prontuario($id_paciente, $formato)` — mesmo controller da tela ativa
(`adm/atendimento/prontuario/{id}`), para reaproveitar a mesma regra de escopo dos agendamentos.
URL: `adm/atendimento/exportar_prontuario/{id}/{pdf|csv|xlsx}?de=AAAA-MM-DD&ate=AAAA-MM-DD`.

Ordem de validação (antes de qualquer consulta pesada):
1. `$id_paciente` int > 1; formato ∈ {pdf, csv, xlsx}, senão 404.
2. Nível do logado ∈ {1,2,3}, senão 403.
3. `padrao_model->can_access_usuario($id_paciente)`, senão 403.
4. Formato `xlsx` sem `class_exists('ZipArchive')` → 404 (o item também some do menu).
5. Datas: aceitas só se `/^\d{4}-\d{2}-\d{2}$/`; inválidas viram vazio; se `de > ate`, troca.

### Coleta — `application/models/Prontuario_export_model.php`
`coletar($id_paciente, $de, $ate, $usuario_logado)` retorna array:
- `paciente`: `id, nome, telefone, email, dt_cadastro` (campos ausentes → `''`).
- `atendimentos`: agendamentos de `id_paciente` com **o mesmo filtro de escopo** de
  `Atendimento::prontuario()` (nível ≠ 1: `id_user|id_paciente|id_prestador IN scope`), no período,
  ordem `data_agenda ASC, hora_agenda ASC`. Cada item: data, hora, status (texto), profissional,
  especialidade (nome), `rotulos` da especialidade do prestador, os 3 campos de texto e
  `extras` = lista `[rótulo, valor]` montada de `campos_extras` (JSON) × `especialidades_campos_config`
  (se a tabela existir; chave sem config usa a própria chave como rótulo).
- `exames`: mesma consulta de `Atendimento::exames()` (`usuarios_exames_atendimento` + joins), restrita
  aos atendimentos coletados.
- `arquivos`: `pacientes_arquivos` do paciente no período (descrição, tipo, data) — sem caminho/arquivo.
- `meta`: período aplicado, gerado_por (nome), gerado_em.

O model não faz controle de acesso (padrão `Disponibilidade_model`): o controller garante.

### Rótulos por especialidade — refactor pontual
O `switch` de rótulos hoje mora em `application/views/adm/usuarios/new/prontuario.php` (linhas ~450–543).
Extrair para `utec_pront_rotulos($especialidade_id)` em `application/helpers/prontuario_export_helper.php`,
retornando o mesmo array `$lbl` (labels + placeholders). A view passa a chamar o helper — saída idêntica.
Assim tela e exportação nunca divergem.

### Funções puras — `application/helpers/prontuario_export_helper.php`
- `utec_pront_rotulos($esp_id)` — acima.
- `utec_pront_status_texto($status)` — 0 Agendado, 1 Em atendimento, 2 Finalizado, 3 Cancelado
  (conferir com os rótulos usados na agenda durante a implementação).
- `utec_pront_linhas_tabulares($dados)` → `['Atendimentos' => [cabecalho, ...linhas], 'Exames' => [...]]`.
  Colunas Atendimentos: Data, Hora, Status, Profissional, Especialidade, Atendimento inicial,
  Avaliação, Reavaliação (nomes genéricos dos campos do banco — um paciente pode ter várias
  especialidades; os rótulos específicos aparecem só no PDF), Campos extras ("Rótulo: valor | ..."). Colunas Exames: Data, Exame, Status, Profissional, Observação.
- `utec_pront_celula_segura($v)` — prefixa `'` quando a célula começa com `= + - @` ou TAB/CR (CSV/XLSX injection).
- `utec_pront_csv($linhas)` — `;`, aspas duplas escapadas, `\r\n`, BOM UTF-8. CSV leva só a aba
  Atendimentos seguida de linha em branco e a aba Exames com seu cabeçalho.
- `utec_pront_nome_arquivo($id, $ext, $data)` → `prontuario-{id}-{AAAAMMDD}.{ext}` (sem nome do paciente).

### XLSX — `application/libraries/Xlsx_simples.php`
Gerador mínimo: `adicionar_aba($nome, $linhas)`, `gerar()` → string binária.
Monta `[Content_Types].xml`, `_rels/.rels`, `xl/workbook.xml`, `xl/_rels/workbook.xml.rels`,
`xl/styles.xml` (1 estilo negrito p/ cabeçalho), `xl/worksheets/sheetN.xml` com
`inlineStr` (sem sharedStrings), escapando XML e removendo caracteres de controle inválidos.
Usa `ZipArchive` em arquivo temporário (`sys_get_temp_dir()`), lê e apaga.

### PDF — `application/views/adm/usuarios/prontuario_pdf.php`
Padrão de `Usuarios::manual_pdf()`: `error_reporting(0)` até depois do `Output()`, `M_pdf`,
fonte DejaVu (cache já commitado). Conteúdo: cabeçalho com nome do estabelecimento/profissional
logado e paciente; bloco de dados do paciente; um bloco por atendimento (data, profissional,
especialidade, 3 campos com rótulos, extras); tabela de exames; lista de arquivos.
Header: "Prontuário — {paciente}". Footer: "Documento confidencial · gerado por {usuário} em {data} ·
página {PAGENO} de {nb}". Todo texto passa por `htmlspecialchars`; quebras de linha → `<br>`.
Saída `'D'` (download) com `utec_pront_nome_arquivo()`.

### Auditoria — tabela `prontuario_exportacoes`
`id, id_usuario, id_paciente, tenant_id (NULL), formato VARCHAR(8), periodo_de DATE NULL,
periodo_ate DATE NULL, ip_hash CHAR(64), criado_em DATETIME`, índices em `id_paciente` e `id_usuario`.
Migração idempotente `Dev::migrar_prontuario_exportacoes` (nível 1). Gravação antes de enviar o arquivo,
guardada por `table_exists` (sem a tabela, exporta mesmo assim). `ip_hash = hash('sha256', ip . encryption_key)`.

### Interface
Na tela do prontuário (bloco de cabeçalho do paciente, ao lado de "Novo agendamento"), visível só para
níveis 1–3: botão **Exportar** (dropdown Bootstrap 4) com campos `de`/`ate` (type=date) e três links
PDF / CSV / XLSX (XLSX oculto sem ZipArchive). Os links montam a URL via JS com as datas; sem JS,
exportam o histórico inteiro.

## Erros
- Sem atendimentos no período: exporta mesmo assim, com aviso "Nenhum atendimento no período".
- Falha do mPDF / ZipArchive: `log_message('error', ...)` + `show_error` genérico 500, sem expor caminho.

## Testes (`tests/prontuario_export_*`, padrão `tests/disponibilidade_*`)
- `utec_pront_csv`: BOM, `;`, aspas, quebras de linha dentro de célula, injeção de fórmula.
- `utec_pront_linhas_tabulares`: cabeçalhos, extras concatenados, período vazio.
- `utec_pront_rotulos`: padrão + 2 especialidades (igual ao switch original).
- `Xlsx_simples`: zip abre, contém as partes obrigatórias, XML de cada aba é bem-formado, texto escapado.
- `utec_pront_nome_arquivo`.
- Rodar com PHP 7.2 (`C:\PHP\PHP7.2`) — mPDF v6 não roda em PHP 8.

## Verificação manual
Local, logado como nível 3 e nível 4: nível 3 baixa os 3 formatos (abrir CSV no Excel, XLSX no Excel/Sheets,
PDF com acentos); nível 4 recebe 403 e não vê o botão; filtro de período reduz linhas; registro em
`prontuario_exportacoes`.

## Entrega
`Manual_conteudo.php`: tópico "Exportar prontuário" no capítulo de prontuário (níveis 2 e 3).
Deploy pelo `agente-dev-infra`: helper, model, library, view PDF, view prontuário, `Atendimento.php`, `Dev.php`;
depois rodar `adm/dev/migrar_prontuario_exportacoes`. Healthcheck padrão.
