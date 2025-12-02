<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (current_user_role() !== 'artist') {
    die("Only artists can upload art.");
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = floatval($_POST["price"] ?? 0);

    if (!$title || !$price) {
        $errors[] = "Title and starting price are required.";
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $errors[] = "Image is required.";
    }

    if (!$errors) {
        $uploadDir = "assets/images/uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
            $stmt = $pdo->prepare("
                INSERT INTO artworks (title, description, starting_price, current_price, image_path, artist_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $price, $price, $filePath, current_user_id()]);

            $success = "Artwork uploaded successfully!";
        } else {
            $errors[] = "Failed to upload image.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Artwork - ArtBid Studio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="wrap">
    <a class="brand" href="index.php">ArtBid Studio</a>
    <div class="navlinks">
      <a href="index.php">Home</a>
      <a class="cta" href="logout.php">Logout</a>
    </div>
  </div>
</header>

<main class="container">
  <div class="panel" style="max-width:600px;margin:auto;">
    <h2>Upload New Artwork</h2>

    <?php foreach ($errors as $e): ?>
      <div class="notice error"><?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
      <div class="notice success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label>Title</label>
      <input type="text" name="title" required>

      <label>Description</label>
      <textarea name="description"></textarea>

      <label>Starting Price (₹)</label>
      <input type="number" name="price" required>

      <label>Upload Image</label>
      <input type="file" name="image" required>

      <button class="btn full" type="submit">Upload Artwork</button>
    </form>
  </div>
</main>

</body>
</html>