<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\UserModel;
use Exception;

class File extends BaseController
{
    private $cnm = "file";
    private $cnmSlug = "file";
    private $viewDir = "file";
    private $title = "File";
    private $pageHeader = "File";
    private $cData;
    private $UserModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];
        $this->UserModel = new UserModel();
    }

    public function private($type = "")
    {
        try {
            if (empty($type)) {
                throw new Exception("Type not found.", 400);
            }
            $dAccess = authVerifyAccess();
            if (!$dAccess["success"]) {
                throw new Exception($dAccess["message"], 400);
            }
            $fPath = "";
            switch (strtolower($type)) {
                case 'reimberkas':
                    $rbKey = $this->request->getGet("rb_key") ?? "";
                    $filename = $this->request->getGet("filename") ?? "";
                    if (empty($rbKey) || empty($filename)) {
                        throw new Exception("Required data not found.", 400);
                    }
                    $BerkasModel = new BerkasModel();

                    $dReimBerkas = $BerkasModel->get([
                        "rb_key" => $rbKey,
                        "rb_file_name" => $filename,
                    ], true);
                    if (empty($dReimBerkas)) {
                        throw new Exception("Data not found.", 400);
                    }
                    $reimTahun = $dReimBerkas["reim_triwulan_tahun"];
                    $reimTriwulan = $dReimBerkas["reim_triwulan_no"];
                    $claimantUGroupKey = $dReimBerkas["ucg_group_key"];

                    $fPath = appConfigDataPath(
                        "reimbursement/berkas/" . $reimTahun . "/" .
                            "triwulan_" . $reimTriwulan . "/" .
                            $claimantUGroupKey . "/" . $filename
                    );
                    break;

                case 'user_photo':
                    $usrKey = $this->request->getGet("usr_key") ?? "";
                    $filename = $this->request->getGet("filename") ?? "";
                    if (empty($usrKey) || empty($filename)) {
                        throw new Exception("Required data not found.", 400);
                    }
                    $dUser = $this->UserModel->get([
                        "usr_key" => $usrKey,
                        "usr_photo_file_name" => $filename,
                    ], true);
                    if (empty($dUser)) {
                        throw new Exception("Data not found.", 400);
                    }
                    $fPath = appConfigDataPath("user/photo/" . $filename);
                    break;

                default:
                    # code...
                    break;
            }
            if ($fPath == "") {
                throw new Exception("Required data not found.", 400);
            }
            if (!is_file($fPath)) {
                header('HTTP/1.0 404 Not Found');
                exit();
            }
            if (!file_exists($fPath)) {
                header('HTTP/1.0 404 Not Found');
                exit();
            }
            $download = $this->request->getVar("download") ?? 0;
            if ($download == 1) {
                return $this->response->download($fPath, null);
            } else {
                $mime = mime_content_type($fPath);

                header("Content-Type: " . $mime);
                header("Content-Length: " . filesize($fPath));
                // header("Content-Disposition: attachment; filename=".$fn); -> not work on chrome mobile
                readfile($fPath);
                // print file_get_contents($file);
            }
            exit();
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            echo $th->getMessage();
            exit();
        }
    }
}
