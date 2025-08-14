<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url("reimbursement"); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reimbursement Triwulan <?= $triwulan; ?>, <?= $tahun; ?></h3>

                <div class="card-tools">

                    <button type="button" class="btn btn-tool" onclick="reloaddTblUsersForReim()" data-load-on-init="false">
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
                    <table class="table table-sm table-hover border" style="width: 100%;" id="dtbl_users_for_reim">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Action</th>
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
<?= appViewInjectScript("reimbursement", "dtbl_user_for_reim"); ?>
<?= $this->endSection(); ?>