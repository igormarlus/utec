# Manual de Ajuda ao Usuário — Modelo v1

## Objetivo

Substituir o manual de suporte atual (`adm/usuarios/manual/{nivel}` e
`adm/usuarios/manual_pdf/{nivel}`) — hoje 5 blocos de texto genérico por nível,
sem nada sobre WhatsApp, SaaS, prontuário por especialidade ou avisos internos —
por um manual estruturado em capítulos reaproveitáveis, com screenshots reais do
sistema, PDF gerado em mPDF (capa, sumário, cabeçalho/rodapé) e uma trilha para
manter o conteúdo sincronizado com as próximas evoluções do produto.

Este é o **modelo v1**: cobre os níveis 2 (Estabelecimento), 3 (Prestador) e 4
(Colaborador), com o conjunto de capítulos descrito abaixo. Amplia-se em versões
futuras (novos capítulos, novos níveis) sem trocar a arquitetura.

## Escopo

- Novo `application/libraries/Manual_conteudo.php`: registro estático dos
  capítulos do manual, com filtro por nível.
- `application/controllers/adm/Usuarios.php`: `manual()` e `manual_pdf()`
  passam a montar os dados a partir de `Manual_conteudo`, em vez de
  `build_manual_context()` (que é removido).
- `application/views/adm/usuarios/manual_funcao.php`: redesenhada para
  renderizar a lista de capítulos (com screenshot, resumo e tópicos) em vez dos
  5 cards fixos atuais.
- `application/views/adm/usuarios/manual_funcao_pdf.php`: reescrita para mPDF —
  capa, sumário, um capítulo por bloco (quebra de página quando fizer sentido),
  cabeçalho/rodapé com número de página e "Manual v1 · gerado em {data}".
- Troca do motor de PDF: `TCPDF` → `mPDF` (`application/libraries/M_pdf.php`,
  já presente no projeto mas sem uso ativo em nenhum controller hoje —
  precisa validação ponta a ponta nesta entrega).
- Novos assets: `imagens/manual/*.png` (screenshots capturados em ambiente
  local, ver seção "Captura de screenshots").
- Atualização do `CLAUDE.md` (seções 10.3.1, 12, 18) com a regra de
  sincronização do manual a cada feature nova.

### Fora de escopo nesta entrega

- Níveis 1 (Admin) e 5 (Paciente) — ver seção "Extensões futuras".
- Edição do conteúdo via tela administrativa (o manual continua sendo
  código versionado no git, não uma tabela editável).
- Automação que bloqueie deploy se o manual não foi atualizado — a garantia
  nesta v1 é de processo (checklist do `agente-dev-infra`), não de código.

## Arquitetura de conteúdo

`Manual_conteudo` é uma classe com um array estático de capítulos e métodos de
consulta. Sem tabela no banco — conteúdo estrutural, versionado no git, editado
só por quem desenvolve a feature (mesmo padrão de dado hardcoded que
`usuarios_especialidades` usa como seed, mas aqui sem persistência).

```php
class Manual_conteudo {
    const VERSAO = 1; // sobe só quando a ESTRUTURA do array mudar

    private static function capitulos() {
        return [
            [
                'slug' => 'boas-vindas',
                'titulo' => 'Boas-vindas',
                'icone' => 'os-icon-home',
                'niveis' => [2, 3, 4],
                'resumo' => '...',
                'topicos' => [
                    '*' => ['tópico comum a todos os níveis do capítulo'],
                    2 => ['tópico específico do estabelecimento'],
                ],
                'print' => 'boas-vindas.png', // ou ['2' => 'x.png', '*' => 'y.png']
                'atualizado_em' => '2026-09-04',
            ],
            // ... demais capítulos
        ];
    }

    public static function capitulos_por_nivel(int $nivel): array {
        // filtra por 'niveis', mescla topicos['*'] + topicos[$nivel],
        // resolve 'print' (string única ou por nível)
    }
}
```

`capitulos_por_nivel($nivel)` é a única porta de entrada usada pelo controller —
tanto `manual()` quanto `manual_pdf()` chamam a mesma função, eliminando a
duplicação tripla de texto que existe hoje entre os 3 níveis.

## Capítulos do modelo v1

| # | Slug | Níveis | Conteúdo novo? |
|---|------|--------|----------------|
| 1 | `boas-vindas` | 2,3,4 | reaproveita texto atual |
| 2 | `acesso-hierarquia` | 2,3,4 | reaproveita texto atual |
| 3 | `agenda` | 2,3,4 | reaproveita texto atual |
| 4 | `pacientes-cadastro` | 2,3,4 | reaproveita texto atual |
| 5 | `prontuario` (com sub-bloco "por especialidade") | 2,3,4 | **novo** — labels/campos variam por especialidade do prestador |
| 6 | `exames` | 2,3,4 | reaproveita texto atual |
| 7 | `whatsapp-confirmacao` | 2,3,4 | **novo** — template, botões confirmar/cancelar, etiqueta na agenda |
| 8 | `whatsapp-lembrete` | 2,3,4 | **novo** — cron, janela ~6h antes, limite trial |
| 9 | `avisos-internos` | 2,3,4 | **novo** — sino de notificações no topo |
| 10 | `equipe` | 2,3 | reaproveita texto atual (colaborador não gerencia equipe) |
| 11 | `assinatura-pagamento` | 2,3,4 | **novo** — ciclos de cobrança, PIX/cartão, efeito de pendência (versão detalhada para nível 2, informativa para 3/4) |
| 12 | `boas-praticas-suporte` | 2,3,4 | reaproveita texto atual + contato de suporte |

## Captura de screenshots

Ambiente: local (`c:\htdocs\utec`, WAMP), dados de teste — nunca produção, para
não expor paciente real.

1. Login como admin (`adm/123456`).
2. Consulta ao banco local (`utecnologiacom_db`, tabela `usuarios`) para achar
   um usuário de teste real de nível 2, 3 e 4.
3. Uso de `/admin/logar_como/{id}` para assumir cada perfil e navegar até a
   tela correspondente a cada capítulo (Agenda, Prontuário, etiqueta do
   WhatsApp na agenda, sino de avisos, tela de assinatura).
4. Captura via Claude in Chrome, recorte da região relevante (não a tela
   inteira), salvo em `imagens/manual/{slug}.png` (ou `{slug}-{nivel}.png`
   quando a tela difere por perfil).
5. Telas idênticas entre níveis usam um único arquivo, referenciado pelos 3
   perfis — evita triplicar assets.

Referência de imagem:
- Na tela (`manual_funcao.php`): `base_url().'imagens/manual/'.$arquivo`.
- No PDF (`manual_funcao_pdf.php`, mPDF): `FCPATH.'imagens/manual/'.$arquivo`
  (caminho absoluto do filesystem, funciona igual em Windows/dev e
  Linux/produção porque `FCPATH` resolve por ambiente).

## PDF em mPDF

Troca do bloco em `Usuarios::manual_pdf()`:

```php
$this->load->library('m_pdf');
$mpdf = $this->m_pdf->pdf; // new mPDF(...)
$mpdf->SetTitle($manual['pdf_title']);
$mpdf->SetHTMLHeader('...cabeçalho com título do manual...');
$mpdf->SetHTMLFooter('...rodapé com número de página e "Manual v1"...');
$mpdf->WriteHTML($html);
$mpdf->Output($manual['pdf_slug'].'.pdf', 'I');
```

Sumário: mPDF gera TOC automático a partir dos `<h2>` de cada capítulo
(`$mpdf->TOCpagebreakByArray()` ou marcação `<tocentry>`, a definir na
implementação conforme a versão do mPDF v6 vendorizada em
`application/third_party/mpdf`).

`M_pdf.php` está no projeto mas **sem nenhum uso ativo** hoje — esta é a
primeira vez que entra em produção via um controller. Precisa validação ponta
a ponta (fontes, acentuação UTF-8, imagens) antes do deploy.

## Views

- `manual_funcao.php`: mantém o wrapper Adminto (menu/top/breadcrumb) e o hero
  atual, mas o corpo passa a iterar `$manual['capitulos']` — cada capítulo vira
  um card com título, screenshot (se houver), resumo e lista de tópicos, em vez
  dos 5 cards fixos hardcoded na view.
- `manual_funcao_pdf.php`: reescrita como documento mPDF — capa (logo, título,
  subtítulo, data de geração), página de sumário, um bloco por capítulo com
  título, screenshot e tópicos, rodapé com paginação e "Manual v{VERSAO}".

## Governança — manter o manual sincronizado

Adicionar ao `CLAUDE.md`:

- Seção 10.3.1 / 18: toda funcionalidade nova visível ao usuário final
  (WhatsApp, SaaS, prontuário, agenda etc.) só é considerada concluída quando
  `Manual_conteudo.php` tiver um capítulo ou tópico atualizado cobrindo ela.
- `agente-dev-infra`, por ser o único agente que publica em produção, passa a
  checar isso como parte do fechamento de qualquer entrega com impacto visível
  ao usuário — mesmo portão que já usa para `php -l` e healthcheck pós-deploy.

Essa garantia é de processo (checklist), não de código — não há trava
automática nesta v1.

## Divisão de trabalho

| Etapa | Responsável |
|---|---|
| `Manual_conteudo.php` (12 capítulos, textos por nível) | sessão atual, com revisão de conteúdo por `agente-produto` |
| Views (`manual_funcao.php`, `manual_funcao_pdf.php` em mPDF) | `agente-frontend` |
| Troca TCPDF → mPDF no controller, deploy FTP, healthcheck | `agente-dev-infra` |
| Screenshots (Chrome + banco local) | sessão atual |
| Atualização do `CLAUDE.md` (regra de sincronização) | sessão atual |

## Extensões futuras (fora desta entrega)

- Nível 1 (Admin): manual interno de operação SaaS/provisionamento/migrações.
- Nível 5 (Paciente): guia curto sobre confirmação via WhatsApp (paciente não
  tem portal dedicado hoje).
- Automação leve (ex.: rota `adm/dev` que lista capítulos com
  `atualizado_em` mais antigo que X meses) — avaliar se vale a pena depois que
  o modelo v1 estiver em uso real.

## Teste / verificação

Sem migração de banco (zero risco de schema). Verificação:

1. `php -l` em todos os arquivos alterados/criados.
2. Local, logado como cada perfil de teste (2, 3, 4): abrir
   `adm/usuarios/manual/{nivel}` e conferir os 12 capítulos, screenshots e
   tópicos corretos para o nível.
3. Abrir `adm/usuarios/manual_pdf/{nivel}` para os 3 níveis: conferir capa,
   sumário, acentuação UTF-8, imagens renderizando (path mPDF via `FCPATH`),
   cabeçalho/rodapé e paginação.
4. Deploy: `agente-dev-infra` sobe os arquivos via FTP, healthcheck padrão
   (home 200, `/admin` 200) + abrir o PDF em produção uma vez para os 3 níveis.
