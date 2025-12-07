<?php
function validateUpload($file, $maxMb = 2) {
    $maxSize = $maxMb * 1024 * 1024;

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return "Tidak ada file yang diupload.";
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Terjadi kesalahan saat upload file.";
    }

    if ($file['size'] > $maxSize) {
        return "Ukuran file maksimal {$maxMb} MB.";
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return "Format file harus JPG, PNG, atau WEBP.";
    }

    return true;
}