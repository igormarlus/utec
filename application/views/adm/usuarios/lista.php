<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Usuários da Plataforma | UTEC</title>

<!-- Plugin Stylesheets first to ease overrides -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/colorpicker/colorpicker.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>custom-plugins/picklist/picklist.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/imgareaselect/css/imgareaselect-default.css" media="screen">


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/select2/select2.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/ibutton/jquery.ibutton.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/cleditor/jquery.cleditor.css" media="screen">

<!-- Required Stylesheets -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/bootstrap.min.css" media="screen">
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
                
            	<div class="mws-panel grid_8">
                	<div class="mws-panel-header">
                    	<span><i class="icon-table"></i> Usuários da plataforma</span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <table class="mws-datatable-fn mws-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Login</th>
                                    <th>E-mail</th>
                                    <th>Perfil</th>   
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php 
                                foreach ($usuarios->result() as $usuario) {
								?>
                                <tr>
                                    <td><?php echo $usuario->nome; ?></td>
                                    <td><?php echo $usuario->login; ?></td>
                                    <td><?php echo $usuario->email; ?></td>
                                    <td><?php echo $usuario->nivel; ?></td>
                                    <td>
                                    <span class="btn-group">
                                            <!-- <a href="#" class="btn btn-small"><i class="icon-search"></i></a> -->
                                            <a href="<?php echo base_url().'adm/usuarios/edicao/'.$usuario->id; ?>" class="btn btn-small"><i class="icon-pencil"></i></a>
                                            <a href="<?php echo base_url().'adm/usuarios/remover/'.$usuario->id; ?>" class="btn btn-small"><i class="icon-trash"></i></a>
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
            <!-- Inner Container End -->
                       
            <!-- Footer -->
      		<?php include('includes/adm/footer.php');?>

            
        </div>
        <!-- Main Container End -->
        
    </div>

    <!-- JavaScript Plugins -->
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery.mousewheel.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/libs/jquery.placeholder.min.js"></script>
    <script src="<?php echo base_url(); ?>custom-plugins/fileinput.js"></script>
    
    <!-- jQuery-UI Dependent Scripts -->
    <script src="<?php echo base_url(); ?>jui/js/jquery-ui-1.9.2.min.js"></script>
    <script src="<?php echo base_url(); ?>jui/jquery-ui.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>jui/js/jquery.ui.touch-punch.js"></script>

     <!-- Plugin Scripts -->
    <script src="<?php echo base_url(); ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/colorpicker/colorpicker-min.js"></script>


    <!-- Core Script -->
    <script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/core/mws.js"></script>

    <!-- Themer Script (Remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/adm/core/themer.js"></script>
    	
    <!-- Demo Scripts (remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/adm/demo/demo.table.js"></script>
 
		
</body>
</html>
