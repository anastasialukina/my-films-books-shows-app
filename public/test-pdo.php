<?php
$host = '127.0.0.1';
$db = 'my-films-books-shows-app';
$user = 'root';
$pass = '1864';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
$stmt = $pdo->query('SELECT full_name FROM users');
while ($row = $stmt->fetch()) {
    echo $row['full_name'] . "\n";
}
