<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url('jenis-berkas'); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <div class="card-header">
                <h3 class="card-title">Jenis Berkas</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="showAddJenisBerkas();">
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
                        "value" => $dJenisBerkas["jb_code"],
                    ],
                    [
                        "name" => "Name:",
                        "value" => $dJenisBerkas["jb_name"],
                    ],
                    [
                        "name" => "Maksimal Ukuran berkas (mb)",
                        "value" => $dJenisBerkas["jb_max_file_size_mb"],
                    ],
                    [
                        "name" => "Status:",
                        "value" => appRenderBadgeStatus($dJenisBerkas["jb_is_active"]),
                    ],
                    [
                        "name" => "Description:",
                        "value" => $dJenisBerkas["jb_description"],
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