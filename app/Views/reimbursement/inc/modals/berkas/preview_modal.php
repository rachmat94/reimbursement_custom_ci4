<div class="modal fade " id="preview_reimberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Preview Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body table-responseive">
                <div class="row">
                    <div class="col-lg-4">
                        <?= appRenderTableInfo2([
                            [
                                "name" => "Jenis Berkas",
                                "value" => "[ " . $dReimBerkas['jb_code'] . " ] " . $dReimBerkas["jb_name"],
                            ],
                            [
                                "name" => "Deskripsi",
                                "value" => $dReimBerkas["jb_description"],
                            ],
                            [
                                "name" => "Status",
                                "value" => ($dReimBerkas['jb_is_required'] == 1) ? "Wajib" : "Tidak Wajib",
                            ],
                            [
                                "name" => "Tipe File",
                                "value" => "File Pdf atau gambar<br> Makimal: " .  $dReimBerkas["jb_max_file_size_mb"] . " Mb",
                            ],
                            [
                                "name" => "File",
                                "value" => $dReimBerkas["rb_file_name"],
                            ],
                            [
                                "name" => "File Name Origin",
                                "value" => $dReimBerkas["rb_file_name_origin"],
                            ],
                            [
                                "name" => "Diupload pada",
                                "value" => appFormatTanggalIndonesia($dReimBerkas["rb_created_at"]),
                            ],
                            [
                                "name" => "Catatan",
                                "value" => $dReimBerkas["rb_note"],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-8">

                        <?php
                        $fUrl = $dFile["file_url"];
                        echo $dFile["message"];
                        if ($dFile["file_category"] == "pdf") {
                        ?>
                            <div class="p-2" style="height:70vh;">
                                <iframe id="pdfViewer" src="<?= $fUrl; ?>" style="width:100%; height:100%;" frameborder="0"></iframe>
                            </div>
                        <?php
                        } else if ($dFile["file_category"] == "image") {
                        ?>
                            <div class="" style="text-align:center; overflow:auto; height:70vh;">
                                <img id="imagePreview" src="<?= $fUrl; ?>" style="max-width:100%; transform: scale(1); transform-origin: center center;">
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-secondary" onclick="zoomOutImage()">- Zoom Out</button>
                                <button class="btn btn-secondary" onclick="zoomInImage()">+ Zoom In</button>
                                <button class="btn btn-secondary" onclick="resetZoom()">Reset</button>
                            </div>
                        <?php
                        } else {
                        }
                        ?>

                    </div>
                </div>


            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>