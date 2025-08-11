<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>

<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row mt-3">
    <div class="col-12">
        <p class="text-center " style="font-size: 14pt;">
            <?= $message ?? "An unexpected error occurred. Please try again." ?>
        </p>
        <?php
       
        $cnmSlug = current_url(true)->getSegment(1);
        if (!empty($cnmSlug)) {
        ?>
            <p class="text-center">
                <a href="<?= base_url($cnmSlug); ?>">Back to <?= $cnmSlug; ?></a>
            </p>
        <?php
        }
        ?>
    </div>
    <div class="col-12 text-center">
        <a href="<?= base_url(); ?>">Home Page</a> |
        <a href="<?= base_url('logout'); ?>">Logout</a> |
        <a href="<?= base_url('login'); ?>">Login</a> |
        <a href="<?= base_url('dashboard'); ?>">Dashboard</a> |
        <a href="<?= base_url('myaccount'); ?>">My Account</a>
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