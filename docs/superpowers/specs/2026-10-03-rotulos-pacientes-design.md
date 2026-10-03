# Rótulos dos pacientes — Design

Data: 2026-10-03 · Origem: roadmap `docs/produto/2026-10-03-roadmap-aceleracao-utec.md` (onda 1)
Status: aprovado no brainstorming

## Objetivo
Etiquetas coloridas por paciente para **organização** (VIP, Convênio, Retorno pendente) e **alertas**
(Gestante, Alérgico) — visíveis na lista de pacientes (com filtro), no cabeçalho do prontuário e, só os de
alerta, na agenda do dia.

Fora de escopo: aplicar em massa, regras automáticas (ex.: inadimplente), relatório por rótulo.

## Decisões
| Tema | Decisão |
|---|---|
| Catálogo | Por clínica = **raiz da árvore** (`id_conta`), com sugestões iniciais |
| Armazenamento | Tabela de catálogo + tabela de vínculo (sem JSON em `usuarios`) |
| Gerenciar catálogo | Nível 1; nível 2; nível 3 só quando é a própria raiz (autônomo) |
| Aplicar/remover | Níveis 1–4, paciente no escopo (`can_access_usuario`), rótulo da mesma raiz do paciente |
| Interação | POST normal + redirect (sem AJAX); painel `<details>` como o Exportar |
| Filtro na lista | No navegador (mesmo padrão do filtro por nome existente, `data-*` na linha) |

## Raiz da árvore (`id_conta`)
A partir de um usuário, sobe por `usuarios.id_user` enquanto o pai existir e tiver nível 2, 3 ou 4; para ao
chegar num nível 2 ou quando não há pai válido. O nó final é a raiz. Nível 1 nunca é raiz (o admin aplica
rótulos usando a raiz do **paciente**). Guarda contra ciclo: no máximo 10 saltos.
Função pura `utec_rotulos_resolver_raiz($id_inicial, callable $buscar)` onde `$buscar($id)` devolve
`array('id'=>, 'nivel'=>, 'id_user'=>)` ou `null` — testável com árvore falsa.

## Dados — migração `Dev::migrar_rotulos_pacientes` (idempotente, nível 1)
`pacientes_rotulos`: `id` PK, `id_conta INT NOT NULL`, `nome VARCHAR(40) NOT NULL`, `cor VARCHAR(20) NOT NULL`,
`alerta TINYINT NOT NULL DEFAULT 0`, `ordem INT NOT NULL DEFAULT 0`, `status TINYINT NOT NULL DEFAULT 1`,
`criado_por INT NULL`, `criado_em DATETIME NOT NULL`; `UNIQUE (id_conta, nome)`, índice `(id_conta, status)`.

`pacientes_rotulos_vinculos`: `id_paciente INT NOT NULL`, `id_rotulo INT NOT NULL`, `aplicado_por INT NULL`,
`aplicado_em DATETIME NOT NULL`; `PRIMARY KEY (id_paciente, id_rotulo)`, índice `(id_rotulo)`.

Sugestões (`garantir_sugestoes`, só se a conta não tem nenhum rótulo): VIP (roxo), Retorno pendente (âmbar),
Convênio (azul), Gestante (rosa, alerta), Alérgico (vermelho, alerta).

## Paleta (chave → hex), fixa
`azul #2563eb`, `verde #16a34a`, `ambar #d97706`, `vermelho #dc2626`, `roxo #7c3aed`, `rosa #db2777`,
`cinza #64748b`, `teal #0d9488`. Cor fora da paleta → `cinza`.

## Código
**Helper `application/helpers/rotulos_helper.php`** (puro, testado):
- `utec_rotulos_paleta()`, `utec_rotulos_cor_valida($cor)` → chave válida ou `'cinza'`
- `utec_rotulos_normalizar_nome($nome)` → trim, colapsa espaços, corta em 40 chars (mb), `''` se vazio
- `utec_rotulos_resolver_raiz($id, callable $buscar)`
- `utec_rotulos_sugestoes()` → lista `[nome, cor, alerta]`
- `utec_rotulos_chips_html(array $rotulos)` → `<span class="ut-rotulo ...">` escapados; alerta com `⚠` e classe `ut-rotulo-alerta`
- `utec_rotulos_ids_validos(array $ids_post, array $ids_catalogo)` → interseção de ints (descarta ids de outra conta)

Regra: as views obtêm os rótulos pelo model (padrão já usado nas views do projeto, que chamam `padrao_model`), para não alterar os controllers que as renderizam.

**Model `application/models/Rotulos_model.php`** (sem controle de acesso; guardas `table_exists`, retorna vazio sem migração):
- `disponivel()` — as 2 tabelas existem
- `conta_raiz($id_usuario)` — usa o helper com busca em `usuarios`
- `catalogo($id_conta, $so_ativos = true)`, `garantir_sugestoes($id_conta, $id_usuario)`
- `salvar_rotulo($id_conta, $id_rotulo, $nome, $cor, $alerta, $id_usuario)` → `['ok'=>bool,'erro'=>string]` (nome vazio / duplicado)
- `alternar_status($id_conta, $id_rotulo)`
- `rotulos_do_paciente($id_paciente, $so_ativos = true)`
- `rotulos_de_pacientes(array $ids)` → `[id_paciente => [rotulos]]` em uma consulta (lista e agenda)
- `definir_rotulos_paciente($id_paciente, array $ids_rotulo, $id_usuario)` — em transação: apaga vínculos do paciente com rótulos da **mesma conta** e insere os novos

**Controller `application/controllers/adm/Rotulos.php`** (padrão dos controllers admin: `verSession`, `Padrao_model`):
- `index` — catálogo da raiz do logado (nível 1 escolhe a conta via `?conta=ID`; sem conta, mostra aviso). 403 para quem não gerencia.
- `salvar` (POST) — cria/edita; flash de sucesso/erro; redirect para `adm/rotulos`.
- `status/{id}` (POST) — ativa/desativa (só rótulo da própria conta).
- `paciente/{id}` (POST) — nível 1–4 + `can_access_usuario`; ids filtrados por `utec_rotulos_ids_validos` contra o catálogo da raiz do **paciente**; redirect para `HTTP_REFERER` do próprio site ou `adm/atendimento/prontuario/{id}`.

**View `application/views/adm/rotulos/index.php`** — lista com chip de pré-visualização, form (nome, cor como radio de bolinhas, checkbox "Alerta"), botão Ativar/Desativar.

### Pontos de exibição
- **Prontuário** (`views/adm/usuarios/new/prontuario.php`, cabeçalho do paciente): chips + botão "Rótulos" com `<details>` e checkboxes dos rótulos ativos da raiz do paciente; link "Gerenciar rótulos" para quem gerencia. A view busca os dados via `get_instance()->load->model('Rotulos_model')` (os dois controllers que renderizam essa view continuam sem mudança). Sem migração → bloco não aparece.
- **Lista de usuários** (`views/adm/usuarios/new/lista.php`, renderizada por `Usuarios::Index()` e `Usuarios::rel($nivel)`): para as linhas com `$u->nivel == 5`, chips abaixo do nome e `data-rotulos=",3,7,"` na `.ul-report-row`; um `<select>` "Todos os rótulos" ao lado da busca (só aparece se há rótulos na página) filtra as linhas no navegador. A view coleta os ids de pacientes da página e chama `rotulos_de_pacientes($ids)` via `get_instance()->load->model('Rotulos_model')` — sem mudança em `Usuarios.php`.
- **Agenda** (`views/adm/usuarios/new/atendimentos.php`, desktop + cartões mobile): só rótulos com `alerta = 1`, ícone ⚠ + nome, ao lado de `paciente_nome`. A view coleta os `id_paciente` de `$qr_agendamentos` e chama `rotulos_de_pacientes($ids)` pelo mesmo mecanismo — sem mudança em `Atendimento.php`.
- **CSS**: classes `ut-rotulo`, `ut-rotulo-alerta` definidas uma vez (bloco `<style>` curto em cada view que usa, mesmo padrão inline das views `new/`).

## Erros
- Nome vazio ou duplicado na mesma conta → flash de erro, nada gravado.
- Rótulo de outra conta enviado no POST → descartado silenciosamente por `utec_rotulos_ids_validos`.
- Sem migração → telas como hoje; `adm/rotulos` mostra aviso "execute adm/dev/migrar_rotulos_pacientes".

## Testes (`tests/rotulos_*`, PHP 7.2, padrão `tests/disponibilidade_*`)
- Helper: paleta/cor inválida, normalização de nome (espaços, 41+ chars, multibyte), raiz (paciente→colab→prestador→estab; autônomo; órfão; ciclo), ids válidos, chips escapam `<script>` e marcam alerta.
- Fonte: migração (2 tabelas, UNIQUE, PK composta), guardas de nível no controller, `can_access_usuario` no `paciente()`, `table_exists` no model, views com os hooks.

## Entrega
`Manual_conteudo.php`: tópico em "Pacientes e cadastro" (níveis 2/3/4) e menção na Agenda. CLAUDE.md: tabelas, controller, migração.
Deploy: helper → model → view `rotulos/index` → controller `Rotulos` → `Dev.php` → `Manual_conteudo.php` → views `lista`, `atendimentos`, `prontuario` (por último). **Antes de subir, baixar do servidor os arquivos existentes e comparar com `main`** (lição do deploy da exportação). Depois rodar a migração e testar como níveis 2, 3 e 4.
