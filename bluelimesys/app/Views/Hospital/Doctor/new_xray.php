<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['id']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    $xray = $this->data['xray'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $fname = $patient['first_name'];
    $mname = $patient['middle_name'];
    $lname = $patient['last_name'];
    $mobile = $patient['mobile'];
    $visit_id = $this->data['visit_id'];
    $patient_id = $patient['id'];
//\BluelimeSystem::show_and_die($visit_id);
?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">

<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
<script src="assets/js/jquery.min.js"></script>

<div class="content-wrapper">

    <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Doctor</h1>
                <small>X-Ray</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="visit?visit_id=<?=$visit_id?>">Visit Page</a></li>
                </ol>
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Upload X-Ray</h4>
                        </div>
                    </div>
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'xray',  'enctype' => 'multipart/form-data');
                            echo form_open('hospital/doctor/addxray', $attributes);
                        ?> 
                    <div class="panel-body">
                    <div class="container col-md-4">
                        <div>
                            <input name="xray" required class="form-control" type="file" id="formFile" onchange="preview()">
                            <input type="hidden" value="<?=$_GET['id']?>" name="patient_id">
                            <input type="hidden" value="<?=$_GET['vid']?>" name="visit_id">
                            <textarea  placeholder="Comment" row="3" class="form-control" id="comment" name="comment"></textarea>
                            <button type="button"onclick="clearImage()" class="btn btn-danger">Clear</button>
                            <input type="submit" value="Upload"class="btn btn-success" >
                        </div>
                    </div>
                    <div class="container col-md-8">
                        <img id="frame" src="" class="img-fluid"  style="width:500px" />
                    </div>
                        
                    <div class="container col-md-12">
                    <hr>
                        <ul class="gallery">
                        <?php foreach($xray as $xr): ?>
                            <li><a href="/XrayResults/<?=$xr['xray_location']?>"> <img src="<?='/XrayResults/'.$xr['xray_location']?>" style="width:200px" class="img-thumbnail"></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                  
            </div>
        </div>
    </section>
</div>

  


<script type="text/javascript">


function preview() {
                frame.src = URL.createObjectURL(event.target.files[0]);
            }
            function clearImage() {
                document.getElementById('formFile').value = null;
                document.getElementById('comment').value = "";
                frame.src = "";
            }
 
$(function() {
    $('.gallery a').lightbox(); 
  });

  

</script>




