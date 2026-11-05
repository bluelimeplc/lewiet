<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    helper('form');
    $external_lab_request = $this->data['external_lab_request'];
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
                <h1>Doctor</h1>
                <small>Laboratory Request Form</small>
                
            </div>
            
        </section>
        
                <!-- Main content -->
                <section class="content">
                <div>
                <button class="btn btn-danger <?= date('Y-m-d',strtotime($external_lab_request[0]['created_at'])) != date('Y-m-d') ? ' hide ' : '' ?>" onclick="delete_report(<?= $_GET['elid'] ?>)">Delete</button>
            </div>
            <?= view('Include/_external_lab_request_print') ?>
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
        
        function delete_report(elid){
        if(!confirm('Are you sure you want to delete this Request?'))
            return 0;
        location = '/hospital/doctor/deletelaboratoryrequest?elid=<?= $_GET['elid'] ?>&vid=<?= $external_lab_request[0]['visit_id'] ?>';
        }

        
        <?php
            $fail = $_GET['fail'] ?? false;
            if($fail)
                echo 'dental_show_alert("There was an error updating the status.", "OK", "WARNING");';
        ?>
    </script>
