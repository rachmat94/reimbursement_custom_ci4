<?php

namespace App\Controllers;

use App\Libraries\AppConfig as LibrariesAppConfig;
use App\Models\AppConfigModel;
use Exception;

class AppConfig extends BaseController
{
    private $cnm = "appconfig";
    private $cnmSlug = "contact-type";
    private $viewDir = "appconfig";
    private $title = "App Config";
    private $pageHeader = "App Config";
    private $cData;

    private $AppConfigModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->AppConfigModel = new AppConfigModel();
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
            $dAccess = authVerifyAccess(false, "u_appconfig");
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 403);
            }
            $sessUsrId = $dAccess["data"]["usr_id"];

            $cfgKey = $this->request->getPost("hdn_cfg_key") ?? "";
            $type = strtolower($this->request->getPost("hdn_type") ?? "");

            if (empty($cfgKey)) {
                throw new Exception("Required data not found.", 400);
            }
            if (empty($type)) {
                throw new Exception("Required data not found", 400);
            }

            $dAppConfig = $this->AppConfigModel->get([
                "cfg_key" => $cfgKey,
            ], true);
            if (empty($dAppConfig)) {
                throw new Exception("Data Not found.", 400);
            }
            switch ($type) {
                case 'maintenance_mode':
                    return $this->_maintenanceMode($dAppConfig, $sessUsrId);
                    break;

                default:
                    throw new Exception("Invalid required data.", 400);
                    break;
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    private function _maintenanceMode($dAppConfig = null, $sessUsrId = null)
    {
        if (empty($dAppConfig)) {
            throw new Exception("Config not found.", 400);
        }
        $status = ($this->request->getPost("cbo_maintenance_mode") ?? "");
        if (!in_array($status, [0, 1])) {
            throw new Exception("Invalid status", 400);
        }

        $dEdit = [
            "cfg_value" => $status,
            "cfg_updated_at" => appCurrentDateTime(),
            "cfg_updated_by_usr_id" => $sessUsrId,
        ];

        if ($this->AppConfigModel->edit($dEdit, [
            "cfg_key" => $dAppConfig["cfg_key"],
        ])) {
            appJsonRespondSuccess(true, "Updated.");
            return;
        } else {
            throw new Exception("Update failed.", 400);
        }
    }

    public function index()
    {
        try {
            $dAccess = authVerifyAccess(false, "r_appconfig");
            if (!$dAccess["success"]) {
                return redirect()->to(base_url('login'))->with("alert", [
                    "code" => "error",
                    "message" => $dAccess["message"],
                ]);
            }

            $this->cData["dAccess"] = $dAccess;
            $dAppConfigs = [];
            $dAppConfigs["maintenance_mode"] = $this->AppConfigModel->get([
                "cfg_name" => "maintenance_mode",
            ], true);
            $this->cData["dAppConfigs"] = $dAppConfigs;
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
