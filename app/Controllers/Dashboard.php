<?php

namespace App\Controllers;

use Exception;

class Dashboard extends BaseController
{
    private $cnm = "dashboard";
    private $cnmSlug = "dashboard";
    private $viewDir = "dashboard";
    private $title = "Dashboard";
    private $pageHeader = "Dashboard";
    private $cData;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
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
            $this->cData["header"] = "Welcome, " . $dAccess["data"]["usr_email"];
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
