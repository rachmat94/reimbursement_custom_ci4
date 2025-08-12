<?= $this->extend(appViewLayoutFile()) ?>

<?= $this->section("head"); ?>
<link rel="stylesheet" href="<?= assetUrl(); ?>plugins/summernote/summernote-bs4.min.css">
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
                        "value" => $dReimbursement["reim_amount"],
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
        <h4 class="mb-3">Hasil Akhir Pengecekan:</h4>
    </div>
    <div class="col-lg-12">
        <div class="card bg-warning">
            <div class="card-body ">
                <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_as_revision'); ?>" id="as_revision_reimbursement_form" enctype="multipart/form-data">
                    <h5>Revisi:</h5>
                    <div class="form-group">
                        <label for="txt_revision_note">Detail Revisi</label>
                        <textarea name="txt_revision_note" id="txt_revision_note" class="form-control"><?= $dRevision['rrev_note'] ?? ""; ?></textarea>
                    </div>
                    <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement['reim_id']; ?>">
                    <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement["reim_key"]; ?>">

                    <input type="hidden" name="btn_action" id="btn_action">
                    <button class="btn btn-primary " type="submit" onclick="$('#btn_action').val('save')"><i class="fas fa-save"></i> Simpan perubahan detail revisi</button>
                    <button class="btn btn-dark " type="submit" onclick="$('#btn_action').val('as_revision')"> Jadikan harus direvisi</button>
                </form>
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
<script src="<?= assetUrl(); ?>plugins/summernote/summernote-bs4.min.js"></script>
<?= $this->endSection(); ?>
<?= $this->section("script_1"); ?>
<?= appViewInjectScript("reimbursement", "berkas/show_preview_script"); ?>
<?= appViewInjectScript("reimbursement", "do_start_validate_script"); ?>
<?= appViewInjectScript("reimbursement", "submit_as_revision_script"); ?>
<script>
    $(function() {
        $('#txt_revision_note').summernote({
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
<?= $this->endSection(); ?>