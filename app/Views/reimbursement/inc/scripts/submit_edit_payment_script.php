<script>
    let imgZoomLevel = 1;

    function showImageModal(imgUrl) {
        imgZoomLevel = 1; // reset zoom setiap buka modal
        const img = document.getElementById('imagePreview');
        img.src = imgUrl;
        img.style.transform = `scale(${imgZoomLevel})`;

        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    function zoomInImage() {
        imgZoomLevel += 0.1;
        document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
    }

    function zoomOutImage() {
        if (imgZoomLevel > 0.2) {
            imgZoomLevel -= 0.1;
            document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
        }
    }

    function resetZoom() {
        imgZoomLevel = 1;
        document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
    }
</script>
<script>
    {
        const formEditPaymentReim = $("#edit_payment_reim_form");
        formEditPaymentReim.submit((e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah anda yakin data sudah benar?',
                icon: 'warning',
                showClass: {
                    popup: "animate__animated animate__fadeInDown",
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp",
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    setOnProgress(true);
                    let data = new FormData(formEditPaymentReim[0]);
                    $.ajax({
                        type: "post",
                        url: formEditPaymentReim.attr('action'),
                        data: data,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: "json",
                        beforeSend: function() {
                            // setOnProgress(true);
                        },
                        complete: function() {
                            //  setOnProgress(false);
                        },
                        success: function(response) {
                            updateCsrf(response.token);
                            setOnProgress(false);
                            redirect = response.redirect;
                            showSwal(response.code.ucfirst, response.message, response.code);
                            if (response.code == "success") {
                                setTimeout(() => {
                                    window.location = `${redirect}`;
                                }, 1000);
                            } else {

                            }
                        },
                        error: function(xhr, ajaxOptions, thrownerror) {
                            setOnProgress(false);
                            let newToken = xhr.getResponseHeader('Req_token');
                            if (newToken) {
                                updateCsrf(newToken);
                            }
                            try {
                                let errResponse = JSON.parse(xhr.responseText);
                                showSwal("Failed", `<b>[ ${xhr.status} ]</b> ${errResponse.message}`, "error");
                            } catch (e) {
                                showSwal("Failed", `<b>[ ${xhr.status} ]</b> ${xhr.statusText}`, "error");
                            }
                        }
                    });

                }
            })

        })
    }
</script>