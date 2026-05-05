<!DOCTYPE html>
<html>
  <head>
    <title>Usuários</title>
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
              <a href="<?=base_url()?>adm/usuarios/dash">Novo atendimento</a>
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
                      <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$dd->id?>" class="btn btn-success">Relatório Agendamento</a>
                    </p>

                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    
                    <div class="element-box">

                      <!-- FORM -->

                      <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/cadastrar" enctype='multipart/form-data'>

                        <input type="number" value="<?=$dd->id?>" name="id_paciente">
                          <h5 class="form-header">
                            Informações do atendimento
                          </h5>

                          <div class="form-desc">
                            Preencha as informações corretamente.
                          </div>

                          <div class="row">

                            <div class="col-sm-12">
                              <div class="form-group bordered">
                                <input type="text" class="form-control" disabled value="<?=$dd->nome?>">  
                              </div>
                            </div>
                            
                          </div>
                            
                          <hr>

                          <div class="row">

                            <div class="col-sm-6">
                              <div class="form-group">
                                  <label class="mws-form-label">Prestador</label>
                                  <div class="mws-form-item">
                                    
                                      <select name="id_prestador" class="form-control" >
                                        <? foreach($prestadores->result() as $prest){ ?>
                                          <option value="<?=$prest->id?>"><?=$prest->nome?></option>
                                        <? } ?>
                                        
                                      </select>
                                  </div>
                              </div>
                            </div>

                            <div class="col-sm-6">
                              <div class="form-group">
                                  <label class="mws-form-label">Tipo</label>
                                  <div class="mws-form-item">
                                    
                                      <select name="tipo" class="form-control" >
                                        <option value="exame">Exame</option>
                                        <option value="Consulta">Consulta</option>
                                      </select>
                                  </div>
                              </div>
                            </div>

                          </div>

                          
                            <div class="row">

                              

                              <div class="col-sm-6">
                                <div class="form-group bordered">
                                    <label class="mws-form-label">Data </label>
                                    <div class="mws-form-item">
                                        <input type="date" name="data_agenda" class="form-control" placeholder="DD/MM/AAAA">
                                    </div>
                                </div>  
                              </div>  

                              <div class="col-sm-6">
                                <div class="form-group bordered">
                                    <label class="mws-form-label">Hora </label>
                                    <div class="mws-form-item">
                                        <input type="time"  name="hora_agenda" class="form-control" placeholder="00:00">
                                    </div>
                                </div>  
                              </div>  

                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label class="mws-form-label"> </label>
                                    <div class="mws-form-item">
                                      <button class="btn btn-primary" type="submit"> Agendar</button>
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

              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    
                    <div class="element-box">


                    </div>
                  </div>
                </div>
                
                

            </div>
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
              "info": true,
              "autoWidth": false,
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
              },
          });
      });
      </script>


    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      
      ga('create', 'UA-XXXXXXXX-9', 'auto');
      ga('send', 'pageview');
    </script>
  </body>
</html>
