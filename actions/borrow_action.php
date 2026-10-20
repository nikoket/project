<?php
// actions/borrow_action.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$action = $_GET['action'] ?? '';

// 1. Student Requests to Borrow a Book
if ($action === 'request' && $_SESSION['role'] === 'student') {
    $book_id = (int)$_GET['book_id'];
    $student_tbl_id = $_SESSION['student_tbl_id'];

    // Stock Verification
    $b_stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
    $b_stmt->execute([$book_id]);
    $book = $b_stmt->fetch();

    if (!$book || $book['available_copies'] <= 0) {
        header("Location: ../home.php?error=Book is out of stock!");
        exit();
    }

    // Submit Request
    $stmt = $pdo->prepare("INSERT INTO borrow_requests (student_id, book_id, status) VALUES (?, ?, 'Pending Borrow')");
    $stmt->execute([$student_tbl_id, $book_id]);

    header("Location: ../home.php?success=Borrow request submitted to librarian!");
    exit();
}

// 2. Librarian Approves Borrow Request
if ($action === 'approve_borrow' && $_SESSION['role'] === 'librarian') {
    $request_id = (int)$_GET['request_id'];

    $r_stmt = $pdo->prepare("SELECT book_id FROM borrow_requests WHERE id = ? AND status = 'Pending Borrow'");
    $r_stmt->execute([$request_id]);
    $request = $r_stmt->fetch();

    if ($request) {
        $due_date = date('Y-m-d', strtotime('+14 days'));
        
        $pdo->beginTransaction();
        $u_stmt = $pdo->prepare("UPDATE borrow_requests SET status = 'Borrowed', approved_date = CURDATE(), due_date = ? WHERE id = ?");
        $u_stmt->execute([$due_date, $request_id]);

        // Decrement stock
        $bk_stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ? AND available_copies > 0");
        $bk_stmt->execute([$request['book_id']]);

        $pdo->commit();
        header("Location: ../requests.php?success=Borrow Request Approved!");
        exit();
    }
}

// 3. Student Requests Return
if ($action === 'request_return' && $_SESSION['role'] === 'student') {
    $request_id = (int)$_GET['request_id'];
    $stmt = $pdo->prepare("UPDATE borrow_requests SET status = 'Return Requested' WHERE id = ? AND student_id = ?");
    $stmt->execute([$request_id, $_SESSION['student_tbl_id']]);

    header("Location: ../home.php?success=Return request submitted!");
    exit();
}

// 4. Librarian Approves Return & Restocks
if ($action === 'approve_return' && $_SESSION['role'] === 'librarian') {
    $request_id = (int)$_GET['request_id'];

    $r_stmt = $pdo->prepare("SELECT book_id FROM borrow_requests WHERE id = ? AND status = 'Return Requested'");
    $r_stmt->execute([$request_id]);
    $request = $r_stmt->fetch();

    if ($request) {
        $pdo->beginTransaction();
        $u_stmt = $pdo->prepare("UPDATE borrow_requests SET status = 'Returned', return_date = CURDATE() WHERE id = ?");
        $u_stmt->execute([$request_id]);

        // Increment stock back
        $bk_stmt = $pdo->prepare("UPDATE books SET available_copies = LEAST(total_copies, available_copies + 1) WHERE id = ?");
        $bk_stmt->execute([$request['book_id']]);

        $pdo->commit();
        header("Location: ../requests.php?success=Book Returned and Stock Updated!");
        exit();
    }
}
?>