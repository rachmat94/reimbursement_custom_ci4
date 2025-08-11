<div class="modal fade " id="edit_user_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Edit User
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('user/do_edit'); ?>" id="edit_user_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_usr_key" value="<?= $dUser['usr_key']; ?>">
                    <div class="form-group row">
                        <label for="txt_username" class="col-sm-4 col-form-label">Username</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" id="txt_username" name="txt_username" placeholder="Username" value="<?= $dUser['usr_username']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cbo_role" class="col-sm-4 col-form-label">Role</label>
                        <div class="col-sm-8">
                            <select name="cbo_role" id="cbo_role" class="select2 form-control" style="width: 100%;">
                                <?php
                                foreach (masterUserRole() as $kRole => $dRole) {
                                ?>
                                    <option value="<?= $kRole; ?>" <?= ($dUser['usr_role'] == $kRole) ? "selected" : ""; ?>><?= $dRole["label"]; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cbo_status" class="col-sm-4 col-form-label">Status</label><span class="text-muted"></span>
                        <div class="col-sm-8">
                            <select name="cbo_status" id="cbo_status" class="select2  form-control " style="width: 100%;">
                                <option value="0" <?= ($dUser['usr_is_active'] == 0) ? " selected " : ""; ?>>Disable</option>
                                <option value="1" <?= ($dUser['usr_is_active'] == 1) ? " selected " : ""; ?>>Enable</option>
                            </select>
                        </div>
                    </div>
                    <?php
                    if (!empty($dUser["usr_photo_file_name"])) {
                        $photo = appRenderUserPhoto($dUser['usr_key'], $dUser['usr_photo_file_name'], false);
                    ?>
                        <div class="form-group">
                            <label for="current_photo">Current Photo:</label><br>
                            <?= $photo; ?>
                            <br>
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="doDeleteUserPhoto('<?= $dUser['usr_key']; ?>')">Delete Photo</button>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="form-group">
                        <label for="file_photo" class="col-form-label">Photo</label>
                        <div id="drop-area" class="drop-area">
                            <i class="fas fa-cloud-upload-alt fa-2x"></i><br />
                            Drag and drop images here, or click to choose a file
                        </div>
                        <img id="preview" class="preview" style="display:none;" />
                        <input
                            type="file"
                            id="file_photo"
                            accept="image/jpeg,image/png,image/webp"
                            style="display:none;" />
                    </div>


                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>