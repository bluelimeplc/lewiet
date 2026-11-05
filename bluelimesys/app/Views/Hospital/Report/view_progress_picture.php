<?php
    $progress_picture = $this->data['progress_picture'];
    
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
                <small>Progress Picture</small>
                
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Progress Picture</h4>
                        </div>
                    </div>
                       
                    <div class="panel-body">
                    
                    <div class="container col-md-8">
                        <img id="frame" src="" class="img-fluid"  style="width:500px" />
                    </div>
                        
                    <div class="container col-md-12">
                    <hr>
                        <ul class="gallery">
                        <?php foreach($progress_picture as $pp): ?>
                            <li><a href="/picture/<?=$pp['picture_location']?>"> <img src="<?='/picture/'.$pp['picture_location']?>" style="width:200px" class="img-thumbnail"></li>
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




