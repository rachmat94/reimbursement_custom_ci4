<div class="modal fade " id="edit_group_modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Edit Group
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('group/do_edit'); ?>" id="edit_group_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_group_key" value="<?= $dGroup["group_key"]; ?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="txt_code" class="col-sm-4 col-form-label">Code</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="txt_code" name="txt_code" placeholder="" value="<?= $dGroup["group_code"]; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_name" class="col-sm-4 col-form-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="txt_name" name="txt_name" placeholder="" value="<?= $dGroup["group_name"]; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cbo_jenis_group" class="col-sm-4 col-form-label">Jenis Group</label>
                                <div class="col-sm-8">
                                    <select name="cbo_jenis_group" id="cbo_jenis_group" class="select2 form-control" style="width: 100%;">
                                        <?php
                                        foreach (masterJenisGroup() as $kJenis  => $vJenis) {
                                        ?>
                                            <option value="<?= $vJenis["code"]; ?>" <?= ($vJenis["code"] == $dGroup['group_jenis']) ? " selected " : ""; ?>><?= $vJenis["label"]; ?> </option>
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
                                        <option value="1" <?= ($dGroup["group_is_active"] == 1) ? " selected " : " "; ?>>Enable</option>
                                        <option value="0" <?= ($dGroup["group_is_active"] == 0) ? " selected " : " "; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="txt_kecamatan" class="col-sm-4 col-form-label">Kecamatan</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="txt_kecamatan" name="txt_kecamatan" placeholder="" value="<?= $dGroup['group_kecamatan']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_desa_kelurahan" class="col-sm-4 col-form-label">Desa/Kelurahan</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="txt_desa_kelurahan" name="txt_desa_kelurahan" placeholder="" value="<?= $dGroup['group_desa_kelurahan']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nbr_jml_sarana_prasarana" class="col-sm-4 col-form-label">Jml. Sarana/Prasarana</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control form-control-sm" id="nbr_jml_sarana_prasarana" name="nbr_jml_sarana_prasarana" placeholder="" value="<?= $dGroup['group_jml_titik_lokasi']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nbr_jml_titik_lokasi" class="col-sm-4 col-form-label">Jml. Titik Lokasi</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control form-control-sm" id="nbr_jml_titik_lokasi" name="nbr_jml_titik_lokasi" placeholder="" value="<?= $dGroup['group_jml_titik_lokasi']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group row">
                                <label for="txt_description" class="col-sm-12 col-form-label">Description</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control form-control-sm" id="txt_description" name="txt_description" placeholder=""><?= $dGroup["group_description"]; ?></textarea>
                                </div>
                            </div>
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