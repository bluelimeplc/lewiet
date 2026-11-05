<ul class="sidebar-menu">

<li class="treeview admindashboard">
    <a href="/hospital/admin/admindashboard">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?></span>
    </a>
</li>
<li class="treeview usermanagment">
    <a href="/hospital/admin">
        <i class="fa fa-user" aria-hidden="true"></i><span>User Managment</span>
    </a>
</li> 

<li class="treeview setupdata">
    <a href="/hospital/admin/setupdata">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Setup Data</span>
    </a>
    <ul class="treeview-menu">
        <li><a href="/hospital/admin/pricesetup?plid=<?= \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID ?>"><i class="fa fa-plus" aria-hidden="true"></i> Price Setup </a></li>
        <li><a href="/hospital/admin/newservice"><i class="fa fa-plus" aria-hidden="true"></i> Add / Edit Service </a></li>
        <li><a href="/hospital/admin/newcompany"><i class="fa fa-plus" aria-hidden="true"></i> Add / Edit Company </a></li>
        <li><a href="/hospital/admin/labsetup"><i class="fa fa-plus" aria-hidden="true"></i> Labratory Setup </a></li>
        <li><a href="/hospital/admin/proceduresetup"><i class="fa fa-plus" aria-hidden="true"></i> Nurse Procedure Setup </a></li>
    </ul>
</li>


<li class="treeview reports">
    <a href="/hospital/report">
        <i class="fa fa-file" aria-hidden="true"></i><span>Reports</span>
    </a>
</li>
<li class="treeview headerinfo">
    <a href="/hospital/admin/headerinfo">
        <i class="fa fa-header" aria-hidden="true"></i><span>Company Header</span>
    </a>
</li> 
<li class="treeview posmachine">
    <a href="/hospital/admin/pos_machine">
        <i class="fa fa-american-sign-language-interpreting" aria-hidden="true"></i><span>POS Machine</span>
    </a>
</li>
<li class="treeview pending_bill">
    <a href="/hospital/admin/pendingbilldeletion">
        <i class="ti-trash"></i><span>Pending Bill</span>
    </a>
</li>
<li class="treeview api_setting">
    <a href="/hospital/admin/api_setting">
        <i class="ti-settings"></i><span>API Setting</span>
    </a>
</li>


<script type="text/javascript">
    $(document).ready(function () {
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }
    });
</script>
