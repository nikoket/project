<?php
// students.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['role']) \vert{}\vert{}$_SESSION['role'] !== 'librarian') {
    header("Location: index.php");
    exit();
}

$students =$pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students & Barcode Lookup - BAPS e-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <main class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow border">
            <div>
                <h2 class="text-2xl font-bold">Registered Students & Barcodes</h2>
                <p class="text-sm text-slate-500">Search student record and active loan status via barcode scanner</p>
            </div>
            <div class="flex space-x-2">
                <input type="text" id="barcodeInput" placeholder="Scan/Enter Barcode (e.g., BC-12345678)" class="px-4 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-blue-600">
                <button onclick="searchBarcode()" class="bg-[#1e293b] text-white px-4 py-2 rounded-xl text-sm font-semibold">Search Barcode</button>
            </div>
        </div>

        <div id="barcodeResult"></div>

        <div class="bg-white rounded-2xl shadow border overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-100 text-xs uppercase font-semibold text-slate-600">
                    <tr>
                        <th class="p-4">Student Name</th>
                        <th class="p-4">Student ID</th>
                        <th class="p-4">Course</th>
                        <th class="p-4">Unique Barcode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    <?php if (empty($students)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-slate-500">No registered students found.</td></tr>
                    <?php else: foreach ($students as$s): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-bold text-slate-900"><?= htmlspecialchars($s['name']) ?></td>
                            <td class="p-4 font-mono text-xs"><?= htmlspecialchars($s['student_id']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($s['course']) ?></td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded bg-amber-100 text-amber-900 font-mono font-bold text-xs border border-amber-300"><?= htmlspecialchars($s['barcode']) ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function searchBarcode() {
            const query = document.getElementById('barcodeInput').value.trim();
            if (!query) return;

            fetch(`actions/student_action.php?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    const box = document.getElementById('barcodeResult');
                    if (!data.found) {
                        box.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm">No student found matching Barcode/ID: <b>${query}</b></div>`;
                        return;
                    }

                    box.innerHTML = `
                        <div class="bg-white border border-blue-200 rounded-2xl p-6 shadow space-y-3">
                            <h3 class="font-bold text-lg text-slate-900">${data.student.name}</h3>
                            <p class="text-xs text-slate-500">ID: ${data.student.student_id} | Course: ${data.student.course} | Barcode: <span class="font-mono font-bold text-amber-800">${data.student.barcode}</span></p>
                            <h4 class="font-bold text-sm text-slate-800 pt-2">Active Loans (${data.loans.length}):</h4>
                            ${data.loans.length === 0 ? '<p class="text-xs text-slate-500">No active loans.</p>' : data.loans.map(l => `
                                <div class="bg-slate-50 p-2.5 rounded border text-xs flex justify-between">
                                    <span><b>${l.title}</b></span>
                                    <span class="font-bold text-blue-600">${l.status}</span>
                                </div>
                            `).join('')}
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
