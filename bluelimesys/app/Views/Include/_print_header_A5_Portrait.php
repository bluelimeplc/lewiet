<?php
    $document_no = $this->data['document_no'] ?? '';
    $document_title = $this->data['document_title'] ?? '';
    $document_date = $this->data['document_date'] ?? '';
    $footer1 = $this->data['footer1'] ?? '';
    $footer2 = $this->data['footer2'] ?? '';
    $registered_by = $this->data['registered_by'] ?? '';
    $company_header = $company_header ?? \BluelimeSystem::get_company_header();
?>
<div id="printable">
    <link href="/css/print_a5_style.css" rel="stylesheet" type="text/css"/>
    <div class="print-page-a5-portrait">
        <table class="attachment_table">
            <tr>
                <td rowspan="2" width="100" height="70">
                    <?php echo '<img  class="img img-responsive" width="200"  style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode( $company_header['logo_printouts'] ).'"/>' ?>
                </td>
                <td class="print-header-company-name" height="50">
                    <div class="print-header-lable">Company Name:</div>
                    <?=$company_header['company_name']?>
                </td>
                <td width="100"  align="center">
                    <div class="print-header-lable">Document N<u>o</u>:</div><br>
                    <span><?= $document_no ?></span>
                </td>
            </tr>
            <tr>
                <td class="print-header-document-title" height="50">
                    <div class="print-header-lable">Document Title:</div>
                    <span><?= $document_title ?></span>
                </td>
                <td align="center">
                    <div class="print-header-lable">Revision: 1</div><br>
                    <span style="font-size: x-small;"><?= $document_date ?><span>
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

