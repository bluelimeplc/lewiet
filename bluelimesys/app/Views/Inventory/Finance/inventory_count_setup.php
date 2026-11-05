<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-credit"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Inventory / Inventory Count Setup</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <h4>New Inventory Count Setup</h4>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="/inventory/finance/add_inventory_count_setup">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Title</th><th>Is Year End</th><th>From</th><th>To</th>
                                </tr>
                                <tr>
                                    <td><input type="text" name="title" class="form-control" required></td>
                                    <td><input type="checkbox" name="is_year_end" id="is_year_end" onchange="is_year_end_changed(this)"></td>
                                    <td><input type="date" name="from_date" class="form-control" id="from_date" disabled required></td>
                                    <td><input type="date" name="to_date" class="form-control" id="to_date" disabled required></td>
                                    <td><button class="btn btn-success">Add</button></td>
                                </tr>

                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" >
                <div class="panel panel-bd"  style="min-height: 550px;">
                    <div class="panel-heading">
                        <h4>Inventory Count Setup List</h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-8">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Created Date</th><th>Title</th><th colspan="2">Year End</th><th>Enable Data Entry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($inventory_count_setups as $inventory_count_setup){
                                            echo '
                                    <tr>
                                        <td>'.substr($inventory_count_setup['created_at'],0,10).'</td>
                                        <td>'.$inventory_count_setup['title'].'</td>
                                        <td'.(!$inventory_count_setup['is_year_end']?' colspan="2" ':'').'>'.($inventory_count_setup['is_year_end']?$inventory_count_setup['from_date'].'<br>'.$inventory_count_setup['to_date'].'</td><td>'.(!$inventory_count_setup['year_end_committed']?'<a role="button" class="btn btn-danger" onclick="create_begining_balance(\''.$inventory_count_setup['id'].'\')">Create begining balance<br>for next year</a>':'<i class="fa fa-2x fa-check" title="Done"></i>'):'').'</td>
                                        <td> ';
                                            if(!$inventory_count_setup['year_end_committed']){
                                                echo '
                                                <label class="switch">
                                                    <input type="checkbox"  id="active_inactive" onchange="active_inactive('.$inventory_count_setup['id'].',this)" '.($inventory_count_setup['active']?'checked':'').'>
                                                    <span class="slider round"></span>
                                                </label>';
                                            }

                                            echo '
                                        </td>
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

    function create_begining_balance(inventory_count_setup_id){
        if(confirm('Are you sure?')){
            let x = parseInt(Math.random()*10);
            let y = parseInt(Math.random()*10);
            let z = prompt('Please answer this simple math.\n'+x+' + '+y+' = ');
            if(z == x+y)
                location = '/inventory/finance/create_begining_balance_for_next_year?inventory_count_setup_id='+inventory_count_setup_id;
        }
    }

    function active_inactive(id,obj){
        $.ajax({
            url: '/inventory/finance/inventory_count_setup_active_inactive',
            method: 'POST',
            data:{id:id, active:(obj.checked?1:0)},
            async:false,
            success:function(result){
                ;
            }
        });
    }

    function is_year_end_changed(chk_obj){
        let from_date = $('#from_date');
        let to_date = $('#to_date');

        if(chk_obj.checked){
            from_date.prop('disabled',false);
            to_date.prop('disabled',false);
        }
        else{
            from_date.prop('disabled',true);
            to_date.prop('disabled',true);
        }
    }
</script>
<?= view('Include/_footer') ?>
