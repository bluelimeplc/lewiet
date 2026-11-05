            
            
                <div style="min-height: 300px; font-size: smaller;">
                Ordered By: <?=$prescription_main['doctor']?> &emsp; &emsp; &emsp; &emsp; Date: <?=$prescription_main['registered_date']?>
                    <table class="table table-bordered table-condensed"  style="border-width:1px;border-color:black;">
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
                            <?php
                                $cnt = 65;
                                foreach($compounding_mains as $compounding_main):
                            ?>
                                    <table class="table table-bordered table-condensed"  style="border-top:solid 2px; margin-top:-15px">
                                        <tr><th colspan="3">Compounding <?= chr($cnt++) ?></th></tr>
                                        <?php
                                            $is_first_line = true;
                                            foreach($compounding_main['compounding_detail'] as $compounding_detail):
                                                $detail = $compounding_detail;
                                                $drug_detail = trim($detail['item_detail']['item_brand_name']);

                                                if(($drug_detail != '') && ($drug_detail != trim($detail['item_detail']['item_generic_name'])) && (trim($detail['item_detail']['item_generic_name']) != '')){
                                                    $drug_detail = "<b>$drug_detail</b> / ".trim($detail['item_detail']['item_generic_name']);
                                                }
                                                elseif(trim($detail['item_detail']['item_generic_name']) != '')
                                                    $drug_detail = trim($detail['item_detail']['item_generic_name']);

                                                if(trim($detail['item_detail']['preparation'])!='')
                                                    $drug_detail .= ' / <span class="badge badge-qty" style="padding:3px 3px;">'.$detail['item_detail']['preparation'].'</span>';

                                                $item_name = $drug_detail; //'<span style="float:right" class="badge badge-qty">'.$compounding_detail['item_detail']['preparation'].'</span><b>'.$compounding_detail['item_detail']['item_brand_name'].'</b><br>'.$compounding_detail['item_detail']['item_generic_name'];
                                                $compounding_detail['amount'] = $compounding_detail['measurement'] == '%' ? $compounding_detail['in_percent'] : $compounding_detail['amount'];
                                        ?>
                                                <tr>
                                                    <td><?= $item_name ?></td><td><?= $compounding_detail['amount'].' '.$compounding_detail['measurement'] ?></td>
                                                    <?php
                                                        if($is_first_line)
                                                            echo '<td rowspan="'.count($compounding_main['compounding_detail'] ).'"><u>Description</u><br>'.str_replace("\n","<br>",$compounding_main['description']).'</td>';
                                                    ?>
                                                </tr>

                            <?php
                                                $is_first_line = false;
                                            endforeach;
                            ?>
                                    </table>
                            <?php
                                endforeach;
                            ?>
                    </table>
                    
                </div>
        