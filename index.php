<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BAPS e-Library Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
        <div class="text-center mb-6">
            <img src="logo.jpg" alt="BAPS Logo" class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-amber-400 object-cover shadow" onerror="this.src='https://placehold.co/80x80?text=BAPS'">
            <h2 class="text-2xl font-bold text-slate-900">BAPS e-Library Login</h2>
            <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">Book Access and Processing System</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2.5 rounded-lg mb-4 text-xs font-semibold">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-2.5 rounded-lg mb-4 text-xs font-semibold">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <form action="actions/auth_action.php?action=login" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Username / Student ID</label>
                <input type="text" name="login_user" required placeholder="Enter username or student ID" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="pass_input" name="login_pass" required placeholder="••••••••" class="w-full px-4 py-2.5 pr-10 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    <button type="button" onclick="togglePass()" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                        <i id="eye_icon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#1e293b] text-white py-3 rounded-lg font-semibold hover:bg-slate-800 transition text-sm shadow">Log In</button>

            <div class="text-center pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-600 mb-1">Don't have an account yet?</p>
                <a href="register.php" class="text-blue-600 font-bold text-sm hover:underline">Create Account/Register Here</a>
            </div>
        </form>
    </div>

    <script>
        function togglePass() {
            const pass = document.getElementById('pass_input');
            const eye = document.getElementById('eye_icon');
            if (pass.type === 'password') {
                pass.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                pass.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>