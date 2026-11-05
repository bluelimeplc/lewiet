
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Reception');?></h1>
            <small><?php echo lang('Pending Biopsy Orders');?></small>
        </div>
    </section>
    <section class="content">
            
        <!-- Search Area -->
        <div class="row">
            <?php
                $attributes = array('method' => 'get', 'role'=>'form');
                helper('form');
                echo form_open('/hospital/reception/biopsyorders', $attributes);
            ?>

        
                        
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <div class="form-group">
                                <div class="col-sm-2">
                                    <label for="filter_name">Name</label>
                                    <input id="filter_name" type="text" name="filter_name" class="form-control" placeholder="Filter by name" value="<?= $_GET['filter_name'] ?? '' ?>">
                                </div>
                                <div class="col-sm-2">
                                    <label for="filter_mobile">Mobile</label>
                                    <input id="filter_mobile" type="text" name="filter_mobile" class="form-control" placeholder="Filter by mobile" value="<?= $_GET['filter_mobile'] ?? '' ?>">
                                </div>
                                <div class="col-sm-2">
                                    <label for="filter_histology_number">Histology Number</label>
                                    <input id="filter_histology_number" type="text" name="filter_histology_number" class="form-control" placeholder="Filter by histology number" value="<?= $_GET['filter_histology_number'] ?? '' ?>">
                                </div>
                                <div class="col-sm-2">
                                    <label for="filter_status">Status</label>
                                    <select id="filter_status" name="filter_status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1" <?= ($_GET['filter_status'] ?? '') == '1' ? 'selected' : '' ?>>Paid</option>
                                        <option value="2" <?= ($_GET['filter_status'] ?? '') == '2' ? 'selected' : '' ?>>Sample Collected</option>
                                        <option value="3" <?= ($_GET['filter_status'] ?? '') == '3' ? 'selected' : '' ?>>Sample Sent</option>
                                        <option value="4" <?= ($_GET['filter_status'] ?? '') == '4' ? 'selected' : '' ?>>Slide Received</option>
                                        <option value="5" <?= ($_GET['filter_status'] ?? '') == '5' ? 'selected' : '' ?>>Result Saved</option>
                                        <option value="6" <?= ($_GET['filter_status'] ?? '') == '6' ? 'selected' : '' ?>>Result Approved</option>
                                        <option value="7" <?= ($_GET['filter_status'] ?? '') == '7' ? 'selected' : '' ?>>Result Delivered</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-success btn-block">Filter <i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <a href="/hospital/reception/biopsyorders" class="btn btn-default btn-block">Clear Filters</a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
    
            <?php
                form_close();
            ?> 
                <!-- Main content -->
                    
            <div class="col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        Pending Orders (<?= count($orders) ?>)
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive biopsy-orders-table" id="pendingAppointmentS">
                            <thead>
                                <tr>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Mobile</th>
                                    <th class="desktop">Ordered Date</th>
                                    <th class="all">Status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                    foreach($orders as $order){
                                        $statusText = 'Unknown';
                                        $statusClass = 'badge-secondary';
                                        $statusStyle = 'background-color: #6c757d; color: #fff;';
                                        switch (isset($order['order_status']) ? $order['order_status'] : '') {
                                            case '1':
                                                $statusText = 'Paid';
                                                $statusClass = 'badge-primary'; // classic blue
                                                $statusStyle = 'background-color: #007bff; color: #fff;';
                                                break;
                                            case '2':
                                                $statusText = 'Sample Collected';
                                                $statusClass = 'badge-warning'; // classic yellow/orange
                                                $statusStyle = 'background-color: #ffc107; color: #212529;';
                                                break;
                                            case '3':
                                                $statusText = 'Sample Sent';
                                                $statusClass = 'badge-info'; // classic cyan
                                                $statusStyle = 'background-color: #17a2b8; color: #fff;';
                                                break;
                                            case '4':
                                                $statusText = 'Slide Received';
                                                $statusClass = 'badge-secondary'; // classic grey
                                                $statusStyle = 'background-color: #6c757d; color: #fff;';
                                                break;
                                            case '5':
                                                $statusText = 'Result Saved';
                                                $statusClass = 'badge-dark'; // classic black
                                                $statusStyle = 'background-color: #343a40; color: #fff;';
                                                break;
                                            case '6':
                                                $statusText = 'Result Approved';
                                                $statusClass = 'badge-success'; // classic green
                                                $statusStyle = 'background-color: #28a745; color: #fff;';
                                                break;
                                            case '7':
                                                $statusText = 'Result Delivered';
                                                $statusClass = 'badge-danger'; // classic red
                                                $statusStyle = 'background-color: #dc3545; color: #fff;';
                                                break;
                                        }
                                ?>
                                <tr onclick="view_order_detail(<?=$order['id']?>)" style="cursor: pointer;">
                                    <td><?=$order['patient_name']?></td>
                                    <td><?=$order['mobile']?></td>
                                    <td><?=date('Y-m-d',strtotime($order['created_at']))?></td>
                                    <td><span class="badge <?=$statusClass?>" style="<?=$statusStyle?>"><?=esc($statusText)?></span></td>
                                    
                                </tr>
                                <?php
                                    }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
                $status_mode = $_GET['filter_status'] ?? '';
                $result_heading = ($status_mode === '7') ? 'Results Delivered' : 'Results Approved';
                $displayed_orders = ($status_mode === '7') ? ($orders_delivered ?? []) : $orders_result;
                $status_label = ($status_mode === '7') ? 'Result Delivered' : 'Result Approved';
                $status_badge_class = ($status_mode === '7') ? 'badge-danger' : 'badge-success';
                $status_badge_style = ($status_mode === '7') ? 'background-color: #dc3545; color: #fff;' : 'background-color: #28a745; color: #fff;';
                $status_param = ($status_mode === '7') ? '7' : '6';
                
            ?>
            <div class="col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <?= esc($result_heading) ?> (<?= count($displayed_orders) ?>)
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive biopsy-orders-table" id="resultAppointmentS">
                            <thead>
                                <tr>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Mobile</th>
                                    <th class="desktop" >Report Date</th>
                                    <th class="all">Status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                    foreach($displayed_orders as $order){
                                        $notificationCellStyle = (isset($order['has_new_notification']) && $order['has_new_notification']) ? 'background-color: #ffc107; color: #212529;' : '';
                                        $delivered_date = ($status_mode === '7') ? "<hr style='margin:0; padding:0;height:4px'>".$order['updated_at'] : '';
                                ?>
                                <tr onclick="location.href='/hospital/reception/viewbiopsyresult?brid='+<?=$order['result_id']?>+'&status='+<?=$status_param?>+'&order_id='+<?=$order['id']?>;" style="cursor: pointer;">
                                    <td><?=esc($order['patient_name'])?></td>
                                    <td><?=$order['mobile']?></td>
                                    <td><?=date('Y-m-d',strtotime($order['updated_at']))?></td>
                                    <td style="<?= $notificationCellStyle ?>"><span class="badge <?=$status_badge_class?>" style="<?=$status_badge_style?>"><?=esc($status_label).$delivered_date?></span></td>
                                    <td><a href="/hospital/reception/order_detail_view_only?order_main_id=<?=$order['id']?>"><i class="fa fa-eye"></i></a></td>
                                </tr>
                                <?php
                                    }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
                            
    </section>
</div>




<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('.biopsy-orders-table').DataTable({
                responsive: true,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
            });
        }
    });

    function view_order_detail(order_main_id) {
        location = '/hospital/reception/order_detail?order_main_id=' + order_main_id;
    }
</script>