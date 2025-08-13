<?php

?>
<div class="modal fade " id="edit_payment_reim_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Pembayaran
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_edit_payment'); ?>" id="edit_payment_reim_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement["reim_id"]; ?>">
                    <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement["reim_key"]; ?>">
                    <div class="form-group">
                        <label for="cbo_status_payment">Status</label>
                        <select name="cbo_status_payment" id="cbo_status_payment" class="select2 form-control form-control-sm" style="width: 100%;">
                            <option value="">-</option>
                            <option value="1" <?= ($dReimbursement['reim_is_paid'] == 1) ? " selected " : ""; ?>>Sudah dibayarkan</option>
                            <option value="0" <?= ($dReimbursement['reim_is_paid'] == 0) ? " selected " : ""; ?>>Belum dibayarkan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dt_payment">Tanggal Pembayaran</label>
                        <input type="date" name="dt_payment" id="dt_payment" class="form-control form-control-sm" value="<?= $dReimbursement["reim_paid_at"]; ?>">
                    </div>
                    <div class="form-group">
                        <label for="file_payment">Bukti Pembayaran</label><br>
                        <?php
                        if (!empty($dFilePayment["file_url"])) {
                        ?>
                            <details class="mb-2">
                                <summary><?= $dFilePayment["file_name"]; ?></summary>

                                <?php
                                $fUrl = $dFilePayment["file_url"];
                                echo $dFilePayment["message"];
                                if ($dFilePayment["file_category"] == "pdf") {
                                ?>
                                    <div class="p-2" style="height:70vh;">
                                        <iframe id="pdfViewer" src="<?= $fUrl; ?>" style="width:100%; height:100%;" frameborder="0"></iframe>
                                    </div>
                                <?php
                                } else if ($dFilePayment["file_category"] == "image") {
                                ?>
                                    <div class="" style="text-align:center; overflow:auto; height:70vh;">
                                        <img id="imagePreview" src="<?= $fUrl; ?>" style="max-width:100%; transform: scale(1); transform-origin: center center;">
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-secondary" type="button" onclick="zoomOutImage()">- Zoom Out</button>
                                        <button class="btn btn-secondary" type="button" onclick="zoomInImage()">+ Zoom In</button>
                                        <button class="btn btn-secondary" type="button" onclick="resetZoom()">Reset</button>
                                    </div>
                                <?php
                                } else {
                                }
                                ?>
                                <a href="<?= $dFilePayment["file_url"]; ?>" target="_blank" class="mt-3 btn btn-link">New Tab <i class="fas fa-external-link-alt"></i></a>

                                <button type="button" onclick="doDelFilePayment('<?= $dReimbursement['reim_key']; ?>')" target="_blank" class="mt-3 btn btn-link text-danger">Delete <i class="fas fa-times"></i></button>
                            </details>
                        <?php
                        }
                        ?>
                        <input type="file" name="file_payment" id="file_payment" class="form-control form-control-sm">
                    </div>
                    <div class="form-group row">
                        <label for="txt_detail" class="col-sm-12 col-form-label">Detail Pembayaran:</label>
                        <div class="col-sm-12">
                            <textarea class="form-control form-control-sm" id="txt_detail" name="txt_detail" placeholder=""><?= $dReimbursement["reim_paid_detail"]; ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>