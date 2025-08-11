<?php
function mycsrfTokenName()
{
    return  "xcsrftoken";
}

function mycsrfTokenLifetime()
{
    $minutes = 30;
    return MINUTE * $minutes;
}

// Generate a new CSRF token and store it in the session
function mycsrfTokenGenerate()
{
    $token = bin2hex(random_bytes(32));
    $timestamp = time();

    if (!isset($_SESSION[mycsrfTokenName()])) {
        $_SESSION[mycsrfTokenName()] = [];
    }

    // Add the new token with timestamp
    $_SESSION[mycsrfTokenName()][] = [
        'token' => $token,
        'timestamp' => $timestamp
    ];

    // Clean up expired tokens
    mycsrfTokenCleanup();

    return $token;
}

// Validate a submitted CSRF token
function mycsrfTokenValidate($token)
{
    if ($token == "") {
        return false;
    }

    if (isset($_SESSION[mycsrfTokenName()])) {
        foreach ($_SESSION[mycsrfTokenName()] as $key => $csrf) {
            if ($csrf['token'] === $token) {
                // Check if token is within the valid lifetime
                if (time() - $csrf['timestamp'] <= mycsrfTokenLifetime()) {
                    // Remove the validated token
                    unset($_SESSION[mycsrfTokenName()][$key]);
                    return true;
                } else {
                    // Token expired
                    unset($_SESSION[mycsrfTokenName()][$key]);
                    return false;
                }
            }
        }
    }

    return false;
}
// Clean up expired tokens
function mycsrfTokenCleanup()
{
    if (!isset($_SESSION[mycsrfTokenName()])) {
        return;
    }

    $current_time = time();

    $_SESSION[mycsrfTokenName()] = array_filter($_SESSION[mycsrfTokenName()], function ($csrf) use ($current_time) {
        return ($current_time - $csrf['timestamp'] <= mycsrfTokenLifetime());
    });
}

// Get a hidden input field for forms
function mycsrfTokenField()
{
    $token = mycsrfTokenGenerate();
    return '<input type="hidden" name="' . mycsrfTokenName() . '" value="' . htmlspecialchars($token) . '">';
}
