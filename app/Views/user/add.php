<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<?= appViewInjectHead("layout", "dropzone_style"); ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <form class="form-horizontal" autocomplete="off" method="post" action="<?= base_url('user/do_add'); ?>" id="add_user_form">
                <div class="card-header">
                    <a href="<?= base_url('user'); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="txt_email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control form-control-sm" id="txt_email" name="txt_email" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="txt_username" class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm" id="txt_username" name="txt_username" placeholder="Username">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cbo_role" class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select name="cbo_role" id="cbo_role" class="select2 form-control" style="width: 100%;">
                                        <option value="">-</option>
                                        <?php
                                        foreach (masterUserRole() as $kRole => $dRole) {
                                        ?>
                                            <option value="<?= $kRole; ?>"><?= $dRole["label"]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
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
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="cbo_group" class="col-sm-3 col-form-label">Group</label>
                                <div class="col-sm-9">
                                    <select name="cbo_group" id="cbo_group" class="select2 form-control" style="width: 100%;">
                                        <option value="">-</option>
                                        <?php
                                        foreach ($dGroups ?? [] as $kGroup => $vGroup) {
                                        ?>
                                            <option value="<?= $vGroup["group_id"]; ?>">
                                                [ <?= $vGroup["group_code"]; ?> ]
                                                <?= $vGroup["group_name"]; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cbo_group_category" class="col-sm-3 col-form-label">Kategori User pada group yang dipilih</label>
                                <div class="col-sm-9">
                                    <select name="cbo_group_category" id="cbo_group_category" class="select2 form-control" style="width: 100%;">
                                        <option value="">-</option>
                                        <?php
                                        foreach (masterUserCategoryInGroup() as $kUCGroup => $vUCGroup) {
                                        ?>
                                            <option value="<?= $kUCGroup; ?>"><?= $vUCGroup["label"]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <?= mycsrfTokenField(); ?>
                    <button type="submit" class="btn btn-dark"><i class="fas fa-save"></i> Save</button>
                    <button type="reset" class="btn btn-default float-right">Reset</button>
                </div>
                <!-- /.card-footer -->
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("footer"); ?>

<?= $this->endSection(); ?>

<?= $this->section("modal"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_0"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_1"); ?>
<?= appViewInjectScript($viewDir, "submit_add_script") ?>

<?= $this->endSection(); ?>