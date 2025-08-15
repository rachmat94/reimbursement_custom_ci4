<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<a href="<?= base_url("reimbursement"); ?>" class="btn btn-outline-dark"><i class="fas fa-backward"></i> Back</a>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row mb-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <?= appRenderTableInfo2([
                    [
                        "name" => "Kode:",
                        "value" => $dReimbursement["reim_code"],
                    ],
                    [
                        "name" => "Status:",
                        "value" => masterReimbursementStatus($dReimbursement["reim_status"], true)["label"],
                    ],
                    [
                        "name" => "Tanggal Dibuat:",
                        "value" => appFormatTanggalIndonesia($dReimbursement["reim_created_at"], true),
                    ],
                    [
                        "name" => "Tanggal Diajukan:",
                        "value" => appFormatTanggalIndonesia($dReimbursement["reim_diajukan_pada"], true),
                    ],
                    [
                        "name" => "Terakhir diubah:",
                        "value" => appFormatTanggalIndonesia($dReimbursement["reim_updated_at"], true),
                    ],
                    [
                        "name" => "Triwulan:",
                        "value" => "Triwulan " . $dReimbursement["reim_triwulan_no"] . ", " . $dReimbursement["reim_triwulan_tahun"],
                    ],
                    [
                        "name" => "Jumlah Nominal:",
                        "value" => appFormatRupiah($dReimbursement["reim_amount"]),
                    ],
                    [
                        "name" => "Kategori:",
                        "value" => $dReimbursement["cat_name"],
                    ],
                    [
                        "name" => "Dibuat Oleh: ",
                        "value" => $dReimbursement["uby_usr_username"],
                    ],
                    [
                        "name" => "Diajukan untuk:",
                        "value" => $dReimbursement["uc_usr_username"],
                    ],
                    [
                        "name" => "Pernah Revisi:",
                        "value" => (!empty($dReimbursement["reim_ever_revised_at"])) ? "Ya - " . $dReimbursement["reim_ever_revised_at"] : "Tidak",
                    ]
                ]); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6>Berkas:</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" style="width: 100%;">
                        <?php
                        foreach ($dReimBerkas ?? [] as $kBerkas => $vBerkas) {
                        ?>
                            <tr>
                                <td><?= $vBerkas["jb_name"]; ?></td>
                                <td>
                                    <a role="button" href="#" onclick="showPreviewReimBerkas('<?= $vBerkas['rb_key']; ?>')"><?= $vBerkas["rb_file_name"]; ?></a>
                                    <details>
                                        <summary>Catatan / Keterangan</summary>
                                        <p>
                                            <?= $vBerkas["rb_note"]; ?>
                                        </p>
                                    </details>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Detail:</h6>
            </div>
            <div class="card-body">
                <?= $dReimbursement["reim_detail"]; ?>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <?php
        if (
            $dReimbursement["reim_status"] == "draft"
        ) {
        ?>
            <div class="card bg-warning">
                <div class="card-body">
                    <h5>Ubah Data</h5>
                    <a href="<?= base_url('reimbursement/draft?reim_key=' . $dReimbursement['reim_key']); ?>" class="btn bg-white"><i class="fas fa-edit"></i> Ubah</a>
                </div>
            </div>

            <?php
        } else if (
            $dReimbursement["reim_status"] == "diajukan"
        ) {
            if ($dAccess["data"]["usr_role"] == "validator" || $dAccess["data"]["usr_id"] == authMasterUserId()) {
            ?>
                <div class="card bg-warning">
                    <div class="card-body ">
                        <h5>Validasi:</h5>
                        <button class="btn bg-white " onclick="doStartValidate('<?= $dReimbursement['reim_key']; ?>')"><i class="fas fa-check-circle"></i> Saya akan memulai validasi data ini</button>
                    </div>
                </div>
            <?php
            } else if ($dAccess["data"]["usr_role"] == "admin_group") {
            ?>
                <div class="card bg-info">
                    <div class="card-body">
                        <h5>Pengajuan telah diajukan</h5>
                        <p>Silahkan tunggu Admin Validator untuk memvalidasi pengajuan anda.</p>
                    </div>
                </div>
            <?php
            }
        } else if (
            $dReimbursement["reim_status"] == "validasi"
        ) {
            if ($dAccess["data"]["usr_role"] == "validator" || $dAccess["data"]["usr_id"] == authMasterUserId()) {
            ?>
                <div class="card bg-warning">
                    <div class="card-body ">
                        <h5><i class="fas fa-clipboard-check"></i> Validasi Reimbursement</h5>
                        <a href="<?= base_url('reimbursement/validation?reim_key=' . $dReimbursement['reim_key']); ?>"
                            class="btn btn-success">
                            <i class="fas fa-clipboard-check"></i> Lanjutkan Validasi
                        </a>

                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="card bg-dark">
                    <div class="card-body ">
                        <h5><i class="fas fa-clipboard-check"></i> Pengajuan Anda sedang divalidasi</h5>
                        <p class="">Mohon tunggu dan periksa secara berkala.</p>

                    </div>
                </div>
            <?php
            }
        } else if ($dReimbursement["reim_status"] == "revisi") {
            ?>
            <div class="card bg-warning">
                <div class="card-body ">
                    <h5>Pengajuan ini perlu direvisi:</h5>
                    <?php
                    if ($dAccess["data"]["usr_role"] == "admin_group" || $dAccess["data"]["usr_id"] == authMasterUserId()) {
                    ?>
                        <a href="<?= base_url('reimbursement/revision?reim_key=' . $dReimbursement['reim_key']); ?>" class="btn btn-dark"><i class="fas fa-edit"></i> Lihat</a>
                    <?php
                    }
                    ?>
                </div>
            </div>

        <?php
        } else if (in_array($dReimbursement["reim_status"], ["disetujui"])) {
        ?>
            <div class="card bg-success">
                <div class="card-body pb-2">
                    <h5>Pengajuan ini disetujui:</h5>
                    <p></p>
                    <a href="<?= base_url('reimbursement/print?reim_key=' . $dReimbursement['reim_key']); ?>&pdf=1" class="btn btn-danger" target="_blank"><i class="fas fa-file-pdf"></i> Download / Print</a>

                    <?php
                    if ($dAccess["data"]["usr_role"] == "validator" || $dAccess["data"]["usr_id"] == authMasterUserId()) {
                    ?>
                        <button class="btn btn-dark " onclick="showEditPayment('<?= $dReimbursement['reim_key']; ?>')"><i class="fas fa-edit"></i> Bukti pembayaran</button>
                    <?php
                    }
                    ?>
                </div>
                <div class="card-body bg-white border ">
                    <h6>Pembayaran:</h6>
                    <?= appRenderTableInfo2([
                        [
                            "name" => "Status:",
                            "value" => appRenderBadgeStatus($dReimbursement["reim_is_paid"], ["Belum dibayarkan", "Sudah dibayarkan"]),
                        ],
                    ]); ?>
                </div>
            </div>

            <?php

            if ($dAccess["data"]["usr_role"] == "validator" || $dAccess["data"]["usr_role"] == "admin_group" || $dAccess["data"]["usr_id"] == authMasterUserId()) {
            ?>
                <div class="card bg-danger mt-5">
                    <div class="card-body ">
                        <h5>Terjadi kesalahan & harus divalidasi ulang?</h5>
                        <?php
                        if ($dAccess["data"]["usr_id"] == authMasterUserId()) {
                        ?>
                            <p></p>
                            <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_back_to_validation'); ?>" id="back_to_validation_reim_form" enctype="multipart/form-data">
                                <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement["reim_id"]; ?>">
                                <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement["reim_key"]; ?>">

                                <button type="submit" class="btn bg-white"><i class="fas fa-sync"></i> Kembalikan ke Validasi</button>
                            </form>
                        <?php
                        } else {
                        ?>
                            <p>Silahkan hubungi Master untuk mengembalikan status nya menjadi Validasi.</p>
                        <?php
                        }
                        ?>

                    </div>
                </div>
        <?php
            }
        }

        if ($dAccess["data"]["usr_id"] == authMasterUserId()) {
            echo appViewInjectContent("reimbursement", "delete_card");
        } else {
            if (in_array($dReimbursement["reim_status"], ["draft", "revisi"])) {
                if($dAccess["data"]["usr_role"] == "admin_group"){
                    echo appViewInjectContent("reimbursement", "delete_card");
                }
            }
        }


        ?>

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
<?= appViewInjectScript("reimbursement", "berkas/show_preview_script"); ?>
<?= appViewInjectScript("reimbursement", "do_start_validate_script"); ?>
<?= appViewInjectScript("reimbursement", "show_edit_payment_script"); ?>
<?= appViewInjectScript("reimbursement", "submit_delete_script"); ?>
<?php
if ($dReimbursement["reim_status"] == "disetujui" && $dAccess["data"]["usr_id"] == authMasterUserId()) {
?>
    <?= appViewInjectScript("reimbursement", "submit_back_to_validation_script"); ?>
<?php
}
?>
<?= $this->endSection(); ?>