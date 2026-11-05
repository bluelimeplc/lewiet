<?php
    $loggeduserdata = \BluelimeAuthentication::get_logged_user_data();
    $logged_user_role = $loggeduserdata['role'];
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
                <small><?php echo 'Recieved Payments' ?></small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/reception/pendingbills">Pending Bills</a></li>
                </ol>
            </div>
        </section>

      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    form_open_multipart('/hospital/reception/receivedbills', $attributes);
            ?>

            
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input id="search_patient" type="text" name="search_patient" class="form-control" placeholder="Search By: Name | Mobile  | MRN | FS Number">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                            <div  style="text-align: right;">
                                <?= $pager->links() ?>
                            </div>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                                <thead>

                                    <tr>     
                                        <th></th>                
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Company</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Receipt No</th>
                                        <th>FS</th>
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                
                                    <?php
                                    $page = $_GET['page'] ?? 1;
                                    $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                    foreach ($bills as $bill_payment) :
                                    ?>
                                    
                                    <tr class="<?= (trim($bill_payment['fs_number']) == '') ? 'bg-gray' : '' ?>">
                                        <td><?= ++$sn ?></td>
                                        <td><?php echo "MRN-".sprintf('%05d', $bill_payment['patient_id']); ?></td>
                                        <td><?php echo  $bill_payment['patient_name'] ?> </td>
                                        <td><?php echo  $bill_payment['company_name'] ?> </td>
                                        <td><?php echo  $bill_payment['billed_date'] ?> </td>
                                        <td><?php echo  $bill_payment['total'] ?> </td>
                                        <td><?php echo  "RANK-".$bill_payment['pos_bill_id'] ?> </td>
                                        <td><?php echo  $bill_payment['fs_number'] ?> </td>
                                        <td>
                                            <a  class="btn  btn-primary btn-sm" href="/hospital/reception/viewreceipt?pos_bill_id=<?= $bill_payment['pos_bill_id']; ?>"><i class="fa fa-file"></i></a>
                                            <?php
                                                if (trim($bill_payment['fs_number']) == '') {
                                            ?>
                                                <a  class="btn btn-danger btn-sm" href="/hospital/reception/reverserecievedbill?bill_id=<?php echo $bill_payment['id'];?>" onclick="return confirm('Are you sure you want to Reverse?');">
                                                    <i class="fa fa-reply"></i>
                                                </a>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php
                                        endforeach
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>

            </div>            
        </section>

    </div> <!-- /.content-wrapper -->
    