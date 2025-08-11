<div class="modal fade " id="upload_reimberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Upload Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_upload_berkas'); ?>" id="upload_reimberkas_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement["reim_id"]; ?>">
                    <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement["reim_key"]; ?>">
                    <input type="hidden" name="hdn_jb_key" value="<?= $dJenisBerkas['jb_key']; ?>">
                    <?= appRenderTableInfo2([
                        [
                            "name" => "Jenis Berkas",
                            "value" => "[ " . $dJenisBerkas['jb_code'] . " ] " . $dJenisBerkas["jb_name"],
                        ],
                        [
                            "name" => "Deskripsi",
                            "value" => $dJenisBerkas["jb_description"],
                        ],
                        [
                            "name" => "Status",
                            "value" => ($dJenisBerkas['jb_is_required'] == 1) ? "Wajib" : "Tidak Wajib",
                        ],
                        [
                            "name" => "Tipe File",
                            "value" => "File Pdf atau gambar<br> Makimal: " .  $dJenisBerkas["jb_max_file_size_mb"] . " Mb",
                        ],
                    ]); ?>
                    <div class="form-group row">
                        <label for="file_berkas" class="col-sm-12 col-form-label">File</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control form-control-sm" id="file_berkas" name="file_berkas" placeholder="" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_note" class="col-sm-12 col-form-label">Catatan / Keterangan</label>
                        <div class="col-sm-12">
                            <textarea class="form-control form-control-sm" id="txt_note" name="txt_note" placeholder=""></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>