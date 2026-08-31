# Relatorio de Usuarios em Linhas Responsivas — Design

**Data:** 2026-08-31
**Status:** Aprovado para planejamento
**Escopo:** Ajustar `adm/usuarios` e `adm/usuarios/rel/{nivel}` para um formato de relatorio em linhas responsivas, com atividade visivel, acoes no inicio, indicadores operacionais/comerciais e avatar sem foto usando icone
**Stack:** PHP 7 + CodeIgniter 3 + Bootstrap 4 + jQuery

## 1. Objetivo

A listagem atual de usuarios em `application/views/adm/usuarios/new/lista.php` usa cards compactos. Isso funciona razoavelmente no celular, mas perde leitura operacional no desktop, onde a equipe precisa comparar registros com mais rapidez. O objetivo desta mudanca e transformar a tela em uma lista com cara de relatorio, sem depender de `<table>`, preservando boa responsividade e os atalhos atuais, especialmente o contato por WhatsApp.

O resultado esperado e uma tela que:

- fique ampla e escaneavel no desktop
- continue pratica no celular, com botoes grandes o suficiente para toque
- mostre a atividade do usuario com mais clareza
- resuma volume operacional e contexto comercial quando isso fizer sentido
- deixe as acoes principais no inicio de cada linha
- pare de exibir imagem de template quando nao houver foto cadastrada

## 2. Fora de escopo

- Alteracoes no formulario de cadastro ou edicao de usuarios
- Mudanca de regras de permissao, escopo ou links das acoes
- Migracao para DataTables, Vue, React ou qualquer outra tecnologia
- Reestruturacao ampla do controller `Usuarios.php` fora da consulta de listagem

## 3. Decisoes de design

| Aspecto | Decisao |
|---|---|
| Estrutura visual | Lista em `div`s com grid responsivo, simulando colunas de relatorio |
| Experiencia desktop | Linhas horizontais com colunas estaveis e maior area de leitura |
| Experiencia mobile | Quebra em blocos verticais mantendo acoes no topo da linha |
| Acoes | Botoes com texto visivel no inicio da linha |
| Atividade | Prestador prioriza `especialidade`; demais niveis usam `profissao` |
| Indicadores | Mostrar mais detalhes para admin, clinicas e profissionais; versao reduzida para atendentes |
| Status comercial | Exibir se o usuario esta em plano pago, trial ou sem assinatura ativa quando houver contexto SaaS |
| Avatar sem foto | Renderizar icone de usuario em SVG, sem imagem placeholder externa |
| Busca | Manter filtro atual por nome, adaptado para as novas linhas |

## 4. Arquitetura da mudanca

### 4.1 Controller `application/controllers/adm/Usuarios.php`

As actions `Index()` e `rel($nivel)` deixam de consultar apenas `usuarios.*` e passam a trazer tambem o nome da especialidade, quando existir:

```sql
SELECT u.*, ue.nome AS especialidade_nome
FROM usuarios u
LEFT JOIN usuarios_especialidades ue ON ue.id = u.especialidade
...
```

Isso evita regra de negocio espalhada na view e permite calcular a atividade com previsibilidade.

Regras da consulta:

- Se a tabela `usuarios_especialidades` existir, usar `LEFT JOIN`
- Se nao existir, manter a consulta funcional sem quebrar a listagem
- Preservar o filtro por escopo ja aplicado hoje para admin e demais niveis

### 4.2 View `application/views/adm/usuarios/new/lista.php`

A view sai do modelo de cards em grade (`.ul-grid` / `.ul-card`) e passa para uma lista vertical de linhas (`.ul-report-list` / `.ul-report-row`).

Cada linha deve expor as seguintes areas:

`Acoes` | `Usuario` | `Atividade` | `Indicadores` | `Contato` | `Cadastro` | `Status`

Comportamento:

- `Acoes`: primeiro bloco da linha, contendo `Prontuario` quando nivel 5, `Editar`, `Remover`, `Acessar`, `Ativar/Desativar` e `WhatsApp` quando houver telefone
- `Usuario`: avatar, nome, id e login quando aplicavel
- `Atividade`: valor resolvido conforme regra da secao 5
- `Indicadores`: bloco com marcacoes, pacientes e situacao comercial, conforme regra da secao 6
- `Contato`: telefone clicavel para WhatsApp, e-mail quando existir
- `Cadastro`: data de cadastro formatada
- `Status`: pill visual de ativo/inativo

No desktop, essas areas ficam alinhadas em uma unica linha. No mobile, quebram em duas ou mais faixas internas, mas cada registro continua sendo um unico bloco visual.

## 5. Regra de exibicao da atividade

Definicao da atividade mostrada em cada registro:

1. Se `nivel == 3` e houver `especialidade_nome`, mostrar `especialidade_nome`
2. Se `nivel == 3` e nao houver `especialidade_nome`, usar `profissao`
3. Para os demais niveis, usar `profissao`
4. Se nenhum dos campos estiver preenchido, mostrar `Nao informado`

Essa regra deixa a tela mais aderente ao uso clinico, onde o papel operacional do prestador costuma ser identificado pela especialidade.

## 6. Indicadores operacionais e comerciais

Os indicadores adicionais devem aparecer com foco nos niveis operacionais:

- **Nivel 1 - Admin:** pode ver a versao completa do relatorio para todos os registros listados
- **Nivel 2 - Clinica / Estabelecimento:** deve ver indicadores completos do proprio registro e dos profissionais vinculados
- **Nivel 3 - Profissional:** deve ver indicadores completos do proprio registro
- **Nivel 4 - Atendente / Colaborador:** deve ver somente uma versao reduzida, sem excesso de dados comerciais
- **Nivel 5 - Paciente:** nao precisa receber esse bloco expandido

### 6.1 Campos propostos no bloco `Indicadores`

Para admin, clinicas e profissionais:

- `Marcacoes`: quantidade de agendamentos vinculados ao usuario
- `Pacientes`: quantidade de pacientes vinculados ao usuario, quando essa relacao fizer sentido
- `Plano`: situacao comercial resumida, como `Pago`, `Trial`, `Sem plano`, `Bloqueado` ou equivalente local

Para atendentes:

- mostrar apenas o essencial, priorizando `Marcacoes` e eventualmente `Pacientes`
- omitir detalhes comerciais mais completos quando nao agregarem ao uso do perfil

### 6.2 Regras de leitura dos indicadores

As regras devem priorizar valor operacional, sem transformar a listagem em dashboard pesado.

**Marcacoes**

- Para profissional: contar agendamentos onde `agendamentos.id_prestador = usuarios.id`
- Para clinica/estabelecimento: contar agendamentos ligados ao escopo operacional daquele usuario, de forma coerente com a arvore atual
- Para admin: exibir contagem conforme o registro listado
- Para atendente: permitir contagem mais simples e enxuta, sem detalhamento excessivo

**Pacientes**

- Para profissional: quantidade de pacientes distintos vinculados aos seus agendamentos
- Para clinica/estabelecimento: quantidade de pacientes do escopo da clinica
- Para admin: quantidade coerente com o registro e com o tipo de usuario listado
- Para atendente: mostrar apenas se a leitura ficar clara e leve

**Plano**

- Quando houver `tenant_id` e dados SaaS relacionados, mostrar um resumo comercial direto:
  - `Pago` para tenant ativo com assinatura operacional valida
  - `Trial` quando o tenant estiver em periodo de trial
  - `Sem plano` quando nao houver assinatura ativa
  - `Bloqueado` quando o tenant existir mas estiver inativo
- Quando o registro nao tiver contexto SaaS aplicavel, omitir o chip ou mostrar um texto neutro, evitando ruído

### 6.3 Informacoes adicionais relevantes

Se a consulta e o custo de renderizacao continuarem simples, o relatorio tambem pode aproveitar mais dois sinais leves:

- `Ultima atividade`: data da ultima marcacao vinculada ao usuario
- `Vinculo`: nome resumido do estabelecimento ou responsavel ao qual o usuario pertence

Esses dados sao opcionais na primeira entrega. Entram apenas se couberem sem deixar a tela poluida e sem complicar demais a consulta.

## 7. Avatar sem foto

Hoje a ausencia de foto nao deve depender de imagem padrao de template. O novo comportamento sera:

- com `img`: renderizar a foto em formato circular
- sem `img`: renderizar um avatar circular neutro com icone de usuario em SVG inline

O icone deve ser leve e local a propria view, evitando dependencia externa. A inicial do nome deixa de ser o fallback principal nessa tela.

## 8. Responsividade

Breakpoints pretendidos:

- `>= 1200px`: linha ampla com colunas bem distribuidas
- `768px a 1199px`: linha em grade com algumas colunas empilhadas
- `< 768px`: acoes no topo, usuario logo abaixo, demais informacoes em blocos compactos

Criterios de usabilidade:

- botoes continuam tocaveis no celular
- telefone/WhatsApp continua facil de acionar com um toque
- textos longos quebram linha sem sobrepor conteudo
- nenhuma rolagem horizontal obrigatoria na tela principal

## 9. Estilo visual

Direcao visual:

- manter compatibilidade com o redesign admin ja presente (`utec-redesign.css`)
- trocar o aspecto de cards independentes por linhas com borda suave e separacao horizontal
- usar rotulos pequenos de coluna no desktop para reforcar leitura de relatorio
- manter botoes com cores semanticamente proximas do que existe hoje, mas mais compactos

O foco nao e parecer uma tabela HTML antiga, e sim entregar leitura de tabela com comportamento responsivo moderno.

## 10. Riscos e mitigacoes

| Risco | Mitigacao |
|---|---|
| View ficar densa demais no celular | Quebrar a linha em blocos e priorizar acoes + usuario no topo |
| Consulta falhar em bases sem `usuarios_especialidades` | Verificar `table_exists()` antes de usar join |
| Consulta ficar pesada com contadores extras | Limitar indicadores ao essencial e agregar no controller com cuidado |
| Acoes no inicio ocuparem espaco excessivo | Usar botoes compactos e permitir quebra controlada |
| Regressao no filtro por nome | Manter atributo `data-nome` por registro e reaproveitar a logica JS |

## 11. Criterios de sucesso

- `adm/usuarios` e `adm/usuarios/rel/{nivel}` passam a exibir linhas de relatorio responsivas
- A atividade aparece corretamente para prestadores com prioridade para especialidade
- Admin, clinicas e profissionais passam a ver indicadores operacionais e comerciais relevantes
- Atendentes recebem uma visualizacao mais limitada e objetiva
- Os botoes ficam no inicio de cada registro
- Quando nao houver foto, aparece um icone de usuario, sem foto padrao do template
- O link de WhatsApp continua funcional e pratico no celular
- A tela continua legivel e mais ampla no desktop
