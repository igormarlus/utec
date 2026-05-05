<!DOCTYPE html>
<html>
  <head>
    <title>Prontuário</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="template language" name="keywords">
    <meta content="Tamerlan Soziev" name="author">
    <meta content="Admin dashboard html template" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="favicon.png" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/dropzone/dist/dropzone.css" rel="stylesheet">

    <!-- <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet"> -->
    

    <link href="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <!--<link href="<?=base_url()?>css/main.css?version=4.5.0" rel="stylesheet">-->
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">

    <link rel="stylesheet" href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper with-side-panel solid-bg-all">
      
      <? include("includes/adm/search.php"); ?>
      <div class="layout-w">
        
        
        <? #include("includes/adm/menu.php"); ?>
        <!--------------------
        END - Mobile Menu
        --------------------><!--------------------
        START - Main Menu
        -------------------->
        <? include("includes/adm/paciente/menu.php"); ?>
        
        <!--------------------
        END - Main Menu
        -------------------->
        <div class="content-w">
          <!--------------------
          START - Top Bar
          -------------------->
          
          <? include("includes/adm/top.php"); ?>
          <!--------------------
          END - Top Bar
          --------------------><!--------------------
          START - Breadcrumbs
          -------------------->
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Home</a>
            </li>
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Pacientes</a>
            </li>
            <li class="breadcrumb-item">
              <span>Lista</span>
            </li>
          </ul>
          <!--------------------
          END - Breadcrumbs
          -------------------->
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="row">
                <div class="col-sm-12">
                  <div class="element-wrapper">
                    <div class="element-actions">
                      <form class="form-inline justify-content-sm-end">
                        <select class="form-control form-control-sm">
                          <option value="Pending">
                            Hoje
                          </option>
                          <option value="Active">
                            Última semana
                          </option>
                          <option value="Cancelled">
                            Últimos 30 dias
                          </option>
                        </select>
                      </form>
                    </div>
                    <h6 class="element-header">
                      <?=$this->padrao_model->get_by_matriz('nivel',$nivel,'usuarios_niveis')->row()->nome?>
                    </h6>

                    <p>
                      <a href="<?=base_url()?>adm/atendimento" class="btn btn-primary">Voltar</a>
                    </p>

                    <p>
                      <a href="<?=base_url()?>adm/atendimento/novo/<?=$dd->id?>" class="btn btn-success">Novo Agendamento</a>
                    </p>

                  </div>
                </div>
              </div>

              <? if($id_agenda > 0){ ?>
              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    
                    <div class="element-box">

                      <!-- FORM -->

                      <h4><?=$this->padrao_model->converte_data($dd_agenda->data_agenda)?> - <?=$dd_agenda->hora_agenda?></h4>

                      <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/set" enctype='multipart/form-data'>
                        <input type="hidden" name="id_agenda" value="<?=$id_agenda?>">
                          <h5 class="form-header">
                            Informações do atendimento
                          </h5>

                          <div class="form-desc">
                            Preencha as informações corretamente.
                          </div>

                          <div class="form-group">
                              <label class="mws-form-label">Atendimento Inicial</label>
                              <div class="mws-form-item">
                                <!-- <input type="hidden" name="id" value="<?php #echo $usuario->id; ?>"> -->
                                  <textarea name="atendimento_inicial" class="form-control" placeholder="Preencha o atendimento inicial"><?=$dd_agenda->atendimento_inicial?></textarea>
                              </div>
                          </div>

                          
                            <div class="row">

                              <div class="col-sm-12">
                                <div class="form-group bordered">
                                    <label class="mws-form-label">Avaliação </label>
                                    <div class="mws-form-item">
                                        <textarea name="avaliacao" class="form-control" placeholder="Preencha sua Avaliação"><?=$dd_agenda->avaliacao?></textarea>
                                    </div>
                                </div>  
                              </div>  

                              <div class="col-sm-12">
                                <div class="form-group bordered">
                                    <label class="mws-form-label">Reavaliação </label>
                                    <div class="mws-form-item">
                                        <textarea  name="reavaliacao" class="form-control" placeholder="Preencha sua Revaliação"><?=$dd_agenda->reavaliacao?></textarea>
                                    </div>
                                </div>  
                              </div>  

                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label class="mws-form-label"> </label>
                                    <div class="mws-form-item">
                                      <button class="btn btn-primary" type="submit"> Salvar</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                        </form>

                      <!-- X FORM -->

                    </div>
                  </div>
                </div>
              </div>
            <? } ?>

              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    
                    <div class="element-box">


                      <div class="table-responsive">
                          
                        <table class="table table-lightborder" id="">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Dt. Cadastro</th>
                                    
                                    
                                    <!--<th>Vídeo</th>-->
                                    <!-- <th>Foto</th> -->

                                    <th>Nome</th>

                                    <th>Prontuário</th>
                                    
                                    <th>Data</th>
                                    <th>hora</th>
                                    <th>Status</th>
                                    <!-- <th>Produtos</th>
                                    <th>Solicitações</th> -->
                                    
                                    <th>Telefone</th>   
                                    <th>Cad. Por</th>
                                    
                                    
                                    <th align="center" style="text-align: center;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              if($qr_agendamentos->num_rows() > 0){
                                foreach ($qr_agendamentos->result() as $agenda) {
                                $usuario = $this->padrao_model->get_by_id($agenda->id_paciente,'usuarios')->row();
                ?>
                                <tr>
                                  <td><?=$agenda->id?></td>
                                  <td><?=$agenda->dt_cadastro?></td>
                                  
                                  
                                  <!--<td>
                                        <? /* if($usuario->video != ""){ ?>
                                            <a target="_blank" href="<?=base_url()?>uploads/<?=$usuario->video?>" alt="Foto usuário"><?=substr($usuario->video,0,10)?></a>
                                        <? }else{ ?>
                                            Sem Vídeo
                                        <? } */ ?>
                                    </td>-->
                                  <!-- <td>
                                    <? if($usuario->img != ""){ ?>
                                            <a target="_blank" href="<?=base_url()?>imagens/usuarios/<?=$usuario->img?>" ><img src="<?=base_url()?>imagens/usuarios/min/<?=$usuario->img?>" alt="Foto usuário"></a>
                                        <? }else{ ?>
                                            Sem foto
                                        <? } ?>
                                  </td> -->
                                    
                                    <td> 
                                      <a href="<?=base_url('adm/atendimento/prontuario/'.$usuario->id)?>" target="_blank">
                                        <?php echo $usuario->nome; ?>
                                      </a>
                                    </td>

                                    <td>
                                      
                                      <span class="btn-group">
                                            <!-- <a href="#" class="btn btn-small"><i class="icon-search"></i></a> -->
                                            <? #if($usuario->nivel == 5){ ?>
                                            <!-- <a href="<?php echo base_url().'index.php/adm/usuarios/prontuario/'.$usuario->id; ?>" 
                                              class="btn btn-small" style="color:blue" target="_blank"> -->

                                             <a href="<?=base_url('adm/atendimento/prontuario/'.$usuario->id.'/'.$agenda->id)?>" target="_blank">
                                                <i class="os-icon os-icon-edit"></i>
                                                Prontuário
                                                <!-- <i class="icon-tag"></i> -->
                                            </a>

                                            <? #} ?>
                                    </td>

                                    

                                    
                                    <td>
                                      <?=$this->padrao_model->converte_data($agenda->data_agenda)?>
                                      
                                      <? #=$agenda->data_hora_agenda?>
                                    </td>

                                    <td>
                                      
                                      <?=substr($agenda->hora_agenda,0,5)?>h
                                      <? #=$agenda->data_hora_agenda?>
                                    </td>

                                    <td>
                                        <a href="<?php echo base_url().'index.php/adm/atendimento/set_status_agenda/'.$agenda->id.'/'.$agenda->status; ?>" class="btn btn-small">
                                          <? if($agenda->status == 1){ ?>
                                              <!--<i class="os-icon os-icon-check"></i>-->
                                              <i class="os-icon os-icon-check-circle"  title="Em atendimento" style="color:green"></i>
                                           <? } ?>
                                           <? if($agenda->status == 2){ ?>
                                              <i class="os-icon os-icon-check-circle" title="Finalizado" style="color:orange"></i>
                                           <? } ?>
                                           <? if($agenda->status == 0){ ?>
                                              <i class="os-icon os-icon-check-circle" title="Pendente" style="color:red"></i>
                                           <? } ?>
                                        </a>
                                        
                                    </td>


                                 
                                    
                                    <td title="<?php #echo $usuario->device; ?>">
                                        <?
                                        $arr_replcae_tel = array("-"," ","+","(",")"); 
                                        $tel_trat = str_replace($arr_replcae_tel, "",$usuario->telefone);
                                        ?>
                                        <a href="https://api.whatsapp.com/send?phone=55<?=$tel_trat?>" target="_blank">
                                            <?php echo $usuario->telefone; ?>                                           
                                        </a> 
                                    </td>

                                    <td><?=$this->padrao_model->get_by_id($agenda->id_user,'usuarios')->row()->nome?></td>
                                    
                                    

                                    <td>
                                      
                                    <span class="btn-group">
                                      
                                            
                                            <a href="<?php echo base_url().'adm/usuarios/prontuario/'.$usuario->id.'/'.$agenda->id; ?>" class="btn btn-small" style="color:orange"><i class="os-icon os-icon-edit"></i>Edição</a>
                                            
                                        </span>
                                    </td>
                                </tr>
                                <?php
                }
              }
                ?>
                            </tbody>
                        </table>



                      </div>
                    </div>
                  </div>
                </div>
                
                

            </div>
            <!-- ═══════════════════════════════════════════════════════
                 SEÇÃO: ARQUIVOS DO PACIENTE
            ═══════════════════════════════════════════════════════ -->
            <div class="row">
              <div class="col-sm-12">
                <div class="element-wrapper">
                  <div class="element-box">
                    <h6 class="element-header">
                      <i class="os-icon os-icon-folder" style="margin-right:6px"></i>
                      Arquivos do Paciente
                    </h6>

                    <!-- Upload form -->
                    <form id="form-upload-arquivo" method="post"
                          action="<?=base_url()?>adm/atendimento/upload_arquivo"
                          enctype="multipart/form-data">
                      <input type="hidden" name="id_paciente" value="<?=$dd->id?>">
                      <input type="hidden" name="id_agendamento" value="<?=(isset($id_agenda) ? $id_agenda : 0)?>">

                      <div class="row align-items-end">
                        <div class="col-sm-5">
                          <div class="form-group" style="margin-bottom:0">
                            <label class="mws-form-label">Arquivo <small>(jpg, png, gif, pdf, doc, xls — máx 10MB)</small></label>
                            <input type="file" name="arquivo" id="input-arquivo"
                                   class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx" required>
                          </div>
                        </div>
                        <div class="col-sm-5">
                          <div class="form-group" style="margin-bottom:0">
                            <label class="mws-form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control"
                                   placeholder="Ex: Resultado exame sangue, Receita...">
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <button type="submit" id="btn-upload" class="btn btn-primary btn-block">
                            <span id="upload-txt">Enviar</span>
                            <span id="upload-spin" style="display:none">Enviando...</span>
                          </button>
                        </div>
                      </div>
                    </form>

                    <div id="upload-msg" style="margin-top:10px;display:none"></div>

                    <hr>

                    <!-- Lista de arquivos -->
                    <?php if(isset($arquivos) && $arquivos->num_rows() > 0): ?>
                    <div class="row" id="lista-arquivos">
                      <?php foreach($arquivos->result() as $arq):
                        $is_img = in_array($arq->tipo, ['imagem','jpg','jpeg','png','gif']);
                        $icone  = $is_img ? 'os-icon-image' : ($arq->tipo == 'pdf' ? 'os-icon-file-text' : 'os-icon-database');
                      ?>
                      <div class="col-sm-4 col-md-3" style="margin-bottom:16px" id="arq-<?=$arq->id?>">
                        <div class="element-box" style="padding:12px;text-align:center;position:relative">

                          <?php if($is_img): ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <img src="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>"
                                   style="max-width:100%;max-height:120px;object-fit:cover;border-radius:4px;margin-bottom:6px">
                            </a>
                          <?php else: ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <i class="os-icon <?=$icone?>" style="font-size:48px;color:#047bf8;display:block;margin-bottom:6px"></i>
                            </a>
                          <?php endif; ?>

                          <div style="font-size:12px;color:#555;word-break:break-all;margin-bottom:4px">
                            <?=htmlspecialchars($arq->nome_original)?>
                          </div>
                          <?php if($arq->descricao): ?>
                          <div style="font-size:11px;color:#888;margin-bottom:6px;font-style:italic">
                            <?=htmlspecialchars($arq->descricao)?>
                          </div>
                          <?php endif; ?>
                          <div style="font-size:10px;color:#aaa;margin-bottom:8px">
                            <?=date('d/m/Y H:i', strtotime($arq->dt_cadastro))?>
                          </div>

                          <div class="btn-group btn-group-sm w-100">
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>"
                               target="_blank" class="btn btn-sm btn-outline-primary" title="Visualizar">
                              <i class="os-icon os-icon-eye"></i>
                            </a>
                            <a href="<?=base_url()?>adm/atendimento/del_arquivo/<?=$arq->id?>"
                               class="btn btn-sm btn-outline-danger btn-del-arq"
                               data-id="<?=$arq->id?>" title="Excluir"
                               onclick="return confirm('Excluir este arquivo?')">
                              <i class="os-icon os-icon-x"></i>
                            </a>
                          </div>

                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted" id="sem-arquivos">Nenhum arquivo enviado ainda.</p>
                    <?php endif; ?>

                  </div>
                </div>
              </div>
            </div>
            <!-- ═══ FIM ARQUIVOS ═══ -->

            <!--------------------
            END - Sidebar
            -------------------->
          </div>
        </div>
      </div>
      <div class="display-type"></div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>bower_components/moment/moment.js"></script>
    <script src="<?=base_url()?>bower_components/chart.js/dist/Chart.min.js"></script>
    <script src="<?=base_url()?>bower_components/select2/dist/js/select2.full.min.js"></script>
    <script src="<?=base_url()?>bower_components/jquery-bar-rating/dist/jquery.barrating.min.js"></script>
    <script src="<?=base_url()?>bower_components/ckeditor/ckeditor.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap-validator/dist/validator.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="<?=base_url()?>bower_components/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <script src="<?=base_url()?>bower_components/dropzone/dist/dropzone.js"></script>
    <script src="<?=base_url()?>bower_components/editable-table/mindmup-editabletable.js"></script>

    <script src="<?=base_url()?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <script src="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/tether/dist/js/tether.min.js"></script>
    <script src="<?=base_url()?>bower_components/slick-carousel/slick/slick.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/alert.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/button.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/carousel.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/modal.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tab.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tooltip.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/popover.js"></script>
    <script src="<?=base_url()?>js/demo_customizer.js?version=4.5.0"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>

    <script>
      $(document).ready(function() {
          $('.table__').DataTable({
              "pageLength": 10,
              "order": [], // evita ordenação automática
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
              }
          });

          $('.table').DataTable({
              "paging": true,
              "lengthChange": true,
              "searching": true,
              "ordering": true,
              "order": [[0, 'desc']],
              "info": true,
              "autoWidth": false,
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
              },
          });
      });
      </script>


    <script>
    $('#form-upload-arquivo').on('submit', function(e){
      e.preventDefault();
      var $btn = $('#btn-upload');
      $btn.prop('disabled', true);
      $('#upload-txt').hide();
      $('#upload-spin').show();
      $('#upload-msg').hide();

      var fd = new FormData(this);
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(resp){
          var r = JSON.parse(resp);
          $('#upload-msg')
            .removeClass('alert-success alert-danger')
            .addClass(r.ok ? 'alert-success alert-' : 'alert alert-danger')
            .addClass('alert ' + (r.ok ? 'alert-success' : 'alert-danger'))
            .text(r.msg)
            .show();
          if(r.ok){
            $('#form-upload-arquivo')[0].reset();
            // recarrega para mostrar novo arquivo
            setTimeout(function(){ location.reload(); }, 800);
          }
        },
        error: function(){
          $('#upload-msg').addClass('alert alert-danger').text('Erro ao enviar. Tente novamente.').show();
        },
        complete: function(){
          $btn.prop('disabled', false);
          $('#upload-txt').show();
          $('#upload-spin').hide();
        }
      });
    });
    </script>
  </body>
</html>
