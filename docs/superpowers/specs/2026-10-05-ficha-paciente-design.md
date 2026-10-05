# Ficha do paciente (informações adicionais — Entrega A) — Design

Data: 2026-10-05 · Origem: roadmap `docs/produto/2026-10-03-roadmap-aceleracao-utec.md` (onda 1, "Informações adicionais do paciente")
Status: aprovado no brainstorming · Branch: `feat/ficha-paciente` (criada a partir de `feat/rotulos-pacientes`, que também altera `prontuario.php`)

## Objetivo
Uma ficha fixa por paciente com três grupos — **pessoal/responsável**, **saúde básica** e **convênio** — editável
numa tela própria e visível no prontuário, com destaque para alergias.

Já existem em `usuarios` (não duplicar): nome, CPF, RG (`identidade`), `dt_nascimento`, profissão, endereço, telefone, e-mail.

**Entrega B (fora deste escopo):** campos configuráveis por clínica, reaproveitando esta ficha e a "conta"
(raiz da árvore `id_user`) usada pelos rótulos. Também fora: histórico de versões da ficha, ficha no PDF exportado, TISS.

## Decisões
| Tema | Decisão |
|---|---|
| Armazenamento | Tabela 1:1 `pacientes_ficha` (PK `id_paciente`), não colunas em `usuarios` |
| Ver | Níveis 1–4 com `can_access_usuario($id_paciente)`; alvo precisa ser nível 5 |
| Editar pessoal + convênio | Níveis 1–4 |
| Editar saúde | Níveis 1–3; para nível 4 o servidor descarta os campos de saúde do POST e a tela mostra somente leitura |
| Auditoria de saúde | `saude_atualizado_por` / `saude_atualizado_em` gravados só quando algum campo de saúde muda |
| Edição | Tela `adm/ficha/paciente/{id}` (GET formulário, POST salva + redirect com flash) |

## Dados — migração `Dev::migrar_ficha_pacientes` (idempotente, nível 1)
`pacientes_ficha` (InnoDB, utf8mb4):
- `id_paciente INT NOT NULL PRIMARY KEY`
- Pessoal: `nome_social VARCHAR(120) NULL`, `sexo VARCHAR(20) NULL`, `estado_civil VARCHAR(20) NULL`,
  `responsavel_nome VARCHAR(120) NULL`, `responsavel_parentesco VARCHAR(40) NULL`, `responsavel_telefone VARCHAR(20) NULL`,
  `responsavel_cpf VARCHAR(14) NULL`, `emergencia_nome VARCHAR(120) NULL`, `emergencia_parentesco VARCHAR(40) NULL`,
  `emergencia_telefone VARCHAR(20) NULL`
- Saúde: `tipo_sanguineo VARCHAR(3) NULL`, `alergias TEXT NULL`, `medicamentos TEXT NULL`, `comorbidades TEXT NULL`,
  `obs_saude TEXT NULL`, `saude_atualizado_por INT NULL`, `saude_atualizado_em DATETIME NULL`
- Convênio: `convenio_nome VARCHAR(80) NULL`, `convenio_plano VARCHAR(80) NULL`, `convenio_carteirinha VARCHAR(40) NULL`,
  `convenio_validade DATE NULL`
- Controle: `atualizado_por INT NULL`, `atualizado_em DATETIME NULL`

## Opções fixas (chave → rótulo)
- `sexo`: `feminino` Feminino, `masculino` Masculino, `outro` Outro, `nao_informado` Não informado
- `estado_civil`: `solteiro` Solteiro(a), `casado` Casado(a), `uniao_estavel` União estável, `divorciado` Divorciado(a), `viuvo` Viúvo(a), `nao_informado` Não informado
- `tipo_sanguineo`: `A+ A- B+ B- AB+ AB- O+ O-` (chave = rótulo); vazio = não informado
Valor fora da lista → `''` (gravado como NULL).

## Regras de normalização (`utec_ficha_normalizar($post, $pode_editar_saude)`)
- Textos curtos: trim, colapsa espaços, corta no tamanho da coluna (mb). Textos longos (alergias, medicamentos,
  comorbidades, obs_saude): trim, normaliza quebras de linha para `\n`, máx. 2000 caracteres.
- Telefones: só dígitos, 10 ou 11 dígitos; fora disso → `''`.
- `responsavel_cpf`: só dígitos; aceito se passar no dígito verificador (`utec_ficha_cpf_valido`), gravado com 11 dígitos; inválido → `''` e erro de validação "CPF do responsável inválido."
- `convenio_validade`: `AAAA-MM-DD` válido (checkdate) ou `''`.
- `$pode_editar_saude = false` → as chaves de saúde **não aparecem** no array retornado (o model não as toca).
- Retorno: `array('dados' => [...], 'erros' => [...])`. Com erros, nada é gravado e o formulário volta com a mensagem.

## Código
**Helper `application/helpers/ficha_paciente_helper.php`** (puro, testado):
`utec_ficha_opcoes_sexo()`, `utec_ficha_opcoes_estado_civil()`, `utec_ficha_opcoes_tipo_sanguineo()`,
`utec_ficha_campos_saude()`, `utec_ficha_cpf_valido($cpf)`, `utec_ficha_normalizar($post, $pode_editar_saude)`,
`utec_ficha_saude_mudou(array $antes, array $depois)`, `utec_ficha_vazia()` (todas as chaves com `''`),
`utec_ficha_rotulo_opcao($opcoes, $valor)` (→ rótulo ou "Não informado"), `utec_ficha_telefone_fmt($digitos)` (→ "(81) 99999-0000").

**Model `application/models/Ficha_paciente_model.php`** (sem controle de acesso; guardas `table_exists`):
- `disponivel()`
- `obter($id_paciente)` → array com todas as chaves (de `utec_ficha_vazia()` mescladas com a linha do banco; NULL → `''`)
- `salvar($id_paciente, array $dados, $id_usuario, $saude_mudou)` → upsert (`INSERT ... ON DUPLICATE KEY UPDATE` só das chaves recebidas), seta `atualizado_por/em`; se `$saude_mudou`, seta `saude_atualizado_por/em`. Retorna bool.

**Controller `application/controllers/adm/Ficha.php`** (padrão `Horarios.php`: `verSession`, `Padrao_model`, bloqueia nível fora de 1–4):
- `paciente($id)`: valida id > 1, `can_access_usuario`, alvo nível 5 (senão 404). GET → view com ficha, opções, `pode_editar_saude`, flashes. POST → normaliza, se erros → flash `ficha_erro` + redirect para o formulário; senão `saude_mudou` (comparando com `obter()`), `salvar`, flash `ficha_ok`, redirect para `adm/atendimento/prontuario/{id}`.
- `pode_editar_saude()`: nível ∈ {1,2,3}.
- Sem migração: GET mostra aviso "execute adm/dev/migrar_ficha_pacientes"; POST → flash de erro.

**View `application/views/adm/ficha/paciente.php`**: esqueleto das views admin (`horarios/index.php`); 3 painéis; saúde com `readonly`/`disabled` + aviso "Somente estabelecimento e profissional editam dados de saúde" para nível 4; mostra "Última alteração de saúde: {nome} em {data}" quando houver.

**Prontuário (`views/adm/usuarios/new/prontuario.php`)**: busca a ficha via `get_instance()->load->model('Ficha_paciente_model', 'ficha_model')` (mesmo padrão dos rótulos; controllers não mudam):
- no cabeçalho, se `alergias` não vazio: `<div class="ut-ficha-alergia">⚠ Alergias: …</div>` (escapado, até 160 caracteres + "…");
- card "Ficha do paciente" logo após o resumo do paciente, 3 colunas (Pessoal/responsável · Saúde · Convênio), campos vazios "Não informado", nome social exibido junto do nome quando houver;
- botão "Editar ficha" → `adm/ficha/paciente/{id}`; flash `ficha_ok` exibido.
- Sem migração: nada novo aparece.

## Testes (`tests/ficha_paciente_*`, PHP 7.2)
- Helper: opções inválidas → `''`; textos cortados (multibyte); quebras de linha; telefone 10/11 dígitos vs inválido; CPF válido/inválido/repetido (`11111111111`); validade inválida; nível sem permissão perde as chaves de saúde; `saude_mudou` detecta mudança só nos campos de saúde; `telefone_fmt`.
- Fonte: migração (tabela, PK), controller (níveis, `can_access_usuario`, nível 5, descarte de saúde), model (`table_exists`, `ON DUPLICATE KEY UPDATE`), views com os hooks e escape.

## Entrega
`Manual_conteudo.php`: tópico em "Pacientes e cadastro" e menção no "Prontuário" (alergias em destaque). CLAUDE.md: tabela, controller, migração.
Deploy: helper → model → view `ficha/paciente` → controller `Ficha` → `Dev.php` → `Manual_conteudo.php` → `prontuario.php` (por último). Antes, baixar do servidor os existentes e comparar com `main`/branch (rotina registrada na memória). Pasta nova: `application/views/adm/ficha/`.
