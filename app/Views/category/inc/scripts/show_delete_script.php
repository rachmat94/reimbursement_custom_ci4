<script>
    function showDeleteCategory(mptKey = "") {
        $("#div_modal").html("");
        $("#div_script").html("");
        setOnProgress(true);
        $.ajax({
            url: `<?= base_url('category/show_delete'); ?>`,
            type: 'POST',
            data: {
                "cat_key": mptKey,
                "<?= mycsrfTokenName(); ?>": getCsrf()
            },
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.token);
                setOnProgress(false);
                if (response.code == "success") {
                    $("#div_modal").html(response.view);
                    $("#div_script").html(response.script);
                    $("#delete_category_modal").modal("show");
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