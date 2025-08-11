<?php
$segments = current_url(true)->getSegments();
$curCNM = strtolower($cnm ?? strtolower(str_replace("-", "", $segments[0] ?? "")));

$dMenu = [
    "dashboard" => [
        "cnm" => "dashboard",
        "res" => "",
        "url" => base_url("admin/dashboard"),
        "label" => "Dashboard",
        "icon" => "fas fa-tachometer-alt",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],
    "user" => [
        "cnm" => "user",
        "res" => "",
        "url" => base_url("admin/user"),
        "label" => "User",
        "icon" => "fas fa-users",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],
    "admin" => [
        "cnm" => "admin",
        "res" => "",
        "url" => base_url("admin/list"),
        "label" => "Admin",
        "icon" => "fas fa-user-tie",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],
    "client" => [
        "cnm" => "client",
        "res" => "",
        "url" => base_url("admin/client"),
        "label" => "Client",
        "icon" => "fas fa-th",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],
];


?>

<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php
            foreach ($dMenu as $kMenu => $vMenu) {
                if (!empty($vMenu["icon_url"])) {
                    $icon = "<img src='" . $vMenu['icon_url'] . "' >";
                } else {
                    $icon = '<i class="nav-icon ' . $vMenu["icon"] . '"></i>';
                }
                if ($vMenu["cnm"] == "nav_header") {
            ?>
                    <li class="nav-header">
                        <?= $vMenu["label"]; ?>
                    </li>
                    <?php
                } else {
                    if (count($vMenu["sub"]) <= 0) {
                    ?>
                        <li class="nav-item">
                            <a href="<?= $vMenu['url']; ?>" class="nav-link <?= ($vMenu["cnm"] == $curCNM) ? ' active ' : ''; ?> ">
                                <?= $icon; ?>
                                <p><?= $vMenu["label"]; ?></p>
                            </a>
                        </li>
                    <?php
                    } else {
                        $mnuOpen = "";
                        $mnuActive = "";
                        if (in_array($curCNM, array_keys($vMenu['sub']))) {
                            $mnuOpen = " menu-open ";
                            $mnuActive = " active ";
                        }
                    ?>
                        <li class="nav-item  <?= $mnuOpen; ?> ">
                            <a href="#" class="nav-link <?= $mnuActive; ?> ">
                                <?= $icon; ?>
                                <p>
                                    <?= $vMenu["label"]; ?>
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php
                                $dSubMenu = $vMenu["sub"] ?? [];
                                foreach ($dSubMenu as $kSubMenu => $vSubMenu) {

                                ?>
                                    <li class="nav-item">
                                        <a href="<?= $vSubMenu['url']; ?>" class="nav-link <?= ($vSubMenu["cnm"] == $curCNM) ? ' active ' : ''; ?> ">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p><?= $vSubMenu["label"]; ?></p>
                                        </a>
                                    </li>
                                <?php

                                }
                                ?>

                            </ul>
                        </li>
            <?php
                    }
                }
            }
            ?>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>