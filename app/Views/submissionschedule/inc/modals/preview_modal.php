<div class="modal fade " id="preview_submissionschedule_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Jadwal Pengajuan
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                 <?= appRenderTableInfo3([
                    [
                        "name" => "Tahun:",
                        "value" => $dSubmissionSchedule["sw_tahun"],
                    ],
                    [
                        "name" => "Triwulan:",
                        "value" => $dSubmissionSchedule["sw_triwulan"],
                    ],
                    [
                        "name" => "Dikunci:",
                        "value" => appRenderBadgeLocked($dSubmissionSchedule["sw_is_locked"]),
                    ],
                    [
                        "name" => "Tgl. Dimulai Pengajuan:",
                        "value" => $dSubmissionSchedule["sw_start_date"],
                    ],
                    [
                        "name" => "Tgl. Akhir Pengajuan:",
                        "value" => $dSubmissionSchedule["sw_end_date"],
                    ]
                ]); ?>

            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>