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
                    <dt>Tanggal: <?= appFormatTanggalIndonesia(Date("Y-m-d"),true); ?></dt>
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
<?= $this->endSection(); ?>

<?= $this->section("footer"); ?>

<?= $this->endSection(); ?>

<?= $this->section("modal"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_0"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_1"); ?>
<?= $this->endSection(); ?>