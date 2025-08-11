<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        helper([
            "text",
            "cookie",
            "adminlte",
            "app",
            "auth",
            "appview",
            "asset",
            "mycsrf",
            "master",
            "appconfig",
        ]);
        appConfigInitDataDir();

        $this->session = service('session');
    }

    public function sendResponse($code = 500, $message = "", $redirect = "", $forceRedirect = false)
    {
        if ($code <= 0) {
            $code = 500;
        }
        if ($code != 200) {
            $codeStr = "error";
        } else {
            $codeStr = "success";
        }
        if ($redirect == "") {
            $redirect = base_url();
        }
        log_message("error", "sendresponsemsg=" . $message);
        if ($this->request->isAJAX()) {
            $response = \Config\Services::response();

            $csrfToken = mycsrfTokenGenerate();

            $response->setStatusCode($code);
            $response->setHeader('Content-Type', 'application/json');
            $response->setHeader('Req_token', $csrfToken);
            $response->setHeader('Redirect', $redirect);
            $response->setHeader('Force_redirect', $forceRedirect);

            $response->setBody(json_encode([
                'status' => $code,
                'message' => $message,
                'redirect' => $redirect,
                'force_redirect' => $forceRedirect,
                'req_token' => $csrfToken,
            ]));

            $response->send();
            exit;
        } else {
            ob_clean();
           
            $strtErr = appViewInjectContent("layout", "error_content", [
                "message" => "[ " . $code . " ] " .  $message,
            ]);
            // log_message("error",$strtErr);
            $strtErr = trim($strtErr);
            if (empty($strtErr)) {
                ob_clean();
                $currentUrl = current_url(true);
                $tryAgain = current_url() . (!empty($currentUrl->getQuery()) ? "?" . $currentUrl->getQuery() : "");

                echo "[ " . $code . " ] " . $message;
                echo "<br><br><a href='" . ($tryAgain) . "'>Try again</a>";
                echo "<br><br><a href='" . ($redirect) . "'>" . $redirect . "</a>";
                exit;
            }
            echo $strtErr;
            exit;
        }
    }
    protected function _onlyPostandAjax()
    {
        $redirect = $this->request->getUserAgent()->getReferrer();
        if (strtolower($this->request->getMethod()) != "post" || !$this->request->isAJAX()) {
            echo "invalid request";
            exit;
        }
    }
}
