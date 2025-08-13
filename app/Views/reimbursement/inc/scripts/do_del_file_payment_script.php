<script>
    function doDelFilePayment(reimKey) {
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
                setOnProgress(true);
                $.ajax({
                    type: "post",
                    url: `<?= base_url('reimbursement/do_del_file_payment'); ?>`,
                    data: {
                        "reim_key": reimKey,
                        "<?= mycsrfTokenName(); ?>": getCsrf(),
                    },
                    enctype: 'multipart/form-data',
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
    }
</script>