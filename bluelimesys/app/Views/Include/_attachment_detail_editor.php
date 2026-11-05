<div>
    <table class="table table-condensed">
        <tr><th>Name</th><td><?= $editor_data['name']['has_difference']?'<i class="fa fa-flag color-red" aria-hidden="true"></i>':'&nbsp;&nbsp;&nbsp;&nbsp;' ?> <?= $editor_data['name']['data'] ?></td></tr>
        <tr><th>Company</th><td><?= $editor_data['company_name']['has_difference']?'<i class="fa fa-flag color-red" aria-hidden="true"></i>':'&nbsp;&nbsp;&nbsp;&nbsp;' ?> <?= $editor_data['company_name']['data'] ?></td></tr>
        <tr><th>Payment</th><td><?= $editor_data['payment_method']['has_difference']?'<i class="fa fa-flag color-red" aria-hidden="true"></i>':'&nbsp;&nbsp;&nbsp;&nbsp;' ?> <?= $editor_data['payment_method']['data'] ?></td></tr>
        
    </table>
</div>