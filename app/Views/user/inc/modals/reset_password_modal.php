<div class="modal fade " id="reset_password_user_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Reset Password user
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('user/do_reset_password'); ?>" id="reset_password_user_form">
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
                    <?php
                    if (!empty($dUser["usr_reset_password_token"])) {
                        $resetUrl = base_url("change_password?token=" . $dUser["usr_reset_password_token"] . "&email=" . $dUser['usr_email']);
                    ?>
                        <h6>Already Generated:</h6>
                        <?= appRenderTableInfo2([
                            [
                                "name" => "Reset Token:",
                                "value" => "<input type='text' readonly value='" . $dUser['usr_reset_password_token'] . "' class='form-control form-control-sm'>",
                            ],
                            [
                                "name" => "Expires:",
                                "value" => $dUser["usr_reset_password_expires"]
                            ],
                            [
                                "name" => "Url:",
                                "value" => "<textarea readonly class='form-control form-control-sm' >" . $resetUrl . "</textarea>",
                            ]
                        ]); ?>
                    <?php
                    }
                    ?>
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_usr_key" value="<?= $dUser['usr_key']; ?>">

                    <div class="form-group">
                        <label for="cbo_send_email">Send Email</label><span class="text-muted"></span>
                        <select name="cbo_send_email" id="cbo_send_email" class="form-control form-control-sm" style="width: 100%;">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Reset Password</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>