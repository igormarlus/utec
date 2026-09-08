<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manual_conteudo {

    const VERSAO = 1;

    public function capitulos_por_nivel($nivel) {
        $nivel = (int)$nivel;
        $resultado = array();
        foreach ($this->capitulos() as $capitulo) {
            if (!in_array($nivel, $capitulo['niveis'], true)) {
                continue;
            }
            $resultado[] = array(
                'slug' => $capitulo['slug'],
                'titulo' => $capitulo['titulo'],
                'icone' => $capitulo['icone'],
                'resumo' => $capitulo['resumo'],
                'topicos' => $this->resolver_topicos($capitulo['topicos'], $nivel),
                'print' => $this->resolver_print(isset($capitulo['print']) ? $capitulo['print'] : null, $nivel),
                'atualizado_em' => $capitulo['atualizado_em'],
            );
        }
        return $resultado;
    }

    private function resolver_topicos($topicos, $nivel) {
        $resultado = array();
        if (isset($topicos['*'])) {
            $resultado = array_merge($resultado, $topicos['*']);
        }
        if (isset($topicos[$nivel])) {
            $resultado = array_merge($resultado, $topicos[$nivel]);
        }
        return $resultado;
    }

    private function resolver_print($print, $nivel) {
        if ($print === null) {
            return null;
        }
        if (!is_array($print)) {
            return $print;
        }
        if (isset($print[$nivel])) {
            return $print[$nivel];
        }
        if (isset($print['*'])) {
            return $print['*'];
        }
        return null;
    }

    private function capitulos() {
        return array(
            array(
                'slug' => 'boas-vindas',
                'titulo' => 'Boas-vindas',
                'icone' => 'os-icon-grid-squares-22',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O UTecnologia Saude centraliza agenda, prontuario, exames e comunicacao com o paciente em um unico sistema, acessado pelo navegador. Este manual explica o que cada perfil de acesso pode fazer no dia a dia.',
                'topicos' => array(
                    '*' => array(
                        'O sistema funciona 100% pelo navegador, sem instalacao - funciona em computador, tablet ou celular.',
                        'Cada perfil de acesso (Estabelecimento, Prestador, Colaborador) enxerga uma fatia diferente da operacao, de acordo com a hierarquia de cadastro.',
                    ),
                    2 => array('Perfil pensado para gestores da clinica, consultorio ou operacao principal - normalmente coordena prestadores, colaboradores e a configuracao geral da rotina.'),
                    3 => array('Perfil pensado para o profissional que atende pacientes e registra prontuario - pode atuar sozinho ou dentro de uma clinica.'),
                    4 => array('Perfil pensado para secretaria e apoio operacional - organiza agenda e cadastro de pacientes no dia a dia.'),
                ),
                'print' => 'boas-vindas.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'acesso-hierarquia',
                'titulo' => 'Acesso e hierarquia',
                'icone' => 'os-icon-users',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O acesso segue uma arvore: quem cadastra vira o vinculo de quem e cadastrado. Isso define automaticamente o que cada perfil consegue ver.',
                'topicos' => array(
                    '*' => array(
                        'Voce so ve pacientes, agendamentos e prontuarios vinculados a sua propria estrutura - nunca dados de outra clinica ou profissional fora da sua arvore.',
                    ),
                    2 => array(
                        'Visualiza a operacao inteira vinculada ao seu cadastro: prestadores, colaboradores e pacientes.',
                        'E quem define, na pratica, quem entra na sua estrutura ao cadastrar novos prestadores e colaboradores.',
                    ),
                    3 => array(
                        'Visualiza seus proprios pacientes e atendimentos, alem do que colaboradores vinculados a voce registrarem.',
                        'Quando esta dentro de uma clinica (nivel Estabelecimento), tambem enxerga o contexto compartilhado dessa clinica.',
                    ),
                    4 => array(
                        'Visualiza pacientes e agendamentos da operacao a que estiver vinculado - direto a uma clinica ou a um prestador.',
                        'Tem acesso ao que voce mesmo cadastrar e ao que a clinica ou profissional vinculado registrar na mesma operacao visivel.',
                    ),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'agenda',
                'titulo' => 'Agenda',
                'icone' => 'os-icon-grid',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A Agenda e o centro operacional do dia: nela voce inicia, finaliza, remarca e cancela atendimentos, alem de acompanhar quem confirmou presenca pelo WhatsApp.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Agenda` no menu lateral para ver os atendimentos do dia, semana ou mes.',
                        'Cada agendamento mostra o status (pendente, confirmado, cancelado) e, quando aplicavel, a etiqueta de confirmacao via WhatsApp.',
                    ),
                    2 => array('Acompanha a agenda de todos os prestadores vinculados a clinica em uma visao unica.'),
                    3 => array('Usa a Agenda para iniciar, finalizar ou remarcar os proprios atendimentos.'),
                    4 => array('Usa a Agenda para confirmar, remarcar e organizar os atendimentos do dia da operacao vinculada.'),
                ),
                'print' => 'agenda.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'pacientes-cadastro',
                'titulo' => 'Pacientes e cadastro',
                'icone' => 'os-icon-folder',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O cadastro de pacientes concentra dados de contato, historico e vinculo com a operacao. Um cadastro bem feito evita duplicidade e erro de contato.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Pacientes` para cadastrar um novo paciente ou localizar um ja existente.',
                        'Mantenha telefone (com WhatsApp) e nome completo atualizados - sao usados no envio de confirmacao de agendamento.',
                    ),
                    2 => array('Revisa a base ativa de pacientes de toda a clinica.'),
                    4 => array('Cadastra novos pacientes e localiza contatos rapidamente durante o atendimento telefonico ou presencial.'),
                ),
                'print' => 'pacientes-cadastro.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'prontuario',
                'titulo' => 'Prontuario',
                'icone' => 'os-icon-file-text',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O prontuario registra o atendimento em tres blocos - Atendimento inicial, Avaliacao e Reavaliacao. Os rotulos desses blocos mudam automaticamente conforme a especialidade do prestador, para refletir o vocabulario clinico de cada area.',
                'topicos' => array(
                    '*' => array(
                        'Abra o prontuario a partir do agendamento em andamento, na Agenda ou na ficha do paciente.',
                        'Especialidades como Fisioterapia, Psicologia, Odontologia, Psiquiatria, Nutricao e Pediatria tem rotulos e exemplos de preenchimento adaptados - por exemplo, Fisioterapia mostra "Queixa / Avaliacao Postural" onde a Clinica Medica mostra "Queixa Principal".',
                        'O conteudo digitado continua sendo texto livre; o que muda por especialidade e apenas o rotulo e o texto de apoio (placeholder) de cada campo.',
                    ),
                    3 => array('E o prestador quem preenche o prontuario durante o atendimento - registrar no mesmo dia mantem o historico clinico organizado.'),
                    2 => array('Acompanha o prontuario dos prestadores vinculados a clinica pela ficha do paciente ou pelos relatorios clinicos.'),
                    4 => array('Acessa o prontuario apenas dentro do escopo operacional liberado para a equipe vinculada.'),
                ),
                'print' => 'prontuario.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'exames',
                'titulo' => 'Exames',
                'icone' => 'os-icon-database',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Exames solicitados e realizados ficam vinculados ao agendamento e ao paciente, com um checklist operacional de acompanhamento.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Exames` para solicitar um exame do catalogo ou registrar um exame ja realizado.',
                        'O checklist mostra o que falta confirmar antes de fechar o atendimento.',
                    ),
                ),
                'print' => 'exames.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'whatsapp-confirmacao',
                'titulo' => 'Confirmacao de agendamento pelo WhatsApp',
                'icone' => 'os-icon-phone-15',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Ao criar um agendamento, e possivel marcar o envio automatico de uma mensagem de confirmacao pelo WhatsApp do paciente, com botoes para confirmar ou cancelar direto pelo celular.',
                'topicos' => array(
                    '*' => array(
                        'Marque a opcao "Enviar confirmacao pelo WhatsApp" ao criar o agendamento - o sistema dispara um template aprovado pela Meta com dois botoes: Confirmar e Cancelar.',
                        'A resposta do paciente atualiza a agenda automaticamente: confirmar mantem o horario, cancelar muda o status do agendamento e libera o horario.',
                        'A etiqueta "Confirmado via WhatsApp" ou "Cancelado via WhatsApp" aparece na Agenda e no prontuario do paciente, entao toda a equipe ve o que aconteceu sem precisar ligar para o paciente.',
                        'Se o envio falhar (numero invalido, sem WhatsApp etc.), o agendamento e salvo normalmente - a falha do WhatsApp nunca bloqueia o cadastro.',
                    ),
                    2 => array('Contas sem assinatura ativa tem um limite de disparos gratuitos por periodo (modo trial); depois disso a confirmacao por WhatsApp exige plano ativo.'),
                ),
                'print' => 'whatsapp-confirmacao.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'whatsapp-lembrete',
                'titulo' => 'Lembrete automatico pelo WhatsApp',
                'icone' => 'os-icon-zap',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Alem da confirmacao no momento do agendamento, o sistema envia automaticamente um lembrete ao paciente poucas horas antes da consulta.',
                'topicos' => array(
                    '*' => array(
                        'O lembrete e disparado automaticamente ate 7 horas antes do horario marcado, para agendamentos que ainda nao foram confirmados nem cancelados.',
                        'Paciente que ja confirmou a presenca, ou que recebeu a confirmacao ha pouco tempo, nao recebe o lembrete duplicado.',
                        'E um envio unico por agendamento - nao gera repeticao de mensagens para o paciente.',
                    ),
                ),
                'print' => 'whatsapp-confirmacao.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'avisos-internos',
                'titulo' => 'Avisos internos (sino de notificacoes)',
                'icone' => 'os-icon-signs-11',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Sempre que o paciente confirma ou cancela pelo WhatsApp, um aviso aparece no sino de notificacoes no topo do sistema para quem precisa saber.',
                'topicos' => array(
                    '*' => array(
                        'O sino no topo mostra os avisos nao lidos, mais recentes primeiro.',
                        'Clicar em um aviso marca como lido e leva direto para o agendamento relacionado.',
                        'Cada aviso e entregue individualmente a quem precisa saber - o prestador do agendamento e quem mais estiver na mesma operacao.',
                    ),
                ),
                'print' => 'avisos-internos.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'equipe',
                'titulo' => 'Equipe',
                'icone' => 'os-icon-users',
                'niveis' => array(2, 3),
                'resumo' => 'A tela de Equipe organiza quem faz parte da operacao: prestadores e colaboradores vinculados a clinica ou ao profissional.',
                'topicos' => array(
                    '*' => array('Acesse `Equipe` para cadastrar ou revisar prestadores e colaboradores.'),
                    2 => array(
                        'Organiza todos os profissionais e colaboradores que participam da operacao da clinica.',
                        'Manter o cadastro da equipe atualizado evita perda de visibilidade na agenda e nos pacientes.',
                    ),
                    3 => array('Cadastra colaboradores proprios para apoio na rotina de atendimento, quando necessario.'),
                ),
                'print' => 'equipe.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'assinatura-pagamento',
                'titulo' => 'Assinatura e pagamento',
                'icone' => 'os-icon-wallet-loaded',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A assinatura do plano e cobrada em ciclos (mensal ou anual) via Mercado Pago, com PIX ou cartao, direto pela area administrativa.',
                'topicos' => array(
                    '*' => array('A tela de assinatura mostra o historico de ciclos de cobranca, pagamentos confirmados e tentativas recentes.'),
                    2 => array(
                        'E o Estabelecimento quem normalmente acompanha e quita a assinatura da operacao, em `Minha assinatura`.',
                        'O pagamento pode ser feito por PIX ou cartao dentro da area administrativa, sem sair do sistema.',
                        'Em caso de pendencia de pagamento, o acesso ao modulo SaaS e bloqueado ate a regularizacao - o restante do sistema (agenda, prontuario) continua funcionando normalmente.',
                    ),
                    3 => array('Quando a operacao tem assinatura vinculada, o prestador pode acompanhar a situacao comercial pela central de pagamento, sem precisar entrar na area de gestao SaaS.'),
                    4 => array('Se fizer parte do fluxo interno da clinica, o colaborador pode consultar o status comercial da assinatura vinculada.'),
                ),
                'print' => 'assinatura-pagamento.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'boas-praticas-suporte',
                'titulo' => 'Boas praticas e suporte',
                'icone' => 'os-icon-life-buoy',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Um fechamento com boas praticas de uso do dia a dia e como pedir ajuda quando precisar.',
                'topicos' => array(
                    '*' => array(
                        'Registre atendimento e status da agenda no mesmo dia - mantem o historico clinico e comercial sempre confiavel.',
                        'Padronize observacoes de remarcacao e cancelamento para toda a equipe entender o historico.',
                        'Em caso de duvida, use os canais de suporte do UTecnologia Saude informados na tela de login.',
                    ),
                    2 => array('Centralize o cadastro de colaboradores no nivel Colaborador para facilitar a operacao compartilhada da clinica.'),
                ),
                'atualizado_em' => '2026-09-07',
            ),
        );
    }
}
