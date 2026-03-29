<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        sale_id,
        vehicle_id,
        customer_id,
        employee_id,
        warranty_name,
        warranty_sale_date,
        start_date,
        length_months,
        cost,
        deductible,
        items_covered,
        total_cost,
        monthly_cost,
        warranty_commission
    FROM warranty
    WHERE warranty_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$warranty = $result->fetch_assoc();
$stmt->close();

if (!$warranty) {
    header("Location: warranties.php?msg=not_found");
    exit;
}

$sale_id = $warranty['sale_id'];
$vehicle_id = $warranty['vehicle_id'];
$customer_id = $warranty['customer_id'];
$employee_id = $warranty['employee_id'];
$warranty_name = $warranty['warranty_name'];
$warranty_sale_date = $warranty['warranty_sale_date'];
$start_date = $warranty['start_date'];
$length_months = $warranty['length_months'];
$cost = $warranty['cost'];
$deductible = $warranty['deductible'];
$items_covered = $warranty['items_covered'];
$total_cost = $warranty['total_cost'];
$monthly_cost = $warranty['monthly_cost'];
$warranty_commission = $warranty['warranty_commission'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sale_id = trim($_POST['sale_id'] ?? '');
    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $customer_id = trim($_POST['customer_id'] ?? '');
    $employee_id = trim($_POST['employee_id'] ?? '');
    $warranty_name = trim($_POST['warranty_name'] ?? '');
    $warranty_sale_date = trim($_POST['warranty_sale_date'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $length_months = trim($_POST['length_months'] ?? '');
    $cost = trim($_POST['cost'] ?? '');
    $deductible = trim($_POST['deductible'] ?? '');
    $items_covered = trim($_POST['items_covered'] ?? '');
    $total_cost = trim($_POST['total_cost'] ?? '');
    $monthly_cost = trim($_POST['monthly_cost'] ?? '');
    $warranty_commission = trim($_POST['warranty_commission'] ?? '');

    if ($sale_id === '' || !ctype_digit($sale_id)) {
        $errors[] = "Sale ID is required and must be a whole number.";
    }

    if ($vehicle_id === '' || !ctype_digit($vehicle_id)) {
        $errors[] = "Vehicle ID is required and must be a whole number.";
    }

    if ($customer_id === '' || !ctype_digit($customer_id)) {
        $errors[] = "Customer ID is required and must be a whole number.";
    }

    if ($employee_id === '' || !ctype_digit($employee_id)) {
        $errors[] = "Employee ID is required and must be a whole number.";
    }

    if ($warranty_name === '') {
        $errors[] = "Warranty name is required.";
    }

    if ($warranty_sale_date === '') {
        $errors[] = "Warranty sale date is required.";
    }

    if ($start_date === '') {
        $errors[] = "Start date is required.";
    }

    if ($length_months === '' || !ctype_digit($length_months)) {
        $errors[] = "Length months is required and must be a whole number.";
    }

    if ($cost === '' || !is_numeric($cost)) {
        $errors[] = "Cost is required and must be numeric.";
    }

    if ($deductible === '' || !is_numeric($deductible)) {
        $errors[] = "Deductible is required and must be numeric.";
    }

    if ($total_cost === '' || !is_numeric($total_cost)) {
        $errors[] = "Total cost is required and must be numeric.";
    }

    if ($monthly_cost === '' || !is_numeric($monthly_cost)) {
        $errors[] = "Monthly cost is required and must be numeric.";
    }

    if ($warranty_commission === '' || !is_numeric($warranty_commission)) {
        $errors[] = "Warranty commission is required and must be numeric.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE warranty
            SET
                sale_id = ?,
                vehicle_id = ?,
                customer_id = ?,
                employee_id = ?,
                warranty_name = ?,
                warranty_sale_date = ?,
                start_date = ?,
                length_months = ?,
                cost = ?,
                deductible = ?,
                items_covered = ?,
                total_cost = ?,
                monthly_cost = ?,
                warranty_commission = ?
            WHERE warranty_id = ?
        ");

        $sale_id_int = (int)$sale_id;
        $vehicle_id_int = (int)$vehicle_id;
        $customer_id_int = (int)$customer_id;
        $employee_id_int = (int)$employee_id;
        $length_months_int = (int)$length_months;
        $cost_float = (float)$cost;
        $deductible_float = (float)$deductible;
        $total_cost_float = (float)$total_cost;
        $monthly_cost_float = (float)$monthly_cost;
        $warranty_commission_float = (float)$warranty_commission;

        $stmt->bind_param(
            "iiiisssidisdddi",
            $sale_id_int,
            $vehicle_id_int,
            $customer_id_int,
            $employee_id_int,
            $warranty_name,
            $warranty_sale_date,
            $start_date,
            $length_months_int,
            $cost_float,
            $deductible_float,
            $items_covered,
            $total_cost_float,
            $monthly_cost_float,
            $warranty_commission_float,
            $id
        );

        if ($stmt->execute()) {
            header("Location: warranties.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Warranty</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Sale ID</label>
    <input type="number" name="sale_id" value="<?= htmlspecialchars($sale_id) ?>" required>

    <label>Vehicle ID</label>
    <input type="number" name="vehicle_id" value="<?= htmlspecialchars($vehicle_id) ?>" required>

    <label>Customer ID</label>
    <input type="number" name="customer_id" value="<?= htmlspecialchars($customer_id) ?>" required>

    <label>Employee ID</label>
    <input type="number" name="employee_id" value="<?= htmlspecialchars($employee_id) ?>" required>

    <label>Warranty Name</label>
    <input type="text" name="warranty_name" value="<?= htmlspecialchars($warranty_name) ?>" required>

    <label>Warranty Sale Date</label>
    <input type="date" name="warranty_sale_date" value="<?= htmlspecialchars($warranty_sale_date) ?>" required>

    <label>Start Date</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>

    <label>Length Months</label>
    <input type="number" name="length_months" value="<?= htmlspecialchars($length_months) ?>" required>

    <label>Cost</label>
    <input type="number" step="0.01" name="cost" value="<?= htmlspecialchars($cost) ?>" required>

    <label>Deductible</label>
    <input type="number" step="0.01" name="deductible" value="<?= htmlspecialchars($deductible) ?>" required>

    <label>Items Covered</label>
    <textarea name="items_covered" rows="4"><?= htmlspecialchars($items_covered) ?></textarea>

    <label>Total Cost</label>
    <input type="number" step="0.01" name="total_cost" value="<?= htmlspecialchars($total_cost) ?>" required>

    <label>Monthly Cost</label>
    <input type="number" step="0.01" name="monthly_cost" value="<?= htmlspecialchars($monthly_cost) ?>" required>

    <label>Warranty Commission</label>
    <input type="number" step="0.01" name="warranty_commission" value="<?= htmlspecialchars($warranty_commission) ?>" required>

    <button type="submit">Update Warranty</button>
    <a class="btn btn-secondary" href="warranties.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>