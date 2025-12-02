<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT a.*, u.username AS artist_name FROM artworks a JOIN users u ON a.artist_id = u.user_id WHERE a.art_id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) die("Artwork not found.");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($art['title']); ?> - ArtBid Studio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="wrap">
    <a class="brand" href="index.php">ArtBid Studio</a>
    <div class="navlinks">
      <?php if(is_logged_in()): ?>
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

  <div class="detail-grid">

    <div class="preview">
      <img src="<?php echo htmlspecialchars($art['image_path']); ?>" alt="">
    </div>

    <div class="details">
      <h1><?php echo htmlspecialchars($art['title']); ?></h1>
      <div class="artist">By <?php echo htmlspecialchars($art['artist_name']); ?></div>

      <div class="price-box">
        Current Price: ₹ <span id="current-price"><?php echo number_format($art['current_price'], 2); ?></span>
      </div>

      <p style="margin:10px 0;">
        <?php echo nl2br(htmlspecialchars($art['description'])); ?>
      </p>

      <?php if(is_logged_in() && current_user_role() === 'buyer'): ?>
      <div class="panel" style="margin-top:20px;">
        <h3>Place Your Bid</h3>

        <label>Your Bid Amount (₹)</label>
        <input type="number" id="bid-amount" min="<?php echo $art['current_price'] + 1; ?>">

        <label>Signature:</label>
        <canvas id="signature" class="signature-pad"></canvas>
        <button class="btn" id="clear-signature" style="margin-top:6px;">Clear</button>

        <button class="btn full" id="place-bid" data-art="<?php echo $art['art_id']; ?>" style="margin-top:16px;">
          Submit Bid
        </button>

        <div id="bid-status"></div>

      </div>
      <?php endif; ?>

    </div>

  </div>

</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
  startBidPolling(<?php echo $art['art_id']; ?>);
</script>

</body>
</html>
