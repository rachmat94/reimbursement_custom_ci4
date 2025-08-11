<div class="modal fade " id="preview_jenisberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Jenis Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
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
                        "name" =>"Maksimal Ukuran berkas (mb)",
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

            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>