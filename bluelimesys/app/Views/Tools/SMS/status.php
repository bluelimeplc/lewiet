<?php
    $status = $this->data['status'];
    $log = $this->data['log'];
?>
<div class="" style="background-color: #fff; padding: 10px;">
    <div class="row">
        <div class="col-sm-5" style="border-right: solid 1px;">
            <h4>Status</h4>
            <?= $status; ?>
            <br>
            <hr>
        </div>
        <div class="col-sm-6">
            <h4>Log</h4>
            <pre><?= $log; ?></pre>
        </div>
    </div>
</div>