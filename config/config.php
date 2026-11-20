<?php
date_default_timezone_set('Asia/Jakarta');

$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));
$base = strpos($appRoot, $docRoot) === 0 ? substr($appRoot, strlen($docRoot)) : '';
define('BASE_URL', rtrim($base, '/'));

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_perpustakaan');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi basis data gagal: ' . $e->getMessage());
}
