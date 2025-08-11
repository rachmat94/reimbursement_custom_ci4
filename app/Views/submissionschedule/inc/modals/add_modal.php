<div class="modal fade " id="add_submissionschedule_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Add Jadwal Pengajuan
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('submission-schedule/do_add'); ?>" id="add_submissionschedule_form">
                <div class="modal-body table-responseive">
                    <?= mycsrfTokenField(); ?>
                    <div class="form-group row">
                        <label for="cbo_tahun" class="col-sm-4 col-form-label">Tahun</label>
                        <div class="col-sm-8">
                            <select name="cbo_tahun" id="cbo_tahun" class="from-control select2" style="width: 100%;">
                                <?php
                                for ($i = date("Y"); $i >= 2015; $i--) {
                                ?>
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cbo_triwulan" class="col-sm-4 col-form-label">Triwulan ke</label>
                        <div class="col-sm-8">
                            <select name="cbo_triwulan" id="cbo_triwulan" class="from-control select2" style="width: 100%;">
                                <?php
                                for ($q = 1; $q <= 4; $q++) {
                                ?>
                                    <option value="<?= $q; ?>"><?= $q; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cob_locked" class="col-sm-4 col-form-label">Kunci</label><span class="text-muted"></span>
                        <div class="col-sm-8">
                            <select name="cbo_locked" id="cbo_locked" class="select2  form-control " style="width: 100%;">
                                <option value="1">Dikunci</option>
                                <option value="0">Dibuka</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dt_start" class="col-sm-4 col-form-label">Tgl. Pengajuan dimulai</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control form-control-sm" id="dt_start" name="dt_start" placeholder="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dt_end" class="col-sm-4 col-form-label">Tgl. Akhir Pengajuan</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control form-control-sm" id="dt_end" name="dt_end" placeholder="">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark px-3 my-1"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>