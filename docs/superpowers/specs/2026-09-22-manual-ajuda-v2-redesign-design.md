# Manual de Ajuda ao Usuário — Redesenho Visual v2

## Objetivo

O manual v1 (`docs/superpowers/specs/2026-09-04-manual-ajuda-v1-design.md`)
resolveu o mecanismo — conteúdo em capítulos, PDF via mPDF — mas ficou
visualmente básico (blocos com borda simples, sem hierarquia tipográfica,
sem cor além do essencial). Em produção, o usuário avaliou o resultado como
"muito arcaico" e pediu um redesenho com "design sofisticado, boa aparência,
legível e intuitivo".

Esta entrega é **só a camada visual**: reescreve o CSS/HTML das 3 views
(`manual_funcao.php`, `manual_pdf_capa.php`, `manual_pdf_conteudo.php`).
Não mexe em `Manual_conteudo.php` (texto dos 12 capítulos já está certo,
só os valores de `print` mudam — ver seção de imagens) nem no controller.

## Escopo

- Redesenho visual da tela (`manual_funcao.php`) e do PDF
  (`manual_pdf_capa.php` + `manual_pdf_conteudo.php`), cobrindo os dois
  juntos com a mesma linguagem visual.
- Substituição das imagens dos capítulos por 6 screenshots reais capturados
  pelo usuário em produção (dados de teste próprios, sem paciente real) +
  1 ilustração criada da mensagem de confirmação no WhatsApp.
- Atualização dos valores `print` em `Manual_conteudo.php` para apontar
  pros arquivos corretos (é a única mudança nesse arquivo — texto dos
  capítulos não muda).

### Fora de escopo

- Conteúdo/texto dos capítulos (já validado no v1).
- Controller (`Usuarios::manual()`/`manual_pdf()`/`manual_dados()` — já
  funcionam, incluindo a correção de `error_reporting` feita em produção).
- Capítulos sem imagem (`acesso-hierarquia`, `avisos-internos`, `equipe`,
  `assinatura-pagamento`, `boas-praticas-suporte`) continuam sem — não há
  screenshot real disponível pra eles, e fabricar uma tela que eu nunca vi
  arrisca mostrar algo que não bate com o sistema de verdade. Ficam pro
  próximo ciclo, quando houver captura real.

## Direção visual (validada com o usuário via mockups)

Paleta puxada do `css/clicklinica-main.css` real do projeto — não inventada:
azul `#047bf8` (primário), teal `#20c997`, tipografia Avenir Next W01/Lato.

**Padrão de capítulo** (tela e PDF):
- Cabeçalho com gradiente azul→teal, contendo: badge circular com o número
  do capítulo, título em branco.
- Lombada colorida (gradiente também) só na altura do cabeçalho — não desce
  pelo corpo do texto inteiro (mantém capítulos longos leves de ler).
- Corpo em fundo branco: resumo, screenshot (quando existir) e lista de
  tópicos com checkmark verde-teal em vez de bullet.

**Capa do PDF:** gradiente cobrindo a página inteira (não só uma barra),
marca "UTEC Saúde" no topo, círculo decorativo, título grande, subtítulo,
rodapé com versão/data — tudo em branco sobre o gradiente. Mesmo motivo
visual do cabeçalho de capítulo, em escala de página cheia.

**Tela web:** mesmo cartão de capítulo, em coluna única empilhada (conteúdo
é texto corrido, não galeria — uma coluna lê melhor). Hero do topo ganha o
mesmo tratamento de gradiente da capa do PDF.

**Restrição técnica:** mPDF v6 não tem flexbox/grid — só `display:table`/
`table-cell`, bordas, cor de fundo e `border-radius` simples. A lombada é
montada com `table-cell` de largura fixa ao lado do cabeçalho. O gradiente
do cabeçalho em CSS puro (`linear-gradient`) é suportado pelo mPDF (já
usado na capa do v1 sem problema). Testado nos mockups do brainstorming.

**Sumário do PDF** (`tocpagebreak`, já existe): mantém a função, mas o CSS
que estiliza as entradas passa a usar a paleta nova (número em azul).

## Imagens dos capítulos

Fonte: 6 screenshots reais capturados pelo usuário em produção (pasta
`prints/` na raiz, dados de teste próprios — paciente "Valéria" e "Marcos
gomes", cadastrados no mesmo dia, claramente não são pacientes reais de
terceiros) + 1 ilustração criada via PHP/GD (mockup da mensagem de
WhatsApp com botões Confirmar/Cancelar, já que essa tela não existe no
admin — é o que aparece no celular do paciente).

Já copiadas para `imagens/manual/` e `Manual_conteudo.php` já atualizado:

| Capítulo | Arquivo | Origem |
|---|---|---|
| `boas-vindas` | `boas-vindas.png` | `prints/relatorio inicial.png` (visão geral com cards de estatística) |
| `agenda` | `agenda.png` | `prints/calendari.png` (calendário mensal) |
| `pacientes-cadastro` | `pacientes-cadastro.png` | `prints/relatorio de pacientes.png` (lista de pacientes) |
| `prontuario` | `prontuario.png` | `prints/atendimento.png` (os 3 blocos do atendimento) |
| `exames` | `exames.png` | `prints/timeline.png` (botão Exames no contexto do prontuário) |
| `whatsapp-confirmacao` | `whatsapp-confirmacao.png` | criado (mockup da mensagem WhatsApp) |
| `whatsapp-lembrete` | `whatsapp-lembrete.png` | `prints/prontuario.png` (mostra "Confirmado via WhatsApp" com timestamp) |
| `acesso-hierarquia`, `avisos-internos`, `equipe`, `assinatura-pagamento`, `boas-praticas-suporte` | — | sem imagem disponível, fica pro próximo ciclo |

A pasta `prints/` na raiz do projeto fica de fora do redesenho (é só a
origem, não precisa ser versionada — pode ser removida pelo usuário depois
que as imagens finais forem conferidas).

## Arquivos alterados

- `application/views/adm/usuarios/manual_funcao.php` — reescrita completa
  do CSS/HTML do card de capítulo e do hero, mantendo os includes Adminto
  (`search.php`/`menu.php`/`top.php`) e o loop `foreach($manual['capitulos'])`
  já existente.
- `application/views/adm/usuarios/manual_pdf_capa.php` — capa redesenhada
  (gradiente cheio).
- `application/views/adm/usuarios/manual_pdf_conteudo.php` — bloco de
  capítulo redesenhado (cabeçalho gradiente + lombada + checkmarks),
  mantendo `page-break-before` entre capítulos e o `<img src="<?=FCPATH...">`
  já existente.
- `application/libraries/Manual_conteudo.php` — só os valores de `print`
  (já feito nesta sessão, ver diff).
- `imagens/manual/*.png` — 7 arquivos novos (já copiados/criados nesta
  sessão).

## Teste / verificação

Mesma abordagem do v1 (sem WAMP local disponível):

1. `php -l` (com `C:\PHP\PHP7.2\php.exe`) nas 3 views alteradas.
2. Script de verificação simulando os 3 níveis (2, 3, 4), renderizando a
   tela web fora do bootstrap CI (aceitando a limitação conhecida de
   `get_instance()` nos includes do admin) e o PDF completo via mPDF,
   conferindo que o PDF sai válido (`%PDF-`) e de tamanho razoável nos 3
   níveis, com as imagens agora existentes sendo efetivamente embutidas
   (tamanho do PDF deve crescer em relação ao v1 sem imagens).
3. Deploy via FTP dos arquivos alterados + as 7 imagens novas.
4. Usuário confirma visualmente em produção, logado, nos 3 níveis.
