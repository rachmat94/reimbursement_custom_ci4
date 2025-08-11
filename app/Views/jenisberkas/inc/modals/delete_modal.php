<div class="modal fade " id="delete_jenisberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Delete Jenis Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('jenis-berkas/do_delete'); ?>" id="delete_jenisberkas_form">
                <div class="modal-body table-responseive">
                    <?= appRenderTableInfo3([
                        [
                            "name" => "Code:",
                            "value" => $dJenisBerkas["jb_code"],
                        ],
                        [
                            "name" => "Name:",
                            "value" => $dJenisBerkas["jb_name"],
                        ],
                        [
                            "name" => "Maksimal Ukuran berkas (mb)",
                            "value" => $dJenisBerkas["jb_max_file_size_mb"],
                        ],
                        [
                            "name" => "Status:",
                            "value" => appRenderBadgeStatus($dJenisBerkas["jb_is_active"]),
                        ],
                        [
                            "name" => "Description:",
                            "value" => $dJenisBerkas["jb_description"],
                        ],
                    ]); ?>
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_jb_key" value="<?= $dJenisBerkas["jb_key"]; ?>">

                    <button type="submit" class="btn btn-danger px-3 my-1"><i class="fas fa-times"></i> Delete</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>