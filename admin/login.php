<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Kalau sudah login, langsung ke dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $admin['username'];
        header('Location: index.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — Batik Nusantara</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body admin-body--center">

<form class="login-card" method="post" novalidate>
  <p class="login-card__eyebrow">Batik Nusantara</p>
  <h1>Login Admin</h1>

  <?php if ($error): ?>
    <p class="form-error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <label for="username">Username</label>
  <input type="text" id="username" name="username" required autofocus>

  <label for="password">Password</label>
  <input type="password" id="password" name="password" required>

  <button type="submit" class="btn btn--solid btn--full">Masuk</button>

  <p class="login-card__hint">Demo: admin / admin123</p>
  <a href="../index.php" class="back-link">&larr; Kembali ke toko</a>
</form>

</body>
</html>
