<?php
    $active_menu = $this->data['active_menu'];
?>

            <div class="popup" data-backdrop="static" id="popup" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <div id="dentalAlertTitleIcon" class=" dental-flag-icon">
                                <i class="fa fa-info-circle color-info dental-flag-icon"></i>
                                <i class="fa fa-warning color-yellow dental-flag-icon" style="display:none"></i>
                            </div>
                            <h4 class="modal-title" id="dentalAlertTitle">Laboratory Result Ready</h4>
                        </div>
                        <div class="modal-body">
                            <div class="resultpopup"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popupnurse" data-backdrop="static" id="popupnurse" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <div id="dentalAlertTitleIcon" class=" dental-flag-icon">
                                <i class="fa fa-info-circle color-info dental-flag-icon"></i>
                                <i class="fa fa-warning color-yellow dental-flag-icon" style="display:none"></i>
                            </div>
                            <h4 class="modal-title" id="dentalAlertTitle">Nurse Procedure Result Ready</h4>
                        </div>
                        <div class="modal-body">
                            <div class="resultpopupnurse"></div>
                        </div>
                    </div>
                </div>
            </div>
<ul class="sidebar-menu">

<li class="treeview doctordashboard">
    <a href="/hospital/doctor/doctordashboard">
        <i class="ti-home"></i> <span><?php echo "Dashboard"?></span>
    </a>
</li>
<li class="treeview patients_waiting">
    <a href="/hospital/doctor">
        <i class="fa fa-plus" aria-hidden="true"></i><span>Patients Waiting <span class="countwaitings"></span></span>
    </a>
</li> 

<li class="treeview myappointment">
    <a href="/hospital/doctor/myappointment">
        <i class="fa fa-search" aria-hidden="true"></i><span>My Appointments <span class="countmyappointments"></span></span>
    </a>
</li> 

<li class="treeview prescriptions">
    <a href="/hospital/doctor/myprescription">
        <i class="ti-write"></i><span>Print Orders</span>
    </a>
</li>
<li class="treeview mypatients">
    <a href="/hospital/doctor/mypatients">
        <i class="ti-server"></i><span>My Patients</span>
    </a>
</li>
<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $user_name = $loggeduserdata['username'];
    if($user_name=="abush"){
?>
<li class="treeview report">
    <a href="/hospital/report">
        <i class="ti-file"></i><span>Report</span>
    </a>
</li>
<?php 
    }
?>
<img src="/img/rank_bot.jpg" width="100px" title="Scan to Join Rank Dermatology" data-toggle="tooltip" data-html="true" style="margin-left: 45px;border-radius: 15px;">
                                            



<script type="text/javascript">
    $(document).ready(function () {
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }
    });
</script>

<script>
function notifylabresult(){
   
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationlabresult",
            'type': 'GET', 
            'success': function(data) {
                if ((data != "")){
                    var container = $(".resultpopup");
                    
                    $('#popup').fadeIn().css('top',100);
                    container.html(data);
                }
            }
            });
        };
    setInterval("notifylabresult()",10000);
    function notifynurseresult(){
   
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationnurseresult",
            'type': 'GET', 
            'success': function(data) {
                if ((data != "")){
                    var container = $(".resultpopupnurse");
                    
                    $('#popupnurse').fadeIn().css('top',100);
                    container.html(data);
                }
            }
            });
        };
    setInterval("notifynurseresult()",10000);
    function viewlabnotification(id){
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/ajax/updatenotifylabresult",
            'type': 'POST',
            'data': {
                'notification_id': id
            },
            'success': function(data) {
                
               $('#popup').fadeOut();
            }
        });


    }
    function viewnursenotification(id){
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/ajax/updatenotifynurseresult",
            'type': 'POST',
            'data': {
                'notification_id': id
            },
            'success': function(data) {
                
               $('#popupnurse').fadeOut();
            }
        });


    }
    $(document).ready(function () {
        notifymyappointments();
        notifywaitings();
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }
    });
    function notifymyappointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getmynotificationappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".countmyappointments");
                container.html(data);
                //console.log(data);
            }
            });
        };
    setInterval("notifymyappointments()",5000);
    function notifywaitings(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getnotificationwaiting",
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
            <style>
                #popup { position: absolute; left: 200%; z-index: 500; display: none;}
            </style>
            <style>
                #popupnurse { position: absolute; left: 200%; z-index: 500; display: none;}
            </style>
