<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$stmt = $pdo->query("SELECT a.*, u.username AS artist_name FROM artworks a JOIN users u ON a.artist_id = u.user_id WHERE a.is_active = 1 ORDER BY a.created_at DESC");
$arts = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ArtBid Studio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="wrap">
      <a class="brand" href="index.php">ArtBid Studio</a>
      <div class="navlinks">
        <?php if(is_logged_in()): ?>
          <span style="color:var(--muted);margin-right:12px;">Hi, <?php echo htmlspecialchars($_SESSION['role']); ?></span>
          <?php if(current_user_role() === 'artist'): ?>
            <a href="upload_art.php">Upload Art</a>
          <?php endif; ?>
          <a class="cta" href="logout.php">Logout</a>
        <?php else: ?>
          <a href="register.php">Register</a>
          <a class="cta" href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="container">
    <h1 style="margin-bottom:18px">Featured Gallery</h1>

    <div class="gallery">
      <?php foreach($arts as $a): ?>
        <div class="card">
          <a href="art.php?id=<?php echo $a['art_id']; ?>" style="color:inherit;text-decoration:none">
            <div class="media">
              <img src="<?php echo htmlspecialchars($a['image_path']); ?>" alt="<?php echo htmlspecialchars($a['title']); ?>">
            </div>
            <div class="body">
              <h3><?php echo htmlspecialchars($a['title']); ?></h3>
              <div class="meta">
                <div class="artist">By <?php echo htmlspecialchars($a['artist_name']); ?></div>
                <div class="price">₹ <?php echo number_format($a['current_price'],2); ?></div>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
