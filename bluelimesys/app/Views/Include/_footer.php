<?php
    $alert = $this->data['alert'] ?? [];
    if(!empty($alert)){
        echo '
        <script>

            let body_html = "' . $alert['body_html'] . '";
            let command = "' . $alert['command'] . '";
            let alert_type = "' . $alert['alert_type'] . '";
            let title = "' . $alert['title'] . '";
            ';

            echo ($alert['autoHide']) ? 'let autoHide = true' : 'let autoHide = false;';

            echo '
            dental_show_alert(body_html,command,alert_type, title, autoHide);

        </script>
        ';
    }
    
?>
<script>
    $(document).ready(function () {
        get_ready_toastr();
        
        <?php 
            $active_menu = $active_menu ?? ''; 
        ?>
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }
    });

    function get_ready_toastr(){
        toastr['options'] = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
            };
    }

    function print_printable(id){
        // var printContents = document.getElementById(id).innerHTML;
        // var originalContents = document.body.innerHTML;
        // document.body.innerHTML = printContents;
        // window.print();
        // document.body.innerHTML = originalContents;
        print_printable_new(id);
    }

    function print_printable_new(id){
        let printContents = document.getElementById(id).cloneNode(true);
        let originalContents = document.body.cloneNode(true);
        document.body.replaceChildren( printContents);
        window.print();
        document.body.replaceChildren(originalContents);
    }

    function show_error_message(){
        let error_message = '';
        let error_code = '';
        <?php
            $error_code = $_GET['error_code'] ?? '';
            echo "error_code = '$error_code'; ";
            if($error_code != '')
                echo 'error_message = "'.BluelimeError::get_message($error_code).'"';
        ?>
        
        if(error_message != '')
            toastr['error'](error_message + "<br>Error code: <b>" + error_code + "</b>");
    }

    $( document ).ready(function() {
        show_error_message();
    });

    function flash_red_inputbox(objID, focus=true){
        let current_bg = $('#'+objID).css('background-color');
        $('#'+objID).css('background-color', '#f00');
        setTimeout("$('#"+objID+"').css('background-color', '"+current_bg+"')", 300);
        if(focus)
            $('#'+objID).focus();
    }

    function refocus_input_box(id){
        let refocusable_input_box = document.getElementById(id);
        refocusable_input_box.focus();
        let length = refocusable_input_box.value.length;
        refocusable_input_box.setSelectionRange(length,length);
    }

    $( document ).ready(function() {
        let refocusable = $('.refocusable');
        for(i=0; i< refocusable.length; i++){
            refocus_input_box(refocusable[i].id);
        }
    });

</script>
            <footer class="main-footer">
               <p class="text-center color-gray"><small>Developed by Bluelime Technologies<br>www.bluelimeplc.com</small></p>
            </footer>
        </div>
        <!-- ./wrapper -->
        <!-- Start Core Plugins
        =====================================================================-->

        <!-- jquery-ui --> 
        <!--script src="<?php echo base_url()?>assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script-->
        <!-- lobipanel -->
        <script src="<?php echo base_url()?>assets/plugins/lobipanel/lobipanel.min.js" type="text/javascript"></script>
        <!-- Pace js -->
        <!-- <script src="<?php echo base_url()?>assets/plugins/pace/pace.min.js" type="text/javascript"></script> -->
        <!-- SlimScroll -->
        <script src="<?php echo base_url()?>assets/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <!-- FastClick -->
        <script src="<?php echo base_url()?>assets/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
        <!-- AdminBD frame -->
        <script src="<?php echo base_url()?>assets/dist/js/frame.min.js" type="text/javascript"></script>
        <!-- End Core Plugins
        =====================================================================-->
        <!-- Start Page Lavel Plugins
        =====================================================================-->
        <!-- Toastr js -->
        <script src="<?php echo base_url()?>assets/plugins/toastr/toastr.min.js" type="text/javascript"></script>
        <!-- Sparkline js -->
        <script src="<?php echo base_url()?>assets/plugins/sparkline/sparkline.min.js" type="text/javascript"></script>
        <!-- Data maps js -->
        <script src="<?php echo base_url()?>assets/plugins/datamaps/d3.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>assets/plugins/datamaps/topojson.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>assets/plugins/datamaps/datamaps.all.min.js" type="text/javascript"></script>
        <!-- Counter js -->
        <!--script src="<?php echo base_url()?>assets/plugins/counterup/waypoints.js" type="text/javascript"></script-->
        <!--script src="<?php echo base_url()?>assets/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script-->
        <!-- Emojionearea -->
        <script src="<?php echo base_url()?>assets/plugins/emojionearea/emojionearea.min.js" type="text/javascript"></script>
        <!-- Monthly js -->
        <script src="<?php echo base_url()?>assets/plugins/monthly/monthly.js" type="text/javascript"></script>
        <!-- ChartJs JavaScript -->
        
        <!--script src="<?php echo base_url()?>assets/plugins/chartJs/Chart.min.js" type="text/javascript"></script-->
        <script src="<?php echo base_url()?>assets/plugins/chartJs/chart.umd.min.js" type="text/javascript"></script>
        <!-- End Page Lavel Plugins-->

        <!-- End Page Lavel Plugins
        =====================================================================-->
        <!-- Start Theme label Script
        =====================================================================-->

         <!-- summernote js -->
        <script src="<?php echo base_url()?>assets/plugins/summernote/summernote.min.js" type="text/javascript"></script>
        <!-- End Page Lavel Plugins-->

        <!-- Dashboard js -->
        <script src="<?php echo base_url()?>assets/dist/js/dashboard.min.js" type="text/javascript"></script>
         <!-- datatable -->
        <script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.min.js" type="text/javascript"></script>        
        <!--script src="<?php echo base_url();?>assets/plugins/datatables/dataTables_custom.min.js" type="text/javascript"></script-->
        <!-- Custom Theme JavaScript -->
        <script src="<?php echo base_url()?>assets/custom.js" type="text/javascript"></script>
    <!-- date time picker >
        <script src="<?php echo base_url()?>assets/plugins/ui-datetimepicker/jquery-ui-timepicker-addon.min.js"></script>
        <script src="<?php echo base_url()?>assets/plugins/ui-datetimepicker/jquery-ui-sliderAccess.js"></script>
    -->
<!-- End Core Plugins
        =====================================================================-->
        <!-- Start Theme label Script
        =====================================================================-->
        <script src="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/dist/bootstrap-tokenfield.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/docs-assets/js/scrollspy.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/docs-assets/js/affix.js" type="text/javascript"></script>
        <!-- End Theme label Script
        =====================================================================-->
    </body>
</html>
<script>
    setTimeout(function() {
        $('input').attr('autocomplete', 'off');
    }, 1000);
</script>