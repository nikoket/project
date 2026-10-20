<?php
// actions/auth_action.php
session_start();
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

// 1. UNIFIED LOGIN
if ($action === 'login') {
    $login_input = trim($_POST['login_user'] ?? '');
    $password    = trim($_POST['login_pass'] ?? '');

    if (empty($login_input) || empty($password)) {
        header("Location: ../index.php?error=Please fill in all fields.");
        exit();
    }

    // Query across both Username and Student ID
    $stmt = $pdo->prepare("
        SELECT u.*, s.id AS student_tbl_id, s.student_id, s.barcode 
        FROM users u 
        LEFT JOIN students s ON u.id = s.user_id 
        WHERE u.username = :input OR s.student_id = :input
    ");
    $stmt->execute(['input' => $login_input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        if ($user['role'] === 'student') {
            $_SESSION['student_tbl_id'] = $user['student_tbl_id'];
            $_SESSION['student_id']     = $user['student_id'];
            $_SESSION['barcode']        = $user['barcode'];
            header("Location: ../home.php");
        } else {
            header("Location: ../dashboard.php");
        }
        exit();
    } else {
        header("Location: ../index.php?error=Invalid Credentials!");
        exit();
    }
}

// 2. STUDENT REGISTRATION WITH BARCODE
if ($action === 'register') {
    $name       = trim($_POST['reg_name'] ?? '');
    $student_id = trim($_POST['reg_id'] ?? '');
    $course     = trim($_POST['reg_course'] ?? '');
    $username   = trim($_POST['reg_user'] ?? '');
    $password   = trim($_POST['reg_pass'] ?? '');

    if (empty($name) || empty($student_id) || empty($course) || empty($username) || empty($password)) {
        header("Location: ../register.php?error=All fields are required.");
        exit();
    }

    // Check for duplicates
    $check = $pdo->prepare("
        SELECT u.id FROM users u 
        LEFT JOIN students s ON u.id = s.user_id 
        WHERE u.username = ? OR s.student_id = ?
    ");
    $check->execute([$username, $student_id]);
    if ($check->fetch()) {
        header("Location: ../register.php?error=Username or Student ID already exists!");
        exit();
    }

    // Generate Unique Barcode (e.g., BC-84920183)
    $barcode = 'BC-' . rand(10000000, 99999999);
    $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

    $pdo->beginTransaction();
    try {
        $stmt1 = $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, 'student')");
        $stmt1->execute([$username, $hashed_pass, $name]);
        $user_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO students (user_id, student_id, name, course, barcode) VALUES (?, ?, ?, ?, ?)");
        $stmt2->execute([$user_id, $student_id, $name, $course, $barcode]);

        $pdo->commit();
        header("Location: ../index.php?success=Registration Successful! Your Unique Barcode is: " . $barcode);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: ../register.php?error=Registration failed. Please try again.");
        exit();
    }
}
?>