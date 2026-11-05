   <?php
        $pettycashs = $this->data['pettycashs'];
        $expense_total = $this->data['expense_total'];
        $petty_total = $this->data['petty_total'];
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
                <h1>Reception</h1>
                <small>Petty Cash</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="<?php echo base_url(); ?>hospital/reception/receptiondashboard">Dashboard</a></li>
                </ol>
            </div>
        </section>
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-heading">
                            
                        </div>
                        <div class="panel-body">
                            <div class="panel-body  table-responsive">
                            <div class="col-sm-12 col-md-12">
                    
                                <div class="row">
                                    
                                <?php
                                    helper('form');
                                    $attributes = array('role'=>'form', 'method'=>'post');
                                    echo form_open_multipart(base_url().'hospital/reception/addpettycash', $attributes);
                                ?>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="panel panel-bd">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                        <label for="mrntitle" class="control-label">Petty Cash</label>
                                        <div class="">
                                            <input type="hidden" name="expence_from" value="">
                                            <input type="hidden" name="reference" value="">
                                            <input type="hidden" name="expence_amont" value="0">
                                            <input type="hidden" name="expence_reason" value="">
                                            <input type="hidden" name="status" value="1">
                                            <input type="hidden" name="payment_type">
                                            <input required type="number" min="1" placeholder="amount" autocomplete="off" name="petty_cash_amount" class="form-control"> 
                                        </div>
                                        </div><br>
                                        <div style="float:right">
                                                <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </div>
                                    <?php
                                        echo form_close();
                                    ?>
                                    
                            </div>

                                
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="panel panel-bd">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><?php echo $petty_total ?><span class="count-number"></span> </h2>
                                                <div class="small">Total Pettycash  </div>
                                            <div class="sparkline1 text-center"></div>
                                            
                                        </div>
                                        <div class="icon"><i class="fa fa-arrow-down" style="color:green"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="panel panel-bd">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><?php echo $expense_total ?><span class="count-number"></span> </h2>
                                                <div class="small">Total Expense  </div>
                                            <div class="sparkline1 text-center"></div>
                                            
                                        </div>
                                        <div class="icon"><i class="fa fa-arrow-up" style="color:blue"></i></div>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                <div class="panel panel-bd">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><?php echo $petty_total-$expense_total ?><span class="count-number"></span> </h2>
                                                <div class="small">Cash On Hand  </div>
                                            <div class="sparkline1 text-center"></div>
                                            
                                        </div>
                                        <div class="icon"><i class="fa fa-money" style="color:red"></i></div>
                                    </div>
                                </div>
                            </div>
                                    
                                </div>

                            </div>

                            
                        </div>

                    </div>
                </div>
                                    
                                
                            </div>
                        </div>
                    
            
            <div class="row">
            
                
                <div class="col-sm-4 col-md-4">
                    <div class="panel panel-bd">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4>Register Expense</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            
                            <div class="form-body">
                                <?php
                                    helper('form');
                                    $attributes = array('role'=>'form', 'method'=>'post');
                                    echo form_open_multipart(base_url().'hospital/reception/addpettycash', $attributes);
                                ?>
                            <div class="form-group col-md-12">
                                        <label for="mrntitle" class="control-label">Expense Amount</label>
                                        <div class="">
                                            <input type="hidden" name="petty_cash_amount" value="0">
                                            <input type="number" min="1" name="expence_amont" placeholder="amount" autocomplete="off" class="form-control" required> 
                                        </div>
                                        <label for="mrntitle" class="control-label">Expense Reason</label>
                                        <div class="">
                                            <input type="text" class="form-control" name="expence_reason" value="" required>
                                             
                                        </div>
                                        <label for="mrntitle" class="control-label">Expense From</label>
                                        <div class="">
                                            <select class="form-control" name="expence_from" required>
                                                <option></option>
                                                <option>Petty Cash</option>
                                                <option>Sales</option>
                                                <option>Other</option>
                                            </select>
                                        </div>
                                        <label for="mrntitle" class="control-label">Payment Type</u></label>
                                        <div class="">
                                            <select class="form-control" name="payment_type" required>
                                                <option></option>
                                                <option>Cash</option>
                                                <option>Check</option>
                                            </select> 
                                        </div>
                                        <label for="mrntitle" class="control-label">Reference<u></label>
                                        <div class="">
                                            <input type="text" name="reference" class="form-control" > 
                                            <input type="hidden" name="status" value="1">
                                        </div><br>
                                        <div style="float:right">
                                                <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                <?php
                                  echo  form_close();
                                ?>
                            </div>
                            
                        </div>

                    </div>
                </div>

                <div class="col-sm-8 col-md-8">
                    <div class="panel panel-bd">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4>Transaction List</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-body">
                                <div class="row">
                                    <table class="table table-hover">
                                        <tr style="font-weight: bold;">
                                            <b><td>Date</td><td>Type</td><td>Payment Type</td><td>Reason</td><td>Amount</td><td>Registered By</td><td>Action</td></b>
                                        </tr>
                                        <?php
                                        foreach($pettycashs as $pettycash):
                                        ?>
                                        <tr style="font-weight: <?php echo ($pettycash['expence_amont']==0)?"bolder":"" ?>;color:<?php echo ($pettycash['expence_amont']==0)?"green":"" ?>">
                                            <td><?php echo substr($pettycash['regisered_date'],0,10) ?></td>
                                            <td><?php echo ($pettycash['expence_amont']==0)?"Petty Cash":"Expense" ?></td>
                                            <td><?php echo $pettycash['payment_type'] ?></td>
                                            <td><?php echo $pettycash['expence_reason'] ?></td>
                                            <td><?php echo ($pettycash['expence_amont']==0)?$pettycash['petty_cash_amount']:$pettycash['expence_amont'] ?></td>
                                            <td><?php echo $pettycash['registered_by_name'] ?></td>
                                            <td><a class=" btn btn-xs btn-danger" onclick="deletePettyCash('<?= $pettycash['id'] ?>')"><i class="fa fa-trash" ></i></a></td>
                                        </tr>
                                        <?php
                                        endforeach;
                                        ?>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                
            </div>
                   
        </section>

    </div> <!-- /.content-wrapper -->
   
    <script>
        textarea.value = '';
        function deletePettyCash(id){
            if(confirm("Are you sure you want to delete?")){
                document.location = "<?= base_url() ?>hospital/reception/deletepettycash?id="+id;
            }
        }
    </script>