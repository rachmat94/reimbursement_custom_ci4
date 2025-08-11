<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<div class="row mb-2">
    <div class="col-12">
        <a href="<?= base_url('user') ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-backward"></i> Back to User List</a>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <?= appRenderSectionHeader("User Information"); ?>
                        <?= appRenderTableInfo3([
                            [
                                "name" => "Code:",
                                "value" => $dUser["usr_code"] ?? ""
                            ],
                            [
                                "name" => "Email:",
                                "value" => $dUser["usr_email"] ?? ""
                            ],
                            [
                                "name" => "Username:",
                                "value" => $dUser["usr_username"] ?? "",
                            ],
                            [
                                "name" => "Role:",
                                "value" => appRenderBadgeUserRole($dUser["usr_role"] ?? "")
                            ],
                            [
                                "name" => "Status:",
                                "value" => appRenderBadgeStatus($dUser["usr_is_active"] ?? 0)
                            ],
                            [
                                "name" => "Created at:",
                                "value" => $dUser["usr_created_at"] ?? ""
                            ],
                            [
                                "name" => "Updated at:",
                                "value" => $dUser["usr_updated_at"] ?? ""
                            ],
                            [
                                "name" => "Photo:",
                                "value" => appRenderUserPhoto($dUser["usr_key"], $dUser["usr_photo_file_name"]),
                            ]
                        ]); ?>
                    </div>
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
<?= $this->endSection(); ?>