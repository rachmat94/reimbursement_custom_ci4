<script>
    function showPreviewReimBerkas(rbKey) {
        $("#div_modal").html("");
        $("#div_script").html("");
        setOnProgress(true);
        $.ajax({
            url: `<?= base_url('reimbursement/show_preview_berkas'); ?>`,
            type: 'POST',
            data: {
                "rb_key": rbKey,
                "<?= mycsrfTokenName(); ?>": getCsrf(),
            },
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.token);
                setOnProgress(false);
                if (response.code == "success") {
                    $("#div_modal").html(response.view);
                    $("#div_script").html(response.script);
                    $("#preview_reimberkas_modal").modal("show");
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
    }
</script>