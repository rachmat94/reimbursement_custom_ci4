<?php

namespace App\Libraries;

use App\Models\AppConfigModel;
use Exception;

class AppConfig
{
    public static function getValue($name)
    {
        try {
            if (empty($name)) {
                throw new Exception("Config name is empty", 400);
            }
            $AppConfigModel = new AppConfigModel();
            $dAppConfig = $AppConfigModel->get([
                "cfg_name" => $name,
            ], true);
            if (empty($dAppConfig)) {
                log_message("error", "App Config (" . $name . ") not found");
                throw new Exception("App Config not found.", 400);
            }
            return $dAppConfig["cfg_value"];
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 400);
        }
    }
}
