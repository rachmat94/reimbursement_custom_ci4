<div class="modal fade " id="delete_user_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Delete User
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('user/do_delete'); ?>" id="delete_user_form">
                <div class="modal-body table-responseive">
                    <?= appRenderTableInfo3([
                        [
                            "name" => "Code:",
                            "value" => $dUser["usr_code"] ?? ""
                        ],
                        [
                            "name" => "Email:",
                            "value" => $dUser["usr_email"] ?? ""
                        ],
                        [
                            "name" => "Username:",
                            "value" => $dUser["usr_username"] ?? "",
                        ],
                        [
                            "name" => "Role:",
                            "value" => appRenderBadgeUserRole($dUser["usr_role"] ?? "")
                        ],
                        [
                            "name" => "Status:",
                            "value" => appRenderBadgeStatus($dUser["usr_is_active"] ?? 0)
                        ],
                        [
                            "name" => "Created at:",
                            "value" => $dUser["usr_created_at"] ?? ""
                        ],
                        [
                            "name" => "Updated at:",
                            "value" => $dUser["usr_updated_at"] ?? ""
                        ],
                        [
                            "name" => "Photo:",
                            "value" => appRenderUserPhoto($dUser["usr_key"], $dUser["usr_photo_file_name"]),
                        ]
                    ]); ?>
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_usr_key" value="<?= $dUser['usr_key']; ?>">
                    <button type="submit" class="btn btn-danger px-3 my-1"><i class="fas fa-times"></i> Delete</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>