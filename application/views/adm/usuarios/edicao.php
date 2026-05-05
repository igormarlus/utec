<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NuvemADMLab </title>

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
                
                <div class="mws-panel grid_4">
                    
                    <div class="mws-panel-header">
                        <span><i class="icon-pencil"></i> Editar usuário</span>
                    </div>
                    
                    <div class="mws-panel-body no-padding">
                        
                        <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>adm/usuarios/editar">
                            
                            <div class="mws-form-inline">
                                
                                 <div class="mws-form-row bordered">
                                    <label class="mws-form-label">Unidades </label>
                                    <div class="mws-form-item">
                                        <select name="id_unidade">
                                        	<? foreach($unidades->result() as $unidade){ ?>
                                            	<?php if($usuario->id_unidade == $unidade->id){ $sel_uni = 'selected="selected"'; }else{ $sel_uni = ''; } ?>
                                                <option <?=$sel_uni?> value="<?=$unidade->id?>"><?=$unidade->titulo?> <?=$unidade->cidade?> <?=$unidade->uf?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mws-form-row bordered">
                                    <label class="mws-form-label">Setor</label>
                                    <div class="mws-form-item clearfix">                                                                                
                                        <select name="id_setor" class="large">
                                        <? foreach($this->db->get('usuarios_setores')->result() as $setor){ ?>
                                        <?php if($usuario->id_setor == $setor->id){ $sel_set = 'selected="selected"'; }else{ $sel_set = ''; } ?>
                                        	<? if($setor->id == '2'){ $disable = 'disabled="disabled"'; }else{ $disable = ''; } ?>
                                           	<option <?=$disable?> value="<?=$setor->id?>"><?=$setor->nome?></option>
                                            <? foreach($this->padrao_model->get_by_matriz('id_setor',$setor->id,'usuarios_setores')->result() as $sub){ ?>
                                            	<?php if($usuario->id_setor == $sub->id){ $sel_set = 'selected="selected"'; }else{ $sel_set = ''; } ?>
                                                <option <?=$sel_set?> value="<?=$sub->id?>"> -- <?=$sub->nome?></option>
                                            <? } ?>
                                        <? } ?>
                                            
                                        </select>
                                    </div>
                                </div>                  
                                
                                
                                
								<div class="mws-form-row bordered">
                                    <label class="mws-form-label">Nome</label>
                                    <div class="mws-form-item">
                                    	<input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                                        <input type="text" name="nome" class="large" value="<?php echo $usuario->nome; ?>">
                                    </div>
                                </div>
                                
                                <div class="mws-form-row bordered">
                                    <label class="mws-form-label">E-mail</label>
                                    <div class="mws-form-item">
                                        <input type="text" name="email" class="large" value="<?php echo $usuario->email; ?>">
                                    </div>
                                </div>
                                                                                                
                                <div class="mws-form-row bordered">
                                    <label class="mws-form-label">Login</label>
                                    <div class="mws-form-item">
                                        <input type="text" name="login" class="large" value="<?php echo $usuario->login; ?>">
                                    </div>
                                </div>                 
                                
                                
                                 <div class="mws-form-row bordered">
                                    <label class="mws-form-label">Nível</label>
                                    <div class="mws-form-item clearfix">                                                                                
                                        <select name="nivel" class="large">
                                        <? foreach($this->db->get('usuarios_niveis')->result() as $niv){ ?>
                                        	<?php if($usuario->nivel == $niv->id){ $sel_niv = 'selected="selected"'; }else{ $sel_niv = ''; } ?>
                                            <option <?=$sel_niv?> value="<?=$niv->id?>"><?=$niv->nome?></option>                                           
                                        <? } ?>
                                        </select>
                                    </div>
                                </div>         
                                                                                                             
                                
                            </div>
                            <div class="mws-button-row">
                                <input type="submit" id="botao" value="Enviar" class="btn btn-danger">
                            </div>
                            
                            <div id="resposta" style="color:#900">
                			</div>
                        </form>
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
    <!--[if lt IE 9]>
    <script src="js/libs/excanvas.min.js"></script>
    <![endif]-->

	<script src="<?php echo base_url(); ?>jui/js/globalize/globalize.js"></script>
    <script src="<?php echo base_url(); ?>jui/js/globalize/cultures/globalize.culture.en-US.js"></script>

    <!-- Plugin Scripts -->
    <script src="<?php echo base_url(); ?>custom-plugins/picklist/picklist.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/autosize/jquery.autosize.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/select2/select2.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/colorpicker/colorpicker-min.js"></script>
    
    <script src="<?php echo base_url(); ?>plugins/ibutton/jquery.ibutton.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/cleditor/jquery.cleditor.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/cleditor/jquery.cleditor.table.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/cleditor/jquery.cleditor.xhtml.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/cleditor/jquery.cleditor.icon.min.js"></script>

    <!-- Core Script -->
    <script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>js/adm/core/mws.js"></script>


    <!-- Demo Scripts (remove if not needed) 
    <script src="<?php echo base_url(); ?>js/adm/demo/demo.dashboard.js"></script>
    
    <!-- Themer Script (Remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/adm/core/themer.js"></script>
    
    <!-- Demo Scripts (remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/adm/demo/demo.widget.js"></script>
	
    <!-- Demo Scripts (remove if not needed) -->
    <script src="<?php echo base_url(); ?>js/demo/demo.formelements.js"></script>

	<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
 	    
    <script type="text/javascript">
    	$(document).ready(function() { 
            		
			$("#form").validate({
								
				rules:{
					nome: {
						required: true,
						minlength: 5
					},
					login: {
						required: true,
						minlength: 6
					},
					senha: {
						required: true,
						minlength: 5,
					},
					confirma: {
						equalTo: "#senha"	
					}
				},
				
				messages:{
					nome: {
						required: "O campo nome é obrigatório.",
						minlength: "Mínimo de 5 caracteres"
					},
					login: {
						required: "O campo login é obrigatório.",
						minlength: "Mínimo de 6 caracteres"
					},
					senha: {
						required: "O campo senha é obrigatório.",
						minlength: "Mínimo de 5 caracteres"
					},
					confirma: {
						equalTo: "Senha incorreta",
					}
				}	
			
			});//fim validate
		//})	
		
    }); 
    </script>
</body>
</html>