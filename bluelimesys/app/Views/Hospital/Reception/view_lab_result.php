<?php
    helper('form');
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $doc_sign  = $loggeduserdata['digital_signature'];
?>

    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Reception</h1>
                <small>Result View</small>
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>
            </span>
            </div>
            
        </section>
        
        <section class="content">
        <?php
            $print_data = [
                'document_no' => sprintf('%05d', $order_result[0]['id'] ),
                'document_title' => 'የላብራቶሪ ውጤት<br>Labratory Result',
                'document_date' =>$order_result[0]['created_at'],
            ];
        ?>
        <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <table class="table table-condensed" id="printable">
            <tr style="border: none">
                <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $order_result[0]['patient_detail']['patient_name'] ?></td>
                <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $order_result[0]['patient_detail']['patient_id'] ) ?></td>
            </tr>
            <tr>
            <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?php echo \BluelimeSystem::year_difference($order_result[0]['patient_detail']['date_of_birth']??"",date("Y/m/d")??0) ?></td>
                <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $order_result[0]['patient_detail']['sex']??"" ?></td>
            </tr>
        </table>
        
        
            <div>
                <table class="tale table-condensed" width="100%" align="center">
                    <thead>
                        <tr>
                            <th  style='border-width:1px;border-color:black;'></th>
                            <th  style='border-width:1px;border-color:black;'>Test</th>
                            <th  style='border-width:1px;border-color:black;'>Result</th>
                            <th  style='border-width:1px;border-color:black;'>Reference Range</th>
                        </tr>
                    </thead>
                    <tbody style="filter: blur(4px);">
                        <?php
                            $old_id = 0;
                            foreach($order_result as $detail):
                                if($detail['result_flag'] == 1) 
                                        $icon = 'text-danger fa fa-arrow-up';
                                        else if($detail['result_flag'] == -1)
                                        $icon = 'text-danger fa fa-arrow-down';
                                        else
                                        $icon = '';
                                if( $old_id != $detail['test_id'] ) {
                                    echo "<tr><td colspan=3  style='border: none'><b>".$detail['test_name']."</b></td>";
                                ?>
                                <tr>
                                <td  style="border: none">&nbsp</td>
                                <td  style="border: none"><?=$detail['attribute_name']?> <i class="<?=$icon ?>"></i> </td>
                                <td  style="border: none"><?=$detail['result'] ?></td>
                                <td  style="border: none"><?=$detail['reference_range'] ?></td>
                                </tr>
                                <?php
                                } else {
                                
                        ?>       
                                
                                <tr>
                                <td  style="border: none">&nbsp</td>
                                <td  style="border: none"><?=$detail['attribute_name'] ?> <i class="<?=$icon ?>"></i> </td>
                                <td  style="border: none"><?=$detail['result'] ?></td>
                                <td  style="border: none"><?=$detail['reference_range'] ?></td>
                                </tr>      
                        <?php
                                }
                                $old_id = $detail['test_id'];
                            endforeach;
                        ?>  
                          
                        
                    </tbody>
                </table>
                    
            </div>
            <br>
            <br>
            <br>
            
            <div class="col-md-12" style="text-align: center; width:100%">
                <div style="text-align: center; padding: 5px;float: right;">
                <?php //echo '<img style="height:80px;" src="data:image/jpeg;base64,'.base64_encode( $doc_sign ).'"/>' ?><br>
                    <u><?= $order_result[0]['approved_by'] ?></u><br>
                    <b>Approved By<br></b>
                </div>
            </div>
    </section>
</div>
<script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>