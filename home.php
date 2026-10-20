<?php
// home.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$student_tbl_id = $_SESSION['student_tbl_id'];

// Fetch Active Loans for this Student
$loans_stmt = $pdo->prepare("
    SELECT br.*, b.title, b.author 
    FROM borrow_requests br 
    JOIN books b ON br.book_id = b.id 
    WHERE br.student_id = ? AND br.status IN ('Pending Borrow', 'Borrowed', 'Return Requested')
");
$loans_stmt->execute([$student_tbl_id]);
$my_loans = $loans_stmt->fetchAll();

// Fetch Recommended / Available Books
$catalog_stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 4");
$catalog_books = $catalog_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal - BAPS e-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <header class="bg-[#1e293b] text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="logo.jpg" class="w-10 h-10 rounded-full border border-amber-400 object-cover" onerror="this.src='https://placehold.co/40x40?text=BAPS'">
                <div><h1 class="font-bold text-lg">BAPS e-Library</h1></div>
            </div>
            <nav class="flex items-center space-x-6 text-sm">
                <a href="home.php" class="text-amber-300 font-bold border-b-2 border-amber-300 pb-1">Home</a>
                <a href="my_borrowed.php" class="hover:text-amber-300">My Borrowed Books</a>
                <a href="catalog.php" class="hover:text-amber-300">Book Catalog</a>
                <a href="logout.php" class="text-rose-400 hover:underline">Log Out</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl w-full mx-auto px-4 py-6 flex-grow space-y-6">
        <div class="bg-[#1e293b] text-white rounded-2xl p-8 shadow-xl">
            <h2 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
            <p class="text-blue-200 text-lg mb-4">Explore Your Library Resources</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Currently Borrowed Section -->
            <div class="lg:col-span-5 bg-white p-6 rounded-2xl shadow border border-slate-200">
                <h3 class="font-bold text-lg mb-4">Currently Borrowed (<?= count($my_loans) ?>)</h3>
                <div class="space-y-3">
                    <?php if (empty($my_loans)): ?>
                        <p class="text-sm text-slate-500 py-6 text-center">You have no currently borrowed books.</p>
                    <?php else: foreach ($my_loans as $l): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border">
                            <div>
                                <h4 class="font-semibold text-sm"><?= htmlspecialchars($l['title']) ?></h4>
                                <p class="text-xs text-slate-500">Status: <?= $l['status'] ?></p>
                            </div>
                            <?php if ($l['status'] === 'Borrowed'): ?>
                                <a href="actions/borrow_action.php?action=request_return&request_id=<?= $l['id'] ?>" class="bg-[#1e293b] text-white text-xs px-3 py-1.5 rounded hover:bg-slate-800">Request Return</a>
                            <?php else: ?>
                                <span class="text-xs text-blue-600 font-bold">Pending</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Recommended Books Section -->
            <div class="lg:col-span-7 bg-white p-6 rounded-2xl shadow border border-slate-200">
                <h3 class="font-bold text-lg mb-4">Recommended for You</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php if (empty($catalog_books)): ?>
                        <p class="col-span-2 text-sm text-slate-500 py-6 text-center">No books available in catalog.</p>
                    <?php else: foreach ($catalog_books as $b): ?>
                        <div class="bg-slate-50 p-4 rounded-xl border flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-sm"><?= htmlspecialchars($b['title']) ?></h4>
                                <p class="text-xs text-slate-500 mb-3">By <?= htmlspecialchars($b['author']) ?></p>
                            </div>
                            <a href="actions/borrow_action.php?action=request&book_id=<?= $b['id'] ?>" class="block text-center bg-[#1e293b] text-white text-xs py-2 rounded font-semibold hover:bg-slate-800">Request Borrow</a>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>