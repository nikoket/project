<?php
// dashboard.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header("Location: index.php");
    exit();
}

// Fetch System Overview Metrics
$total_books     = $pdo->query("SELECT SUM(total_copies) FROM books")->fetchColumn() ?: 0;
$reg_students    = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() ?: 0;
$curr_borrowed   = $pdo->query("SELECT COUNT(*) FROM borrow_requests WHERE status IN ('Borrowed', 'Return Requested')")->fetchColumn() ?: 0;
$overdue_books   = $pdo->query("SELECT COUNT(*) FROM borrow_requests WHERE status = 'Borrowed' AND due_date < CURDATE()")->fetchColumn() ?: 0;

// Fetch Recent Requests
$requests_stmt   = $pdo->query("
    SELECT br.*, s.name AS student_name, b.title AS book_title 
    FROM borrow_requests br 
    JOIN students s ON br.student_id = s.id 
    JOIN books b ON br.book_id = b.id 
    ORDER BY br.id DESC LIMIT 4
");
$recent_requests = $requests_stmt->fetchAll();

// Fetch Low Stock Books
$low_stock_stmt  = $pdo->query("SELECT * FROM books WHERE available_copies <= 2 ORDER BY available_copies ASC LIMIT 4");
$low_stock       = $low_stock_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librarian Dashboard - BAPS e-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="bg-[#1e293b] text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="logo.jpg" alt="BAPS Logo" class="w-10 h-10 rounded-full border border-amber-400 object-cover" onerror="this.src='https://placehold.co/40x40?text=BAPS'">
                <div>
                    <h1 class="font-bold text-lg tracking-wide flex items-center space-x-2">
                        <span>BAPS e-Library</span>
                        <span class="text-xs bg-amber-400 text-slate-950 px-2 py-0.5 rounded font-semibold">System</span>
                    </h1>
                </div>
            </div>

            <nav class="flex items-center space-x-6">
                <a href="dashboard.php" class="text-amber-300 font-bold border-b-2 border-amber-300 pb-1 text-sm">Dashboard</a>
                <a href="inventory.php" class="hover:text-amber-300 text-sm transition">Inventory</a>
                <a href="students.php" class="hover:text-amber-300 text-sm transition">Students</a>
                <a href="requests.php" class="hover:text-amber-300 text-sm transition">Borrowed/Return Requests</a>
                <a href="about.php" class="hover:text-amber-300 text-sm transition">About</a>

                <!-- Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700">
                        <i class="fa-solid fa-user-shield text-amber-400"></i>
                        <span class="text-sm font-medium">Librarian Admin</span>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded-xl shadow-xl hidden group-hover:block border border-slate-200 py-1 z-50">
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-right-from-bracket mr-2"></i>Log Out</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl w-full mx-auto px-4 py-6 flex-grow space-y-6">
        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-xl flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
                <p class="text-blue-200 text-sm mt-1">BAPS e-Library Management Overview & System Control Center</p>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow border border-slate-200 flex justify-between items-center">
                <div><p class="text-xs font-bold text-slate-500 uppercase">Total Books</p><h3 class="text-3xl font-bold mt-1"><?= $total_books ?></h3></div>
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xl"><i class="fa-solid fa-book"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow border border-slate-200 flex justify-between items-center">
                <div><p class="text-xs font-bold text-slate-500 uppercase">Registered Students</p><h3 class="text-3xl font-bold mt-1"><?= $reg_students ?></h3></div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow border border-slate-200 flex justify-between items-center">
                <div><p class="text-xs font-bold text-slate-500 uppercase">Currently Borrowed</p><h3 class="text-3xl font-bold mt-1"><?= $curr_borrowed ?></h3></div>
                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xl"><i class="fa-solid fa-book-reader"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow border border-slate-200 flex justify-between items-center">
                <div><p class="text-xs font-bold text-slate-500 uppercase">Overdue Books</p><h3 class="text-3xl font-bold text-rose-600 mt-1"><?= $overdue_books ?></h3></div>
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-sm text-slate-500">
        &copy; 2026 BAPS e-Library Management System
    </footer>
</body>
</html>
