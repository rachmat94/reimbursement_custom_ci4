<?php

use CodeIgniter\I18n\Time;

function appName()
{
    return "ReimC";
}

function appVersion()
{
    return "1.0.0";
}


function appRenderBadgeUserRole($role = "")
{
    if (empty($role)) {
        return "";
    }
    $dRole = masterUserRole($role, true);
    if (empty($dRole)) {
        return "";
    }
    $label = lteBadge($dRole["label"], $dRole["color"], "px-2 py-1");
    return $label;
}

function appFormatRupiah($angka, $withPrefix = true)
{
    $formatted = number_format($angka, 0, ',', '.');
    return $withPrefix ? 'Rp ' . $formatted : $formatted;
}

function appGetReimBerkas($rbKey, $berkasFName, int $tahun, int $triwulan, $claimantUGroupKey)
{
    $berkasFUrl  = "";
    $code        = "error";
    $message     = "";
    $fileMime    = "";
    $fileCategory = "";
    $berkasFPath = "";

    if (!empty($berkasFName)) {
        $berkasFPath = appConfigDataPath(
            "reimbursement/berkas/" . $tahun . "/" .
                "triwulan_" . $triwulan . "/" .
                $claimantUGroupKey . "/" . $berkasFName
        );

        if (file_exists($berkasFPath)) {
            // Ambil MIME type asli file
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMime = finfo_file($finfo, $berkasFPath);
            finfo_close($finfo);

            // Kategorikan file
            if ($fileMime === 'application/pdf') {
                $fileCategory = 'pdf';
            } elseif (strpos($fileMime, 'image/') === 0) {
                $fileCategory = 'image';
            } else {
                $fileCategory = 'other';
            }

            $code = "success";
            $berkasFUrl = base_url('file/reimberkas?rb_key=' . $rbKey . '&filename=' . $berkasFName);
        } else {
            $message = "[ File not found ]";
        }
    } else {
        $message = "[ empty ]";
    }

    return [
        "code"         => $code,
        "message"      => $message,
        "file_mime"    => $fileMime,
        "file_category" => $fileCategory,
        "file_name"    => $berkasFName,
        "file_path"    => $berkasFPath,
        "file_url"     => $berkasFUrl,
    ];
}


function appGetUserPhoto($usrKey = "", $photoFName = "")
{
    $photoFPath = "";
    $photoFUrl = "";
    $code = "error";
    $message = "";
    if ($photoFName != "") {
        $photoFPath = appConfigDataPath("user/photo/" . $photoFName);
        if (file_exists($photoFPath)) {
            $code = "success";
            $photoFUrl = base_url('file/user_photo?usr_key=' . $usrKey . "&filename=" . $photoFName);
        } else {
            $message = "[ File not found ]";
        }
    } else {
        $message = "[ empty ]";
    }
    return [
        "code" => $code,
        "message" => $message,
        "file_name" => $photoFName,
        "file_path" => $photoFPath,
        "file_url" => $photoFUrl,
    ];
}

function appGetFile($type = "", $keyName = "", $keyValue = "", $filePath = "", $fileName = "")
{
    $fileUrl = "";
    $code = "error";
    $message = "";
    if ($fileName != "") {
        $filePath = appConfigDataPath($filePath . $fileName);
        if (file_exists($filePath)) {
            $code = "success";
            $fileUrl = base_url('file/' . $type . '?' . $keyName . '=' . $keyValue . "&filename=" . $fileName);
        } else {
            $message = "[ File not found ]";
        }
    } else {
        $message = "[ empty ]";
    }
    return [
        "code" => $code,
        "message" => $message,
        "file_name" => $fileName,
        "file_path" => $filePath,
        "file_url" => $fileUrl,
    ];
}

function appRenderSwal(
    string $icon = "info",
    string $title = "",
    string $html = ""
) {
    $icon = strtolower($icon);
    if (!in_array($icon, [
        "danger",
        "error",
        "success",
        "warning",
    ])) {
        $icon = "info";
    }
    if ($icon == "danger") {
        $icon = "error";
    }
    $swal = <<<SWAL
    Swal.fire({
        title: `{$title}`,
        html: `{$html}`,
        icon: `{$icon}`,
    }).then(function () {
        
    });
    SWAL;
    return $swal;
}

function appSaveThrowable(Throwable $th, $level = "error", $saveTrace = true)
{
    $saveTrace = false;
    if ($saveTrace) {
        $strTrace = " | " .  json_encode($th->getTrace());
    } else {
        $strTrace = "";
    }
    log_message($level, $th->getLine() . " -> " . $th->getMessage() . " | " . $th->getFile() . $strTrace);
}


function appJsonRespondSuccess(
    bool $withToken,
    string $message,
    string $redirect = "",
    string $view = "",
    string $script = "",
    array $data = [],
) {
    return appJsonRespond(
        $withToken,
        "success",
        $message,
        $redirect,
        $view,
        $script,
        $data,
    );
}

function appJsonRespondError(
    bool $withToken,
    string $message,
    string $redirect = "",
    string $view = "",
    string $script = "",
    array $data = [],
) {
    return appJsonRespond(
        $withToken,
        "error",
        $message,
        $redirect,
        $view,
        $script,
        $data,
    );
}

function appJsonRespond(
    bool $withToken,
    string $code,
    string $message,
    string $redirect = "",
    string $view = "",
    string $script = "",
    array $data = [],
) {
    // Generate token only if required
    $token = $withToken ? mycsrfTokenGenerate() : null;
    $Request = \Config\Services::request();
    if ($redirect == "") {
        $redirect = $Request->getUserAgent()->getReferrer();
    }

    echo json_encode([
        "code" => $code,
        "message" => $message,
        "redirect" => $redirect,
        "data" => $data,
        "view" => $view,
        "script" => $script,
        "token" => $token // Include token only if generated
    ]);
    return;
}


function appIsEmailValid($email): bool
{
    $email = trim($email); // Hapus spasi di awal & akhir
    if (strlen($email) > 320) { // Email maksimum 320 karakter (sesuai standar RFC 5321)
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function appValidateUsername($userName)
{
    $userName = trim($userName);

    // Validasi format username
    if (!preg_match(appRegexUserName(), $userName)) {
        return [
            "code"    => "error",
            "message" => "Username must be between 5 and 20 characters long and can only contain letters, numbers, and underscores."
        ];
    }

    // Daftar username yang dilarang (lebih ringkas dan lebih cepat dicari)
    static $blockedUsernames = [
        "admin",
        "root",
        "password",
        "12345",
        "qwerty",
        "username",
        "test",
        "guest",
        "superuser",
        "support",
        "user",
        "administrator",
        "letmein",
        "welcome",
        "default",
        "changeme",
        "iloveyou",
        "monkey",
        "123456",
        "admin123",
        "password123",

        // Kata-kata kasar (Inggris & Indonesia)
        // "fuck",
        // "shit",
        // "bitch",
        // "asshole",
        // "dick",
        // "pussy",
        // "cunt",
        // "bastard",
        // "faggot",
        // "slut",
        // "whore",
        // "bodoh",
        // "bego",
        // "anjing",
        // "kontol",
        // "bajingan",
        // "goblok",
        // "perek",
        // "sundal",
        // "pelacur",
        // "monyet",
        // "tai",
        // "bangsat",
        // "jancok"
    ];

    if (isset(array_flip($blockedUsernames)[strtolower($userName)])) {
        return [
            "code"    => "error",
            "message" => "The chosen username is not allowed. Please select a different one."
        ];
    }

    return [
        "code"    => "success",
        "message" => "Username is valid."
    ];
}


function appValidatePassword($password)
{
    $errors = [];

    // Validasi masing-masing aturan
    if (!preg_match('/[a-z]/', $password)) {
        // $errors[] = "Password must include at least one lowercase letter.";
        $result = ["code" => "error", "message" => "Password must include at least one lowercase letter."];
        return $result;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        // $errors[] = "Password must include at least one uppercase letter.";
        $result = ["code" => "error", "message" => "Password must include at least one uppercase letter."];
        return $result;
    }
    if (!preg_match('/\d/', $password)) {
        // $errors[] = "Password must include at least one number.";
        $result = ["code" => "error", "message" => "Password must include at least one number."];
        return $result;
    }
    if (!preg_match('/[!@#$%^&*()_\-+=\[\]{};:\',.<>?|]/', $password)) {
        // $errors[] = "Password must include at least one special character (!@#$%^&*()_-+=[]{};:',.<>?|).";
        $result = ["code" => "error", "message" => "Password must include at least one special character (!@#$%^&*()_-+=[]{};:',.<>?|)."];
        return $result;
    }
    if (strlen($password) < 8) {
        // $errors[] = "Password must be at least 8 characters long.";
        $result = ["code" => "error", "message" => "Password must be at least 8 characters long."];
        return $result;
    }
    $passwordBlacklist = [
        "Password123!",
        "Qwerty123@",
        "Admin123#",
        "Welcome1$",
        "Sunshine2023!",
        "Monkey#2023",
        "Iloveyou123@",
        "Test123!@",
        "Abc123$#",
        "Superman2023*",
        "Qwerty!123",
        "123Password@",
        "Passw0rd123!",
        "123qwe!@",
        "Master123#",
        "Football#1!",
        "12345Qwerty@",
        "Hello2023#",
        "Shadow1234!",
        "Letmein2023#"
    ];
    if (in_array($password, $passwordBlacklist)) {
        $result = ["code" => "error", "message" =>  "Please choose a stronger password."];
        return $result;
    }
    $result = ["code" => "success", "message" => "Password is valid."];
    return $result;
}

function appRegexUsername()
{
    // return '/^[a-zA-Z0-9_]{5,}$/';
    return '/^[a-zA-Z0-9_]{5,20}$/';
}

function appRegexPassword()
{
    // return '/^[a-zA-Z0-9_@#$&-]{5,}$/';
    return "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:',.<>?|])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:',.<>?|]{8,}$";
}


function appGenerateString($length = 32)
{
    $key = bin2hex(random_bytes($length));
    return $key;
}

function appGenerateRandomString(int $length = 6): string
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function appGenerateRandomNumber(int $length = 6): string
{
    $digits = '0123456789';
    $digitsLength = strlen($digits);
    $randomNumber = '';

    for ($i = 0; $i < $length; $i++) {
        $randomNumber .= $digits[random_int(0, $digitsLength - 1)];
    }

    return $randomNumber;
}

function appRenderIdKey($id = "", $key = "", $label = "")
{
    $html = <<<HTML
    <p style="font-size: 12px;" class="text-muted bg-light p-1">
        <span>{$label}</span>
        <span class="">#{$id}:{$key}</span>
    </p>
    HTML;

    return $html;
}

function appCurrentDateTime($format = "Y-m-d H:i:s")
{
    return date($format);
}

function appRenderActionsDTbl(array $params = [
    "id" => "",
    "cd" => "",
    "actions" => [],
    "menu" => [],
])
{
    $id = $params["id"] ?? appGenerateString(9);
    $cd = $params["cd"] ?? appGenerateString(9);

    $actions = implode(" ", $params["actions"] ?? []);
    $menu = implode("", $params["menu"] ?? []);
    if ($menu != "") {
        $collapse = <<<COLLAPSE
        <div class='btn-group btn-group-xs mb-1 btn-block ' style="width:150px;">
            {$actions}
            <button type="button" data-toggle="collapse" data-target="#div_collapse_{$cd}" class="btn btn-sm p-1 btn-dark"  title="Actions" style="width:50px;"><i class="fas fa-chevron-down px-2"></i></button>
        </div>
        <div class="collapse mt-2 mb-3 " id="div_collapse_{$cd}" style="width:150px;">
            {$menu}
        </div>
        COLLAPSE;
    } else {
        $collapse = <<<COLLAPSE
        <div class='btn-group btn-group-sm mb-1 ' >
            {$actions}
        </div>
        COLLAPSE;
    }
    return $collapse;
}

function appRenderLabel(
    $text = "",
    $width = "220px",
    $bg = "",
) {
    if (empty($bg)) {
        $lClass = [];
    } else {
        $lClass = ["bg-" . $bg];
    }
    $lStyle = ["width:" . $width];
    $params = [
        "class" => $lClass,
        "style" => $lStyle,
    ];
    if ($text == null) {
        $text = "";
    }
    return lteLabel($text, $params);
}

function appRenderLabelCustom(
    $text = "",
    array $class = [],
    array $style = []
) {
    if (!is_array($class)) {
        $lClass = [$class];
    } else {
        $lClass = $class;
    }
    if (!is_array($style)) {
        $lStyle = [$style];
    } else {
        $lStyle = $style;
    }
    if (count($lClass) <= 0) {
        $lClass = ["bg-white"];
    }
    if (count($lStyle) <= 0) {
        $lStyle = ["width:220px"];
    }
    $params = [
        "class" => $lClass,
        "style" => $lStyle,
    ];
    if ($text == null) {
        $text = "";
    }
    return lteLabel($text, $params);
}

function appRenderSectionHeader($text = "")
{
    $html = '<h6 class="border-bottom pb-2">' . $text . '</h6>';
    return $html;
}

function appRenderBtnPreviewDTbl(
    string $key,
    string $functionName = "",
    string $text = '<i class="fas fa-eye"> Preview</i>',
    string $width = "100px",
) {
    if ($functionName != "") {
        $actPreview = $functionName . "('" . $key . "');";
    } else {
        $actPreview = "";
    }
    $btnPreview = '<button type="button" class="btn btn-sm btn-dark text-center" onclick="' . $actPreview . '" style="width:' . $width . ';">' . $text . '</button>';

    return $btnPreview;
}

function appRenderBtnDtbl(
    string $key,
    string $text = " ",
    string $functionName = "",
    string $color = "dark",
    string $style = "",
) {
    if ($functionName != "") {
        $actBtn = $functionName . "('" . $key . "');";
    } else {
        $actBtn = "";
    }

    $btn = '<button type="button" class="btn btn-sm text-left btn-block btn-outline-' . $color . '" onclick="' . $actBtn . '" style="' . $style . '" >' . $text . '</button>';

    return $btn;
}

function appRenderBtnLinkDtbl($url = "", $text = "", $btnTypeClass = "btn-outline-dark", array $class = [], array $style = [])
{
    if ($text == "") {
        return "";
    }
    $defClass = ["btn", "btn-block", "btn-sm", "text-left"];
    $defClass[] = $btnTypeClass;
    foreach ($class as $vClass) {
        if (in_array($vClass, $defClass)) {
        } else {
            $defClass[] = $vClass;
        }
    }
    $strClass = implode(" ", $defClass);
    $strStyle = "";
    if (count($style) > 0) {
        $strStyle = "style='" . implode(" ", $style) . "'";
    }
    $btnView = "<a href='" . $url . "' class=' " . $strClass . "' " . $strStyle . ">" . $text . "</a>";
    return $btnView;
}

function appEmptyDTBL(
    string $code = "info",
    string  $message = "Empty"
) {
    if (!isset($_REQUEST['draw'])) {
        $draw = 1;
    } else {
        $draw = $_REQUEST['draw'];
    }
    $output = array();
    $output['draw'] = $draw;
    $output['data'] = array();
    $output['recordsTotal'] =   $output['recordsFiltered'] = 0;
    $output["code"] = $code;
    $output["message"] = $message;

    return $output;
}

function appRenderBadgeStatus(
    int $status = 0,
    array $text = ["Disabled", "Enable"]
) {
    if (!in_array($status, [0, 1])) {
        return "[ ? ]";
    }
    if ($status == 1) {
        return lteBadge($text[1], "success", "px-2 py-1");
    } else {
        return lteBadge($text[0], "danger",  "px-2 py-1");
    }
}


function appRenderBadgeLocked(
    int $status = 0,
    array $text = ["Dibuka", "Dikunci"]
) {
    if (!in_array($status, [0, 1])) {
        return "[ ? ]";
    }
    if ($status == 0) { // dibuka
        return lteBadge($text[0], "success", "px-2 py-1");
    } else {
        return lteBadge($text[1], "danger",  "px-2 py-1");
    }
}


function appGetQuarterMonthRange(int $quarter): ?array
{
    $ranges = [
        1 => ['start' => 1,  'end' => 3],   // Jan–Mar
        2 => ['start' => 4,  'end' => 6],   // Apr–Jun
        3 => ['start' => 7,  'end' => 9],   // Jul–Sep
        4 => ['start' => 10, 'end' => 12],  // Okt–Des
    ];

    return $ranges[$quarter] ?? null;
}

function appGetQuarterMonths(int $quarter): ?array
{
    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $range = appGetQuarterMonthRange($quarter);
    if (!$range) return null;

    $months = [];

    for ($i = $range['start']; $i <= $range['end']; $i++) {
        $months[] = $monthNames[$i];
    }

    return $months;
}

function appFormatTanggalIndonesia($date, bool $withDay = false): ?string
{
    if (empty($date)) {
        return "";
    }
    if (!$date || !strtotime($date)) {
        return null;
    }

    $namaHari = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    $namaBulan = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $timestamp = strtotime($date);
    $dayName   = date('l', $timestamp); // English day
    $day       = date('d', $timestamp);
    $month     = (int)date('m', $timestamp);
    $year      = date('Y', $timestamp);

    $formatted = $day . ' ' . $namaBulan[$month] . ' ' . $year;

    if ($withDay) {
        $formatted = $namaHari[$dayName] . ', ' . $formatted;
    }

    return $formatted;
}

function appGetCurrentQuarter(): int
{
    $bulan = (int)date('n'); // ambil nomor bulan (1–12)

    if ($bulan >= 1 && $bulan <= 3) {
        return 1;
    } elseif ($bulan >= 4 && $bulan <= 6) {
        return 2;
    } elseif ($bulan >= 7 && $bulan <= 9) {
        return 3;
    } else {
        return 4;
    }
}

function appIsValidYear($year, int $minYear = 1900, int $maxYear = null): bool
{
    if ($maxYear === null) {
        $maxYear = (int)date('Y') + 50;
    }

    return is_numeric($year)
        && (int)$year == $year
        && strlen((string)$year) === 4
        && $year >= $minYear
        && $year <= $maxYear;
}

function appIsDateInRanges(string $date, array $ranges): bool
{
    if (!strtotime($date)) {
        return false;
    }

    $timestamp = strtotime($date);

    foreach ($ranges as $range) {
        if (!isset($range['start'], $range['end'])) {
            continue; // skip jika tidak lengkap
        }

        $start = strtotime($range['start']);
        $end = strtotime($range['end']);

        if ($start && $end && $timestamp >= $start && $timestamp <= $end) {
            return true;
        }
    }

    return false;
}

function appRenderTableInfo2(
    array $data = [],
) {
    if (count($data) <= 0) {
        $html = <<<HTML
        <div class="table-responsive my-1" style="width:100%;">
            <table class="table table-striped table-hover table-sm border" style="width: 100%;">
                <tr>
                    <th >[ Empty ]</th>
                </tr>
            </table>
        </div>
        HTML;
        return $html;
    }

    $rows = [];

    foreach ($data as $key => $item) {
        $name = $item["name"];
        $value = $item["value"];
        $rows[] = <<<ROW
            <tr>
                <td>
                <span class='mb-2 text-bold' style='font-size:8pt'>{$name}</span><br>
                {$value}
                </td>
            </tr>
        ROW;
    }
    $strRows = implode("", $rows);
    $html = <<<HTML
    <div class="table-responsive">
        <table class="table table-hover table-sm no-border" style="width: 100%;">
            {$strRows}
        </table>
    </div>
    HTML;
    return $html;
}

function appRenderTableInfo(
    array $data = [],
    $colNameWidth = "120px"
) {
    if (count($data) <= 0) {
        $html = <<<HTML
        <div class="table-responsive my-1" style="width:100%;">
            <table class="table table-striped table-hover table-sm border" style="width: 100%;">
                <tr>
                    <th >[ Empty ]</th>
                </tr>
            </table>
        </div>
        HTML;
        return $html;
    }

    $rows = [];

    foreach ($data as $key => $item) {
        $name = $item["name"];
        $value = $item["value"];
        $nameClass = "";
        $valueClass = "";
        if (!empty($item["name_class"])) {
            $nameClass = $item["name_class"];
        }
        if (!empty($item["value_class"])) {
            $valueClass = $item["value_class"];
        }
        $rows[] = <<<ROW
            <tr>
                <th style="width: {$colNameWidth};" class="{$nameClass}">{$name}</th>
                <th style="width: 10px;">:</th>
                <td class="{$valueClass}">{$value}</td>
            </tr>
        ROW;
    }
    $strRows = implode("", $rows);
    $html = <<<HTML
    <div class="table-responsive">
        <table class="table table-striped table-hover table-sm border" style="width: 100%;">
            {$strRows}
        </table>
    </div>
    HTML;
    return $html;
}

function appRenderTableInfo3(
    array $data = [],
    $colNameWidth = "120px"
) {
    if (count($data) <= 0) {
        $html = <<<HTML
        <div class="table-responsive my-1" style="width:100%;">
            <table class="table table-striped table-hover table-sm border" style="width: 100%;">
                <tr>
                    <th >[ Empty ]</th>
                </tr>
            </table>
        </div>
        HTML;
        return $html;
    }

    $rows = [];

    foreach ($data as $key => $item) {
        $name = $item["name"];
        $value = $item["value"];
        $nameClass = "";
        $valueClass = "";
        if (!empty($item["name_class"])) {
            $nameClass = $item["name_class"];
        }
        if (!empty($item["value_class"])) {
            $valueClass = $item["value_class"];
        }
        $rows[] = <<<ROW
            <tr>
                <th style="width: {$colNameWidth};" class="{$nameClass}">{$name}</th>
                <th style="width: 10px;">:</th>
                <td class="{$valueClass}">{$value}</td>
            </tr>
        ROW;
    }
    $strRows = implode("", $rows);
    $html = <<<HTML
    <div class="table-responsive">
        <table class="table table-hover table-sm border" style="width: 100%;">
            {$strRows}
        </table>
    </div>
    HTML;
    return $html;
}

function appRenderImage(
    string $src = "",
    string $alt = "",
    string $class = "img-fluid",
    string $style = "max-width: 100%; height: auto;"
) {
    if ($src == "") {
        return "";
    }
    $html = <<<HTML
    <img src="{$src}" alt="{$alt}" class="{$class}" style="{$style}">
    HTML;
    return $html;
}

function appRenderUserPhoto($usrKey = "", $photoFName = "", $useDefault = true)
{
    if ($usrKey == "" || $photoFName == "") {
        if ($useDefault) {
            return appRenderImage(assetUser(), "User Photo", "img-thumbnail", "width: 120px;");
        } else {
            return "";
        }
    }
    // Get user photo
    $user = appGetUserPhoto($usrKey, $photoFName);
    if ($user["code"] != "success") {
        return "";
    }
    $photoUrl = $user["file_url"];
    if ($photoUrl == "") {
        return "";
    }
    return appRenderImage($photoUrl, "User Photo", "img-thumbnail", "width: 120px;");
}

function appGetFutureDateTime(int $days, string $format = 'Y-m-d H:i:s'): string
{
    return date($format, strtotime("+$days days"));
}

function appValidateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
function appIsValidDateTime(string $datetime): bool
{
    try {
        // Time::parse($datetime, 'Asia/Jakarta');
        Time::parse($datetime);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function appIsExpired(string $endDateTime): bool
{
    if (!appIsValidDateTime($endDateTime)) {
        return true;
    }

    // $now = Time::now('Asia/Jakarta');
    // $end = Time::parse($endDateTime, 'Asia/Jakarta');

    $now = Time::now();
    $end = Time::parse($endDateTime);
    return $now->isAfter($end);
}

function appBtnCopyToClipboard($text = "", $class = "", $style = "", $label = "<i class='fas fa-copy'></i> Copy")
{

    $btn = <<<BTN
        <a role="button" class="btn btn-sm btn-link {$class}" onclick='copyToClipboard("{$text}")' title="Copy" style="{$style}">
        {$label}
        </a>
    BTN;
    return $btn;
}

function appRenderInputText($value = "", $style = "")
{
    $html = <<<HTML
    <input type="text" class="form-control form-control-sm" value="{$value}" readonly style="{$style}">
    HTML;
    return $html;
}

function appDeviceInfo()
{
    $Request = \Config\Services::request();
    $agent = $Request->getUserAgent();
    if ($agent->isBrowser()) {
        return $agent->getBrowser() . ' - ' . $agent->getPlatform();
    } elseif ($agent->isRobot()) {
        return 'Robot - ' . $agent->getRobot();
    } elseif ($agent->isMobile()) {
        return $agent->getMobile();
    }
    return 'Unknown Device';
}

function appSendEmail($receiverEmail, $subject = "", $message = "")
{
    try {
        $ciEmail = service('email');
        $ciEmail->setTo($receiverEmail);
        // $email->setCC('another@another-example.com');
        // $email->setBCC('them@their-example.com');

        $ciEmail->setSubject($subject);
        $body = $message;
        $ciEmail->setMessage($body);
        if ($ciEmail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (\Throwable $th) {
        log_message("error", "Send Email failed." . $th->getMessage() . " [ " . $th->getLine() . " ] ");
        return false;
    }
}
