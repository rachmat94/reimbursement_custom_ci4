<script>
    $('#togglePassword').click(function() {
        const passwordField = $('#txt_password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
    {
        const formChangePassword = $("#change_password_form");
        formChangePassword.submit((e) => {
            e.preventDefault();
            setOnProgress(true);
            let data = new FormData(formChangePassword[0]);
            $.ajax({
                type: "post",
                url: formChangePassword.attr('action'),
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
                    redirect = response.redirect;
                    setOnProgress(false);
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

        })

    }
</script>