<?php
    helper('form');
    //BluelimeSystem::show_and_die($doc_id .$medical_certificate[0]['registerd_by']);
?>

    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Nurse</h1>
                <small>Referal Request Form</small>
                
            </div>
            
        </section>
        
                <!-- Main content -->
                <section class="content">
                <?= view('Include/_external_referal_request_print') ?>
        </section>

    </div> <!-- /.content-wrapper -->
    <script>
        function doPrint(el){
            var restorepage  = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
            location.reload();
        }
        function delete_report(rrid){
        if(!confirm('Are you sure you want to delete this Request?'))
            return 0;
        location = '/hospital/doctor/deletereferalrequest?rrid=<?= $_GET['rrid'] ?>&vid=<?= $external_referal_request[0]['visit_id']??$_GET['vid'] ?>';
        }

        
        <?php
            $fail = $_GET['fail'] ?? false;
            if($fail)
                echo 'dental_show_alert("There was an error updating the status.", "OK", "WARNING");';
        ?>
    </script>
