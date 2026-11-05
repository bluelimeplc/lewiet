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
                                    <div class="form-group"  style="float:left; border-color: coral;">
                                        &emsp;
                                    </div> 
                                    <div style="height: 20mm; float:left">
                                        <img src="/img/logo-white-bg.png" width="200" class="img img-responsive" alt="" style="margin-bottom:10px">
                                        <h4> </h4>
                                    </div>
                                    <div class="form-group"  style="float:right; border-color: coral; margin-right:10mm">
                                        <span style="font-size:11px; float:right; text-align:justify">
                                            
                                        </span>
                                    </div>
                                                                        
                            </div>
                            <hr>
                                <h4 style="text-align: center;"><?=strtoupper(@$template['template_name'])?> <br><u><?=strtoupper('consent Form')?></u></h4>
                            <hr>
                            <div style="padding:20px; 20px; 20px; 20px;">
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




