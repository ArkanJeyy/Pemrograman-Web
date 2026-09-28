<?php
$host = "localhost";
$port = "5432";
$db   = "simpus-mini";
$user = "postgres";
$pass = "arkan06";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
 
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}