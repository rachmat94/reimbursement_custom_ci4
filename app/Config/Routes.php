<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get("file/(:any)", "File::private/$1");

$routes->get("error", "Error::index");
$routes->get("login", "Auth::login");
$routes->post("do_login", "Auth::doLogin");
$routes->get("logout", "Auth::logout");
$routes->get("change_password", "Auth::changePassword");
$routes->post("do_change_password", "Auth::doChangePassword");
$routes->get("forgot-password", "Auth::forgotPassword");
$routes->post("do_forgot_password", "Auth::doForgotPassword");

$routes->group("dashboard", static function ($routes) {
    $routes->get("", "Dashboard::index");
});

$routes->group("appconfig", static function ($routes) {
    $routes->get("", "AppConfig::index");
    $routes->post("do_edit", "AppConfig::doEdit");
});


$routes->group("user", static function ($routes) {
    $routes->get("", "User::index");
    $routes->get("view", "User::view");
    $routes->get("add", "User::add");
    $routes->post("do_add", "User::doAdd");
    $routes->post("dtbl_main", "User::dtblMain");
    $routes->post("show_preview", "User::showPreview");
    $routes->post("show_edit", "User::showEdit");
    $routes->post("do_edit", "User::doEdit");
    $routes->post("do_delete_photo", "User::doDeletePhoto");
    $routes->post("show_delete", "User::showDelete");
    $routes->post("do_delete", "User::doDelete");
    $routes->post("show_reset_password", "User::showResetPassword");
    $routes->post("do_reset_password", "User::doResetPassword");


    // $routes->post("show_add", "Admin\User::showAdd");
    // 
});


$routes->group("group", static function ($routes) {
    $routes->get("", "Group::index");
    $routes->get("view", "Group::view");
    $routes->get("add", "Group::add");
    $routes->post("show_add", "Group::showAdd");
    $routes->post("do_add", "Group::doAdd");
    $routes->post("dtbl_main", "Group::dtblMain");
    $routes->post("show_preview", "Group::showPreview");
    $routes->post("show_edit", "Group::showEdit");
    $routes->post("do_edit", "Group::doEdit");
    $routes->post("show_delete", "Group::showDelete");
    $routes->post("do_delete", "Group::doDelete");
    $routes->get("user", "Group::user");
});

$routes->group("category", static function ($routes) {
    $routes->get("", "Category::index");
    $routes->get("view", "Category::view");
    $routes->post("show_add", "Category::showAdd");
    $routes->post("do_add", "Category::doAdd");
    $routes->post("dtbl_main", "Category::dtblMain");
    $routes->post("show_preview", "Category::showPreview");
    $routes->post("show_edit", "Category::showEdit");
    $routes->post("do_edit", "Category::doEdit");
    $routes->post("show_delete", "Category::showDelete");
    $routes->post("do_delete", "Category::doDelete");
    $routes->post("show_for_select", "Category::showForSelect");
    $routes->post("do_select", "Category::doSelect");
});

$routes->group("jenis-berkas", static function ($routes) {
    $routes->get("", "JenisBerkas::index");
    $routes->get("view", "JenisBerkas::view");
    $routes->post("show_add", "JenisBerkas::showAdd");
    $routes->post("do_add", "JenisBerkas::doAdd");
    $routes->post("dtbl_main", "JenisBerkas::dtblMain");
    $routes->post("show_preview", "JenisBerkas::showPreview");
    $routes->post("show_edit", "JenisBerkas::showEdit");
    $routes->post("do_edit", "JenisBerkas::doEdit");
    $routes->post("show_delete", "JenisBerkas::showDelete");
    $routes->post("do_delete", "JenisBerkas::doDelete");
    $routes->post("show_for_select", "JenisBerkas::showForSelect");
    $routes->post("do_select", "JenisBerkas::doSelect");
});

$routes->group("submission-schedule", static function ($routes) {
    $routes->get("", "SubmissionSchedule::index");
    $routes->get("view", "SubmissionSchedule::view");
    $routes->post("show_add", "SubmissionSchedule::showAdd");
    $routes->post("do_add", "SubmissionSchedule::doAdd");
    $routes->post("dtbl_main", "SubmissionSchedule::dtblMain");
    $routes->post("show_preview", "SubmissionSchedule::showPreview");
    $routes->post("show_edit", "SubmissionSchedule::showEdit");
    $routes->post("do_edit", "SubmissionSchedule::doEdit");
    $routes->post("show_delete", "SubmissionSchedule::showDelete");
    $routes->post("do_delete", "SubmissionSchedule::doDelete");
    $routes->post("show_for_select", "SubmissionSchedule::showForSelect");
    $routes->post("do_select", "SubmissionSchedule::doSelect");
});


$routes->group("reimbursement", static function ($routes) {
    $routes->get("", "Reimbursement::index");
    $routes->get("create", "Reimbursement::create");
    $routes->post("do_create", "Reimbursement::doCreate");
    $routes->post("show_select_user", "Reimbursement::showSelectUser");
    $routes->post("do_select_user", "Reimbursement::doSelectUser");
    $routes->get("view", "Reimbursement::view");
    $routes->get("list", "Reimbursement::list");
    $routes->post("dtbl_list", "Reimbursement::dtbl_list");
    $routes->get("draft", "Reimbursement::draft");
    $routes->post("do_save_draft", "Reimbursement::doSaveDraft");
    $routes->post("do_delete_berkas", "Reimbursement::doDeleteBerkas");
    $routes->post("show_upload_berkas", "Reimbursement::showUploadBerkas");
    $routes->post("do_upload_berkas", "Reimbursement::doUploadBerkas");
    $routes->post("show_edit_berkas", "Reimbursement::showEditBerkas");
    $routes->post("do_edit_berkas", "Reimbursement::doEditBerkas");
    // $routes->post("show_add", "Reimbursement::showAdd");
    // $routes->post("dtbl_main", "Reimbursement::dtblMain");
    // $routes->post("show_preview", "Reimbursement::showPreview");
    // $routes->post("show_edit", "Reimbursement::showEdit");
    // $routes->post("do_edit", "Reimbursement::doEdit");
    // $routes->post("show_delete", "Reimbursement::showDelete");
    // $routes->post("do_delete", "Reimbursement::doDelete");
    // $routes->post("show_for_select", "Reimbursement::showForSelect");
    // $routes->post("do_select", "Reimbursement::doSelect");
});
