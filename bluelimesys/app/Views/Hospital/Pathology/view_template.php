<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1><?php echo lang('Reception')?></h1>
            <small><?php echo lang('Receipt')?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/receivedbills"><?php echo "Recieved Bills"?></a></li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content"  style="min-height: 297mm; width: 210mm;">
    
 


    <div class="row">

        <div  style="min-height: 297mm; width: 210mm;">
            <div class="panel panel-bd">
                <div id="printableArea">
                    <div class="panel-body">
                            <div class="row">
                            <?php
            $print_data = [
                'document_no' => $template['id'] ?? 0,
                'document_title' => 'የባዮፕሲ ውጤት<br>Biopsy Result',
                'document_date' => $template['created_at'] ?? 0,
                'footer1' => 'Recieved By',
                'footer2' => 'Authorized By'
            ];
        ?>
                            <?= view('Include/_print_header_A4_Portrait', $print_data) ?>  
                                                                        
                            </div>
                            
                            <div style="padding:20px;">
                                <p>
                                    <?=($template['template_content'])?>
                                </p>
                            </div>
                    </div>
                            <div style="padding-left:30px;width:90%;text-align:left;border-top:0px solid #000;margin-top: 100px;font-weight: bold;">
                                    <?php echo "Patient Name: "?> <u><?php echo @$_GET['pname'];?></u> &emsp;&emsp;
                                    <?php echo 'Signature: '?><u>&emsp;&emsp;&emsp;&emsp;</u> &emsp;&emsp; 
                                    <?php echo 'Date: '?><u><?= date('d-m-Y');?></u>
                                        
                            </div>
                            <div style="color: #000; text-align:center; font-size: x-small;">
                            <hr>
                                Developed by Bluelime Technologies bluelimeplc@gmail.com 
                            </div>
                </div>

                     <div class="panel-footer text-left">
                        <a class="btn btn-info" href="#" onclick="printContent('printableArea')"><span class="fa fa-print"></span></a>
                        <a class="btn btn-info" href="#" onclick="printContent('printableArea')"><span class="fa fa-telegram"></span></a>
                    </div>
                </div>
            </div>
        </div>            
    </section>
</div>


<script type="text/javascript">
    //print a div
    function printContent(el){
        var restorepage  = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
        location.reload();
    }
</script>




