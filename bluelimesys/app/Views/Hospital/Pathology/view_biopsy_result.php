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
                <h1>Doctor</h1>
                <small>Pathology Report</small>
                
            </div>
            
        </section>
        <section class="content">
        <div>
                <button class="btn btn-success" onclick="show_confirm_pin()">Approve</button>
                <button class="btn btn-danger" onclick="delete_report()">Delete</button>
                <button class="btn btn-primary" onclick="back()">Back</button>
                
            </div>
            <?= view('Include/_pathology_result_print') ?>
        </section>
        

    </div> <!-- /.content-wrapper -->

    <form onsubmit="event.preventDefault(); checkpin();">
        <div class="modal fade" id="confirmPin" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_entered" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggeduserdata['digital_signature'] ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-success" value="Confirm" type="button" onclick="checkpin()">
                    </div>
                </div>
            </div>
        </div>
        </form>
    <script>
        function doPrint(el){
            var restorepage  = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
            location.reload();
        }

        function delete_report(){
        if(!confirm('Are you sure you want to delete this Report?'))
            return 0;
        location = '/hospital/pathology/deletebiopsyresult?brid='+<?=$_GET['brid']??0?>+'&oid='+<?=$_GET['order_id']?>;
        }
        function approveresult(){
            location = '/hospital/pathology/approveresult?brid='+<?=$_GET['brid']??0?>+'&order_id='+<?=$_GET['order_id']?>;
        }
        function back(){
            location = '/hospital/pathology/order_detail?order_main_id='+<?=$_GET['order_id']?>;
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


       
function show_confirm_pin(){
        
        $('#confirmPin').modal('show');
        $('#confirmPin').on('shown.bs.modal', function () {
        $('#pin_entered').focus();
})
        $('#pin_entered').focus();
    }

    function checkpin(){
        var pin = $("#pin_entered").val();
        var user = $("#user_id").val();
        $.ajax({
            method: 'post',
            url: "/hospital/ajax/checkpin",
            data: {"pin_code":pin,'id':user},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    approveresult();
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
    }

</script>
