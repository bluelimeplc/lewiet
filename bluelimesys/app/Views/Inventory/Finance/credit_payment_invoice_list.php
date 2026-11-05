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
                <small>Credit Payment Invoice List</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/inventory/finance/newcreditpaymentinvoice">New Payment Invoice</a></li>
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
                            <h4 class="panel-title">Credit Payment Invoice List</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-striped table-hover" id="paymentRequestTable">
                                <thead>
                                    <tr>
                                        <th>Letter N<u>o</u></th>
                                        <th>Company Title</th>
                                        <th>For The Month Of</th>
                                        <th>Payment N<u>o</u></th>
                                        <th>From - To</th>
                                        <th>Total Amount</th>
                                        <th>Created On</th>
                                        <th width="70">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sn = 1;
                                        foreach($creditPaymentInvoices as $creditPaymentInvoice){
                                            //$no_of_bills = count(\BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']));
                                            $viewButton = '<a class=" btn btn-xs btn-primary" href="/inventory/finance/credit_payment_invoice_view?invoice_id='.$creditPaymentInvoice['id'].'"><i class="fa fa-eye"></i></a>';
                                            echo '<tr '.($creditPaymentInvoice['status']==0?'class="bg-red"':'').'>';
                                            echo '<td>' . $creditPaymentInvoice['letter_number'] . '</td>';
                                            echo '<td>' . $creditPaymentInvoice['company_title'] . '</td>';
                                            echo '<td>' . $creditPaymentInvoice['month'] . '</td>';
                                            echo '<td>' . $creditPaymentInvoice['payment_number'] . '</td>';
                                            echo '<td>' . $creditPaymentInvoice['from_date'] . '<br>' . $creditPaymentInvoice['to_date'] . '</td>';
                                            echo '<td>' . number_format($creditPaymentInvoice['total_amount']) . '</td>';
                                            echo '<td>' . substr($creditPaymentInvoice['created_at'],0,10) . '</td>';
                                            echo '<td>' . $viewButton . '</td>';
                                            echo '</tr>';
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

        function deletePaymentRequest(id){
            if(confirm("Are you sure you want to delete this payment request?")){
                document.location = "<?= base_url() ?>inventory/finance/deletepaymentrequest?id="+id;
            }
        }

        $(document).ready(function() {
        var t = $('#paymentRequestTable').DataTable( {
            //info: false,
            lengthMenu: [
                [25, 50, -1],
                [25, 50, 'All']
            ],
            // order: [[3, 'desc']]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
    </script>
