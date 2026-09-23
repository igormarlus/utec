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
                'resumo' => 'O UTecnologia Saúde centraliza agenda, prontuário, exames e comunicação com o paciente em um único sistema, acessado pelo navegador. Este manual explica o que cada perfil de acesso pode fazer no dia a dia.',
                'topicos' => array(
                    '*' => array(
                        'O sistema funciona 100% pelo navegador, sem instalação - funciona em computador, tablet ou celular.',
                        'Cada perfil de acesso (Estabelecimento, Prestador, Colaborador) enxerga uma fatia diferente da operação, de acordo com a hierarquia de cadastro.',
                    ),
                    2 => array('Perfil pensado para gestores da clínica, consultório ou operação principal - normalmente coordena prestadores, colaboradores e a configuração geral da rotina.'),
                    3 => array('Perfil pensado para o profissional que atende pacientes e registra prontuário - pode atuar sozinho ou dentro de uma clínica.'),
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
                'resumo' => 'O acesso segue uma árvore: quem cadastra vira o vínculo de quem é cadastrado. Isso define automaticamente o que cada perfil consegue ver.',
                'topicos' => array(
                    '*' => array(
                        'Você só vê pacientes, agendamentos e prontuários vinculados à sua própria estrutura - nunca dados de outra clínica ou profissional fora da sua árvore.',
                    ),
                    2 => array(
                        'Visualiza a operação inteira vinculada ao seu cadastro: prestadores, colaboradores e pacientes.',
                        'É quem define, na prática, quem entra na sua estrutura ao cadastrar novos prestadores e colaboradores.',
                    ),
                    3 => array(
                        'Visualiza seus próprios pacientes e atendimentos, além do que colaboradores vinculados a você registrarem.',
                        'Quando está dentro de uma clínica (nível Estabelecimento), também enxerga o contexto compartilhado dessa clínica.',
                    ),
                    4 => array(
                        'Visualiza pacientes e agendamentos da operação a que estiver vinculado - direto a uma clínica ou a um prestador.',
                        'Tem acesso ao que você mesmo cadastrar e ao que a clínica ou profissional vinculado registrar na mesma operação visível.',
                    ),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'agenda',
                'titulo' => 'Agenda',
                'icone' => 'os-icon-grid',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A Agenda é o centro operacional do dia: nela você inicia, finaliza, remarca e cancela atendimentos, além de acompanhar quem confirmou presença pelo WhatsApp.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Agenda` no menu lateral para ver os atendimentos do dia, semana ou mês.',
                        'Cada agendamento mostra o status (pendente, confirmado, cancelado) e, quando aplicável, a etiqueta de confirmação via WhatsApp.',
                    ),
                    2 => array('Acompanha a agenda de todos os prestadores vinculados à clínica em uma visão única.'),
                    3 => array('Usa a Agenda para iniciar, finalizar ou remarcar os próprios atendimentos.'),
                    4 => array('Usa a Agenda para confirmar, remarcar e organizar os atendimentos do dia da operação vinculada.'),
                ),
                'print' => 'agenda.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'horarios-atendimento',
                'titulo' => 'Horários de atendimento',
                'icone' => 'os-icon-clock',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Cada profissional define em quais dias e horários atende, quanto dura cada consulta e os períodos bloqueados. A agenda usa isso para sugerir horários livres.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Horários de atendimento` no menu lateral.',
                        'Em `Duração da consulta`, escolha quanto tempo cada agendamento ocupa (ex.: 30 minutos).',
                        'Em `Grade semanal`, marque os dias de atendimento e informe um ou mais intervalos por dia, como 08:00 às 12:00 e 14:00 às 18:00. O botão `Copiar segunda para seg–sex` repete a grade de segunda nos demais dias úteis.',
                        'Em `Bloqueios`, cadastre férias, feriados ou qualquer período sem atendimento. Marque `Dia inteiro` para bloquear datas completas.',
                        'Ao agendar ou remarcar, o sistema mostra os horários livres do profissional na data escolhida. Se você escolher um horário ocupado, bloqueado ou fora da grade, aparece um aviso, mas o agendamento pode ser salvo como encaixe.',
                        'Consultas canceladas liberam o horário automaticamente.',
                    ),
                    2 => array('Configura os horários de todos os profissionais da clínica, escolhendo o profissional no topo da tela.'),
                    3 => array('Configura os próprios horários, duração da consulta e bloqueios.'),
                    4 => array('Consulta os horários dos profissionais para orientar os pacientes; alterações ficam com o profissional ou o estabelecimento.'),
                ),
                'atualizado_em' => '2026-09-22',
            ),
            array(
                'slug' => 'pacientes-cadastro',
                'titulo' => 'Pacientes e cadastro',
                'icone' => 'os-icon-folder',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O cadastro de pacientes concentra dados de contato, histórico e vínculo com a operação. Um cadastro bem feito evita duplicidade e erro de contato.',
                'topicos' => array(
                    '*' => array(
                        'Acesse `Pacientes` para cadastrar um novo paciente ou localizar um já existente.',
                        'Mantenha telefone (com WhatsApp) e nome completo atualizados - são usados no envio de confirmação de agendamento.',
                    ),
                    2 => array('Revisa a base ativa de pacientes de toda a clínica.'),
                    4 => array('Cadastra novos pacientes e localiza contatos rapidamente durante o atendimento telefônico ou presencial.'),
                ),
                'print' => 'pacientes-cadastro.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'prontuario',
                'titulo' => 'Prontuário',
                'icone' => 'os-icon-file-text',
                'niveis' => array(2, 3, 4),
                'resumo' => 'O prontuário registra o atendimento em três blocos - Atendimento inicial, Avaliação e Reavaliação. Os rótulos desses blocos mudam automaticamente conforme a especialidade do prestador, para refletir o vocabulário clínico de cada área.',
                'topicos' => array(
                    '*' => array(
                        'Abra o prontuário a partir do agendamento em andamento, na Agenda ou na ficha do paciente.',
                        'Especialidades como Fisioterapia, Psicologia, Odontologia, Psiquiatria, Nutrição e Pediatria têm rótulos e exemplos de preenchimento adaptados - por exemplo, Fisioterapia mostra "Queixa / Avaliação Postural" onde a Clínica Médica mostra "Queixa Principal".',
                        'O conteúdo digitado continua sendo texto livre; o que muda por especialidade é apenas o rótulo e o texto de apoio (placeholder) de cada campo.',
                    ),
                    3 => array('É o prestador quem preenche o prontuário durante o atendimento - registrar no mesmo dia mantém o histórico clínico organizado.'),
                    2 => array('Acompanha o prontuário dos prestadores vinculados à clínica pela ficha do paciente ou pelos relatórios clínicos.'),
                    4 => array('Acessa o prontuário apenas dentro do escopo operacional liberado para a equipe vinculada.'),
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
                        'Acesse `Exames` para solicitar um exame do catálogo ou registrar um exame já realizado.',
                        'O checklist mostra o que falta confirmar antes de fechar o atendimento.',
                    ),
                ),
                'print' => 'exames.png',
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'whatsapp-confirmacao',
                'titulo' => 'Confirmação de agendamento pelo WhatsApp',
                'icone' => 'os-icon-phone-15',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Ao criar um agendamento, é possível marcar o envio automático de uma mensagem de confirmação pelo WhatsApp do paciente, com botões para confirmar ou cancelar direto pelo celular.',
                'topicos' => array(
                    '*' => array(
                        'Marque a opção "Enviar confirmação pelo WhatsApp" ao criar o agendamento - o sistema dispara um template aprovado pela Meta com dois botões: Confirmar e Cancelar.',
                        'A resposta do paciente atualiza a agenda automaticamente: confirmar mantém o horário, cancelar muda o status do agendamento e libera o horário.',
                        'A etiqueta "Confirmado via WhatsApp" ou "Cancelado via WhatsApp" aparece na Agenda e no prontuário do paciente, então toda a equipe vê o que aconteceu sem precisar ligar para o paciente.',
                        'Se o envio falhar (número inválido, sem WhatsApp etc.), o agendamento é salvo normalmente - a falha do WhatsApp nunca bloqueia o cadastro.',
                        'O paciente também pode remarcar ou cancelar sozinho pelo chatbot, com pelo menos 24 horas de antecedência: ele escolhe o dia e o horário entre os horários livres do mesmo profissional (definidos em `Horários de atendimento`) e confirma. A agenda é atualizada na hora.',
                        'Quando o paciente remarca ou cancela pelo chatbot, quem marcou a consulta e o profissional recebem um aviso no sino (com o motivo do cancelamento, se ele informar). Com menos de 24 horas para a consulta, o pedido chega para a equipe analisar, como antes.',
                    ),
                    2 => array('Contas sem assinatura ativa têm um limite de disparos gratuitos por período (modo trial); depois disso a confirmação por WhatsApp exige plano ativo.'),
                ),
                'print' => 'whatsapp-confirmacao.png',
                'atualizado_em' => '2026-09-23',
            ),
            array(
                'slug' => 'whatsapp-lembrete',
                'titulo' => 'Lembrete automático pelo WhatsApp',
                'icone' => 'os-icon-zap',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Além da confirmação no momento do agendamento, o sistema envia automaticamente um lembrete ao paciente poucas horas antes da consulta.',
                'topicos' => array(
                    '*' => array(
                        'O lembrete é disparado automaticamente até 7 horas antes do horário marcado, para agendamentos que ainda não foram confirmados nem cancelados.',
                        'Paciente que já confirmou a presença, ou que recebeu a confirmação há pouco tempo, não recebe o lembrete duplicado.',
                        'É um envio único por agendamento - não gera repetição de mensagens para o paciente.',
                    ),
                ),
                'print' => 'whatsapp-lembrete.png',
                'atualizado_em' => '2026-09-22',
            ),
            array(
                'slug' => 'avisos-internos',
                'titulo' => 'Avisos internos (sino de notificações)',
                'icone' => 'os-icon-signs-11',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Sempre que o paciente confirma ou cancela pelo WhatsApp, um aviso aparece no sino de notificações no topo do sistema para quem precisa saber.',
                'topicos' => array(
                    '*' => array(
                        'O sino no topo mostra os avisos não lidos, mais recentes primeiro.',
                        'Clicar em um aviso marca como lido e leva direto para o agendamento relacionado.',
                        'Cada aviso é entregue individualmente a quem precisa saber - o prestador do agendamento e quem mais estiver na mesma operação.',
                    ),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'equipe',
                'titulo' => 'Equipe',
                'icone' => 'os-icon-users',
                'niveis' => array(2, 3),
                'resumo' => 'A tela de Equipe organiza quem faz parte da operação: prestadores e colaboradores vinculados à clínica ou ao profissional.',
                'topicos' => array(
                    '*' => array('Acesse `Equipe` para cadastrar ou revisar prestadores e colaboradores.'),
                    2 => array(
                        'Organiza todos os profissionais e colaboradores que participam da operação da clínica.',
                        'Manter o cadastro da equipe atualizado evita perda de visibilidade na agenda e nos pacientes.',
                    ),
                    3 => array('Cadastra colaboradores próprios para apoio na rotina de atendimento, quando necessário.'),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'assinatura-pagamento',
                'titulo' => 'Assinatura e pagamento',
                'icone' => 'os-icon-wallet-loaded',
                'niveis' => array(2, 3, 4),
                'resumo' => 'A assinatura do plano é cobrada em ciclos (mensal ou anual) via Mercado Pago, com PIX ou cartão, direto pela área administrativa.',
                'topicos' => array(
                    '*' => array('A tela de assinatura mostra o histórico de ciclos de cobrança, pagamentos confirmados e tentativas recentes.'),
                    2 => array(
                        'É o Estabelecimento quem normalmente acompanha e quita a assinatura da operação, em `Minha assinatura`.',
                        'O pagamento pode ser feito por PIX ou cartão dentro da área administrativa, sem sair do sistema.',
                        'Em caso de pendência de pagamento, o acesso ao módulo SaaS é bloqueado até a regularização - o restante do sistema (agenda, prontuário) continua funcionando normalmente.',
                    ),
                    3 => array('Quando a operação tem assinatura vinculada, o prestador pode acompanhar a situação comercial pela central de pagamento, sem precisar entrar na área de gestão SaaS.'),
                    4 => array('Se fizer parte do fluxo interno da clínica, o colaborador pode consultar o status comercial da assinatura vinculada.'),
                ),
                'atualizado_em' => '2026-09-07',
            ),
            array(
                'slug' => 'boas-praticas-suporte',
                'titulo' => 'Boas práticas e suporte',
                'icone' => 'os-icon-life-buoy',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Um fechamento com boas práticas de uso do dia a dia e como pedir ajuda quando precisar.',
                'topicos' => array(
                    '*' => array(
                        'Registre atendimento e status da agenda no mesmo dia - mantém o histórico clínico e comercial sempre confiável.',
                        'Padronize observações de remarcação e cancelamento para toda a equipe entender o histórico.',
                        'Em caso de dúvida, use os canais de suporte do UTecnologia Saúde informados na tela de login.',
                    ),
                    2 => array('Centralize o cadastro de colaboradores no nível Colaborador para facilitar a operação compartilhada da clínica.'),
                ),
                'atualizado_em' => '2026-09-07',
            ),
        );
    }
}
