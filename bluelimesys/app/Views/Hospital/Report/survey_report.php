<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Hospital/Admin/_left_sidebar_admin') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Admin</h1>
            <small>Survey Report</small>
            <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-9">
                                    <form action="" method="GET">
                                        <div class=" col-lg-3" style="margin-bottom: 2px;">
                                            <div class="input-group">
                                                <span class="input-group-addon" ><b>From</b></span>
                                                <input type="date" class="form-control" value="<?= $from ?>" name="from">
                                            </div>
                                        </div>
                                        <div class=" col-lg-3">
                                            <div class="input-group">
                                                <span class="input-group-addon" ><b>To</b></span>
                                                <input type="date" class="form-control" value="<?=$to?>"  name="to">    
                                            </div>
                                            <input type="submit" style="visibility:hidden">
                                        </div>
                                    <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                                    </form>
                                </div>
                                <div class="col-lg-2">
                                    <table class="table table-bordered">
                                        <?php
                                            $head = '<tr>';
                                            //$indicator = '<tr>';
                                            foreach($api_connection as $api){
                                                $head .='<th>
                                                            <i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i> '.ucfirst($api['branch']).'</th>';
                                                //$indicator .= '<td><i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i></td>';
                                            }
                                            $head .= '</tr>';
                                            //$indicator .='</tr>';
                                            echo $head;
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Survey report</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php
                                        $branches = array_column($api_connection,'branch');
                                    ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Survey</th>
                                                <?php
                                                    foreach($branches as $branch){
                                                        echo '<th>'.$branch.'</th>';
                                                    }
                                                ?>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach($survey_report as $survey => $report){
                                                    echo '<tr>';
                                                    echo '<td>'.$survey.'</td>';
                                                    foreach($branches as $branch){
                                                        echo '<td>'.($report[$branch] ?? '').'</td>';
                                                    }
                                                    echo '<td>'.($report['_total_']??'').'</td>';
                                                    echo '</tr>';
                                                }
                                            ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>


</script>

<?= view('Include/_footer') ?>