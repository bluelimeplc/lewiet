<?php 
   //$compounding_prescriptions = $this->data['compounding_prescriptions'];
?>

<?php
    echo view('Include/_header')
        .view('Include/_left_sidebar_top')
        .view('Inventory/Dispensary/_left_sidebar_dispensary')
        .view('Include/_left_sidebar_bottom');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Compounding</h1>
            <small>External Order</small>
            <ol class="breadcrumb">
                <li role="button">
                    <a href="/inventory/compounding/list">Compounding Prescriptions List</a>
                </li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Prescription Detail
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group col-md-3">
                            <label class="control-label">Patient Name</label>
                            <div class="">
                                <input type="text" id="patient_name" class="form-control">
                            </div>
                            <div id="patient_mobile_alert" class="alert alert-warning hide" role="alert">
                                <span>Cannot be empty</span>
                            </div>
                        </div>
                        <div class="form-group col-md-1">
                            <label class="control-label">Age</label>
                            <div class="">
                                <input type="number" id="patient_age" class="form-control">
                            </div>
                            <div id="patient_mobile_alert" class="alert alert-warning hide" role="alert">
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="control-label">Moblie</label>
                            <div class="">
                                <input type="text" id="patient_mobile" class="form-control">
                            </div>
                            <div id="patient_mobile_alert" class="alert alert-warning hide" role="alert">
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Doctor Name</label>
                            <div class="">
                                <input type="text" id="doctor_name" class="form-control">
                            </div>
                            <div id="patient_mobile_alert" class="alert alert-warning hide" role="alert">
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Hospital</label>
                            <div class="">
                                <input type="text" id="hospital_name" class="form-control">
                            </div>
                            <div id="patient_mobile_alert" class="alert alert-warning hide" role="alert">
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Compounding 
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Item</td><td width="100">Amount</td><td>Measurment</td><td width="20"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" id="prescription_item">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" id="prescription_amount">
                                    </td>
                                    <td>
                                        <select class="form-control" id="prescription_measurment">
                                            <option></option>
                                            <option value="%">%</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-success" onclick="add_compounding_item()">+</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th><th width="50">Amount</th><th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody id="compounding_prescription_table">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div style="text-align: right;">
                            <button class="btn btn-success" onclick="save_compounding_order()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
</div>

<script>
    var compounding_list = {};
    var number_of_line = 0;
    function add_compounding_item(){
        if(!validate_prescription_form())
            return 0;

        let item = items[selected_item_index['comp_selected_item_index']];
        let amount = $('#prescription_amount').val();
        let measurement = $('#prescription_measurment').val();
        let in_percent = 0;
        if(measurement == '%'){
            in_percent = amount;
            amount = 0;
        }

        let new_line = {'item_id':item.id, 'amount':amount, 'measurement':measurement, 'in_percent':in_percent};
        number_of_line++;
        compounding_list['line_'+number_of_line] = new_line;
        create_compounding_prescription_line();
        clear_after_add();
    }

    function create_compounding_prescription_line(){
        let item = items[selected_item_index['comp_selected_item_index']];
        let amount = $('#prescription_amount').val();
        let measurement = $('#prescription_measurment').val();
        let compounding_prescription_table = $('#compounding_prescription_table');

        let row = '<tr id="compounding_prescription_line_'+number_of_line+'">'+
                    '<td><span class="badge" style="float:right">' + item.item_code + '</span><b>'+ item.item_generic_name +'</b><br>' +  item.item_brand_name + '</td>'+
                    '<td>'+amount+' '+measurement+'</td>'+
                    '<td><a role="button" class="fa fa-minus-circle text-danger" title="Remove line" onclick="remove_compounding_line('+number_of_line+')"></a></td>'+
                  '</tr>';
        compounding_prescription_table.html(compounding_prescription_table.html() + row);
    }

    function remove_compounding_line(line_index){
        let line = $('#compounding_prescription_line_'+line_index);
        delete compounding_list['line_'+line_index];
        line.remove();
    }

    function clear_after_add(){
        let prescription_item = $('#prescription_item');
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');

        prescription_item.val('');
        prescription_amount.val('');
        let option = '<option></option><option value="%">%</option>';
        prescription_measurment.html(option);
        selected_item_index['comp_selected_item_index'] = -1;
        prescription_item.focus();
    }

    function validate_prescription_form(){
        let isValid = true;
        if(selected_item_index['comp_selected_item_index'] == -1){
            toastr['error']('Item is not selected');
            isValid = false;
            $('#prescription_item').focus();
        }

        if(Number($('#prescription_amount').val()) == 0){
            toastr['error']('Amount must not be zero.');
            if(isValid)
                $('#prescription_amount').focus();
            isValid = false;
        }

        if($('#prescription_measurment').val() == ''){
            toastr['error']('Measurement is not selected.');
            if(isValid)
                $('#prescription_measurment').focus();
            isValid = false;
        }

        return isValid;
    }

    function validate_save(){
        let patient_name = $('#patient_name');
        let age = $('#patient_age');
        let mobile = $('#patient_mobile');
        let doctor_name = $('#doctor_name');
        let hospital = $('#hospital_name');
        let prescription_item = $('#prescription_item');

        let isValid = true;

        if(Object.keys(compounding_list).length == 0){
            toastr['error']('Compounding item are empty.');
            prescription_item.focus();
            isValid = false;
        }

        if(patient_name.val().trim() == ''){
            toastr['error']('Patient name cannot be empty.');
            if(isValid)
                patient_name.focus();
            isValid = false;
        }

        return isValid;
    }

    function change_measurement(){
        let item = items[selected_item_index['comp_selected_item_index']];
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');
        let option = '<option></option><option value="'+item.compounding_measurement+'">'+item.compounding_measurement+'</option><option value="%">%</option>'
        prescription_measurment.html(option);
        prescription_amount.focus();
    }

    function save_compounding_order(){
         if(!validate_save())
             return 0;

        let patient_name = $('#patient_name').val();
        let age = $('#patient_age').val();
        let mobile = $('#patient_mobile').val();
        let doctor_name = $('#doctor_name').val();
        let hospital_name = $('#hospital_name').val();
        
        $.ajax({
            url: '/inventory/compounding/save_compounding_prescription_external',
            method: 'POST',
            data: {'compounding_prescription':compounding_list, 'patient_name':patient_name, 'patient_age':age, 'patient_phone':mobile, 'doctor_name':doctor_name, 'hospital_name':hospital_name},
            dataType: 'json',
            async: false,
            success:function(result){
                location = '/inventory/compounding/prepare?id='+result.value;
            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
            }
        });
        
    }
</script>

<?= view('Include/_autocomplete_item') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    selected_item_index['comp_selected_item_index'] = -1;
    var auto_complete_option = {};
    
    auto_complete_option = {
        input: document.getElementById("prescription_item"),
        items: items,
        selected_index: 'comp_selected_item_index',
        callback_function: change_measurement,
        //show_current_balance: true,
    };
    autocomplete_item_by_options(auto_complete_option);
    //autocomplete_item(document.getElementById("prescription_item"), items, 'comp_selected_item_index', change_measurement);
</script>
<?php
    echo view('Include/_footer');
?>