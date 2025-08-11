<?php
$segments = current_url(true)->getSegments();
$curCNM = strtolower($cnm ?? strtolower(str_replace("-", "", $segments[0] ?? "")));

$dMenu = [
    "dashboard" => [
        "cnm" => "dashboard",
        "res" => "",
        "url" => base_url("user"),
        "label" => "Dashboard",
        "icon" => "fas fa-tachometer-alt",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],

    "reimbursement" => [
        "cnm" => "reimbursement",
        "res" => "",
        "url" => base_url("reimbursement"),
        "label" => "Reimbursement",
        "icon" => "fas fa-tachometer-alt",
        "res_list" => [],
        "exception_role_list" => [],
        "sub" => [],
    ],
    
    "user" => [
        "cnm" => "",
        "res" => "",
        "url" => "#",
        "label" => "User Management",
        "icon" => "fas fa-user",
        "icon_url" => "",
        "res_list" => ["r_user"],
        "exception_role_list" => [],
        "sub" => [
            "user" => [
                "cnm" => "user",
                "res" => "r_user",
                "exception_role_list" => [],
                "url" => base_url("user"),
                "label" => "User",
                "icon" => "far fa-circle",
            ],

        ],
    ],

    "master" => [
        "cnm" => "",
        "res" => "",
        "url" => "#",
        "label" => "Master",
        "icon" => "fas fa-database",
        "icon_url" => "",
        "res_list" => ["r_group"],
        "exception_role_list" => [],
        "sub" => [
            "group" => [
                "cnm" => "group",
                "res" => "r_group",
                "exception_role_list" => [],
                "url" => base_url("group"),
                "label" => "Group User",
                "icon" => "far fa-circle",
            ],
            "category" => [
                "cnm" => "category",
                "res" => "r_category",
                "exception_role_list" => [],
                "url" => base_url("category"),
                "label" => "Category Reimbursement",
                "icon" => "far fa-circle",
            ],
            "jenisberkas" => [
                "cnm" => "jenisberkas",
                "res" => "r_jenis_berkas",
                "exception_role_list" => [],
                "url" => base_url("jenis-berkas"),
                "label" => "Jenis Berkas",
                "icon" => "far fa-circle",
            ],
            "submissionschedule" => [
                "cnm" => "submissionschedule",
                "res" => "r_submission_schedule",
                "exception_role_list" => [],
                "url" => base_url("submission-schedule"),
                "label" => "Jadwal Pengajuan",
                "icon" => "far fa-circle",
            ],

        ],
    ],

    "appconfig" => [
        "cnm" => "",
        "res" => "",
        "url" => "#",
        "label" => "App Config",
        "icon" => "fas fa-cogs",
        "icon_url" => "",
        "res_list" => ["r_appconfig"],
        "exception_role_list" => [],
        "sub" => [
            "appconfig" => [
                "cnm" => "appconfig",
                "res" => "r_appconfig",
                "exception_role_list" => [],
                "url" => base_url("appconfig"),
                "label" => "All Config",
                "icon" => "far fa-circle",
            ],

        ],
    ],

];


?>

<div class="sidebar">
    <!-- SidebarSearch Form -->
    <div class="form-inline mt-2">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
    </div>
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