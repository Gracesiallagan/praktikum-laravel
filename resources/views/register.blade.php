<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96">
        <h1 class="text-2xl font-bold text-center mb-6">Register Mahasiswa</h1>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Mahasiswa</label>
                <input type="text" name="nama_mahasiswa" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Username</label>
                <input type="text" name="username" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" class="w-full border p-2 rounded" required>
            </div>

            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Daftar</button>
        </form>

        <p class="text-center text-sm mt-4">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-semibold">Login</a>
        </p>
    </div>
</body>
</html>
