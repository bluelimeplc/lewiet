<?php
    helper('form');
    $companies = $this->data['companies'];
    $company_id = $this->data['company_id'];
    $date_from = $this->data['date_from'];
    $date_to = $this->data['date_to'];
    $paymentRequests = $this->data['paymentRequests'];
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
                    <li class="active"><a href="<?php echo base_url(); ?>inventory/reception/newcreditpaymentrequest">New Payment Request</a></li>
                </ol>
            </div>
        </section>
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <div class="panel-body">
                                <?php
                                    $attributes = array('role'=>'form', 'method'=>'post');
                                    echo form_open_multipart(base_url().'inventory/finance/creditpayment', $attributes);
                                ?>
                                    <div class="form-group col-md-4">
                                        <label for="company" class="control-label">Company</label>
                                        <div class="">
                                            <select id="company" name="company_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                    foreach($companies as $company){
                                                        echo '<option value="' . $company['id'] . '"';
                                                        if($company_id === $company['id']){
                                                            echo " selected ";
                                                            $company_name = $company['company_name'];
                                                        }
                                                        echo '>' . $company['company_name'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="date_from" class="control-label">Date From</label>
                                        <div class="">
                                            <input id="date_from" type="date" name="date_from" class="form-control" value="<?= $date_from ?>"> 
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="date_to" class="control-label">To</label>
                                        <div class="">
                                            <input id="date_to" type="date" name="date_to" class="form-control" value="<?= $date_to ?>"> 
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="control-label">&nbsp;</label>
                                        <div class="">
                                            <button type="submit" class="btn btn-success">Show</button>
                                        </div>
                                    </div>
                                <?php echo form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th width="100">N<u>o</u> Of Bills</th>
                                        <th>Total Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th width="70">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($paymentRequests as $paymentRequest){
                                            $no_of_bills = count(\BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']));

                                            echo '
                                            <tr>
                                                <td>' . $paymentRequest['company_name'] . '</td>
                                                <td>' . $no_of_bills . '</td>
                                                <td>' . $paymentRequest['total_amount'] . '</td>
                                                <td>' . substr($paymentRequest['registered_date'],0,10) . '</td>
                                                <td>' . $paymentRequest['status'] . ' </td>
                                                <td>
                                                    <a class=" btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                    <a class=" btn btn-xs btn-danger"><i class="fa fa-trash" onclick="deletePaymentRequest(' . $paymentRequest['id'] . ')"></i></a>
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

        function deletePaymentRequest(id){
            if(confirm("Are you sure you want to delete this payment request?")){
                document.location = "<?= base_url() ?>inventory/finance/deletepaymentrequest?id="+id;
            }
        }
    </script>
