<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Store/_left_sidebar_store') ?>
<?= view('Include/_left_sidebar_bottom') ?>
<style>
    .tool-tip[title-new]:hover:after {
      content: attr(title-new);
      position: absolute;
      border: #c0c0c0 1px dotted;
      padding: 10px;
      display: block;
      z-index: 100;
      background-color: #000000;
      color: #ffffff;
      max-width: 200px;
      text-decoration: none;
      text-align:center;
    }
</style>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Store Issue Form</small> | <u>Store: <?= @$default_store['store_name'] ?></u>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/store/">Request List</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="form-horizontal">
            <div class="row">
                
                <div class="col-sm-12 col-md-12">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            <div class="panel-title" >
                                <h4>Store Request Info</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td width="150" align="right"><b>Requesting Store:</b></td><td><?= $srv_main['requesting_store'] ?></td>
                                    <td width="150" align="right"><b>Request Date:</b></td><td>2024-05-23</td>
                                    <td width="150" align="right"><b>Description:</b></td><td colspan="3">Pharmacy</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 col-md-5">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            <div class="panel-title" >
                                <h4>Issue</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div id="issue_form">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 col-md-7">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            <div class="panel-title" >
                                <h4>Requested Items</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="20" rowspan="2">SN</th><th rowspan="2">Item</th><th rowspan="2"><i class="fa fa-pie-chart" aria-hidden="true"></i></th><th rowspan="2">Quantity</th><th width="120" colspan="2">Issued</th>
                                    </tr>
                                    <tr>
                                        <th>Batch</th><th>Qty</th>
                                    </tr>
                                    <style>
                                        .bt-compact-table{
                                            margin-bottom:0px !important;
                                            background-color:#0000 !important;
                                        }

                                        .bt-compact-table>tbody>tr>td{
                                            padding: 1px !important;
                                            border-top: none;
                                            border-bottom: solid 1px #ccc;
                                        }

                                        .bt-removable-row:hover .bt-btn-remove-row{
                                            visibility: visible;
                                        }

                                        .bt-btn-remove-row{
                                            visibility: hidden;
                                        }

                                    </style>
                                    <tbody>
                                        <?php
                                            $sn = 0;
                        
                                            foreach($srv_detail as $srv_line){
                                                $sn++;
                                                $item_name = '<span style="float:right"><span class="badge">'.$item_detail['item_id_'.$srv_line['item_id']]['strength'].'</span><span class="badge badge-qty">'.$item_detail['item_id_'.$srv_line['item_id']]['preparation'].'</span></span><b>'.$srv_line['item_detail']['item_generic_name'].'</b><br>'.$srv_line['item_detail']['item_brand_name'];
                                                $onclick_script = 'onclick="show_batch_quantity(\''.$srv_line['item_id'].'\', '.$sn.', '.$srv_line['quantity'].')"';
                                                if($srv_main['requesting_store'] == 'Compounding')
                                                {
                                                    if(intval($item_detail['item_id_'.$srv_line['item_id']]['compounding_volume']) <= 0){
                                                        $item_name .= '<br><span class="text-danger" title="Cannot be issued to compounding store.">This item has no compounding volume.</span> <a href="/inventory/store/new_item?iid='.$srv_line['item_id'].'&edit=1" target="_blank" class="btn btn-xs btn-primary tool-tip" title-new="Click to set compounding volume"><i class="fa fa-edit"></i></a>';
                                                        $onclick_script = 'onclick="toastr[\'error\'](\'This item has no compounding volume. It cannot be issued to compounding store.\')"';
                                                    }
                                                }
                                                    
                                                echo '
                                        <tr class="siv_line" id="siv_line_'.$sn.'" '.$onclick_script.'>
                                            <td>'.$sn.'</td>
                                            <td>'.$item_name.'</td>
                                            <td>'.$srv_line['item_detail']['measurement'].'</td>
                                            <td>'.$srv_line['quantity'].'</td>
                                            <td colspan="2">
                                                <table class="table bt-compact-table">
                                                    <tbody class="issue_list">
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                                ';
                                            }
                                        ?>
                                    </tbody>
                                </thead>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-success" id="siv_save_button" onclick="save_siv()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    var item_quantity = <?= \BluelimeSystem::convert_array_to_string($item_quantity) ?>;
    var item_detail = <?= \BluelimeSystem::convert_array_to_string($item_detail) ?>;
    
    <?php
        $issue_data = '';
        foreach($srv_detail as $srv_line)
        {
            if(empty($issue_data))
                $issue_data = '{';
            else
                $issue_data .= ',';

            $issue_data .= '"item_id_'.$srv_line['item_id'].'":{"item_id":'.$srv_line['item_id'].', "batch_quantity":{}}';
        }

        if(empty($issue_data))
            $issue_data = '{';
        $issue_data .= '}';
    ?>

    var issue_data = <?= $issue_data ?>;
    var selected_item_requested_quantity = 0;
    var prv_selected_row_index = -1;
    var selected_item_id = -1;
    function show_batch_quantity(item_id, row_index, requested_quantity, force_reload = false)
    {
        if((prv_selected_row_index == row_index) && (!force_reload))
            return false;
        prv_selected_row_index = row_index;
        selected_item_id = item_id;

        selected_item_requested_quantity = requested_quantity;

        let selected_row = $('#siv_line_'+row_index);
        let allrow = $('.siv_line');
        let issue_form = $('#issue_form');

        allrow.css('background-color','');
        selected_row.css('background-color','#dee');
        
        let selected_item_quantity = item_quantity['item_id_'+item_id];

        let available_found = false;
        let html_table = '<table class="table"><thead><tr><th>Batch N<u>o</u></th><th>Expiry</th><th>Qty</th><th width="100">Issue</th><th>&nbsp;</th></thead><tbody>';
        $.each(selected_item_quantity, function(i, item) {
            available_found = true;
            let expiry_date = new Date(item.expiry_date);
            let today = new Date("<?= date("Y-m-d") ?>");
            let remaining_days = Math.round((expiry_date.getTime() - today.getTime())/ (1000 * 3600 * 24));
            let remaining_months = Math.floor(remaining_days / 30);
            let row_bg = '';
            if(remaining_months < 0)
                row_bg = 'bg-red';
            html_table += '<tr class="'+row_bg+'"><td>'+item.batch_number+'</td><td>'+item.expiry_date+'</td><td>'+(item.quantity)+'</td><td><input data-toggle="tooltip" data-html="true" title="" id="issue_qty" type="number" class="form-control"></td><td><button class="btn" onclick="add_issue_quantity(this,'+item_id+','+row_index+')">Add</button></td></tr>';
        });

        if(!available_found)
            html_table += '<tr><td colspan="4">There is no available '+ item_detail['item_id_'+item_id]['item_generic_name'] + ' | <b>' + item_detail['item_id_'+item_id]['item_brand_name'] + '</b> ' + '<span class="badge">' + item_detail['item_id_'+item_id]['item_code'] + '</span>' + ' in your store</td></tr>';
        html_table += '</tbody></table>';

        issue_form.html(html_table);
    }

    function add_issue_quantity(btn, item_id, row_index)
    {
        let issue_value = parseFloat(btn.parentElement.parentElement.children[3].firstChild.value);
        if(issue_value <= 0)
            return 0;

        let selected_batch_number = btn.parentElement.parentElement.children[0].innerHTML;
        let item_batch_detail = item_quantity['item_id_'+item_id]['batch_'+selected_batch_number];
        let batch_quantity = item_batch_detail['quantity'];
        let issu_check = check_available_batch_quantity(item_id,selected_batch_number,issue_value);
        
        if(issu_check <= 0)
        {
            if(issue_quantity_is_more(item_id, issue_value)){
                toastr["error"]("Cannot add more than requested");
                return 0;
            }

            let issue_data_temp = issue_data['item_id_'+item_id]['batch_quantity'];
            let quantity = 0;
            if (issue_data_temp.hasOwnProperty(selected_batch_number))
                quantity = issue_data_temp[selected_batch_number];
            issue_data_temp[selected_batch_number] = parseFloat(quantity + issue_value);
            issue_data['item_id_'+item_id]['batch_quantity'] = issue_data_temp;

            batch_quantity = batch_quantity - issue_value;
            item_quantity['item_id_'+item_id]['batch_'+selected_batch_number]['quantity'] = batch_quantity;
            btn.parentElement.parentElement.children[2].innerHTML = batch_quantity;
            btn.parentElement.parentElement.children[3].firstChild.value = '';
            update_issue_quantity(item_id, row_index);
        }
        else
            toastr["error"]("No enough quantity");
    }

    function remove_from_issued(item_id,batch_number, row_index){
        item_quantity['item_id_'+item_id]['batch_'+batch_number]['quantity'] += issue_data['item_id_'+item_id]['batch_quantity'][batch_number];
        issue_data['item_id_'+item_id]['batch_quantity'][batch_number] = 0;
        delete issue_data['item_id_'+item_id]['batch_quantity'][batch_number];
        update_issue_quantity(item_id, row_index);
        show_batch_quantity(item_id, row_index, true);
    }

    function update_issue_quantity(item_id, row_index)
    {
        let row = $('#siv_line_'+row_index);
        let issue_list = issue_data['item_id_'+item_id]['batch_quantity'];

        let table = '';
        let total = 0;
        $.each(issue_list, function(batch_number, quantity) {
            table += '<tr class="bt-removable-row"><td>'+batch_number+'</td><td>'+quantity+'</td><td><a role="button" class="fa fa-minus-circle text-danger bt-btn-remove-row" onclick="remove_from_issued('+item_id+',\''+batch_number+'\','+row_index+')"></a></td></tr>';
            total += quantity;
        });
        if(total > 0)
            table += '<tr><td>TOTAL</td><td><b>'+total+'</b></td><td></td></tr>';
        row.children()[4].children[0].children[0].innerHTML = table;

        // if(requested_quantity >= total){
        //     if(total > 0)
        //         table += '<tr><td>TOTAL</td><td><b>'+total+'</b></td><td></td></tr>';
        //     row.children()[4].children[0].children[0].innerHTML = table;
        // }
        // else{
        //     document.getElementById("issue_qty").style.borderColor = 'red';
        //     $('#issue_qty').attr('title', 'Requested Quantity is '+requested_quantity);
        // }
    }

    function check_available_batch_quantity(item_id, batch_number, quantity){
        let batch_quantity = item_quantity['item_id_'+item_id]['batch_'+batch_number]['quantity'];
        if(batch_quantity > quantity)
            return -1;
        else if(batch_quantity == quantity)
            return 0;
        else
            return 1;
    }

    function issue_quantity_is_more(item_id, issue_quantity){
        let batch_quantity = issue_data['item_id_'+item_id]['batch_quantity'];
        let total = 0;
        $.each(batch_quantity, function(batch_number, quantity) {
            total +=quantity;
        });

        if((issue_quantity + total) > selected_item_requested_quantity)
            return true;

        return false;
    }

    function save_siv(){
        $('#siv_save_button').prop('disabled', true);
        $('#siv_save_button').html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: '/inventory/store/siv_save',
            method: 'POST',
            data:{issue_data:issue_data, srv_id:<?= $srv_main['id'] ?>},
            dataType: 'json',
            async: false,
            success:function(result){
                if(result.status == 'success')
                    location = '/inventory/store/siv_print?siv_id='+result.value.siv_id;
                else
                    alert(result.message)
            },
            error:function(jqXHR,status,error){
                alert(error);
            }
        });
    }
    
</script>
<?= view('Include/_footer') ?>