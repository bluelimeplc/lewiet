<ul class="sidebar-menu">

<li class="treeview receptiondashboard">
    <a href="/hospital/reception/receptiondashboard">
        <i class="ti-home"></i> <span><?php

use phpseclib3\Math\BigInteger\Engines\PHP;

 echo "Dashboard"?> <span class="notifyapproval"></span></span>
    </a>
</li>
<li class="treeview new_patient">
    <a href="/hospital/reception">
        <i class="fa fa-plus" aria-hidden="true"></i><span>New Patient</span>
    </a>
</li> 

<li class="treeview search_patient">
    <a href="/hospital/reception/searchpatient">
        <i class="fa fa-search" aria-hidden="true"></i><span>Search Patient</span>
    </a>
</li>
<li class="treeview patientwaitinglist">
    <a href="/hospital/reception/waitinglist">
        <i class="fa fa-list" aria-hidden="true"></i>
        <span>Patient Waiting List</span>
        <span class="patientwaitinglist-count"></span>
    </a>
</li> 


<li class="treeview pending_bills">
    <a href="/hospital/reception/pendingbills">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Pending Bills <span class="countbills"></span> </span>
    </a>
</li>


<!--li class="treeview treatment_plans">
    <a href="/hospital/reception/treatmentplans">
        <i class="fa fa-tasks" aria-hidden="true"></i><span>Treatment Plans <span class="countplans"> </span>
    </a>
</li-->

<li class="treeview appointment">
    <a href="/hospital/reception/appointmentlist">
        <i class="fa fa-calendar" aria-hidden="true"></i><span>Appointments <span class="countappointments"></span></span>
    </a>
</li>

<li class="treeview printoutlist">
    <a href="/hospital/reception/printoutlist">
        <i class="ti-printer"></i><span>For Print <span class="countprints"></span></span>
    </a>
</li>

<!--li class="treeview recieved_payments">
    <a href="/hospital/reception/receivedbills">
        <i class="ti-receipt"></i><span>Recieved Payments</span>
    </a>
</li-->
<li class="treeview biopsy_followup">
    <a href="/hospital/reception/biopsyorders">
        <i class="ti-agenda"></i><span>Biopsy & Pathology <span class="countbiopsy"></span></span>
    </a>
</li>
<li class="treeview list">
    <a href="/cashier/">
        <i class="ti ti-money" aria-hidden="true"></i><span>Cashier</span>
    </a>
</li>
<li class="treeview cashierdashboard">
    <a href="/cashier/dashboard/">
        <i class="ti-home"></i> Cashier Dashboard
    </a>
</li>
<li class="treeview credit_receipts">
    <a href="/hospital/reception/credit_receipts/">
        <i class="ti-receipt"></i> Credit Receipts
    </a>
</li>
<li class="treeview sales_summary_report">
    <a href="/hospital/reception/clinic_bill_groupedby_service_name/">
        <i class="ti-home"></i> Sales Summary Report
    </a>
</li>
<li class="treeview patientqueue">
        <a href="/hospital/reception/patientqueue/">
            <i class="ti-list"></i> Patient Queue
        </a>
</li>

<!--li class="treeview labrequestlist">

    <a href="/hospital/reception/labrequestlist">
        <i class="ti-share"></i><span>Lab Request Followup <span class="countdelivery"></span></span>
    </a>
</li-->


<script type="text/javascript">
    $(document).ready(function () {
        notifybills();
        notifyprints();
        notifybiopsy();
        notifyappointments();
        notifylabdelivery();
        notifycancelledappointments();
        notifyplans();
        notifyappointments();
        notifywaitinglist();
        notifyreportapproval();
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }
    });
    function notifyplans(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationplans",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countplans");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyplans()",5000);
    function notifyreportapproval(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotifyreportapproval",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".notifyapproval");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyreportapproval()",5000);
    function notifybills(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationbills",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countbills");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifybills()",5000);
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
    function notifybiopsy(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationbiopsy",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countbiopsy");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifybiopsy()",5000);
    function notifyappointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countappointments");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyappointments()",5000);
    function notifywaitinglist(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationwaitinglist",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".patientwaitinglist-count");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifywaitinglist()",5000);
    function notifypendingappointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationpendingappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".pendingappointments");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifyappointments()",5000);
    function notifylabdelivery(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationlabdelivery",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countdelivery");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifylabdelivery()",5000);
    function notifycancelledappointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationcancelledappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".cancelledappointments");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifycancelledappointments()",5000);
</script>