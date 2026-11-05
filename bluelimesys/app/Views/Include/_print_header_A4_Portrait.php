<?php
    $document_no = $this->data['document_no'] ?? '';
    $revision = $this->data['revision'] ?? 1;
    $document_title = $this->data['document_title'] ?? '';
    $document_date = $this->data['document_date'] ?? '';
    $ordered_by = $this->data['ordered_by'] ?? '';
    $recieved_by = $this->data['recieved_by'] ?? '';
    $footer1 = $this->data['footer1'] ?? '';
    $footer2 = $this->data['footer2'] ?? '';
    $registered_by = $this->data['registered_by'] ?? '';
    $company_header = \BluelimeSystem::get_company_header();
?>
<div id="printable">
    <style>
    .print-page-a4-portrait {
        width: 210mm;
        background-color: #fff;
        padding: 20px;
        overflow: hidden;
    }
    
    .print-header-company-name{
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        background-color: #eee;
    }
    .print-header-document-title{
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        height: 40px;
        padding: 0px;
    }
    .print-header-lable {
        text-align: left;
        width: 100px;
        float: left;
        font-size: 10px;
        font-weight: normal;
        margin-top: 1px;
        margin-left: 1px;
        margin-right: -120px;
    }
    .print-header-address span{
        margin-left: 20px;
        display: inline-block;
        font-size: 12px;
    }
    
    .print-footer{
        width: 210mm;
        text-align: center;
        font-size: 12px;
        color: #aaa;
        margin-top: -50px;
    }
    .header td{
        border: 2px solid black;
    }
    .data td{
        border: 0.2px solid black;
    }
    .data th{
        border: 0.2px solid black;
    }
    
    .data thead{
        border: 0.2px solid black;
    }
    .data tr{
        border: 0.2px solid black;
    }
    .title td{
        border: 0.2px;
    }
    
</style>
<div class="print-page-a4-portrait">
    <table style="margin-bottom: 2px;width:200mm;padding: 20px;" class="header">
        <tr>
            <td rowspan="2" width="150" height="100">
            <?php echo '<img  class="img img-responsive" width="200"  style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode( $company_header['logo_printouts'] ).'"/>' ?>
            </td>
            <td class="print-header-company-name" height="50">
                <div class="print-header-lable">Company Name:</div>
                <?=$company_header['company_name']?>
            </td>
            <td width="200"  align="center">
                <div class="print-header-lable">Document N<u>o</u>:</div><br>
                <span><?= $document_no ?></span>
            </td>
        </tr>
        <tr>
            <td class="print-header-document-title" height="50">
                <div class="print-header-lable">Document Title:</div>
                <?= $document_title ?>
            </td>
            <td align="center">
                <div class="print-header-lable">Revision: <?= $revision ?></div><br>
                <span><?= $document_date ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="print-header-address">
            <span><b>Address: </b><?=$company_header['location']?></span>
                <span><b>Tel</b> <?=$company_header['tel']?></span>
                <span><b>Mob</b> <?=$company_header['mobile']?> </span>
                <span><b>Email</b> <?=$company_header['email']?></span>
            </td>
        </tr>
    </table>
    <script>
    function print_printable_document(){
        var originalContents = document.body.innerHTML;
        $('a').removeAttr('href');
        var printContents = document.getElementById('printable').innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>

