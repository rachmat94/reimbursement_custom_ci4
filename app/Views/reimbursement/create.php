<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<link rel="stylesheet" href="<?= assetUrl(); ?>plugins/summernote/summernote-bs4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('header'); ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<?php
if (!$isValid) {
?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <h5>Buat Pengajuan</h5>
                </div>
                <div class="card-body">
                    <p>
                        Proses pengisian data tidak dapat dilakukan:<br>
                        <?= $message ?? ""; ?>
                    </p>
                    <a href="<?= base_url('reimbursement'); ?>" class="btn btn-link">Kembali</a>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_create'); ?>" id="create_reimbursement_form" enctype="multipart/form-data">
                    <div class="card-body border">
                        <h5>Buat Pengajuan</h5>
                        <dl>
                            <dt>Triwulan: <?= $dSubmissionSchedule["sw_triwulan"]; ?>, <?= $dSubmissionSchedule["sw_tahun"]; ?></dt>
                            <dt>Group: <?= $dAccess["data"]["group_name"] ?? "-"; ?></dt>
                        </dl>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="hdn_subschedule_id" value="<?= $dSubmissionSchedule['sw_id']; ?>">
                        <div class="form-group row">
                            <label for="btn_user" class="col-sm-3 col-form-label">User</label>
                            <div class="col-sm-9">
                                <div id="div_selected_user">
                                    <?php
                                    if ($withClaimant) {
                                        echo $vUSelected;
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="showSelectUser();">Pilih User</button>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cbo_category" class="col-sm-3 col-form-label">Kategori</label>
                            <div class="col-sm-9">
                                <select name="cbo_category" id="cbo_category" class="form-control select2" style="width: 100%;">
                                    <option value="">-</option>
                                    <?php
                                    foreach ($dCategories as $kCategory  => $vCategory) {
                                    ?>
                                        <option value="<?= $vCategory["cat_id"]; ?>">
                                            [ <?= $vCategory["cat_code"]; ?> ] <?= $vCategory["cat_name"]; ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nbr_nominal" class="col-sm-3 col-form-label">Total Nominal</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control form-control-sm" id="nbr_nominal" name="nbr_nominal" placeholder="" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txt_detail" class="col-sm-12 col-form-label">Detail</label>
                            <div class="col-sm-12">
                                <textarea class="form-control form-control-sm" id="txt_detail" name="txt_detail" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6>Berkas:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hovered" style="width: 100%;">
                                <?php
                                foreach ($dJenisBerkas ?? [] as $kJb => $vJb) {
                                ?>
                                    <tr>
                                        <td>
                                            <h6><?= $vJb["jb_name"]; ?></h6>
                                            <details>
                                                <summary>Deskripsi:</summary>
                                                <p>
                                                    <?= $vJb["jb_description"]; ?>
                                                </p>
                                            </details>

                                        </td>
                                        <td style="width: 100px;">
                                            <?= ($vJb["jb_is_required"] == 1) ? "Wajib" : "Tidak Wajib"; ?>
                                        </td>
                                        <td style="width: 160px;">
                                            File Pdf atau gambar<br>
                                            <?= "Makimal: " .  $vJb["jb_max_file_size_mb"] . " Mb"; ?>
                                        </td>
                                        <td style="width: 300px;">
                                            <input type="file" class="form-control form-control-sm" name="file_jb[<?= $vJb['jb_id']; ?>]" id="file_jb_<?= $vJb['jb_id']; ?>">
                                            <details class="mt-2">
                                                <summary>Tambah Catatan / Keterangan:</summary>
                                                <p>
                                                    <textarea name="txt_file_note[<?= $vJb['jb_id']; ?>]" id="txt_file_note_<?= $vJb['jb_id']; ?>" class="form-control form-control-sm"></textarea>
                                                </p>
                                            </details>
                                        </td>
                                        <td style="width: 60px;">
                                            <button type="button" onclick="clearFileInput('file_jb_<?= $vJb['jb_id']; ?>')" class="btn btn-link text-danger">Clear</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group row">
                            <label for="cbo_action" class="col-sm-2 col-form-label">Pilih Aksi</label><span class="text-muted"></span>
                            <div class="col-sm-6">
                                <select name="cbo_action" id="cbo_action" class="select2  form-control " style="width: 100%;">
                                    <option value="as_draft_create">Simpan sebagai Draft dan Buat Lagi</option>
                                    <option value="as_draft">Simpan sebagai Draft</option>
                                    <option value="do_submit">Ajukan Sekarang</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-outline-dark btn-sm">Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>
<?= $this->endSection(); ?>

<?= $this->section("footer"); ?>

<?= $this->endSection(); ?>

<?= $this->section("modal"); ?>

<?= $this->endSection(); ?>
<?= $this->section("script_0"); ?>
<script src="<?= assetUrl(); ?>plugins/summernote/summernote-bs4.min.js"></script>
<?= $this->endSection(); ?>
<?= $this->section("script_1"); ?>
<script>
    $(function() {
        $('#txt_detail').summernote({
            height: 200,
            toolbar: [
                // ['group name', [list of buttons]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
                // Tidak ada 'insert' => menghilangkan tombol gambar, video, dll
            ]
        })
    })
</script>
<?= appViewInjectScript("reimbursement", "submit_create_script"); ?>
<?= $this->endSection(); ?>