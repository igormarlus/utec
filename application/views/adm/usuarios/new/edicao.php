<!DOCTYPE html>
<html>
  <head>
    <title>Edição de dados</title>
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
    <!--<link href="<?=base_url()?>css/main.css?version=4.5.0" rel="stylesheet"> -->
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
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
              <span>Edição de dados</span>
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



              <!-- FORMULARIO -->
              
              
                <div class="row">
                  <div class="col-lg-12">
                    <div class="element-wrapper">
                      <h6 class="element-header">
                        Minhas informações
                      </h6>

                      <div class="alert alert-warning">Solicite sua ativação falando com:<br> <a href="https://api.whatsapp.com/send?phone=5581983276882&text=Ol%C3%A1!%20Gostaria%20de%20ativar%20minha%20conta" class="read-more">Nosso Whatsapp (81 98327-6882)</a>
                      </div>
                      <?php if($usuario->status == "1"){ ?>
                        <div class="alert alert-success">Dados validados</div>
                      <?php } ?>

                      <div class="element-box">

                        <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>index.php/adm/usuarios/editar" enctype='multipart/form-data'>
                          <h5 class="form-header">
                            Edição de dados
                          </h5>

                          <div class="form-desc">
                            Preencha as informações corretamente para que possa ser encontrado pelo seus pacientes.
                          </div>

                          

                          <div class="form-group">
                            <div  class="mws-form-row bordered">
                                <label class="mws-form-label">Vídeo (Max de 16MB)</label>
                                <div class="mws-form-item">
                                    <input type="file" name="userfile" id="userfile" />
                                </div>
                                <div class="mws-form-item">
                                    <div id=''></div>
                                </div>
                            </div>
                          </div>


                          <div class="form-group">
                            <div id="ima" class="mws-form-row bordered">
                                <label class="mws-form-label">Foto</label>
                                <div class="mws-form-item">
                                    <input type="file" name="photoimg" id="photoimg" />
                                </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <div id="pre" class="mws-form-row bordered">
                                <label class="mws-form-label">Preview</label>
                                
                                <div class="mws-form-item">
                                    <div id='preview'>
                                        <?php if($usuario->img != ""){ ?>
                                            <img src="<?=base_url()?>imagens/usuarios/min/<?=$usuario->img?>" alt="Foto usuário">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                          </div>




                          <div class="form-group">
                              <label class="mws-form-label">Nível</label>
                              <div class="mws-form-item">
                                
                                  <input disabled="disabled" type="text" name="nivel_atual" class="form-control" value="<?=$this->padrao_model->get_by_matriz('nivel',$usuario->nivel,'usuarios_niveis')->row()->nome?>">
                              </div>
                          </div>



                          <div class="form-group">
                              <label class="mws-form-label">Nome</label>
                              <div class="mws-form-item">
                                <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                                  <input type="text" name="nome" class="form-control" value="<?php echo $usuario->nome; ?>">
                              </div>
                          </div>

                         



                          <?php if($usuario->nivel == 3){?>
                         
                                

                                <div class="form-group bordered">
                                    <label class="mws-form-label">Especialidade </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="especialidade" class="form-control" value="<?php echo $usuario->especialidade; ?>">
                                    </div>
                                </div>    

                                <div class="form-group bordered">
                                    <label class="mws-form-label">Registro de Classe </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="classe" class="form-control" value="<?php echo $usuario->classe; ?>">
                                    </div>
                                </div>    

                                <?php } ?>

                                <?php if($usuario->nivel == 5){ ?>

                                <div class="form-group">
                                    <label class="mws-form-label">Afiliações </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="afiliacoes" class="form-control" value="<?php echo $usuario->afiliacoes; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mws-form-label">Data de nascimento </label>
                                    <div class="mws-form-item">
                                        <input type="date" name="dt_nascimento" class="form-control" value="<?php echo $usuario->dt_nascimento; ?>">
                                    </div>
                                </div>

                                <?php } ?>

                                <?php if($usuario->nivel < 5){ ?>
                                  <div class="row">
                                    <div class="col-sm-6">                                                              
                                      <div class="form-group">
                                          <label class="mws-form-label">Login</label>
                                          <div class="mws-form-item">
                                              <input type="text" name="login"  class="form-control" placeholder="Login de acesso" value="<?php echo $usuario->login; ?>">
                                          </div>
                                      </div>    
                                    </div>

                                    <div class="col-sm-6">

                                      <div class="form-group">
                                          <label class="mws-form-label">Senha</label>
                                          <div class="mws-form-item">
                                              <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para não alterar">
                                          </div>
                                      </div>    

                                    </div>
                                  </div>

                                  <?php } ?>
                                
                                <div class="form-group">
                                    <label class="mws-form-label">E-mail</label>
                                    <div class="mws-form-item">
                                        <input type="text" name="email" class="form-control" value="<?php echo $usuario->email; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mws-form-label">Telefone </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="telefone" class="form-control" value="<?php echo $usuario->telefone; ?>">
                                    </div>
                                </div>    
                                <div class="form-group">
                                    <label class="mws-form-label">CPF </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="cpf" class="form-control" value="<?php echo $usuario->cpf; ?>">
                                    </div>
                                </div>  

                                <div class="form-group">
                                    <label class="mws-form-label">Identidade </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="identidade" class="form-control" value="<?php echo $usuario->rg; ?>">
                                    </div>
                                </div>  

                                <div class="form-group" style="display: none">
                                    <label class="mws-form-label">Profissão </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="profissao" class="form-control" value="<?php echo $usuario->profissao; ?>">
                                    </div>
                                </div>    

                                
                                                                                                
                                <div class="form-group">
                                    <label class="mws-form-label">Login</label>
                                    <div class="mws-form-item">
                                        <input type="text" name="login" disabled="disabled" class="form-control" value="<?php echo $usuario->login; ?>">
                                    </div>
                                </div>                 
                                
                                
                                 <div class="form-group" style="display: none">
                                    <label class="mws-form-label">Nível</label>
                                    <div class="mws-form-item clearfix">                                                                                
                                        <select name="nivel" class="form-control">
                                        <?php foreach($this->db->get('usuarios_niveis')->result() as $niv){ ?>
                                          <?php if($usuario->nivel == $niv->id){ $sel_niv = 'selected="selected"'; }else{ $sel_niv = ''; } ?>
                                            <option <?=$sel_niv?> value="<?=$niv->id?>"><?=$niv->nome?></option>                                           
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>   


                                <div class="form-group">
                                    <label class="mws-form-label">CEP </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="cep" class="form-control" value="<?php echo $usuario->cep; ?>">
                                    </div>
                                </div>    

                                <div class="form-group">
                                    <label class="mws-form-label">Endereço </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="endereco" class="form-control" value="<?php echo $usuario->endereco; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="mws-form-label">Número </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="numero" class="form-control" value="<?php echo $usuario->numero; ?>">
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label class="mws-form-label">Complemento </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="complemento" class="form-control" value="<?php echo $usuario->complemento; ?>">
                                    </div>
                                </div>               
                                <div class="form-group">
                                    <label class="mws-form-label">Bairro </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="bairro" class="form-control" value="<?php echo $usuario->bairro; ?>">
                                    </div>
                                </div>    
                                <div class="form-group">
                                    <label class="mws-form-label">Cidade </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="cidade" class="form-control" value="<?php echo $usuario->cidade; ?>">
                                    </div>
                                </div>   
                                <div class="form-group">
                                    <label class="mws-form-label">UF </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="uf" class="form-control" value="<?php echo $usuario->uf; ?>">
                                    </div>
                                </div>    


                                <div class="form-group">
                                    <label class="mws-form-label">Redes Sociais </label>
                                    <div class="mws-form-item">
                                        <input type="text" name="redes_sociais" class="form-control" value="<?=$usuario->redes_sociais?>">
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="submit"> Salvar</button>



                          <!--------------------------- X FORM NOVO ------------------------------>
                          
                          <!--

                          <br><br>
                          <hr>
                          <br>

                          <div class="form-group">
                            <label for=""> Email address</label><input class="form-control" placeholder="Enter email" type="email">
                          </div>
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for=""> Password</label><input class="form-control" placeholder="Password" type="password">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="">Confirm Password</label><input class="form-control" placeholder="Password" type="password">
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for=""> Regular select</label><select class="form-control">
                              <option>
                                Select State
                              </option>
                              <option>
                                New York
                              </option>
                              <option>
                                California
                              </option>
                              <option>
                                Boston
                              </option>
                              <option>
                                Texas
                              </option>
                              <option>
                                Colorado
                              </option>
                            </select>
                          </div>
                          <div class="form-group">
                            <label for=""> Multiselect</label><select class="form-control select2 select2-hidden-accessible" multiple="" data-select2-id="1" tabindex="-1" aria-hidden="true">
                              <option selected="true" data-select2-id="3">
                                New York
                              </option>
                              <option selected="true" data-select2-id="4">
                                California
                              </option>
                              <option>
                                Boston
                              </option>
                              <option>
                                Texas
                              </option>
                              <option>
                                Colorado
                              </option>
                            </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="2" style="width: 447px;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-selection__choice" title="
                                New York
                              " data-select2-id="5"><span class="select2-selection__choice__remove" role="presentation">×</span>
                                New York
                              </li><li class="select2-selection__choice" title="
                                California
                              " data-select2-id="6"><span class="select2-selection__choice__remove" role="presentation">×</span>
                                California
                              </li><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                          </div>
                          <fieldset class="form-group">
                            <legend><span>Section Example</span></legend>
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for=""> First Name</label><input class="form-control" placeholder="First Name" type="text">
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Last Name</label><input class="form-control" placeholder="Last Name" type="text">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for=""> Date Picker</label>
                                  <div class="date-input">
                                    <input class="single-daterange form-control" placeholder="Date of birth" type="text" value="04/12/1978">
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Twitter Username</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">
                                        @
                                      </div>
                                    </div>
                                    <input class="form-control" placeholder="Twitter Username" type="text">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label> Example textarea</label><textarea class="form-control" rows="3"></textarea>
                            </div>
                          </fieldset>
                          <div class="form-check">
                            <label class="form-check-label"><input class="form-check-input" type="checkbox">I agree to terms and conditions</label>
                          </div>
                          <div class="form-buttons-w">
                            <button class="btn btn-primary" type="submit"> Submit</button>
                          </div>


                        -->
                        </form>
                      </div>
                    </div>
                  </div>
                 

                      <? #} ?>

          

                    
                

                        
                <? #} ?>

                    <!-- ########################################### -->

                  </div>
                </div>


                <!--------------------
              START - Color Scheme Toggler
              -------------------->
              
              <!--------------------
              END - Color Scheme Toggler
              --------------------><!--------------------
              START - Demo Customizer
              -------------------->
         
              <!--------------------
              END - Chat Popup Box
              -------------------->
            </div>

              <!-- XXXXXXXXXXXXXXX -->
              
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





    <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
      
    <script type="text/javascript">
      $(document).ready(function() { 
          //alert("AAA");

                  //upload e preview da imagem
                $('#photoimg').on('change', function() { 
                    $("#preview").html('');
                    $("#preview").html('<img src="<?php echo base_url(); ?>images/ajax-loader.gif" alt="Uploading...."/>'); //
                    
                    $("#form").attr('action', '<?php echo base_url(); ?>index.php/adm/usuarios/upImgPost');            
                    $("#form").validate().cancelSubmit = true;
                    
                    var options = { 
                                target:        '#preview',   // target element(s) to be updated with server response 
                                //beforeSubmit:  showRequest('oi'),  // pre-submit callback 
                                success: function() { 
                                                        $('#preview').fadeIn('slow'); 
                                                    }  
                            }; 
                    $("#form").ajaxSubmit(options);     
                            
                    $("#form").attr('action', '<?php echo base_url(); ?>index.php/adm/usuarios/editar');
                    $("#form").validate().cancelSubmit = false;
                    /*
                    $("#form").ajaxForm({
                        target: '#preview'
                    }).submit();        
                    */
                    
                });


                //qrcode                
                var aa = 0;
                //var check_time_a = setInterval(function(){ aa++;

                 //if(aa == 5){
                  /*

                          $.get("<?=base_url()?>whats/get_qrcode/<?=$usuario->id?>"  , function(code_qrcode){
                              //alert(data);
                              $("#qrcode").html("<img src='"+code_qrcode+"'>");
                              
                            })
                          */
                        //  clearInterval(check_time_a);
                          
                      //} // x aa == 5

                //} , 2000); 
                
                
  
    
    }); 
    </script>




    
  </body>
</html>
