<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th width="50">&nbsp;</th>
            <th width="100">Date</th>
            <th>FS N<u>o</u></th>
            <th>Patient Name</th>
            <th>Medicine Price</th>
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
                $gtotal += $receipt['total_amount'];
                echo '
        <tr>
            <td>' . $sn++ . '</td>
            <td>' . substr($receipt['date'],0,10) . '</td>
            <td><a href="/inventory/finance/receipt?fs_no='.$receipt['fs_number'].'&department=Inventory" target="_blank">' . $receipt['fs_number'] . '</a></td>
            <td>' . $receipt['patient_name'] . '</td>
            <td align="right">' . number_format($receipt['total_amount'],2) . '</td>
            <td><a role="button" '.(!$active?'disabled':'').' class="btn '.($active?'btn-danger':'').'" '.($active?'onclick="delete_undelete(this, \'pharmacy\', '.$receipt['id'].', '.$paymentRequest['id'].')"':'').' ><i class="fa fa-trash"></i></a></td>
        </tr>
                ';
            }
        ?>
        <tr>
            <td colspan="4" align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= number_format($gtotal,2) ?></b></td>
        </tr>
    </tbody>
</table>