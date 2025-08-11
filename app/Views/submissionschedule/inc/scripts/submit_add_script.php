<script>
    {
        const formEditSubmissionSchedule = $("#add_submissionschedule_form");
        formEditSubmissionSchedule.submit((e) => {
            e.preventDefault();
            setOnProgress(true);
            let data = new FormData(formEditSubmissionSchedule[0]);
            $.ajax({
                type: "post",
                url: formEditSubmissionSchedule.attr('action'),
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
                        $("#add_submissionschedule_modal").modal("hide");
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