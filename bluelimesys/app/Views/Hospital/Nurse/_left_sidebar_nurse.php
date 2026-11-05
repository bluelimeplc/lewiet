<ul class="sidebar-menu">

<li class="treeview nursedashboard">
    <a href="/hospital/nurse/nursedashboard">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?></span>
    </a>
</li>
<li class="treeview patients_waiting">
    <a href="/hospital/nurse">
        <i class="fa fa-user" aria-hidden="true"></i><span>Patients Waiting <span class="countwaitings"></span></span>
    </a>
</li>
<li class="treeview waiting_list">
    <a href="/hospital/nurse/waitinglist">
        <i class="fa fa-list" aria-hidden="true"></i><span>Waiting List</span>
    </a>
</li> 
<li class="treeview nurse_orders">
    <a href="/hospital/nurse/orders">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders <span class="countorders"></span></span>
    </a>
</li> 

<li class="treeview appointments">
    <a href="/hospital/nurse/appointmentlist">
        <i class="fa fa-calendar" aria-hidden="true"></i><span>Appointments <span class="countappointments"></span>
    </a>
</li> 

<li class="treeview printoutlist">
    <a href="/hospital/nurse/printoutlist">
        <i class="ti-printer" aria-hidden="true"></i><span>For Print <span class="countprints"></span></span>
    </a>
</li>
<li class="treeview patientqueue">
        <a href="/hospital/nurse/patientqueue/">
            <i class="ti-list"></i> Patient Queue
        </a>
</li>
<li class="treeview previousvisit">
        <a href="/hospital/nurse/previousvisit/">
            <i class="fa fa-address-card"></i> All Patients
        </a>
</li>
<li class="treeview report">
        <a href="#">
            <i class="fa fa-file-excel-o"></i> Reports
        </a>
    <ul class="treeview-menu">
        <li class="report_daily_new_patient"><a href="/hospital/nurse/report_daily_new_patients/"><i class="fa fa-plus" aria-hidden="true"></i> Daily New Patients </a></li>
        <li class="report_diagnosis_dhis"><a href="/hospital/nurse/report_diagnosis_by_age_and_sex?type=dhis<?= (isset($_GET['date_from'])?'&date_from='.$_GET['date_from']:'')?><?= (isset($_GET['date_to'])?'&date_to='.$_GET['date_to']:'')?>"><i class="fa fa-plus" aria-hidden="true"></i> Diagnosis By Age and<br>&emsp13;&nbsp;&nbsp;&nbsp;&nbsp;Sex DHIS</a></li>
        <li class="report_diagnosis_doctors"><a href="/hospital/nurse/report_diagnosis_by_age_and_sex?type=doctors<?= (isset($_GET['date_from'])?'&date_from='.$_GET['date_from']:'')?><?= (isset($_GET['date_to'])?'&date_to='.$_GET['date_to']:'')?>"><i class="fa fa-plus" aria-hidden="true"></i> Diagnosis By Age and<br>&emsp13;&nbsp;&nbsp;&nbsp;&nbsp;Sex Doctors</a></li>
        <li class="report_patient_count"><a href="/hospital/nurse/report_patient_count_by_age"><i class="fa fa-plus" aria-hidden="true"></i> Patient Count By Age<br>&emsp13;&nbsp;&nbsp;&nbsp;&nbsp;and Sex</a></li>
    </ul>
</li>
<li class="treeview srv_form">
    <a href="/hospital/nurse/srv_form">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Store Request</span>
    </a>
</li>
<li class="treeview biopsy_followup">
    <a href="/hospital/nurse/biopsyorders">
        <i class="ti-receipt"></i><span>Biopsy Followup</span>
    </a>
</li>


<script type="text/javascript">
    $(document).ready(function () {
        notifyprints();
        notifyappointments();
        notifywaitings();
        // var active_menu = '.<?= $active_menu; ?>';
        // if (active_menu != '.') {
        //     $(active_menu).addClass('active');
        // }
    });

    $(document).ready(function () {
        let active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }

        let active_sub_menu = '.<?= $active_sub_menu ?? ''; ?>';
        if (active_sub_menu != '.') {
            $(active_sub_menu).addClass('active');
        }
    });
    
    function notifyprints(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationprints",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countprints");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyprints()",5000);
    function notifyappointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countappointments");
                var container = $(".countorders");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyappointments()",5000);
    function notifywaitings(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationwaitingnurse",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countwaitings");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifywaitings()",5000);
</script>
