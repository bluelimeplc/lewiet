<?php
    $document_no = $this->data['document_no'] ?? '';
    $document_title = $this->data['document_title'] ?? '';
    $document_date = $this->data['document_date'] ?? '';
    $company_header = \BluelimeSystem::get_company_header();
    $footer1 = $this->data['footer1'] ?? '';
    $footer2 = $this->data['footer2'] ?? '';
    $recieved_by = $this->data['recieved_by'] ?? '';
?>
<div id="printable">
    <style>
    .print-page-a4-landscape {
        width: 297mm;
        background-color: #fff;
        padding: 50px;
        overflow: hidden;
    }
    .print-header-company-name{
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        background-color: #eee;
    }
    .print-header-document-title{
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        height: 50px;
        padding: 0px;
    }
    .print-header-lable {
        text-align: left;
        width: 100px;
        float: left;
        font-size: 10px;
        font-weight: normal;
        margin-top: -5px;
        margin-left: -5px;
        margin-right: -120px;
    }
    .print-header-address span{
        margin-left: 60px;
        display: inline-block;
        font-size: 12px;
    }
    .print-signature .signature{
        height: 40px;
    }
    .print-signature .signee{
        text-align: center;
        font-weight: bold;
        border-top: solid 1px;
    }
    .print-footer{
        width: 297mm;
        text-align: center;
        font-size: 12px;
        color: #aaa;
        margin-top: -50px;
    }
    
</style>
<div class="print-page-a4-landscape">
    <table class="table table-bordered">
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
                <div class="print-header-lable">Revision: 1</div><br>
                <b><?= $document_date ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="print-header-address">
            <span><b>Address: </b><?=$company_header['location']?></span>
                <span><b>Tel</b> <?=$company_header['tel']?></span>
                <span><b>Mob</b> <?=$company_header['mobile']?> </span>
                <span><b>Email</b> <?=$company_header['email']?></span>
                <span><b>Web</b> <?=$company_header['website']?></span>
            </td>
        </tr>
    </table>

