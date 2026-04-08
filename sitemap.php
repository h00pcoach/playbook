<?php
require_once('mydb_pdo.php');
header('Content-Type: application/xml; charset=utf-8');

$base = 'https://www.hoopcoach.org/playbook';

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

// Fetch all public plays with enough movements
$sql = "SELECT id, name, created_on FROM playdata
        WHERE private = 0
          AND copied = 0
          AND (CHAR_LENGTH(COALESCE(movements,'')) - CHAR_LENGTH(REPLACE(COALESCE(movements,''), CHAR(96), '')) + 1) >= 5
        ORDER BY created_on DESC";

$st = $conn->prepare($sql);
$st->execute();
$plays = $st->fetchAll();
$conn  = null;

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Static pages -->
    <url>
        <loc><?= $base ?>/basketball-plays.php</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= $base ?>/play.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $base ?>/register.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Public plays -->
    <?php foreach ($plays as $play): ?>
    <url>
        <loc><?= $base ?>/play.php?id=<?= (int)$play['id'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($play['created_on'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

</urlset>
