           
<script type="text/javascript">
    $(document).ready(function () {
        var segment_3 = 'Appointment_controller';

        if (segment_3 === 'Prescription_controller') {
            $('.pres').addClass('active');
        }
        else if (segment_3 === 'Appointment_controller') {
            $('.appointment').addClass('active');
        }
        else if (segment_3 === 'Patient_controller') {
            $('.patient').addClass('active');
        }
        else if (segment_3 === 'Schedule_controller') {
            $('.schedule').addClass('active');
        }
        else if (segment_3 === 'Emergency_stop_controller') {
            $('.emergency_stop').addClass('active');
        }
        else if (segment_3 === 'Invoice') {
            $('.Invoice').addClass('active');
        }
    });
</script>

            <?php
                $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
                $logged_user = $loggeduserdata['name'];
            ?>
            <!-- =============================================== -->
            <!-- Left side column. contains the sidebar -->

            <aside class="main-sidebar">
                <!-- sidebar -->
                <div class="sidebar">
                <!-- Sidebar user panel -->

                    <div class="user-panel text-center">
                        
                        <div class="info">
                        <p><?php echo $logged_user ?></p>
                            <a href="#"><i class="fa fa-circle text-success"> &nbsp; Online</i> </a>
                        </div>
                    </div>



                </div> <!-- /.sidebar -->
            </aside>