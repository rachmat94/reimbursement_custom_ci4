<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5>Saat ini:</h5>
                <dl>
                    <dt>Tanggal: <?= appFormatTanggalIndonesia(Date("Y-m-d"), true); ?></dt>
                </dl>
                <h5>Triwulan: <?= appGetCurrentQuarter(); ?></h5>
            </div>
        </div>
    </div>
    <?php
    if (isset($dSubmissionSchedule)) {
        foreach ($dSubmissionSchedule as $kSubSchedule => $vSubSchedule) {
    ?>
            <div class="col-lg-3 col-md-6">
                <div class="card elevation-2">
                    <div class="card-body">
                        <h5>Triwulan <?= $vSubSchedule["sw_triwulan"]; ?>, <?= $vSubSchedule["sw_tahun"]; ?></h5>
                        <p>
                            <?= implode(", ", appGetQuarterMonths($vSubSchedule["sw_triwulan"])); ?>
                        </p>
                        <dl>
                            <dt>Jadwal Pengajuan:</dt>
                            <dd>
                                <?= appFormatTanggalIndonesia($vSubSchedule["sw_start_date"], true); ?><br>
                                s/d <br>
                                <?= appFormatTanggalIndonesia($vSubSchedule["sw_end_date"], true); ?>
                            </dd>

                            <dt>Status:</dt>
                            <dd>
                                <?php
                                if ($vSubSchedule["sw_is_locked"] == 1) {
                                    echo "Dikunci";
                                } else {
                                    echo "Dibuka";
                                }
                                ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('reimbursement/create?triwulan=' . $vSubSchedule["sw_triwulan"] . '&tahun=' . $vSubSchedule['sw_tahun']); ?>" class="btn btn-block btn-outline-dark">Buka Form Pengajuan</a>
                        <a href="<?= base_url('reimbursement/list?triwulan=' . $vSubSchedule["sw_triwulan"] . '&tahun=' . $vSubSchedule['sw_tahun']); ?>" class="btn btn-block btn-outline-dark">Lihat Data</a>
                    </div>
                </div>
            </div>
        <?php
        }
    } else {
        ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Tidak ada data Jadwal Pengajuan Triwulan.</h5>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reimbursement</h3>

                <div class="card-tools">

                    <button type="button" class="btn btn-tool" onclick="reloaddTblReimbursements()" data-load-on-init="false">
                        <i class="fas fa-sync-alt"></i>
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover border" style="width: 100%;" id="dtbl_reimbursement">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Action</th>
                                <th>Triwulan</th>
                                <th>Tahun</th>
                                <th>Group</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Kategori User</th>
                                <th>Kode Reimbursement</th>
                                <th>Status Reimbursement</th>
                                <th>Category Reimbursement</th>
                                <th>Nominal Reimbursement</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
<?= appViewInjectScript("reimbursement", "dtbl_reimbursements"); ?>
<?= $this->endSection(); ?>