<?php
include '../db.php';

$errors = [];
$sale_id = '';
$vehicle_id = '';
$customer_id = '';
$employee_id = '';
$warranty_name = '';
$warranty_sale_date = '';
$start_date = '';
$length_months = '';
$cost = '';
$deductible = '';
$items_covered = '';
$total_cost = '';
$monthly_cost = '';
$warranty_commission = '';

// fetch options for dropdowns
$sales = [];
$sales_res = $conn->query("
    SELECT s.sale_id, s.sale_date, v.make, v.model, v.year
    FROM sale s
    INNER JOIN vehicle v ON s.vehicle_id = v.vehicle_id
    ORDER BY s.sale_date DESC, s.sale_id DESC
");
if ($sales_res) {
    while ($row = $sales_res->fetch_assoc()) {
        $sales[] = $row;
    }
}

$vehicles = [];
$vehicles_res = $conn->query("
    SELECT vehicle_id, make, model, year
    FROM vehicle
    ORDER BY make, model, year
");
if ($vehicles_res) {
    while ($row = $vehicles_res->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

$customers = [];
$customers_res = $conn->query("
    SELECT customer_id, first_name, last_name
    FROM customer
    ORDER BY last_name, first_name
");
if ($customers_res) {
    while ($row = $customers_res->fetch_assoc()) {
        $customers[] = $row;
    }
}

$employees = [];
$employees_res = $conn->query("
    SELECT employee_id, first_name, last_name
    FROM employee
    ORDER BY last_name, first_name
");
if ($employees_res) {
    while ($row = $employees_res->fetch_assoc()) {
        $employees[] = $row;
    }
}

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
            INSERT INTO warranty (
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            "iiiisssidisddd",
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
            $warranty_commission_float
        );

        if ($stmt->execute()) {
            header("Location: warranties.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add Warranty</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Sale</label>
    <select name="sale_id" required>
        <option value="">Select sale</option>
        <?php foreach ($sales as $s): ?>
            <?php
            $sid = (string)$s['sale_id'];
            $slabel = 'Sale #' . $sid . ' — ' . $s['sale_date'] . ' — ' . $s['make'] . ' ' . $s['model'] . ' ' . $s['year'];
            ?>
            <option value="<?= htmlspecialchars($sid) ?>" <?= $sale_id === $sid ? 'selected' : '' ?>>
                <?= htmlspecialchars($slabel) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Vehicle</label>
    <select name="vehicle_id" required>
        <option value="">Select vehicle</option>
        <?php foreach ($vehicles as $v): ?>
            <?php
            $vid = (string)$v['vehicle_id'];
            $vlabel = $v['make'] . ' ' . $v['model'] . ' ' . $v['year'];
            ?>
            <option value="<?= htmlspecialchars($vid) ?>" <?= $vehicle_id === $vid ? 'selected' : '' ?>>
                <?= htmlspecialchars($vlabel) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Customer</label>
    <select name="customer_id" required>
        <option value="">Select customer</option>
        <?php foreach ($customers as $c): ?>
            <?php
            $cid = (string)$c['customer_id'];
            $clabel = $c['first_name'] . ' ' . $c['last_name'];
            ?>
            <option value="<?= htmlspecialchars($cid) ?>" <?= $customer_id === $cid ? 'selected' : '' ?>>
                <?= htmlspecialchars($clabel) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Employee</label>
    <select name="employee_id" required>
        <option value="">Select employee</option>
        <?php foreach ($employees as $e): ?>
            <?php
            $eid = (string)$e['employee_id'];
            $elabel = $e['first_name'] . ' ' . $e['last_name'];
            ?>
            <option value="<?= htmlspecialchars($eid) ?>" <?= $employee_id === $eid ? 'selected' : '' ?>>
                <?= htmlspecialchars($elabel) ?>
            </option>
        <?php endforeach; ?>
    </select>

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

    <button type="submit">Save Warranty</button>
    <a class="btn btn-secondary" href="warranties.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>