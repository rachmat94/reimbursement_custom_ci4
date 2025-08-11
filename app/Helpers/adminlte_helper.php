<?php
function lteTextarea(
    string $text,
    array $params = [
        "id" => "",
        "name" => "",
        "rows" => "",
        "cols" => "",
        "readonly" => "",
        "class" => "",
        "style" => "",
        "disabled" => ""
    ]
) {
    if ($text == "") {
        return "";
    }

    $id = $params["id"] ?? "";
    $name = $params["name"] ?? "";
    $rows = $params["rows"] ?? "2";
    $cols = $params["cols"] ?? "";
    $readonly = $params["readonly"] ?? "";
    $class = $params["class"] ?? "";
    $style = $params["style"] ?? "";
    $disabled  = $params["disabled"] ?? "";
    $textarea = <<<TEXT
    <textarea id="{$id}" name="{$name}" rows="{$rows}" cols="{$cols}" {$readonly} class="{$class}" style="{$style}" {$disabled}>{$text}</textarea>
    TEXT;
    return $textarea;
}

function lteLabel(
    string $text = "",
    array $params = []
) {
    if ($text == "") {
        return "";
    }

    if (isset($params["class"]) && !is_array($params["class"])) {
        $params["class"] = [$params["class"]];
    }

    if (isset($params["style"]) && !is_array($params["style"])) {
        $params["style"] = [$params["style"]];
    }

    $class = implode(" ", $params["class"] ?? []);
    $style = implode(";", $params["style"] ?? []);

    $label = <<<LABEL
    <label class="px-2 py-1 {$class}" style="{$style}">{$text}</label>
    LABEL;
    return $label;
}

function lteBadge(
    string $text,
    string $code = "info",
    string $class = "",
    string $style = ""
) {
    $attStyle = "";
    if ($style != "") {
        $attStyle = " style='" . $style . "' ";
    }
    $badge = <<<BADGE
    <span class="badge badge-{$code} {$class}" {$attStyle}>{$text}</span>
    BADGE;
    return $badge;
}

function lteAlert(
    string $code = "info",
    string $message = "",
    array $params = [
        "close" => true,
        "disableTitle" => false,
        "titleIcon" => "fas fa-info",
        "titleText" => "Alert!",
    ]
) {
    $code = strtolower($code);
    if ($code == "error") {
        $code = "danger";
    }
    if (!in_array($code, [
        "info",
        "danger",
        "warning",
        "success",
        "dark",
    ])) {
        $code = "info";
    }
    $clsClose = "alert-dismissible";
    $clsElement = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    if (isset($params["close"]) && $params["close"] == false) {
        $clsClose = "";
        $clsElement = "";
    }

    if (isset($params["disableTitle"]) && $params["disableTitle"] == true) {
        $titleElement = "";
    } else {
        $titleIcon = "fas fa-info";
        if (isset($params["titleIcon"]) && $params["titleIcon"] != "") {
            $titleIcon = $params["titleIcon"];
        } else {
            switch ($code) {
                case 'danger':
                    $titleIcon = "fas fa-ban";
                    $titleElement = '<h5><i class="icon fas fa-ban"></i> Alert!</h5>';
                    break;
                case 'warning':
                    $titleIcon = " fas fa-exclamation-triangle";
                    $titleElement = '<h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>';
                    break;
                case 'success':
                    $titleIcon = "fas fa-check";
                    $titleElement = '<h5><i class="icon fas fa-check"></i> Alert!</h5>';
                    break;
                case 'info':
                default:
                    $titleIcon = "fas fa-info";
                    $titleElement = '<h5><i class="icon fas fa-info"></i> Alert!</h5>';
                    break;
            }
        }
        if (isset($params["titleText"]) && $params["titleText"] != "") {
            $titleText = $params["titleText"];
        } else {
            $titleText = "Alert!";
        }
        $titleElement = '<h5><i class="icon ' . $titleIcon . '"></i> ' . $titleText . '</h5>';
    }

    $alert = <<<ALERT
    <div class="alert alert-{$code} {$clsClose}">
        {$clsElement}
        {$titleElement}
        {$message}
    </div>
    ALERT;
    return $alert;
}
