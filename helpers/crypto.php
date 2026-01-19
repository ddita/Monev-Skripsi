<?php
if (!function_exists('encriptData')) {
    function encriptData($data)
    {
        return urlencode(base64_encode($data));
    }
}

if (!function_exists('decriptData')) {
    function decriptData($data)
    {
        return base64_decode(urldecode($data));
    }
}
