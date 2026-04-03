<?php
include '../db.php';

$purchases = [];
$purchases_result = $conn->query("
    SELECT p.purchase_id, p.purchase_date, v.make, v.model, v.year
    FROM purchase p
    INNER JOIN vehicle v ON p.vehicle_id = v.vehicle_id
    ORDER BY p.purchase_id ASC
");
if ($purchases_result) {
    while ($row = $purchases_result->fetch_assoc()) {
        $purchases[] = $row;
    }
}

$errors = [];
$purchase_id = '';
$problem_description = '';
$est_repair_cost = '';
$actual_cost = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $purchase_id = trim($_POST['purchase_id'] ?? '');
    $problem_description = trim($_POST['problem_description'] ?? '');
    $est_repair_cost = trim($_POST['est_repair_cost'] ?? '');
    $actual_cost = trim($_POST['actual_cost'] ?? '');

    if ($purchase_id === '' || !ctype_digit($purchase_id) || (int)$purchase_id <= 0) {
        $errors[] = "Purchase is required.";
    }

    if ($problem_description === '') {
        $errors[] = "Problem description is required.";
    }

    if ($est_repair_cost === '') {
        $errors[] = "Estimated repair cost is required.";
    } elseif (!is_numeric($est_repair_cost)) {
        $errors[] = "Estimated repair cost must be numeric.";
    }

    if ($actual_cost === '') {
        $errors[] = "Actual cost is required.";
    } elseif (!is_numeric($actual_cost)) {
        $errors[] = "Actual cost must be numeric.";
    }

    if (empty($errors)) {
        $purchase_id_int = (int)$purchase_id;
        $chk = $conn->prepare("SELECT 1 FROM purchase WHERE purchase_id = ? LIMIT 1");
        $chk->bind_param("i", $purchase_id_int);
        $chk->execute();
        $chk_res = $chk->get_result();
        if (!$chk_res || $chk_res->num_rows === 0) {
            $errors[] = "That purchase does not exist. Add a purchase first or choose one from the list.";
        }
        $chk->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO repair (purchase_id, problem_description, est_repair_cost, actual_cost)
            VALUES (?, ?, ?, ?)
        ");

        $est_repair_cost_float = (float)$est_repair_cost;
        $actual_cost_float = (float)$actual_cost;

        $stmt->bind_param("isdd", $purchase_id_int, $problem_description, $est_repair_cost_float, $actual_cost_float);

        if ($stmt->execute()) {
            header("Location: repairs.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add Repair</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<?php if (empty($purchases)): ?>
    <div class="message error">There are no purchases yet. <a href="/barecarsales_app/purchase/purchase_create.php">Add a purchase</a> before you can record a repair.</div>
<?php else: ?>
<form method="post">
    <label>Purchase</label>
    <select name="purchase_id" required>
        <option value="">— Select purchase —</option>
        <?php foreach ($purchases as $p):
            $pid = (string) $p['purchase_id'];
            $plabel = '#' . $pid . ' — ' . $p['purchase_date'] . ' — ' . $p['make'] . ' ' . $p['model'] . ' ' . $p['year'];
            ?>
            <option value="<?= htmlspecialchars($pid) ?>" <?= $purchase_id === $pid ? 'selected' : '' ?>><?= htmlspecialchars($plabel) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Problem Description</label>
    <input type="text" name="problem_description" value="<?= htmlspecialchars($problem_description) ?>" required>

    <label>Estimated Repair Cost</label>
    <input type="number" step="0.01" name="est_repair_cost" value="<?= htmlspecialchars($est_repair_cost) ?>" required>

    <label>Actual Cost</label>
    <input type="number" step="0.01" name="actual_cost" value="<?= htmlspecialchars($actual_cost) ?>" required>

    <button type="submit">Save Repair</button>
    <a class="btn btn-secondary" href="repairs.php">Cancel</a>
</form>
<?php endif; ?>

<?php
include '../footer.php';
$conn->close();
?>