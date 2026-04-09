<?php
require_once('mydb_pdo.php');
session_start();

if (!isset($_GET['id'])) {
    header('Location: basketball-plays.php');
    exit;
}

// Pro users only
if (!isset($_SESSION['user_id'])) {
    header('Location: play.php?id=' . (int)$_GET['id']);
    exit;
}

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");
$stUser = $conn->prepare("SELECT paid FROM users WHERE id = :id LIMIT 1");
$stUser->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stUser->execute();
$user = $stUser->fetch();

if (!$user || $user['paid'] != 1) {
    header('Location: play.php?id=' . (int)$_GET['id']);
    exit;
}

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "SELECT playdata.id, playdata.name, playdata.file, playdata.userid,
               playdata.movements, playdata.tags, playdata.comments,
               category.name AS cat, users.name AS coach
        FROM playdata
        JOIN users    ON users.id    = playdata.userid
        JOIN category ON category.id = playdata.catid
        WHERE playdata.id = :id AND playdata.private = 0";

$st = $conn->prepare($sql);
$st->bindValue(':id', (int)$_GET['id'], PDO::PARAM_INT);
$st->execute();
$play = $st->fetch();
$conn = null;

if (!$play) {
    header('Location: basketball-plays.php');
    exit;
}

// Collect all frame images that exist
$base   = 'users/' . $play['userid'] . '/' . $play['file'];
$frames = [];
$i = 1;
while (file_exists($base . '_' . $i . '.jpeg') && $i <= 20) {
    $frames[] = 'https://www.hoopcoach.org/playbook/' . $base . '_' . $i . '.jpeg';
    $i++;
}

$title = htmlspecialchars($play['name'], ENT_QUOTES, 'UTF-8');
$coach = htmlspecialchars($play['coach'], ENT_QUOTES, 'UTF-8');
$cat   = htmlspecialchars($play['cat'], ENT_QUOTES, 'UTF-8');
$tags  = htmlspecialchars($play['tags'], ENT_QUOTES, 'UTF-8');
$notes = htmlspecialchars($play['comments'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> — HoopCoach Playbook</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #222;
            background: #fff;
            padding: 20px;
        }

        #print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        #print-header h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        #print-header .meta {
            font-size: 12px;
            color: #555;
            line-height: 1.6;
        }

        #print-header .logo {
            font-size: 11px;
            color: #888;
            text-align: right;
        }

        .frames {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .frame {
            text-align: center;
        }

        .frame img {
            display: block;
            border: 1px solid #ccc;
            max-width: 100%;
        }

        .frame .label {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        .notes-section {
            border-top: 1px solid #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }

        .notes-section h3 {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .notes-lines {
            border-top: 1px solid #ddd;
            margin-top: 30px;
        }

        .notes-lines div {
            border-bottom: 1px solid #ddd;
            height: 24px;
        }

        #screen-controls {
            margin-bottom: 16px;
        }

        #screen-controls button {
            padding: 8px 20px;
            margin-right: 8px;
            font-size: 14px;
            cursor: pointer;
            background: #0077cc;
            color: white;
            border: none;
            border-radius: 4px;
        }

        #screen-controls button.secondary {
            background: #eee;
            color: #333;
        }

        @media print {
            #screen-controls { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div id="screen-controls">
    <button onclick="window.print()">&#128438; Print / Save as PDF</button>
    <button class="secondary" onclick="window.close()">Close</button>
</div>

<div id="print-header">
    <div>
        <h1><?= $title ?></h1>
        <div class="meta">
            <strong>Coach:</strong> <?= $coach ?> &nbsp;|&nbsp;
            <strong>Category:</strong> <?= $cat ?>
            <?php if ($tags): ?>
                &nbsp;|&nbsp; <strong>Tags:</strong> <?= $tags ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="logo">
        HoopCoach Playbook<br>
        hoopcoach.org
    </div>
</div>

<?php if (!empty($frames)): ?>
<div class="frames">
    <?php foreach ($frames as $idx => $src): ?>
    <div class="frame">
        <img src="<?= $src ?>" alt="Frame <?= $idx + 1 ?>" width="<?= count($frames) === 1 ? 600 : 280 ?>">
        <div class="label">Frame <?= $idx + 1 ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($notes): ?>
<div class="notes-section">
    <h3>Notes</h3>
    <p><?= nl2br($notes) ?></p>
</div>
<?php else: ?>
<div class="notes-section">
    <h3>Notes</h3>
    <div class="notes-lines">
        <div></div><div></div><div></div><div></div><div></div>
    </div>
</div>
<?php endif; ?>

<script>
    // Auto-trigger print dialog when page loads
    window.addEventListener('load', function() {
        // Small delay so images have time to render
        setTimeout(function() { window.print(); }, 800);
    });
</script>
</body>
</html>
