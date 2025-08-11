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
      // showConfirmButton: false,
      // timer: 3100,
    }).then(function () {
      if (redirect != "") {
        window.location = `${redirect}`;
      }
    });
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