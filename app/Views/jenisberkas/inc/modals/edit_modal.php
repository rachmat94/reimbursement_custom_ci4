<div class="modal fade " id="edit_jenisberkas_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Edit Jenis Berkas
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('jenis-berkas/do_edit'); ?>" id="edit_jenisberkas_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_jb_key" value="<?= $dJenisBerkas["jb_key"]; ?>">
                    <div class="form-group row">
                        <label for="txt_code" class="col-sm-4 col-form-label">Code</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" id="txt_code" name="txt_code" placeholder="" value="<?= $dJenisBerkas["jb_code"]; ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_name" class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" id="txt_name" name="txt_name" placeholder="" value="<?= $dJenisBerkas["jb_name"]; ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nbr_max_file_size" class="col-sm-4 col-form-label">Maksimal Ukuran Berkas (mb)</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control form-control-sm" id="nbr_max_file_size" name="nbr_max_file_size" placeholder="" value="<?=$dJenisBerkas["jb_max_file_size_mb"];?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cbo_status" class="col-sm-4 col-form-label">Status</label><span class="text-muted"></span>
                        <div class="col-sm-8">
                            <select name="cbo_status" id="cbo_status" class="select2  form-control " style="width: 100%;">
                                <option value="1" <?= ($dJenisBerkas["jb_is_active"] == 1) ? " selected " : " "; ?>>Enable</option>
                                <option value="0" <?= ($dJenisBerkas["jb_is_active"] == 0) ? " selected " : " "; ?>>Disable</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_description" class="col-sm-4 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <textarea class="form-control form-control-sm" id="txt_description" name="txt_description" placeholder=""><?= $dJenisBerkas["jb_description"]; ?></textarea>
                        </div>
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