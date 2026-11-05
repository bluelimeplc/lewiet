<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><title>Bluelime: <?=explode('/', string: $_SERVER['REQUEST_URI'])[1]!=''?explode('/', string: $_SERVER['REQUEST_URI'])[1]:'Dashboard'?></title>
        </title>
        <!-- favicon -->
        <link rel="icon" href="/favicon.png" sizes="16x16"> 
        <link href="<?php echo base_url()?>css/hospital/style.css?date=<?= date('Ymd'); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>css/hospital/style.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo base_url()?>js/jquery/jquery-3.6.3.js"></script>

       
        <!-- Start Global Mandatory Style
        =====================================================================-->
        <!-- jquery-ui css -->
        <!--link href="<?php echo base_url()?>assets/plugins/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css"/-->
        <!-- Bootstrap -->
        <link href="<?php echo base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!-- Bootstrap rtl -->
        <!-- <link href="<?php echo base_url()?>assets/bootstrap-rtl/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/> -->
        <!-- Lobipanel css -->
        <link href="<?php echo base_url()?>assets/plugins/lobipanel/lobipanel.min.css" rel="stylesheet" type="text/css"/>
        <!-- Pace css -->
        <!-- <link href="<?php echo base_url()?>assets/plugins/pace/flash.css" rel="stylesheet" type="text/css"/> -->
        <!-- Font Awesome -->
        <link href="<?php echo base_url()?>assets/font-awesome/css/all.min.css" rel="stylesheet" type="text/css"/>
        <!-- Pe-icon -->
        <link href="<?php echo base_url()?>assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
        <!-- Themify icons -->
        <link href="<?php echo base_url()?>assets/themify-icons/themify-icons.css" rel="stylesheet" type="text/css"/>
        <!-- End Global Mandatory Style
        =====================================================================-->

        <!-- DataTables CSS -->
       <link href="<?php echo base_url();?>assets/plugins/datatables/dataTables.min.css" rel="stylesheet" type="text/css"/>
       <!-- datepiker -->
       <link href="<?php echo base_url()?>assets/plugins/ui-datetimepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css"/>

       <!-- summernote css -->
        <link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
        

        <!-- Start page Label Plugins 
        =====================================================================-->
        <!-- Toastr css -->
        <link href="<?php echo base_url()?>assets/plugins/toastr/toastr.css" rel="stylesheet" type="text/css"/>
        <!-- Emojionearea -->
        <link href="<?php echo base_url()?>assets/plugins/emojionearea/emojionearea.min.css" rel="stylesheet" type="text/css"/>
        <!-- Monthly css -->
        <link href="<?php echo base_url()?>assets/plugins/monthly/monthly.css" rel="stylesheet" type="text/css"/>
        <!-- End page Label Plugins 
        =====================================================================-->
        <!-- Start Theme Layout Style
        =====================================================================-->
        <!--link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/dist/css/tokenfield-typeahead.min.css" rel="stylesheet" type="text/css"/-->
        <link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/dist/css/bootstrap-tokenfield.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/docs-assets/css/pygments-manni.css" rel="stylesheet" type="text/css"/>
        
        <!-- Theme style -->
        <link href="<?php echo base_url()?>assets/dist/css/styleBD.css" rel="stylesheet" type="text/css"/>
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-BLUE.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-GREEN.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-GREEN.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>extra/style.css" rel="stylesheet" type="text/css"/>
        
        <!-- Theme style rtl -->
        <!-- <link href="assets/dist/css/styleBD-rtl.css" rel="stylesheet" type="text/css"/> -->
        <!-- End Theme Layout Style
        =====================================================================-->
        <!-- jQuery -->

        
        <link href="<?php echo base_url()?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>css/twitter-app.css" rel="stylesheet" type="text/css"/>
        
        
        <script src="<?php echo base_url()?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>js/twitter-bootstrap.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/typeahead.bundle.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/bloodhound.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/dental/dental_script.js" type="text/javascript"></script>    

        <link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

    </head>

<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title">
                            
                        </div>
                    </div>
                    <div class="panel-body"></div>
                        <?php
                            $image_list = $_GET['image_list'] ?? '';
                            $image_list = explode(',',$image_list);
                            foreach($image_list as $image_name){
                                if(trim($image_name) != '')
                                    echo '<img style="width:100px" src="/hospital/reception/manualcard?image='.$image_name.'&thumbnail=1" /> ';
                            }
                        ?>
                    </div>
                </div>
            </div>
                    

            <div class="col-lg-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Upload Picture</h4>
                        </div>
                    </div>
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                            echo form_open('hospital/reception/addmanualcard_mobile', $attributes);
                        ?> 
                    <div class="panel-body">

                    <div class="col-md-4">
                        
                        <div>
                            <input name="picture" required class="form-control" type="file" id="formFile" onchange="preview()"  accept="image/*" >
                            <input type="hidden" value="<?=$_GET['id']?>" name="patient_id">
                            <input type="hidden" value="<?=$_GET['image_list'] ?? ''?>" name="image_list">
                            <textarea  placeholder="Comment" row="3" class="form-control" id="comment" name="comment"></textarea>
                            <input type="submit" value="Upload"class="btn btn-success" >
                        </div>
                    </div>
                    <div class="">
                        <img id="frame" src="" class="img-fluid"  style="width:50%;" />
                    </div>

                </div>
            </div>
        </div>
        <button id="clear_btn" type="button"onclick="clearImage()" class="btn btn-danger hidden">Clear</button>

    </section>
</div>

  


<script type="text/javascript">

    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
        $('#clear_btn').removeClass('hidden');

    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
        $('#clear_btn').addClass('hidden');
    }
    $(function() {
        $('.gallery a').lightbox(); 
    });

</script>
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
<?= view('Include/_footer') ?>