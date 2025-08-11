<?php

namespace App\Controllers;

use App\Models\SubmissionWindowModel;
use Exception;

class SubmissionSchedule extends BaseController
{
    private $cnm = "submissionschedule";
    private $cnmSlug = "submission-schedule";
    private $viewDir = "submissionschedule";
    private $title = "Jadwal Pengajuan";
    private $pageHeader = "Jadwal Pengajuan";
    private $cData;

    private $SubmissionWindowModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->SubmissionWindowModel = new SubmissionWindowModel();
    }

    public function doDelete()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "d_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $swKey = $this->request->getPost("hdn_sw_key") ?? "";

            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dSubmissionSchedule = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data Not found.", 400);
            }

            if ($this->SubmissionWindowModel->del([
                "sw_key" => $swKey,
            ])) {
                appJsonRespondSuccess(true, "Deleted.", $redirect);
                return;
            } else {
                throw new Exception("Delete failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function showDelete()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "c_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $swKey = $this->request->getPost("sw_key") ?? "";
            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dSubmissionSchedule = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dSubmissionSchedule" => $dSubmissionSchedule,
            ];
            $view = appViewInjectModal($this->viewDir, "delete_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_delete_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function showPreview()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "c_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $swKey = $this->request->getPost("sw_key") ?? "";
            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dSubmissionSchedule = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dSubmissionSchedule" => $dSubmissionSchedule,
            ];
            $view = appViewInjectModal($this->viewDir, "preview_modal", $dView);
            $script = "";
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function doEdit()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "u_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $swKey = $this->request->getPost("hdn_sw_key") ?? "";
            $triwulan = $this->request->getPost("cbo_triwulan") ?? null;
            $tahun = $this->request->getPost("cbo_tahun") ?? null;
            $startDate = $this->request->getPost("dt_start") ?? null;
            $endDate = $this->request->getPost("dt_end") ?? null;
            $isLocked = $this->request->getPost("cbo_locked") ?? 1;

            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }
            if (empty($triwulan)) {
                throw new Exception("Required Triwulan", 400);
            }
            if (!in_array($triwulan, [1, 2, 3, 4])) {
                throw new Exception("Invalid Triwulan", 400);
            }
            if (empty($tahun)) {
                throw new Exception("Required Tahun.", 400);
            }
            if (empty($startDate)) {
                throw new Exception("Required Start Date.", 400);
            }
            if (empty($endDate)) {
                throw new Exception("Required End Date.", 400);
            }
            if (!in_array($isLocked, [0, 1])) {
                throw new Exception("Invalid Locked.", 400);
            }

            if (! appValidateDate($startDate)) {
                throw new Exception("Invalid start date.", 400);
            }

            if (!appValidateDate($endDate)) {
                throw new Exception("Invalid end date.", 400);
            }
            
            $dSubmissionWindow = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionWindow)) {
                throw new Exception("Data Not found.", 400);
            }
            if ($this->SubmissionWindowModel->get([
                "sw_triwulan" => $triwulan,
                "sw_tahun" => $tahun,
                "sw_key !=" => $swKey,
            ], true)) {
                throw new Exception("Triwulan sudah ada.", 400);
            }

            $dEdit = [
                "sw_triwulan" => $triwulan,
                "sw_tahun" => $tahun,
                "sw_start_date" => $startDate,
                "sw_end_date" => $endDate,
                "sw_is_locked" => $isLocked,
                "sw_updated_at" => appCurrentDateTime(),
            ];

            if ($this->SubmissionWindowModel->edit($dEdit, [
                "sw_key" => $swKey,
            ])) {
                appJsonRespondSuccess(true, "Updated.", $redirect);
                return;
            } else {
                throw new Exception("Update failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function showEdit()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "c_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $swKey = $this->request->getPost("sw_key") ?? "";
            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dSubmissionSchedule = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dSubmissionSchedule" => $dSubmissionSchedule,
            ];
            $view = appViewInjectModal($this->viewDir, "edit_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_edit_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function doAdd()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "c_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $triwulan = $this->request->getPost("cbo_triwulan") ?? null;
            $tahun = $this->request->getPost("cbo_tahun") ?? null;
            $startDate = $this->request->getPost("dt_start") ?? null;
            $endDate = $this->request->getPost("dt_end") ?? null;
            $isLocked = $this->request->getPost("cbo_locked") ?? 1;

            if (empty($triwulan)) {
                throw new Exception("Required Triwulan", 400);
            }
            if (!in_array($triwulan, [1, 2, 3, 4])) {
                throw new Exception("Invalid Triwulan", 400);
            }
            if (empty($tahun)) {
                throw new Exception("Required Tahun.", 400);
            }
            if (empty($startDate)) {
                throw new Exception("Required Start Date.", 400);
            }
            if (empty($endDate)) {
                throw new Exception("Required End Date.", 400);
            }
            if (!in_array($isLocked, [0, 1])) {
                throw new Exception("Invalid Locked.", 400);
            }

            if (! appValidateDate($startDate)) {
                throw new Exception("Invalid start date.", 400);
            }

            if (!appValidateDate($endDate)) {
                throw new Exception("Invalid end date.", 400);
            }
           
            if ($this->SubmissionWindowModel->get([
                "sw_triwulan" => $triwulan,
                "sw_tahun" => $tahun,
            ], true)) {
                throw new Exception("Triwulan sudah ada.", 400);
            }
            $swKey = $this->SubmissionWindowModel->generateKey();
            $dAdd = [
                "sw_key" => $swKey,
                "sw_by_usr_id" => $sessUsrId,
                "sw_triwulan" => $triwulan,
                "sw_tahun" => $tahun,
                "sw_start_date" => $startDate,
                "sw_end_date" => $endDate,
                "sw_is_locked" => $isLocked,
                "sw_created_at" => appCurrentDateTime(),
            ];
            if ($this->SubmissionWindowModel->add($dAdd)) {
                appJsonRespondSuccess(true, "Saved.", $redirect);
                return;
            } else {
                throw new Exception("Failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function showAdd()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid Request token.Try Reload again.", 400);
            }
            $dAccess = authVerifyAccess(false, "c_submission_schedule");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $dView = [
                "viewDir" => $this->viewDir,
            ];
            $view = appViewInjectModal($this->viewDir, "add_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_add_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function view()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_submission_schedule");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $swKey = $this->request->getGet("sw_key") ?? "";
            if (empty($swKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dSubmissionSchedule = $this->SubmissionWindowModel->get([
                "sw_key" => $swKey,
            ], true);
            if (empty($dSubmissionSchedule)) {
                throw new Exception("Data not found.", 400);
            }

            $this->cData["dSubmissionSchedule"] = $dSubmissionSchedule;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_submission_schedule");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $dSubmissionWindows = $this->SubmissionWindowModel->get();
            $no = 0;
            $dDtbl = [];
            foreach ($dSubmissionWindows as $item) :
                $no++;
                $swId = $item["sw_id"];
                $swKey = $item["sw_key"];

                $btnView = appRenderBtnLinkDtbl(
                    base_url($this->cnmSlug . '/view?sw_key=' . $swKey),
                    "<i class='fas fa-folder-open text-center' style='width:20px'></i> View"
                );
                $btnPreview = appRenderBtnPreviewDTbl($swKey, "showPreviewSubmissionSchedule");
                if (authVerifyAccess(false, "u_submission_schedule", false)) {
                    $btnEdit = appRenderBtnDtbl($swKey, "<i class='fas fa-edit'></i> Edit", "showEditSubmissionSchedule");
                } else {
                    $btnEdit = "";
                }

                if (authVerifyAccess(false, "d_submission_schedule", false)) {
                    $btnDelete = appRenderBtnDtbl($swKey, "<i class='fas fa-trash'></i> Delete", "showDeleteSubmissionSchedule");
                } else {
                    $btnDelete = "";
                }

                $row = [];
                $row[] =  $swId;
                if (
                    $btnView == ""  &&
                    $btnDelete == "" &&
                    $btnEdit == ""
                ) {
                    $btnActions = "<div class='btn-group'>" . $btnPreview . "</div>";
                } else {
                    $btnActions = appRenderActionsDTbl([
                        "id" => $swId,
                        "actions" => [$btnPreview],
                        "menu" => [$btnView, $btnEdit, $btnDelete]
                    ]);
                }

                $row[] = $btnActions;
                $row[] = appRenderBadgeLocked($item["sw_is_locked"]);
                $row[] = appRenderLabel($item["sw_tahun"], "100px");
                $row[] = appRenderLabel($item["sw_triwulan"], "60px");
                $row[] = appRenderLabel($item["sw_start_date"], "140px");
                $row[] = appRenderLabel($item["sw_end_date"], "140px");
                $row[] = appRenderLabel($item["sw_created_at"], "160px");
                $dDtbl[] = $row;
            endforeach;
            $this->cData["dDtblSubmissionSchedule"] = $dDtbl;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
