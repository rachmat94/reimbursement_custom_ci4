<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url('submission-schedule'); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <div class="card-header">
                <h3 class="card-title">Jadwal Pengajuan</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="showAddSubmissionSchedule();">
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
                        "name" => "Tahun:",
                        "value" => $dSubmissionSchedule["sw_tahun"],
                    ],
                    [
                        "name" => "Triwulan:",
                        "value" => $dSubmissionSchedule["sw_triwulan"],
                    ],
                    [
                        "name" => "Dikunci:",
                        "value" => appRenderBadgeLocked($dSubmissionSchedule["sw_is_locked"]),
                    ],
                    [
                        "name" => "Tgl. Dimulai Pengajuan:",
                        "value" => $dSubmissionSchedule["sw_start_date"],
                    ],
                    [
                        "name" => "Tgl. Akhir Pengajuan:",
                        "value" => $dSubmissionSchedule["sw_end_date"],
                    ]
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