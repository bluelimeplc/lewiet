<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>
<?= view('Include/_add_new_general') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Pharmacy</h1>
            <small>Compounding Sales</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()" style="cursor: pointer;">Label Print</a></li>
            
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?php
            $print_data = [
                'document_no' => $sales_main['id'],
                'document_title' => 'የሽያጭ አባሪ<br>Sales Attachment',
                'document_date' => $sales_main['created_at'],
                'registered_by' => $sales_main['registered_by_name'],
                'footer1' => 'Dispensed By',
                'footer2' => 'Cashier',
            ];
        ?>
        <div class="panel panel-bd col-md-7">
        <table class="table table-bordered table-stripped">
            <tr>
                <td width="300"><b>Customer Name:</b> <?= $sales_main['patient_name'] ?></td>
                <td width="200"><b>Date:</b> <?= $sales_main['created_at'] ?></td>
                <td <?= (trim($sales_main['fs_number']) == ''? 'class="bg-red"' : '' )?> ><b>FS N<u>o</u>:</b><?= (trim($sales_main['fs_number']) == ''?'<b style="color:#FFF; font-size:large;">NOT PAID</b>' : substr($sales_main['fs_number'],0,10)) ?><span class="badge" style="border:solid 1px #ccc !important;">Paid</span> </td>
                
            </tr>
        </table>
        <hr>
        <table class="table table-bordered" style="text-align: right;">
            <thead>
                <tr>
                    <th>N<u>o</u></th><th>Item Name</th><th>Meas</th><th>Qty</th><th>U.Price</th><th>Total</th><th>Vat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    
                    $sn = 0;
                    $sum = 0;
                    $t_vat = 0;
                    $row_tr = '';
                    foreach($sales_detail as $sales_line)
                    {
                        $sn++;
                        $sum += ($sales_line['quantity']*$sales_line['unit_price']);
                        $t_vat += ($sales_line['quantity']*$sales_line['vat']);
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td>
                            <td>'.$sales_line['item_detail']['item_brand_name'].' '.$sales_line['item_detail']['item_brand_name'].'</td>
                            <td>'.($sales_line['item_detail']['compounding_measurement']).'</td>
                            <td>'.round($sales_line['quantity'],2).'</td>
                            <td>'.number_format(round($sales_line['unit_price'],2),2).'</td>
                            <td>'.number_format(round(($sales_line['quantity']*$sales_line['unit_price']),2),2).'</td>
                            <td>'.number_format(round($sales_line['quantity']*$sales_line['vat'],2),2).'</td>
                        </tr>';
                    }
                    echo $row_tr;
                    echo '<tr><td rowspan="2">Direction<br>(አወሳሰድ) <br><a  class="btn btn-primary btn-xs" title="የአወሳሰድ አይነቶች ዝርዝር" data-toggle="tooltip" data-html="true" onclick = "show_direction_list()"><i class="fa fa-edit"></i> </a></td>
                    <td colspan="4" rowspan="2"><textarea rows="3" style="color:red;font-size:16px;resize: none;" class="form-control" type="text" id="selected_direction" onkeyup ="updatechangeonlabel(this.value)"></textarea><input type="hidden" id="hidden_direction"  value="'.$sales_main['compounding_amharic_direction'].'"></td>
                    <td colspan="1" align="right">Total</td>
                    <td>'.number_format(round($sum,2),2).'</td></tr>';
                    echo '<tr><td colspan="1" align="right">Vat 15%</td>
                    <td>'.number_format(round($t_vat,2),2).'</td></tr>';
                    echo '<tr><td colspan="6" align="right">G. Total</td>
                    <td>'.number_format(round($sum + $t_vat,2),2).'</td></tr>';
                    echo '<tr><td></td>
                    <td colspan="3" align="right">Compounding Code: <b>'.substr($sales_main['id'], -3).'</b> <button class="btn btn-primary" onclick="compoundingdone('.$sales_main['id'].')">Done</button></td>
                    <td><a class="btn btn-info" href="#" onclick="print_printable_document('.$sales_main['id'].')"  title="Print Label" data-toggle="tooltip" data-html="true"><span class="fa fa-print"></span></a></td></tr>';
                ?>
                
            </tbody>
        </table>
        </div>
        <div>
        <div class="col-md-5">
            <div class="modal fade" id="direction" tabindex="-1" role="dialog" aria-labelledby="direction" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">የአወሳሰድ አይነቶች ዝርዝር</h4>
                        </div>
                        <div class="modal-body">
                            <div class="panel-body">
                            <table class="table table-striped table-hover table-condensed table-bordered" id="template_list" style="font-size:12px;" >
                            <?php
                                $cnt=0;
                                foreach($compounding_remarks as $remarks){
                                    if($remarks != ""){
                                        $cnt++;
                                            echo '<tr style="cursor:pointer"><td onclick="add_selected_direction('.$cnt.')">'.$cnt.'</td><td onclick="add_selected_direction('.$cnt.')">'.$remarks.'<input id="remark_'.$cnt.'" type="hidden" value="'.$remarks.'"></td></tr>';
                                        }
                                    }
                            ?>
                            <tr><td><?=$cnt + 1?></td><td><div style="width: 100%;"><div style="float:left;width: 94%;"><textarea rows="2" style="color:red;font-size:14px;resize: none;" class="form-control" type="text" id="new_direction_template"></textarea></div><div style="float:left"><button class="btn btn-primary btn-sm" onclick="savenewdirectiontemplate(<?=$cnt+1?>)"><b>+</b></button></div></div></td></tr>
                        </table>

                        </div>
                    </div>

                    </div>
                </div>
            </div>
            <div class="panel panel-bd" style="font-size: 12px;">
                
                <div class="panel-body collapsed" id="collapsePrescription">
                    
                        <?php
                            foreach($prescription_group as $group){
                                $date = $group['prescriptions'][0]['created_at'] ?? $group['compoundings'][0]['created_at'] ?? '';
                                //$date = $group['prescriptions'][0]['created_at'] ?? '';
                                $prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? $group['compoundings'][0]['created_by_name'] ?? '';
                                //$prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? '';

                                echo '
                        <div class="col-lg-12">
                            <fieldset class="reset">
                                <legend class="reset"><span class="badge badge-qty-warning">'.$date.'</span> <span class=" badge badge-qty-warning">'.$prescribed_by.'</span></legend>';
                                
                                if(!empty($group['prescriptions'])){
                                    $prescription_detail = [];
                                    foreach($group['prescriptions'] as $prescriptions){
                                        array_push($prescription_detail, ...$prescriptions['details']);
                                    }

                                    echo view('Include/_prescription_print_medicine', ['prescription_detail'=>$prescription_detail]);
                                }

                                $compounding_mains = [];
                                foreach($group['compoundings'] as $compounding){
                                    array_push($compounding_mains, $compounding);
                                }

                                echo view('Include/_prescription_print_compounding', ['compounding_mains'=>$compounding_mains]);

                                echo '
                            </fieldset>
                        </div>';
                            }
                        ?>
                    
                </div>
            </div>
        </div>
        </div>
        <div id="printable" style="height: 0px; overflow: hidden; width:2.4in;">
            <div class="panel panel-bd" style="font-size: 9px;">
                <style>
                    table, th, td {
                    border: 0px solid black;
                    border-collapse: collapse;
                    };
                    th, td{
                    padding: 0px;
                    }
                </style>
                <!--div style="text-align: right;top:0px;border: 0px solid black;display:none">
                <table style="border: 0px solid black;margin-left: 15px;">
                    <tr>
                        <td><img src="/img/gelead_logo02.png" width="40px;"></td><td>&emsp;<b>Gelead Pharmacy|ገለአድ ፋርማሲ</b> <br>#2 Lebu 0114713135</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>
            </div-->
              
            <table style="text-align: left;margin-left: 20px;">
                        <tr>
                            <td colspan="2">
                                <div style="height:5px"></div>
                                <span style="margin-left:40px; width:18px; display:block; border:solid 1px; border-radius: 5px;"> <?= substr($sales_main['id'], -3) ?></span>
                                <span style="width:30px; display:inline-block;"></span><b><?= $sales_main['patient_name'] ?></b></td>
                        </tr>
                    <tbody style="margin-left: 20px;">
                        
                        <?php
                        
                            $sn = 0;
                            $sum = 0;
                            $t_vat = 0;
                            $row_tr = '';
                            foreach($sales_detail as $sales_line){
                                $sn++;
                                if ( $sn != count($sales_detail) ){
                            
                                
                                $sum += ($sales_line['quantity']*$sales_line['unit_price']);
                                $t_vat += ($sales_line['quantity']*$sales_line['vat']);
                                $row_tr .= '
                                <tr>
                                    <td>'.$sales_line['item_detail']['item_brand_name'].'</td>
                                    <td> '.round($sales_line['quantity'],2).($sales_line['item_detail']['compounding_measurement']).'</td>
                                
                                </tr>';
                                }
                            
                            }
                            echo $row_tr;
                            echo '<tr><td><b>Prepared Date: '.date('Y-m-d',strtotime($sales_line['created_at'])).'</b></td><td><b>exp.(ለሁለት ወር ብቻ!)</b></td></tr>';
                            
                        ?>
                        <tr><td colspan="2"><i id="label_direction"></i></td></tr>
                    </tbody>
                </table>
          
        </div>
        </div>
    </section>
</div>

<?= view('Include/_footer') ?>

<script>
    $(document).ready(function() {
        //alert($('#hidden_direction').val());
        document.getElementById("selected_direction").value = $('#hidden_direction').val();
        document.getElementById("label_direction").innerHTML = $('#hidden_direction').val();
    });
    function print_printable_document(sales_id){
        selected_direction = $('#selected_direction').val();
        if(selected_direction.length == 0){
            toastr['error']("Please add Direction First");
            return false;
        }
        addlabeldirection(sales_id);
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        document.getElementById("selected_direction").value = $('#hidden_direction').val();
        compoundingdone(sales_id);
}

function show_direction_list(){
                    $("#direction").modal('show');
                }
function add_selected_direction(selected_index){
                    let selected_direction = $('#remark_'+ selected_index).val()
                    //alert(x);
                    document.getElementById("selected_direction").value += selected_direction;
                    document.getElementById("label_direction").innerHTML += selected_direction;
                    $("#direction").modal('hide');
                }
function updatechangeonlabel(changed_value){
                    
                    document.getElementById("selected_direction").value = changed_value;
                    document.getElementById("label_direction").innerHTML = changed_value;
                }

function compoundingdone(sales_id) {
    

    $.ajax({
        url: '/inventory/dispensary/compoundingdone',
        data: {
            sales_id: sales_id 
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            if(result.status == "success"){
                toastr['success']('Compoundig Status Changed.');
                //location = '/inventory/dispensary/';
                //$('tr#'+prescription_group_id).remove();
            }
            else{
                toastr['error'](result.message);
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}
function addlabeldirection(sales_id) {
    selected_direction = $('#selected_direction').val();

    $.ajax({
        url: '/inventory/dispensary/addlabeldirection',
        data: {
            sales_id: sales_id ,selected_direction:selected_direction
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            if(result.status == "success"){
                toastr['success']('Direction Added.');
                //location = '/inventory/dispensary/';
                //$('tr#'+prescription_group_id).remove();
            }
            else{
                toastr['error'](result.message);
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}
function savenewdirectiontemplate(cnt){
        let value = $("#new_direction_template").val();
        $.ajax({
            method: 'post',
            url: '/inventory/dispensary/add_template_data',
            data: {"value":value.trim()},
            success: function(result, textStatus, jqXHR){
                try{
                    if(result == '')
                        throw 'Error';
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        location.reload();
                    }
                    else{
                        error_html = "Status: " + r.status + "<br>" + r.message;
                        dental_show_flash('addNewGeneral_Flash', error_html, false);
                    }
                }
                catch (e){
                    error_html = "Invalid retun result.<br><u>Result:</u><br>"+result;
                    dental_show_flash('addNewGeneral_Flash', error_html, false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Cannot add to database. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('addNewGeneral_Flash', error_html, false);
            }
        });
    }
</script>