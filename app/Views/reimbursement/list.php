<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?=base_url("reimbursement");?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover border" style="width: 100%;" id="dtbl_reimbursements">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aksi</th>
                                <th>Triwulan</th>
                                <th>Tahun</th>
                                <th>Group</th>
                                <th>User</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dReimbursements  as $kReim  => $vReim) {
                            ?>
                                <tr>
                                    <td><?= $vReim["reim_id"]; ?></td>
                                    <td></td>
                                    <td><?=$vReim["reim_triwulan_no"];?></td>
                                    <td><?=$vReim["reim_triwulan_tahun"];?></td>
                                    <td>group</td>
                                    <td>User</td>
                                    <td>Code</td>
                                    <td>Status</td>
                                    <td>Category</td>
                                    <td>Nominal</td>
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
<script>
    $("#dtbl_reimbursements").DataTable({
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