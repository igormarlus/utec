<!DOCTYPE html>
<html>
  <head>
    <title>Dashboard Clicklinica</title>
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
    <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/main.css?version=4.5.0" rel="stylesheet">
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
              <a href="<?=base_url()?>adm/usuarios/dash">Home</a>
            </li>
            
            <li class="breadcrumb-item">
              <span>Dashboard</span>
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
                            Recentes
                          </option>
                          <!--
                          <option value="Active">
                            Last Week 
                          </option>
                          <option value="Cancelled">
                            Last 30 Days
                          </option>-->
                        </select>
                      </form>
                    </div>
                    <div class="row">

                      
                      
                    </div>
                    <h6 class="element-header">
                      Resumo
                    </h6>
                    <div class="element-content">
                      <div class="row">
                        <div class="col-sm-6 col-xxxl-6">
                          <a class="element-box el-tablo" href="<?=base_url()?>usuarios/dash">
                            <div class="label">
                              Meus Atendimentos
                            </div>
                            <div class="value">
                              <?=$carrinhos_pagos->num_rows()?>
                            </div>
                            <!--
                            <div class="trending trending-up-basic">
                              <span>4%</span><i class="os-icon os-icon-arrow-up2"></i>
                            </div>
                          -->
                          </a>
                        </div>
                        <div class="col-sm-6 col-xxxl-6">
                          <a class="element-box el-tablo" href="<?=base_url()?>adm/usuarios/financeiro/">
                            <div class="label">
                              Saldo mensal
                            </div>
                            <div class="value">
                              R$ <?=number_format($saldo_mensal->row()->total,2,",",".")?>
                            </div>
                            <!--
                            <div class="trending trending-down-basic">
                              <span>12%</span><i class="os-icon os-icon-arrow-down"></i>
                            </div>
                          -->
                          </a>
                        </div>
                        <div class="col-sm-6 col-xxxl-6">
                          <a class="element-box el-tablo" href="<?=base_url()?>adm/marcacoes/pacientes">
                          <div class="label">
                              Meus Clientes
                            </div>
                            <div class="value">
                              <?=$clientes->num_rows()?>
                              <? #=$carrinhos_pendentes->num_rows()?>
                            </div>
                            <!--
                            <div class="trending trending-down-basic">
                              <span>9%</span><i class="os-icon os-icon-arrow-down"></i>
                            </div>
                          -->
                          </a>
                        </div>
                        <div class="col-sm-6 col-xxxl-6">
                          <a class="element-box el-tablo" href="<?=base_url()?>adm/usuarios/financeiro/">
                            <div class="label">
                              Saldo Geral
                            </div>
                            <div class="value">
                              R$ <?=number_format($saldo->row()->total,2,",",".")?>
                            </div>
                            <!--
                            <div class="trending trending-up-basic">
                              <span>12%</span><i class="os-icon os-icon-arrow-up2"></i>
                            </div>
                          -->
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    <h6 class="element-header">
                      Últimas marcações 
                    </h6>
                    <div class="element-box">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Data Reg.</th>
                            <th>Data Marc.</th>
                            <th>Hora Marc</th>
                              <th>ID PAC</th>
                              <th>
                                Paciente
                              </th>
                              <th>
                                Tipo
                              </th>
                              <? if($this->session->userdata('nivel') == 4){ ?>
                                <th>Nome</th>
                              <? } ?>
                              <!--<th>Anexos</th>-->
                              
                              <th class="text-right">
                                -
                              </th>

                          </thead>
                          <tbody>
                            <? if($carrinhos->num_rows() > 0){ ?>
                              <? foreach($carrinhos->result() as $car){ ?>
                                <tr>
                                  <td><?=$car->id?></td>
                                  <td>
                                    <? 
                                    if($car->status == 0){ echo "<strong style='color:red'>Pendente</strong>"; }
                                    if($car->status == 1){ echo "<strong style='color:green'>Confirmada</strong>"; }
                                    ?>
                                    
                                  </td>
                                  <td><?=$this->padrao_model->converte_data(substr($car->dt,0,10))?></td>
                                  <td><?=$this->padrao_model->converte_data(substr($car->data_marcacao,0,10))?></td>
                                  <td><?=$car->hora_marcacao?></td>
                                  <td class="nowrap">
                                    <?=$car->id_user?>
                                  </td>
                                  <td>
                                    <?=$this->padrao_model->get_by_id($car->id_user,'pi_whats_users')->row()->nome?>                                    
                                  </td>
                                  <td> -- <? #=$this->padrao_model->get_by_id($car->tipo,'marcacao_tipos')->row()->nome?></td>
                                  <? if($this->session->userdata('nivel') == 4){ ?>
                                  <td>
                                    <? 
                                    if($car->tipo == 1){ 
                                      echo $this->padrao_model->get_by_id($car->id_tipo,'consultas')->row()->nome;
                                    }
                                    if($car->tipo == 2){ 
                                      echo $this->padrao_model->get_by_id($car->id_tipo,'exames')->row()->nome;
                                    }

                                    ?>
                                  </td>
                                <? } ?>
                                  <!--<td>

                                    <div class="cell-image-list">
                                      <div class="cell-img" style="background-image: url(<?=base_url()?>img/portfolio9.jpg)"></div>
                                      <div class="cell-img" style="background-image: url(<?=base_url()?>img/portfolio2.jpg)"></div>
                                      <div class="cell-img" style="background-image: url(<?=base_url()?>img/portfolio12.jpg)"></div>
                                      <div class="cell-img-more">
                                        + 5 more
                                      </div>
                                    </div>
                                  </td>-->
                                  
                                  <td class="text-right">
                                    <? # if($car->valor != ""){ echo "R$ ".$car->valor;}  ?>
                                    <?
                                    #$qr_consulta = $this->db->query("SELECT * FROM consultas WHERE nome = '".$car->nm_produto."'");
                                    #if($qr_consulta->num_rows() > 0){ echo "R$ ".$qr_consulta->row()->valor; }
                                    ?>
                                    
                                  </td>
                                </tr>
                              <? } ?>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xxxl-4" style="display: none">
                  <div class="element-wrapper">
                    <h6 class="element-header">
                      Pacientes Novos/Recorrentes
                    </h6>
                    <div class="element-box less-padding">
                      <div class="el-chart-w">
                        <canvas height="120" id="donutChart1" width="120"></canvas>
                        <div class="inside-donut-chart-label">
                          <strong><?=$carrinhos_pagos->num_rows()+$carrinhos_pendentes->num_rows()?></strong><span>Pacientes</span>
                        </div>
                      </div>
                      <div class="el-legend condensed">
                        <div class="row">
                          <div class="col-auto col-xxxxl-6 ml-sm-auto mr-sm-auto">
                            <div class="legend-value-w">
                              <div class="legend-pin legend-pin-squared" style="background-color: #6896f9;"></div>
                              <div class="legend-value">
                                <span>Pagos</span>
                                <div class="legend-sub-value">
                                  30%, <?=$carrinhos_pagos->num_rows()?> pagos
                                </div>
                              </div>
                            </div>
                            <div class="legend-value-w">
                              <div class="legend-pin legend-pin-squared" style="background-color: #85c751;"></div>
                              <div class="legend-value">
                                <span>Pendentes</span>
                                <div class="legend-sub-value">
                                  70%, <?=$carrinhos_pendentes->num_rows()?> Pendentes
                                </div>
                              </div>
                            </div>
                          </div>
                          
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <? if($this->session->userdata('nivel') == 1){ ?>
                  <div class="col-sm-6 d-xxxl-none">
                    <!--START - Top Selling Chart-->
                    <div class="element-wrapper">
                      <h6 class="element-header"> 
                        Marcações (P/ Clínica) 1
                      </h6>
                      <div class="element-box">
                        <div class="el-chart-w">
                          <canvas height="120" id="donutChart" width="120"></canvas>
                          <div class="inside-donut-chart-label">
                            <strong>142</strong><span>Total</span>
                          </div>
                        </div>
                        <div class="el-legend condensed">
                          <div class="row">
                            <div class="col-auto col-xxxxl-6 ml-sm-auto mr-sm-auto col-6">
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #6896f9;"></div>
                                <div class="legend-value">
                                  <span>Dr. Mauro</span>
                                  <div class="legend-sub-value">
                                    14 marcações
                                  </div>
                                </div>
                              </div>
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #85c751;"></div>
                                <div class="legend-value">
                                  <span>Dra. Joana</span>
                                  <div class="legend-sub-value">
                                    26 marcações
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-6 d-lg-none d-xxl-block">
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #806ef9;"></div>
                                <div class="legend-value">
                                  <span>Clínica Cardio</span>
                                  <div class="legend-sub-value">
                                    17 marcações
                                  </div>
                                </div>
                              </div>
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #d97b70;"></div>
                                <div class="legend-value">
                                  <span>-</span>
                                  <div class="legend-sub-value">
                                    12 indefinido
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--END - Top Selling Chart-->
                  </div>

                  <div class="col-sm-6 d-xxxl-none">
                    <!--START - Top Selling Chart-->
                    <div class="element-wrapper">
                      <h6 class="element-header"> 
                        Marcações (P/ Clínica) 1
                      </h6>
                      <div class="element-box">
                        <div class="el-chart-w">
                          <canvas height="120" id="donutChart" width="120"></canvas>
                          <div class="inside-donut-chart-label">
                            <strong>142</strong><span>Total</span>
                          </div>
                        </div>
                        <div class="el-legend condensed">
                          <div class="row">
                            <div class="col-auto col-xxxxl-6 ml-sm-auto mr-sm-auto col-6">
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #6896f9;"></div>
                                <div class="legend-value">
                                  <span>Dr. Mauro</span>
                                  <div class="legend-sub-value">
                                    14 marcações
                                  </div>
                                </div>
                              </div>
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #85c751;"></div>
                                <div class="legend-value">
                                  <span>Dra. Joana</span>
                                  <div class="legend-sub-value">
                                    26 marcações
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-6 d-lg-none d-xxl-block">
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #806ef9;"></div>
                                <div class="legend-value">
                                  <span>Clínica Cardio</span>
                                  <div class="legend-sub-value">
                                    17 marcações
                                  </div>
                                </div>
                              </div>
                              <div class="legend-value-w">
                                <div class="legend-pin legend-pin-squared" style="background-color: #d97b70;"></div>
                                <div class="legend-value">
                                  <span>-</span>
                                  <div class="legend-sub-value">
                                    12 indefinido
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--END - Top Selling Chart-->
                  </div>

                <? } ?>

                <!-- -->



                <? if($this->session->userdata('nivel') == 1){ ?>
                <div class="d-none d-xxxl-block col-xxxl-4">
                  <!--START - Questions per Product-->
                  <div class="element-wrapper">
                    <div class="element-actions">
                      <form class="form-inline justify-content-sm-end">
                        <select class="form-control form-control-sm rounded">
                          <option value="Pending">
                            Today
                          </option>
                          <option value="Active">
                            Last Week 
                          </option>
                          <option value="Cancelled">
                            Last 30 Days
                          </option>
                        </select>
                      </form>
                    </div>

                    <h6 class="element-header">
                      Inventory Stats
                    </h6>
                    <div class="element-box">
                      <div class="os-progress-bar primary">
                        <div class="bar-labels">
                          <div class="bar-label-left">
                            <span class="bigger">Eyeglasses</span>
                          </div>
                          <div class="bar-label-right">
                            <span class="info">25 items / 10 remaining</span>
                          </div>
                        </div>
                        <div class="bar-level-1" style="width: 100%">
                          <div class="bar-level-2" style="width: 70%">
                            <div class="bar-level-3" style="width: 40%"></div>
                          </div>
                        </div>
                      </div>
                      <div class="os-progress-bar primary">
                        <div class="bar-labels">
                          <div class="bar-label-left">
                            <span class="bigger">Outwear</span>
                          </div>
                          <div class="bar-label-right">
                            <span class="info">18 items / 7 remaining</span>
                          </div>
                        </div>
                        <div class="bar-level-1" style="width: 100%">
                          <div class="bar-level-2" style="width: 40%">
                            <div class="bar-level-3" style="width: 20%"></div>
                          </div>
                        </div>
                      </div>
                      <div class="os-progress-bar primary">
                        <div class="bar-labels">
                          <div class="bar-label-left">
                            <span class="bigger">Shoes</span>
                          </div>
                          <div class="bar-label-right">
                            <span class="info">15 items / 12 remaining</span>
                          </div>
                        </div>
                        <div class="bar-level-1" style="width: 100%">
                          <div class="bar-level-2" style="width: 60%">
                            <div class="bar-level-3" style="width: 30%"></div>
                          </div>
                        </div>
                      </div>
                      <div class="os-progress-bar primary">
                        <div class="bar-labels">
                          <div class="bar-label-left">
                            <span class="bigger">Jeans</span>
                          </div>
                          <div class="bar-label-right">
                            <span class="info">12 items / 4 remaining</span>
                          </div>
                        </div>
                        <div class="bar-level-1" style="width: 100%">
                          <div class="bar-level-2" style="width: 30%">
                            <div class="bar-level-3" style="width: 10%"></div>
                          </div>
                        </div>
                      </div>
                      <div class="mt-4 border-top pt-3">
                        <div class="element-actions d-none d-sm-block">
                          <form class="form-inline justify-content-sm-end">
                            <select class="form-control form-control-sm form-control-faded">
                              <option selected="true">
                                Last 30 days
                              </option>
                              <option>
                                This Week
                              </option>
                              <option>
                                This Month
                              </option>
                              <option>
                                Today
                              </option>
                            </select>
                          </form>
                        </div>
                        <h6 class="element-box-header">
                          Inventory History
                        </h6>
                        <div class="el-chart-w">
                          <canvas data-chart-data="13,28,19,24,43,49,40,35,42,46,38,32,45" height="50" id="liteLineChartV3" width="300"></canvas>
                        </div>
                      </div>
                    </div>
                  
                  </div>
                  <!--END - Questions per product                  -->
                </div>
                <? } ?>
              </div>
            
              
            </div>
            <!--------------------
            START - Sidebar
            -------------------->
            <div class="content-panel">


              <div class="content-panel-close">
                <i class="os-icon os-icon-close"></i>
              </div>
              <div class="element-wrapper">
                <h6 class="element-header">
                  Menu
                </h6>
                <div class="element-box-content"><a href="<?=base_url()?>adm/usuarios/edicao/<?=$this->session->userdata('id')?>" class="mr-2 mb-2 btn btn-primary btn-rounded"  style="width: 100%"> Meus dados</a>
                  <a href="<?=base_url()?>adm/usuarios/horarios/" class="mr-2 mb-2 btn btn-primary btn-rounded"   style="width: 100%;margin-left:0px">Horários de atendimento</a>
                  <a href="<?=base_url()?>adm/usuarios/planos/" class="mr-2 mb-2 btn btn-primary btn-rounded" style="width: 100%;margin-left:0px"> Planos de saúde</a>
                  <a href="<?=base_url()?>adm/marcacoes" class="mr-2 mb-2 btn btn-primary btn-rounded" style="width: 100%;margin-left:0px">Meus Atendimentos</a>
                  <a href="<?=base_url()?>adm/marcacoes/pacientes" class="mr-2 mb-2 btn btn-primary btn-rounded" style="width: 100%;margin-left:0px">Meus Pacientes</a>
                  <!--<a href="<?=base_url()?>adm/exames/marcar" class="mr-2 mb-2 btn btn-primary btn-rounded" style="width: 100%;margin-left:0px"> Agenda</a> -->
                  <a href="<?=base_url()?>adm/usuarios/financeiro/" class="mr-2 mb-2 btn btn-primary btn-rounded"  style="width: 100%;margin-left:0px"> Financeiro</a></div>
                  <a href="<?=base_url()?>adm/marcacoes/calendario/" class="mr-2 mb-2 btn btn-primary btn-rounded"  style="width: 100%;margin-left:0px"> Agenda</a></div>
                
              
              <!--------------------
              START - Support Agents
              -------------------->
              <div class='row'>
                <div class="element-wrapper">


                  <h6 class="element-header">
                    Agenda de Hoje
                  </h6>
                  <div class="element-box-tp">
                    <div class="activity-boxes-w">
                      <? if($agenda_hj->num_rows() > 0){ ?>

                        <?  foreach($agenda_hj->result() as $agenda){ ?>
                        <div class="activity-box-w">
                          <div class="activity-time">
                            <?=$agenda->hora_marcacao?>
                          </div>
                          <div class="activity-box">
                            <div class="activity-avatar">
                              <img alt="" src="<?=base_url()?>img/avatar1.jpg">
                            </div>
                            <div class="activity-info">
                              <div class="activity-role">
                                <?=$this->padrao_model->get_by_id($agenda->id_user,'pi_whats_users')->row()->nome?>
                              </div>
                              <strong class="activity-title">
                                Resumo
                              </strong>
                            </div>
                          </div>
                        </div>

                        <? } ?>
                      <? } ?>
                      <!--
                      <div class="activity-box-w">
                        <div class="activity-time">
                          2 Hours
                        </div>
                        <div class="activity-box">
                          <div class="activity-avatar">
                            <img alt="" src="<?=base_url()?>img/avatar2.jpg">
                          </div>
                          <div class="activity-info">
                            <div class="activity-role">
                              Ben Gossman
                            </div>
                            <strong class="activity-title">Posted Comment</strong>
                          </div>
                        </div>
                      </div>
                      <div class="activity-box-w">
                        <div class="activity-time">
                          5 Hours
                        </div>
                        <div class="activity-box">
                          <div class="activity-avatar">
                            <img alt="" src="<?=base_url()?>img/avatar3.jpg">
                          </div>
                          <div class="activity-info">
                            <div class="activity-role">
                              Phil Nokorin
                            </div>
                            <strong class="activity-title">Opened New Account</strong>
                          </div>
                        </div>
                      </div>
                      <div class="activity-box-w">
                        <div class="activity-time">
                          2 Days
                        </div>
                        <div class="activity-box">
                          <div class="activity-avatar">
                            <img alt="" src="<?=base_url()?>img/avatar4.jpg">
                          </div>
                          <div class="activity-info">
                            <div class="activity-role">
                              Jenny Miksa
                            </div>
                            <strong class="activity-title">Uploaded Image</strong>
                          </div>
                        </div>
                      </div>
                    -->
                    </div>
                  </div>
                </div>
              </div>
              <!--------------------
              END - Recent Activity
              <!--------------------
              START - Support Agents
              -------------------->
              <div class='row'>
                <div class="element-wrapper">
                  <h6 class="element-header">
                    Marcações não finalizadas
                  </h6>



                  <div class="element-box-tp">
                    <? if($n_finalizadas->num_rows() > 0){ ?>
                      <? foreach($n_finalizadas->result() as $not_fim){ ?>
                        <?
                        $dd_user = $this->padrao_model->get_by_id($not_fim->id_user,"pi_whats_users")->row();

                        ?>
                        <div class="profile-tile">
                          <a class="profile-tile-box" href="users_profile_small.html">
                            <!--
                            <div class="pt-avatar-w">
                              <img alt="" src="<?=base_url()?>img/avatar1.jpg"> 
                            </div>
                            -->
                            <div class="pt-user-name">
                              <?=$dd_user->nome?>

                            </div>
                          </a>
                          <div class="profile-tile-meta">
                            <ul>
                              <li>
                                Data marc.:<strong><?=$this->padrao_model->converte_data($not_fim->data_marcacao)?></strong>
                              </li>
                              <li>
                                Hora marc.:<strong><?=$not_fim->hora_marcacao?></strong>
                              </li>
                              <li>
                                Data Reg.:<strong><?=substr($not_fim->dt,0,10)?></strong>
                              </li>
                              
                              <li>
                                Telefone:<strong><?=$dd_user->telefone?></strong>
                              </li>
                              <li>
                                Marcações Realizadas:<strong><a href="#"><?=$this->padrao_model->get_by_matriz('id_cliente',$not_fim->user,"carrinho")->num_rows()?></a></strong>
                              </li>
                            </ul>
                            <div class="pt-btn">
                              <a class="btn btn-success btn-sm" href="tel:<?=$dd_user->telefone?>">Ligar</a>
                            </div>
                          </div>
                        </div>

                      <? } ?>
                    <? } ?>

                    <!--<div class="profile-tile">
                      <a class="profile-tile-box" href="users_profile_small.html">
                        <div class="pt-avatar-w">
                          <img alt="" src="<?=base_url()?>img/avatar3.jpg">
                        </div>
                        <div class="pt-user-name">
                          Ben Gossman
                        </div>
                      </a>
                      <div class="profile-tile-meta">
                        <ul>
                          <li>
                            Last Login:<strong>Offline</strong>
                          </li>
                          <li>
                            Tickets:<strong><a href="apps_support_index.html">9</a></strong>
                          </li>
                          <li>
                            Response Time:<strong>3 hours</strong>
                          </li>
                        </ul>
                        <div class="pt-btn">
                          <a class="btn btn-secondary btn-sm" href="apps_full_chat.html">Send Message</a>
                        </div>
                      </div>
                    </div>-->
                  </div>
                </div>
              </div>
              <!--------------------
              END - Recent Activity
              --------------------><!--------------------
              START - Team Members
              -------------------->
              <? if($dd_user->id_setor == 1){ ?>
                <div class="element-wrapper">
                  <h6 class="element-header">
                    Profissionais (P/ Clínica)
                  </h6>
                  <div class="element-box-tp">
                    <div class="input-search-w">
                      <input class="form-control rounded bright" placeholder="Search team members..." type="search">
                    </div>
                    <div class="users-list-w">
                      <div class="user-w with-status status-green">
                        <div class="user-avatar-w">
                          <div class="user-avatar">
                            <img alt="" src="img/avatar1.jpg">
                          </div>
                        </div>
                        <div class="user-name">
                          <h6 class="user-title">
                            John Mayers
                          </h6>
                          <div class="user-role">
                            Account Manager
                          </div>
                        </div>
                        <a class="user-action" href="users_profile_small.html">
                          <div class="os-icon os-icon-email-forward"></div>
                        </a>
                      </div>
                      <div class="user-w with-status status-green">
                        <div class="user-avatar-w">
                          <div class="user-avatar">
                            <img alt="" src="img/avatar2.jpg">
                          </div>
                        </div>
                        <div class="user-name">
                          <h6 class="user-title">
                            Ben Gossman
                          </h6>
                          <div class="user-role">
                            Administrator
                          </div>
                        </div>
                        <a class="user-action" href="users_profile_small.html">
                          <div class="os-icon os-icon-email-forward"></div>
                        </a>
                      </div>
                      <div class="user-w with-status status-red">
                        <div class="user-avatar-w">
                          <div class="user-avatar">
                            <img alt="" src="img/avatar3.jpg">
                          </div>
                        </div>
                        <div class="user-name">
                          <h6 class="user-title">
                            Phil Nokorin
                          </h6>
                          <div class="user-role">
                            HR Manger
                          </div>
                        </div>
                        <a class="user-action" href="users_profile_small.html">
                          <div class="os-icon os-icon-email-forward"></div>
                        </a>
                      </div>
                      <div class="user-w with-status status-green">
                        <div class="user-avatar-w">
                          <div class="user-avatar">
                            <img alt="" src="img/avatar4.jpg">
                          </div>
                        </div>
                        <div class="user-name">
                          <h6 class="user-title">
                            Jenny Miksa
                          </h6>
                          <div class="user-role">
                            Lead Developer
                          </div>
                        </div>
                        <a class="user-action" href="users_profile_small.html">
                          <div class="os-icon os-icon-email-forward"></div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              <? } ?>
              </div>
              <!--------------------
              END - Team Members
              -------------------->
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


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5bQmFBGhYqyB6_7HMC_OYiO8s77tvGTI"></script>
    <script>
      $(document).ready(function(){
          var latitude = -7.9971294;
          var longitude =  -34.9023988;
          var loc = new google.maps.LatLng(latitude, longitude);
          console.log(loc);

          $.get({
                url: 'http://nominatim.openstreetmap.org/reverse?',
                method: 'get',
                crossOrigin: true,
                type: 'json',
                data: {
                    format: 'json',
                    lat: latitude,
                    lon: longitude,
                    addressdetails: 1,
                    'accept-language': 'pt-BR',
                    zoom: 18
                }
            }).then(function (response) {
                  console.log(response);
              msg_el.innerHTML = response.display_name;
            }).fail(function (err, msg) {
                console.log(err, msg);
            });

        //alert("OK");
      })
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
