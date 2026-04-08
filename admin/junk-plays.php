<?php
include 'header.php';
require_once('../mydb_pdo.php');
require_once('../csrf.php');

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

// --- Configurable thresholds (from GET params so admin can tune) ---
$max_movements = isset($_GET['max_movements']) ? (int)$_GET['max_movements'] : 2;
$min_age_days  = isset($_GET['min_age_days'])  ? (int)$_GET['min_age_days']  : 7;
$require_default_name = isset($_GET['default_name']) ? (bool)$_GET['default_name'] : false;

// Count backtick-separated movements: "a`b`c" = 3 movements
// A play with zero movements stored as '' counts as 1 with this formula, so we cap at max_movements
$movement_expr = "(CHAR_LENGTH(COALESCE(movements,'')) - CHAR_LENGTH(REPLACE(COALESCE(movements,''), CHAR(96), '')) + 1)";

$where = "WHERE $movement_expr <= :max_movements
      AND playdata.created_on < DATE_SUB(NOW(), INTERVAL :min_age_days DAY)
      AND playdata.thumbsup = 0
      AND playdata.thumbsdown = 0
      AND COALESCE(playdata.copied, 0) = 0";

if ($require_default_name) {
    $where .= " AND playdata.name = 'customplaybook'";
}

$count_sql = "SELECT COUNT(*) FROM playdata JOIN users ON users.id = playdata.userid $where";
$data_sql  = "SELECT playdata.id, playdata.name, playdata.userid, playdata.file,
                     playdata.movements, playdata.created_on, playdata.private,
                     $movement_expr AS movement_count,
                     users.name AS user_name, users.email AS user_email
              FROM playdata
              JOIN users ON users.id = playdata.userid
              $where
              ORDER BY playdata.created_on ASC";

$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 48;
$offset   = ($page - 1) * $per_page;

$stCount = $conn->prepare($count_sql);
$stCount->bindValue(':max_movements', $max_movements, PDO::PARAM_INT);
$stCount->bindValue(':min_age_days',  $min_age_days,  PDO::PARAM_INT);
$stCount->execute();
$total      = $stCount->fetchColumn();
$page_count = (int)ceil($total / $per_page);

// Embed limit/offset directly — PDO bound params for LIMIT can fail on some hosts
$data_sql .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;
$st = $conn->prepare($data_sql);
$st->bindValue(':max_movements', $max_movements, PDO::PARAM_INT);
$st->bindValue(':min_age_days',  $min_age_days,  PDO::PARAM_INT);
$st->execute();
$plays = $st->fetchAll();
$conn  = null;

$base_url    = 'https://www.hoopcoach.org/playbook/';
$csrf_token  = csrf_token();
?>
<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Junk Plays — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <style>
        .junk-card img { height: 120px; object-fit: cover; background: #eee; }
        .junk-card .card-body { padding: .5rem; font-size: .8rem; }
        .selected-card { outline: 3px solid #dc3545; }
        #sticky-bar { position: sticky; top: 0; z-index: 100; background: #fff;
                      border-bottom: 1px solid #dee2e6; padding: .75rem 0; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-xl navbar-light bg-light mb-3">
    <div class="container">
        <a class="navbar-brand text-secondary" href="/playbook/admin/index.php">admin home</a>
        <span class="navbar-text ml-auto">Junk Play Cleaner</span>
    </div>
</nav>

<main>
<div class="container">

    <!-- Filter bar -->
    <div class="card mb-4">
        <div class="card-header"><strong>Filter criteria</strong> <small class="text-muted">— tune these to preview before deleting</small></div>
        <div class="card-body">
            <form method="GET" class="form-inline">
                <label class="mr-2">Max movements</label>
                <input type="number" name="max_movements" class="form-control form-control-sm mr-3"
                       value="<?= $max_movements ?>" min="1" max="20" style="width:70px">

                <label class="mr-2">Older than (days)</label>
                <input type="number" name="min_age_days" class="form-control form-control-sm mr-3"
                       value="<?= $min_age_days ?>" min="1" style="width:70px">

                <div class="form-check mr-3">
                    <input class="form-check-input" type="checkbox" name="default_name" value="1" id="chk-name"
                           <?= $require_default_name ? 'checked' : '' ?>>
                    <label class="form-check-label" for="chk-name">Default name only ("customplaybook")</label>
                </div>

                <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
            </form>
        </div>
    </div>

    <!-- Sticky action bar -->
    <div id="sticky-bar">
        <div class="d-flex align-items-center">
            <span class="mr-3"><strong><?= $total ?></strong> found &mdash; <strong><?= count($plays) ?></strong> loaded this page</span>
            <button id="btn-select-all" class="btn btn-sm btn-outline-secondary mr-2">Select All</button>
            <button id="btn-deselect-all" class="btn btn-sm btn-outline-secondary mr-2">Deselect All</button>
            <span id="selected-count" class="mr-3 text-muted">0 selected</span>
            <button id="btn-delete" class="btn btn-sm btn-danger" disabled>
                <i class="fa fa-trash"></i> Delete Selected
            </button>
            <span id="delete-status" class="ml-3"></span>
        </div>
    </div>

    <?php if (empty($plays)): ?>
        <div class="alert alert-success mt-4">No plays match these criteria.</div>
    <?php else: ?>

    <!-- Play grid -->
    <form id="junk-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        <div class="row mt-3">
            <?php foreach ($plays as $play): ?>
            <div class="col-6 col-sm-4 col-lg-2 mb-3">
                <div class="card junk-card" data-id="<?= $play['id'] ?>">
                    <img src="<?= htmlspecialchars($base_url . 'users/' . $play['userid'] . '/' . $play['file'] . '_1.jpeg') ?>"
                         alt="play thumbnail" class="card-img-top">
                    <div class="card-body">
                        <div class="form-check mb-1">
                            <input class="form-check-input play-checkbox" type="checkbox"
                                   name="ids[]" value="<?= $play['id'] ?>" id="cb-<?= $play['id'] ?>">
                            <label class="form-check-label font-weight-bold" for="cb-<?= $play['id'] ?>">
                                <?= htmlspecialchars($play['name']) ?>
                            </label>
                        </div>
                        <div class="text-muted">
                            <?= (int)$play['movement_count'] ?> movement<?= $play['movement_count'] == 1 ? '' : 's' ?><br>
                            <?= htmlspecialchars($play['user_name']) ?><br>
                            <?= date('M j, Y', $play['created_on']) ?>
                            <?php if ($play['private']): ?>
                                <span class="badge badge-secondary">private</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>

    <?php endif; ?>

    <?php if ($page_count > 1): ?>
    <?php
        $qs = http_build_query(array_filter([
            'max_movements' => $max_movements,
            'min_age_days'  => $min_age_days,
            'default_name'  => $require_default_name ? 1 : null,
        ]));
    ?>
    <div class="d-flex justify-content-center mt-4 mb-4">
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?= $qs ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 3); $i <= min($page_count, $page + 3); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $qs ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $page_count): ?>
                    <li class="page-item"><a class="page-link" href="?<?= $qs ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

</div>
</main>

<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script>
    var selectedIds = new Set();

    function updateUI() {
        var count = selectedIds.size;
        $('#selected-count').text(count + ' selected');
        $('#btn-delete').prop('disabled', count === 0);
        $('.junk-card').each(function() {
            var id = $(this).data('id').toString();
            $(this).toggleClass('selected-card', selectedIds.has(id));
        });
    }

    $(document).on('change', '.play-checkbox', function() {
        var id = $(this).val();
        if (this.checked) selectedIds.add(id); else selectedIds.delete(id);
        updateUI();
    });

    $('#btn-select-all').click(function() {
        $('.play-checkbox').prop('checked', true).each(function() {
            selectedIds.add($(this).val());
        });
        updateUI();
    });

    $('#btn-deselect-all').click(function() {
        $('.play-checkbox').prop('checked', false);
        selectedIds.clear();
        updateUI();
    });

    $('#btn-delete').click(function() {
        if (selectedIds.size === 0) return;
        if (!confirm('Permanently delete ' + selectedIds.size + ' play(s)? This cannot be undone.')) return;

        $('#btn-delete').prop('disabled', true).text('Deleting...');
        $('#delete-status').text('');

        var ids = Array.from(selectedIds);
        var csrf = $('input[name="csrf_token"]').val();

        $.post('bulk-delete-plays.php', { ids: ids, csrf_token: csrf }, function(data) {
            if (data.success) {
                $('#delete-status').html('<span class="text-success">Deleted ' + data.deleted + ' play(s).</span>');
                // Remove deleted cards from the DOM
                ids.forEach(function(id) {
                    $('.junk-card[data-id="' + id + '"]').closest('.col-6').remove();
                });
                selectedIds.clear();
                updateUI();
                $('#btn-delete').text('Delete Selected');
            } else {
                $('#delete-status').html('<span class="text-danger">' + data.error + '</span>');
                $('#btn-delete').prop('disabled', false).text('Delete Selected');
            }
        }, 'json').fail(function() {
            $('#delete-status').html('<span class="text-danger">Request failed. Try again.</span>');
            $('#btn-delete').prop('disabled', false).text('Delete Selected');
        });
    });
</script>
</body>
</html>
