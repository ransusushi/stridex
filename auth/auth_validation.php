<?php
function validateAuthRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validatePasswordStrength(string $value): ?string
{
    return strlen($value) >= 6 ? null : "Password must be at least 6 characters.";
}

function validatePasswordMatch(string $password, string $confirm): ?string
{
    return $password === $confirm ? null : "Passwords do not match.";
}

function validateEmailUnique(string $email): ?string
{
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() ? "Email already registered." : null;
}

function validateSignupInput(array $post): array
{
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';
    $confirm  = $post['confirm_password'] ?? '';
    $first_name = trim($post['first_name'] ?? '');
    $last_name  = trim($post['last_name'] ?? '');

    $errors = array_filter([
        validateAuthRequired($first_name, 'First name'),
        validateAuthRequired($last_name, 'Last name'),
        validateAuthRequired($email, 'Email'),
        validateEmailFormat($email),
        validateEmailUnique($email),
        validateAuthRequired($password, 'Password'),
        validatePasswordStrength($password),
        validatePasswordMatch($password, $confirm),
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data' => [
            'email' => $email,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ],
    ];
}

function validateLoginInput(array $post): array
{
    $email = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';
    $remember = isset($post['remember']);

    $errors = array_filter([
        validateAuthRequired($email, 'Email'),
        validateEmailFormat($email),
        validateAuthRequired($password, 'Password'),
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data' => [
            'email' => $email,
            'password' => $password,
            'remember' => $remember,
        ],
    ];
}