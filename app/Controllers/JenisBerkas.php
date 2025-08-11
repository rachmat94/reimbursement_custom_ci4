<?php

namespace App\Controllers;

use App\Models\JenisBerkasModel;
use Exception;

class JenisBerkas extends BaseController
{
    private $cnm = "jenisberkas";
    private $cnmSlug = "jenis-berkas";
    private $viewDir = "jenisberkas";
    private $title = "Jenis Berkas";
    private $pageHeader = "Jenis Berkas";
    private $cData;

    private $JenisBerkasModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->JenisBerkasModel = new JenisBerkasModel();
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
            $dAccess = authVerifyAccess(false, "d_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $jbKey = $this->request->getPost("hdn_jb_key") ?? "";

            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data Not found.", 400);
            }

            if ($this->JenisBerkasModel->del([
                "jb_key" => $jbKey,
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
            $dAccess = authVerifyAccess(false, "c_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $jbKey = $this->request->getPost("jb_key") ?? "";
            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dJenisBerkas" => $dJenisBerkas,
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
            $dAccess = authVerifyAccess(false, "c_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $jbKey = $this->request->getPost("jb_key") ?? "";
            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dJenisBerkas" => $dJenisBerkas,
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
            $dAccess = authVerifyAccess(false, "u_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $jbKey = $this->request->getPost("hdn_jb_key") ?? "";
            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $description = $this->request->getPost("txt_description") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 1;
            $maxFileSize = $this->request->getPost("nbr_max_file_size") ?? 5;

            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }
            if (empty($name)) {
                throw new Exception("Required name", 400);
            }
            if ($maxFileSize < 1) {
                throw new Exception("Invalid Max File size.", 400);
            }
            if (empty($code)) {
                $code = $this->JenisBerkasModel->generateCode();
            }
            $code = str_replace(" ", "", $code);
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data Not found.", 400);
            }
            if ($this->JenisBerkasModel->get([
                "jb_code" => $code,
                "jb_key !=" => $jbKey,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }

            $dEdit = [
                "jb_code" => $code,
                "jb_name" => $name,
                "jb_max_file_size_mb" => $maxFileSize,
                "jb_description" => $description,
                "jb_is_active" => $status,
                "jb_updated_at" => appCurrentDateTime(),
            ];

            if ($this->JenisBerkasModel->edit($dEdit, [
                "jb_key" => $jbKey,
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
            $dAccess = authVerifyAccess(false, "c_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $jbKey = $this->request->getPost("jb_key") ?? "";
            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dJenisBerkas" => $dJenisBerkas,
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
            $dAccess = authVerifyAccess(false, "c_jenis_berkas");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 0;
            $description = $this->request->getPost("txt_description") ?? "";
            $maxFileSize = $this->request->getPost("nbr_max_file_size") ?? 5;

            if (empty($name)) {
                throw new Exception("Required Name.", 400);
            }

            if ($maxFileSize < 1) {
                throw new Exception("Invalid max file size.", 400);
            }
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status.", 400);
            }

            if (empty($code)) {
                $code = $this->JenisBerkasModel->generateCode();
            }

            $code = str_replace(" ", "", $code);
            if ($this->JenisBerkasModel->get([
                "jb_code" => $code,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }
            $jbKey = $this->JenisBerkasModel->generateKey();
            $dAdd = [
                "jb_key" => $jbKey,
                "jb_by_usr_id" => $sessUsrId,
                "jb_code" => $code,
                "jb_name" => $name,
                "jb_max_file_size_mb" => $maxFileSize,
                "jb_description" => $description,
                "jb_is_active" => $status,
                "jb_created_at" => appCurrentDateTime(),
            ];
            if ($this->JenisBerkasModel->add($dAdd)) {
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
            $dAccess = authVerifyAccess(false, "c_jenis_berkas");
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
            $dAccess = authVerifyAccess(false, "r_jenis_berkas");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $jbKey = $this->request->getGet("jb_key") ?? "";
            if (empty($jbKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dJenisBerkas = $this->JenisBerkasModel->get([
                "jb_key" => $jbKey,
            ], true);
            if (empty($dJenisBerkas)) {
                throw new Exception("Data not found.", 400);
            }

            $this->cData["dJenisBerkas"] = $dJenisBerkas;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_jenis_berkas");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $dJenisBerkass = $this->JenisBerkasModel->get();
            $no = 0;
            $dDtbl = [];
            foreach ($dJenisBerkass as $item) :
                $no++;
                $jbId = $item["jb_id"];
                $jbKey = $item["jb_key"];

                $btnView = appRenderBtnLinkDtbl(
                    base_url($this->cnmSlug . '/view?jb_key=' . $jbKey),
                    "<i class='fas fa-folder-open text-center' style='width:20px'></i> View"
                );
                $btnPreview = appRenderBtnPreviewDTbl($jbKey, "showPreviewJenisBerkas");
                if (authVerifyAccess(false, "u_jenis_berkas", false)) {
                    $btnEdit = appRenderBtnDtbl($jbKey, "<i class='fas fa-edit'></i> Edit", "showEditJenisBerkas");
                } else {
                    $btnEdit = "";
                }

                if (authVerifyAccess(false, "d_jenis_berkas", false)) {
                    $btnDelete = appRenderBtnDtbl($jbKey, "<i class='fas fa-trash'></i> Delete", "showDeleteJenisBerkas");
                } else {
                    $btnDelete = "";
                }

                $row = [];
                $row[] =  $jbId;
                if (
                    $btnView == ""  &&
                    $btnDelete == "" &&
                    $btnEdit == ""
                ) {
                    $btnActions = "<div class='btn-group'>" . $btnPreview . "</div>";
                } else {
                    $btnActions = appRenderActionsDTbl([
                        "id" => $jbId,
                        "actions" => [$btnPreview],
                        "menu" => [$btnView, $btnEdit, $btnDelete]
                    ]);
                }

                $row[] = $btnActions;
                $row[] = appRenderBadgeStatus($item["jb_is_active"]);
                $row[] = appRenderLabel($item["jb_code"], "140px");
                $row[] = appRenderLabel($item["jb_name"], "240px");
                $row[] = appRenderLabel($item["jb_max_file_size_mb"],"60");
                $row[] = appRenderLabel($item["jb_created_at"], "160px");
                $dDtbl[] = $row;
            endforeach;
            $this->cData["dDtblJenisBerkas"] = $dDtbl;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
