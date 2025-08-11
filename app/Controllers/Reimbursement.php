<?php

namespace App\Controllers;

use App\Libraries\FileLimiter;
use App\Models\BerkasModel;
use App\Models\CategoryModel;
use App\Models\JenisBerkasModel;
use App\Models\ReimbursementModel;
use App\Models\SubmissionWindowModel;
use App\Models\UserModel;
use Exception;

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
            $dAccess = authVerifyAccess(false, "c_reimbursement");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $sessGroupId = $dAccess["data"]["group_id"];
            $sessGroupName = $dAccess["data"]["group_name"];

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
            if (!empty($sessGroupId)) {
                if ($sessGroupId != $dUser["usr_group_id"]) {
                    throw new Exception("Anda hanya boleh mengisi untuk group " . $sessGroupName, 400);
                }
            } else {
                if ($sessUsrId != authMasterUserId()) {
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

                        // Upload
                        $newName = $file->getRandomName();
                        $uploadPath = appConfigDataPath("reimbursement/berkas/" . $swTahun . "/" . "triwulan_" . $swTriwulan . "/" . $userId . "_" . $dUser["usr_key"]);
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
                    if((getenv("demo"))){
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
            if ($this->ReimbursementModel->edit($dEdit, [
                "reim_id" => $reimId,
            ])) {
                log_message("alert", "simpan perubahan draft berhasil.");
                $dFailed = [];
                $doRollback = false;
                $jbFiles = $this->request->getFiles();
                foreach ($jbFiles['file_jb'] as $id => $file) :
                    $jenisBerkasId = $id;

                    // Cek jika tidak ada ID jenis berkas
                    if (empty($jenisBerkasId)) {
                        log_message("alert", "ID jenis berkas kosong.");
                        $doRollback = true;
                        $dFailed[] = "Id Jenis Berkas tidak ditemukan.";
                        continue;
                    }

                    $dJenisBerkas = $this->JenisBerkasModel->get([
                        "jb_id" => $jenisBerkasId,
                    ], true);

                    if (empty($dJenisBerkas)) {
                        log_message("alert", "Jenis berkas dengan ID $jenisBerkasId tidak ditemukan.");
                        $doRollback = true;
                        $dFailed[] = "Jenis berkas dengan ID $jenisBerkasId tidak ditemukan.";
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
                            $dFailed[] = "Berkas $jenisBerkasId wajib diunggah, tapi tidak ada file.";
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
                            $dFailed[] = "File untuk ID $id melebihi batas ukuran maksimum {$jbMaxFileSizeMb}MB.";
                            continue;
                        }

                        // Validasi ekstensi dan mime
                        $ext = strtolower($file->getExtension());
                        $mime = $file->getMimeType();

                        if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimeTypes)) {
                            log_message("alert", "File ID $id memiliki tipe yang tidak diizinkan. Ext: $ext, Mime: $mime.");
                            $doRollback = true;
                            $dFailed[] = "File ID $id memiliki tipe yang tidak diizinkan. Ext: $ext, Mime: $mime.";
                            continue;
                        }

                        // Upload
                        $newName = $file->getRandomName();
                        $uploadPath = appConfigDataPath("reimbursement/berkas/" . $reimTriwulanTahun . "/" . "triwulan_" . $reimTriwulan . "/" . $reimClaimantUsrId . "_" . $reimClaimantUsrKey);
                        $filePath = $uploadPath . "/" . $newName;
                        $file->move($uploadPath, $newName);
                        if (!file_exists($filePath)) {
                            $doRollback = true;
                            log_message("alert", "File untuk ID $id tidak valid atau gagal diproses.");
                            $dFailed[] = "File untuk ID $id tidak valid atau gagal diproses.";
                            continue;
                        }
                        $uploadedFiles[] = $newName;
                        $rbKey = $this->BerkasModel->generateKey();
                        $rbNote = $this->request->getPost("txt_file_note[" . $id . "]") ?? "";
                        $dAddBerkas = [
                            "rb_key" => $rbKey,
                            "rb_by_usr_id" => $sessUsrId,
                            "rb_reim_id" => $reimId,
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
                            $dFailed[] = "File untuk ID $id tidak valid atau gagal diproses.";
                            continue;
                        }
                    } else {
                        log_message("alert", "File untuk ID $id tidak valid atau gagal diproses.");
                        $doRollback = true;
                        $dFailed[] = "File untuk ID $id tidak valid atau gagal diproses.";
                        continue;
                    }
                endforeach;
                log_message("alert", "reimberkas=" . json_encode($dFailed));
                $redirect = base_url("reimbursement/draft?reim_key=" . $reimKey);
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

            $dReimbursements = $this->ReimbursementModel->get($dWhere);
            $this->cData["dReimbursements"] = $dReimbursements;
            return view($this->viewDir . "/list", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
