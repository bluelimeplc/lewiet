<ul class="sidebar-menu">

<li class="treeview dispensarydashboard">
    <a href="/inventory/dispensary/sales_dashboard">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?><span class="cancelledappointments"></span></span>
    </a>
</li>
<li class="treeview sales_form">
    <a href="/inventory/dispensary/">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Sales</span>
    </a>
</li>
<li class="treeview compounding">
    <a href="/inventory/compounding/prepare/">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Compounding</span>
    </a>
</li>
<li class="treeview compounding_list">
    <a href="/inventory/dispensary/compounding_list/">
        <i class="fa fa-money" aria-hidden="true"></i><span>Pending Compounding List</span>
    </a>
</li>
<li class="treeview sales_list">
    <a href="/inventory/dispensary/sales_list/">
        <i class="fa fa-money" aria-hidden="true"></i><span>Sales List</span>
    </a>
</li>
<li class="treeview checkout">
    <a href="/inventory/dispensary/checkout/">
        <i class="fa fa-cart-plus" aria-hidden="true"></i><span>Check out</span>
    </a>
</li>
<li class="treeview srv_form">
    <a href="/inventory/dispensary/srv_form">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Store Request</span>
    </a>
</li>
<!--li class="treeview pricesetup">
    <a href="/inventory/store/pricesetup_form">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Price Setup</span>
    </a>
</li-->
<li class="treeview new_item">
    <a href="/inventory/store/new_item">
        <i class="fa fa-plus" aria-hidden="true"></i><span>New/Edit Items</span>
    </a>
</li>
<li class="treeview new_item_dispensary">
    <a href="/inventory/dispensary/new_item">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Pharmacy Items</span>
    </a>
</li>
<li class="treeview inventory">
    <a href="/inventory/dispensary/inventory">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Inventory</span>
    </a>
</li>
<li class="treeview inventory_count">
    <a href="/hospital/admin/setupdata">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Inventory Count2</span>
    </a>
    <ul class="treeview-menu">
        <li class="inventory_location"><a href="/inventory/dispensary/inventory_location"><i class="fa fa-plus" aria-hidden="true"></i><span>Item Location</span></a></li>
        <li class="inventory_item_with_no_location"><a href="/inventory/dispensary/inventory_item_with_no_location"><i class="fa fa-plus" aria-hidden="true"></i><span>Item With No Location</span></a></li>
        <li class="inventory_count_print"><a href="/inventory/dispensary/inventory_count_print"><i class="fa fa-plus" aria-hidden="true"></i>Print List</a></li>
    </ul>
</li>
<li class="treeview credit_receipts">
    <a href="/inventory/dispensary/credit_receipts">
        <i class="ti-receipt" aria-hidden="true"></i><span>Credit Receipts</span>
    </a>
</li>
<li class="treeview reports">
    <a href="/inventory/dispensary/reports">
        <i class="ti-receipt" aria-hidden="true"></i><span>Reports</span>
    </a>
    <ul class="treeview-menu">
        <li class="dispensary_grouped_item"><a href="/inventory/dispensary/pharmacy_grouped_item"><i class="fa fa-plus" aria-hidden="true"></i> Top Sales By Quantity </a></li>
        <li class="dispensary_grouped_item_price"><a href="/inventory/dispensary/pharmacy_grouped_item_price"><i class="fa fa-plus" aria-hidden="true"></i> Top Sales By Price </a></li>
        <li class="inventory_quantity_on_hand"><a href="/inventory/dispensary/inventory_quantity_on_hand"><i class="fa fa-plus" aria-hidden="true"></i> Top Count in Stores </a></li>
    </ul>
</li>
<!--li class="treeview adjustment">
    <a href="/adjustment/">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Adjustment <span class="color-red">Temporary</span></span>
    </a>
</li-->
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