<?php

namespace App\Controllers;

use App\Models\GroupModel;
use App\Models\ReimbursementModel;
use App\Models\SubmissionWindowModel;
use Exception;
use Kint\Value\ArrayValue;

class Group extends BaseController
{
    private $cnm = "group";
    private $cnmSlug = "group";
    private $viewDir = "group";
    private $title = "Group";
    private $pageHeader = "Group";
    private $cData;

    private $GroupModel;
    private $SubmissionWindowModel;
    private $ReimbursementModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->GroupModel = new GroupModel();
        $this->SubmissionWindowModel = new SubmissionWindowModel();
        $this->ReimbursementModel = new ReimbursementModel();
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
            $dAccess = authVerifyAccess(false, "d_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $groupKey = $this->request->getPost("hdn_group_key") ?? "";

            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data Not found.", 400);
            }

            if ($this->GroupModel->del([
                "group_key" => $groupKey,
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
            $dAccess = authVerifyAccess(false, "c_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $groupKey = $this->request->getPost("group_key") ?? "";
            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dGroup" => $dGroup,
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
            $dAccess = authVerifyAccess(false, "c_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $groupKey = $this->request->getPost("group_key") ?? "";
            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dGroup" => $dGroup,
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
            $dAccess = authVerifyAccess(false, "u_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $groupKey = $this->request->getPost("hdn_group_key") ?? "";
            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $description = $this->request->getPost("txt_description") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 1;
            $jenis = $this->request->getPost("cbo_jenis_group") ?? "";
            $kecamatan = $this->request->getPost("txt_kecamatan") ?? "";
            $desaKelurahan = $this->request->getPost("txt_desa_kelurahan") ?? "";
            $jmlSaranaPrasarana = $this->request->getPost("nbr_jml_sarana_prasarana") ?? 0;
            $jmlTitikLokasi = $this->request->getPost("nbr_jml_titik_lokasi") ?? Null;

            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            if (empty($name)) {
                throw new Exception("Required name", 400);
            }
            if (empty($code)) {
                $code = $this->GroupModel->generateCode();
            }
            $code = str_replace(" ", "", $code);
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data Not found.", 400);
            }
            if ($this->GroupModel->get([
                "group_code" => $code,
                "group_key !=" => $groupKey,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }

            $dEdit = [
                "group_code" => $code,
                "group_name" => $name,
                "group_jenis" => $jenis,
                "group_kecamatan" => $kecamatan,
                "group_desa_kelurahan" => $desaKelurahan,
                "group_jml_sarana_prasarana" => $jmlSaranaPrasarana,
                "group_jml_titik_lokasi" => $jmlTitikLokasi,
                "group_description" => $description,
                "group_is_active" => $status,
                "group_updated_at" => appCurrentDateTime(),
            ];

            if ($this->GroupModel->edit($dEdit, [
                "group_key" => $groupKey,
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
            $dAccess = authVerifyAccess(false, "c_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $groupKey = $this->request->getPost("group_key") ?? "";
            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dGroup" => $dGroup,
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
            $dAccess = authVerifyAccess(false, "c_group");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 0;
            $description = $this->request->getPost("txt_description") ?? "";
            $jenis = $this->request->getPost("cbo_jenis_group") ?? "";
            $kecamatan = $this->request->getPost("txt_kecamatan") ?? "";
            $desaKelurahan = $this->request->getPost("txt_desa_kelurahan") ?? "";
            $jmlSaranaPrasarana = $this->request->getPost("nbr_jml_sarana_prasarana") ?? 0;
            $jmlTitikLokasi = $this->request->getPost("nbr_jml_titik_lokasi") ?? Null;

            if (empty($name)) {
                throw new Exception("Required Name.", 400);
            }
            if (!in_array($jenis, array_keys(masterJenisGroup()))) {
                throw new Exception("Invalid Jenis Group", 400);
            }
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status.", 400);
            }

            if (empty($code)) {
                $code = $this->GroupModel->generateCode();
            }

            $code = str_replace(" ", "", $code);
            if ($this->GroupModel->get([
                "group_code" => $code,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }
            $groupKey = $this->GroupModel->generateKey();
            $dAdd = [
                "group_key" => $groupKey,
                "group_by_usr_id" => $sessUsrId,
                "group_code" => $code,
                "group_name" => $name,
                "group_jenis" => $jenis,
                "group_kecamatan" => $kecamatan,
                "group_desa_kelurahan" => $desaKelurahan,
                "group_jml_sarana_prasarana" => $jmlSaranaPrasarana,
                "group_jml_titik_lokasi" => $jmlTitikLokasi,
                "group_description" => $description,
                "group_is_active" => $status,
                "group_created_at" => appCurrentDateTime(),
            ];
            if ($this->GroupModel->add($dAdd)) {
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
            $dAccess = authVerifyAccess(false, "c_group");
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
            $dAccess = authVerifyAccess(false, "r_group");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $groupKey = $this->request->getGet("group_key") ?? "";
            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data not found.", 400);
            }

            $this->cData["dGroup"] = $dGroup;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_group");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $dGroups = $this->GroupModel->get();
            $no = 0;
            $dDtbl = [];
            foreach ($dGroups as $item) :
                $no++;
                $groupId = $item["group_id"];
                $groupKey = $item["group_key"];

                $btnView = appRenderBtnLinkDtbl(
                    base_url($this->cnmSlug . '/view?group_key=' . $groupKey),
                    "<i class='fas fa-folder-open text-center' style='width:20px'></i> View"
                );
                $btnUser = appRenderBtnLinkDtbl(
                    base_url($this->cnmSlug . '/user?group_key=' . $groupKey),
                    "<i class='fas fa-users text-center' style='width:20px'></i> User"
                );
                $btnPreview = appRenderBtnPreviewDTbl($groupKey, "showPreviewGroup");
                if (authVerifyAccess(false, "u_group", false)) {
                    $btnEdit = appRenderBtnDtbl($groupKey, "<i class='fas fa-edit'></i> Edit", "showEditGroup");
                } else {
                    $btnEdit = "";
                }

                if (authVerifyAccess(false, "d_group", false)) {
                    $btnDelete = appRenderBtnDtbl($groupKey, "<i class='fas fa-trash'></i> Delete", "showDeleteGroup");
                } else {
                    $btnDelete = "";
                }

                $row = [];
                $row[] =  $groupId;
                if (
                    $btnView == ""  &&
                    $btnDelete == "" &&
                    $btnEdit == ""
                ) {
                    $btnActions = "<div class='btn-group'>" . $btnPreview . "</div>";
                } else {
                    $btnActions = appRenderActionsDTbl([
                        "id" => $groupId,
                        "actions" => [$btnPreview],
                        "menu" => [$btnView, $btnUser, $btnEdit, $btnDelete]
                    ]);
                }

                $row[] = $btnActions;
                $row[] = appRenderBadgeStatus($item["group_is_active"]);
                $row[] = appRenderLabel($item["group_code"], "120px");
                $row[] = appRenderLabel($item["group_name"], "240px");
                $row[] = appRenderLabel(masterJenisGroup($item["group_jenis"], true)["label"]);
                $row[] = appRenderLabel($item["usr_username"], "240px");
                $row[] = appRenderLabel($item["leader_username"], "240px");
                $row[] = appRenderLabel($item["group_created_at"], "160px");
                $dDtbl[] = $row;
            endforeach;
            $this->cData["dDtblGroups"] = $dDtbl;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function user()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_group_user");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $groupKey = $this->request->getGet("group_key") ?? "";
            if (empty($groupKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dGroup = $this->GroupModel->get([
                "group_key" => $groupKey,
            ], true);
            if (empty($dGroup)) {
                throw new Exception("Data not found.", 400);
            }
            $groupId = $dGroup["group_id"];
            $dGroupUsers = $this->GroupModel->getUsers($groupId);
            $dSubSchedule = $this->SubmissionWindowModel->get([
                "sw_tahun" => date("Y"),
            ]);
            $dGUserReim = [];
            foreach ($dGroupUsers as $kGUser => $vGUser) {
                foreach ($dSubSchedule as $kSub  => $vSub) {
                    $subId = $vSub["sw_id"];
                    $swTriwulan = $vSub["sw_triwulan"];
                    $swTahun = $vSub["sw_tahun"];
                    $dUReim = $this->ReimbursementModel->get([
                        "reim_claimant_usr_id" => $vGUser["usr_id"],
                        "reim_triwulan_no" => $swTriwulan,
                        "reim_triwulan_tahun" => $swTahun,
                    ], true);
                    if ($dUReim) {
                        $vGUser["dReimbursement"][$swTriwulan] = $dUReim;
                    } else {
                        $vGUser["dReimbursement"][$swTriwulan] = null;
                    }
                }
                $dGUserReim[] = $vGUser;
            }

            $this->cData["dSubSchedule"] = $dSubSchedule;
            $this->cData["dGroupUsers"] = $dGUserReim;
            $this->cData["dGroup"] = $dGroup;

            return view($this->viewDir . "/user", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
