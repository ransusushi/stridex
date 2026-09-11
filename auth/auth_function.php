<?php
// Turn off error displaying so we can show our own custom error page
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require '../database/config.php';
require 'auth_validation.php';

// ---------- Handle Signup ----------
if (isset($_POST['signup'])) {
    $result = validateSignupInput($_POST);
    $errors = $result['errors'];

    if (!empty($errors)) {
        $message = implode(' ', $errors);
        header('Location: signup.php?status=error&message=' . urlencode($message));
        exit;
    }

    try {
        $pdo = getConnection();
        $hash = password_hash($result['data']['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password_hash, first_name, last_name)
                VALUES (:email, :hash, :first_name, :last_name)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $result['data']['email']);
        $stmt->bindValue(':hash', $hash);
        $stmt->bindValue(':first_name', $result['data']['first_name']);
        $stmt->bindValue(':last_name', $result['data']['last_name']);
        $stmt->execute();

        header('Location: login.php?status=success&message=Account created! Please log in.');
        exit;
    } catch (PDOException $e) {
        // Log the real error for yourself
        if (defined('ERROR_LOG_FILE')) {
            file_put_contents(ERROR_LOG_FILE, "[" . date('Y-m-d H:i:s') . "] SIGNUP DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
        // Show the maintenance page instead of redirecting
        showMaintenancePage();
        exit;
    }
}

// ---------- Handle Login ----------
if (isset($_POST['login'])) {
    $result = validateLoginInput($_POST);
    $errors = $result['errors'];

    if (!empty($errors)) {
        $message = implode(' ', $errors);
        header('Location: login.php?status=error&message=' . urlencode($message));
        exit;
    }

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$result['data']['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($result['data']['password'], $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];

            if ($result['data']['remember']) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + 86400 * 30, '/');
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
            }

            // ---------- REDIRECT LOGIC ----------
            if ($user['role'] === 'admin') {
                header('Location: /stride/admin/index.php');
                exit;
            } else {
                header('Location: /stride/index.php');
                exit;
            }
        } else {
            header('Location: login.php?status=error&message=Invalid email or password.');
            exit;
        }

    } catch (PDOException $e) {
        // Log the real error for yourself
        if (defined('ERROR_LOG_FILE')) {
            file_put_contents(ERROR_LOG_FILE, "[" . date('Y-m-d H:i:s') . "] LOGIN DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
        
        // Show the maintenance page instead of redirecting
        showMaintenancePage();
        exit;
    }
}