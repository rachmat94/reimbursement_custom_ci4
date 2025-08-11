<script>
    function clearFileInput(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = ''; // reset value

        }
    }

    function doRemoveSelectedUser() {
        Swal.fire({
            title: 'Are you Sure?',
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
                $("#div_selected_user").html(`<button type="button" class="btn btn-outline-dark btn-sm" onclick="showSelectUser();">Pilih User</button>`)
            }
        })
    }

    function showSelectUser() {
        $("#div_modal").html("");
        $("#div_script").html("");
        setOnProgress(true);
        $.ajax({
            url: `<?= base_url('reimbursement/show_select_user'); ?>`,
            type: 'POST',
            data: {
                "<?= mycsrfTokenName(); ?>": getCsrf(),
            },
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.token);
                setOnProgress(false);
                if (response.code == "success") {
                    $("#div_modal").html(response.view);
                    $("#div_script").html(response.script);
                    $("#select_user_modal").modal("show");
                } else {
                    showSwal("Failed", `${response.message}`, "error");
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
    } {
        const formCreateReimbursement = $("#create_reimbursement_form");
        formCreateReimbursement.submit((e) => {
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
                    let data = new FormData(formCreateReimbursement[0]);
                    $.ajax({
                        type: "post",
                        url: formCreateReimbursement.attr('action'),
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