<ul class="sidebar-menu">

<li class="treeview financedashboard">
    <a href="/inventory/finance/financedashboard/">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?><span class="cancelledappointments"></span></span>
    </a>
</li>
<li class="treeview pharmacy_items">
    <a href="/inventory/finance/pharmacy_items">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Pharmacy Items</span>
    </a>
</li>
<li class="treeview grv">
    <a style="cursor: pointer;">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>GRV</span>
    </a>
    <ul class="treeview-menu">
        <li class="grv_list"><a href="/inventory/finance/"><i class="fa fa-plus" aria-hidden="true"></i> GRV List </a></li>
        <li class="vat_purchases"><a href="/inventory/finance/vat_purchases"><i class="fa fa-plus" aria-hidden="true"></i> VAT Purchases</a></li>
        <li class="none_vat_purchases"><a href="/inventory/finance/none_vat_purchases"><i class="fa fa-plus" aria-hidden="true"></i> None VAT Purchases</a></li>
    </ul>
</li>
<li class="treeview pendingsiv">
    <a href="/inventory/finance/siv_list">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>SIV</span>
    </a>
</li>
<li class="treeview sales_report_pharmacy">
    <a style="cursor: pointer;">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Sales Report Pharmacy</span>
    </a>
    <ul class="treeview-menu">
        <li class="daily_sales_export"><a href="/inventory/finance/pharmacy_daily_sales_export"><i class="fa fa-plus" aria-hidden="true"></i> Daily Sales Export<br>&nbsp;&emsp; (Peachtree) </a></li>
        <li class="sales_by_receipt_pharmacy"><a href="/inventory/finance/sales_by_receipt_pharmacy"><i class="fa fa-plus" aria-hidden="true"></i> Sales By Receipt </a></li>
    </ul>
</li>
<li class="treeview sales_report_clinic">
    <a style="cursor: pointer;">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Sales Report Clinic</span>
    </a>
    <ul class="treeview-menu">
        <li class="daily_sales_export"><a href="/inventory/finance/clinic_daily_sales_export"><i class="fa fa-plus" aria-hidden="true"></i> Daily Sales Export<br>&nbsp;&emsp; (Peachtree) </a></li>
        <li class="sales_by_receipt_clinic"><a href="/inventory/finance/sales_by_receipt_clinic"><i class="fa fa-plus" aria-hidden="true"></i> Sales By Receipt </a></li>
    </ul>
</li>
<li class="treeview receipt">
    <a href="/inventory/finance/receipt">
        <i class="fa fa-money" aria-hidden="true"></i><span>Receipt</span>
    </a>
</li>
<li class="treeview doctors_work">
    <a href="/inventory/finance/doctors_work">
        <i class="fa fa-file" aria-hidden="true"></i><span>Doctors Work</span>
    </a>
</li>
<li class="treeview creditpayment">
    <a href="/hospital/admin/setupdata">
        <i class="ti-credit-card" aria-hidden="true"></i><span>Credit Payment</span>
    </a>
    <ul class="treeview-menu">
        <li class="receipt_list"><a href="/inventory/finance/creditpayment"><i class="fa fa-plus" aria-hidden="true"></i> Group Receipt List </a></li>
        <?php
            $branch_name = strtolower(env('BRANCH'));
            if(($branch_name == 'lebu') || ($branch_name == 'abiy dev'))
                echo '<li class="credit_payment_invoice_list"><a href="/inventory/finance/credit_payment_invoice_list"><i class="fa fa-plus" aria-hidden="true"></i> Compile Invoice Request </a></li>';
            else
                echo '<li class="credit_payment_invoice_list"><a style="cursor: not-allowed;" title="Works only on Lebu branch"><i class="fa fa-plus" aria-hidden="true"></i> Compile Invoice Request </a></li>';
        ?>
    </ul>
</li>

<li class="treeview cashierdashboard">
    <a href="/cashier/dashboard">
        <i class="ti-credit-card"></i><span>Cashiers Dashboard</span>
    </a>    
</li>

<li class="treeview setupdata">
    <a href="/hospital/admin/setupdata">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Setup Data</span>
    </a>
    <ul class="treeview-menu">
        <li><a href="/inventory/finance/pricesetup?plid=<?= \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID ?>"><i class="fa fa-plus" aria-hidden="true"></i> Price Setup </a></li>
        <li><a href="/inventory/finance/newservice"><i class="fa fa-plus" aria-hidden="true"></i> Add / Edit Service </a></li>
        <li><a href="/inventory/finance/newcompany"><i class="fa fa-plus" aria-hidden="true"></i> Add / Edit Company </a></li>
    </ul>
</li>
<li class="treeview inventory">
    <a style="cursor: pointer;">
        <i class="fa fa-list" aria-hidden="true"></i><span>Inventory</span>
    </a>
    <ul class="treeview-menu">
        <li class="quantity_on_hand"><a href="/inventory/finance/inventory_quantity_on_hand"><i class="fa fa-plus" aria-hidden="true"></i> Quantity On Hand </a></li>
        <li class="count_setup"><a href="/inventory/finance/inventory_count_setup"><i class="fa fa-plus" aria-hidden="true"></i> Count Setup </a></li>
        <li class="count_record"><a href="/inventory/finance/inventory_count_record"><i class="fa fa-plus" aria-hidden="true"></i> Count Record </a></li>
    </ul>
</li>
<li class="treeview reports">
    <a style="cursor: pointer;">
        <i class="fa fa-list" aria-hidden="true"></i><span>Reports</span>
    </a>
    <ul class="treeview-menu">
        <li class="report_sales_receipts"><a href="/inventory/finance/report_sales_receipts"><i class="fa fa-plus" aria-hidden="true"></i> Sales Receipts</a></li>
        <li class="report_compounding_sales_detail"><a href="/inventory/finance/compounding_sales_detail"><i class="fa fa-plus" aria-hidden="true"></i> Compounding Sales Detail</a></li>
        <li class="report_sales_period"><a href="/inventory/finance/report_sales_period"><i class="fa fa-plus" aria-hidden="true"></i> Sales Report Period</a></li>
        <li class="report_sales_date_range"><a href="/inventory/finance/report_sales_date_range"><i class="fa fa-plus" aria-hidden="true"></i> Sales Report Date Range</a></li>
        <li class="report_all_item_transaction"><a href="/inventory/finance/all_item_transaction"><i class="fa fa-plus" aria-hidden="true"></i> All Items Transaction</a></li>
        <li class="report_sales_chart"><a href="/inventory/finance/report_sales_chart"><i class="fa fa-plus" aria-hidden="true"></i> Sales Report Chart</a></li>
    </ul>
</li>
<li class="treeview other_links">
    <a style="cursor: pointer;">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Other Links</span>
    </a>
    <ul class="treeview-menu">
        
        <li class="dispensary_grouped_item"><a href="/inventory/finance/pharmacy_grouped_item?sales_type=<?= BluelimeConfiguration::SALES_TYPE::DISPENSARY ?><?= ((($_GET['selected_date']??'')!='') && (($_GET['end_date']??'')!=''))?'&selected_date='.$_GET['selected_date'].'&end_date='.$_GET['end_date']:''?>&receipt_type=<?=($_GET['receipt_type']??'Cash Register')?>&payment_method=<?=($_GET['payment_method']??'Cash')?>"><i class="fa fa-plus" aria-hidden="true"></i>Dispensary Items By<br>&nbsp;&emsp;  Selling Price</a></li>
        <li class="compounding_grouped_item"><a href="/inventory/finance/pharmacy_grouped_item?sales_type=<?= BluelimeConfiguration::SALES_TYPE::COMPOUNDING ?><?= ((($_GET['selected_date']??'')!='') && (($_GET['end_date']??'')!=''))?'&selected_date='.$_GET['selected_date'].'&end_date='.$_GET['end_date']:''?>&receipt_type=<?=($_GET['receipt_type']??'Cash Register')?>&payment_method=<?=($_GET['payment_method']??'Cash')?>"><i class="fa fa-plus" aria-hidden="true"></i>Compunding Items By<br>&nbsp;&emsp; Selling Price</a></li>
        <li class="cash_sales_grouped_item"><a href="/inventory/finance/cash_sales_grouped_item<?= ((($_GET['selected_date']??'')!='') && (($_GET['end_date']??'')!=''))?'?selected_date='.$_GET['selected_date'].'&end_date='.$_GET['end_date']:''?>"><i class="fa fa-plus" aria-hidden="true"></i>Cash Sales Summary</a></li>
        <li class="daily_sales_by_payment_type"><a href="/inventory/finance/daily_sales_by_payment_type?department=Inventory"><i class="fa fa-plus" aria-hidden="true"></i> Daily Sales By Payment<br>&nbsp;&emsp; Type (<u>Zeni</u>) </a></li-->
        
        <!--li class="clinic_bill_groupedby_service_type"><a href="/inventory/finance/clinic_bill_groupedby_service_type"><i class="fa fa-plus" aria-hidden="true"></i> Clinic Grouped Service </a></li-->
    
    </ul>
</li>
</ul>
<script>
    $(document).ready(function () {
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }

        var active_sub_menu = '.<?= $active_sub_menu ?? ''; ?>';
        if (active_sub_menu != '.') {
            $(active_sub_menu).addClass('active');
        }
    });
</script>