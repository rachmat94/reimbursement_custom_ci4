function showToastr(code = "info", msg = "") {
  if (code == "danger") {
    code = "error";
  }
  if (code == "success") {
    toastr.success(msg);
  } else if (code == "warning") {
    toastr.warning(msg);
  } else if (code == "error") {
    toastr.error(msg);
  } else {
    toastr.info(msg);
  }
}

/**
 * ===============================================================
 * fireToast
 * icon = success,info,error,warning,question
 * position = "top","top-start","top-end","top-left","top-right","center","center-start","center-end","center-left","center-right","bottom","bottom-start","bottom-end","bottom-left","bottom-right",
 */

function fireToast(title = "[Info]", icon = "info", position = "top-end") {
  if (icon == "danger") {
    icon = "warning";
  }
  if (icon == "help") {
    icon = "question";
  }

  let Toast = Swal.mixin({
    toast: true,
    position: position,
    showConfirmButton: false,
    timer: 5000,
  });

  Toast.fire({
    icon: icon,
    title: title,
  });
}

// fireToast("info", "info");
// end fireToast ================================================

/**
         * ==============================================================
         * showToasts
         * Position: bottomLeft,bottomRight,topLeft,
         * 
         * 
         * //  class: 'bg-success',
          //   fixed: false,
          //   autohide: true,
          //   delay: 750,
          //   position: 'topLeft',
          //   icon: 'fas fa-envelope fa-lg',
          //   image: '<?= appTemplateURL(); ?>dist/img/user3-128x128.jpg',
          //   imageAlt: 'User Picture',
          //   title: 'Toast Title',
          //   subtitle: 'Subtitle',
          //   body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'

         */
function showToasts(
  options = {
    title: "Title",
    body: "Body",
    icon: "fas fa-envelope fa-lg",
  }
) {
  $(document).Toasts("create", options);
}

// showToasts();
// end showToast =================================================

/**
 * ===============================================================
 * simple swal
 * showSwal
 * icon= info,success,error,warning,question
 */
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
    showClass: {
      popup: "animate__animated animate__fadeInDown",
    },
    hideClass: {
      popup: "animate__animated animate__fadeOutUp",
    },
    // showConfirmButton: false,
    // timer: 3100,
  }).then(function () {
    if (redirect != "") {
      window.location = `${redirect}`;
    }
  });
}

function setOnProgress(status = true, title = "") {
  if (title != "") {
    add = `
        <p class="text-white fw-bold h6">
          ${title}
        </p>
        `;
  } else {
    add = "";
  }
  let pre = `
    <div id="process_overlay" class="preloader flex-column justify-content-center align-items-center" style="background-color: rgba(0, 0, 0, .6); position: fixed; inset: 0; z-index: 9999;">
      <div class="text-center">
        <div class="spinner-border text-light mb-3" role="status"></div>
        ${add}
        <p class="text-white fw-bold fs-5">
          Please wait, processing your request...
        </p>
        
      </div>
    </div>
  `;
  if (status) {
    $("body").append(pre);
  } else {
    $("#process_overlay").remove();
  }
}

function clearInputFile(selector = "") {
  if (selector != "") {
    $(selector).val("");
  }
}

function removePreviewImage(
  inputFileId = "#file_pic",
  container = "#file_container"
) {
  $("#" + inputFileId).val("");
  $(`${container}`).html("");
}

function previewImage(input, container = "#file_container") {
  if (input.files && input.files[0]) {
    let inputId = input.getAttribute("id");
    var reader = new FileReader();
    reader.onload = function (e) {
      $(`${container}`)
        .html(`<img class="img-thumbnail mt-2" id="view_image" src="${e.target.result}"  alt="" style="width:80%;">
    <br>
    <span onclick="removePreviewImage('${inputId}','${container}');" class="btn btn-sm btn-danger mt-3">Remove</span>`);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(
    function () {
      showToastr("success", "Copied to clipboard");
    },
    function (err) {
      console.error("Could not copy text: ", err);
      showToastr("danger", "Could not copy");
    }
  );
}

$(".select2").select2({
  width: "100%",
  placeholder: "-",
  allowClear: true,
});
