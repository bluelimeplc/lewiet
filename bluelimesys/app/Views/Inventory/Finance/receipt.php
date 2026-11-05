<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Receipt</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
    <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-body">
                        <form action="" method="GET">
                            <div class="form-group form-inline">
                                <label for="fs_no">FS/Receipt N<u>o</u>: </label>
                                <input type="text" name="fs_no" id="fs_no" class="form-control" value="<?=$fs_no?>">
                                <input type="submit" class="btn btn-success"value="Go">
                                <br>
                                <input type="radio" id="clinic" name="department" value="Hospital" <?= ($department == 'Hospital')?' checked ':'' ?>>
                                <label for="clinic">Clinic</label>
                                <br>
                                <input type="radio" id="pharmacy" name="department" value="Inventory" <?= ($department == 'Inventory')?' checked ':'' ?>>
                                <label for="pharmacy">Pharmacy</label> 

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                if(empty($attachments)){
                    if(trim($fs_no) != ''){
                        echo '
                        <div class="col-lg-6">
                            <div class="panel">
                                <div class="panel-body">There is no result for FS/Receipt N<u>o</u> <b>'.$fs_no.'</b></div>
                            </div>
                        </div>';
                    }
                }
                $sn = 0;
                foreach($attachments as $attachment){
                    $sn++;
                    echo '
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-body">
                        '.$attachment['detail_editor'].'
                        <button class="btn btn-primary pull-right" onclick="print_printable(\'printable_'.$sn.'\')">Print</button>
                        <div id="printable_'.$sn.'">
                            '.$attachment['body'].'
                        </div>
                    </div>
                </div>
            </div>
                    ';
                } 
            ?>
        </div>
    </section>
</div>
<?= view('Include/_footer') ?>