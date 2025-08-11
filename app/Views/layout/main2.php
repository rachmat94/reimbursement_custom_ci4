<?php
try {
?>

    <?php
    if (!isset($dSession) || $dSession == null) {
        $dSession =  authVerifyAccess();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        if (isset($title) && $title != "") {
            echo "<title>" . ucwords($title) . " :: " . appName() . "</title>";
        } else {
            echo "<title>" . appName() . "</title>";
        }
        ?>
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/toastr/toastr.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?= assetUrl(); ?>css/adminlte.min.css">
        <?= appViewInjectHead("layout", "fixed_datatable_head"); ?>
        <style>
            .card {
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                /* border: 1px solid rgb(16, 17, 17); */
            }

            .card-footer {
                border-bottom-left-radius: 20px;
                border-bottom-right-radius: 20px;
                padding-bottom: 20px;
                padding-top: 20px;
            }

            .btn-rounded {
                padding-left: 20px;
                padding-right: 20px;
                padding-top: 6px;
                padding-bottom: 6px;
                border-radius: 16px;
            }
        </style>
        <style>
            /* Gaya khusus untuk tombol halaman yang sedang aktif */
            .dataTables_paginate .page-item.active .page-link {
                background-color: #2b2b2b;
                color: #fff;
                border-color: #444;
            }
        </style>
        <?= $this->renderSection("head"); ?>
    </head>

    <!-- <body class="hold-transition  sidebar-collapse layout-top-nav layout-navbar-fixed layout-fixed"> -->

    <body class="hold-transition sidebar-mini layout-navbar-fixed layout-fixed  text-sm">
        <div class="wrapper">

            <!-- Navbar -->
            <!-- <nav class="main-header navbar navbar-expand-sm navbar-light navbar-white elevation-2 border-bottom-0"> -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <?= appViewInjectContent("layout", "navbar_main2", ["dSession" => $dSession]); ?>
            </nav>
            <!-- /.navbar -->


            <!-- Main Sidebar Container -->
            <!-- <aside class="main-sidebar sidebar-dark-primary elevation-4"> -->
            <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="<?= base_url(); ?>" class="brand-link">
                    <img src="<?= assetLogo(); ?>" alt="Logo" class="brand-image" style="background-color: white;">
                    <span class="brand-text "><?= appName(); ?></span>
                    <span class="badge badge-primary" style="font-size: 8pt;"><?= appVersion(); ?></span>
                </a>

                <!-- Sidebar -->
                <?= appViewInjectContent("layout", "sidebar_main2"); ?>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper pt-3 pb-5 text-sm">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container">
                        
                        <?php
                        $isHideHeader = $hideHeader ?? false;
                        if (!($isHideHeader)) {
                        ?>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class=""><?= (($header ?? "") == "") ? appName() : $header; ?></h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <?php
                                        // <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        // <li class="breadcrumb-item"><a href="#">Layout</a></li>
                                        // <li class="breadcrumb-item active">Top Navigation</li>

                                        ?>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <?= $this->renderSection("header"); ?>
                        <?php
                        }
                        ?>

                        <div class="row">
                            <div class="col-12">
                                <?php if ($result = session()->getFlashdata('alert')): ?>
                                    <?= lteAlert($result["code"], $result["message"], [
                                        "disableTitle" => true,
                                    ]); ?>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <div class="content">
                    <div class="container">

                        <?= $this->renderSection("content"); ?>

                        <?= $this->renderSection("modal"); ?>
                    </div><!-- /.container-fluid -->
                    <div id="div_modal"></div>
                </div>
                <!-- /.content -->
                <a id="back-to-top" href="#" class="btn bg-white border border-dark elevation-3 back-to-top mb-5 " role="button" aria-label="Scroll to top">
                    <i class="fas fa-chevron-up"></i>
                </a>
            </div>
            <!-- /.content-wrapper -->

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->

            <!-- Main Footer -->
            <footer class="main-footer text-sm">
                <?= $this->renderSection("footer"); ?>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-right d-none d-sm-inline">
                                <?= appVersion(); ?>
                            </div>

                            &copy; <?= date("Y"); ?> <a href="<?= base_url(); ?>" class="text-dark "><?= appName(); ?></a>
                        </div>
                    </div>
                </div>

            </footer>
        </div>
        <!-- ./wrapper -->
        <?= mycsrfTokenField(); ?>

        <!-- REQUIRED SCRIPTS -->

        <!-- jQuery -->
        <script src="<?= assetUrl(); ?>plugins/jquery/jquery.min.js"></script> <!-- Bootstrap 4 -->
        <script src="<?= assetUrl(); ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/moment/moment.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/toastr/toastr.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/select2/js/select2.full.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/inputmask/jquery.inputmask.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/daterangepicker/daterangepicker.js"></script>
        <script src="<?= assetUrl(); ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="<?= assetUrl(); ?>plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js"></script>
        <script src="<?= assetUrl(); ?>js/adminlte.min.js"></script>
        <script src="<?= assetUrl(); ?>js/appjs.js"></script>
        <?= $this->renderSection("script_0"); ?>
        <?= appViewInjectScript("layout", "app_script"); ?>
        <?= $this->renderSection("script_1"); ?>
        <div id="div_script"></div>
    </body>

    </html>
<?php

} catch (\Throwable $th) {
    echo $th->getMessage();
}
?>