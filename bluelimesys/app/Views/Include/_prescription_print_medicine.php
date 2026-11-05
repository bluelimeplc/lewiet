        
<table class="table table-bordered table-condensed"  style="border-width:1px;border-color:black; margin-bottom:2px;">
    <thead class="<?= empty($prescription_detail)?'hide':'' ?>">
        <tr style="border-top: solid 2px;">
            <th style="border-width:1px;border-color:black;">Drug</th>
            <th style="border-width:1px;border-color:black;">Route</th>
            <th style="border-width:1px;border-color:black;">Direction</th>
            <th style="border-width:1px;border-color:black;">NOD</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($prescription_detail as $detail):
                $drug_detail = trim($detail['drug_detail']['item_brand_name']);
                if(($drug_detail != '') && ($drug_detail != trim($detail['drug_detail']['item_generic_name'])) && (trim($detail['drug_detail']['item_generic_name']) != '')){
                    $drug_detail = "<b>$drug_detail</b> / ".trim($detail['drug_detail']['item_generic_name']);
                }
                elseif(trim($detail['drug_detail']['item_generic_name']) != '')
                    $drug_detail = trim($detail['drug_detail']['item_generic_name']);

                if(trim($detail['drug_detail']['strength'])!='')
                    $drug_detail .= ' / <span class="badge badge" style="padding:3px 3px;">'.$detail['drug_detail']['strength'].'</span>';
                if(trim($detail['drug_detail']['preparation'])!='')
                    $drug_detail .= ' / <span class="badge badge-qty" style="padding:3px 3px;">'.$detail['drug_detail']['preparation'].'</span>';
        ?>       
                <tr>
                    <td style="border-width:1px;border-color:black;"><?= $drug_detail ?></td>
                <?php
                    if(trim($detail['custom_direction']) != ''):
                ?>
                    <td style="border-width:1px;border-color:black;" colspan="3"><?=str_replace("\n","<br>",$detail['custom_direction']) ?></td>
                <?php
                    else:
                ?>
                    <td style="border-width:1px;border-color:black;"><?=$detail['route'] ?></td>
                    <td style="border-width:1px;border-color:black;"><?=$detail['direction'] ?></td>
                    <td style="border-width:1px;border-color:black;"><?=$detail['number_of_days'] ?></td>
                <?php
                    endif;
                ?>
                </tr>      
        <?php
            endforeach;
        ?>
    </tbody>
</table>