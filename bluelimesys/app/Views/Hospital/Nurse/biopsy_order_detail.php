<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<?php
            helper('form');
        ?>
<script src="<?php echo base_url()?>assets/plugins/summernote/summernote.min.js" type="text/javascript"></script>
<link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
                        <link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
                        <script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
    <style>
                                
                        @keyframes jump {
                          0%{
                            font-size: 25px;
                            color: orange;
                          }
                          50%{
                            font-size: 45px;
                            color: red;
                          }
                          100%{
                            font-size: 25px;
                            color: orange;
                          }
                        }
                            .jump
                            {
                                 animation: jump 1.5s ease 0s normal ;
                                color: orange;
                                 /*animation: jump 1.5s ease 0s infinite normal ;*/
                            }
                        
                            .dropZoneOverlay, .FileUpload {
                                    width: 100%;
                                    height: 100%;
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                }

                            .dropZoneOverlay {
                                font-family: cursive;
                                color: #7066fb;
                                display: flex;
                                flex-direction: column;
                                justify-content: center;
                                align-items: center;
                                cursor: pointer;
                                background: transparent;
                            }

                            .FileUpload {
                                opacity: 0;
                                cursor: pointer;
                                z-index: 2;
                            }
                        </style>        

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Nurse');?></h1>
            <small><?php echo lang('Biopsy Order Detail');?></small>
        </div>
    </section>
    <section class="content">
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                    <div class="panel-body col-md-2">
                        <div class="col-sm-12">
                            <h3 style="margin-top: 5px !important;">
                                <b>
                                    <?= "MRN-".sprintf('%05d', $order_main[0]['patient_id']);?>
                                </b>
                            </h3>
                                <b><?= $order_main[0]['patient_name']?></b><br>
                                <b>Age: </b><?= BluelimeSystem::year_difference(@$order_main[0]['date_of_birth']??date('Y-m-d'),date('Y-m-d'));  ?>
                                <b> &nbsp; Sex: </b><?= @$order_main[0]['sex'] ?><br>
                                <?= $order_main[0]['mobile'];?>
                        <!--input type="button"  class="btn-xs btn-success" style="background-color: green; border-0" value="ET"-->
                        </div>
                        
                        
                    </div>
                    <div class="panel-body col-md-2">
                    <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <tbody>
                                
                                <tr>
                                   
                                    <td style="cursor: pointer;"><h5><?=$order_detail[0]['test_name']?></h5></td>
                                    
                                </tr>
                                   
                                 
                            </tbody>
                        </table>

                        <?php
                                        helper('form');
                                        $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                                        echo form_open('hospital/nurse/uploadbiopsyrequest', $attributes);
                                    ?> 
                                        <div class="col-sm-1">
                                            <input type="file" id="drop_zone" class="FileUpload"   accept="image/*" onchange="this.form.submit()"  name="picture"  title="Upload Request" data-toggle="tooltip" data-html="true" >
                                            <input type="hidden" value="<?=$_GET['order_main_id']?>" name="order_id">
                                            <div class="dropZoneOverlay">
                                                <img src="/img/green-upload.png" width="60px" alt="QR Code for mobile photo" title="Scan with mobile to take pictures." />
                                            
                                            </div>
                                            <div hidden><input type="submit"></div>
                                            
                                        </div>
                                        <?php $result = (explode(',', $order_main[0]['scanned_documents'])); 
                                            //BluelimeSystem::show_and_die(count($result));
                                            ?>
                                            <div class="col-sm-1" ></div>
                                            
                                            <div class="col-sm-2 manual_card  <?= (empty($order_main[0]['scanned_documents']))?" hidden ":""?>" style="margin-left: 30px;">
                                                

                                                
                                                <?php
                                                    $manual_card_image = explode(',',$order_main[0]['scanned_documents']);
                                                    $is_first = true;
                                                    foreach($manual_card_image as $image_name):
                                                        echo '<a href="/image/manual_card?image='.$image_name.'" data-image_name="'.$image_name.'"><img src="/image/manual_card?image='.$image_name.'&thumbnail=1" style="width:50px" ></a>';
                                                    endforeach;
                                                ?>
                                                
                                            
                                            </div>
                                        
                                    <!--button style="position: absolute; bottom: 0; right: 0;" class="btn btn-xs btn-danger <?= (empty($patient['scanned_documents']))?" hidden ":""?>" type="button" onclick="deleteallcards()">Delete All Cards</button-->
                                    <?php echo form_close(); ?>
                                    </td>
                    </div>
                    <div class="panel-body col-md-8">
                    
                        <?php
                        
        
                            if($order_main[0]['order_status'] == 1){
                                echo '<button class="btn btn-success" title="Collect Sample" onclick="checkdocumentattached()">Collect Sample</button> ';
                            }
                            elseif($order_main[0]['order_status'] == 2){
                                echo '<a href="/hospital/nurse/change_sample_status?order_id='.$order_main[0]['id'].'" class="btn btn-primary" title="Send Sample">Send Sample</a> ';
                                echo '<a href="/hospital/nurse/reverse_sample_status?order_id='.$order_main[0]['id'].'"  class="btn btn-warning" title="Revert"><i class="fa fa-reply"></i> Revert</a> ';
                            }
                            elseif($order_main[0]['order_status'] == 3){
                                echo '<button class="btn btn-danger" title="Recieve Slide"  onclick="show_slidenote_modal()()">Recieve Slide</button> ';
                                echo '<a href="/hospital/nurse/reverse_sample_status?order_id='.$order_main[0]['id'].'"  class="btn btn-warning" title="Revert"><i class="fa fa-reply"></i> Revert</a> ';
                            }
                            elseif($order_main[0]['order_status'] == 4){
                                echo '<a href="/hospital/nurse/reverse_sample_status?order_id='.$order_main[0]['id'].'"  class="btn btn-warning" title="Revert"><i class="fa fa-reply"></i> Revert</a> ';
                            }
                            else{
                                echo '<a disabled class="btn btn-warning" title="Revert"><i class="fa fa-reply"></i> Revert</a> ';
                            }


                        ?>
                        <div style="float: right;">
                            <table>
                                <tr style="text-align: center;">

                                    <td><?= ($order_main[0]['order_status'] == 1)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 2)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 3)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 4)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 5)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 6)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($order_main[0]['order_status'] == 7)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                </tr>
                                <tr>
                                    <td><span class="badge" style="background-color: #ff5733;">Paid</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #df4c2d;">Sample Collected</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #bf4126;">Sample Sent</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #9f3620;">Slide Recieved</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #802b1a;">Result Saved</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #602113;">Result Approved</span></td>
                                    <td>&nbsp;<span class="badge" style="background-color: #40160d;">Result Delivered</span></td>
                                </tr>
                                <tr style="text-align: center;">

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><a href="/hospital/nurse/viewbiopsyresult?brid=<?=$biopsy_results[0]['id']??0?>&status=<?=$order_main[0]['order_status']?>&order_id=<?=$order_main[0]['id']?>"><?= ($order_main[0]['order_status'] == 6)?'<i class="btn btn-success fa fa-eye"></i>':'' ?></a></td>
                                    <td><a href="/hospital/nurse/viewbiopsyresult?brid=<?=$biopsy_results[0]['id']??0?>&status=<?=$order_main[0]['order_status']?>&order_id=<?=$order_main[0]['id']?>"><?= ($order_main[0]['order_status'] == 7)?'<i class="btn btn-success fa fa-eye"></i>':'' ?></a></td>
                                    
                                </tr>
                            </table>
                        </div>       
                    
                    </div>
                    </div>
                </div>
                
                
            
            </div>
            
        </div>
        
    </section>
</div>

<div class="modal fade" id="slidenote" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Slide Note</h4>
                                </div>
                                <?php
                                        $attributes = array('role'=>'form', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return validate_closevisit();');
                                        echo form_open_multipart('/hospital/nurse/closevisit', $attributes);
                                ?>
                                    <div class="modal-body">
                                        <input type="hidden" name="visit_id" value="<?= $visit_id??0 ?>">
                                        <div class="input-group">
                                            <br>
                                            <label for="slide" style="color: red; text-align: center;"><b></b>Slide Note</label><br>
                                            <textarea class="form-control" id="slide" name="slide" required style="width:270px"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="button" onclick="saveslide()">
                                            Recieve
                                        </button>
                                    </div>
                                <?php
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
<script>
    function show_slidenote_modal(){
                    $("#slidenote").modal("show");
                }

    function checkdocumentattached() {
        let doc = "<?=$order_main[0]['scanned_documents']; ?>";
        let pid = "<?=$order_main[0]['patient_id']; ?>";
        if(doc == '' && pid == 0){
            toastr['error']('Please Attach Request First');
            return;
        }
        else{
            location = '/hospital/nurse/change_sample_status?order_id='+ <?=$order_main[0]['id']?>;
        }
    }
    
    function saveslide() {
        let note = document.getElementById('slide').value;
        location = '/hospital/nurse/change_sample_status?order_id='+ <?=$order_main[0]['id']?> +'&note='+ note;
    }
    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
    }

    function delete_image_via_lightbox(plugin){
        let image_name = $(plugin.current).data('image_name');
        if(confirm('Are you sure you want to delete this picture?')){
            $.ajax({
                url: '/hospital/nurse/delete_biopsy_document',
                method: 'POST',
                data: {image_name:image_name, order_id:<?= $order_main[0]['id'] ?>},
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
        $('.manual_card a').lightbox({custom_button1:delete_button_lightbox}); 
    });
</script>