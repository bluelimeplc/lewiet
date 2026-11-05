<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    helper('form');
    $biopsy_result = $this->data['biopsy_result'];
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
                <h1>Nurse</h1>
                <small>Biopsy Report</small>
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>
            </span>
            </div>
            
        </section>
        <section class="content">
        <div <?=$_GET['status']==6?"":"hidden"?>>
                <a href="/hospital/nurse/change_sample_status?order_id=<?=$_GET['order_id']?>" class="btn btn-success">Deliver</a>
            </div>
            <?= view('Include/_pathology_result_print2') ?>
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

        
        function approveresult(){
           
            location = '/hospital/pathology/approveresult?brid='+<?=$_GET['brid']??0?>+'&order_id='+<?=$_GET['order_id']?>;
        }
        
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
