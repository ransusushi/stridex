<?php
require 'database/config.php';
try {
    $pdo = getConnection();
    echo "✅ Connected!";
} catch (Exception $e) {
    echo "❌ " . $e->getMessage();
}