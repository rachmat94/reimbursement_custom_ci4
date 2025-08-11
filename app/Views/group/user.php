<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url('group'); ?>" class="btn btn-sm btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row ">
    <div class="col-lg-12">
        <div class="card elevation-2">
            <div class="card-header">
                <h3 class="card-title">Group</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="showAddGroup();">
                        <i class="fas fa-plus-circle"></i>
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
            <div class="card-body ">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" style="width: 100%;" id="dtbl_group_users">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 140px;">Group</th>
                                <th rowspan="2" style="width: 260px;">User</th>
                                <th rowspan="2" style="width: 90px;">Kategori User</th>
                                <th class="text-center" colspan="<?= count($dSubSchedule); ?>">Pengajuan <?= date("Y"); ?></th>
                            </tr>
                            <tr>
                                <?php
                                foreach ($dSubSchedule ?? [] as $kSub => $vSub) {
                                    if (appGetCurrentQuarter() == $vSub["sw_triwulan"]) {
                                        $colColor = "bg-secondary";
                                    } else {
                                        $colColor = "";
                                    }
                                ?>
                                    <th class="text-center <?= $colColor; ?>">
                                        Triwulan <?= $vSub["sw_triwulan"] ?>
                                        <i class="fas fa-info-circle float-right" title="Waktu Pengajuan: <?= appFormatTanggalIndonesia($vSub['sw_start_date']) . ' s/d ' . appFormatTanggalIndonesia($vSub['sw_end_date']); ?>"></i>
                                    </th>
                                <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dGroupUsers  as $kGUser  => $vGUser) {
                                $photoUrl = assetUser();
                                if (!empty($item["usr_photo_file_name"])) {
                                    $photoData = appGetUserPhoto($item["usr_key"], $item["usr_photo_file_name"]);
                                    if (!empty($photoData["file_url"])) {
                                        $photoUrl = $photoData["file_url"];
                                    }
                                }
                                $uPhoto = "<img src='" . $photoUrl . "' class='img-thumbnail mb-2' style='width: 90px;'>";
                                if (!empty($vGUser["usr_group_category"])) {
                                    $ucategory = masterUserCategoryInGroup($vGUser["usr_group_category"], true)["label"];
                                } else {
                                    $ucategory = "-";
                                }
                                $lblRole = appRenderBadgeUserRole($vGUser["usr_role"]);
                            ?>
                                <tr>
                                    <td><?= $vGUser["group_name"]; ?></td>
                                    <td><?= $lblRole; ?> <?= $vGUser["usr_username"]; ?></td>
                                    <td><?= $ucategory; ?></td>
                                    <?php
                                    foreach ($dSubSchedule ?? [] as $kSub => $vSub) {
                                        $today = date("Y-m-d");
                                        $startDate = $vSub["sw_start_date"];
                                        $endDate = $vSub["sw_end_date"];
                                        $triwulan = $vSub["sw_triwulan"];
                                        $tahun = $vSub["sw_tahun"];
                                        $currentTriwulan = appGetCurrentQuarter();
                                        $inRange = appIsDateInRanges($today, ["start" => "", "end" => ""]);
                                        $usrKey = $vGUser["usr_key"];

                                        if ($currentTriwulan == $triwulan) {
                                            $colColor = "bg-secondary";
                                        } else {
                                            $colColor = "";
                                        }

                                        $btn = "";
                                        $dGUReim = $vGUser["dReimbursement"][$triwulan];
                                        if (!empty($dGUReim)) {
                                            $reimKey = $dGUReim["reim_key"];
                                            $reimStatus = $dGUReim["reim_status"];
                                            $dReimStatus = masterReimbursementStatus($reimStatus, true);
                                            $lblReimStatus = $dReimStatus["label"];
                                            $colorReimStatus = $dReimStatus["color"];
                                        }
                                        if ($triwulan > $currentTriwulan) {
                                            $btn  = <<<BTN
                                                <a href="javascript::void" class="btn btn-sm btn-danger disabled" >
                                                    <i class="fas fa-eye-slash"></i> Belum
                                                </a>
                                            BTN;
                                        } else if ($triwulan <= $currentTriwulan) {
                                            if (!empty($dGUReim)) {
                                                $url = base_url("reimbursement/view?reim_key={$reimKey}");
                                                $btn  = <<<BTN
                                                    <a href="{$url}" class="btn btn-sm bg-{$colorReimStatus}" target="_blank">
                                                        <i class="fas fa-eye"></i> {$lblReimStatus}
                                                    </a>
                                                BTN;
                                            } else {
                                                if ($vSub["sw_is_locked"]) {
                                                    $btn  = <<<BTN
                                                        <a href="javascript::void" class="btn btn-sm btn-danger disabled" >
                                                            <i class="fas fa-lock"></i> Dikunci
                                                        </a>
                                                    BTN;
                                                } else {
                                                    $url = base_url("reimbursement/create?triwulan={$triwulan}&tahun={$tahun}&usr_key={$usrKey}");
                                                    $btn  = <<<BTN
                                                        <a href="{$url}" class="btn btn-sm btn-dark" target="_blank">
                                                            <i class="fas fa-plus-circle"></i> Ajukan
                                                        </a>
                                                    BTN;
                                                }
                                            }
                                        } else {
                                        }
                                    ?>
                                        <td class="text-center <?= $colColor; ?>">
                                            <?= $btn; ?>
                                        </td>
                                    <?php
                                    }
                                    ?>
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
<?= appViewInjectScript("user", "show_preview_script"); ?>
<script>
    $("#dtbl_group_users").DataTable({
        scrollX: true,
        fixedHeader: true,
        "responsive": false,
        "order": [
            ['0', 'desc']
        ],
        fixedColumns: {
            left: 0,
        },
        // "iDisplayLength": 10,

        "columnDefs": [{}, ]
    });
</script>
<?= $this->endSection(); ?>