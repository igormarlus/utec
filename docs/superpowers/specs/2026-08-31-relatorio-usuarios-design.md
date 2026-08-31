# Relatorio de Usuarios em Linhas Responsivas — Design

**Data:** 2026-08-31
**Status:** Aprovado para planejamento
**Escopo:** Ajustar `adm/usuarios` e `adm/usuarios/rel/{nivel}` para um formato de relatorio em linhas responsivas, com atividade visivel, acoes no inicio e avatar sem foto usando icone
**Stack:** PHP 7 + CodeIgniter 3 + Bootstrap 4 + jQuery

## 1. Objetivo

A listagem atual de usuarios em `application/views/adm/usuarios/new/lista.php` usa cards compactos. Isso funciona razoavelmente no celular, mas perde leitura operacional no desktop, onde a equipe precisa comparar registros com mais rapidez. O objetivo desta mudanca e transformar a tela em uma lista com cara de relatorio, sem depender de `<table>`, preservando boa responsividade e os atalhos atuais, especialmente o contato por WhatsApp.

O resultado esperado e uma tela que:

- fique ampla e escaneavel no desktop
- continue pratica no celular, com botoes grandes o suficiente para toque
- mostre a atividade do usuario com mais clareza
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

`Acoes` | `Usuario` | `Atividade` | `Contato` | `Cadastro` | `Status`

Comportamento:

- `Acoes`: primeiro bloco da linha, contendo `Prontuario` quando nivel 5, `Editar`, `Remover`, `Acessar`, `Ativar/Desativar` e `WhatsApp` quando houver telefone
- `Usuario`: avatar, nome, id e login quando aplicavel
- `Atividade`: valor resolvido conforme regra da secao 5
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

## 6. Avatar sem foto

Hoje a ausencia de foto nao deve depender de imagem padrao de template. O novo comportamento sera:

- com `img`: renderizar a foto em formato circular
- sem `img`: renderizar um avatar circular neutro com icone de usuario em SVG inline

O icone deve ser leve e local a propria view, evitando dependencia externa. A inicial do nome deixa de ser o fallback principal nessa tela.

## 7. Responsividade

Breakpoints pretendidos:

- `>= 1200px`: linha ampla com colunas bem distribuidas
- `768px a 1199px`: linha em grade com algumas colunas empilhadas
- `< 768px`: acoes no topo, usuario logo abaixo, demais informacoes em blocos compactos

Criterios de usabilidade:

- botoes continuam tocaveis no celular
- telefone/WhatsApp continua facil de acionar com um toque
- textos longos quebram linha sem sobrepor conteudo
- nenhuma rolagem horizontal obrigatoria na tela principal

## 8. Estilo visual

Direcao visual:

- manter compatibilidade com o redesign admin ja presente (`utec-redesign.css`)
- trocar o aspecto de cards independentes por linhas com borda suave e separacao horizontal
- usar rotulos pequenos de coluna no desktop para reforcar leitura de relatorio
- manter botoes com cores semanticamente proximas do que existe hoje, mas mais compactos

O foco nao e parecer uma tabela HTML antiga, e sim entregar leitura de tabela com comportamento responsivo moderno.

## 9. Riscos e mitigacoes

| Risco | Mitigacao |
|---|---|
| View ficar densa demais no celular | Quebrar a linha em blocos e priorizar acoes + usuario no topo |
| Consulta falhar em bases sem `usuarios_especialidades` | Verificar `table_exists()` antes de usar join |
| Acoes no inicio ocuparem espaco excessivo | Usar botoes compactos e permitir quebra controlada |
| Regressao no filtro por nome | Manter atributo `data-nome` por registro e reaproveitar a logica JS |

## 10. Criterios de sucesso

- `adm/usuarios` e `adm/usuarios/rel/{nivel}` passam a exibir linhas de relatorio responsivas
- A atividade aparece corretamente para prestadores com prioridade para especialidade
- Os botoes ficam no inicio de cada registro
- Quando nao houver foto, aparece um icone de usuario, sem foto padrao do template
- O link de WhatsApp continua funcional e pratico no celular
- A tela continua legivel e mais ampla no desktop
