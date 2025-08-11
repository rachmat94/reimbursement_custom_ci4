<?= $this->extend(appViewLayoutFile("auth")) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<h4 class="text-center mb-4">Change Password</h4>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<form action="<?= base_url("do_change_password"); ?>" method="post" id="change_password_form">

    <?= mycsrfTokenField() ?>
    <input type="hidden" name="hdn_token" value="<?= $token; ?>">
    <input type="hidden" name="hdn_usr_key" value="<?= $dUser['usr_key']; ?>">
    <div class="input-group mb-3">
        <input type="email" class="form-control form-control-sm" placeholder="Email" name="txt_email" id="txt_email" value="<?= $dUser["usr_email"]; ?>" readonly disabled>
        <div class="input-group-append">
            <div class="input-group-text" style="width: 40px;"><i class="fas fa-envelope"></i></div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" class="form-control form-control-sm" placeholder="Password" name="txt_password" id="txt_password">
        <div class="input-group-append">
            <div class="input-group-text" style="width: 40px;">
                <span class="fa fa-eye-slash" id="togglePassword" style="cursor: pointer;"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" class="form-control form-control-sm" placeholder="Retype Password" name="txt_password_confirm" id="txt_password_confirm">
        <div class="input-group-append">
            <div class="input-group-text" style="width: 40px;"><i class="fas fa-lock"></i></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-dark btn-block">Change Password</button>
        </div>

        <div class="col-12">
            <p class="mt-3 text-center">
                Back to: <br>
                <a href="<?= base_url('login'); ?>">Login</a>
            </p>
        </div>
    </div>
</form>
<?= $this->endSection(); ?>

<?= $this->section("footer"); ?>

<?= $this->endSection(); ?>

<?= $this->section("modal"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_0"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_1"); ?>
<?= appViewInjectScript($viewDir, "submit_change_password"); ?>
<?= $this->endSection(); ?>