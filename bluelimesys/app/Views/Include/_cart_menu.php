<?php
    $number_of_op = $number_of_op ?? 0;
?>
<div class="col-lg-12">
    <div class="panel panel-bd panel-form">
        <div class="panel-body">
            
            <a href="/inventory/dispensary/checkout" role="button" class="btn btn-<?=(intval($number_of_sales)>0?'danger':'warning')?>" style="float: right;">
                <i class="fa fa-cart-plus"></i> Check out
                <span class="badge badge-number" style="position: absolute; display: <?=(intval($number_of_sales)>0?'inline-block':'none')?>; margin-top: 5px; background-color: lightsalmon; color: black;"><?=$number_of_sales?></span>
            </a>&emsp;
            <a href="/inventory/dispensary/onlineprescriptions" role="button" class="btn btn-<?=(intval($number_of_op)>0?'primary':'success')?>" style="float: left;display: <?=(intval($number_of_op)>0?'inline-block':'none')?>;">
                <i class="fa fa-globe"></i>
                <span class="badge badge-number" style="position: absolute;  margin-top: 5px; background-color: lightsalmon; color: black;"><?=$number_of_op?></span>
            </a>
        </div>
    </div>
</div>