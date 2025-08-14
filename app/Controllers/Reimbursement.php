<?php

namespace App\Controllers;

use App\Libraries\FileLimiter;
use App\Models\BerkasModel;
use App\Models\CategoryModel;
use App\Models\JenisBerkasModel;
use App\Models\ReimbursementModel;
use App\Models\RevisionModel;
use App\Models\SubmissionWindowModel;
use App\Models\UserModel;
use Exception;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class Reimbursement extends BaseController
{
    private $cnm = "reimbursement";
    private $cnmSlug = "reimbursement";
    private $viewDir = "reimbursement";
    private $title = "Reimbursement";
    private $pageHeader = "Reimbursement";
    private $cData;

    private $SubmissionScheduleModel;
    private $JenisBerkasModel;
    private $UserModel;
    private $ReimbursementModel;
    private $CategoryModel;
    private $BerkasModel;
    private $RevisionModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];

        $this->SubmissionScheduleModel = new SubmissionWindowModel();
        $this->JenisBerkasModel = new JenisBerkasModel();
        $this->UserModel = new UserModel();
        $this->ReimbursementModel = new ReimbursementModel();
        $this->CategoryModel = new CategoryModel();
        $this->BerkasModel = new BerkasModel();
        $this->RevisionModel = new RevisionModel();
    }

    public function doSelectUser()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $userId = $this->request->getPost("usr_id");
            if (empty($userId)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_id" => $userId,
            ], true);
            if (empty($dUser)) {
                throw new Exception("User tidak ditemukan.", 400);
            }

            $usrCode = $dUser["usr_code"];
            $usrUsername = $dUser["usr_username"];
            $usrEmail = $dUser["usr_email"];
            $usrRole = $dUser["usr_role"];
            $usrGroup = $dUser["group_name"];
            $usrCategory = $dUser["usr_group_category"];
            $vSelected = <<<VIEW
            <div class="p-2">
                <dl>
                    <dt>[ {$usrCode} ] @{$usrUsername} {$usrEmail}</dt>
                    <dd>Role: {$usrRole} | Group: {$usrGroup} | Kategori User: {$usrCategory}</dd>
                </dl>

                <button type="button" class="btn btn-sm btn-outline-danger py-2" onclick="doRemoveSelectedUser()">Hapus</button>
            </div>
            <input type="hidden" name="hdn_user_id" value="{$userId}">
            VIEW;
            $view = $vSelected;
            $script = "";
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function showSelectUser()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];
            $dWhere = [];
            if ($sessUsrId != authMasterUserId()) {
                $dWhere["usr_group_id"] = $sessUsrId;
                $dWhere["usr_is_active"] = 1;
            }
            $dUsers = $this->UserModel->get($dWhere);
            $dView = [
                "viewDir" => $this->viewDir,
                "dUsers" => $dUsers,
            ];
            $view = appViewInjectModal($this->viewDir, "select_user_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "do_select_user_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doCreate()
    {
        $this->_onlyPostandAjax();
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Anda tida punya akses.", 400);
            }

            $userId = $this->request->getPost("hdn_user_id") ?? null;
            $subScheduleId = $this->request->getPost("hdn_subschedule_id") ?? null;
            $nominal = $this->request->getPost("nbr_nominal") ?? null;
            $detail = $this->request->getPost("txt_detail") ?? "";
            $action = $this->request->getPost("cbo_action") ?? "";
            $categoryId = $this->request->getPost("cbo_category") ?? "";
            $jbFiles = $this->request->getFiles();

            if (empty($userId)) {
                throw new Exception("Tidak ada user yang dipilih.", 400);
            }

            $dUser = $this->UserModel->get([
                "usr_id" => $userId,
            ], true);
            if (empty($dUser)) {
                throw new Exception("User yang dipilih tidak ada.", 400);
            }
            if ($sessUsrId != authMasterUserId()) {
                if (!empty($sessGroupId)) {
                    if ($sessGroupId != $dUser["usr_group_id"]) {
                        throw new Exception("Anda hanya boleh mengisi untuk group " . $sessGroupName, 400);
                    }
                } else {
                    throw new Exception("Akun anda tidak memiliki Group ", 400);
                }
            }

            if (empty($subScheduleId)) {
                throw new Exception("Jadwal triwulan tidak ada.", 400);
            }
            $dSubmissionSchedule = $this->SubmissionScheduleModel->get([
                "sw_id" => $subScheduleId,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data jadwal triwulan tidak ada.", 400);
            }
            if (empty($nominal) || $nominal < 0) {
                throw new Exception("Isi total nominal dengan benar.", 400);
            }
            if (empty($detail)) {
                throw new Exception("Detail harus diisi.", 400);
            }
            if (!in_array($action, ["as_draft_create", "as_draft", "do_submit"])) {
                throw new Exception("Aksi tidak valid.", 400);
            }
            if (!isset($jbFiles['file_jb']) || !is_array($jbFiles['file_jb'])) {
                throw new Exception("Tidak ada berkas yang dikirim", 400);
            }

            if (!empty($categoryId)) {
                $dCategory = $this->CategoryModel->get([
                    "cat_id" => $categoryId,
                    "cat_is_active" => 1,
                ], true);
                if (empty($dCategory)) {
                    throw new Exception("Data kategori sudah tidak ditemukan atau dinonaktifkan.", 400);
                }
            }

            $key = $this->ReimbursementModel->generateKey();
            $code = $this->ReimbursementModel->generateCode();
            $swTriwulan = $dSubmissionSchedule["sw_triwulan"];
            $swTahun =  $dSubmissionSchedule["sw_tahun"];

            $dExist = $this->ReimbursementModel->get([
                "reim_triwulan_no" => $swTriwulan,
                "reim_triwulan_tahun" => $swTahun,
                "reim_claimant_usr_id" => $userId,
            ], true);
            if ($dExist) {
                throw new Exception("User " . $dUser["usr_username"] . " sudah memiliki data Reimbursement Triwulan " . $swTriwulan . ", " . $swTahun, 400);
            }
            $dAdd = [
                "reim_key" => $key,
                "reim_code" => $code,
                "reim_by_usr_id" => $sessUsrId,
                "reim_claimant_usr_id" => $userId,
                "reim_cat_id" => $categoryId,
                "reim_amount" => $nominal,
                "reim_detail" => $detail,
                "reim_triwulan_no" => $swTriwulan,
                "reim_triwulan_tahun" => $swTahun,
                "reim_sw_id" => $subScheduleId,
                "reim_created_at" => appCurrentDateTime(),
            ];

            $status = "draft";
            $redirect = base_url("reimbursement");
            if ($action == "do_submit") {
                $status = "diajukan";
                $redirect = base_url("reimbursement/view?reim_key=" . $key);
            } else if ($action == "as_draft") {
                $status = "draft";
                $redirect = base_url("reimbursement/view?reim_key=" . $key);
            } else if ($action == "as_draft_create") {
                $status = "draft";
                $redirect = base_url("reimbursement/create?triwulan=" . $swTriwulan . "&tahun=" . $swTahun);
            } else {
                throw new Exception("Invalid action.", 400);
            }
            $dAdd["reim_status"] = $status;

            $newReimId = $this->ReimbursementModel->add($dAdd);

            if ($newReimId !== false && $newReimId > 0) {
                $doRollback = false;
                $uploadedFiles = [];

                foreach ($jbFiles['file_jb'] as $id => $file) :
                    $jenisBerkasId = $id;

                    // Cek jika tidak ada ID jenis berkas
                    if (empty($jenisBerkasId)) {
                        log_message("alert", "ID jenis berkas kosong.");
                        $doRollback = true;
                        continue;
                    }

                    $dJenisBerkas = $this->JenisBerkasModel->get([
                        "jb_id" => $jenisBerkasId,
                    ], true);

                    if (empty($dJenisBerkas)) {
                        log_message("alert", "Jenis berkas dengan ID $jenisBerkasId tidak ditemukan.");
                        $doRollback = true;
                        continue;
                    }

                    $jbIsRequired = (bool) $dJenisBerkas["jb_is_required"];
                    $jbMaxFileSizeMb = $dJenisBerkas["jb_max_file_size_mb"];
                    $maxSizeBytes = $jbMaxFileSizeMb * 1024 * 1024;

                    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                    $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];

                    // --- CEK APAKAH FILE DIUPLOAD --- //
                    if ($file->getError() === 4) { // Tidak ada file yang diupload
                        if ($jbIsRequired) {
                            log_message("alert", "Berkas $jenisBerkasId wajib diunggah, tapi tidak ada file.");
                            $doRollback = true;
                        } else {
                            log_message("info", "Berkas $jenisBerkasId bersifat opsional, dan tidak diunggah.");
                        }
                        continue;
                    }

                    // --- CEK VALIDITAS FILE --- //
                    if ($file->isValid() && !$file->hasMoved()) {
                        // Validasi ukuran
                        if ($file->getSize() > $maxSizeBytes) {
                            log_message("alert", "File untuk ID $id melebihi batas ukuran maksimum {$jbMaxFileSizeMb}MB.");
                            $doRollback = true;
                            continue;
                        }

                        // Validasi ekstensi dan mime
                        $ext = strtolower($file->getExtension());
                        $mime = $file->getMimeType();

                        if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimeTypes)) {
                            log_message("alert", "File ID $id memiliki tipe yang tidak diizinkan. Ext: $ext, Mime: $mime.");
                            $doRollback = true;
                            continue;
                        }

                        $claimantUGroupId = $dUser["group_id"];
                        $claimantUGroupKey = $dUser["group_key"];
                        $newName = $file->getRandomName();
                        $uploadPath = appConfigDataPath("reimbursement/berkas/" . $swTahun . "/" . "triwulan_" . $swTriwulan . "/" . $claimantUGroupKey);
                        log_message("alert", "Reim upload path =" . $uploadPath);
                        $filePath = $uploadPath . "/" . $newName;
                        $file->move($uploadPath, $newName);
                        if (!file_exists($filePath)) {
                            $doRollback = true;
                            log_message("alert", "File untuk ID $id tidak valid atau gagal diproses.");
                            continue;
                        }
                        $uploadedFiles[] = $newName;
                        $rbKey = $this->BerkasModel->generateKey();
                        $rbNote = $this->request->getPost("txt_file_note[" . $id . "]") ?? "";
                        $dAddBerkas = [
                            "rb_key" => $rbKey,
                            "rb_by_usr_id" => $sessUsrId,
                            "rb_reim_id" => $newReimId,
                            "rb_jb_id" => $jenisBerkasId,
                            "rb_file_name" => $newName,
                            "rb_file_name_origin" => $file->getClientName(),
                            "rb_note" => $rbNote,
                            "rb_created_at" => appCurrentDateTime(),
                        ];
                        $rbId = $this->BerkasModel->add($dAddBerkas);
                        if ($rbId > 0) {
                            log_message("alert", "File untuk ID $id berhasil diupload dengan nama $newName.");
                        } else {
                            log_message("alert", "File untuk ID $id tidak valid atau gagal diproses.");
                            $doRollback = true;
                            continue;
                        }
                    } else {
                        log_message("alert", "File untuk ID $id tidak valid atau gagal diproses.");
                        $doRollback = true;
                        continue;
                    }
                endforeach;

                // Jika terjadi error pada salah satu file
                if ($doRollback) {
                    // Lakukan rollback jika kamu pakai transaksi
                    $this->ReimbursementModel->del([
                        "reim_id" => $newReimId,
                    ]); // atau rollback lainnya
                    throw new Exception("Pengajuan gagal disimpan karena ada berkas yang tidak valid.", 400);
                } else {
                    if ((getenv("demo"))) {
                        FileLimiter::limitFiles(appConfigDataPath("reimbursement/berkas"), 100);
                    }
                    if ($status == "diajukan") {
                        appJsonRespondSuccess(true, "Pengajuan berhasil disimpan untuk dilakukan validasi.", $redirect);
                        return;
                    } else {
                        appJsonRespondSuccess(true, "Draft Pengajuan berhasil disimpan.", $redirect);
                        return;
                    }
                }
            } else {
                throw new Exception("Pengajuan gagal disimpan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function create()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $triwulan = $this->request->getGet("triwulan") ?? "";
            $tahun = $this->request->getGet("tahun") ?? "";
            $userKey = $this->request->getGet("usr_key") ?? "";

            $isValid = true;
            $message = "";
            if (empty($triwulan) || $triwulan < 0 || $triwulan > 4) {
                $isValid = false;
                $message = "Data Triwulan tidak valid.";
                return $this->_returnCreateInvalid($message);
            }

            if (empty($tahun) || !appIsValidYear($tahun, 2000, date("Y"))) {
                $isValid = false;
                $message = "Tahun tidak valid.";
                return $this->_returnCreateInvalid($message);
            }

            $dSubmissionSchedule = $this->SubmissionScheduleModel->get([
                "sw_tahun" => $tahun,
                "sw_triwulan" => $triwulan,
            ], true);
            if (empty($dSubmissionSchedule)) {
                $isValid = false;
                $message = "Data Jadwal pengajuan tidak ada.";
                return $this->_returnCreateInvalid($message);
            }
            if ($dSubmissionSchedule["sw_is_locked"]) {
                $isValid = false;
                $dateStart = appFormatTanggalIndonesia($dSubmissionSchedule["sw_start_date"], true);
                $dateEnd = appFormatTanggalIndonesia($dSubmissionSchedule["sw_end_date"], true);
                $message = <<<MSG
                <b>Triwulan {$triwulan} dikunci.<br>
                <b>Tanggal Pengajuan: </b> {$dateStart} s/d {$dateEnd}
                MSG;
            }
            $isOutOfDateRange = appIsDateInRanges(date("Y-m-d"), ["start" => $dSubmissionSchedule["sw_start_date"], "end" => $dSubmissionSchedule["sw_end_date"]]);
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_is_active" => 1,
            ]);
            if (empty($dJenisBerkas)) {
                $isValid = false;
                $message = "Data Jenis Berkas tidak ada.";
                return $this->_returnCreateInvalid($message);
            }

            $this->cData["dCategories"] = $this->CategoryModel->get([
                "cat_is_active" => 1,
            ]);

            $withClaimant = false;
            $dUser = null;
            $vUSelected = "";
            if (!empty($userKey)) {
                $dUser = $this->UserModel->get([
                    "usr_key" => $userKey,
                ], true);
                if (!empty($dUser)) {
                    $userId = $dUser["usr_id"];
                    $withClaimant = true;
                    $usrCode = $dUser["usr_code"];
                    $usrUsername = $dUser["usr_username"];
                    $usrEmail = $dUser["usr_email"];
                    $usrRole = (!empty($dUser["usr_role"])) ? masterUserRole($dUser["usr_role"], true)["label"] : "-";
                    $usrGroup = $dUser["group_name"];
                    $usrCategory = (!empty($dUser["usr_group_category"])) ? masterUserCategoryInGroup($dUser["usr_group_category"], true)["label"] : "-";

                    $vUSelected = <<<VIEW
                    <div class="p-2">
                        <dl>
                            <dt>[ {$usrCode} ] @{$usrUsername} {$usrEmail}</dt>
                            <dd>Role: {$usrRole} | Group: {$usrGroup} | Kategori User: {$usrCategory}</dd>
                        </dl>
                    </div>
                    <input type="hidden" name="hdn_user_id" value="{$userId}">
                    VIEW;
                    $dUReim = $this->ReimbursementModel->get([
                        "reim_claimant_usr_id" => $userId,
                        "reim_triwulan_no" => $triwulan,
                        "reim_triwulan_tahun" => $tahun,
                    ], true);
                    if (!empty($dUReim)) {
                        return redirect()->to(base_url('reimbursement/view?reim_key=' . $dUReim["reim_key"]))->with("alert", ["code" => "warning", "message" => "User sudah memiliki data Reimbursement"]);
                    }
                }
            }

            $this->cData["withClaimant"] = $withClaimant;
            $this->cData["vUSelected"] = $vUSelected;
            $this->cData["isLocked"] = $dSubmissionSchedule["sw_is_locked"];
            $this->cData["isValid"] = $isValid;
            $this->cData["message"] = $message;
            $this->cData["dSubmissionSchedule"] = $dSubmissionSchedule;
            $this->cData["dJenisBerkas"] = $dJenisBerkas;
            return view($this->viewDir . "/create", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    private function _returnCreateInvalid($message = "")
    {
        $this->cData["isValid"] = false;
        $this->cData["message"] = $message;
        return view($this->viewDir . "/create", $this->cData);
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $dSubmissionSchedule = $this->SubmissionScheduleModel->get([
                "sw_tahun" => date("Y")
            ]);
            $this->cData["dSubmissionSchedule"] = $dSubmissionSchedule;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function view()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $reimKey = $this->request->getGet("reim_key") ?? "";
            if (empty($reimKey)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data yang dibutuhkan tidak ada.",
                ]);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data tidak ditemukan",
                ]);
            }
            $reimId = $dReimbursement["reim_id"];
            $dReimBerkas = $this->BerkasModel->get([
                "rb_reim_id" => $reimId,
            ]);
            $this->cData["dReimBerkas"] = $dReimBerkas;
            $this->cData["dReimbursement"] = $dReimbursement;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function print()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $reimKey = $this->request->getGet("reim_key") ?? "";
            if (empty($reimKey)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data yang dibutuhkan tidak ada.",
                ]);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data tidak ditemukan",
                ]);
            }
            $reimId = $dReimbursement["reim_id"];
            $currentStatus = $dReimbursement["reim_status"];
            if (!in_array($currentStatus, ["disetujui"])) {
                throw new Exception("Pengajuan belum berstatus disetujui.", 400);
            }
            $dReimBerkas = $this->BerkasModel->get([
                "rb_reim_id" => $reimId,
            ]);
            $this->cData["dReimBerkas"] = $dReimBerkas;
            $this->cData["dReimbursement"] = $dReimbursement;

            return $this->_asPdf($this->cData);
            // return view($this->viewDir . "/print", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    private function _asPdf($data)
    {
        try {
            try {
                $dReimbursement = $data["dReimbursement"];
                $dPdf = [
                    "sessUserId" => $data["dAccess"]["data"]["usr_id"],
                    "sessUsername" => $data["dAccess"]["data"]["usr_username"],
                    "dReimbursement" => $dReimbursement,
                ];
                $html = appViewInjectContent("reimbursement", "single_pdf", $dPdf);
                $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
                $html2pdf->pdf->setDisplayMode("fullpage");
                $html2pdf->pdf->SetTitle($dReimbursement["reim_code"] . " - Reimbursement");
                $html2pdf->setTestIsImage(false);

                $html2pdf->writeHTML($html);
                $html2pdf->output($dReimbursement["reim_code"] . "-reimbursement.pdf");
                exit;
            } catch (Html2PdfException $e) {
                if (isset($html2pdf)) {
                    $html2pdf->clean();
                }
                $formatter = new ExceptionFormatter($e);
                echo $formatter->getHtmlMessage();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * =================================================
     * SELESAI
     * =================================================
     */
    public function showSetAsDone()
    {
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimKey = $this->request->getPost("reim_key");
            if (empty($reimKey)) {
                throw new Exception("Data yang diperlukan tidak ditemukan.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $dView = [
                "viewDir" => $this->viewDir,
                "dReimbursement" => $dReimbursement,
            ];
            $redirect = $this->request->getUserAgent()->getReferrer();
            $view = appViewInjectModal($this->viewDir, "set_as_done_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_set_as_done_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    // END SELESAI =====================================

    /**
     * =================================================
     * PAYMENT
     * =================================================
     */
    public function doDelFilepayment()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimKey = $this->request->getPost("reim_key");

            if (empty($reimKey)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $filePayment = $dReimbursement["reim_paid_file_name"];
            if (empty($filePayment)) {
                throw new Exception("File sudah terhapus.", 400);
            }
            $fPath = appConfigDataPath("reimbursement/payment/" . $filePayment);
            $dEdit = [
                "reim_paid_file_name" => null,
                "reim_updated_at" => appCurrentDateTime(),
            ];
            if ($this->ReimbursementModel->edit($dEdit, ["reim_id" => $dReimbursement["reim_id"]])) {
                if (file_exists($fPath)) {
                    if (unlink($fPath)) {
                    } else {
                        log_message("error", "Gagal menghapus file: " . $fPath);
                    }
                }
                appJsonRespondSuccess(true, "File berhasil dihapus.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menghapus file.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
    public function doEditPayment()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id");
            $reimKey = $this->request->getPost("hdn_reim_key");
            $status = $this->request->getPost("cbo_status_payment");
            $date = $this->request->getPost("dt_payment") ?? null;
            $detail = $this->request->getPost("txt_detail");

            if (empty($reimId) || empty($reimKey)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }
            if (!empty($status) && !in_array($status, [1, 0])) {
                throw new Exception("Invalid status", 400);
            }
            if (!empty($date)) {
                if (!appValidateDate($date)) {
                    throw new Exception("Invalid tanggal Pembayaran", 400);
                }
            }

            $dReimbursement = $this->ReimbursementModel->get([
                "reim_id" => $reimId,
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan", 400);
            }
            $dEdit = [
                "reim_is_paid"  => $status,
                "reim_paid_at" => $date,
                "reim_paid_detail" => $detail,
                "reim_updated_at" => appCurrentDateTime(),
            ];
            $jbMaxFileSizeMb = 5;
            $maxSizeBytes = $jbMaxFileSizeMb * 1024 * 1024;

            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];

            $file = $this->request->getFile("file_payment");
            // --- CEK APAKAH FILE DIUPLOAD --- //
            if ($file->getError() === 4) { // Tidak ada file yang diupload
            } else {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Validasi ukuran
                    if ($file->getSize() > $maxSizeBytes) {
                        log_message("alert", "File  melebihi batas ukuran maksimum {$jbMaxFileSizeMb}MB.");
                        throw new Exception("Ukurang maksimum $jbMaxFileSizeMb MB", 400);
                    }

                    // Validasi ekstensi dan mime
                    $ext = strtolower($file->getExtension());
                    $mime = $file->getMimeType();

                    if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimeTypes)) {
                        log_message("alert", "File memiliki tipe yang tidak diizinkan. Ext: $ext, Mime: $mime.");
                        throw new Exception("Tipe file tidak diizinkan.", 400);
                    }
                    $newName = $file->getRandomName();
                    $uploadPath = appConfigDataPath("reimbursement/payment/");
                    log_message("alert", "Reim Upload Path= " . $uploadPath);
                    $filePath = $uploadPath . "/" . $newName;
                    $file->move($uploadPath, $newName);
                    if (!file_exists($filePath)) {
                        log_message("alert", "File  tidak valid atau gagal diproses.");
                        throw new Exception("Gagal upload file.", 400);
                    }

                    $dEdit["reim_paid_file_name"] = $newName;
                } else {
                    throw new Exception("File gagal diupload.", 400);
                }
            }

            if ($this->ReimbursementModel->edit($dEdit, [
                "reim_id" => $reimId,
            ])) {
                appJsonRespondSuccess(true, "Perubahan berhasil disimpan.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menyimpan perubahan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
    public function showEditPayment()
    {
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimKey = $this->request->getPost("reim_key");
            if (empty($reimKey)) {
                throw new Exception("Data yang diperlukan tidak ditemukan.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $paidFileName = $dReimbursement["reim_paid_file_name"];
            $dFilePayment = appGetFileReimPayment($reimKey, $paidFileName);
            $dView = [
                "viewDir" => $this->viewDir,
                "dReimbursement" => $dReimbursement,
                "dFilePayment" => $dFilePayment,
            ];
            $redirect = $this->request->getUserAgent()->getReferrer();
            $view = appViewInjectModal($this->viewDir, "edit_payment_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_edit_payment_script");
            $script .= appViewInjectScript($this->viewDir, "do_del_file_payment_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
    // END PAYMENT =====================================
    /**
     * =================================================
     * REVISI
     * =================================================
     */

    public function doSaveRevision()
    {
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id");
            $reimKey = $this->request->getPost("hdn_reim_key");
            $categoryId = $this->request->getPost("cbo_category");
            $nominal = $this->request->getPost("nbr_nominal");
            $detail = $this->request->getPost("txt_detail");
            $btnAction = $this->request->getPost("btn_action");

            log_message("alert", "action=" . json_encode($btnAction));

            if (empty($reimId)) {
                throw new Exception("Reim Id not found.", 400);
            }
            if (empty($reimKey)) {
                throw new Exception("Reim key not found.", 400);
            }
            if (!in_array($btnAction, ["save_revision", "save_ajukan"])) {
                throw new Exception("Invalid action.", 400);
            }

            $dReimbursement = $this->ReimbursementModel->get([
                "reim_id" => $reimId,
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 404);
            }
            if ($dReimbursement["reim_status"] != "revisi") {
                throw new Exception("Gagal menyimpan perubahan karena status sudah bukan revisi.", 400);
            }
            $reimTriwulanTahun = $dReimbursement["reim_triwulan_tahun"];
            $reimTriwulan = $dReimbursement["reim_triwulan_no"];
            $reimClaimantUsrId = $dReimbursement["reim_claimant_usr_id"];
            $dUser = $this->UserModel->get([
                "usr_id" => $reimClaimantUsrId,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data tidak valid.[ref:user claimant not found]", 400);
            }
            $reimClaimantUsrKey = $dUser["usr_key"];
            $dCategory = $this->CategoryModel->get([
                "cat_id" => $categoryId,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data category tidak ada.", 404);
            }
            $dEdit = [
                "reim_cat_id" => $categoryId,
                "reim_amount" => $nominal,
                "reim_detail" => $detail,
                "reim_updated_at" => appCurrentDateTime(),
            ];
            if ($btnAction == "save_ajukan") {
                $dEdit["reim_status"] = "diajukan";
                $redirect = base_url("reimbursement/view?reim_key=" . $reimKey);
            } else {
                $redirect = base_url("reimbursement/revision?reim_key=" . $reimKey);
            }
            if ($this->ReimbursementModel->edit($dEdit, [
                "reim_id" => $reimId,
            ])) {
                log_message("alert", "simpan perubahan revisi berhasil.");
                appJsonRespondSuccess(true, "Simpan perubahan berhasil.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menyimpan perubahan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function revision()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $this->cData["dAccess"] = $dAccess;

            $reimKey = $this->request->getGet("reim_key") ?? "";
            if (empty($reimKey)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data yang dibutuhkan tidak ada.",
                ]);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data tidak ditemukan",
                ]);
            }
            $reimId = $dReimbursement["reim_id"];
            $currentStatus = $dReimbursement["reim_status"];
            $claimantUserId = $dReimbursement["reim_claimant_usr_id"];
            $claimantUserGroupId = $dReimbursement["ucg_group_id"];

            if ($currentStatus != "revisi") {
                throw new Exception("Status bukan revisi", 400);
            }

            if ($sessUsrId != authMasterUserId() && $claimantUserGroupId != $sessGroupId) {
                throw new Exception("Anda tidak punya akses.", 400);
            }

            $dUser = null;
            $vUSelected = "";
            $isValid = true;
            $message = "";
            if (!empty($claimantUserId)) {
                $dUser = $this->UserModel->get([
                    "usr_id" => $claimantUserId,
                ], true);
                if (!empty($dUser)) {
                    $usrCode = $dUser["usr_code"];
                    $usrUsername = $dUser["usr_username"];
                    $usrEmail = $dUser["usr_email"];
                    $usrRole = $dUser["usr_role"];
                    $usrGroup = $dUser["group_name"];
                    $usrCategory = masterUserCategoryInGroup($dUser["usr_group_category"])["label"];

                    $vUSelected = <<<VIEW
                    <div class="p-2">
                        <dl>
                            <dt>[ {$usrCode} ] @{$usrUsername} {$usrEmail}</dt>
                            <dd>Role: {$usrRole} | Group: {$usrGroup} | Kategori User: {$usrCategory}</dd>
                        </dl>
                    </div>
                    <input type="hidden" name="hdn_user_id" value="{$claimantUserId}">
                    VIEW;
                }
            }

            $dReimBerkas = $this->BerkasModel->get([
                "rb_reim_id" => $reimId,
            ]);

            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_is_active" => 1,
            ]);
            if (empty($dJenisBerkas)) {
                $isValid = false;
                $message = "Data Jenis Berkas tidak ada.";
                return $this->_returnCreateInvalid($message);
            }
            $inJB = [];
            $outJB = [];

            $jbIds = array_column($dJenisBerkas, 'jb_id');

            foreach ($dReimBerkas as $vRB) {
                if (in_array($vRB["rb_jb_id"], $jbIds)) {
                    $inJB[] = $vRB;
                } else {
                    $outJB[] = $vRB;
                }
            }

            // Kelompokkan ReimBerkas berdasarkan jb_id
            $mapReim = [];
            foreach ($dReimBerkas as $vRB) {
                $mapReim[$vRB["rb_jb_id"]][] = $vRB;
            }

            // Gabungkan ke data JenisBerkas
            foreach ($dJenisBerkas as &$vJB) {
                $jb_id = $vJB["jb_id"];
                $vJB["dReimBerkas"] = $mapReim[$jb_id] ?? [];
            }
            unset($vJB);

            $this->cData["dCategories"] = $this->CategoryModel->get([
                "cat_is_active" => 1,
            ]);

            $dRevision = $this->RevisionModel->get([
                "rrev_reim_id" => $reimId,
            ], true);
            $this->cData["dRevision"] = $dRevision;
            $this->cData["inJB"] = $inJB;
            $this->cData["outJB"] = $outJB;
            $this->cData["dReimBerkas"] = $dReimBerkas;
            $this->cData["dReimbursement"] = $dReimbursement;
            $this->cData["vUSelected"] = $vUSelected;
            // $this->cData["isLocked"] = $dSubmissionSchedule["sw_is_locked"];
            $this->cData["isValid"] = $isValid;
            $this->cData["message"] = $message;
            // $this->cData["dSubmissionSchedule"] = $dSubmissionSchedule;
            $this->cData["dJenisBerkas"] = $dJenisBerkas;
            return view($this->viewDir . "/revision", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    // END REVISI ======================================
    /**
     * =================================================
     * VALIDASI
     * =================================================
     */

    public function doAsAccepted()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id");
            $reimKey = $this->request->getPost("hdn_reim_key");
            $btnAction = $this->request->getPost("btn_action");

            if (empty($reimId) || empty($reimKey)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }

            $dReimbursement = $this->ReimbursementModel->get([
                "reim_id" => $reimId,
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $currentStatus = $dReimbursement["reim_status"];
            if ($currentStatus != "validasi") {
                throw new Exception("Status bukan [validasi] " . masterReimbursementStatus($currentStatus)["label"], 400);
            }

            if ($this->ReimbursementModel->edit([
                "reim_status" => "disetujui",
                "reim_updated_at" => appCurrentDateTime(),
            ], [
                "reim_id" => $reimId,
            ])) {
                $redirect = base_url("reimbursement/view?reim_key=" . $reimKey);
                appJsonRespondSuccess(true, "Perubahan berhasil disimpan.", $redirect);
                return;
            } else {
                throw new Exception("Perubahan gagal disimpan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doAsRevision()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id");
            $reimKey = $this->request->getPost("hdn_reim_key");
            $note = $this->request->getPost("txt_revision_note");
            $btnAction = $this->request->getPost("btn_action");

            if (empty($reimId) || empty($reimKey)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }

            if (empty($note)) {
                throw new Exception("Detail Revisi harus diisi.", 400);
            }
            if (!in_array($btnAction, ["save", "as_revision"])) {
                throw new Exception("Invalid action", 400);
            }

            $dRevision = $this->RevisionModel->get([
                "rrev_reim_id" => $reimId,
            ], true);
            if ($dRevision) {
                $dEdit = [
                    "rrev_note" => $note,
                    "rrev_updated_at" => appCurrentDateTime(),
                ];
                if ($this->RevisionModel->edit($dEdit, [
                    "rrev_id" => $dRevision["rrev_id"],
                ])) {
                    if ($btnAction == "as_revision") {
                        if ($this->ReimbursementModel->edit([
                            "reim_status" => "revisi",
                            "reim_ever_revised_at" => appCurrentDateTime(),
                            "reim_updated_at" => appCurrentDateTime(),
                        ], [
                            "reim_id" => $reimId,
                        ])) {
                            $redirect = base_url("reimbursement/view?reim_key=" . $reimKey);
                        } else {
                            log_message("alert", "Gagal update status reimbursement ke revisi." . $reimId);
                        }
                    }
                    appJsonRespondSuccess(true, "Perubahan berhasil disimpan", $redirect);
                    return;
                } else {
                    throw new Exception("Perubahan gagal disimpan.", 400);
                }
            } else {
                $key = $this->RevisionModel->generateKey();
                $dAdd = [
                    "rrev_key" => $key,
                    "rrev_by_usr_id" => $sessUsrId,
                    "rrev_reim_id" => $reimId,
                    "rrev_note" => $note,
                    "rrev_created_at" => appCurrentDateTime(),
                ];
                if ($this->RevisionModel->add($dAdd)) {
                    if ($btnAction == "as_revision") {
                        if ($this->ReimbursementModel->edit([
                            "reim_status" => "revisi",
                            "reim_ever_revised_at" => appCurrentDateTime(),
                            "reim_updated_at" => appCurrentDateTime(),
                        ], [
                            "reim_id" => $reimId,
                        ])) {
                            $redirect = base_url("reimbursement/view?reim_key=" . $reimKey);
                        } else {
                            log_message("alert", "Gagal update status reimbursement ke revisi." . $reimId);
                        }
                    }
                    appJsonRespondSuccess(true, "Perubahan berhasil disimpan", $redirect);
                    return;
                } else {
                    throw new Exception("Perubahan gagal disimpan.", 400);
                }
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function validation()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }
            $this->cData["dAccess"] = $dAccess;

            $reimKey = $this->request->getGet("reim_key") ?? "";
            if (empty($reimKey)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data yang dibutuhkan tidak ada.",
                ]);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data tidak ditemukan",
                ]);
            }
            $reimId = $dReimbursement["reim_id"];
            $currentStatus = $dReimbursement["reim_status"];
            if ($currentStatus != "validasi") {
                throw new Exception("Status bukan [ validasi ] " . masterReimbursementStatus($currentStatus)["label"], 400);
            }
            $dReimBerkas = $this->BerkasModel->get([
                "rb_reim_id" => $reimId,
            ]);
            $dRevision = $this->RevisionModel->get([
                "rrev_reim_id" => $reimId,
            ], true);
            $this->cData["dRevision"] = $dRevision;
            $this->cData["dReimBerkas"] = $dReimBerkas;
            $this->cData["dReimbursement"] = $dReimbursement;
            return view($this->viewDir . "/validation", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doStartValidate()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "validator") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimKey = $this->request->getPost("reim_key");
            if (empty($reimKey)) {
                throw new Exception("Data yang dibutuhkan tidak ada.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $currentStatus  = $dReimbursement["reim_status"];
            if ($currentStatus == "validasi") {
                throw new Exception("Data sudah berstatus validasi (" . masterReimbursementStatus($currentStatus)["label"], 400);
            }
            if (!in_array($dReimbursement["reim_status"], ["draft", "diajukan"])) {
                throw new Exception("Status tidak valid.", 400);
            }
            $dEdit = [
                "reim_status" => "validasi",
                "reim_validation_start_at" => appCurrentDateTime(),
                "reim_validation_by_usr_id" => $sessUsrId,
                "reim_updated_at" => appCurrentDateTime(),
            ];
            if ($this->ReimbursementModel->edit($dEdit, [
                "reim_id" => $dReimbursement["reim_id"]
            ])) {
                $redirect = base_url("reimbursement/validation?reim_key=" . $reimKey);
                appJsonRespondSuccess(true, "Perubahan berhasil disimpan.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menyimpan perubahan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    // END VALIDASI ====================================

    public function doEditBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $rbKey = $this->request->getPost("hdn_rb_key");
            $rbNote = $this->request->getPost("txt_note") ?? "";
            if (empty($rbKey)) {
                throw new Exception("Data yang diperlukan tidak ditemukan.", 400);
            }
            $dReimBerkas = $this->BerkasModel->get([
                "rb_key" => $rbKey,
            ], true);
            if (empty($dReimBerkas)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $dEdit = [
                "rb_note" => $rbNote,
                "rb_updated_at" => appCurrentDateTime(),
            ];
            if ($this->BerkasModel->edit($dEdit, [
                "rb_id" => $dReimBerkas["rb_id"],
            ])) {
                $redirect = $this->request->getUserAgent()->getReferrer();
                appJsonRespondSuccess(true, "Berhasil disimpan.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menyimpan data.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function showEditBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $rbKey = $this->request->getPost("rb_key");
            if (empty($rbKey)) {
                throw new Exception("Data yang diperlukan tidak ditemukan.", 400);
            }
            $dReimBerkas = $this->BerkasModel->get([
                "rb_key" => $rbKey,
            ], true);
            if (empty($dReimBerkas)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }
            $dView = [
                "viewDir" => $this->viewDir,
                "dReimBerkas" => $dReimBerkas,
            ];
            $redirect = $this->request->getUserAgent()->getReferrer();
            $view = appViewInjectModal($this->viewDir, "berkas/edit_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "berkas/submit_edit_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }


    public function showPreviewBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $rbKey = $this->request->getPost("rb_key");
            if (empty($rbKey)) {
                throw new Exception("Data yang diperlukan tidak ditemukan.", 400);
            }
            $dReimBerkas = $this->BerkasModel->get([
                "rb_key" => $rbKey,
            ], true);
            if (empty($dReimBerkas)) {
                throw new Exception("Data tidak ditemukan.", 400);
            }

            $berkasFName = $dReimBerkas["rb_file_name"];
            $reimTahun = $dReimBerkas["reim_triwulan_tahun"];
            $reimTriwulan = $dReimBerkas["reim_triwulan_no"];
            $claimantUGroupKey = $dReimBerkas["ucg_group_key"];

            $dFile = appGetReimBerkas($rbKey, $berkasFName, $reimTahun, $reimTriwulan, $claimantUGroupKey);
            $dView = [
                "viewDir" => $this->viewDir,
                "dReimBerkas" => $dReimBerkas,
                "dFile" => $dFile,
            ];
            $redirect = $this->request->getUserAgent()->getReferrer();
            $view = appViewInjectModal($this->viewDir, "berkas/preview_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "berkas/preview_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doUploadBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id") ?? "";
            $reimKey = $this->request->getPost("hdn_reim_key") ?? "";
            $jbKey = $this->request->getPost("hdn_jb_key") ?? "";
            $note = $this->request->getPost("txt_note") ?? "";

            if (empty($reimId)) {
                throw new Exception("Data Reimbursement tidak ada.", 400);
            }
            if (empty($reimKey)) {
                throw new Exception("Data Reimbursement tidak ada.", 400);
            }
            if (empty($jbKey)) {
                throw new Exception("Data Jenis Berkas tidak ada.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
                "reim_id" => $reimId,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data Reimbursement tidak ditemukan.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data Jenis Berkas tidak ditemukan.", 400);
            }

            if ($sessUsrId != authMasterUserId()) {
                if ($sessGroupId != $dReimbursement["uc_usr_group_id"]) {
                    throw new Exception("Anda tidak memiliki akses.", 400);
                }
            }

            $jenisBerkasId = $dJenisBerkas["jb_id"];
            $jbName = $dJenisBerkas["jb_name"];
            $jbIsRequired = (bool) $dJenisBerkas["jb_is_required"];
            $jbMaxFileSizeMb = $dJenisBerkas["jb_max_file_size_mb"];
            $maxSizeBytes = $jbMaxFileSizeMb * 1024 * 1024;

            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];

            $file = $this->request->getFile("file_berkas");
            // --- CEK APAKAH FILE DIUPLOAD --- //
            if ($file->getError() === 4) { // Tidak ada file yang diupload
                if ($jbIsRequired) {
                    log_message("alert", "Berkas $jenisBerkasId wajib diunggah, tapi tidak ada file.");
                } else {
                    log_message("info", "Berkas $jenisBerkasId bersifat opsional, dan tidak diunggah.");
                }
                throw new Exception("Tidak ada file yang akan diupload.", 400);
            }

            // --- CEK VALIDITAS FILE --- //
            if ($file->isValid() && !$file->hasMoved()) {
                // Validasi ukuran
                if ($file->getSize() > $maxSizeBytes) {
                    log_message("alert", "File  melebihi batas ukuran maksimum {$jbMaxFileSizeMb}MB.");
                    throw new Exception("Ukurang maksimum $jbMaxFileSizeMb MB", 400);
                }

                // Validasi ekstensi dan mime
                $ext = strtolower($file->getExtension());
                $mime = $file->getMimeType();

                if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimeTypes)) {
                    log_message("alert", "File memiliki tipe yang tidak diizinkan. Ext: $ext, Mime: $mime.");
                    throw new Exception("Tipe file tidak diizinkan.", 400);
                }

                // Upload
                $reimTahun = $dReimbursement["reim_triwulan_tahun"];
                $reimTriwulan = $dReimbursement["reim_triwulan_no"];
                $reimClaimantUsrId = $dReimbursement["reim_claimant_usr_id"];
                $reimClaimantUsrKey = $dReimbursement["uc_usr_key"];
                $reimClaimantUsrGroupKey = $dReimbursement["ucg_group_key"];
                $newName = $file->getRandomName();
                $uploadPath = appConfigDataPath("reimbursement/berkas/" . $reimTahun . "/" . "triwulan_" . $reimTriwulan . "/" . $reimClaimantUsrGroupKey);
                log_message("alert", "Reim Upload Path= " . $uploadPath);
                $filePath = $uploadPath . "/" . $newName;
                $file->move($uploadPath, $newName);
                if (!file_exists($filePath)) {
                    $doRollback = true;
                    log_message("alert", "File  tidak valid atau gagal diproses.");
                    throw new Exception("Gagal upload file.", 400);
                }
                $uploadedFiles[] = $newName;
                $rbKey = $this->BerkasModel->generateKey();
                $dAddBerkas = [
                    "rb_key" => $rbKey,
                    "rb_by_usr_id" => $sessUsrId,
                    "rb_reim_id" => $reimId,
                    "rb_jb_id" => $jenisBerkasId,
                    "rb_file_name" => $newName,
                    "rb_file_name_origin" => $file->getClientName(),
                    "rb_note" => $note,
                    "rb_created_at" => appCurrentDateTime(),
                ];
                $rbId = $this->BerkasModel->add($dAddBerkas);
                if ($rbId > 0) {
                    log_message("alert", "File  berhasil diupload dengan nama $newName.");
                    $redirect = $this->request->getUserAgent()->getReferrer();
                    appJsonRespondSuccess(true, "File berhasil diupload.", $redirect);
                    return;
                } else {
                    log_message("alert", "File tidak valid atau gagal diproses.");
                    throw new Exception("Upload file gagal.", 400);
                }
            } else {
                log_message("alert", "File tidak valid atau gagal diproses.");
                throw new Exception("Upload file gagal.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function showUploadBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimKey = $this->request->getPost("reim_key") ?? "";
            $jbKey = $this->request->getPost("jb_key") ?? "";

            if (empty($reimKey)) {
                throw new Exception("Data Reimbursement tidak ada.", 400);
            }
            if (empty($jbKey)) {
                throw new Exception("Data Jenis Berkas tidak ada.", 400);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data Reimbursement tidak ditemukan.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data Jenis Berkas tidak ditemukan.", 400);
            }

            if ($sessUsrId != authMasterUserId()) {
                if ($sessGroupId != $dReimbursement["uc_usr_group_id"]) {
                    throw new Exception("Anda tidak memiliki akses.", 400);
                }
            }
            $dView = [
                "viewDir" => $this->viewDir,
                "dReimbursement" => $dReimbursement,
                "dJenisBerkas" => $dJenisBerkas,
            ];
            $redirect = $this->request->getUserAgent()->getReferrer();
            $view = appViewInjectModal($this->viewDir, "berkas/upload_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "berkas/submit_upload_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doDeleteBerkas()
    {
        try {
            $dAccess = authVerifyAccess(false, "u_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $rbKey = $this->request->getPost("rb_key") ?? "";

            if (empty($rbKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dReimBerkas = $this->BerkasModel->get([
                "rb_key" => $rbKey,
            ], true);
            if (empty($dReimBerkas)) {
                throw new Exception("Data tidak ditemukan atau sudah dihapus.", 404);
            }
            $fName = $dReimBerkas["rb_file_name"];
            $reimTriwulan = $dReimBerkas["reim_triwulan_no"];
            $reimTriwulanTahun = $dReimBerkas["reim_triwulan_tahun"];
            $reimClaimantUsrId = $dReimBerkas["reim_claimant_usr_id"];
            $reimClaimantUsrKey = $dReimBerkas["uc_usr_key"];

            $reimClaimantUsrGroupKey = $dReimBerkas["ucg_group_key"];

            if ($this->BerkasModel->del([
                "rb_id" => $dReimBerkas["rb_id"],
            ])) {
                if (!empty($fName)) {
                    $fPath = appConfigDataPath("reimbursement/berkas/" . $reimTriwulanTahun . "/" . "triwulan_" . $reimTriwulan . "/" . $reimClaimantUsrGroupKey . $fName);
                    log_message("alert", "reim berkas uploaded=" . $fPath);
                    if (file_exists($fPath)) {
                        if (unlink($fPath)) {
                        } else {
                            log_message("alert", "gagal hapus berkas: " . $fPath);
                        }
                    }
                }
                $redirect = $this->request->getUserAgent()->getReferrer();
                appJsonRespondSuccess(true, "Hapus berkas berhasil.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menghapus berkas.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doSaveDraft()
    {
        try {
            $dAccess = authVerifyAccess(false, "draft_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessUsrRole = $dAccess["data"]["usr_role"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            if ($sessUsrId != authMasterUserId() && $sessUsrRole != "admin_group") {
                throw new Exception("Kamu tidak memiliki akses.", 400);
            }

            $reimId = $this->request->getPost("hdn_reim_id");
            $reimKey = $this->request->getPost("hdn_reim_key");
            $categoryId = $this->request->getPost("cbo_category");
            $nominal = $this->request->getPost("nbr_nominal");
            $detail = $this->request->getPost("txt_detail");
            $btnAction = $this->request->getPost("btn_action");

            log_message("alert", "action=" . json_encode($btnAction));

            if (empty($reimId)) {
                throw new Exception("Reim Id not found.", 400);
            }
            if (empty($reimKey)) {
                throw new Exception("Reim key not found.", 400);
            }
            if (!in_array($btnAction, ["save_draft", "save_ajukan"])) {
                throw new Exception("Invalid action.", 400);
            }

            $dReimbursement = $this->ReimbursementModel->get([
                "reim_id" => $reimId,
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                throw new Exception("Data tidak ditemukan.", 404);
            }
            if ($dReimbursement["reim_status"] != "draft") {
                throw new Exception("Gagal menyimpan perubahan karena status sudah bukan draft.", 400);
            }
            $reimTriwulanTahun = $dReimbursement["reim_triwulan_tahun"];
            $reimTriwulan = $dReimbursement["reim_triwulan_no"];
            $reimClaimantUsrId = $dReimbursement["reim_claimant_usr_id"];
            $dUser = $this->UserModel->get([
                "usr_id" => $reimClaimantUsrId,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data tidak valid.[ref:user claimant not found]", 400);
            }
            $reimClaimantUsrKey = $dUser["usr_key"];
            $dCategory = $this->CategoryModel->get([
                "cat_id" => $categoryId,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data category tidak ada.", 404);
            }
            $dEdit = [
                "reim_cat_id" => $categoryId,
                "reim_amount" => $nominal,
                "reim_detail" => $detail,
                "reim_updated_at" => appCurrentDateTime(),
            ];
            if ($btnAction == "save_ajukan") {
                $dEdit["reim_status"] = "diajukan";
                $dEdit["reim_diajukan_pada"] = appCurrentDateTime();
                $dEdit["reim_diajukan_by_usr_id"] = $sessUsrId;
                $redirect = base_url("reimbursement/view?reim_key=" . $reimKey);
            } else {
                $redirect = base_url("reimbursement/draft?reim_key=" . $reimKey);
            }
            if ($this->ReimbursementModel->edit($dEdit, [
                "reim_id" => $reimId,
            ])) {
                log_message("alert", "simpan perubahan draft berhasil.");
                appJsonRespondSuccess(true, "Simpan perubahan berhasil.", $redirect);
                return;
            } else {
                throw new Exception("Gagal menyimpan perubahan.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function draft()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $reimKey = $this->request->getGet("reim_key") ?? "";
            if (empty($reimKey)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data yang dibutuhkan tidak ada.",
                ]);
            }
            $dReimbursement = $this->ReimbursementModel->get([
                "reim_key" => $reimKey,
            ], true);
            if (empty($dReimbursement)) {
                return redirect()->to(base_url("reimbursement"))->with("alert", [
                    "code" => "danger",
                    "message" => "Data tidak ditemukan",
                ]);
            }
            $reimId = $dReimbursement["reim_id"];
            $claimantUserId = $dReimbursement["reim_claimant_usr_id"];
            $dUser = null;
            $vUSelected = "";
            $isValid = true;
            $message = "";
            if (!empty($claimantUserId)) {
                $dUser = $this->UserModel->get([
                    "usr_id" => $claimantUserId,
                ], true);
                if (!empty($dUser)) {
                    $usrCode = $dUser["usr_code"];
                    $usrUsername = $dUser["usr_username"];
                    $usrEmail = $dUser["usr_email"];
                    $usrRole = $dUser["usr_role"];
                    $usrGroup = $dUser["group_name"];
                    $usrCategory = masterUserCategoryInGroup($dUser["usr_group_category"])["label"];

                    $vUSelected = <<<VIEW
                    <div class="p-2">
                        <dl>
                            <dt>[ {$usrCode} ] @{$usrUsername} {$usrEmail}</dt>
                            <dd>Role: {$usrRole} | Group: {$usrGroup} | Kategori User: {$usrCategory}</dd>
                        </dl>
                    </div>
                    <input type="hidden" name="hdn_user_id" value="{$claimantUserId}">
                    VIEW;
                }
            }

            $dReimBerkas = $this->BerkasModel->get([
                "rb_reim_id" => $reimId,
            ]);

            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_is_active" => 1,
            ]);
            if (empty($dJenisBerkas)) {
                $isValid = false;
                $message = "Data Jenis Berkas tidak ada.";
                return $this->_returnCreateInvalid($message);
            }
            $inJB = [];
            $outJB = [];

            $jbIds = array_column($dJenisBerkas, 'jb_id');

            foreach ($dReimBerkas as $vRB) {
                if (in_array($vRB["rb_jb_id"], $jbIds)) {
                    $inJB[] = $vRB;
                } else {
                    $outJB[] = $vRB;
                }
            }

            // Kelompokkan ReimBerkas berdasarkan jb_id
            $mapReim = [];
            foreach ($dReimBerkas as $vRB) {
                $mapReim[$vRB["rb_jb_id"]][] = $vRB;
            }

            // Gabungkan ke data JenisBerkas
            foreach ($dJenisBerkas as &$vJB) {
                $jb_id = $vJB["jb_id"];
                $vJB["dReimBerkas"] = $mapReim[$jb_id] ?? [];
            }
            unset($vJB);

            $this->cData["dCategories"] = $this->CategoryModel->get([
                "cat_is_active" => 1,
            ]);

            $this->cData["inJB"] = $inJB;
            $this->cData["outJB"] = $outJB;
            $this->cData["dReimBerkas"] = $dReimBerkas;
            $this->cData["dReimbursement"] = $dReimbursement;
            $this->cData["vUSelected"] = $vUSelected;
            // $this->cData["isLocked"] = $dSubmissionSchedule["sw_is_locked"];
            $this->cData["isValid"] = $isValid;
            $this->cData["message"] = $message;
            // $this->cData["dSubmissionSchedule"] = $dSubmissionSchedule;
            $this->cData["dJenisBerkas"] = $dJenisBerkas;
            return view($this->viewDir . "/draft", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function list()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            $this->cData["dAccess"] = $dAccess;

            $triwulan = $this->request->getGet("triwulan") ?? "";
            $tahun = $this->request->getGet("tahun") ?? "";

            $dWhere = [];
            if (!empty($triwulan)) {
                $dWhere["reim_triwulan_no"] = $triwulan;
            }
            if (!empty($tahun)) {
                $dWhere["reim_triwulan_tahun"] = $tahun;
            }

            // $dReimbursements = $this->ReimbursementModel->get($dWhere);
            // $this->cData["dReimbursements"] = $dReimbursements;
            $this->cData["triwulan"] = $triwulan;
            $this->cData["tahun"] = $tahun;
            return view($this->viewDir . "/list", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function dtblUserForReim()
    {
        $this->_onlyPostandAjax();
        try {
            $dAccess = authVerifyAccess(true);
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 400);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

            $conditions = [];
            $triwulan = $this->request->getPost("triwulan");
            $tahun = $this->request->getPost("tahun");

            if (!empty($triwulan) && $triwulan > 0 && $triwulan <= 4) {
                // $conditions["where"]["reim_triwulan_no"] = $triwulan;
                $conditions["triwulan"] = $triwulan;
            }
            if (!empty($tahun) && $triwulan > 0) {
                // $conditions["where"]["reim_triwulan_tahun"] = $tahun;
                $conditions["tahun"] = $tahun;
            }
            log_message("alert", "dtbl_condition=" . json_encode($conditions));
            $list = $this->ReimbursementModel->getDtblUserForReim($conditions);
            $data = [];
            $no = $_POST['start'] ?? 0;
            foreach ($list as $item) :
                $no++;
                $usrId = $item["usr_id"];
                $usrKey = $item["usr_key"];

                $row = [];
                $row[] =  $usrId;

                $hasReim = $item["has_reimbursement"];
                log_message("alert", "has reimbursement=" . $hasReim);
                if ($hasReim == 1) {
                    $reimKey = $item["reim_key"];
                    $btnActions = "<a href='" . base_url("reimbursement/view?reim_key=" . $reimKey) . "' class='btn btn-sm btn-dark text-left' style='width:100px'  target='_blank'><i class='fas fa-folder-open'></i> View</a>";
                } else {
                    $btnActions = "<a href='" . base_url("reimbursement/create?triwulan=" . $triwulan . "&tahun=" . $tahun . "&usr_key=" . $usrKey) . "' class='btn btn-sm btn-dark text-left' style='width:100px'  target='_blank'><i class='fas fa-plus-circle'></i> Ajukan</a>";
                }

                $lblReimStatus = "";
                if (!empty($item["reim_status"])) {
                    $lblReimStatus = appRenderLabel(masterReimbursementStatus($item["reim_status"])["label"], "140px");
                }

                $lblReimAmount = "";
                if (!empty($item["reim_amount"])) {
                    $lblReimAmount = appRenderLabel(appFormatRupiah($item["reim_amount"]), "140px");
                }

                $lblUserCategory = "";
                if (!empty($item["usr_group_category"])) {
                    $lblUserCategory = appRenderLabel(masterUserCategoryInGroup($item["usr_group_category"])["label"], "140px");
                }
                $row[] = "" . $btnActions . "";
                $row[] =  appRenderLabel($item["group_name"], "140px");
                $row[] = appRenderLabel($item["usr_username"], "240px");
                $row[] = appRenderBadgeUserRole($item["usr_role"]);
                $row[] =  $lblUserCategory;
                $row[] =  appRenderLabel($item["reim_code"], "140px");
                $row[] =  $lblReimStatus;
                $row[] =  appRenderLabel($item["cat_name"], "240px");
                $row[] =  $lblReimAmount;

                $data[] = $row;
            endforeach;
            $output = [
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->ReimbursementModel->dTblCountAllUserForReim($conditions),
                "recordsFiltered" => $this->ReimbursementModel->dTblCountFilteredUserForReim($conditions),
                "data" => $data,
                "code" => "success",
                "message" => "Request Done.",
            ];
            echo json_encode($output);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            echo json_encode(appEmptyDTBL("error", $th->getMessage()));
            return;
        }
    }
}
