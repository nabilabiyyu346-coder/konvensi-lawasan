<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

$pageTitle = 'Login';
$kembaliKe = $_GET['kembali_ke'] ?? 'index.php';

if (pelangganLoggedIn()) {
    header('Location: ' . $kembaliKe);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $kembaliKe = $_POST['kembali_ke'] ?? $kembaliKe;

    $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $pelanggan = $stmt->fetch();

    if ($pelanggan && password_verify($password, $pelanggan['password'])) {
        session_regenerate_id(true);
        $_SESSION['pelanggan_id']   = $pelanggan['id'];
        $_SESSION['pelanggan_nama'] = $pelanggan['nama'];
        header('Location: ' . $kembaliKe);
        exit;
    }

    $error = 'Email atau password salah.';
}

require_once 'includes/header.php';
?>

<main>
  <section class="section section--narrow">
    <div class="auth-card">
      <p class="section__index">Akun</p>
      <h1>Masuk ke akunmu</h1>
      <?php if ($kembaliKe === 'checkout.php'): ?>
        <p class="auth-card__lede">Login dulu untuk melanjutkan checkout.</p>
      <?php endif; ?>

      <?php if ($error): ?><p class="form-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <form method="post" class="auth-form">
        <input type="hidden" name="kembali_ke" value="<?= htmlspecialchars($kembaliKe) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn--solid btn--full">Masuk</button>
      </form>

      <p class="auth-card__switch">Belum punya akun? <a href="daftar.php?kembali_ke=<?= urlencode($kembaliKe) ?>">Daftar di sini</a></p>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
