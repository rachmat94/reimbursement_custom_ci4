<div class="container">
    <a href="<?= base_url(); ?>" class="navbar-brand">
        <img src="<?= assetLogo(); ?>" alt="Logo" class="brand-image ">
        <span class="brand-text "><?= appName(); ?></span>
        <span class="badge badge-dark" style="font-size: 8pt;">admin</span>
    </a>


    <button class="navbar-toggler order-1 p-0" type="button" data-widget="pushmenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>


        </ul>


    </div>

    <!-- Right navbar links -->
    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">

        <?php
        if (!($dAccess["success"] ?? null)) {
        ?>
            <li class="nav-item user-menu">
                <a href="<?= base_url('admin/auth/login'); ?>" class="nav-link dropdown-toggle px-2">
                    <i class="fas fa-sign-in-alt"> Login</i>
                </a>

            </li>
        <?php
        } else {
        ?>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle pr-1" data-toggle="dropdown">
                    <img src="<?= assetUser(); ?>" class="user-image border border-dark " alt="User Image" style="width: 24px;height: 24px;">
                </a>
                <ul class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="border-radius: 20px 20px 20px 20px;">
                    <!-- User image -->
                    <li class="user-header bg-dark" style="border-radius: 20px 20px 0px 0px;">
                        <img src="<?= assetUser(); ?>" class="img-circle elevation-2" alt="User Image">

                        <p>
                            <?= $dAccess["data"]["adm_email"] ?? ""; ?>
                        </p>
                    </li>
                   
                    <li class="user-footer py-4 bg-white" style="border-radius: 0px 0px 20px 20px;">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= base_url('admin/myaccount'); ?>" class="btn btn-sm btn-block btn-outline-primary " style="border-radius: 10px;"><i class="fas fa-user-circle"></i> My Account</a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base_url('admin/auth/logout'); ?>" class="btn btn-sm btn-outline-danger btn-block" style="border-radius: 10px;"><i class="fas fa-sign-out-alt"></i> Log out</a>
                            </div>
                        </div>


                    </li>
                   
                </ul>
            </li>
        <?php
        }
        ?>

    </ul>
</div>