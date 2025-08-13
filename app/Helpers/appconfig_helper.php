<?php

function appConfigDataDirList()
{
    $paths = [
        "user/",
        "user/photo/",

        "reimbursement/",
        "reimbursement/berkas/",
        "reimbursement/berkas/" . date("Y") . "/",
        
        "reimbursement/payment/",
    ];
    return $paths;
}

function appConfigDataDirName()
{
    return "data";
}

function appConfigRootDir()
{
    return "";
}
function appConfigDataPath($path = "")
{
    $dataPath =  WRITEPATH . "/" . appConfigRootDir() . "/" . appConfigDataDirName();
    if (!file_exists($dataPath . "/")) {
        if (mkdir($dataPath . "/")) {
        }
    }

    if ($path != "") {
        if (substr($path, 0, 1) != "/") {
            $dataPath .= "/";
        }
        $dataPath .= $path;
        if (!substr($path, -1) == "/") {
            $dataPath . "/";
        }
    }
    return $dataPath;
}

function appConfigInitDataDir()
{
    if (!file_exists(appConfigDataPath())) {
        if (mkdir(appConfigDataPath())) {
        }
    }

    foreach (appConfigDataDirList() as $dir) {
        $dirPath = appconfigDataPath($dir);
        if (!file_exists($dirPath)) {
            if (mkdir($dirPath)) {
            }
        }
    }
}
