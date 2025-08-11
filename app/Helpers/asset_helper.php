<?php
function assetUser()
{
    return assetUrl("img/user_bw.jpg");
}
function assetLogo()
{
    return assetUrl("img/logo.png");
}
function assetUrl(string $path = "")
{
    $url =  base_url('assets');
    if ($path == "") {
        return $url . "/";
    }
    if (substr($path, 0, 1) == "/") {
        return $url . $path;
    } else {
        return $url . "/" . $path;
    }
    return $url;
}
