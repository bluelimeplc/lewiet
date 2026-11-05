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
                <small>New Credit Payment Invoice</small>
                
            </div>
        </section>
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                
                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-bd">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="col-md-4">
                                        <label for="company_title">Company Title</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="company_title" name="company_title">
                                    </div>
                                    <br><br>
                                    <div class="col-md-4">
                                        <label for="month">For The Month of</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="month" name="company_title">
                                    </div>
                                    <br><br>
                                    <div class="col-md-4">
                                        <label for="month">Number Of Receipts</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="number_of_receipts" readonly value="0">
                                    </div>
                                    <br><br>
                                    <div class="col-md-4">
                                        <label for="month">Total Amount</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="total_amount" readonly value="0">
                                    </div>
                                    <br><br>
                                    <div class="col-md-4">
                                        <label for="month"> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <button type="button" class="btn btn-primary" onclick="generate_invoice()" id="btn_generate_invoice">Generate Invoice</button>
                                        <br><br>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Branch</th><th>Status</th><th>Message</th>
                                        </tr>
                                        <?php
                                            foreach($api_success as $row){
                                                echo '
                                        <tr>
                                            <td>#'.$row['branch_number'].' '.$row['branch_name'].'</td>
                                            <td class="'.($row['status']=='success'?'bg-green':'bg-red').'">'.($row['status']=='success'?'<i class="fa fa-check"></i>':'<i class="fa fa-warning"></i>').'</td>
                                            <td>'.$row['error_message'].'</td>
                                        </tr>
                                                ';
                                            }
                                        ?>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-bordered table-striped table-hover" id="paymentRequestTable">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Department</th>
                                        <th>Branch</th>
                                        <th>N<u>o</u> Of Receipts</th>
                                        <th>Total Amount</th>
                                        <th>Date</th>
                                        <th width="50">
                                            Include<br>
                                            <button type="button" class="btn btn-xs btn-default" onclick="checkAll()" title="Check all bills"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn btn-xs btn-default" onclick="uncheckAll()" title="Uncheck all bills"><i class="fa fa-times"></i></button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <style>
                                        .selected_row{
                                            background-color: yellow !important;
                                        }
                                    </style>
                                    <?php
                                        $sn = 1;
                                        //$paymentRequests = [];
                                        foreach($receipt_group_list as $receipt_group){
                                            //$number_of_receipts = $receipt_group['number_of_bills'];
                                            echo '
                                            <tr>
                                                <td>'.$receipt_group['company_name'].'</td>
                                                <td>'.$receipt_group['department'].'</td>
                                                <td>#'.$receipt_group['branch'].' '.$receipt_group['branch_name'].'</td>
                                                <td>'.$receipt_group['number_of_receipts'].'</td>
                                                <td align="right">'.number_format($receipt_group['total_amount'],2).'</td>
                                                <td>'.$receipt_group['from_date'].' - '.(empty($receipt_group['to_date'])?'<u>'.substr($receipt_group['created_at'],0,10).'</u>':$receipt_group['to_date']).'</td>
                                                <td>
                                                    <input type="hidden" class="receipt_group_id" value="'.$receipt_group['id'].'">
                                                    <input type="hidden" class="receipt_group_branch" value="'.$receipt_group['branch'].'">
                                                    <input type="hidden" class="receipt_group_number_of_receipts" value="'.$receipt_group['number_of_receipts'].'">
                                                    <input type="hidden" class="receipt_group_total_amount" value="'.$receipt_group['total_amount'].'">
                                                    <input type="checkbox" class="include_receipt form-control" onclick="include_remove_receipt(this)">
                                                </td>
                                            </tr>
                                            ';
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

        var invoice_data = {"company_title": "", "month": "", "receipt_group_list": {}, "total_amount": 0, "number_of_receipts": 0};

        function generate_invoice(){
            
            if(!validate_invoice())
                return;
            
            invoice_data['company_title'] = $('#company_title').val().trim();
            invoice_data['month'] = $('#month').val().trim();

            $('#btn_generate_invoice').html('<i class="fa fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
            $.ajax({
                url: '/inventory/finance/generate_credit_invoice',
                method: 'POST',
                data: {
                    'invoice_data': JSON.stringify(invoice_data)
                },
                dataType: 'json',
                success:function(result){
                    //console.log(result.value.invoice_id);
                    location = '/inventory/finance/credit_payment_invoice_view?invoice_id=' + result.value.invoice_id;
                }
                ,error:function(xhr, status, error){
                    console.error('Error generating invoice:', error);
                    toastr['error']('Failed to generate invoice. '+error);
                    $('#btn_generate_invoice').html('Generate Invoice').prop('disabled', false);
                }
            });
        }
        
        function validate_invoice(){
            if(invoice_data['number_of_receipts'] <= 0){
                toastr['error']('Please select at least one receipt to include in the invoice');
                return false;
            }
            
            if($('#company_title').val().trim() == ''){
                flash_red_inputbox('company_title');
                toastr['error']('Please enter the company title');
                return false;
            }

            if($('#month').val().trim() == ''){
                flash_red_inputbox('month');
                toastr['error']('Please enter the month for the invoice');
                return false;
            }

            return true;
        }

        function include_remove_receipt(checkbox){
            let row_id = $(checkbox).closest('tr').find('.receipt_group_id').val()+'_'+$(checkbox).closest('tr').find('.receipt_group_branch').val();
            let total_amount = $(checkbox).closest('tr').find('.receipt_group_total_amount').val();
            let number_of_receipts = $(checkbox).closest('tr').find('.receipt_group_number_of_receipts').val();
 
            if($(checkbox).is(':checked')){
                invoice_data['receipt_group_list'][row_id] ={
                    'receipt_group_id': $(checkbox).closest('tr').find('.receipt_group_id').val(),
                    'branch': $(checkbox).closest('tr').find('.receipt_group_branch').val(),
                };
                invoice_data['total_amount'] += parseFloat(total_amount);
                invoice_data['number_of_receipts'] += parseFloat(number_of_receipts);

                if($('#company_title').val().trim() == ''){
                    $('#company_title').val($(checkbox).closest('tr').find('td:nth-child(1)').text());
                }
                $(checkbox).closest('tr').addClass('selected_row');

            }else{
                invoice_data['total_amount'] -= parseFloat(total_amount);
                invoice_data['number_of_receipts'] -= parseFloat(number_of_receipts);

                delete invoice_data['receipt_group_list'][row_id];
                $(checkbox).closest('tr').removeClass('selected_row');
            }
            display_invoice_detail();
        }

        function display_invoice_detail(){
                       
            $('#number_of_receipts').val(invoice_data['number_of_receipts']);
            $('#total_amount').val(parseFloat(invoice_data['total_amount'].toFixed(2)).toLocaleString("en-US"));

        }

        function checkAll(){
            $('.include_receipt').each(function(){
                if(!$(this).is(':checked')){
                    $(this).prop('checked', true);
                    include_remove_receipt(this);
                }
            });
        }
        function uncheckAll(){
            $('.include_receipt').each(function(){
                if($(this).is(':checked')){
                    $(this).prop('checked', false);
                    include_remove_receipt(this);
                }
            });
        }

        function deletePaymentRequest(id){
            if(confirm("Are you sure you want to delete this payment request?")){
                document.location = "<?= base_url() ?>inventory/finance/deletepaymentrequest?id="+id;
            }
        }

        $(document).ready(function() {
        var t = $('#paymentRequestTable2').DataTable( {
            //info: false,
            // lengthMenu: [
            //     [25, 50, -1],
            //     [25, 50, 'All']
            // ],
            // order: [[3, 'desc']]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
    </script>
