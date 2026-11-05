<ul class="sidebar-menu">

<li class="treeview admindashboard">
    <a href="/hospital/admin/admindashboard">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?></span>
    </a>
</li>


<li class="treeview reports">
    <a href="/hospital/report">
        <i class="fa fa-file" aria-hidden="true"></i><span>Reports</span>
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
