<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
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
                <div class="table-responsive">
                    <table class="table table-sm table-hover " style="width: 100%;" id="dtbl_submission_schedule">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 120px;">Actions</th>
                                <th>Dikunci</th>
                                <th>Tahun</th>
                                <th>Triwulan</th>
                                <th>Tgl. Dimulai Pengajuan</th>
                                <th>Tgl. Akhir Pengajuan</th>
                                <th style="width:160px;">Created at</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dDtblSubmissionSchedule as $kRow  => $vRow) {
                            ?>
                                <tr>
                                    <?php
                                    foreach ($vRow as $vCell) {
                                    ?>
                                        <td><?= $vCell; ?></td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            <?php
                            }
                            ?>
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
<?= appViewInjectScript($viewDir, "show_preview_script"); ?>
<?= appViewInjectScript($viewDir, "show_add_script"); ?>
<?= appViewInjectScript($viewDir, "show_edit_script"); ?>
<?= appViewInjectScript($viewDir, "show_delete_script"); ?>
<script>
    $("#dtbl_submission_schedule").DataTable({
        "responsive": true,
        "order": [
            ['0', 'desc']
        ],
        fixedColumns: {
            left: 1,
        },
        // "iDisplayLength": 10,

        "columnDefs": [{}, ]
    });
</script>
<?= $this->endSection(); ?>