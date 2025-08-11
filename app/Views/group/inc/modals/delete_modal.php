<div class="modal fade " id="delete_group_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Delete Group
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('group/do_delete'); ?>" id="delete_group_form">
                <div class="modal-body table-responseive">
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
                            "value" => masterJenisGroup($dGroup["group_jenis"], true)["label"],
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
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_group_key" value="<?= $dGroup["group_key"]; ?>">

                    <button type="submit" class="btn btn-danger px-3 my-1"><i class="fas fa-times"></i> Delete</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>