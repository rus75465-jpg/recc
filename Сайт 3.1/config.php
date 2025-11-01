<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'rus754jm_hvyhjim');
define('DB_PASS', 'Vz2v5Z2E');
define('DB_NAME', 'rus754jm_hvyhjim');

session_start();

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_MUSIC_TYPES', ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']);

if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
    mkdir(UPLOAD_DIR . 'photos/', 0777, true);
    mkdir(UPLOAD_DIR . 'music/', 0777, true);
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

