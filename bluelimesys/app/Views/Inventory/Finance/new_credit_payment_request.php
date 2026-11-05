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
                <small>New Receipt List</small>
                
            </div>
        </section>
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-heading">
                            <h4 class="panel-title">Create New Receipt List</h4>
                        </div>
                        <div class="panel-body">
                            <?php
                                $attributes = array('role'=>'form', 'method'=>'get');
                                echo form_open_multipart(base_url().'inventory/finance/newcreditpaymentrequest', $attributes);
                            ?>
                                <div class="form-group col-md-4">
                                    <label for="company" class="control-label">Company</label>
                                    <div class="">
                                        <select id="company" name="company_id" class="form-control" required>
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
                                <div class="form-group col-md-2">
                                    <label for="date_from" class="control-label">Date From</label>
                                    <div class="">
                                        <input id="date_from" type="date" name="date_from" class="form-control" value="<?= $date_from ?>"> 
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="date_to" class="control-label">To</label>
                                    <div class="">
                                        <input id="date_to" type="date" name="date_to" class="form-control" value="<?= $date_to ?>"> 
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="date_to" class="control-label">Department</label>
                                    <div class="">
                                        <select name="department" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Clinic" <?= $department=='Clinic'?' selected ':'' ?>>Clinic</option>
                                            <option value="Pharmacy"<?= $department=='Pharmacy'?' selected ':'' ?>>Pharmacy</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <label class="control-label">&nbsp;</label>
                                    <div class="">
                                        <button type="submit" class="btn btn-success">Show</button>
                                    </div>
                                </div>
                            <?php echo form_close() ?>
                        </div>
                    </div>
                </div>
                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h4><b>Filtered data:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Department: <?= $department ?>, Company: <?= $company_name?>, Date from: <?= $date_from ?>, Date to: <?= $date_to ?></h4>
                            <?php
                                if(empty($bills))
                                    echo "There is no data for the current filter.";
                            ?>
                            <div style="<?php if(empty($bills)) echo "display:none";?>">
                                <?php
                                    $attributes = array('role'=>'form', 'method'=>'post');
                                    echo form_open_multipart(base_url().'inventory/finance/createcreditpaymentrequest', $attributes);
                                ?>
                                    <input type="hidden" name="department" value="<?= $department ?>" />
                                    <input type="hidden" name="company_id" value="<?= $company_id ?>" />
                                    <input type="hidden" name="company_name" value="<?= $company_name ?>" />
                                    <input type="hidden" name="total_amount" id="total_amount" value="" />
                                    <input type="hidden" name="date_from" value="<?= $date_from ?>" />
                                    <input type="hidden" name="date_to" value="<?= $date_to ?>" />
                                    
                                    <table width="100%" class="table table-striped table-bordered table-hover dental-credit-payment-bill-list-table">
                                        <thead>
                                            <tr>
                                                <th>MRN No</th>
                                                <th>Full Name</th>
                                                <th>Date</th>
                                                <th>FS N<u>o</u></th>
                                                <th>Amount</th>
                                                <th width="50">
                                                    Include<br>
                                                    <button type="button" class="btn btn-xs btn-default" onclick="checkAll()" title="Check all bills"><i class="fa fa-check"></i></button>
                                                    <button type="button" class="btn btn-xs btn-default" onclick="uncheckAll()" title="Uncheck all bills"><i class="fa fa-times"></i></button>
                                                </th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $totalAmount = 0;
                                                $numberOfBills = count($bills);
                                                foreach($bills as $bill){
                                                    $amount = $bill['total_amount'];
                                                    $totalAmount += $amount;
                                                    $include_check = '<input type="checkbox" id="billListInclude_' . $bill['id'] . '" class="form-control include_credit" name="selected_bills[]" value="' . $bill['id'] . '" onchange="updateTotal(' . $bill['id'] . ', ' . $amount . ')" checked />';
                                                    echo '
                                                    <tr id="billListRow_' . $bill['id'] . '" class="">
                                                        <td>' . $bill['patient_id'] . '</td>
                                                        <td>' . $bill['patient_name'] . '</td>
                                                        <td>' . substr($bill['paid_date'],0,10) . '</td>
                                                        <td><a target="_blank" href="/inventory/finance/receipt?fs_no='.$bill['fs_number'].'&department='.($department=='Clinic'?'Hospital':'Inventory').'">' . $bill['fs_number'] . '</a></td>
                                                        <td class="amount" align="right">' . number_format($amount,2) . '</td>
                                                        <td>' . $include_check . '</td>
                                                    <tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr><th colspan="4">Summary</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>N<u>o</u> of bills</th>
                                                    <td><?= $numberOfBills ?></td>
                                                    <th>Total amount</th>
                                                    <td align="right"><?= number_format($totalAmount,2) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Selected bills</th>
                                                    <td><span id="dental-selected-bills"><?= $numberOfBills?></span></td>
                                                    <th>Total selected amount</th>
                                                    <td align="right"><span id="dental-selected-bills-amount"><?= number_format($totalAmount,2) ?></span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-success">Create Receipt List</button>
                                    </div>
                                    
                                <?php
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </section>

    </div> <!-- /.content-wrapper -->
    <script>
        var totalAmount_All = <?= $totalAmount ?>;
        var numberOfBills = <?= $numberOfBills ?>;
        var totalAmount = <?= $totalAmount ?>;
        var numberOfSelected = <?= $numberOfBills ?>;

        $('#total_amount').val(totalAmount);

        function uncheckAll(){
            $('.include_credit').each(function(){
                $(this).prop('checked', false);
            });
            numberOfSelected = 0;
            totalAmount = 0;
            renderTotal();
        }

        function checkAll(){
            $('.include_credit').each(function(){
                $(this).prop('checked', true);
            });
            numberOfSelected = numberOfBills;
            totalAmount = totalAmount_All;
            renderTotal();
        }

        function updateTotal(id, amount){
            rowId = 'billListRow_' + id;
            includeCheckBoxId = 'billListInclude_' + id;

            if($("#"+includeCheckBoxId).is(":checked")){
                totalAmount += amount;
                numberOfSelected++;
                $("#"+rowId).removeClass('row-exclude');
            }
            else{
                totalAmount -= amount;
                numberOfSelected--;
                $("#"+rowId).addClass('row-exclude');
            }
           renderTotal();
        }

        function renderTotal(){
            $("#dental-selected-bills-amount").html(totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $("#dental-selected-bills").html(numberOfSelected);
            $('#total_amount').val(totalAmount);
        }
        
        
    </script>
