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
                <small>Credit Payment Invoice</small>
                
            </div>
        </section>
                <!-- Main content -->
        <section class="content" style="width:70%;">
            <div class="row">
                 <!--  Search area -->           
                
                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-bd">
                        <div class="panel-body">
                            <div style="width: 250px; display: inline-block;">
                                <select class="form-control" id="peachtree_customer_id" >
                                    <option value="">Select Peachtree Customer ID</option>
                                    <?php
                                        foreach($peachtree_customers as $customer){
                                            echo '<option value="'.$customer['customer_id'].'">'.$customer['customer_id'].'</option>';
                                        }
                                    ?>
                                </select>
                                <a role="btn" title="Edit Peachtree Customer ID" onclick="view_edit_customer_id()"><i class="fa fa-gear"></i></a>
                            </div>

                            <a href="" target="_blank"  style="vertical-align: top;" class="btn btn-warning" onclick="return export_csv(this);">
                                <i class="fa fa-reply"></i> Export to Peachtree (CSV)
                            </a>
                            

                            <button class="btn btn-danger pull-right" style="margin-left: 10px;" onclick="delete_invoice();">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                             <button class="btn btn-primary pull-right" onclick="print_printable('printable_invoice_detail');">
                                <i class="fa fa-print"></i> Print Invoice
                            </button>
                        </div>
                    </div>
                    <div  class="panel panel-bd">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <th>Company Title</th><th>For the month of</th><th>Letter N<u>o</u></th><th>Payment N<u>o</u></th><th></th>
                                </tr>
                                <tr>
                                    <td><?= $invoice['company_title'] ?></td>
                                    <td><?= $invoice['month'] ?></td>
                                    <td><?= $invoice['letter_number'] ?></td>
                                    <td><?= $invoice['payment_number'] ?></td>
                                    <td><button class="btn btn-primary" onclick="view_edit_invoice();" title="Edit"><i class="fa fa-edit"></i></button></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div  class="panel panel-bd">
                        <div class="panel-body" id="printable_invoice_detail">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <div style="float: right; position: absolute; right: 40px;">Date: <b><?= $invoice['date'] ?></b></div>    
                                        <h4><u>Credit Invoice Detail</u></h4>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    Company Name: <b><?= $invoice['company_title'] ?></b>
                                    <span style="width: 100px; display: inline-block;">&nbsp;</span>For the month of: <b><?= $invoice['month'] ?></b>
                                </div>
                                
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Patient Name</th>
                                                <th>FS N<u>o</u></th>
                                                <th><i class="fa fa-code-branch"></i></th>
                                                <th width="100">Date</th>
                                                <th style="text-align: center;">Services</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Example data, replace with actual data from your model
                                            // $receipts = [
                                            //     ['patient_name' => 'Mihret Setu Aynalem', 'fs_no' => '00086847', 'date' => '2025-06-11', 'total' => 2000],
                                            //     ['patient_name' => 'Mihret Setu Aynalem', 'fs_no' => '00086847', 'date' => '2025-06-11', 'total' => 2000],
                                            // ];
                                            $sn = 1;
                                            foreach ($receipts as $receipt) {
                                                echo '<tr>';
                                                echo '<td>' . $sn++ . '</td>';
                                                echo '<td>' . $receipt['patient_name'] . '</td>';
                                                echo '<td>' . $receipt['fs_no'] . '</td>';
                                                echo '<td>' . $receipt['branch'] . '</td>';
                                                echo '<td>' . $receipt['date'] . '</td>';
                                                echo '<td>';
                                                echo '<table class="table table-condensed table-borderless" style="margin: 0px; padding: 0px;">';
                                                foreach($receipt['sales_detail'] as $item => $amount){
                                                    echo '<tr>';
                                                    echo '<td style="padding: 2px; margin: 0px; border:solid 1px #fff !important;">' . $item . '</td>';
                                                    echo '<td style="padding: 2px; margin: 0px; border:solid 1px #fff !important;" width="50" align="right">' . number_format($amount) . '</td>';
                                                    echo '</tr>';
                                                }
                                                echo '</table>';
                                                echo '</td>';
                                                echo '<td align="right">' . number_format($receipt['total'],2) . '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="6" align="right"><b>Total</b></td>
                                                <td align="right"><b><?= number_format(array_sum(array_column($receipts, 'total')), 2) ?></b></td>
                                            </tr>
                                         </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </section>
    </div> <!-- /.content-wrapper -->
    <div>
        <div class="modal fade" id="edit_report_info_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Report Info</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <tr>
                                <td>Company Title</td>
                                <td><input type="text" maxlength="100" class="form-control" id="company_title_edit" value="<?= $invoice['company_title'] ?>"></td>
                            </tr>
                            <tr>
                                <td>For the month of</td>
                                <td><input type="text" maxlength="30" class="form-control" id="month_name_edit" value="<?= $invoice['month'] ?>"></td>
                            </tr>
                            <tr>
                                <td>Letter Number</td>
                                <td><input type="text" maxlength="13" class="form-control" id="letter_number_edit" value="<?= $invoice['letter_number'] ?>" title="e.g. RANK/2025/192">e.g. RANK/2025/192</td>
                            </tr>
                            <tr>
                                <td>Payment Number</td>
                                <td><input type="text" maxlength="20" class="form-control" id="payment_number_edit" value="<?= $invoice['payment_number'] ?>"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="update_report_info()" id="btn_update_report_info">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit_customer_id_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Peachtree Customer ID</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td></td>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>#</td>
                                    <td><input type="text" maxlength="20" class="form-control" id="peachtree_customer_id_new"></td>
                                    <td><input type="text" maxlength="39" class="form-control" id="peachtree_customer_name_new" ></td>
                                    <td><button class="btn btn-primary" onclick="add_customer_id_row()">+</button></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 1;
                                    foreach($peachtree_customers as $customer){
                                        echo '<tr id="peachtree_customer_row_'.$customer['id'].'">';
                                        echo '<td>'.$sn++.'</td>';
                                        echo '<td>'.$customer['customer_id'].'</td>';
                                        echo '<td>'.$customer['name'].'</td>';
                                        echo '<td><button class="btn btn-danger" onclick="delete_customer_id_row(\''.$customer['id'].'\')">X</button></td>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        var company_title = '<?= $invoice['company_title'] ?>';
        var month = '<?= $invoice['month'] ?>';
        var letter_number = '<?= $invoice['letter_number'] ?>';
        var payment_number = '<?= $invoice['payment_number'] ?>';

        function export_csv(link){
            if(letter_number.trim() == ''){
                $('#edit_report_info_modal').modal('show');
                setTimeout(function() {
                    flash_red_inputbox('letter_number_edit');
                }, 500);
                return false;
            }
            let customer_id = $('#peachtree_customer_id').val();
            if(customer_id.trim() == ''){
                toastr['error']('Please select Peachtree Customer ID', 'Error');
                flash_red_inputbox('peachtree_customer_id');
                return false;
            }
            link.href = "/inventory/finance/export_credit_payment_invoice_csv?invoice_id=<?=$invoice['id']?>&peachtree_id=" + encodeURIComponent(customer_id);
            return true;
            //let url = '/inventory/finance/export_credit_payment_invoice_csv?company_title=' + encodeURIComponent(company_title) + '&month=' + encodeURIComponent(month) + '&letter_number=' + encodeURIComponent(letter_number) + '&payment_number=' + encodeURIComponent(payment_number);
        }

        function view_edit_invoice(){
            $('#edit_report_info_modal').modal('show');
        }

        function view_edit_customer_id(){
            $('#edit_customer_id_modal').modal('show');
        }

        function add_customer_id_row(){
            let customer_id = $('#peachtree_customer_id_new').val();
            let customer_name = $('#peachtree_customer_name_new').val();
            if(customer_id.trim() == ''){
                flash_red_inputbox('peachtree_customer_id_new');
                return false;
            }
            if(customer_name.trim() == ''){
                flash_red_inputbox('peachtree_customer_name_new');
                return false;
            }
            $.ajax({
                method: 'post',
                url: "/inventory/finance/add_peachtree_customer_id",
                data: {customer_id: customer_id, name: customer_name},
                dataType: 'json',
                async: false,
                success: function(result){
                        if(result.status == 'success'){
                            $id = result.value;
                            $('#edit_customer_id_modal tbody').prepend('<tr id="peachtree_customer_row_'+$id+'"><td></td><td>'+customer_id+'</td><td>'+customer_name+'</td><td><button class="btn btn-danger" onclick="delete_customer_id_row(\''+$id+'\')">X</button></td></tr>');
                            $('#peachtree_customer_id_new').val('');
                            $('#peachtree_customer_name_new').val('');
                        }
                        else
                            toastr['error'](result.message, 'Error');
                },
                error:function(jqXHR, textStatus, errorThrown){
                    toastr['error'](errorThrown, 'Error');
                }
            });
        }

        function delete_customer_id_row(id){
            if(confirm("Are you sure you want to delete this Peachtree Customer ID?")){
                $.ajax({
                    method: 'post',
                    url: "/inventory/finance/delete_peachtree_customer_id",
                    data: {id: id},
                    dataType: 'json',
                    async: false,
                    success: function(result){
                            if(result.status == 'success'){
                                $('#peachtree_customer_row_' + id).remove();
                            }
                            else
                                toastr['error'](result.message, 'Error');
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        toastr['error'](errorThrown, 'Error');
                    }
                });
            }
        }

        function update_report_info(){
            let month_name_edit = $('#month_name_edit').val();
            let company_title_edit = $('#company_title_edit').val();
            let letter_number_edit = $('#letter_number_edit').val();
            let payment_number_edit = $('#payment_number_edit').val();

            if(company_title_edit.trim() == ''){
                flash_red_inputbox('company_title_edit');
                return false;
            }

            if(month_name_edit.trim() == ''){
                flash_red_inputbox('month_name_edit');
                return false;
            }
            
            $.ajax({
                method: 'post',
                url: "/inventory/finance/update_credit_invoice_info",
                data: {month: month_name_edit, company_title: company_title_edit, letter_number:letter_number_edit,payment_number:payment_number_edit, id: <?= $invoice['id'] ?>},
                dataType: 'json',
                async: false,
                success: function(result){
                        if(result.status == 'success'){
                            location.reload();
                        }
                        else
                            toastr['error'](result.message, 'Error');
                },
                error:function(jqXHR, textStatus, errorThrown){
                    toastr['error'](failMessage + errorThrown, 'Error');
                }
            });
            $('#update_report_info').prop('disabled', true);
        }

        function delete_invoice(){
            if(confirm("Are you sure you want to delete this invoice?")){
                do_delete_invoice(<?= $invoice['id'] ?>);
            }
        }

        function do_delete_invoice(invoice_id){
            $.ajax({
                method: 'post',
                url: "/inventory/finance/delete_credit_invoice",
                data: {invoice_id: invoice_id},
                dataType: 'json',
                async: false,
                success: function(result){
                        if(result.status == 'success'){
                            location.href = '/inventory/finance/credit_payment_invoice_list';
                        }
                        else
                            toastr['error'](result.message, 'Error');
                },
                error:function(jqXHR, textStatus, errorThrown){
                    toastr['error'](failMessage + errorThrown, 'Error');
                }
            });
        }
        
    </script>
