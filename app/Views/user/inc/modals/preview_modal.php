<div class="modal fade " id="preview_user_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    User
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body table-responseive">
                <?= appRenderSectionHeader("User Information"); ?>
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
            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>