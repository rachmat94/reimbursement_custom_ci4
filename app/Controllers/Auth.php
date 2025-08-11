<?php

namespace App\Controllers;

use App\Libraries\RowKeyGeneratorv2;
use App\Models\AuthModel;
use App\Models\UserSessionModel;
use Exception;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    private $cnm = "auth";
    private $cnmSlug = "auth";
    private $viewDir = "auth";
    private $title = "Auth";
    private $pageHeader = "Auth";
    private $cData;

    private $AuthModel;
    private $UserSessionModel;

    public function __construct()
    {
        $this->cData = [
            "cnm" => $this->cnm,
            "cnm_slug" => $this->cnmSlug,
            "title" => $this->title,
            "header" => $this->pageHeader,
            "viewDir" => $this->viewDir,
        ];

        $this->AuthModel = new AuthModel();
        $this->UserSessionModel = new UserSessionModel();
    }

    public function doForgotPassword()
    {
        $redirect = base_url('login');
        $this->_onlyPostandAjax();
        try {
            if (!mycsrfTokenValidate($this->request->getPost(mycsrfTokenName()) ?? "")) {
                log_message("error", "Invalid csrf token");
                throw new Exception("Invalid request token, or has been expired. Please Try again or reload the page.", 400);
            }
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                throw new Exception("Please logout current user first.", 400);
            }

            $email = $this->request->getPost("txt_email") ?? "";

            if (empty($email)) {
                throw new Exception("Invalid Url.", 400);
            }

            if (!appIsEmailValid($email)) {
                throw new Exception("Invalid Email Address.", 400);
            }

            $dUser = $this->AuthModel->getUser([
                "usr_email" => $email,
            ]);
            if (empty($dUser)) {
                throw new Exception("Account not found.", 400);
            }
            if ($dUser["usr_is_active"] != 1) {
                throw new Exception("Account currently disabled.", 400);
            }
            $usrId = $dUser["usr_id"];

            $resetToken = appGenerateString(30);
            $resetExpires = date("Y-m-d H:i:s", strtotime("+1 hours"));
            $dEdit = [
                "usr_reset_password_token" => $resetToken,
                "usr_reset_password_expires" => $resetExpires,
                "usr_updated_at" => appCurrentDateTime(),
            ];

            if ($this->AuthModel->editUser($dEdit, [
                "usr_id" => $usrId
            ])) {
                if (!empty($resetToken)) {
                    $resetUrl = base_url("change_password?token=" . $resetToken . "&email=" . $email);
                }
                $body = appViewInjectContent($this->viewDir, "reset_password_email", [
                    "resetUrl" => $resetUrl,
                    "email" => $email,
                    "username" => (!empty($dUser["usr_username"]) ? $dUser["usr_username"] : $email),
                ]);
                if (appSendEmail($dUser["usr_email"], "Reset Password", $body)) {
                    $seResult = "A password reset link has been sent to your email.";
                } else {
                    $seResult = "Send email failed.";
                }
                appJsonRespondSuccess(false, $seResult, $redirect);
                return;
            } else {
                throw new Exception("Reset password request failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }

    public function forgotPassword()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                throw new Exception("Please logout current user first.", 400);
            }
            $this->cData["title"] = "Forgot Password";
            return view($this->viewDir . "/forgot_password", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function login()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                $redirect = base_url("dashboard");
                return redirect()->to($redirect)->with("alert", [
                    "code" => "success",
                    "message" => "You already login.",
                ]);
            }
            $this->cData["title"] = "Login";

            return view($this->viewDir . "/login", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doLogin()
    {
        $this->_onlyPostandAjax();
        $redirect = base_url("login");
        try {
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                appJsonRespondSuccess(false, "You already login.", base_url("dashboard"));
                return;
            }
            $email = $this->request->getPost("txt_email") ?? "";
            $password = $this->request->getPost("txt_password") ?? "";


            if (empty($email) || empty($password)) {
                throw new Exception("Required Email & Password.", 400);
            }

            if (!appIsEmailValid($email)) {
                throw new Exception("Invalid email.", 400);
            }

            $dUser = $this->AuthModel->getUser([
                "usr_email" => $email,
            ]);

            if (empty($dUser)) {
                throw new Exception("User not found.", 400);
            }

            if (!password_verify($password, $dUser["usr_password"])) {
                throw new Exception("Incorrent Email or Password.", 400);
            }

            if ($dUser["usr_is_active"] != 1) {
                throw new Exception("Account currently disabled.", 400);
            }

            if ($dUser["usr_id"] != authMasterUserId()) {
                if (authIsMaintenance()) {
                    throw new Exception("Under Maintenance", 400);
                }
            }

            $usKey = $this->UserSessionModel->generateKey();
            $timeExpires = time() + authLifetime();
            $expiresAt = date("Y-m-d H:i:s", $timeExpires);
            log_message("alert", "Exp: " . $timeExpires . "|" . $expiresAt);
            $usToken = $this->UserSessionModel->generateToken(
                $dUser["usr_key"],
                $usKey,
                $timeExpires,
            );

            authSetSession($usToken);
            if (!empty(authGetSession())) {
                log_message("alert", "doLogin: Session set successfully.");
                $dUSession = [
                    "us_key" => $usKey,
                    "us_usr_id" => $dUser["usr_id"],
                    "us_token" => $usToken,
                    "us_ip_address" => $this->request->getIPAddress(),
                    "us_device" => appDeviceInfo(),
                    "us_expired_at" => $expiresAt,
                    "us_login_at" => appCurrentDateTime(),
                    "us_created_at" => appCurrentDateTime(),
                ];
                if ($this->UserSessionModel->add($dUSession)) {

                    $redirect = base_url('dashboard');
                    appJsonRespondSuccess(false, "Login successfull", $redirect);
                    return;
                } else {
                    throw new Exception("Login failed.", 400);
                }
            } else {
                throw new Exception("Failed to set session.", 500);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function logout()
    {
        try {
            $usKey = authGetSession(true);
            if (!empty($usKey)) {
                $dUSession = $this->UserSessionModel->get([
                    "us_key" => $usKey,
                ], true);
                if (!empty($dUSession)) {
                    if ($this->UserSessionModel->edit([
                        "us_logout_at" => appCurrentDateTime(),
                    ], [
                        "us_key" => $usKey,
                    ])) {
                    } else {
                        log_message("error", "Save Logout info failed.");
                    }
                } else {
                    log_message("error", "User Session not found.");
                }
            }
            authResetSession();
            return redirect()->to(base_url('login'))->with("alert", [
                "code" => "success",
                "message" => "You are loged out",
            ]);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function changePassword()
    {
        try {
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                throw new Exception("Please logout current user first.", 400);
            }
            $resetToken = $this->request->getGet("token") ?? "";
            $email = $this->request->getGet("email") ?? "";
            if (empty($resetToken) || empty($email)) {
                throw new Exception("Invalid Url.[x01]", 400);
            }
            $dUser = $this->AuthModel->getUser([
                "usr_reset_password_token" => $resetToken,
                "usr_email" => $email,
            ]);
            if (empty($dUser)) {
                throw new Exception("Invalid Url.[x02]", 404);
            }
            if ($dUser["usr_email"] != $email) {
                throw new Exception("Invalid Url.[x03]", 400);
            }
            unset($dUser["usr_password"]);

            $this->cData["token"] = $resetToken;
            $this->cData["dUser"] = [
                "usr_id" => $dUser["usr_id"],
                "usr_key" => $dUser["usr_key"],
                "usr_username" => $dUser["usr_username"],
                "usr_email" => $dUser["usr_email"],
            ];
            $this->cData["title"] = "Change Password";
            return view($this->viewDir . "/change_password", $this->cData);
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage());
        }
    }

    public function doChangePassword()
    {
        $redirect = base_url('login');
        $this->_onlyPostandAjax();
        try {
            $dAccess = authVerifyAccess(true);
            if ($dAccess["success"]) {
                throw new Exception("Please logout current user first.", 400);
            }
            $token = $this->request->getPost("hdn_token") ?? "";
            $usrKey = $this->request->getPost("hdn_usr_key") ?? "";
            $password = $this->request->getPost("txt_password") ?? "";
            $passwordConfirm = $this->request->getPost("txt_password_confirm") ?? "";

            if (empty($token) || empty($usrKey)) {
                throw new Exception("Invalid Url.", 400);
            }

            $dUser = $this->AuthModel->getUser([
                "usr_key" => $usrKey,
                "usr_reset_password_token" => $token,
            ]);
            if (empty($dUser)) {
                throw new Exception("Invalid Url", 400);
            }
            if (empty($dUser["usr_reset_password_expires"])) {
                throw new Exception("Invalid Url", 400);
            }
            if ($dUser["usr_reset_password_token"] != $token) {
                throw new Exception("Invalid Url", 400);
            }
            $expires = $dUser["usr_reset_password_expires"];
            if (appIsExpired($expires)) {
                throw new Exception("Url has been expired.", 400);
            }

            if ($dUser["usr_is_active"] != 1) {
                throw new Exception("User is not active.", 400);
            }

            $dValidatePassword = appValidatePassword($password);
            if ($dValidatePassword["code"] != "success") {
                throw new Exception($dValidatePassword["message"], 400);
            }
            if ($password != $passwordConfirm) {
                throw new Exception("Incorrent password confirmation.", 400);
            }

            $dEdit = [
                "usr_password" => password_hash($password, PASSWORD_DEFAULT),
                "usr_reset_password_token" => null,
                "usr_reset_password_expires" => null,
                "usr_updated_at" => appCurrentDateTime(),
            ];
            if ($this->AuthModel->editUser($dEdit, [
                "usr_key" => $usrKey,
            ])) {
                $redirect = base_url('login');
                appJsonRespondSuccess(false, "Change Password Success.", $redirect);
                return;
            } else {
                throw new Exception("Change Password Failed.", 400);
            }
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return $this->sendResponse($th->getCode(), $th->getMessage(), $redirect);
        }
    }
}
