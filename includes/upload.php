<?php
function handleUpload($file, $subfolder, $allowedTypes = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }

    if ($allowedTypes === null) $allowedTypes = ALLOWED_IMAGE_TYPES;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $mime];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large (max 50MB).'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('nf_', true) . '.' . strtolower($ext);
    $dir = UPLOAD_PATH . $subfolder . '/';

    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $dest = $dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to save file.'];
    }

    return ['success' => true, 'path' => 'assets/uploads/' . $subfolder . '/' . $filename];
}

function deleteFile($path) {
    if ($path && file_exists(__DIR__ . '/../' . $path)) {
        unlink(__DIR__ . '/../' . $path);
    }
}

function imageTag($path, $alt = '', $class = '') {
    if ($path && file_exists(__DIR__ . '/../' . $path)) {
        $url = SITE_URL . '/' . $path;
        return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '">';
    }
    return '';
}

function assetUrl($path) {
    if ($path && file_exists(__DIR__ . '/../' . $path)) {
        return SITE_URL . '/' . $path;
    }
    return '';
}

function isVideoAsset($path) {
    return is_string($path) && preg_match('/\.(mp4|webm)$/i', $path) === 1;
}
