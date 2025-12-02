<?php
require_once __DIR__ . '/../includes/db.php';

header("Content-Type: text/xml");

$art_id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT current_price FROM artworks WHERE art_id = ?");
$stmt->execute([$art_id]);
$row = $stmt->fetch();

echo "<?xml version=\"1.0\"?>\n";
?>
<bid>
  <current><?php echo $row ? $row['current_price'] : 0; ?></current>
</bid>
