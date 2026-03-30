<?php
/**
 * TRACKCOOP - Sector Migration Script
 * Run this ONCE to populate the sectors table from existing users.sector data.
 * Delete this file after running!
 */
include('../auth/db_connect.php');

// ── Step 1: Create sectors table if not exists ─────────────────────────────
$create = $conn->query("
    CREATE TABLE IF NOT EXISTS `sectors` (
        `id`          INT(11) NOT NULL AUTO_INCREMENT,
        `name`        VARCHAR(100) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `chairperson` VARCHAR(150) DEFAULT NULL,
        `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ── Step 2: Fetch distinct sectors from users table ───────────────────────
$result = $conn->query("
    SELECT DISTINCT sector 
    FROM users 
    WHERE sector IS NOT NULL AND TRIM(sector) != ''
    ORDER BY sector ASC
");

$inserted = 0;
$skipped  = 0;
$errors   = [];
$list     = [];

if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("INSERT IGNORE INTO sectors (name) VALUES (?)");
    while ($row = $result->fetch_assoc()) {
        $name = trim($row['sector']);
        $list[] = $name;
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) $inserted++;
            else $skipped++;
        } else {
            $errors[] = $name;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sector Migration | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); max-width: 550px; width: 100%; overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #20a060, #1a8548); padding: 32px; text-align: center; }
        .card-body { padding: 36px; }
        .result-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 12px; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600; }
        .result-ok  { background: #eefdf5; color: #27ae60; }
        .result-skip{ background: #f1f5f9; color: #64748b; }
        .result-err { background: #fee2e2; color: #ef4444; }
        .stat-box { background: #f8fafc; border-radius: 14px; padding: 18px; text-align: center; border: 1px solid #e2e8f0; }
        .stat-num { font-size: 2rem; font-weight: 800; line-height: 1; }
        .btn-go { background: #20a060; color: white; border: none; border-radius: 14px; padding: 14px 28px; font-weight: 700; font-size: 1rem; width: 100%; text-decoration: none; display: block; text-align: center; transition: 0.3s; }
        .btn-go:hover { background: #1a8548; color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32,160,96,0.3); }
        .warning-box { background: #fff9e6; border: 1px solid #fcd34d; border-radius: 12px; padding: 14px 18px; font-size: 0.85rem; color: #92400e; margin-top: 16px; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <i class="bi bi-diagram-3-fill text-white" style="font-size:2.5rem;"></i>
        <h4 class="text-white fw-800 mt-3 mb-1">Sector Migration</h4>
        <p class="text-white mb-0" style="opacity:0.85;font-size:0.9rem;">Populating sectors from users table</p>
    </div>
    <div class="card-body">

        <?php if (!$create): ?>
        <div class="alert alert-danger rounded-4 fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Failed to create sectors table: <?php echo $conn->error; ?></div>
        <?php else: ?>

        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num text-success"><?php echo $inserted; ?></div>
                    <small class="text-muted fw-600">Inserted</small>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num text-secondary"><?php echo $skipped; ?></div>
                    <small class="text-muted fw-600">Skipped</small>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num text-danger"><?php echo count($errors); ?></div>
                    <small class="text-muted fw-600">Errors</small>
                </div>
            </div>
        </div>

        <!-- Sector List -->
        <?php if (empty($list)): ?>
        <div class="result-item result-skip"><i class="bi bi-info-circle"></i> No sector data found in users table.</div>
        <?php else: ?>
        <p class="fw-700 mb-2" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">Sectors Processed</p>
        <?php foreach ($list as $s): 
            $cls = in_array($s, $errors) ? 'result-err' : ($inserted > 0 && !in_array($s, $errors) ? 'result-ok' : 'result-skip');
            $ico = in_array($s, $errors) ? 'bi-x-circle-fill' : 'bi-check-circle-fill';
        ?>
        <div class="result-item <?php echo $cls; ?>">
            <i class="bi <?php echo $ico; ?>"></i>
            <?php echo htmlspecialchars($s); ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- Go to Sectors -->
        <a href="sectors.php" class="btn-go mt-4">
            <i class="bi bi-arrow-right-circle me-2"></i>Go to Sector Management
        </a>

        <div class="warning-box">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Important:</strong> Delete this file (<code>migrate_sectors.php</code>) after running it for security.
        </div>

        <?php endif; ?>
    </div>
</div>
</body>
</html>
