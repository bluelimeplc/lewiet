<?php
    helper('form');
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
                <h1>Finance</h1>
                <small>Credit Payment Receipt List</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="<?php echo base_url(); ?>inventory/finance/newcreditpaymentrequest">New Receipt List</a></li>
                </ol>
            </div>
        </section>
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                
                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-bd">
                        <div class="panel-heading">
                            <h4 class="panel-title">Payment Request List</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-striped table-hover" id="paymentRequestTable">
                                <thead>
                                    <tr>
                                        <th width="10"> </th>
                                        <th>Company Name</th>
                                        <th>Department</th>
                                        <th width="10">N<u>o</u> Of Receipts</th>
                                        <th>Total Amount</th>
                                        <!--th>Month</th-->
                                        <th>Date</th>
                                        <th>Invoice Number</th>
                                        <th width="70">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sn = 1;
                                        foreach($paymentRequests as $paymentRequest){
                                            $change_status_button = '';
                                            if(array_search(BluelimeConfiguration::USER_ROLE_EXTRA::FINANCE_SUPER,($loggeduserdata['extra']['extra_role'] ?? [])) !== false)
                                                $change_status_button = '<a onclick="change_status(this, \''.$paymentRequest['id'].'\')" class=" btn btn-xs btn-warning" title="Change status"><i class="fa fa-refresh"></i></a>';

                                            $no_of_bills = count(\BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']));
                                            //\BluelimeSystem::show_and_die($paymentRequest['bill_id_list']);
                                            $deleteButton = '<a class=" btn btn-xs btn-danger" onclick="deletePaymentRequest(' . $paymentRequest['id'] . ')"><i class="fa fa-trash"></i></a>';
                                            
                                            //If a status of payment request is not null, then it is not possible to delete the request.
                                            if(!empty($paymentRequest['status']))
                                                $deleteButton = '<a class=" btn btn-xs bg-gray" title="Pyment request with status \''.$paymentRequest['status'].'\' cannot be deleted."><i class="fa fa-trash"></i></a>';

                                            if(trim($paymentRequest['company_title']) == '')
                                                $paymentRequest['company_title'] = $paymentRequest['company_name'];

                                            echo '
                                            <tr>
                                                <td>'. $sn++ . '</td>
                                                <td>' . $paymentRequest['company_title']. (trim($paymentRequest['company_title'])!=trim($paymentRequest['company_name'])?' ('.$paymentRequest['company_name'].')':'') . '</td>
                                                <td>'. $paymentRequest['department'] . '</td>
                                                <td>' . $no_of_bills . '</td>
                                                <td align="right">' . number_format($paymentRequest['total_amount'],2) . '</td>
                                                <!--td>' . $paymentRequest['month_name'] . '</td-->
                                                <td title="Created on '.$paymentRequest['created_at'].'">' . $paymentRequest['from_date'].' - ' . ($paymentRequest['to_date']!=''?'<u>'.$paymentRequest['to_date'].'</u>':substr($paymentRequest['created_at'],0,10)) . '</td>
                                                <td>' . $change_status_button .' <span>'.$paymentRequest['status'].'</span></td>
                                                <td>
                                                    <a href="' . base_url() . 'inventory/finance/viewcreditpaymentrequest?id=' . $paymentRequest['id'] . '" class=" btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                    ' . $deleteButton . '
                                                </td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>            
        </section>

    </div> <!-- /.content-wrapper -->
    <script>
        var x =0;
        function change_status(button, payment_requet_id){
            //alert(payment_requet_id);
            let change_status_button = '<a onclick="change_status(this, \''+payment_requet_id+'\')" class=" btn btn-xs btn-warning" title="Change status"><i class="fa fa-refresh"></i></a>';
            $.ajax({
                url: '/inventory/finance/change_credit_receipt_group_status',
                method: 'POST',
                data: {payment_requet_id: payment_requet_id},
                dataType: 'json',
                beforeSend: function() {
                    // Optionally show a loading spinner or message
                    $(button).html('<i class="fa fa-spinner fa-spin"></i>');
                    $(button).attr('disabled',true);
                },
                success: function(response) {
                    if(response.status === 'success'){
                        $(button).closest('td').find('span').html(response.value);
                    } else {
                        toastr['error']('Error updating: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while saving API data.');
                },
                complete: function() {
                    // Optionally hide the loading spinner or message
                    $(button).html('<i class="fa fa-refresh"></i>');
                    $(button).attr('disabled',false);
                }
            });
        }

        function deletePaymentRequest(id){
            if(confirm("Are you sure you want to delete this payment request?")){
                document.location = "<?= base_url() ?>inventory/finance/deletepaymentrequest?id="+id;
            }
        }

        $(document).ready(function() {
        var t = $('#paymentRequestTable').DataTable( {
            //info: false,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ],
            // order: [[3, 'desc']]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
    </script>
