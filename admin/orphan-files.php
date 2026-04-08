<?php
include 'header.php';
require_once('../mydb_pdo.php');
require_once('../csrf.php');

$users_dir = __DIR__ . '/../users';
$base_url  = 'https://www.hoopcoach.org/playbook/';
$csrf_token = csrf_token();

// --- Handle delete POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['files'])) {
    verify_csrf();

    $files   = $_POST['files'];
    $deleted = 0;
    $errors  = 0;

    foreach ($files as $rel_path) {
        // Sanitize: only allow users/{int}/{filename} pattern, no traversal
        if (!preg_match('#^users/(\d+)/([a-zA-Z0-9_\-]+)$#', $rel_path, $m)) {
            continue;
        }
        $base = __DIR__ . '/../' . $rel_path;

        // Delete .json
        if (file_exists($base . '.json') && unlink($base . '.json')) {
            $deleted++;
        } else {
            $errors++;
        }
        // Delete any .jpeg frames
        $i = 1;
        while (file_exists($base . '_' . $i . '.jpeg')) {
            unlink($base . '_' . $i . '.jpeg');
            $i++;
        }
    }

    echo json_encode(['success' => true, 'deleted' => $deleted, 'errors' => $errors]);
    exit;
}

// --- Scan for orphans ---
$orphans = [];

if (is_dir($users_dir)) {
    $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $conn->exec("set names utf8");

    // Load all known (userid, file) pairs into a lookup set
    $st = $conn->query("SELECT userid, file FROM playdata");
    $known = [];
    while ($row = $st->fetch()) {
        $known[$row['userid'] . '/' . $row['file']] = true;
    }
    $conn = null;

    // Walk users/{userid}/*.json
    foreach (glob($users_dir . '/*', GLOB_ONLYDIR) as $user_dir) {
        $userid = basename($user_dir);
        if (!ctype_digit($userid)) continue;

        foreach (glob($user_dir . '/*.json') as $json_file) {
            $filename = pathinfo($json_file, PATHINFO_FILENAME);
            $key      = $userid . '/' . $filename;

            if (!isset($known[$key])) {
                $size    = filesize($json_file);
                $mtime   = filemtime($json_file);
                // Check for matching jpeg frames
                $frames  = 0;
                $i = 1;
                while (file_exists($user_dir . '/' . $filename . '_' . $i . '.jpeg')) {
                    $frames++;
                    $i++;
                }
                $orphans[] = [
                    'rel_path' => 'users/' . $userid . '/' . $filename,
                    'userid'   => $userid,
                    'filename' => $filename,
                    'size'     => $size,
                    'mtime'    => $mtime,
                    'frames'   => $frames,
                    'thumb'    => $base_url . 'users/' . $userid . '/' . $filename . '_1.jpeg',
                ];
            }
        }
    }
}

$total = count($orphans);
?>
<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <title>Orphan Files — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <style>
        .orphan-card img { height: 100px; object-fit: cover; background: #eee; }
        .orphan-card .card-body { padding: .5rem; font-size: .8rem; }
        .selected-card { outline: 3px solid #dc3545; }
        #sticky-bar { position: sticky; top: 0; z-index: 100; background: #fff;
                      border-bottom: 1px solid #dee2e6; padding: .75rem 0; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-xl navbar-light bg-light mb-3">
    <div class="container">
        <a class="navbar-brand text-secondary" href="/playbook/admin/index.php">admin home</a>
        <span class="navbar-text ml-auto">Orphan File Cleaner</span>
    </div>
</nav>

<main>
<div class="container">

    <div class="row mb-3">
        <div class="col">
            <h4>Orphaned Files</h4>
            <p class="text-muted">These <code>.json</code> files exist on disk but have no matching database entry. They are safe to delete.</p>
        </div>
    </div>

    <!-- Sticky action bar -->
    <div id="sticky-bar">
        <div class="d-flex align-items-center">
            <span class="mr-3"><strong><?= $total ?></strong> orphaned file<?= $total !== 1 ? 's' : '' ?> found</span>
            <button id="btn-select-all" class="btn btn-sm btn-outline-secondary mr-2">Select All</button>
            <button id="btn-deselect-all" class="btn btn-sm btn-outline-secondary mr-2">Deselect All</button>
            <span id="selected-count" class="mr-3 text-muted">0 selected</span>
            <button id="btn-delete" class="btn btn-sm btn-danger" disabled>
                <i class="fa fa-trash"></i> Delete Selected
            </button>
            <span id="delete-status" class="ml-3"></span>
        </div>
    </div>

    <?php if (empty($orphans)): ?>
        <div class="alert alert-success mt-4">No orphaned files found. Everything is clean.</div>
    <?php else: ?>

    <form id="orphan-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        <div class="row mt-3">
            <?php foreach ($orphans as $f): ?>
            <div class="col-6 col-sm-4 col-lg-2 mb-3">
                <div class="card orphan-card" data-path="<?= htmlspecialchars($f['rel_path']) ?>">
                    <img src="<?= htmlspecialchars($f['thumb']) ?>" alt="thumbnail" class="card-img-top"
                         onerror="this.style.display='none'">
                    <div class="card-body">
                        <div class="form-check mb-1">
                            <input class="form-check-input orphan-checkbox" type="checkbox"
                                   name="files[]" value="<?= htmlspecialchars($f['rel_path']) ?>"
                                   id="cb-<?= htmlspecialchars($f['filename']) ?>">
                            <label class="form-check-label font-weight-bold"
                                   for="cb-<?= htmlspecialchars($f['filename']) ?>">
                                <?= htmlspecialchars($f['filename']) ?>
                            </label>
                        </div>
                        <div class="text-muted">
                            User #<?= htmlspecialchars($f['userid']) ?><br>
                            <?= date('M j, Y', $f['mtime']) ?><br>
                            <?= $f['frames'] ?> frame<?= $f['frames'] !== 1 ? 's' : '' ?>
                            &middot; <?= round($f['size'] / 1024, 1) ?>KB
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>

    <?php endif; ?>
</div>
</main>

<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script>
    var selectedPaths = new Set();

    function updateUI() {
        var count = selectedPaths.size;
        $('#selected-count').text(count + ' selected');
        $('#btn-delete').prop('disabled', count === 0);
        $('.orphan-card').each(function() {
            var path = $(this).data('path');
            $(this).toggleClass('selected-card', selectedPaths.has(path));
        });
    }

    $(document).on('change', '.orphan-checkbox', function() {
        var path = $(this).val();
        if (this.checked) selectedPaths.add(path); else selectedPaths.delete(path);
        updateUI();
    });

    $('#btn-select-all').click(function() {
        $('.orphan-checkbox').prop('checked', true).each(function() {
            selectedPaths.add($(this).val());
        });
        updateUI();
    });

    $('#btn-deselect-all').click(function() {
        $('.orphan-checkbox').prop('checked', false);
        selectedPaths.clear();
        updateUI();
    });

    $('#btn-delete').click(function() {
        if (selectedPaths.size === 0) return;
        if (!confirm('Permanently delete ' + selectedPaths.size + ' orphaned file(s)? This cannot be undone.')) return;

        $('#btn-delete').prop('disabled', true).text('Deleting...');

        var paths = Array.from(selectedPaths);
        var csrf  = $('input[name="csrf_token"]').val();

        $.post('orphan-files.php', { files: paths, csrf_token: csrf }, function(data) {
            if (data.success) {
                $('#delete-status').html('<span class="text-success">Deleted ' + data.deleted + ' file(s).</span>');
                paths.forEach(function(path) {
                    $('.orphan-card[data-path="' + path + '"]').closest('.col-6').remove();
                });
                selectedPaths.clear();
                updateUI();
                $('#btn-delete').text('Delete Selected');
            } else {
                $('#delete-status').html('<span class="text-danger">Error. Try again.</span>');
                $('#btn-delete').prop('disabled', false).text('Delete Selected');
            }
        }, 'json').fail(function() {
            $('#delete-status').html('<span class="text-danger">Request failed.</span>');
            $('#btn-delete').prop('disabled', false).text('Delete Selected');
        });
    });
</script>
</body>
</html>
