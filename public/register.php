<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? 'buyer', ['artist','buyer']) ? $_POST['role'] : 'buyer';

    if (!$username || !$email || !$password) $errors[] = 'All fields required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) $errors[] = 'Username or email already taken.';
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $ins->execute([$username, $email, $hash, $role]);
            header('Location: login.php?registered=1'); exit;
        }
    }
}
$token = generate_csrf_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register - ArtBid Studio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header"><div class="wrap"><a class="brand" href="index.php">ArtBid Studio</a></div></header>
  <main class="container">
    <div class="panel" style="max-width:520px;margin:auto">
      <h2>Create account</h2>

      <?php foreach ($errors as $e): ?>
        <div class="notice error"><?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>

      <form method="post" action="register.php">
        <label>Username
          <input name="username" required>
        </label>

        <label>Email
          <input name="email" type="email" required>
        </label>

        <label>Password
          <input name="password" type="password" required>
        </label>

        <label>Role
          <select name="role">
            <option value="buyer">Buyer</option>
            <option value="artist">Artist</option>
          </select>
        </label>

        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($token); ?>">

        <button class="btn full" type="submit">Register</button>
      </form>

      <p style="margin-top:12px">Already have account? <a href="login.php">Login</a></p>
    </div>
  </main>
</body>
</html>