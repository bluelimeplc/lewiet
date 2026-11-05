<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th rowspan="2">&nbsp;</th>
            <th width="100">Date</th>
            <th>FS N<u>o</u></th>
            <th>Patient Name</th>
            <th style="text-align: center;">Services</th>
            <th>Total</th>
        </tr>
        
    </thead>
    <tbody>
        
        <?php
            $active = true;
            if(!empty($paymentRequest['status']))
                $active = false;

            $gtotal = 0;
            $sn = 1;
            foreach($receipt_list as $receipt){
                $gtotal += $receipt['total'];
                echo '
        <tr>
            <td>' . $sn++ . '</td>
            <td>' . substr($receipt['date'],0,10) . '</td>
            <td><a href="/inventory/finance/receipt?fs_no='.$receipt['fs_number'].'&department=Hospital" target="_blank">' . $receipt['fs_number'] . '</a></td>
            <td>' . $receipt['patient_name'] . '</td>
            <td>
                <table class="table table-condensed table-borderless" style="margin: 0px; padding: 0px;">
                ';
            
            foreach($receipt['services'] as $service){
                echo '<tr>
                        <td style="padding: 2px; margin: 0px; border:solid 1px #fff !important;">' . $service['service_name'] . '</td>
                        <td style="padding: 2px; margin: 0px; border:solid 1px #fff !important;" width="50" align="right" >' . number_format($service['bill_quantity'] * $service['bill_amount']) . '</td>
                    </tr>';
            }

        echo '
                </table>
            </td>
            <td align="right">' . number_format($receipt['total']) . '</td>
            <td><a role="button" '.(!$active?'disabled':'').' class="btn '.($active?'btn-danger':'').'" '.($active?'onclick="delete_undelete(this, \'clinic\', '.$receipt['id'].', '.$paymentRequest['id'].')"':'').' ><i class="fa fa-trash"></i></a></td>
        </tr>
                ';
            }
        ?>
        <tr>
            <td colspan="5" align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= number_format($gtotal) ?></b></td>
        </tr>
    </tbody>
</table>