<?php
// actions/student_action.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$query = trim($_GET['query'] ?? '');

if (empty($query)) {
    echo json_encode(['error' => 'Query string is required']);
    exit();
}

// Search student by Barcode or Student ID
$stmt = $pdo->prepare("
    SELECT s.*, u.username 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.barcode = :q OR s.student_id = :q
");
$stmt->execute(['q' => $query]);
$student = $stmt->fetch();

if (!$student) {
    echo json_encode(['found' => false]);
    exit();
}

// Fetch active loans for this student
$l_stmt = $pdo->prepare("
    SELECT br.*, b.title 
    FROM borrow_requests br 
    JOIN books b ON br.book_id = b.id 
    WHERE br.student_id = ? AND br.status IN ('Pending Borrow', 'Borrowed', 'Return Requested')
");
$l_stmt->execute([$student['id']]);
$loans = $l_stmt->fetchAll();

echo json_encode([
    'found' => true,
    'student' => $student,
    'loans' => $loans
]);
?>