# SEO/GEO - Chatbot WhatsApp por Perfil

## Objetivo

Transformar o chatbot de WhatsApp por perfil em um diferencial verificavel do UTecnologia Saude nas paginas externas, inicialmente pela landing de WhatsApp e homepage, preparando blocos reutilizaveis para distribuicao gradual nas demais landings.

## Escopo da Primeira Publicacao

### Landing de WhatsApp

A pagina `/confirmacao-de-consulta-por-whatsapp` permanece como hub do tema. Ela recebera uma secao apos a explicacao de confirmacao e lembrete com:

- titulo orientado ao resultado: `Do lembrete ao autoatendimento pelo WhatsApp`;
- resposta curta: pacientes consultam proximas consultas, solicitam cancelamento ou remarcacao; profissionais veem agenda e pendencias; o acesso muda conforme o perfil cadastrado;
- tres cards de fluxo: Paciente, Profissional/Atendente e Administrador;
- SVG semantico de conversa e agenda, com `title`, `desc`, contraste acessivel e equivalente textual no HTML;
- limitacao honesta: o telefone precisa estar cadastrado e o atendimento humano continua disponivel.

O bloco inclui um link contextual para teste e links internos para as paginas de sistema para clinicas e sistema para consultorio.

### Homepage

A homepage recebe um card de recurso na area de funcionalidades, com o beneficio `WhatsApp que atende por voce`, resumo objetivo e CTA para o hub. O SVG compacto mostra a sequencia `mensagem -> escolha de perfil -> agenda ou solicitacao` sem simular telas que nao existem.

## SEO e GEO

- Usar termos naturais no conteudo: `chatbot para clinicas no WhatsApp`, `agenda pelo WhatsApp`, `confirmacao de consulta por WhatsApp` e `autoatendimento para pacientes`.
- Manter uma intencao por URL: a landing continua centrada em confirmacao, lembrete e autoatendimento; nao sera criada uma nova URL de chatbot nesta etapa para evitar canibalizacao.
- Adicionar perguntas e respostas visiveis no FAQ sobre o que paciente e profissional podem fazer, como o sistema identifica o perfil e quando o atendimento humano e indicado.
- Atualizar o JSON-LD de `FAQPage` apenas para perguntas que estejam visiveis na pagina. Manter `SoftwareApplication` e `BreadcrumbList` coerentes com o conteudo real.
- O texto usara experiencia concreta do produto, fluxo especifico e limitacao operacional; nao empregara promessas absolutas, comparacoes nao comprovadas ou paginas em escala com texto repetido.
- Meta title, description, canonical e Open Graph da landing sao revisados somente se o novo escopo deixar de estar refletido neles.

## Expansao Gradual

Depois da primeira publicacao validada, o bloco sera adaptado com uma frase e link contextual nas paginas:

1. `/sistema-para-clinicas`
2. `/software-para-clinicas`
3. `/sistema-para-consultorio-medico`

Especialidades e comparativos ficam fora desta primeira alteracao para evitar conteudo repetitivo; receberao adaptacoes baseadas no caso de uso de cada pagina.

## Arquivos e Ativos

- `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php`: hub, FAQ, JSON-LD e SVG principal.
- `application/views/index-front.php`: card da homepage e SVG compacto.
- `img/seo/`: dois SVGs externos, leves e com texto acessivel no documento SVG.
- `sitemap.xml` e sitemaps existentes: somente atualizar `lastmod` das URLs efetivamente alteradas.

## Validacao

- Validar sintaxe PHP das views modificadas.
- Conferir a renderizacao desktop e mobile, com SVGs responsivos e sem sobreposicao de texto.
- Validar JSON-LD com o Rich Results Test e conferir canonical, title, description e links internos no HTML gerado.
- Avaliar a landing em busca de promessas que ultrapassem o comportamento implementado.
- Medir cliques da homepage para o hub e do hub para `/experimentar` pelo rastreamento existente.
