<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GroupModel;
use App\Models\UserModel;
use Exception;

class User extends BaseController
{
    private $cnm = "user";
    private $cnmSlug = "user";
    private $viewDir = "user";
    private $title = "User";
    private $pageHeader = "User";
    private $cData;

    private $UserModel;
    private $GroupModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnmSlug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->UserModel = new UserModel();
        $this->GroupModel = new GroupModel();
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $this->cData["dAccess"] = $dAccess;

            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function add()
    {
        try {
            $dAccess = authVerifyAccess(false, "c_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $this->cData["dAccess"] = $dAccess;
            $dGroups = $this->GroupModel->get([
                "group_is_active" => 1,
            ]);
            $this->cData["dGroups"] = $dGroups;

            $this->cData["header"] = $this->cData["title"] = "Create new User";
            return view($this->viewDir . "/add", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function view()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $this->cData["dAccess"] = $dAccess;
            $usrKey = $this->request->getGet("usr_key") ?? "";
            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 404);
            }
            unset($dUser["usr_password"]);
            $this->cData["dUser"] = $dUser;
            return view($this->viewDir . "/view", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doAdd()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();

        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "c_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $email = trim($this->request->getPost("txt_email") ?? "");
            $username = trim($this->request->getPost("txt_username") ?? "");
            $role = strtolower(trim($this->request->getPost("cbo_role") ?? ""));
            $groupId = (($this->request->getPost("cbo_group") ?? null));
            $groupCategory = strtolower(trim($this->request->getPost("cbo_group_category") ?? ""));

            if (!appIsEmailValid($email)) {
                throw new Exception("Invalid Email format.", 400);
            }
            $dValidateUsername = appValidateUsername($username);
            if ($dValidateUsername["code"] != "success") {
                throw new Exception($dValidateUsername["message"], 400);
            }

            if (!empty($groupCategory)) {
                if (!in_array($groupCategory, array_keys(masterUserCategoryInGroup()))) {
                    throw new Exception("Invalid User Category in Group.", 400);
                }
            }
            $dGroup = null;
            $doSetAsGroupLeader = false;
            if (!empty($groupId)) {
                $dGroup = $this->GroupModel->get([
                    "group_id" => $groupId,
                ], true);
                if (empty($dGroup)) {
                    throw new Exception("Invalid Group", 400);
                }
                if ($groupCategory == "ketua") {
                    if (empty($dGroup["group_leader_usr_id"])) {
                        $doSetAsGroupLeader = true;
                    } else {
                        throw new Exception("Group " . $dGroup["group_name"] . " sudah memiliki ketua", 400);
                    }
                }
            }

            if (empty($role)) {
                $role = "unassigned";
            }
            if (!in_array($role, array_keys(masterUserRole()))) {
                throw new Exception("Invalid Role." . $role . "." . json_encode(array_keys(masterUserRole())), 400);
            }

            $doSetAsGroupAdmin = false;
            if ($role == "admin_group") {
                if (!empty($dGroup)) {
                    if (!empty($dGroup["group_admin_usr_id"])) {
                        throw new Exception("Group " . $dGroup["group_name"] . " sudah memiliki admin.", 400);
                    } else {
                        $doSetAsGroupAdmin = true;
                    }
                }
            }

            if ($this->UserModel->get([
                "usr_email" => $email,
            ], true)) {
                throw new Exception("Email is exist.", 400);
            }
            if ($this->UserModel->get([
                "usr_username" => $username,
            ], true)) {
                throw new Exception("Username is exist.", 400);
            }
            $byUsrId = $sessUsrId;
            $usrKey = $this->UserModel->generateKey();
            $usrCode  = $this->UserModel->generateCode();
            $password = appGenerateString(6);

            if ($dAccess["data"]["usr_id"] != authMasterUserId()) {
                if (!in_array($role, authRoleBy($dAccess["data"]["usr_role"]))) {
                    throw new Exception("You don't have access to create user with this role.", 403);
                }
            }

            $dAdd = [
                "usr_by_usr_id" => $byUsrId,
                "usr_key" => $usrKey,
                "usr_code" => $usrCode,
                "usr_email" => $email,
                "usr_username" => $username,
                "usr_role" => $role,
                "usr_group_id" => $groupId,
                "usr_group_category" => $groupCategory,
                "usr_password" => password_hash($password, PASSWORD_DEFAULT),
                "usr_is_active" => 1,
                "usr_created_at" => appCurrentDateTime(),
            ];

            $inputFileName = "file_photo";
            $filePhoto = $this->request->getFile($inputFileName);
            $logMsg = [];
            $photoFName = "";
            $photoFPath = "";

            $logMsg = [];
            if (!$this->validate([
                $inputFileName => [
                    "rules" => "uploaded[" . $inputFileName . "]|ext_in[" . $inputFileName . ",jpg,jpeg,png]|mime_in[" . $inputFileName . ",image/jpg,image/jpeg,image/png]|max_size[" . $inputFileName . ",5000]",
                    "errors" => [
                        "uploaded" => 'Nothing to upload.usr_code=' . $usrCode,
                        "max_size" => 'File is too large.',
                        "mime_in" => 'File type not supported.',
                        "ext_in" => "File extension not supported.",
                    ]
                ]
            ])) {
                $validation = \Config\Services::validation();
                $errors = $validation->getErrors();
                $strError = implode("", $errors);
                $logMsg[] = $strError;
            } else {
                if (!$filePhoto->isValid() && $filePhoto->hasMoved()) {
                    $strError = $filePhoto->getErrorString();
                    $logMsg[] = $strError;
                } else {
                    $uploadPath = appConfigDataPath("user/photo");
                    $photoFName = $usrCode . "_" . $filePhoto->getRandomName();
                    $filePhoto->move($uploadPath, $photoFName);
                    $photoFMime = $filePhoto->getClientMimeType();
                    $photoFNameOrigin = $filePhoto->getClientName();
                    $photoFPath = $uploadPath . "/" . $photoFName;
                    $logMsg[] = "File Uploaded.";

                    if (!file_exists($photoFPath)) {
                        $logMsg[] = "Uploaded file not found";
                    } else {
                        $dAdd["usr_photo_file_name"] = $photoFName;
                    }
                }
            }
            log_message("info", implode("|", $logMsg));
            $newUserId = $this->UserModel->add($dAdd);
            if ($newUserId !== false && $newUserId > 0) {
                $redirect = base_url($this->cnmSlug);
                $dEditGroup = [
                    "group_updated_at" => appCurrentDateTime(),
                ];
                if ($doSetAsGroupAdmin) {
                    $dEditGroup["group_admin_usr_id"] = $newUserId;
                }
                if ($doSetAsGroupLeader) {
                    $dEditGroup["group_leader_usr_id"] = $newUserId;
                }
                if ($this->GroupModel->edit($dEditGroup, [
                    "group_id" => $groupId,
                ])) {
                } else {
                    log_message("error", "update group failed." . $groupId);
                }
                appJsonRespondSuccess(false, "Add new user success.", $redirect);
                return;
            } else {
                if (!empty($photoFPath)) {
                    if (file_exists($photoFPath)) {
                        if (unlink($photoFPath)) {
                            log_message("info", "File deleted: " . $photoFPath);
                        } else {
                            log_message("error", "Failed to delete file: " . $photoFPath);
                        }
                    }
                }
                throw new Exception("Add new user failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function dtblMain()
    {
        $this->_onlyPostandAjax();
        try {
            $dAccess = authVerifyAccess(false, "r_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $conditions = [];
            $list = $this->UserModel->getDtbl($conditions);
            $data = [];
            $no = $_POST['start'] ?? 0;
            foreach ($list as $item) :
                $no++;
                $usrId = $item["usr_id"];
                $usrKey = $item["usr_key"];

                $btnView = appRenderBtnLinkDtbl(
                    base_url('user/view?usr_key=' . $usrKey),
                    "<i class='fas fa-folder-open text-center' style='width:20px'></i> View"
                );
                $btnPreview = appRenderBtnPreviewDTbl($usrKey, "showPreviewUser");
                if (authVerifyAccess(false, "u_user", false)) {
                    $btnEdit = appRenderBtnDtbl($usrKey, "<i class='fas fa-edit'></i> Edit", "showEditUser");
                    $btnResetPassword = appRenderBtnDtbl($usrKey, "<i class='fas fa-edit'></i> Reset Password", "showResetPasswordUser");
                } else {
                    $btnEdit = "";
                    $btnResetPassword = "";
                }

                if (authVerifyAccess(false, "d_user", false)) {
                    $btnDelete = appRenderBtnDtbl($usrKey, "<i class='fas fa-trash'></i> Delete", "showDeleteUser");
                } else {
                    $btnDelete = "";
                }

                $row = [];
                $row[] =  $usrId;
                if (
                    $btnView == ""  &&
                    $btnDelete == "" &&
                    $btnEdit == "" &&
                    $btnResetPassword == ""
                ) {
                    $btnActions = "<div class='btn-group'>" . $btnPreview . "</div>";
                } else {
                    $btnActions = appRenderActionsDTbl([
                        "id" => $usrKey,
                        "actions" => [$btnPreview],
                        "menu" => [$btnView, $btnEdit, $btnResetPassword, $btnDelete]
                    ]);
                }


                $photoUrl = assetUser();
                if (!empty($item["usr_photo_file_name"])) {
                    $photoData = appGetUserPhoto($item["usr_key"], $item["usr_photo_file_name"]);
                    if (!empty($photoData["file_url"])) {
                        $photoUrl = $photoData["file_url"];
                    }
                }
                $row[] = "<div class='text-center' style='width:150px'><img src='" . $photoUrl . "' class='img-thumbnail mb-2' style='width: 90px;'>" . "<br>" . $btnActions . "</div>";
                $row[] = appRenderBadgeStatus($item["usr_is_active"]);
                $row[] = appRenderBadgeUserRole($item["usr_role"]);
                $row[] =  appRenderLabel($item["group_name"], "140px");
                $row[] =  appRenderLabel($item["usr_group_category"], "140px");
                $row[] = appRenderLabel($item["usr_email"], "240px");
                $row[] = appRenderLabel($item["usr_username"], "240px");
                $row[] = appRenderLabel($item["usr_created_at"], "160px");
                $data[] = $row;
            endforeach;
            $output = [
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->UserModel->dTblCountAll($conditions),
                "recordsFiltered" => $this->UserModel->dTblCountFiltered($conditions),
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

    public function showPreview()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "r_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("usr_key") ?? "";
            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 404);
            }
            unset($dUser["usr_password"]);
            $dView = [
                "viewDir" => $this->viewDir,
                "dUser" => $dUser,
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

    public function showEdit()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "u_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("usr_key") ?? "";
            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 404);
            }
            unset($dUser["usr_password"]);

            $dView = [
                "viewDir" => $this->viewDir,
                "dUser" => $dUser,
            ];
            $view = appViewInjectModal($this->viewDir, "edit_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_edit_script");
            $script .= appViewInjectScript($this->viewDir, "do_delete_photo_script");
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
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }

            $dAccess = authVerifyAccess(false, "u_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("hdn_usr_key") ?? "";
            $username = trim($this->request->getPost("txt_username") ?? "");
            $status = trim($this->request->getPost("cbo_status") ?? "");
            $role = strtolower(trim($this->request->getPost("cbo_role") ?? ""));

            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dValidateUsername = appValidateUsername($username);
            if ($dValidateUsername["code"] != "success") {
                throw new Exception($dValidateUsername["message"], 400);
            }
            if ($role == "") {
                $role = "unassigned";
            }
            if (!in_array(strtolower($role), array_keys(masterUserRole()))) {
                throw new Exception("Invalid Role", 400);
            }
            if (!in_array($status, [0, 1])) {
                throw new Exception("Invalid status", 400);
            }

            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 400);
            }

            if ($this->UserModel->get([
                "usr_username" => $username,
                "usr_key !=" => $usrKey,
            ], true)) {
                throw new Exception("Username is exist.", 400);
            }

            if ($dAccess["data"]["usr_id"] != authMasterUserId()) {
                if (!in_array($role, authRoleBy($dAccess["data"]["usr_role"]))) {
                    throw new Exception("You don't have access to create user with this role.", 403);
                }
            }
            $dEdit = [
                "usr_username" => $username,
                "usr_role" => $role,
                "usr_is_active" => (int) $status,
                "usr_updated_at" => appCurrentDateTime(),
            ];

            $prevPhotoFName = $dUser["usr_photo_file_name"] ?? "";

            $inputFileName = "file_photo";
            $filePhoto = $this->request->getFile($inputFileName);
            $logMsg = [];
            $photoFName = "";
            $photoFPath = "";

            $logMsg = [];
            if (!$this->validate([
                $inputFileName => [
                    "rules" => "uploaded[" . $inputFileName . "]|ext_in[" . $inputFileName . ",jpg,jpeg,png]|mime_in[" . $inputFileName . ",image/jpg,image/jpeg,image/png]|max_size[" . $inputFileName . ",5000]",
                    "errors" => [
                        "uploaded" => 'Nothing to upload.usr_code=' . $dUser["usr_code"],
                        "max_size" => 'File is too large.',
                        "mime_in" => 'File type not supported.',
                        "ext_in" => "File extension not supported.",
                    ]
                ]
            ])) {
                $validation = \Config\Services::validation();
                $errors = $validation->getErrors();
                $strError = implode("", $errors);
                $logMsg[] = $strError;
            } else {
                if (!$filePhoto->isValid() && $filePhoto->hasMoved()) {
                    $strError = $filePhoto->getErrorString();
                    $logMsg[] = $strError;
                } else {
                    $uploadPath = appConfigDataPath("user/photo");
                    $photoFName = $dUser["usr_code"] . "_" . $filePhoto->getRandomName();
                    $filePhoto->move($uploadPath, $photoFName);
                    $photoFMime = $filePhoto->getClientMimeType();
                    $photoFNameOrigin = $filePhoto->getClientName();
                    $photoFPath = $uploadPath . "/" . $photoFName;
                    $logMsg[] = "File Uploaded.";

                    if (!file_exists($photoFPath)) {
                        $logMsg[] = "Uploaded file not found";
                    } else {
                        $dEdit["usr_photo_file_name"] = $photoFName;
                    }
                }
            }
            log_message("info", implode("|", $logMsg));


            if ($this->UserModel->edit($dEdit, [
                "usr_key" => $usrKey
            ])) {
                if (!empty($prevPhotoFName) && !empty($photoFName) && $prevPhotoFName != $photoFName) {
                    $prevPhotoFPath = appConfigDataPath("user/photo/" . $prevPhotoFName);
                    if (file_exists($prevPhotoFPath)) {
                        if (unlink($prevPhotoFPath)) {
                            log_message("info", "File deleted: " . $prevPhotoFPath);
                        } else {
                            log_message("error", "Failed to delete file: " . $prevPhotoFPath);
                        }
                    }
                }
                appJsonRespondSuccess(false, "Edit user success.", $redirect);
                return;
            } else {
                if (!empty($photoFPath) && file_exists($photoFPath)) {
                    if (unlink($photoFPath)) {
                        log_message("info", "File deleted: " . $photoFPath);
                    } else {
                        log_message("error", "Failed to delete file: " . $photoFPath);
                    }
                }
                throw new Exception("Edit user failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function doDeletePhoto()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();

        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }

            $dAccess = authVerifyAccess(false, "u_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("usr_key") ?? "";

            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }


            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 400);
            }
            $photoFName = $dUser["usr_photo_file_name"] ?? "";
            if (empty($photoFName)) {
                throw new Exception("Photo not found.", 404);
            }
            $photoFPath = appConfigDataPath("user/photo/" . $photoFName);
            if (!file_exists($photoFPath)) {
                throw new Exception("Photo file not found.", 404);
            }
            if ($this->UserModel->edit([
                "usr_photo_file_name" => "",
                "usr_updated_at" => appCurrentDateTime(),
            ], [
                "usr_key" => $usrKey
            ])) {
                if (unlink($photoFPath)) {
                    log_message("info", "File deleted: " . $photoFPath);
                } else {
                    log_message("error", "Failed to delete file: " . $photoFPath);
                }
                appJsonRespondSuccess(false, "Photo has been removed from user.", $redirect);
                return;
            } else {
                throw new Exception("Failed to update user data.", 500);
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
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "d_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("usr_key") ?? "";
            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 404);
            }
            unset($dUser["usr_password"]);
            $dView = [
                "viewDir" => $this->viewDir,
                "dUser" => $dUser,
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

    public function doDelete()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();

        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "d_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("hdn_usr_key") ?? "";

            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }

            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 400);
            }
            $prevPhotoFName = $dUser["usr_photo_file_name"] ?? "";
            if ($this->UserModel->del([
                "usr_key" => $usrKey
            ])) {
                if (!empty($prevPhotoFName)) {
                    $prevPhotoFPath = appConfigDataPath("user/photo/" . $prevPhotoFName);
                    if (file_exists($prevPhotoFPath)) {
                        if (unlink($prevPhotoFPath)) {
                            log_message("info", "File deleted: " . $prevPhotoFPath);
                        } else {
                            log_message("error", "Failed to delete file: " . $prevPhotoFPath);
                        }
                    }
                }

                $this->$redirect = base_url("user");
                appJsonRespondSuccess(false, "Delete user success.", $redirect);
                return;
            } else {
                throw new Exception("Delete user failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function showResetPassword()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "u_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("usr_key") ?? "";
            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 404);
            }
            unset($dUser["usr_password"]);
            $dView = [
                "viewDir" => $this->viewDir,
                "dUser" => $dUser,
            ];
            $view = appViewInjectModal($this->viewDir, "reset_password_modal", $dView);
            $script = appViewInjectScript($this->viewDir, "submit_reset_password_script");
            appJsonRespondSuccess(true, "Request done.", $redirect, $view, $script);
            return;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function doResetPassword()
    {
        $this->_onlyPostandAjax();
        $redirect = $this->request->getUserAgent()->getReferrer();

        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request" . "[ref:ex823]", 400);
            }
            $dAccess = authVerifyAccess(false, "u_user");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }

            $usrKey = $this->request->getPost("hdn_usr_key") ?? "";
            $sendEmail = trim($this->request->getPost("cbo_send_email") ?? "");

            if (empty($usrKey)) {
                throw new Exception("Required data not found.", 400);
            }

            if (!in_array($sendEmail, [0, 1])) {
                throw new Exception("Invalid Send Email value", 400);
            }
            $dUser = $this->UserModel->get([
                "usr_key" => $usrKey,
            ], true);
            if (empty($dUser)) {
                throw new Exception("Data not found.", 400);
            }

            $password = appGenerateString(10);
            $resetToken = appGenerateString(30);
            $resetExpires = date("Y-m-d H:i:s", strtotime("+1 hours"));
            $dEdit = [
                "usr_password" => password_hash($password, PASSWORD_DEFAULT),
                "usr_reset_password_token" => $resetToken,
                "usr_reset_password_expires" => $resetExpires,
                "usr_updated_at" => appCurrentDateTime(),
            ];

            if ($this->UserModel->edit($dEdit, [
                "usr_key" => $usrKey
            ])) {
                $email = $dUser["usr_email"];
                $seResult = "";
                if ($sendEmail && !empty($email)) {
                    if (!empty($resetToken)) {
                        $resetUrl = base_url("change_password?token=" . $resetToken . "&email=" . $email);
                    }
                    $body = appViewInjectContent($this->viewDir, "reset_password_email", [
                        "resetUrl" => $resetUrl,
                        "email" => $email,
                        "username" => (!empty($dUser["usr_username"]) ? $dUser["usr_username"] : $email),
                    ]);
                    if (appSendEmail($email, "Reset Password", $body)) {
                        $seResult = "Send email success.";
                    } else {
                        $seResult = "Send email failed.";
                    }
                }
                appJsonRespondSuccess(false, "Reset Password user success." . $seResult, $redirect);
                return;
            } else {
                throw new Exception("Reset Password user failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }
}
