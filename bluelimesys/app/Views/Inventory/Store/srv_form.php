
<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Store Request</small>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php 
            helper('form');
            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
            echo form_open_multipart('inventory/store/savesrv', $attributes);                
        ?>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>SRV Main</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="srv_code" class="col-md-4 control-label"
                                                style="text-align: left;">SRV Code:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="srv_code" id="srv_code" class="form-control"
                                                    required readonly value="<?=sprintf('%04d',@$srv_main[0]['id']+1)?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="request_date" class="col-md-4 control-label"
                                                style="text-align: left;">Date:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="request_date" id="request_date"
                                                    class="form-control" readonly required value="<?= date('Y-m-d') ?>"
                                                    placeholder="">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="requesting_store" class="col-md-4 control-label"
                                                        style="text-align: left;">Requesting:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="requesting_store" id="requesting_store"
                                                            class="form-control" required value="Despensary Store"
                                                            placeholder="" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="requested_store" class="col-md-4 control-label"
                                                        style="text-align: left;">Requested:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="requested_store" id="requested_store"
                                                            class="form-control" required value="Main Store"
                                                            placeholder="" readonly>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-bd panel-form">

                            <div class="panel-body">
                                <div class="portlet-body form">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <td width="250">
                                                    <label for="srv_detail_item">Item</label><br>
                                                    <input type="text" class="form-control" id="srv_detail_item">

                                                </td>

                                                <td width="100">
                                                    <label for="current_balance ">Balance</label>
                                                    <input type="text" class="form-control" id="current_balance"
                                                        readonly>
                                                </td>

                                                <td width="100">
                                                    <label for="srv_detail_quantity">Quantity</label><br>
                                                    <input type="text" class="form-control" id="srv_detail_quantity">
                                                </td>

                                                
                                                <td>
                                                    <label
                                                        for="add_button">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                                                    <button id="add_button" type="button" class="btn btn-primary"
                                                        onclick="add_srv_line()"><b>+</b></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody id="srv_detail_line">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6">&nbsp;</td>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-footer" align="right">
                                <input type="button" class="btn btn-success" value="Save" onclick="save_srv()">
                            </div>
                        </div>
                    </div>

                </div>



            </div>
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Store Request List</h4>
                        </div>
                    </div>
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="row">&nbsp;</div>
                            <div class="col-sm-6">
                                <input id="search_store_request" type="text" placeholder="Search Store Request"
                                    name="search_store_request" class="form-control" value="<?= @$searchkey ?>">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Search <i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped">
                        <tr style="font-weight: bold;">
                            <td>Code</td>
                            <td>Date</td>
                            <td>Request By</td>
                            <td>Requested Store</td>
                            <td></td>
                        </tr>

                        <?php
                                                    $sn = 0;
                                                    foreach($srv_main as $srv)
                                                    {
                                                        $sn++;
                                                        echo '
                                                            <tr>
                                                            <td>'.sprintf('%05d',$srv['id']).'</td><td>'.date('Y-m-d', strtotime($srv['request_date'])).'</td><td>'.$srv['requested_by__name'].'</td><td>'.$srv['requesting_store'].'</td><td>'.$srv['requested_store'].'</td><td> <a href="/inventory/store/srv_print?srv_id='.$srv['id'].'" class="btn btn-warning btn-xs"> <i class="fa fa-eye"></i></a> </td><td></td>
                                                            </tr>
                                                        ';
                                                    }
                                            ?>

                    </table>

                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">
                                <div class="col-sm-12">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
    </section>
</div>
<script>
var item = $('#srv_detail_item');
var quantity = $('#srv_detail_quantity');
var current_balance  = $('#current_balance');

var srv_detail_data = {};
var number_of_line = 0;

function clear_srv_line() {
    item.val('');
    quantity.val('');
    current_balance.val('');
    document.getElementById("srv_detail_quantity").style.removeProperty('border');
    document.getElementById("srv_detail_item").style.removeProperty('border');
    item.focus();
}

function add_srv_line()
    {
        if(!validate_add_srv_line())
            return false; 
        number_of_line++;

        let selected_item = items[selected_item_array_id];
        
        let new_data = {"id":selected_item.id, "item_code":selected_item.item_code, "item_category":selected_item.item_category, "item_generic_name":selected_item.item_generic_name, "item_brand_name":selected_item.item_brand_name, "measurement":selected_item.measurement, "quantity":quantity.val()};
        
        srv_detail_data['line_'+number_of_line] = new_data;

        let new_row_html = '<tr id="srv_detail_line_tr_'+number_of_line+'">'+
                                '<td><span class="badge" style="float:right">' + selected_item.item_code + '</span><b>'+ selected_item.item_generic_name +'</b><br>' +  selected_item.item_brand_name + '</td>'+
                                '<td>'+selected_item.current_balance+'</td>'+
                                '<td>'+new_data.quantity+'</td>'+
                                '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_srv_line('+number_of_line+')"></a></td>'+
                            '</tr>';
        
        let srv_detail_line = $('#srv_detail_line');
        srv_detail_line.append(new_row_html);
        calculate_total();
        clear_srv_line();
    }
    function validate_add_srv_line(){
        let srv_detail_quantity = $('#srv_detail_quantity');
        let sel_item =  $('#srv_detail_item');
        
        if(sel_item.val() === '')
        {
            document.getElementById("srv_detail_item").style.borderColor = 'red';
            srv_detail_item.focus();
            return false;
        }
        else if(parseFloat('0'+srv_detail_quantity.val()) <= 0)
        {
            document.getElementById("srv_detail_quantity").style.borderColor = 'red';
            srv_detail_quantity.focus();
            return false;
        }
        
        return true;
    }

function remove_srv_line(line_number) {
    $('#srv_detail_line_tr_' + line_number).remove();
    delete srv_detail_data['line_' + line_number];
    calculate_total();
}

function calculate_total() {
    let srv_detail_no_of_items = $('#srv_detail_no_of_items');
    let srv_detail_total_price = $('#srv_detail_total_price');

    srv_detail_no_of_items.html(Object.keys(srv_detail_data).length);

    let total_price = 0;
    jQuery.each(srv_detail_data, function(i, val) {
        total_price += val.unit_price * val.quantity;
    });

    srv_detail_total_price.html(total_price);
}

function save_srv() {
    let srv_code = $('#srvcode').val();
    let request_date = $('#request_date').val();
    let requesting_store = 2;
    let requested_store = 1;


    $.ajax({
        url: '/inventory/store/srvsave',
        data: {
            srv_main: {
                srv_code: srv_code,
                request_date: request_date,
                requesting_store: requesting_store,
                requested_store: requested_store,
            },
            srv_detail: srv_detail_data
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            location = '/inventory/store/srv_print?srv_id='+result.value;
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}


</script>


<script>
    var selected_item_array_id = -1;
    
    function autocomplete(inp, arr) {
      /*the autocomplete function takes two arguments,
      the text field element and an array of possible autocompleted values:*/
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      inp.addEventListener("input", function(e) {
          var a, b, i, val = this.value;
          /*close any already open lists of autocompleted values*/
          closeAllLists();
          if (!val) { return false;}
          currentFocus = -1;
          /*create a DIV element that will contain the items (values):*/
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "bt_autocomplete-list");
          a.setAttribute("class", "bt_autocomplete-items bt_autocomplete");
          /*append the DIV element as a child of the autocomplete container:*/
          this.parentNode.appendChild(a);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            /*+++++++++++++++++++++++++++++++++++++++++++*/
            if ((arr[i].item_generic_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i].item_brand_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i].item_code.substr(0, val.length).toUpperCase() == val.toUpperCase()) ) {
            //if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              let inner_html = '';
              inner_html += '<span class="badge " style="float:right">' + arr[i].item_code + '</span>';
              inner_html += '<b>';
              inner_html += "<span class=\"selected\">" + arr[i].item_generic_name.substr(0, val.length) + "</span>";
              inner_html += arr[i].item_generic_name.substr(val.length);
              inner_html += '</b><br>';
              /*insert a input field that will hold the current array item's value:*/
              inner_html += arr[i].item_brand_name;
              inner_html += '<input type="hidden" value="' + i + '">';
              inner_html += '<input type="hidden" value="' + arr[i].item_brand_name + '">';
              b.innerHTML = inner_html;
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
                  /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[1].value;
                    selected_item_array_id = this.getElementsByTagName("input")[0].value;
                    let selected_item = items[selected_item_array_id];
                    document.getElementById("current_balance").value = selected_item.current_balance;
                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists();
              });
              a.appendChild(b);
            }
          }
      });
      /*execute a function presses a key on the keyboard:*/
      inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "bt_autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
              /*and simulate a click on the "active" item:*/
              if (x) x[currentFocus].click();
            }
          }
      });
      function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("bt_autocomplete-active");
      }
      function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
          x[i].classList.remove("bt_autocomplete-active");
        }
      }
      function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("bt_autocomplete-items");
        for (var i = 0; i < x.length; i++) {
          if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }
      /*execute a function when someone clicks in the document:*/
      document.addEventListener("click", function (e) {
          closeAllLists(e.target);
      });
    }
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;

    autocomplete(document.getElementById("srv_detail_item"), items);
    </script>