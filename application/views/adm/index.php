<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<title>Pedidos</title>

<!-- Viewport Metatag -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<!-- Plugin Stylesheets first to ease overrides -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/colorpicker/colorpicker.css" media="screen">

<!-- Required Stylesheets 
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/bootstrap.min.css" media="screen"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"  crossorigin="anonymous"> -->

<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/fonts/ptsans/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/fonts/icomoon/style.css" media="screen">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/mws-style.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/icons/icol16.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/icons/icol32.css" media="screen">

<!-- Demo Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/demo.css" media="screen">

<!-- jQuery-UI Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>jui/css/jquery.ui.all.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>jui/jquery-ui.custom.css" media="screen">

<!-- Theme Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/mws-theme.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/adm/themer.css" media="screen">

</head>

<body>

<?php include("includes/adm/header.php"); ?>

    <!-- Start Main Wrapper -->
    <div id="mws-wrapper">
    
    	<!-- Necessary markup, do not remove -->
		<div id="mws-sidebar-stitch"></div>
		<div id="mws-sidebar-bg"></div>
        
        <?php include('includes/adm/sidebar.php');?> 
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">


                <h2>Pedidos (<?=$pedidos->num_rows()?>)</h2>

                <? if($pedidos->num_rows() > 0){ ?>

                    <!-- Inner Container Start -->
                    <div class="container">
                        
                        <div class="mws-panel grid_8">
                            <div class="mws-panel-header">
                                <span><i class="icon-table"></i>Lista de Produtos</span>
                            </div>
                            <div class="mws-panel-body no-padding">
                                <table class="mws-datatable-fn mws-table" id="tabela">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Data</th>
                                            <th>Vendedor</th>
                                            <th>Cliente</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($pedidos->result() as $pedido) {
                                        ?>
                                        <tr class="thead-dark">
                                            <td><?php echo $pedido->id; ?></td>
                                            <td><?php echo $this->padrao_model->converte_data(substr($pedido->dt, 0,10)); ?> 
                                                <?php echo substr($pedido->dt, 10,10); ?>
                                                
                                            </td>

                                            <td><?php echo $this->padrao_model->get_by_id($pedido->id_user,'MJ_users')->row()->nome; ?></td>
                                            <td><?php echo $this->padrao_model->get_by_id($pedido->id_comprador,'MJ_users')->row()->razao_social; ?></td>
                                            <td>

                                                <?php /* if( $pedido->status == "0"){ ?>                 
                                                    <span class="badge badge-danger">Aguardando</span>
                                                <? } ?>
                                                <?php if( $pedido->status == "1"){ ?>                 
                                                    <span class="badge badge-warning">Atendido</span>
                                                <? } ?>
                                                <?php if( $pedido->status == "2"){ ?>                 
                                                    <span class="badge badge-primary">Enviado</span>
                                                <? } ?>
                                                <?php if( $pedido->status == "2"){ ?>                 
                                                    <span class="badge badge-success">Finalizado</span>
                                                <? } */ ?>
                                                <!--
                                                <div class="dropdown">
                                                  <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Aguardando
                                                  </button>
                                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Aguardando</a>
                                                    <a class="dropdown-item" href="#">Atendido</a>
                                                    <a class="dropdown-item" href="#">Finalizado</a>
                                                    
                                                  </div>
                                                </div> -->
                                                <form action="<?=base_url()?>adm/home/set_status_pedido" method="post" id="form_pedido<?=$pedido->id?>">
                                                    <input type="hidden" value="<?=$pedido->id?>" name="id_pedido">
                                                    <select name="status" class="set_status" title="<?=$pedido->id?>" >
                                                        <? if($pedido->status == '0'){ $sel_opt = 'selected="selected"'; }else{ $sel_opt = "";  } ?>
                                                        <option value="0" <?=$sel_opt?> style="color: red" >Aguardando</option>
                                                        <? if($pedido->status == '1'){ $sel_opt = 'selected="selected"'; }else{ $sel_opt = "";  } ?>
                                                        <option value="1" <?=$sel_opt?> style="color: orange" >Atendido</option>
                                                        <? if($pedido->status == '2'){ $sel_opt = 'selected="selected"'; }else{ $sel_opt = "";  } ?>
                                                        <option value="2" <?=$sel_opt?> style="color: green">Finalizado</option>
                                                    </select>
                                                    <input type="submit" name="" value="OK" class="btn btn-primary">
                                                </form>

                                            </td>
                                            
                                            <td>
                                            <span class="btn-group">
                                                    <!-- <a href="#" class="btn btn-small"><i class="icon-search"></i></a> -->
                                                    <a target="_blank" href="<?php echo base_url().'adm/produtos/pedido/'.$pedido->id; ?>" class="btn btn-small"><i class="icon-eye-open"></i></a>
                                                    <!--<a target="_blank" href="<?php echo base_url().'adm/produtos/pedido/'.$pedido->id; ?>" class="btn btn-small"><i class="icon-trash"></i></a> -->
                                                </span>
                                            </td>
                                        </tr>
                                        <!-- PLISTA DE PRODUTOS -->
                                        <tr>
                                                <td><?php echo $pedido->id; ?></td>
                                                <td><?php echo $this->padrao_model->converte_data(substr($pedido->dt, 0,10)); ?> 
                                                    <?php echo substr($pedido->dt, 10,10); ?>
                                                    
                                                </td>

                                                <td colspan="3">
                                                    <? 
                                                    $carrinho = $this->padrao_model->get_by_matriz('id_pedido',$pedido->id,'carrinho');
                                                    $total = 0; foreach($carrinho->result() as $car){ 
                                                    $dd_pro = $this->padrao_model->get_by_id($car->id_produto,'produtos');
                                                    $produto = $dd_pro->row();
                                                    $valor = $car->qtd * $produto->preco_venda;
                                                    $total += $valor;
                                                    ?>
                                                    <? #=$produto->modelo?><br />

                                                    <div class="container">
                                                        <div class="row"  style="color:#000;font-size: 11px">
                                                            
                                                            <!--<div class="col-sm">
                                                                <img class="img-responsive" src="<?=base_url()?>imagens/produtos/min/<?=$produto->img_portfolio?>" style="width: 20%">
                                                            </div>-->

                                                            <div class="col-sm" align="left" style="text-align: left;max-width: 30%">
                                                                <?=$produto->modelo?>
                                                            </div>

                                                            

                                                            <!--<div class="col-sm"  style="max-width: 25%">
                                                                R$ <?=number_format($produto->preco_venda, 2, ',', '.')?>
                                                            </div>-->
                                                            <div class="col-sm" style="max-width: 5%">
                                                                <strong style="font-size: "><?=$car->qtd?></strong>
                                                            </div>
                                                            <!--
                                                            <div class="col-sm" style="max-width: 25%">
                                                                R$ <?=number_format($valor, 2, ',', '.')?>
                                                            </div>
                                                            <div class="col-sm" style="max-width: 25%">
                                                                R$ <?=number_format($valor, 2, ',', '.')?>
                                                            </div>-->
                                                        
                                                            <div class="col-sm" style="max-width: 25%">
                                                                
                                                                <!--<a href="<?=base_url()?>user/rem/<?=$car->id?>" style="color:red">Remover</a> -->
                                                            </div>

                                                        </div>

                                                    </div>


                                                    <? } ?>
                                                </td>
                                                <td>

                                                    

                                                
                                                
                                            </tr>
                                        <?php
                                        } // x foreach
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                    <!-- Inner Container End -->



                <? } ?>
            

            </div>
            <!-- (x) Inner Container End -->
                       
            <!-- Footer -->
      		<?php include('includes/adm/footer.php');?>

            
        </div>
        <!-- (x) Main Container End -->
        
    </div>
    <!-- (x) Start Main Wrapper -->

    <!-- JavaScript Plugins -->
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery.mousewheel.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery.placeholder.min.js"></script>
    <!--<script src="<?php echo base_url(); ?>custom-plugins/fileinput.js"></script>-->
    
    <!-- jQuery-UI Dependent Scripts -->
    <script src="<?php echo base_url(); ?>jui/js/jquery-ui-1.9.2.min.js"></script>
    <script src="<?php echo base_url(); ?>jui/jquery-ui.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>jui/js/jquery.ui.touch-punch.js"></script>

    <!-- Plugin Scripts -->
    <script src="<?php echo base_url(); ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <!--[if lt IE 9]>
    <script src="js/libs/excanvas.min.js"></script>
    <![endif]-->
    <!--<script src="<?php echo base_url(); ?>plugins/flot/jquery.flot.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/flot/plugins/jquery.flot.tooltip.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/flot/plugins/jquery.flot.pie.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/flot/plugins/jquery.flot.stack.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/flot/plugins/jquery.flot.resize.min.js"></script>-->
    <script src="<?php echo base_url(); ?>plugins/colorpicker/colorpicker-min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/validate/jquery.validate-min.js"></script>
    <!--<script src="<?php echo base_url(); ?>custom-plugins/wizard/wizard.min.js"></script>-->

    <!-- Core Script -->
    <script src="<?php echo base_url(); ?>js/adm/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/core/mws.js"></script>


    <!-- Demo Scripts (remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/adm/demo/demo.dashboard.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".set_status").change(function(){
                var status = $(this).val();
                var id = $(this).attr('title');
                $("#form_pedido"+id).submit();
                //alert(id);
            })
           // alert("OK 4");
        })
    </script>
 
		

</body>

</html>