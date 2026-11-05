<style>
#grv_detail_line tr:first-child{
    border-top: solid 2px;

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
            <small>Goods Receiving</small> | <u>Store: <?= $default_store['store_name'] ?></u>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/store/grv_list">GRV List</a></li>
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
                                <h4>GRV Main</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="grv_code" class="col-md-3 control-label" style="text-align: left;">GRV Code:</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="grvcode" id="grvcode" class="form-control" required value="<?='GRV-'.sprintf('%04d', $last_grv_id+1)?>" readonly> 
                                                    <input type="hidden" id="default_store" value="<?=$default_store['id']?>"> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="recieved_date" class="col-md-3 control-label" style="text-align: left;">Received Date:</label>
                                                <div class="col-md-9">
                                                    <input type="date" name="recieved_date" id="recieved_date" class="form-control" required value="<?= substr($grv_main['recieved_date'] ?? date('Y-m-d'),0,10) ?>" placeholder=""> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="invoice_no" class="col-md-3 control-label" style="text-align: left;">Invoice N<u>o</u>:</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" required value="<?= $grv_main['invoice_no'] ?? '' ?>" placeholder=""> 
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label for="description" class="col-md-3 control-label" style="text-align: left;">Description:</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" name="description" id="description"><?= $grv_main['description'] ?? '' ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="grv_supplier" class="col-md-3 control-label" style="text-align: left;">Supplier:</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="supplier_id" id="grv_supplier" class="form-control" required value="<?= $grv_main['grv_supplier_name'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="fs_no" class="col-md-3 control-label" style="text-align: left;">FS N<u>o</u>:</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="fs_no" id="fs_no" class="form-control" required value="<?= $grv_main['fs_no'] ?? '' ?>"  maxlength="13" onkeypress="return /[0-9]/i.test(event.key)" title="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="fs_no" class="col-md-3 control-label" style="text-align: left;">MRC N<u>o</u>:</label>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="text" name="machine_no" id="machine_no" class="form-control" maxlength="10" minlength="10" value="<?= $grv_main['machine_no'] ?? '' ?>" title="Machine Number must be 10 characters long if not manual. Eg. ABD1234567">
                                                                <a id="btn_fetch_last_mrc_number" onclick="fetch_last_mrc_number()" class="input-group-addon btn btn-warning" role="button" title="Fetch last one."><i class="fa fa-repeat"></i></a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="checkbox" id="is_manual_receipt" name="is_manual_receipt" onchange="is_manual_change()"> <label for="is_manual_receipt" class="control-label" style="text-align: left;">Is Manual</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="fs_no" class="col-md-3 control-label" style="text-align: left;">Receipt Date:</label>
                                                        <div class="col-md-6">
                                                            <div class="col-md-3" style="padding: 4px;">
                                                                <input type="text" id="receipt_date_date" class="form-control" value="<?= $grv_main['receipt_date_']['date'] ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="2">
                                                                <label for="receipt_date_date" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;DD</label>
                                                            </div>
                                                            <div class="col-md-3" style="padding: 4px;">
                                                                <input type="text" id="receipt_date_month" class="form-control" value="<?= $grv_main['receipt_date_']['month'] ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="2">
                                                                <label for="receipt_date_month" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;MM</label>
                                                            </div>
                                                            <div class="col-md-6" style="padding: 4px;">
                                                                <input type="text" id="receipt_date_year" class="form-control" value="<?= $grv_main['receipt_date_']['year'] ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="4">
                                                                <label for="receipt_date_year" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;YYYY</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="checkbox" id="is_ethiopian_calander" name="is_ethiopian_calander" title="Is Ethiopian Calander"> <label for="is_ethiopian_calander" class="control-label" style="text-align: left;" title="Is Ethiopian Calander">Eth Calander</label>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary <?= ($_GET['mode']??'')!='edit'?'':' hide '?>" onclick="show_grv_detail()" style="float: right;" id="show_grv_detail_button">GRV Detail <i class="fa fa-arrow-circle-down"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 <?= ($_GET['mode']??'')!='edit'?' hide ':''?>" id="grv_detail_section">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            <div class="panel-title" >
                                <h4>GRV Detail</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="portlet-body form">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <td width="300">
                                                    <label for="grv_detail_item">Item</label><br>
                                                    <input type="text" class="form-control" id="grv_detail_item">
                                                
                                            </td>
                                            <td width="100">
                                                <label for="grv_detail_unit_price">Unit Price</label><br>
                                                <input type="text" class="form-control" id="grv_detail_unit_price" onkeyup="calculate_tax()">
                                            </td>
                                             <td width="100">
                                                <label for="grv_detail_quantity" onkeyup="calculate_tax()">Quantity</label><br>
                                                <input type="text" class="form-control" id="grv_detail_quantity" onkeyup="calculate_tax()">
                                            </td>
                                            <td width="100">
                                                <label for="grv_detail_tax">Vat</label><br>
                                                <input type="text" class="form-control" readonly id="grv_detail_tax">
                                            </td>
                                            <td width="100">
                                                <label for="grv_detail_total">Total</label><br>
                                                <input type="text" class="form-control" readonly id="grv_detail_total">
                                            </td>
                                            <td  width="100">
                                                <label for="grv_detail_expiry_date">Expiry Date</label><br>
                                                <input type="date" class="form-control" id="grv_detail_expiry_date">
                                            </td>
                                            <td width="100">
                                                <label for="grv_detail_batch_no">Batch N<u>o</u></label>
                                                <input type="text" class="form-control" id="grv_detail_batch_no">
                                            </td>
                                            <td>
                                                <br>
                                                <button type="button" class="btn btn-primary" onclick="add_grv_line()">Add</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><div id="drug_detail" style="min-height: 50px;"></div></td>
                                        </tr>
                                    </thead>
                                    <tbody id="grv_detail_line">
                                        <?php
                                            $grv_detail_data = [];
                                            $number_of_line = 1;
                                            foreach($grv_detail as $row){
                                                echo '
                                        <tr id="grv_detail_line_tr_'.$number_of_line.'">
                                            <td><span style="float:right"><span class="badge">'.$row['item_detail']['strength'].'</span> <span class="badge badge-qty">'.$row['item_detail']['preparation'].'</span></span><b>'.$row['item_detail']['item_generic_name'].'</b><br>'.$row['item_detail']['item_brand_name'].'</td>
                                            <td>'.number_format($row['unit_price'],2).'</td>
                                            <td>'.$row['quantity'].'</td>
                                            <td>'.number_format($row['tax'],2).'</td>
                                            <td>'.number_format(($row['unit_price']*$row['quantity'])+($row['tax']),2).'</td>
                                            <td>'.$row['expiry_date'].'</td>
                                            <td>'.$row['batch_number'].'</td>
                                            <td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_grv_line('.$number_of_line.')"></a></td>
                                        </tr>';
                                                $temp = [
                                                    'id' => $row['item_id'],
                                                    'item_code' => $row['item_detail']['item_code'],
                                                    'item_category' => $row['item_detail']['item_category'],
                                                    'item_generic_name' => $row['item_detail']['item_generic_name'],
                                                    'item_brand_name' => $row['item_detail']['item_brand_name'],
                                                    'measurement' => $row['item_detail']['measurement'],
                                                    'unit_price' => $row['unit_price'],
                                                    'quantity' => $row['quantity'],
                                                    'tax' => $row['tax'],
                                                    'total' => ($row['unit_price']*$row['quantity'])+($row['tax']),
                                                    'expiry_date' => $row['expiry_date'],
                                                    'batch_no' => $row['batch_number']
                                                ];

                                                $grv_detail_data['line_'.$number_of_line] = $temp;
                                                $number_of_line++;
                                            }

                                        ?>
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #aaa;">
                                            <td>N<u>o</u> of items: <span id="grv_detail_no_of_items"></span></td><td colspan="2">VAT: <span id="grv_detail_total_tax"></span></td><td colspan="2">Total price: <span id="grv_detail_total_price"></span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-success" id="save_grv_btn" onclick="save_grv()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    var item = $('#grv_detail_item');
    var unit_price = $('#grv_detail_unit_price');
    var quantity = $('#grv_detail_quantity');
    var vat = $('#grv_detail_tax');
    var manufactured_date = $('#grv_detail_manufactured_date');
    var expiry_date = $('#grv_detail_expiry_date');
    var batch_no = $('#grv_detail_batch_no');

    var grv_detail_data = {};
    var number_of_line = <?= ($number_of_line ?? 1)-1?>;
    <?php 
        if(!empty($grv_detail_data))
            echo 'grv_detail_data = '.BluelimeSystem::convert_array_to_string($grv_detail_data).';';                          
    ?>
    
    function clear_grv_line()
    {
        item.val('');
        unit_price.val('');
        quantity.val('');
        vat.val('');
        manufactured_date.val('');
        $('#grv_detail_total').val('');
        expiry_date.val('');
        batch_no.val('');
        $("#grv_detail_item").removeAttr("style");
        $("#grv_detail_unit_price").removeAttr("style");
        $("#grv_detail_quantity").removeAttr("style");
        $("#grv_detail_manufactured_date").removeAttr("style");
        $("#grv_detail_total").removeAttr("style");
        $("#grv_detail_expiry_date").removeAttr("style");
        $("#grv_detail_batch_no").removeAttr("style");
        item.focus();
        selected_item_index['grv_selected_item_index'] = -1;
        $('#drug_detail').html('');
    }

    function add_grv_line()
    {
        if(!validate_add_grv_line())
            return false; 

        number_of_line++;

        let selected_item = items[selected_item_index['grv_selected_item_index']];

        let tax = 0;
        if(selected_item.is_taxable > 0){
            tax = 0.15;
        }
        
        let new_data = {
            "id":selected_item.id, 
            "item_code":selected_item.item_code, 
            "item_category":selected_item.item_category, 
            "item_generic_name":selected_item.item_generic_name, 
            "item_brand_name":selected_item.item_brand_name, 
            "measurement":selected_item.measurement, 
            "unit_price":unit_price.val(),
            "tax": (tax*unit_price.val()*quantity.val()), 
            "quantity":quantity.val(), 
            "manufactured_date":manufactured_date.val(), 
            "total":$('#grv_detail_total').val(), 
            "expiry_date":expiry_date.val(), 
            "batch_no":batch_no.val()
        };

        console.log(new_data);
        
        grv_detail_data['line_'+number_of_line] = new_data;

        let new_row_html = '<tr id="grv_detail_line_tr_'+number_of_line+'">'+
                                '<td><span style="float:right"><span class="badge">' + selected_item.strength + '</span> <span class="badge badge-qty">' + selected_item.preparation + '</span></span><b>'+ selected_item.item_generic_name +'</b><br>' +  selected_item.item_brand_name + '</td>'+
                                '<td>'+parseFloat(new_data.unit_price).toFixed( 2 )+'</td>'+
                                '<td>'+new_data.quantity+'</td>'+
                                '<td>'+parseFloat(new_data.tax).toFixed( 2 )+'</td>'+
                                '<td>'+new_data.total+'</td>'+
                                '<td>'+new_data.expiry_date+'</td>'+
                                '<td>'+new_data.batch_no+'</td>'+
                                '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_grv_line('+number_of_line+')"></a></td>'+
                            '</tr>';
        
        let grv_detail_line = $('#grv_detail_line');
        grv_detail_line.append(new_row_html);
        calculate_total();
        clear_grv_line();
    }

    function validate_add_grv_line(){
        let grv_detail_item = $('#grv_detail_item');
        let grv_detail_unit_price = $('#grv_detail_unit_price');
        let grv_detail_quantity = $('#grv_detail_quantity');
        let grv_detail_total = $('#grv_detail_total');
        let grv_detail_manufactured_date = $('#grv_detail_manufactured_date');
        let grv_detail_expiry_date = $('#grv_detail_expiry_date');
        let grv_detail_batch_no = $('#grv_detail_batch_no');
        let selected_item = items[selected_item_index['grv_selected_item_index']];

        if((grv_detail_item.val() === '') || (selected_item_index['grv_selected_item_index'] == -1) )
        {
            flash_red_for_validation('grv_detail_item', focus=true)
            return false;
        }
        if(parseFloat('0'+grv_detail_unit_price.val()) <= 0)
        {
            grv_detail_unit_price.css("borderColor","red");
            grv_detail_unit_price.focus();
            return false;
        }

        if(parseFloat('0'+grv_detail_quantity.val()) <= 0)
        {
            grv_detail_quantity.css("borderColor","red");
            grv_detail_quantity.focus();
            return false;
        }
        if(parseFloat('0'+grv_detail_total.val()) <= 0)
        {
            grv_detail_total.css("borderColor","red");
            grv_detail_total.focus();
            return false;
        }
        if(selected_item.is_perishable > 0){
            
            let today = new Date("<?= date('Y-m-d') ?>");
            
            let expiry_date =new Date(grv_detail_expiry_date.val());
            if(expiry_date == 'Invalid Date')
            {
                grv_detail_expiry_date.css("borderColor","red");
                grv_detail_expiry_date.focus();
                return false;
            }
            else if(expiry_date <= today)
            {
                grv_detail_expiry_date.css("borderColor","red");
            }

            if(grv_detail_batch_no.val().trim().length == 0)
            {
                grv_detail_batch_no.css("borderColor","red");
                grv_detail_batch_no.focus();
                return false;
            }
        }
        
        return true;
    }

    function remove_grv_line(line_number)
    {
        $('#grv_detail_line_tr_'+line_number).remove();
        delete grv_detail_data['line_'+line_number];
        calculate_total();
    }

    function calculate_total()
    {
        let grv_detail_no_of_items = $('#grv_detail_no_of_items');
        let grv_detail_total_price = $('#grv_detail_total_price');
        let grv_detail_total_tax = $('#grv_detail_total_tax');

        grv_detail_no_of_items.html(Object.keys(grv_detail_data).length);

        let total_price = 0;
        let total_tax = 0;
        jQuery.each(grv_detail_data, function(i, val) {
            total_price += Number(val.tax) + Number(val.unit_price * val.quantity);
            total_tax += Number(val.tax);
        });

        grv_detail_total_price.html(parseFloat(total_price).toFixed( 2 ));
        grv_detail_total_tax.html(parseFloat(total_tax).toFixed( 2 ));
    }

    function calculate_tax()
    {
       
        let selected_item = items[selected_item_index['grv_selected_item_index']];
        let total_tax = 0;

        let tax = 0;
        if(selected_item.is_taxable > 0){
            tax = 0.15;
        }
        total_tax = parseFloat(unit_price.val()*quantity.val()*tax).toFixed( 2 );
        total = (parseFloat(unit_price.val()*quantity.val()) + parseFloat(total_tax)).toFixed( 2 );
        $('#grv_detail_tax').val(total_tax);
        $('#grv_detail_total').val(total);
        $('#grv_detail_tax').val(total_tax);
    }
    
    function save_grv()
    {
        if(!validate_save_grv())
            return false
        
        let grv_code = $('#grvcode').val();
        let default_store = $('#default_store').val();
        let recieved_date = $('#recieved_date').val();
        let supplier_id = suppliers[selected_supplier['grv_selected_supplier_index']]['id'];
        let invoice_no = $('#invoice_no').val();
        let fs_no = $('#fs_no').val().padStart(8, '0'); // Ensure FS No is 8 digites long
        let machine_no = $('#machine_no').val().toUpperCase();
        let receipt_date = get_valid_receipt_date();
        let is_manual_receipt = $('#is_manual_receipt').is(':checked') ? 1 : 0;
        let is_ethiopian_calander = $('#is_ethiopian_calander').is(':checked') ? 1 : 0;
        let description = $('#description').val();
 
        $('#save_grv_btn').attr('disabled', true);
        $('#save_grv_btn').html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: '/inventory/store/grvsave<?= (($_GET['mode']??'')=='edit')?'?mode=update':'' ?>',
            data: {<?= (($_GET['mode']??'')=='edit')?'grv_id:'.$_GET['grv_id'].',':'' ?> grv_main:{grv_code:grv_code, recieved_date: recieved_date, supplier_id: supplier_id, invoice_no: invoice_no, fs_no: fs_no,machine_no: machine_no, receipt_date: receipt_date, is_ethiopian_calander: is_ethiopian_calander, is_manual_receipt: is_manual_receipt, description: description}, grv_detail: JSON.stringify(grv_detail_data)},
            method:'POST',
            async: false,
            dataType: 'json',
            success:function(result){
                location = '/inventory/store/grv_print?grv_id='+result.value;
            },
            error:function(jqXHR,status,error){
                alert(error);
            }
        });
    }
    
    var machine_no_val = '';
    function is_manual_change()
    {
        let is_manual = $('#is_manual_receipt');
        let machine_no = $('#machine_no');
        let btn_fetch_last_mrc_number = $('#btn_fetch_last_mrc_number');
        if(is_manual.is(':checked')==true){
            machine_no_val = machine_no.val();
            machine_no.val('');
            machine_no.attr('readonly', true);
            btn_fetch_last_mrc_number.attr('disabled', true);
            btn_fetch_last_mrc_number.attr('onclick', '');
        }
        else{
            machine_no.val(machine_no_val);
            machine_no.removeAttr('readonly');
            btn_fetch_last_mrc_number.removeAttr('disabled');
            btn_fetch_last_mrc_number.attr('onclick', 'fetch_last_mrc_number()');
        }
    }

    function show_grv_detail()
    {
        if(!validate_save_grv(false))
            return false;

        $('#grv_detail_section').removeClass('hide');
        $('#grv_detail_item').focus();
        $('#show_grv_detail_button').addClass('hide');
    }

    function validate_save_grv(with_item_list = true){
        
        let grv_code = $('#grvcode');
        let recieved_date = $('#recieved_date');
        let grv_supplier = $('#grv_supplier');
        let invoice_no = $('#invoice_no');
        let fs_no = $('#fs_no');
        let description = $('#description');

        if(grv_code.val().trim() == '')
        {
            flash_red_for_validation('grvcode',false);
            return false;
        }

        let today = new Date('<?= date('Y-m-d') ?>');
        let recieved_date_ = new Date(recieved_date.val());
        if(recieved_date_ == 'Invalid Date')
        {
            flash_red_for_validation('recieved_date');
            return false;
        }
        else if(recieved_date_ > today){
            flash_red_for_validation('recieved_date');
            return false;
        }

        if(selected_supplier['grv_selected_supplier_index'] >= 0){
            if(suppliers[selected_supplier['grv_selected_supplier_index']]['company_name'] != grv_supplier.val().trim()){
                flash_red_for_validation('grv_supplier');
                return false;
            }
        }

        if(selected_supplier['grv_selected_supplier_index'] == -1){
            flash_red_for_validation('grv_supplier');
            return false;
        }

        // if(invoice_no.val().trim() == ''){
        //     flash_red_for_validation('invoice_no');
        //     return false;
        // }
        
        let is_manual = $('#is_manual_receipt');

        if(Number(fs_no.val().trim()) < 1){
            flash_red_for_validation('fs_no');
            toastr['error']('FS Number cannot be empty.');
            return false;
        }
        // else if(fs_no.val().trim().length < 10 && is_manual.is(':checked')==false){
        //     flash_red_for_validation('fs_no');
        //     toastr['error']('FS Number must be at least 10 digits long if not manual');
        //     return false;
        // }

        let machine_no = $('#machine_no');
        
        if((machine_no.val().trim().length < 10) && (is_manual.is(':checked')==false)){
            flash_red_for_validation('machine_no');
            return false;
        }
        
        let receipt_date = get_valid_receipt_date();
        if(receipt_date == 'Invalid Date')
        {
            return false;
        }
        else if(receipt_date.length < 10){
            flash_red_for_validation('receipt_date_date');
            flash_red_for_validation('receipt_date_month',false);
            flash_red_for_validation('receipt_date_year',false);
            return false;
        }

        if((Object.keys(grv_detail_data).length == 0) && (with_item_list))
        {
            item.css("borderColor","red");
            item.focus();
            return false;
        }

        return true;
    }

    function fetch_last_mrc_number(){

        let machine_no_input = $('#machine_no');
        let supplier_id = suppliers[selected_supplier['grv_selected_supplier_index']]['id'];

        if(supplier_id == undefined || supplier_id == null || supplier_id == '' || supplier_id == -1){
            toastr['error']('Please select a supplier first.');
            return false;
        }

        if(machine_no_input.val().trim() != ''){
            toastr['error']('Machine number is already set. Please clear it first.');
            return false;
        }

        let btn_fetch_last_mrc_number = $('#btn_fetch_last_mrc_number');
        btn_fetch_last_mrc_number.html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/inventory/store/get_last_mrc_number?supplier_id='+supplier_id,
            method: 'GET',
            dataType: 'json',
            success: function(result){
                if(result.status == 'success'){
                    machine_no_input.val(result.value);
                }
                else{
                    toastr['error'](result.message);
                }
                btn_fetch_last_mrc_number.html('<i class="fa fa-repeat"></i>');

            },
            error: function(jqXHR, status, error){
                toastr['error']('Error fetching last MRC number: ' + error);
                btn_fetch_last_mrc_number.html('<i class="fa fa-repeat"></i>');
            }
        });
    }

    const machine_no_input = document.getElementById('machine_no');

    machine_no_input.addEventListener('keypress', function(event) {
        // console.log('Key pressed:', event.key);
        // console.log('Val:', machine_no_input.value);
        // console.log('POS:', machine_no_input.selectionStart);

        let new_key = event.key;
        let val = machine_no_input.value;
        let position = machine_no_input.selectionStart;
        let new_val = val.slice(0, position) + new_key + val.slice(position);

        let letters = new_val.slice(0, 3).replace(/[^A-z]/g, '');
        let digits = new_val.slice(3).replace(/[^0-9]/g, '');
        digits = digits.slice(0, 7); // Only allow 7 digits
        let result = letters + digits;

        if(result !== new_val) {
            event.preventDefault(); // Prevent the default action of the key press
            // machine_no_input.value = result; // Set the value to the formatted result
            // machine_no_input.setSelectionRange(position + 1, position + 1); // Move the cursor to the next position
        }

    });

    function get_valid_date(date, month, year, is_ethiopian_calander = 0){
        let date_string = '';
        date_string = (parseInt(date) < 10 ? '0' : '') + parseInt(date) + '/' + (parseInt(month) < 10 ? '0' : '') + parseInt(month) + '/' + year;

        date = Number(date)
        month = Number(month)
        year = Number(year)

        if(parseInt(date) < 1 || parseInt(date) > 31){
            flash_red_for_validation('receipt_date_date');
            return 'Invalid Date';
        }
        if((parseInt(month) < 1 || parseInt(month) > 12) && (!is_ethiopian_calander)){
            flash_red_for_validation('receipt_date_month');
            return 'Invalid Date';
        }
        else if((parseInt(month) < 1 || parseInt(month) > 13) && (is_ethiopian_calander)){
            flash_red_for_validation('receipt_date_month');
            return 'Invalid Date';
        }

        if((parseInt(year) < <?= intval(date('Y'))-1 ?> || parseInt(year) > <?= date('Y')?>) && is_ethiopian_calander == 0){
            flash_red_for_validation('receipt_date_year');
            return 'Invalid Date';
        }
        return date_string;
    }

    function get_valid_receipt_date(){
        let receipt_date_date = $('#receipt_date_date').val();
        let receipt_date_month = $('#receipt_date_month').val();
        let receipt_date_year = $('#receipt_date_year').val(); 
        let is_ethiopian_calander = $('#is_ethiopian_calander').is(':checked') ? 1 : 0;
        
        return get_valid_date(receipt_date_date, receipt_date_month, receipt_date_year, is_ethiopian_calander);
    }

    function is_manual_receipt_on(){
        $('#is_manual_receipt').prop( "checked", true );
        is_manual_change();
    }

    
    <?php
        if($grv_main['is_manual_receipt'] ?? 0){
                echo '
    $( document ).ready(function() {
        is_manual_receipt_on();
    });';
        }
    ?>

    <?php
        if($grv_main['is_ethiopian_calander'] ?? 0){
                echo '
    $( document ).ready(function() {
        $("#is_ethiopian_calander").prop( "checked", true );
    });';
        }
    ?>
    

    function flash_red_for_validation(objID, focus=true){
        $('#'+objID).css('background-color', '#f00');
        setTimeout("$('#"+objID+"').css('background-color', '#fff')", 300);
        if(focus)
            $('#'+objID).focus();
    }
</script>
<?= view('Include/_autocomplete_item') ?>
<?= view('Include/_autocomplete_supplier') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    var auto_complete_option = {};
    
    selected_item_index['grv_selected_item_index'] = -1;
    auto_complete_option = {
        input: document.getElementById("grv_detail_item"),
        items: items,
        selected_index: 'grv_selected_item_index',
        callback_function: grv_item_selected,
        //show_current_balance: true
    };
    autocomplete_item_by_options(auto_complete_option);


    function grv_item_selected(){
        let item_index = selected_item_index['grv_selected_item_index'];
        let selected_item = items[item_index];
        let item_display = '<table><tr>'
                                +'<td><b>'+selected_item['item_brand_name']+'</b><br>'+selected_item['item_generic_name']+'</td>'
                                +'<td><span class="badge" style="margin:1px">'+selected_item['strength']+'</span>'
                                +'<span class="badge badge-qty" style="margin:1px">'+selected_item['preparation']+'</span></span></td>'
                            +'</tr></table>';
        $('#drug_detail').html(item_display);
        calculate_tax();
        unit_price.focus();
    }
    //autocomplete_item(document.getElementById("grv_detail_item"), items);

    var suppliers = <?= \BluelimeSystem::convert_array_to_string($suppliers) ?>;
    selected_supplier['grv_selected_supplier_index'] = -1;
    autocomplete_supplier(document.getElementById("grv_supplier"), suppliers, "grv_selected_supplier_index");
    selected_supplier['grv_selected_supplier_index'] = <?= ($grv_main['supplier_id'] ?? -1) > 0 ? "'id_".($grv_main['supplier_id']??-1)."'" : -1 ?>;
    calculate_total();
</script>