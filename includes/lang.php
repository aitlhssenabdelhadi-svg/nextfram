<?php
function initLang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang'] = 'fr';
    }
    return $_SESSION['lang'];
}

function t($key) {
    static $strings = null;
    if ($strings === null) {
        $lang = $_SESSION['lang'] ?? 'fr';
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (!file_exists($file)) $file = __DIR__ . '/../lang/fr.php';
        $strings = require $file;
    }
    return isset($strings[$key]) ? $strings[$key] : $key;
}

function getLang() {
    return $_SESSION['lang'] ?? 'fr';
}

function langField($row, $field) {
    $lang = getLang();
    $key = $field . '_' . $lang;
    return isset($row[$key]) ? $row[$key] : (isset($row[$field . '_fr']) ? $row[$field . '_fr'] : '');
}
