<?= $this->extend(appViewLayoutFile("auth")) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<h4 class="text-center mb-4">Login</h4>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<form action="<?= base_url("do_login"); ?>" method="post" id="login_form">
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
    <div class="form-group">
        <label for="txt_password">Password:</label>
        <div class="input-group mb-3">
            <input type="password" class="form-control form-control-sm" placeholder="Password" name="txt_password" id="txt_password">
            <div class="input-group-append">
                <div class="input-group-text" style="width: 40px;">
                    <span class="fa fa-eye-slash" id="togglePassword" style="cursor: pointer;"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 mb-3 text-center">
            <button type="submit" class="btn btn-dark btn-block">Login</button>
        </div>

        <div class="col-12 mb-2">
            <a href="<?= base_url('forgot-password'); ?>">Forgot Password?</a>
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
<?= appViewInjectScript($viewDir, "submit_login"); ?>
<?= $this->endSection(); ?>