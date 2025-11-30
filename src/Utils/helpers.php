<?php
// Utils helpers for the financial system

function now() {
    return date("Y-m-d H:i:s");
}

function generateHash($text) {
    return hash("sha256", $text);
}
