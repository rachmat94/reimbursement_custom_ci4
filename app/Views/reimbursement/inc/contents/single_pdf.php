<style type="text/css">
    body,
    table {
        font-family: DejaVu Sans, sans-serif;
    }

    table.page_header {
        width: 100%;
        border: none;
        background-color: orange;
        padding: 2mm
    }

    table.page_footer {
        width: 100%;
        border: none;
        background-color: orange;
        padding: 2mm
    }

    /* div.note {border: solid 1mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm; width: 100%; }
    ul.main { width: 95%; list-style-type: square; }
    ul.main li { padding-bottom: 2mm; } */
    /* h1 { text-align: center; font-size: 20mm}
    h3 { text-align: center; font-size: 14mm} */


    table.item {
        width: 290mm;

        /* border: none;  */
        /* background-color: #EEEEEE;  */
        /* border-top: solid 1px black;  */
        /* padding: 2mm ; */
    }

    table.item th {
        font-size: 11pt;
        border-left: solid 1px black;
        border-bottom: solid 1px black;
        border-top: solid 1px black;
        padding-top: 6pt;
        padding-left: 6pt;
        padding-right: 6pt;
        padding-bottom: 6pt;

    }

    table.item td {
        /* border: solid black 1px; */

        /* border: solid 1px black; */
        border-left: solid 1px black;
        /* border-top:solid 1px black; */
        border-bottom: solid 1px black;
        font-size: 11pt;

        padding-top: 6pt;
        padding-left: 6pt;
        padding-right: 6pt;
        padding-bottom: 8pt;
    }

    table.jp-footer {
        width: 100%;
        margin: auto;
        border: none;
        background-color: #EEEEEE;
        border-top: solid 1mm #AAAADD;
        padding: 2mm;
    }

    table.jp-footer th {
        font-size: 11pt;
        padding-left: 6pt;
        padding-right: 6pt;
        padding-bottom: 8pt;

    }

    table.jp-footer td {
        font-size: 9pt;
        padding-left: 6pt;
        padding-right: 6pt;
        padding-bottom: 8pt;
    }

    td {
        vertical-align: top;
    }

    .clear-border {
        border: none;
    }

    /* A4 = 210mm x 297mm */
</style>
<?php
$a4Width = 210;
$a4Height = 297;
$backLeft = 10;
$backRight = 10;

$workAreaWidth = $a4Width - ($backLeft + $backRight);
$logoAreaWidth = 0;
$textHeaderWidth = $workAreaWidth - $logoAreaWidth;

$qrcodeAreaWidth = 50;
$detailAreaWidth = $workAreaWidth - $qrcodeAreaWidth;

?>
<page backtop="14mm" backbottom="14mm" backleft="<?= $backLeft; ?>mm" backright="<?= $backRight; ?>mm" style="font-size: 12pt">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width: 50%; text-align: left">
                    <?= appName(); ?>
                </td>
                <td style="width: 50%; text-align: right">
                    [ at: <?= appCurrentDateTime(); ?> | by: #<?= $sessUserId; ?> @<?= $sessUsername; ?> ]
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 33%; text-align: left;">

                </td>
                <td style="width: 34%; text-align: center">
                    page [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 33%; text-align: right">

                </td>
            </tr>
        </table>
    </page_footer>
    <br>
    <table class="" style="margin-left: 0mm;" cellspacing="0" border="0">
        <tr>
            <td style="vertical-align: top;width: 50%;text-align:left;border-bottom: solid 1px black;">
                <img src="<?= FCPATH . '/assets/img/logo.png'; ?>" style="width: 20mm; height: 20mm;">
            </td>
            <td style="vertical-align: top;width: 50%;text-align:right;border-bottom: solid 1px black;">
                <h3 style="font-size:24pt;">Reimbursement</h3>
            </td>
        </tr>
    </table>

    <table style="margin-left: 0mm; margin-top: 10mm;" cellspacing="0" border="0">
        <tr>
            <td style="vertical-align: top; width: <?= $detailAreaWidth; ?>mm; text-align: left;">
                <table class="item " width="100%" cellspacing="0" border="0" style="margin-top: 0mm;">
                    <tr>
                        <td class="clear-border" style="width: 30mm; "><b>Kode</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm;">
                            <?= $dReimbursement['reim_code']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Triwulan</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            <?= $dReimbursement["reim_triwulan_no"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Tahun</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            <?= $dReimbursement["reim_triwulan_tahun"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Group</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            [ <?= $dReimbursement["ucg_group_code"]; ?> ] <?= $dReimbursement["ucg_group_name"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Tgl. Diajukan</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            <?= appFormatTanggalIndonesia($dReimbursement["reim_diajukan_pada"]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Pemohon</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            [ <?= $dReimbursement["uc_usr_code"]; ?> ] <?= $dReimbursement["uc_usr_username"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Diajukan oleh</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            [ <?= $dReimbursement["ud_usr_code"]; ?> ] <?= $dReimbursement["ud_usr_username"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm;"><b>Validator</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            [ <?= $dReimbursement["uv_usr_code"]; ?> ] <?= $dReimbursement["uv_usr_username"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm; "><b>Category</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm;">
                            <?= "[ " . ($dReimbursement["cat_code"]) . " ] " . $dReimbursement["cat_name"]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm; "><b>Amount</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm;">
                            <?= appFormatRupiah($dReimbursement["reim_amount"]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="clear-border" style="width: 30mm; "><b>Status</b></td>
                        <th class="clear-border">:</th>
                        <td class="clear-border" style="width: <?= $detailAreaWidth - 54; ?>mm; ">
                            <?= masterReimbursementStatus($dReimbursement["reim_status"])["label"]; ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; width: <?= $qrcodeAreaWidth; ?>mm; text-align: center;">
                <qrcode value="<?= base_url('reimbursement/view?reim_key=' . $dReimbursement['reim_key']); ?>" ec="Q" style="width: <?= $qrcodeAreaWidth - 10; ?>mm; background-color: white; color: black;"></qrcode>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="vertical-align: top; width: <?= $workAreaWidth; ?>mm; text-align: left; ">&nbsp;</td>
        </tr>
    </table>


</page>