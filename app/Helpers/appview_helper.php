<?php

function appViewInjectContent(string $view, string $filename = "", array $data = [])
{
    if ($filename == "") {
        return "";
    }
    if (substr($filename, 0, 1) == "/") {
        $path = $view . "/inc/contents" . $filename;
    } else {
        $path = $view . "/inc/contents/" . $filename;
    }
    return view($path, $data, ["saveData" => true]);
}


function appViewInjectHead(string $view, string $filename = "", array $data = [])
{
    if ($filename == "") {
        return "";
    }
    if (substr($filename, 0, 1) == "/") {
        $path = $view . "/inc/heads" . $filename;
    } else {
        $path = $view . "/inc/heads/" . $filename;
    }
    return view($path, $data, ["saveData" => true]);
}


function appViewInjectModal(string $view, string $filename = "", array $data = [])
{
    if ($filename == "") {
        return "";
    }
    if (substr($filename, 0, 1) == "/") {
        $path = $view . "/inc/modals" . $filename;
    } else {
        $path = $view . "/inc/modals/" . $filename;
    }
    return view($path, $data, ["saveData" => true]);
}

function appViewInjectScript(string $view, string $filename = "", array $data = [])
{
    if ($filename == "") {
        return "";
    }
    if (substr($filename, 0, 1) == "/") {
        $path = $view . "/inc/scripts" . $filename;
    } else {
        $path = $view . "/inc/scripts/" . $filename;
    }
    return view($path, $data, ["saveData" => true]);
}


function appViewDirLayoutScript(string $filename = "")
{
    if ($filename == "") {
        return "layout/inc/scripts/";
    }
    if (substr($filename, 0, 1) == "/") {
        return "layout/inc/scripts" . $filename;
    } else {
        return "layout/inc/scripts/" . $filename;
    }
}

function appViewDirLayoutContent(string $filename = "")
{
    if ($filename == "") {
        $fPath = "layout/inc/contents/";
    }
    if (substr($filename, 0, 1) == "/") {
        $fPath = "layout/inc/contents" . $filename;
    } else {
        $fPath = "layout/inc/contents/" . $filename;
    }
    return $fPath;
}

function appViewDirLayoutHead(string $filename = "")
{
    if ($filename == "") {
        return "layout/inc/heads/";
    }
    if (substr($filename, 0, 1) == "/") {
        return "layout/inc/heads" . $filename;
    } else {
        return "layout/inc/heads/" . $filename;
    }
}

function appViewLayoutFile(string $type = "main")
{
    switch (strtolower($type)) {
        case 'auth':
            $layout = "layout/auth";
            break;
        case 'error':
            $layout = "layout/error";
            break;
        case 'main':
        default:
            $layout = "layout/main2";
            break;
    }
    return $layout;
}
