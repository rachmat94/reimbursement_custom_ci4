<script>
    function getCsrf() {
        return $("[name='<?= mycsrfTokenName(); ?>']").val();
    }

    function updateCsrf(newToken) {
        $("[name='<?= mycsrfTokenName(); ?>']").val(newToken);
    }

    // function showSwal(title = "", html = "", icon = "", redirect = "") {
    //     if (icon == "") {
    //         icon = "info";
    //     }
    //     if (icon == "danger") {
    //         icon = "error";
    //     }
    //     Swal.fire({
    //         title: title,
    //         html: html,
    //         icon: icon,
    //         showClass: {
    //             popup: 'animate__animated animate__fadeInDown'
    //         },
    //         hideClass: {
    //             popup: 'animate__animated animate__fadeOutUp'
    //         }
    //         // showConfirmButton: false,
    //         // timer: 3100,
    //     }).then(function() {
    //         if (redirect != "") {
    //             window.location = `${redirect}`;
    //         }

    //     });
    // }

    // function setOnProgress(status = true) {
    //     let pre = `
    //     <div id="process_overlay" class="preloader flex-column justify-content-center align-items-center" style="background-color: rgba(0, 0, 0, .6);">
    //     <p class="text-white">
    //     on Progress...
    //     </p>
    //     </div>
    // `;
    //     if (status) {
    //         $("body").append(pre);
    //     } else {
    //         $("#process_overlay").remove();
    //     }
    // }

    $("body").ready(function() {
        <?php
        if ((session("__swal__") != null)) {
            $dSwal = session("__swal__");
            echo appRenderSwal($dSwal["code"], $dSwal["dSwal"]["title"], $dSwal["message"]);
        }
        ?>
        $(".select2").select2();
    })
</script>