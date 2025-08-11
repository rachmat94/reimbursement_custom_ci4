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
                    <h5>Draft</h5>
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
                <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_save_draft'); ?>" id="draft_reimbursement_form" enctype="multipart/form-data">
                    <div class="card-body border">
                        <h5>Draft Pengajuan</h5>
                        <dl>
                            <dt>Triwulan: <?= $dReimbursement["reim_triwulan_no"]; ?>, <?= $dReimbursement["reim_triwulan_tahun"]; ?></dt>
                            <dt>Group: <?= $dAccess["data"]["group_name"] ?? "-"; ?></dt>
                        </dl>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement['reim_id']; ?>">
                        <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement['reim_key']; ?>">

                        <div class="form-group row">
                            <label for="btn_user" class="col-sm-3 col-form-label">User</label>
                            <div class="col-sm-9">
                                <div id="div_selected_user">
                                    <?php
                                    if (!empty($vUSelected)) {
                                        echo $vUSelected;
                                    } else {
                                        echo $dReimbursement["reim_claimant_usr_id"];
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
                                        <option value="<?= $vCategory["cat_id"]; ?>" <?= ($vCategory["cat_id"] == $dReimbursement["reim_cat_id"]) ? " selected " : ""; ?>>
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
                                <input type="number" class="form-control form-control-sm" id="nbr_nominal" name="nbr_nominal" placeholder="" value="<?= $dReimbursement["reim_amount"]; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txt_detail" class="col-sm-12 col-form-label">Detail</label>
                            <div class="col-sm-12">
                                <textarea class="form-control form-control-sm" id="txt_detail" name="txt_detail" placeholder=""><?= $dReimbursement["reim_detail"]; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= d($inJB); ?>
                        <h6>Berkas:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover border" style="width: 100%;">
                                <?php
                                foreach ($dJenisBerkas ?? [] as $kJb => $vJb) {
                                    $dReimBerkas = $vJb["dReimBerkas"];

                                    if (empty($dReimBerkas)) {
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
                                            <?php
                                            /*
                                            <td style="width: 300px;">
                                                <input type="file" class="form-control form-control-sm" name="file_jb[<?= $vJb['jb_id']; ?>]" id="file_jb_<?= $vJb['jb_id']; ?>">
                                                <p class="text-danger">* File lama akan dihapus dan digantikan dengan yang baru diupload.</p>
                                                <details class="mt-2">
                                                    <summary>Tambah Catatan / Keterangan:</summary>
                                                    <p>
                                                        <textarea name="txt_file_note[<?= $vJb['jb_id']; ?>]" id="txt_file_note_<?= $vJb['jb_id']; ?>" class="form-control form-control-sm"></textarea>
                                                    </p>
                                                </details>

                                            </td>
                                            */
                                            ?>
                                            <td style="width: 160px;">
                                                <?php
                                                /*
                                                <button type="button" onclick="clearFileInput('file_jb_<?= $vJb['jb_id']; ?>')" class="btn btn-link text-danger">Clear</button>
                                                */
                                                ?>
                                                <button type="button" class="btn btn-dark btn-sm mt-2" onclick="showUploadReimBerkas('<?= $dReimbursement['reim_key']; ?>')"><i class="fas fa-upload"></i> Upload Berkas</button>
                                            </td>
                                        </tr>
                                    <?php
                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="5">
                                                <ul>
                                                    <?php
                                                    foreach ($dReimBerkas as $vBerkas) {
                                                    ?>
                                                        <li>
                                                            <h6><?= $vJb["jb_name"]; ?> | <?= $vBerkas["rb_file_name"]; ?></h6>
                                                            <b>Catatan:</b>
                                                            <p>
                                                                <?= $vBerkas["rb_note"]; ?>
                                                            </p>
                                                            <p>
                                                                <b>Diupload Pada:</b> <?= appFormatTanggalIndonesia($vBerkas["rb_created_at"]); ?><br>
                                                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="doDelReimBerkas('<?= $vBerkas['rb_key']; ?>')">Hapus Berkas</button>
                                                                <button type="button" class="btn btn-warning btn-sm mt-2" onclick="showEditReimBerkas('<?= $vBerkas['rb_key']; ?>')">Edit Berkas</button>
                                                            </p>
                                                        </li>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="5">
                                            &nbsp;
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="btn_action" id="btn_action">
                        <button type="submit" onclick="$('#btn_action').val('save_draft')" class="btn btn-primary">Simpan Draft</button>
                        <button type="submit" onclick="$('#btn_action').val('save_ajukan')" class="btn btn-dark">Simpan dan Ajukan Sekarang</button>
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
<?= appViewInjectScript("reimbursement", "submit_save_draft_script"); ?>
<?= appViewInjectScript("reimbursement", "do_delete_reimberkas_script"); ?>
<?= $this->endSection(); ?>