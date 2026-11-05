
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>POS Machine</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/admin/">Dashboard</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="col-sm-12 col-md-12">
            <div  class="panel panel-bd panel-form">
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>POS Machine</th><th>Type</th><th>Active</th><th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($pos_machines as $pos_machine){
                                echo '
                            <tr>
                                <td>'.$pos_machine['machine_name'].'</td>
                                <td>'.$pos_machine['type'].'</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" autocomplete="off" id="active_inactive" onchange="active_inactive('.$pos_machine['id'].', this)" '.($pos_machine['active']?'checked':'').'>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>';
                                echo BluelimeSystem::print_associated_array($pos_machine['extra'],false);
                                echo '</td>
                            </tr>
                                ';
                            }
                        ?>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div> 
    </section>
</div>
<script>
    function active_inactive(id,obj){
        if(prompt("Are you sure?\nPlease enter the answer.\n5 + 2 = __") == "7"){
            $.ajax({
                url: '/hospital/admin/active_inactive_pos_machine',
                method: 'POST',
                data:{id:id, active:(obj.checked?1:0)},
                async:false,
                success:function(result){
                    ;
                }
            });
        }
        else{
            obj.checked = !obj.checked;
        }
    }
</script>