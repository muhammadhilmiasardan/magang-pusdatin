<?php
try {
    $db = new PDO('mysql:host=127.0.0.1', 'root', '');
    $db->exec('CREATE DATABASE IF NOT EXISTS magang_pusdatin');
    echo "Database created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
