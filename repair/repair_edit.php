<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("SELECT repair_id, purchase_id, problem_description, est_repair_cost, actual_cost FROM repair WHERE repair_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$repair = $result->fetch_assoc();
$stmt->close();

if (!$repair) {
    header("Location: repairs.php?msg=not_found");
    exit;
}

$purchase_id = $repair['purchase_id'];
$problem_description = $repair['problem_description'];
$est_repair_cost = $repair['est_repair_cost'];
$actual_cost = $repair['actual_cost'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $purchase_id = trim($_POST['purchase_id'] ?? '');
    $problem_description = trim($_POST['problem_description'] ?? '');
    $est_repair_cost = trim($_POST['est_repair_cost'] ?? '');
    $actual_cost = trim($_POST['actual_cost'] ?? '');

    if ($purchase_id === '' || !ctype_digit($purchase_id) || (int)$purchase_id <= 0) {
        $errors[] = "Purchase ID is required and must be a valid number.";
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
        $stmt = $conn->prepare("UPDATE repair SET purchase_id = ?, problem_description = ?, est_repair_cost = ?, actual_cost = ? WHERE repair_id = ?");
        $purchase_id_int = (int)$purchase_id;
        $est_repair_cost_float = (float)$est_repair_cost;
        $actual_cost_float = (float)$actual_cost;

        $stmt->bind_param("isddi", $purchase_id_int, $problem_description, $est_repair_cost_float, $actual_cost_float, $id);

        if ($stmt->execute()) {
            header("Location: repairs.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Repair</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Purchase ID</label>
    <input type="number" name="purchase_id" value="<?= htmlspecialchars($purchase_id) ?>" required>

    <label>Problem Description</label>
    <input type="text" name="problem_description" value="<?= htmlspecialchars($problem_description) ?>" required>

    <label>Estimated Repair Cost</label>
    <input type="number" step="0.01" name="est_repair_cost" value="<?= htmlspecialchars($est_repair_cost) ?>" required>

    <label>Actual Cost</label>
    <input type="number" step="0.01" name="actual_cost" value="<?= htmlspecialchars($actual_cost) ?>" required>

    <button type="submit">Update Repair</button>
    <a class="btn btn-secondary" href="repairs.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>