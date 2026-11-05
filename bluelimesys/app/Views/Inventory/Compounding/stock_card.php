<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Compounding</h1>
            <small>Stock Card</small>

        </div>
    </section>


    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                
                <div class="panel panel-bd panel-form">

                        <div class="panel-body">
                            <div class="portlet-body form">
                                <?php
                                    helper('form');
                                    $attributes = array('class' => 'form-horizontal','role'=>'form');
                                    echo form_open_multipart('/inventory/compounding/stock_card', $attributes);
                                ?>
                                <div class="col-sm-3">
                                    <label for="stock_item">Select Item</label>
                                    <input autocomplete="off" type="text" class="form-control" id="stock_item" name="stock_item" style="width:350px">
                                    <input type="hidden" class="form-control" id="stock_item_id" name="stock_item_id">
                                </div>
                                <div class="col-sm-2">
                                    <label for="stock_item">&nbsp;</label>
                                    <button type="submit" class="form-control btn btn-success">Search</button>
                                </div>
                                <?php
                                    echo form_close();
                                ?>
                            </div>


                        </div>

                    </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-body">
                        <table class="table table-bordered">
                    <tr>
                        <th>
                            Item Code: &nbsp;&emsp;&emsp;&emsp;&emsp;<?=@$transactions[0]['item_code']?><br>
                            Item Generic Name: <?=@$transactions[0]['item_generic_name']?><br>
                            Item Brand Name:&emsp;<?=@$transactions[0]['item_brand_name']?>
                        </th>
                        <th>Store: <?=@$transactions[0]['store_name'] ?></th></tr>
                        </table>   
                        <table class="table table-bordered" id="report_table">
                            <thead>
                                <tr>
                                    <th class="all"><?php echo "Reference"; ?></th>
                                    <th class="all"><?php echo "Transaction Type"; ?></th>
                                    <th class="all"><?php echo "Transaction Date"; ?></th>
                                    <th class="all"><?php echo "Batch No"; ?></th>
                                    <th class="all"><?php echo "Quantity"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                        $sn = 0;
                                        foreach($transactions as $transaction)
                                        {
                                            $sn++;
                                            echo '
                                                <tr>
                                                    <td>'.$transaction['transaction_reference'].'</td>
                                                    <td>'.$transaction['transaction_type'].'</td>
                                                    <td>'.date('Y-m-d', strtotime($transaction['created_at'])).'</td>
                                                    <td>'.$transaction['batch_number'].'</td>
                                                    <td>'.$transaction['quantity'].'</td>
                                                   
                                                </tr>
                                            ';
                                        }
                                ?>
                                <tr><td colspan="4" style="text-align: right; font-weight: bolder;">Balance</td><td style="font-weight: bolder;"><?=$sum['sum']?></td></tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>
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
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "bt_autocomplete-list");
        a.setAttribute("class", "bt_autocomplete-items bt_autocomplete");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        $.each(arr, function(i,arr1){
        //for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            /*+++++++++++++++++++++++++++++++++++++++++++*/
            if ((arr[i].item_generic_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i]
                    .item_brand_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i]
                    .item_code.substr(0, val.length).toUpperCase() == val.toUpperCase())) {
                //if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                let inner_html = '';
                inner_html += '<span class="badge " style="float:right">' + arr[i].item_code + '</span>';
                inner_html += '<b>';
                inner_html += "<span class=\"selected\">" + arr[i].item_generic_name.substr(0, val.length) +
                    "</span>";
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
                    document.getElementById("stock_item_id").value = selected_item.id;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
            }
        });
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
    document.addEventListener("click", function(e) {
        closeAllLists(e.target);
    });
}
var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;

autocomplete(document.getElementById("stock_item"), items);
</script>