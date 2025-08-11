<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (isset($title) && $title != "") {
        echo "<title>" . ucwords($title) . " :: " . appName() . "</title>";
    } else {
        echo "<title>" . appName() . "</title>";
    }
    ?>
    <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= assetUrl(); ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= assetUrl(); ?>css/adminlte.min.css">
    <style>
        .auth-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            width: 400px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 1);
            /*            border: 1px solid black;*/
            border-radius: 20px;
            /*            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);*/
        }

        .auth-side {
            background-color: rgba(255, 255, 255, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-size: 20px;
            text-align: center;
            padding: 20px;
            padding-left: 40px;
            margin-left: -20px;
        }

        .auth-wrapper {
            background-color: rgba(255, 255, 255, 1);
            display: flex;
            width: 80%;
            max-width: 900px;
            /*            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);*/
            border-radius: 20px;
            overflow: hidden;
        }

        .hidden {
            display: none;
        }
    </style>

    <style>
        /* html {
    height: 100%;
  } */

        /* body {
    margin: 0;
  } */

        .bg {
            animation: slide 3s ease-in-out infinite alternate;
            background-image: linear-gradient(-60deg, #232323 50%,rgb(49, 129, 235) 50%);
            bottom: 0;
            left: -50%;
            opacity: .5;
            position: fixed;
            right: -50%;
            top: 0;
            z-index: -1;
        }

        .bg2 {
            animation-direction: alternate-reverse;
            animation-duration: 4s;
        }

        .bg3 {
            animation-duration: 5s;
        }

        .bg4 {
            animation-duration: 6s;
        }

        .content {
            background-color: rgba(255, 255, 255, .8);
            border-radius: .25em;
            box-shadow: 0 0 .25em rgba(0, 0, 0, .25);
            box-sizing: border-box;
            left: 50%;
            padding: 10vmin;
            position: fixed;
            text-align: center;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        h1 {
            font-family: monospace;
        }

        h2 {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        }

        @keyframes slide {
            0% {
                transform: translateX(-25%);
            }

            100% {
                transform: translateX(25%);
            }
        }

        .card:hover {
            /* transform: scale(1.03); */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            /* z-index: 1; */
            border: 1px solid #343a40;
        }
    </style>
</head>

<body class="hold-transition ">
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="bg bg4"></div>
    <div class="auth-container ">
        <div class="auth-wrapper elevation-4">
            <!-- Form Auth (Sisi Kiri) -->
            <div class="auth-box">
                <?= $this->renderSection("header"); ?>

                <?php if ($result = session()->getFlashdata('alert')): ?>
                    <?= lteAlert($result["code"], $result["message"], [
                        "disableTitle" => true,
                    ]); ?>
                <?php endif; ?>

                <?= $this->renderSection("content"); ?>
                <div class="row">
                    <div class="col-12 text-center mt-3">
                        <a href="<?= base_url(); ?>" class="text-dark">Home page <i class="fas fa-arrow-right ml-2"></i> </a>
                    </div>

                </div>
            </div>

            <!-- Gambar/Keterangan (Sisi Kanan) -->
            <div class="auth-side w-50 d-none d-md-flex">
                <h2><?= appName(); ?><sub><?= appVersion(); ?></sub></h2>
            </div>
        </div>
    </div>
    <?= mycsrfTokenField(); ?>

    <script src="<?= assetUrl(); ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= assetUrl(); ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= assetUrl(); ?>js/adminlte.min.js"></script>
    <script src="<?= assetUrl(); ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <?= $this->renderSection("script_0"); ?>
    <script>
        function getCsrf() {
            return $("[name='<?= mycsrfTokenName(); ?>']").val();
        }

        function updateCsrf(newToken) {
            $("[name='<?= mycsrfTokenName(); ?>']").val(newToken);
        }

        function setOnProgress(status = true) {
            let pre = `
        <div id="process_overlay" class="preloader flex-column justify-content-center align-items-center" style="background-color: rgba(0, 0, 0, .6);">
        <p class="text-white">
        on Progress...
        </p>
        </div>
    `;
            if (status) {
                $("body").append(pre);
            } else {
                $("#process_overlay").remove();
            }
        }

        function showSwal(title = "", html = "", icon = "", redirect = "") {
            if (icon == "") {
                icon = "info";
            }
            if (icon == "danger") {
                icon = "error";
            }
            Swal.fire({
                title: title,
                html: html,
                icon: icon,
                // showConfirmButton: false,
                // timer: 3100,
            }).then(function() {
                if (redirect != "") {
                    window.location = `${redirect}`;
                }
            });
        }
        $("body").ready(function() {
            <?php
            if ((session("__swal__") != null)) {
                $dSwal = session("__swal__");
                echo appRenderSwal($dSwal["code"], $dSwal["dSwal"]["title"], $dSwal["message"]);
            }
            ?>
        })
    </script>


    <div id="div_script"></div>
    <?= $this->renderSection("script_1"); ?>
</body>

</html>