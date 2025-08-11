<script>
    {
        const dropArea = $("#drop-area");
        const photoInput = $("#file_photo");
        const preview = $("#preview");
        let selectedFile = null;
        // Drag Events
        dropArea.on("dragover", (e) => {
            e.preventDefault();
            dropArea.addClass("dragover");
        });
        dropArea.on("dragleave drop", (e) => {
            e.preventDefault();
            dropArea.removeClass("dragover");
        });

        // Add remove button for file input
        // Create remove button dynamically
        const removeBtn = $('<button type="button" class="btn btn-sm btn-danger mt-2">Remove Photo</button><br>').hide();
        dropArea.after(removeBtn);

        removeBtn.on("click", function() {
            selectedFile = null;
            photoInput.val("");
            preview.hide().attr("src", "");
            removeBtn.hide();
        });

        // Show remove button when file is selected
        function handleFileInput(files) {
            if (files.length > 0) {
                selectedFile = files[0];
                const validMime = ["image/jpeg", "image/png"];
                if (!validMime.includes(selectedFile.type)) {
                    // showAlert("The file format is not supported. Only JPEG and PNG files are allowed.", "danger");
                    showToastr("error", "The file format is not supported. Only JPEG and PNG files are allowed.");
                    return;
                }
                if (selectedFile.size > 5 * 1024 * 1024) {
                    // showAlert("The maximum allowed file size is 5MB", "danger");
                    showToastr("error", "The maximum allowed file size is 5MB");
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.attr("src", e.target.result).show();
                    removeBtn.show();
                };
                reader.readAsDataURL(selectedFile);
                // uploadBtn.prop("disabled", false); // Remove or comment out if not used
            }
        }

        dropArea.on("drop", (e) => {
            e.preventDefault();
            handleFileInput(e.originalEvent.dataTransfer.files);
        });
        dropArea.on("click", () => {
            photoInput.trigger("click");
        });
        photoInput.on("change", (e) => {
            handleFileInput(e.target.files);
        });

        const formAddUser = $("#add_user_form");
        formAddUser.submit((e) => {
            e.preventDefault();
            setOnProgress(true);
            let data = new FormData(formAddUser[0]);
            // const formData = new FormData();
            data.append("file_photo", selectedFile);
            $.ajax({
                type: "post",
                url: formAddUser.attr('action'),
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

        })

    }
</script>