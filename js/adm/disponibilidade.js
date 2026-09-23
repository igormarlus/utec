/* Widget de disponibilidade do profissional: lista horarios livres e avisa encaixe.
 * Nunca impede o envio do formulario. */
(function (window, $) {
  'use strict';

  var MENSAGENS = {
    ocupado: 'Já existe agendamento neste horário para o profissional.',
    fora_da_grade: 'Horário fora da grade de atendimento do profissional.',
    bloqueado: 'O profissional bloqueou este horário.'
  };

  function attach(opts) {
    var $box = $(opts.box);
    var $data = $(opts.data);
    var $hora = $(opts.hora);
    if (!$box.length || !$data.length || !$hora.length) {
      return { atualizar: function () {} };
    }
    var $lista = $('<div class="utec-disp-livres" style="margin-top:8px;"></div>');
    var $alerta = $('<div class="alert alert-warning" style="display:none;margin:8px 0 0;font-size:13px;"></div>');
    $box.empty().append($lista).append($alerta);
    var seqLista = 0;
    var seqHora = 0;

    function idPrestador() { return parseInt(opts.prestador(), 10) || 0; }
    function idIgnorar() { return opts.ignorar ? (parseInt(opts.ignorar(), 10) || 0) : 0; }

    function buscar(hora) {
      return $.getJSON(opts.baseUrl + 'adm/horarios/livres', {
        id_prestador: idPrestador(),
        data: $data.val(),
        ignorar: idIgnorar(),
        hora: hora || ''
      });
    }

    function atualizarLista() {
      var seq = ++seqLista;
      $lista.empty();
      if (!idPrestador() || !$data.val()) { return; }
      buscar('').done(function (r) {
        if (seq !== seqLista || !r || !r.tem_grade) { return; }
        if (!r.livres.length) {
          $lista.append('<small class="text-muted">Sem horários livres nesta data.</small>');
          return;
        }
        $lista.append('<small class="text-muted d-block" style="margin-bottom:4px;">Horários livres (' + r.duracao + ' min):</small>');
        $.each(r.livres, function (_, h) {
          $('<button type="button" class="btn btn-sm btn-outline-success" style="margin:0 4px 4px 0;"></button>')
            .text(h)
            .on('click', function () { $hora.val(h).trigger('change'); })
            .appendTo($lista);
        });
      }).fail(function () {
        if (seq === seqLista) { $lista.empty(); }
      });
    }

    function verificarHora() {
      var seq = ++seqHora;
      var h = $hora.val();
      $alerta.hide();
      if (!h || !idPrestador() || !$data.val()) { return; }
      buscar(h).done(function (r) {
        if (seq !== seqHora || !r || !r.tem_grade || !r.situacao || r.situacao === 'livre') { return; }
        $alerta.text((MENSAGENS[r.situacao] || 'Horário fora da disponibilidade.') + ' Você pode salvar mesmo assim (encaixe).').show();
      }).fail(function () {
        if (seq === seqHora) { $alerta.hide(); }
      });
    }

    function atualizar() { atualizarLista(); verificarHora(); }

    if (opts.prestadorEl) { $(opts.prestadorEl).on('change', atualizar); }
    $data.on('change', atualizar);
    $hora.on('change', verificarHora);

    return { atualizar: atualizar };
  }

  window.UtecDisponibilidade = { attach: attach };
})(window, jQuery);
