<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    helper('form');
    $lab_request = $this->data['lab_request'];
    $doc_sign = $this->data['doc_sign'];
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
                <small>Lab Request</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/nurse/">Back</a></li>
                </ol>
            </div>
            
        </section>
        
                <!-- Main content -->
                <?= view('Dental/Shared/_lab_request'); ?> 
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

        
        <?php
            $fail = $_GET['fail'] ?? false;
            if($fail)
                echo 'dental_show_alert("There was an error updating the status.", "OK", "WARNING");';
        ?>
    </script>
