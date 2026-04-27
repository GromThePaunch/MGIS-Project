<?php
// Database configuration
$host = 'studb-2.scb.rit.edu';
$db   = '2255_MGIS445_13';
$user = 'mcm5381';
$pass = 'mjzmrkgo0547XLBA';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    die("Sorry, we're having trouble connecting to the database. Please try again later.");
}
?>