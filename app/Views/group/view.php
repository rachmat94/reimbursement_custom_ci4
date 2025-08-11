<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url('group'); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <div class="card-header">
                <h3 class="card-title">Group</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="showAddGroup();">
                        <i class="fas fa-plus-circle"></i>
                    </button>
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
<?= $this->endSection(); ?>