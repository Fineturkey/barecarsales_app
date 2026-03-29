<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        sale_id,
        vehicle_id,
        customer_id,
        salesperson_employee_id,
        sale_date,
        total_due,
        down_payment,
        financed_amount,
        sale_price,
        salesperson_commission
    FROM sale
    WHERE sale_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$sale = $result->fetch_assoc();
$stmt->close();

if (!$sale) {
    header("Location: sales.php?msg=not_found");
    exit;
}

$vehicle_id = (string)$sale['vehicle_id'];
$customer_id = (string)$sale['customer_id'];
$salesperson_id = (string)$sale['salesperson_employee_id'];
$sale_date = (string)$sale['sale_date'];

$total_due = (string)$sale['total_due'];
$down_payment = (string)$sale['down_payment'];
$financed_amount = (string)$sale['financed_amount'];
$sale_price = (string)$sale['sale_price'];
$salesperson_commission = (string)$sale['salesperson_commission'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $customer_id = trim($_POST['customer_id'] ?? '');
    $salesperson_id = trim($_POST['salesperson_id'] ?? '');
    $sale_date = trim($_POST['sale_date'] ?? '');

    $down_payment = trim($_POST['down_payment'] ?? '0');
    $financed_amount = trim($_POST['financed_amount'] ?? '0');
    $sale_price = trim($_POST['sale_price'] ?? '0');
    $salesperson_commission = trim($_POST['salesperson_commission'] ?? '0');
    $total_due = trim($_POST['total_due'] ?? '0');

    if ($vehicle_id === '' || !ctype_digit($vehicle_id)) {
        $errors[] = "Vehicle ID must be a whole number.";
    }
    if ($customer_id === '' || !ctype_digit($customer_id)) {
        $errors[] = "Customer ID must be a whole number.";
    }
    if ($salesperson_id === '' || !ctype_digit($salesperson_id)) {
        $errors[] = "Salesperson ID must be a whole number.";
    }
    if ($sale_date === '') {
        $errors[] = "Sale date is required.";
    }
    if ($total_due === '' || !is_numeric($total_due)) {
        $errors[] = "Total due must be numeric.";
    }
    if ($down_payment === '' || !is_numeric($down_payment)) {
        $errors[] = "Down payment must be numeric.";
    }
    if ($financed_amount === '' || !is_numeric($financed_amount)) {
        $errors[] = "Financed amount must be numeric.";
    }
    if ($sale_price === '' || !is_numeric($sale_price)) {
        $errors[] = "Sale price must be numeric.";
    }
    if ($salesperson_commission === '' || !is_numeric($salesperson_commission)) {
        $errors[] = "Salesperson commission must be numeric.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE sale
            SET
                vehicle_id = ?,
                customer_id = ?,
                salesperson_employee_id = ?,
                sale_date = ?,
                total_due = ?,
                down_payment = ?,
                financed_amount = ?,
                sale_price = ?,
                salesperson_commission = ?
            WHERE sale_id = ?
        ");

        $vehicle_id_int = (int)$vehicle_id;
        $customer_id_int = (int)$customer_id;
        $salesperson_id_int = (int)$salesperson_id;
        $total_due_float = (float)$total_due;
        $down_payment_float = (float)$down_payment;
        $financed_amount_float = (float)$financed_amount;
        $sale_price_float = (float)$sale_price;
        $salesperson_commission_float = (float)$salesperson_commission;

        $stmt->bind_param(
            "iiisdddddi",
            $vehicle_id_int,
            $customer_id_int,
            $salesperson_id_int,
            $sale_date,
            $total_due_float,
            $down_payment_float,
            $financed_amount_float,
            $sale_price_float,
            $salesperson_commission_float,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: sales.php?msg=updated");
            exit;
        }

        $errors[] = "Update failed: " . $stmt->error;
        $stmt->close();
    }
}

$cur_vehicle_id = (int)$vehicle_id;
$vehicles_stmt = $conn->prepare("
    SELECT vehicle_id, make, model, year
    FROM vehicle
    WHERE current_status = 'in_stock' OR vehicle_id = ?
    ORDER BY make, model, year
");
$vehicles_stmt->bind_param("i", $cur_vehicle_id);
$vehicles_stmt->execute();
$vehicles_res = $vehicles_stmt->get_result();
$vehicles = [];
while ($row = $vehicles_res->fetch_assoc()) {
    $vehicles[] = $row;
}
$vehicles_stmt->close();

$customers_result = $conn->query("
    SELECT customer_id, first_name, last_name
    FROM customer
    ORDER BY last_name, first_name
");
$customers = [];
if ($customers_result) {
    while ($row = $customers_result->fetch_assoc()) {
        $customers[] = $row;
    }
}

$cur_salesperson_id = (int)$salesperson_id;
$salespeople_stmt = $conn->prepare("
    SELECT employee_id, first_name, last_name
    FROM employee
    WHERE role IN ('salesperson', 'both') OR employee_id = ?
    ORDER BY last_name, first_name
");
$salespeople_stmt->bind_param("i", $cur_salesperson_id);
$salespeople_stmt->execute();
$salespeople_res = $salespeople_stmt->get_result();
$salespeople = [];
while ($row = $salespeople_res->fetch_assoc()) {
    $salespeople[] = $row;
}
$salespeople_stmt->close();

include '../header.php';
?>

<h2>Edit Sale</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Vehicle</label>
    <select name="vehicle_id" required>
        <option value="">Select vehicle</option>
        <?php foreach ($vehicles as $v): ?>
            <?php
            $vid = (string)$v['vehicle_id'];
            $vlabel = trim($v['make'] . ' ' . $v['model'] . ' ' . $v['year']);
            ?>
            <option value="<?= htmlspecialchars($vid) ?>" <?= $vehicle_id === $vid ? 'selected' : '' ?>><?= htmlspecialchars($vlabel) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Customer</label>
    <select name="customer_id" required>
        <option value="">Select customer</option>
        <?php foreach ($customers as $c): ?>
            <?php
            $cid = (string)$c['customer_id'];
            $clabel = trim($c['first_name'] . ' ' . $c['last_name']);
            ?>
            <option value="<?= htmlspecialchars($cid) ?>" <?= $customer_id === $cid ? 'selected' : '' ?>><?= htmlspecialchars($clabel) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Salesperson</label>
    <select name="salesperson_id" required>
        <option value="">Select salesperson</option>
        <?php foreach ($salespeople as $sp): ?>
            <?php
            $sid = (string)$sp['employee_id'];
            $splabel = trim($sp['first_name'] . ' ' . $sp['last_name']);
            ?>
            <option value="<?= htmlspecialchars($sid) ?>" <?= $salesperson_id === $sid ? 'selected' : '' ?>><?= htmlspecialchars($splabel) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Sale Date</label>
    <input type="date" name="sale_date" value="<?= htmlspecialchars($sale_date) ?>" required>

    <label>Total Due</label>
    <input type="number" step="0.01" name="total_due" value="<?= htmlspecialchars($total_due) ?>" required>

    <label>Down Payment</label>
    <input type="number" step="0.01" name="down_payment" value="<?= htmlspecialchars($down_payment) ?>" required>

    <label>Financed Amount</label>
    <input type="number" step="0.01" name="financed_amount" value="<?= htmlspecialchars($financed_amount) ?>" required>

    <label>Sale Price</label>
    <input type="number" step="0.01" name="sale_price" value="<?= htmlspecialchars($sale_price) ?>" required>

    <label>Salesperson Commission</label>
    <input type="number" step="0.01" name="salesperson_commission" value="<?= htmlspecialchars($salesperson_commission) ?>" required>

    <button type="submit">Update Sale</button>
    <a class="btn btn-secondary" href="sales.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>
