<div class="modal fade " id="preview_group_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Group
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <?= appRenderTableInfo3([
                    [
                        "name" => "Code:",
                        "value" => $dGroup["group_code"],
                    ],
                    [
                        "name" => "Name:",
                        "value" => $dGroup["group_name"],
                    ],
                    [
                        "name" => "Jenis Group:",
                        "value" => masterJenisGroup($dGroup["group_jenis"],true)["label"],
                    ],
                    [
                        "name" => "Kecamatan:",
                        "value" => $dGroup["group_kecamatan"],
                    ],
                    [
                        "name" => "Desa/Kelurahan:",
                        "value" => $dGroup["group_desa_kelurahan"]
                    ],
                    [
                        "name" => "Jml. Sarana/Prasarana",
                        "value" => $dGroup["group_jml_sarana_prasarana"],
                    ],
                    [
                        "name" => "Jml. Titik Lokasi",
                        "value" => $dGroup["group_jml_titik_lokasi"],
                    ],
                    [
                        "name" => "Status:",
                        "value" => appRenderBadgeStatus($dGroup["group_is_active"]),
                    ],
                    [
                        "name" => "Description:",
                        "value" => $dGroup["group_description"],
                    ],
                ]); ?>

            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>