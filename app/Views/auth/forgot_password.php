<?= $this->extend(appViewLayoutFile("user_auth")) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<h4 class="text-center mb-2">Forgot Password</h4>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<form action="<?= base_url("do_forgot_password"); ?>" method="post" id="forgot_password_form">

    <?= mycsrfTokenField() ?>
    <div class="form-group">
        <label for="txt_email">Email:</label>
        <div class="input-group mb-3">
            <input type="email" class="form-control form-control-sm" placeholder="Email" name="txt_email" id="txt_email" value="">
            <div class="input-group-append">
                <div class="input-group-text" style="width: 40px;"><i class="fas fa-envelope"></i></div>
            </div>
        </div>

    </div>


    <div class="row mb-4">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-dark btn-block">Reset Password</button>
        </div>

        <div class="col-12 mt-3">
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
<?= appViewInjectScript($viewDir, "submit_forgot_password"); ?>
<?= $this->endSection(); ?>