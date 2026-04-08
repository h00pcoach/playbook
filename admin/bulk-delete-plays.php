<?php
include 'header.php';
require_once('../mydb_pdo.php');
require_once('../csrf.php');

verify_csrf();

$ids = $_POST['ids'] ?? [];

if (empty($ids) || !is_array($ids)) {
    echo json_encode(['error' => 'No plays selected.']);
    exit;
}

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$deleted = 0;
$errors  = 0;

foreach ($ids as $id) {
    $id = (int)$id;
    if ($id <= 0) continue;

    $st = $conn->prepare("SELECT userid, file, movements FROM playdata WHERE id = :id");
    $st->bindValue(":id", $id, PDO::PARAM_INT);
    $st->execute();
    $play = $st->fetch();

    if (!$play) continue;

    // Delete image files
    $base = "../users/" . $play['userid'] . "/" . $play['file'];
    $i = 1;
    while (file_exists($base . '_' . $i . '.jpeg')) {
        unlink($base . '_' . $i . '.jpeg');
        $i++;
    }
    // Delete JSON file
    if (file_exists($base . '.json')) {
        unlink($base . '.json');
    }

    // Delete DB row (admin — no userid restriction)
    $st = $conn->prepare("DELETE FROM playdata WHERE id = :id");
    $st->bindValue(":id", $id, PDO::PARAM_INT);
    if ($st->execute()) {
        $deleted++;
    } else {
        $errors++;
    }
}

$conn = null;
echo json_encode(['success' => true, 'deleted' => $deleted, 'errors' => $errors]);
