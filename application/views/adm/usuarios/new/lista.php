<!DOCTYPE html>
<html>
  <head>
    <title>Cadastro de Usuarios</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="cadastro de usuarios utec saude" name="keywords">
    <meta content="Tamerlan Soziev" name="author">
    <meta content="Lista e gestao de usuarios da operacao clinica." name="description">
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
        
        
        <? include("includes/adm/menu.php"); ?>
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
              <a href="<?=base_url()?>adm/usuarios/dash">Painel</a>
            </li>
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/rel/<?=$nivel?>"><?=$this->padrao_model->get_by_matriz('nivel',$nivel,'usuarios_niveis')->row()->nome?></a>
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
                      <a href="<?=base_url()?>adm/usuarios/cadastro/<?=$nivel?>" class="btn btn-success">Novo registro</a>
                    </p>

                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    
                    <div class="element-box">
                      <div class="table-responsive">
                          
                        <table class="table table-lightborder" id="">
                            <thead>
                                <tr>
                                    <th>Dt. Cadastro</th>
                                    <th>ID</th>
                                    
                                    <!--<th>Vídeo</th>-->
                                    <th>Foto</th>

                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Login</th>
                                    <th>Acessar</th>
                                    <!-- <th>Produtos</th>
                                    <th>Solicitações</th> -->
                                    
                                    <th>Telefone</th>   
                                    <th>Cad. Por</th>
                                    
                                    
                                    <th align="center" style="text-align: center;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                                foreach ($usuarios->result() as $usuario) {
                ?>
                                <tr>
                                  <td><?=$usuario->dt_cadastro?></td>
                                  <td><?=$usuario->id?></td>
                                  
                                  <!--<td>
                                        <? /* if($usuario->video != ""){ ?>
                                            <a target="_blank" href="<?=base_url()?>uploads/<?=$usuario->video?>" alt="Foto usuário"><?=substr($usuario->video,0,10)?></a>
                                        <? }else{ ?>
                                            Sem Vídeo
                                        <? } */ ?>
                                    </td>-->
                                  <td>
                                    <? if($usuario->img != ""){ ?>
                                            <a target="_blank" href="<?=base_url()?>imagens/usuarios/<?=$usuario->img?>" ><img src="<?=base_url()?>imagens/usuarios/min/<?=$usuario->img?>" alt="Foto usuário"></a>
                                        <? }else{ ?>
                                            Sem foto
                                        <? } ?>
                                  </td>
                                    
                                    <td><?php echo $usuario->nome; ?></td>

                                    <td>
                                        <a href="<?php echo base_url().'index.php/adm/usuarios/set_status/'.$usuario->id.'/'.$usuario->status; ?>" class="btn btn-small">
                                          <? if($usuario->status == 1){ ?>
                                              <!--<i class="os-icon os-icon-check"></i>-->
                                              <i class="os-icon os-icon-check-circle"  title="Ativo" style="color:green"></i>
                                           <? }else{ ?>
                                              <i class="os-icon os-icon-power" title="Desativado" style="color:red"></i>
                                           <? } ?>
                                        </a>
                                        
                                    </td>

                                    <td><?php echo $usuario->login; ?></td>
                                    <td>
                                      <? if($this->session->userdata('nivel') == 1){ ?>
                                          <a href="<?=base_url()?>admin/logar_como/<?=$usuario->id?>" class="btn btn-primary btn-sm" target="_blank">Acessar</a>
                                        <? } ?>
                                    </td>
                                    <!-- <td>
                                        <?php 
                                        #$qr_produtos = $this->padrao_model->get_by_matriz('id_user',$usuario->id,'produtos')->num_rows();
                                        #echo $qr_produtos;
                                        ?>
                                        --
                                    </td>

                                    <td>
                                        <?php 
                                        #$qr_pedidos = $this->padrao_model->get_by_matriz('id_cliente',$usuario->id,'pedidos')->num_rows();
                                        #echo $qr_pedidos;
                                        ?>
                                    </td> -->
                                    
                                    <td title="<?php #echo $usuario->device; ?>">
                                        <?
                                        $arr_replcae_tel = array("-"," ","+","(",")"); 
                                        $tel_trat = str_replace($arr_replcae_tel, "",$usuario->telefone);
                                        ?>
                                        <a href="https://api.whatsapp.com/send?phone=55<?=$tel_trat?>" target="_blank">
                                            <?php echo $usuario->telefone; ?>                                           
                                        </a> 
                                    </td>

                                    <td><?=$this->padrao_model->get_by_id($usuario->id_user,'usuarios')->row()->nome?></td>
                                    
                                    

                                    <td>
                                      
                                    <span class="btn-group">
                                            <!-- <a href="#" class="btn btn-small"><i class="icon-search"></i></a> -->
                                            <? if($usuario->nivel == 5){ ?>
                                            <a href="<?php echo base_url().'index.php/adm/usuarios/prontuario/'.$usuario->id; ?>" class="btn btn-small" style="color:blue" target="_blank">
                                                <i class="os-icon os-icon-edit"></i>
                                                Prontuário
                                                <!-- <i class="icon-tag"></i> -->
                                            </a>

                                            <? } ?>
                                            
                                            <a href="<?php echo base_url().'adm/usuarios/edicao/'.$usuario->id; ?>" class="btn btn-small" style="color:orange"><i class="os-icon os-icon-edit"></i>Edição</a>
                                            <a href="<?php echo base_url().'adm/usuarios/remover/'.$usuario->id; ?>" class="btn btn-small" style="color:red"><i class="os-icon os-icon-x-square"></i> Remover</a>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                }
                ?>
                            </tbody>
                        </table>



                      </div>
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
