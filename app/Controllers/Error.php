<?php

namespace App\Controllers;

use Exception;

class Error extends BaseController
{
    private $cnm = "error";
    private $cnmSlug = "error";
    private $viewDir = "error";
    private $title = "Error";
    private $pageHeader = "Error";
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
            return view($this->viewDir . "/index", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }
}
