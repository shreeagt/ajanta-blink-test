@extends('layouts.app-master')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <div id="app" class="container mx-auto p-4 md:p-8 max-w-lg mt-10">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-6 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-6 font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-8 rounded-lg shadow-md border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Change Admin Password</h1>
            
            <form action="{{ route('admin.change_password.update') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="new_password" class="block text-gray-700 font-medium mb-2">New Password:</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters.</p>
                </div>

                <div class="mb-8">
                    <label for="new_password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm New Password:</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded transition font-medium">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium shadow-sm">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
@endsection
