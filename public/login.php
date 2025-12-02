<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$username || !$password) $errors[] = 'All fields required.';
        else {
            $stmt = $pdo->prepare("SELECT user_id, password_hash, role FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $u = $stmt->fetch();
            if ($u && password_verify($password, $u['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $u['user_id'];
                $_SESSION['role'] = $u['role'];
                header('Location: index.php'); exit;
            } else {
                $errors[] = 'Invalid credentials.';
            }
        }
    }
}
$token = generate_csrf_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - ArtBid Studio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header"><div class="wrap"><a class="brand" href="index.php">ArtBid Studio</a></div></header>
  <main class="container">
    <div class="panel" style="max-width:520px;margin:auto">
      <h2>Login</h2>

      <?php if (!empty($_GET['registered'])): ?>
        <div class="notice success">Registration successful. Please login.</div>
      <?php endif; ?>

      <?php foreach ($errors as $e): ?>
        <div class="notice error"><?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>

      <form method="post" action="login.php">
        <label>Username or Email
          <input name="username" required>
        </label>

        <label>Password
          <input name="password" type="password" required>
        </label>

        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($token); ?>">

        <button class="btn full" type="submit">Login</button>
      </form>

      <p style="margin-top:12px">No account? <a href="register.php">Register</a></p>
    </div>
  </main>
</body>
</html>