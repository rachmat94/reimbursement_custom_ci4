<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use Exception;

class Category extends BaseController
{
    private $cnm = "category";
    private $cnmSlug = "category";
    private $viewDir = "category";
    private $title = "Category Reimbursement";
    private $pageHeader = "Category Reimbursement";
    private $cData;

    private $CategoryModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->CategoryModel = new CategoryModel();
    }

    public function showForSelect()
    {
        $redirect = $this->request->getUserAgent()->getReferrer();
        $this->_onlyPostandAjax();
        try {
            $dAccess = authVerifyAccess(false, "r_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];
            $dCategories = $this->CategoryModel->get([
                "cat_is_active" => 1,
            ]);
            $dView = [
                "view_dir" => $this->viewDir,
                "dCategories" => $dCategories,
            ];
            $view = appViewInjectModal($this->viewDir, "select_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "do_select_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appJsonRespondError(true, $th->getMessage() . " [ " . $th->getLine() . " ]", $redirect);
            return;
        }
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
            $dAccess = authVerifyAccess(false, "d_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $catKey = $this->request->getPost("hdn_cat_key") ?? "";

            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data Not found.", 400);
            }

            if ($this->CategoryModel->del([
                "cat_key" => $catKey,
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
            $dAccess = authVerifyAccess(false, "c_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $catKey = $this->request->getPost("cat_key") ?? "";
            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dCategory" => $dCategory,
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
            $dAccess = authVerifyAccess(false, "c_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $catKey = $this->request->getPost("cat_key") ?? "";
            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dCategory" => $dCategory,
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
            $dAccess = authVerifyAccess(false, "u_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $catKey = $this->request->getPost("hdn_cat_key") ?? "";
            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $description = $this->request->getPost("txt_description") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 1;

            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }
            if (empty($name)) {
                throw new Exception("Required name", 400);
            }
            if (empty($code)) {
                $code = $this->CategoryModel->generateCode();
            }
            $code = str_replace(" ", "", $code);
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status", 400);
            }
            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data Not found.", 400);
            }
            if ($this->CategoryModel->get([
                "cat_code" => $code,
                "cat_key !=" => $catKey,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }

            $dEdit = [
                "cat_code" => $code,
                "cat_name" => $name,
                "cat_description" => $description,
                "cat_is_active" => $status,
                "cat_updated_at" => appCurrentDateTime(),
            ];

            if ($this->CategoryModel->edit($dEdit, [
                "cat_key" => $catKey,
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
            $dAccess = authVerifyAccess(false, "c_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $catKey = $this->request->getPost("cat_key") ?? "";
            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data not found.", 400);
            }

            $dView = [
                "viewDir" => $this->viewDir,
                "dCategory" => $dCategory,
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
            $dAccess = authVerifyAccess(false, "c_category");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $code = $this->request->getPost("txt_code") ?? "";
            $name = $this->request->getPost("txt_name") ?? "";
            $status = $this->request->getPost("cbo_status") ?? 0;
            $description = $this->request->getPost("txt_description") ?? "";

            if (empty($name)) {
                throw new Exception("Required Name.", 400);
            }
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status.", 400);
            }

            if (empty($code)) {
                $code = $this->CategoryModel->generateCode();
            }

            $code = str_replace(" ", "", $code);
            if ($this->CategoryModel->get([
                "cat_code" => $code,
            ], true)) {
                throw new Exception("Code already exist.", 400);
            }
            $catKey = $this->CategoryModel->generateKey();
            $dAdd = [
                "cat_key" => $catKey,
                "cat_by_usr_id" => $sessUsrId,
                "cat_code" => $code,
                "cat_name" => $name,
                "cat_description" => $description,
                "cat_is_active" => $status,
                "cat_created_at" => appCurrentDateTime(),
            ];
            if ($this->CategoryModel->add($dAdd)) {
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
            $dAccess = authVerifyAccess(false, "c_category");
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
            $dAccess = authVerifyAccess(false, "r_category");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $catKey = $this->request->getGet("cat_key") ?? "";
            if (empty($catKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dCategory = $this->CategoryModel->get([
                "cat_key" => $catKey,
            ], true);
            if (empty($dCategory)) {
                throw new Exception("Data not found.", 400);
            }

            $this->cData["dCategory"] = $dCategory;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_category");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }
            $this->cData["dAccess"] = $dAccess;
            $dCategories = $this->CategoryModel->get();
            $no = 0;
            $dDtbl = [];
            foreach ($dCategories as $item) :
                $no++;
                $catId = $item["cat_id"];
                $catKey = $item["cat_key"];

                $btnView = appRenderBtnLinkDtbl(
                    base_url($this->cnmSlug . '/view?cat_key=' . $catKey),
                    "<i class='fas fa-folder-open text-center' style='width:20px'></i> View"
                );
                $btnPreview = appRenderBtnPreviewDTbl($catKey, "showPreviewCategory");
                if (authVerifyAccess(false, "u_category", false)) {
                    $btnEdit = appRenderBtnDtbl($catKey, "<i class='fas fa-edit'></i> Edit", "showEditCategory");
                } else {
                    $btnEdit = "";
                }

                if (authVerifyAccess(false, "d_category", false)) {
                    $btnDelete = appRenderBtnDtbl($catKey, "<i class='fas fa-trash'></i> Delete", "showDeleteCategory");
                } else {
                    $btnDelete = "";
                }

                $row = [];
                $row[] =  $catId;
                if (
                    $btnView == ""  &&
                    $btnDelete == "" &&
                    $btnEdit == ""
                ) {
                    $btnActions = "<div class='btn-group'>" . $btnPreview . "</div>";
                } else {
                    $btnActions = appRenderActionsDTbl([
                        "id" => $catId,
                        "actions" => [$btnPreview],
                        "menu" => [$btnView, $btnEdit, $btnDelete]
                    ]);
                }

                $row[] = $btnActions;
                $row[] = appRenderBadgeStatus($item["cat_is_active"]);
                $row[] = appRenderLabel($item["cat_code"], "140px");
                $row[] = appRenderLabel($item["cat_name"], "240px");
                $row[] = appRenderLabel($item["cat_created_at"], "160px");
                $dDtbl[] = $row;
            endforeach;
            $this->cData["dDtblCategories"] = $dDtbl;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
