<?php
    $company_header = \BluelimeSystem::get_company_header();
?>
    <div class="row">
            <div class="form-group"  style="float:left; border-color: coral;">
                &emsp;
            </div> 
            <div style="height: 20mm; float:left">
                <?php echo '<img  class="img img-responsive" width="200"  style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode( $company_header['logo'] ).'"/>' ?>
                <h4><?=$company_header['company_name']?></h4>
            </div>
            <div class="form-group"  style="float:right; border-color: coral; margin-right:10mm">
                <span style="font-size:11px; float:right; text-align:justify">
                <b>Mob.</b> <?=$company_header['mobile']?> 
                <br> <b>Tel.</b> <?=$company_header['tel']?>
                <br> <b>Email.</b> <?=$company_header['email']?>
                <br> <b>website.</b> <?=$company_header['website']?>
                <br> <b>Location.</b> <?=$company_header['location']?>
        </span>
            </div>
                                                    
    </div>
        <hr>