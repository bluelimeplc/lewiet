<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    helper('form');
    $medical_report = $this->data['medical_report'];
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
                <small>Medical Report</small>
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>
            </span>
            </div>
            
        </section>
        <section class="content">
        <div>
                <button class="btn btn-danger <?= date('Y-m-d',strtotime($medical_report[0]['created_at'])) != date('Y-m-d') ? ' hide ' : '' ?>" onclick="delete_report(<?= $_GET['mid'] ?>)">Delete</button>
            </div>
            <?= view('Include/_medical_report_print') ?>
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

        function delete_report(mid){
        if(!confirm('Are you sure you want to delete this Report?'))
            return 0;
        location = '/hospital/doctor/deletemedicalreport?mid=<?= $_GET['mid'] ?>&vid=<?= $_GET['vid'] ?>';
    }

        
        <?php
            $fail = $_GET['fail'] ?? false;
            if($fail)
                echo 'dental_show_alert("There was an error updating the status.", "OK", "WARNING");';
        ?>
    </script>
    <script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
