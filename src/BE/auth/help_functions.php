<?php
function checkLength($field, $min, $max) {
    $string = trim($field);
    $length = strlen($string);
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}
function checkEmpty($field) {
    if (empty(trim($field))) {
        return true;
    }
    return false;
}
function checkUsername($username) {
    if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))) {
        return false;
    }
    return true;
}