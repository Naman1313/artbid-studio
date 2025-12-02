<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
header("Content-Type: application/json");

if (!is_logged_in() || current_user_role() !== 'buyer') {
    echo json_encode(["status" => "error", "msg" => "Not authorized"]);
    exit;
}

$art_id = intval($_POST['art_id'] ?? 0);
$amount = floatval($_POST['amount'] ?? 0);
$signature = $_POST['signature'] ?? "";

if ($amount <= 0 || !$art_id || !$signature) {
    echo json_encode(["status" => "error", "msg" => "Invalid input"]);
    exit;
}

$stmt = $pdo->prepare("SELECT current_price FROM artworks WHERE art_id = ?");
$stmt->execute([$art_id]);
$art = $stmt->fetch();

if (!$art) {
    echo json_encode(["status" => "error", "msg" => "Artwork not found"]);
    exit;
}

if ($amount <= $art['current_price']) {
    echo json_encode(["status" => "error", "msg" => "Bid must be higher"]);
    exit;
}

$ins = $pdo->prepare("INSERT INTO bids (art_id, user_id, amount, signature_data) VALUES (?, ?, ?, ?)");
$ins->execute([$art_id, current_user_id(), $amount, $signature]);

$upd = $pdo->prepare("UPDATE artworks SET current_price = ? WHERE art_id = ?");
$upd->execute([$amount, $art_id]);

echo json_encode(["status" => "success", "msg" => "Bid placed"]);
?>
