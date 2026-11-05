<?php
    $header = \BluelimeSystem::get_company_header();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Login</title>

      <?php 
            //query for logo 
            
        ?>
        <!-- favicon -->
        <link rel="icon" href="<?php echo (!empty($result->picture)?$result->picture:null); ?>" sizes="16x16"> 
        <!-- Bootstrap -->
        <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!-- Bootstrap rtl -->
        <!-- <link href="<?php echo base_url();?>assets/bootstrap-rtl/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/> -->
        <!-- Pe-icon-7-stroke -->
        <link href="<?php echo base_url();?>assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
        <!-- style css -->
        <link href="<?php echo base_url();?>assets/dist/css/styleBD.css" rel="stylesheet" type="text/css"/>
        <!-- Theme style rtl -->
        <!-- <link href="assets/dist/css/styleBD-rtl.css" rel="stylesheet" type="text/css"/> -->
        
        <link href="<?php echo base_url()?>css/dental/style.css" rel="stylesheet" type="text/css"/>
         <!-- jQuery -->
        <script src="<?php echo base_url();?>assets/plugins/jQuery/jquery-1.12.4.min.js" type="text/javascript"></script>
    <?php
        helper('form');
    ?>
    </head>


    <body>
    
        <div class="login-wrapper">
        
            <div class="container-center">
                <div class="text-center" style="margin-top:-70px;">
                    <img src="/img/Blue.png"  width="30%"/>
                </div>
                <div class="panel panel-bd">
               
                    <div class="panel-heading">
                        <div class="view-header">
                            <div class="header-icon">
                                <i class="pe-7s-unlock"></i>
                            </div>
                            <div class="header-title">
                                <h3>Login</h3>
                                <small><strong>Please login</strong></small>
                                <label style="float:right;padding-top:-20px; 20px;font-size: 18px;color: #54c2f0;"><?=strtoupper($_ENV['BRANCH']??"")?></label>
                    
                            </div>
                            <div class="header-title">
                                <?php $fail_msg = (@$_GET['fail'] == 1)? "Incorrect Username or Password": "" ?>
                                    <small style="color: red;"><strong><?=$fail_msg?></strong></small>
                            </div>
                        </div>
                    </div>
                
                    <div class="panel-body">
                        <?php
                            $attributes = array('method'=>'post','role'=>'form');
                            echo form_open_multipart('login/authentication', $attributes);

                        ?>

                                <div class="form-group">
                                    <input class="form-control" id="username" placeholder="Username" name="username" type="text" autofocus required />
                                </div>

                                <div class="form-group">
                                    <input class="form-control" id="pass" placeholder="password" name="password" type="password" required />
                                </div>
                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
       
        <!-- bootstrap js -->
        <script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    