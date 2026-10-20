<!-- register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - BAPS e-Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
        <div class="text-center mb-6">
            <img src="logo.jpg" alt="BAPS Logo" class="w-16 h-16 rounded-full mx-auto mb-2 border border-amber-400 object-cover shadow" onerror="this.src='https://placehold.co/60x60?text=BAPS'">
            <h2 class="text-2xl font-bold text-slate-900">Student Registration</h2>
            <p class="text-xs text-slate-500 mt-1">Register to generate your unique student barcode</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2.5 rounded-lg mb-4 text-xs font-semibold">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="actions/auth_action.php?action=register" method="POST" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Full Name</label>
                <input type="text" name="reg_name" required placeholder="Juan Dela Cruz" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Student ID Number</label>
                <input type="text" name="reg_id" required placeholder="2026-00001" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Course & Year</label>
                <input type="text" name="reg_course" required placeholder="BSIT - 2nd Year" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Username</label>
                <input type="text" name="reg_user" required placeholder="juandelacruz" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="reg_pass" name="reg_pass" required placeholder="••••••••" class="w-full px-4 py-2 pr-10 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 text-sm">
                    <button type="button" onclick="toggleRegPass()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                        <i id="reg_eye" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#1e293b] text-white py-3 rounded-lg font-semibold hover:bg-slate-800 transition text-sm shadow mt-2">Register Account</button>

            <div class="text-center pt-3 border-t border-slate-100">
                <a href="index.php" class="text-blue-600 font-bold text-sm hover:underline">Already have an account? Log In here</a>
            </div>
        </form>
    </div>

    <script>
        function toggleRegPass() {
            const pass = document.getElementById('reg_pass');
            const eye = document.getElementById('reg_eye');
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