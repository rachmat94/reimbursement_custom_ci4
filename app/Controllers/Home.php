<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Exception;

class Home extends BaseController
{
    private $cnm = "";
    private $cnmSlug = "home";
    private $viewDir = "home";
    private $title = "Home";
    private $pageHeader = "Home";
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
            $dAccess = authVerifyAccess();
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
}
