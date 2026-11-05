<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['id']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    $progress_picture = $this->data['progress_picture'];
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
                <h1>Nurse</h1>
                <small>Progress Picture</small>
                
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <div class="col-lg-4">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Name</td><td><?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name']  ?></td>
                                </tr>
                                <tr>
                                    <td>Sex: </td><td><?= $patient['sex'] ?></td>
                                </tr>
                                <tr>
                                    <td>Age: </td><td><?= BluelimeSystem::year_difference($patient['date_of_birth'], date('Y-m-d')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-5">
                            <?php
                        
                                $url_for_mobile_org = 'http://192.168.1.8/hospital/nurse/newprogresspicture_mobile?id='.$_GET['id'].'&vid='.$_GET['vid'].'&logged_user_id='.$logged_user;
                                $url_for_mobile = urlencode($url_for_mobile_org);
                                
                            ?>
                            <h4>Scan with your mobile to take progress picture</h4>
                            <a href="<?= $url_for_mobile_org ?>" target="_blank">
                                <img src="/api/qrcode?data=<?= $url_for_mobile ?>" alt="QR Code for mobile photo" title="Scan with mobile to take pictures." />
                            </a><br><h5> Your mobile must be in same network with this computer.</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Upload Picture</h4>
                        </div>
                    </div>
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'xray',  'enctype' => 'multipart/form-data');
                            echo form_open('hospital/nurse/addprogresspicture', $attributes);
                        ?> 
                    <div class="panel-body">
                    <div class="container col-md-4">
                        <div>
                            <input name="picture" required class="form-control" type="file" id="formFile" onchange="preview()"  accept="image/*" >
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
                        <?php
                            $registered_date_prv = '';
                            foreach($progress_picture as $pp): 

                                if($registered_date_prv == ''){
                                    echo '
                                    <fieldset class="reset">
                                        <legend class="reset"><h4>'.substr($pp['registered_date'],0,10).'</h4></legend>
                                        <ul class="gallery">';
                                }
                                elseif($registered_date_prv != substr($pp['registered_date'],0,10)){
                                    echo '
                                        </ul>
                                    </fieldset>
                                    <fieldset class="reset">
                                        <legend class="reset"><h4>'.substr($pp['registered_date'],0,10).'</h4></legend>
                                        <ul class="gallery">';
                                }
                        ?>
                            
                            
                                
                                <li><a href="/image/progresspicture?image=<?= $pp['picture_location']?>" data-id="<?= $pp['id']?>" data-caption="<?= $pp['comment']?>"> <img src="/image/progresspicture?image=<?= $pp['picture_location']?>&thumbnail=1" style="width:200px" class="img-thumbnail"></a></li>

                        <?php 
                            $registered_date_prv = substr($pp['registered_date'],0,10);
                            endforeach;
                            if($registered_date_prv != '')
                                echo '
                                    </ul>
                                </fieldset>';
                        ?>
                        
                    </div>
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

   
    function delete_image_via_lightbox(plugin){
        let progress_picture_id = $(plugin.current).data('id');
        if(confirm('Are you sure you want to delete this picture?')){
            $.ajax({
                url: '/hospital/nurse/delete_progress_picutre',
                method: 'POST',
                data: {progress_picture_id:progress_picture_id},
                async: false,
                success:function(result){
                    plugin.current.remove();
                    plugin.items.splice(plugin.getCurrentIndex(),1);
                    plugin.close();
                }
            });
        }
    }

    var delete_button_lightbox = {
        "background-image" : "/img/delete_button_01.png",
        callback: delete_image_via_lightbox
    };
  
  $(function() {
    $('.gallery a').lightbox({custom_button1:delete_button_lightbox}); 
  });


</script>




