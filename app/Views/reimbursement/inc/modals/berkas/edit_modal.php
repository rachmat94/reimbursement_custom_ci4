<div class="modal fade " id="edit_reimberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Edit Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_edit_berkas'); ?>" id="edit_reimberkas_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_rb_key" value="<?= $dReimBerkas["rb_key"]; ?>">
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
                        ]
                    ]); ?>

                    <div class="form-group row">
                        <label for="txt_note" class="col-sm-12 col-form-label">Catatan / Keterangan</label>
                        <div class="col-sm-12">
                            <textarea class="form-control form-control-sm" id="txt_note" name="txt_note" placeholder=""><?= $dReimBerkas["rb_note"]; ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>