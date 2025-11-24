<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Apply Inter font globally and handle input focus visibility */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Custom focus ring color for better aesthetics */
        .input-focus:focus {
            --tw-ring-color: #8b5cf6; /* violet-500 */
        }
    </style>
    <!-- Tailwind Configuration for custom color palette and font -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#6366f1', // Indigo-500 for main actions
                        'secondary': '#8b5cf6', // Violet-500 for accents/hover
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <!-- Login Card: Slightly larger, stronger shadow, rounded corners -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl shadow-2xl w-full max-w-md border border-gray-100 transform transition duration-500 hover:shadow-xl">
        <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-2">Login</h2>
        <p class="text-center text-gray-500 mb-8">Access your personalized dashboard.</p>

        <!-- Error Message Block: More visually distinct -->
        @if($errors->any())
            <div class="bg-red-50 text-red-700 font-medium p-4 rounded-xl mb-6 border border-red-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-6">
            @csrf
            
            <!-- Username Field -->
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required
                    class="input-focus block w-full px-5 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-150"
                    placeholder="Enter your username">
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="input-focus block w-full px-5 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition duration-150"
                    placeholder="••••••••">
            </div>

            <!-- Submit Button: Gradient color, strong hover effect -->
            <button type="submit"
                class="w-full text-white font-extrabold py-3 px-4 rounded-xl bg-primary hover:bg-secondary transition duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-secondary focus:ring-opacity-50">
                Sign In
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="#" class="text-sm text-gray-500 hover:text-primary transition duration-150">Forgot Password?</a>
        </div>
    </div>

</body>
</html>
