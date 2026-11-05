<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Inventory Item With No Location</small>
            <ol class="breadcrumb">
                
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">  
                    <div class="panel-heading">
                        <a role="button" class="btn btn-primary pull-right" onclick="print_printable_document();"><i class="fa fa-print"></i> Print</a>
                    </div>
                    <div class="panel-body" style="min-height: 700px;">
                        <div id="printable">
                            <style>
                                #inventory_data_table td{
                                    padding: 2px 5px !important;
                                }
                            </style>
                            <table class="table table-bordered table-condensed" id="inventory_data_table">
                                <thead>
                                    <tr>
                                        <th width="20">SN</th><th class="text-center" width="450">Item Description</th><th width="5">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sn = 1;
                                        foreach($items as $item){
                                            $item_name = '<b>'.$item['item_brand_name'].'</b> ';
                                            if(trim($item['item_brand_name']) != trim($item['item_generic_name']))
                                                $item_name .= $item['item_generic_name'];
                                            
                                            $item_name .= ' <span class="badge">'.$item['strength'].'</span>  <span class="badge badge-qty">'.$item['preparation'].'</span>';
                                            

                                            echo '
                                    <tr>
                                        <td>' . $sn++ . '</td>
                                        <td>' . $item_name . '</td>
                                        <td>' . $item['quantity'] . '</td>
                                    </tr>
                                            ';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<script>
    
    $(document).ready(function() {
        create_data_table();
    });

    function create_data_table(){
        var t = $('#inventory_data_table').DataTable( {
            info: false,
            ordering: false,
            paging: false,
        } );
    }


    function print_printable_document(){
        let data_table = document.getElementById('inventory_data_table_wrapper');
        data_table.childNodes[0].style.display = 'none';
        var originalContents = document.body.innerHTML;
        //$('a').removeAttr('href');
        $('#footer_signature').show();
        // data_table.childNodes[2].style.display = 'none';

        var printContents = document.getElementById('printable').innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        //create_data_table();
        $('#footer_signature').hide();
    }

    $(document).ready(function () {
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }

        var active_sub_menu = '.<?= $active_sub_menu ?? ''; ?>';
        if (active_sub_menu != '.') {
            $(active_sub_menu).addClass('active');
        }
    });
</script>
<?= view('Include/_footer') ?>
