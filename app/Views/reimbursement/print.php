<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pengajuan</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= assetUrl(); ?>css/adminlte.min.css">
</head>

<body>
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h2 class="page-header">
                        <i class="fas fa-globe"></i> Reimbursement
                        <small class="float-right"></small>
                    </h2>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info mt-4">
                <div class="col-sm-4 invoice-col">
                    Pemohon
                    <address>
                        <strong><?= $dReimbursement["uc_usr_username"]; ?></strong><br>
                        Kode User: <?= $dReimbursement["uc_usr_code"]; ?><br>
                        Email: <?= $dReimbursement["uc_usr_email"]; ?>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Diajukan Oleh
                    <address>
                        <strong><?= $dReimbursement["ud_usr_username"]; ?></strong><br>
                        Kode User: <?= $dReimbursement["ud_usr_code"]; ?><br>
                        Email: <?= $dReimbursement["ud_usr_email"]; ?>
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    Divalidasi Oleh
                    <address>
                        <strong><?= $dReimbursement["uv_usr_username"]; ?></strong><br>
                        Kode User: <?= $dReimbursement["uv_usr_code"]; ?><br>
                        Email: <?= $dReimbursement["uv_usr_email"]; ?>
                    </address>
                </div>
                <!-- /.col -->

                <!-- /.col -->
            </div>
            <!-- /.row -->


            <table class="table table-sm border mt-3">
                <tr>
                    <th style="width: 160px;">Kode</th>
                    <th style="width: 30px;">:</th>
                    <td><?= $dReimbursement["reim_code"]; ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Tanggal Diajukan</th>
                    <th style="width: 30px;">:</th>
                    <td><?= appFormatTanggalIndonesia($dReimbursement["reim_diajukan_pada"]); ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Triwulan</th>
                    <th style="width: 30px;">:</th>
                    <td><?= $dReimbursement["reim_triwulan_no"]; ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Tahun</th>
                    <th style="width: 30px;">:</th>
                    <td><?= $dReimbursement["reim_triwulan_tahun"]; ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Group</th>
                    <th style="width: 30px;">:</th>
                    <td><?= "[ " . $dReimbursement["ucg_group_code"] . " ] " . $dReimbursement["ucg_group_name"]; ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Nominal</th>
                    <th style="width: 30px;">:</th>
                    <td><?= appFormatRupiah($dReimbursement["reim_amount"], true); ?></td>
                </tr>
                <tr>
                    <th style="width: 160px;">Kategori</th>
                    <th style="width: 30px;">:</th>
                    <td><?= "[ " . $dReimbursement["cat_code"] . " ] " . $dReimbursement["cat_name"]; ?></td>
                </tr>
            </table>


        </section>
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
    <!-- Page specific script -->
    <script>
        window.addEventListener("load", window.print());
    </script>
</body>

</html>