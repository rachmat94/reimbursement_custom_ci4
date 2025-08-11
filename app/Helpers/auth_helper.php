<?php

use App\Libraries\AppConfig;
use App\Models\AuthModel;
use App\Models\userSessionModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authReturnNull($message = "Failed.", bool $redirectLogin = true, $data = null)
{
    return [
        "success" => false,
        "message" => $message,
        "redirect_login" => $redirectLogin,
        "data" => $data
    ];
}

function authVerifyAccess(bool $onlyLogin = false, string $accessResource = "", bool $returnData = true)
{
    try {
        $sessValue = authGetSession();
        // log_message("alert", "authVerifyAccess: sessValue: " . $sessValue);
        if (empty($sessValue)) {
            // log_message("error", "authVerifyAccess: Session value is empty." . $sessValue);
            // throw new Exception("Session not found.", 400);
            return authReturnNull("Login Session not found. Please login again.");
        }
        $Request = \Config\Services::request();
        $strAgent = $Request->getUserAgent()->getAgentString();
        $deviceInfo = appDeviceInfo();
        $ipAddress = $Request->getIPAddress();

        $decodedJWT = (array) JWT::decode($sessValue, new Key(authJWTSecretKey(), authJWTAlgo()));
        $iss = $decodedJWT["iss"] ?? "";
        $aud = $decodedJWT["aud"] ?? "";
        $sub = $decodedJWT["sub"] ?? "";
        $usKey = $decodedJWT["us_key"] ?? "";
        $usrKey = $decodedJWT["usr_key"] ?? "";
        $device = $decodedJWT["device"] ?? "";
        $exp = $decodedJWT["exp"] ?? null;

        if (empty($iss) || empty($aud) || empty($sub) || empty($usKey) || empty($usrKey) || empty($device) || empty($exp)) {
            return authReturnNull("Invalid Login Session. Please login again.[x01]");
        }

        if ($iss != "reimbursement") {
            return authReturnNull("Invalid Login Session. Please login again.[x02]");
        }
        if ($aud != "reimbursement") {
            return authReturnNull("Invalid Login Session. Please login again.[x03]");
        }
        if ($sub != "user_session") {
            return authReturnNull("Invalid Login Session. Please login again.[x04]");
        }
        if ($device != $deviceInfo) {
            return authReturnNull("Invalid Login Session. Please login again.[x05]");
        }
        if ($exp < time()) {
            return authReturnNull("Invalid Login Session. Please login again.[x06]");
        }

        // log_message("alert", "Dec JWT=" . json_encode($decodedJWT));
        // log_message("alert", "JWT EXP=" . $decodedJWT["exp"] . "|" . date("Y-m-d H:i:s", $decodedJWT["exp"]));
        $UserSessionModel = new userSessionModel();
        $dWhere = [
            "us_token" => $sessValue,
            "us_device" => $deviceInfo,
            "us_key" => $usKey,
            "usr_key" => $usrKey,
        ];
        // log_message("alert", "authVerifyAccess: dWhere: " . json_encode($dWhere));
        $dSession = $UserSessionModel->get($dWhere, true);
        // log_message("alert", "authVerifyAccess: dSession: " . json_encode($dSession));
        if (empty($dSession)) {
            authResetSession();
            return authReturnNull("Invalid Login Session. Please login again.[x07]");
        }
        $expiresAt = $dSession["us_expired_at"];
        if (empty($expiresAt)) {
            authResetSession();
            return authReturnNull("Login Session has been expired. Please login again.");
        }
        if (!appIsValidDateTime($expiresAt)) {
            authResetSession();
            return authReturnNull("Invalid Login Session. Please login again.[x08]");
        }
        if (appIsExpired($expiresAt)) {
            authResetSession();
            return authReturnNull("Login session has been expired. Please login again.");
        }

        if ($dSession["usr_is_active"] != 1) {
            return authReturnNull("User Account not active");
        }

        $usrId = $dSession["usr_id"];
        if (empty($usrId)) {
            return authReturnNull("Invalid Login Session. Please login again.[x09]");
        }
        $photoFUrl = appGetUserPhoto($dSession["usr_key"], $dSession["usr_photo_file_name"])["file_url"];
        if (empty($photoFUrl)) {
            $photoFUrl = assetUser();
        }
        if ($usrId == authMasterUserId()) {
            return [
                "success" => true,
                "message" => "You have access",
                "redirect_login" => false,
                "data" => [
                    "us_key" => $dSession["us_key"],
                    "us_login_at" => $dSession["us_login_at"],
                    "us_expired_at" => $dSession["us_expired_at"],
                    "usr_id" => $dSession["usr_id"],
                    "usr_key" => $dSession["usr_key"],
                    "usr_role" => $dSession["usr_role"],
                    "usr_email" => $dSession["usr_email"],
                    "usr_username" => $dSession["usr_username"],
                    "photo_url" => $photoFUrl,
                    "group_name" => $dSession["group_name"],
                    "group_id" => $dSession["group_id"],
                ],
            ];
        }

        if (authIsMaintenance()) {
            return authReturnNull("Under maintenance");
        }
        /**
         * Check if the user has access to the requested resource
         */

        if ($onlyLogin) {
            return [
                "success" => true,
                "message" => "You have access",
                "redirect_login" => false,
                "data" => [
                    "us_key" => $dSession["us_key"],
                    "us_login_at" => $dSession["us_login_at"],
                    "us_expired_at" => $dSession["us_expired_at"],
                    "usr_id" => $dSession["usr_id"],
                    "usr_key" => $dSession["usr_key"],
                    "usr_role" => $dSession["usr_role"],
                    "usr_email" => $dSession["usr_email"],
                    "usr_username" => $dSession["usr_username"],
                    "photo_url" => $photoFUrl,
                    "group_name" => $dSession["group_name"],
                    "group_id" => $dSession["group_id"],
                ]
            ];
        } else {
            if (empty($accessResource)) {
                throw new Exception("Access resource is required.", 400);
            }
            // log_message("alert", "authVerifyAccess: accessResource: " . $accessResource);
            $AuthModel = new AuthModel();
            $dACL = $AuthModel->getACL($accessResource);
            if (empty($dACL)) {
                throw new Exception("Access denied.", 403);
            }
            // log_message("alert", "authVerifyAccess: dACL: " . json_encode($dACL));
            if (!in_array(strtolower($dSession["usr_role"]), $dACL)) {
                throw new Exception("Access denied for your role.", 403);
            }

            return [
                "success" => true,
                "message" => "You have access",
                "redirect_login" => false,
                "data" => [
                    "us_key" => $dSession["us_key"],
                    "us_login_at" => $dSession["us_login_at"],
                    "us_expired_at" => $dSession["us_expired_at"],
                    "usr_id" => $dSession["usr_id"],
                    "usr_key" => $dSession["usr_key"],
                    "usr_role" => $dSession["usr_role"],
                    "usr_email" => $dSession["usr_email"],
                    "usr_username" => $dSession["usr_username"],
                    "photo_url" => $photoFUrl,
                    "group_name" => $dSession["group_name"],
                    "group_id" => $dSession["group_id"],
                ]
            ];
        }
    } catch (\Throwable $th) {
        appSaveThrowable($th);
        return [
            "success" => false,
            "message" => $th->getMessage(),
            "redirect_login" => false,
            "data" => null
        ];
    }
}

function authIsMaintenance()
{
    $status = AppConfig::getValue("maintenance_mode");
    if ($status == 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * Mengatur role yang bisa dibuat oleh user.
 *
 * @param string $role User session role.
 * @return mixed Daftar role yang dapat dibuat oleh user, atau hasil pemeriksaan role.
 */
function authRoleBy($role = "")
{
    if (empty($role)) {
        return [];
    }
    switch (strtolower($role)) {
        case "super_user":
            return ["user", "admin_group", "admin_validator", "super_user"];
        case "admin_group":
        case "admin_validator":
        case "user":
        default:
            return [];
    }
}

function authJWTSecretKey()
{
    return getenv("jwt.secretKey") ?? "a7sd98f7ads89f7sdf79s8f32j4lkj3lkj";
}

function authJWTAlgo()
{
    return getenv("jwt.algo") ?? "HS256";
}

function authLifetime()
{
    $lifetime =  MINUTE * (getenv("auth.lifetime.minutes") ?? 120);
    return $lifetime;
}

function authSessionName()
{
    return getenv("auth.session.name") ?? "x-us-reimc";
}

function authMasterUserId()
{
    return getenv("master.usr_id") ?? null;
}

function authGetSession()
{
    $sessionName = authSessionName();
    if (empty($sessionName)) {
        log_message("error", "authGetSession: Session name is empty.");
        return null;
    }
    $value = session()->get($sessionName) ?? "";
    if (empty($value)) {
        return null;
    }
    return $value;
}

function authSetSession($value = "")
{
    $sessionName = authSessionName();
    if (empty($sessionName)) {
        log_message("error", "authSetSession: Session name is empty.");
        return;
    }
    // log_message("alert", "authSetSession: name:" . $sessionName . ", value: " . $value);
    session()->set($sessionName, $value);
}

function authResetSession()
{
    session()->remove(authSessionName());
    // session()->destroy();
}
