<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #eef2f7; height: 100vh; display: flex; align-items: center; justify-content: center; }
    .form-box { background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); width: 400px; }
    h3 { text-align: center; color: #0d6efd; margin-bottom: 25px; }
  </style>
</head>
<body>
  <div class="form-box">
    <h3>Login</h3>
    <form action="{{ route('user.login.post') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <p class="text-center mt-3">Don’t have an account? <a href="{{ route('user.register') }}">Register</a></p>
    </form>
  </div>
</body>
</html>
