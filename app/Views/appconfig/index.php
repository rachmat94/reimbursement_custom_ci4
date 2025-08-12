<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-4">
        <div class="card elevation-2">
            <div class="card-header">
                <h3 class="card-title">Maintenance Mode</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="dropdown-menu  dropdown-menu-right" role="menu">
                            <a role="button" onclick="" class="dropdown-item text-danger"><i class="fas fa-times"></i> Sub Menu</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <div class="card-body ">
                <form autocomplete="off" method="post" action="<?= base_url('appconfig/do_edit'); ?>" id="edit_maintenance_mode_form" class="appconfig_form">
                    <div class="form-group row">
                        <label for="cbo_maintenance_mode" class="col-sm-12 col-form-label">Status</label><span class="text-muted"></span>
                        <div class="col-sm-12">
                            <select name="cbo_maintenance_mode" id="cbo_maintenance_mode" class="select2  form-control " style="width: 100%;">
                                <option value="0" <?= ($dAppConfigs["maintenance_mode"]["cfg_value"]  == 0) ? " selected " : " "; ?>>Disable</option>
                                <option value="1" <?= ($dAppConfigs["maintenance_mode"]["cfg_value"] == 1) ? " selected " : " "; ?>>Enable</option>
                            </select>
                        </div>
                    </div>
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_type" value="maintenance_mode">
                    <input type="hidden" name="hdn_cfg_key" value="<?= $dAppConfigs["maintenance_mode"]["cfg_key"]; ?>">

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
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
<?= appViewInjectScript($viewDir, "submit_edit_script"); ?>
<?= $this->endSection(); ?>