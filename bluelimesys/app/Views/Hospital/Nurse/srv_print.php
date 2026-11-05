<?php
    $srv_main = $this->data['srv_main'];
    $srv_detail = $this->data['srv_detail'];
    //\BluelimeSystem::show_and_die($srv_detail);
    
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Hospital/Nurse/_left_sidebar_nurse') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Nurse</h1>
            <small>Store Request Voucher</small>

            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
                <li onclick="delete_srv()" role="button">Delete</li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?php
            $print_data = [
                'document_no' => $srv_main['id'],
                'document_title' => 'የእቃ መጠየቂያ<br>Store Request Voucher',
                'document_date' => $srv_main['created_at'],
                'footer1' => 'Requested By',
                'footer2' => 'Approved By',
                'recieved_by' => $srv_main['requested_by'],
            ];
        ?>
        <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
        <table class="table-borderless table">
            <tr>
                <td width="100" align="right"><b>Code:</b></td><td><?= $srv_main['srv_code'] ?></td>
                <td width="100" align="right"><b>Requesting Store:</b></td><td><?= $srv_main['requesting_store'] ?></td>
                <td width="100" align="right"><b>Requested Store:</b></td><td><?= $srv_main['requested_store'] ?></td>
                
            </tr>
            <tr>
                <td align="right"><b>Date:</b></td><td><?= substr($srv_main['request_date'],0,10) ?></td>
            </tr>
        </table>
        
        <table class="table table-bordered" style="text-align: right;">
            <thead>
                <tr>
                    <th>N<u>o</u></th><th>Item Code</th><th>Item Name</th><th>Meas</th><th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    
                    $sn = 0;
                    $row_tr = '';
                    foreach($srv_detail as $srv_line)
                    {
                        $sn++;
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td>
                            <td>'.$srv_line['item_detail']['item_code'].'</td>
                            <td>'.$srv_line['item_detail']['item_generic_name'].' | '.$srv_line['item_detail']['item_brand_name'].'</td>
                            <td>'.$srv_line['item_detail']['measurement'].'</td>
                            <td>'.$srv_line['quantity'].'</td>
                        </tr>';
                    }
                    echo $row_tr;
                ?>
                
            </tbody>
        </table>
        

        <?= view('Include/_print_footer_A4_Portrait') ?>
    </section>
</div>
<script>
    function delete_srv(){
        if(confirm('Are you sure?'))
            window.location = '/hospital/nurse/delete_srv?srv_id=<?= $_GET['srv_id'] ?>';
    }
</script>

<?= view('Include/_footer') ?>