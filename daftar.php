<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

$pageTitle = 'Daftar';
$kembaliKe = $_GET['kembali_ke'] ?? 'index.php';

if (pelangganLoggedIn()) {
    header('Location: ' . $kembaliKe);
    exit;
}

$error = '';
$old = ['nama' => '', 'email' => '', 'telepon' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telepon  = trim($_POST['telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $ulangi   = $_POST['ulangi_password'] ?? '';
    $old = ['nama' => $nama, 'email' => $email, 'telepon' => $telepon];

    if ($nama === '') {
        $error = 'Nama wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } elseif ($telepon === '') {
        $error = 'Nomor WhatsApp wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $ulangi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM pelanggan WHERE email = :email");
        $cek->execute([':email' => $email]);
        if ($cek->fetch()) {
            $error = 'Email ini sudah terdaftar. Silakan login.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO pelanggan (nama, email, password, telepon)
                VALUES (:nama, :email, :password, :telepon)
            ");
            $stmt->execute([
                ':nama'     => $nama,
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':telepon'  => $telepon,
            ]);

            session_regenerate_id(true);
            $_SESSION['pelanggan_id']   = $pdo->lastInsertId();
            $_SESSION['pelanggan_nama'] = $nama;

            header('Location: ' . $kembaliKe);
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<main>
  <section class="section section--narrow">
    <div class="auth-card">
      <p class="section__index">Akun</p>
      <h1>Buat akun baru</h1>
      <p class="auth-card__lede">Perlu akun untuk checkout, supaya pesananmu bisa dilacak.</p>

      <?php if ($error): ?><p class="form-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <form method="post" class="auth-form">
        <label for="nama">Nama lengkap</label>
        <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($old['nama']) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($old['email']) ?>">

        <label for="telepon">No. WhatsApp</label>
        <input type="tel" id="telepon" name="telepon" required value="<?= htmlspecialchars($old['telepon']) ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6">

        <label for="ulangi_password">Ulangi password</label>
        <input type="password" id="ulangi_password" name="ulangi_password" required minlength="6">

        <button type="submit" class="btn btn--solid btn--full">Daftar</button>
      </form>

      <p class="auth-card__switch">Sudah punya akun? <a href="login.php?kembali_ke=<?= urlencode($kembaliKe) ?>">Login di sini</a></p>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
