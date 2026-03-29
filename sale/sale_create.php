<?php
include '../db.php';

$errors = [];
$vehicle_id = '';
$customer_id = '';
$salesperson_id = '';
$sale_date = '';

$down_payment = '0.00';
$financed_amount = '0.00';
$sale_price = '0.00';
$salesperson_commission = '0.00';
$total_due = '0.00';

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

    if ($vehicle_id === '' || !ctype_digit($vehicle_id) || (int)$vehicle_id <= 0) {
        $errors[] = "Vehicle ID is required and must be a valid number.";
    }
    if ($customer_id === '' || !ctype_digit($customer_id) || (int)$customer_id <= 0) {
        $errors[] = "Customer ID is required and must be a valid number.";
    }
    if ($salesperson_id === '' || !ctype_digit($salesperson_id) || (int)$salesperson_id <= 0) {
        $errors[] = "Salesperson ID is required and must be a valid number.";
    }
    if ($sale_date === '') {
        $errors[] = "Sale date is required.";
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
    if ($total_due === '' || !is_numeric($total_due)) {
        $errors[] = "Total due must be numeric.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO sale (
                vehicle_id,
                customer_id,
                salesperson_employee_id,
                sale_date,
                down_payment,
                financed_amount,
                sale_price,
                salesperson_commission,
                total_due
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $vehicle_id_int = (int)$vehicle_id;
        $customer_id_int = (int)$customer_id;
        $salesperson_id_int = (int)$salesperson_id;
        $down_payment_float = (float)$down_payment;
        $financed_amount_float = (float)$financed_amount;
        $sale_price_float = (float)$sale_price;
        $salesperson_commission_float = (float)$salesperson_commission;
        $total_due_float = (float)$total_due;

        $stmt->bind_param(
            "iiisddddd",
            $vehicle_id_int,
            $customer_id_int,
            $salesperson_id_int,
            $sale_date,
            $down_payment_float,
            $financed_amount_float,
            $sale_price_float,
            $salesperson_commission_float,
            $total_due_float
        );

        if ($stmt->execute()) {
            header("Location: sales.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add Sale</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Vehicle ID</label>
    <input type="text" name="vehicle_id" value="<?= htmlspecialchars($vehicle_id) ?>" required>

    <label>Customer ID</label>
    <input type="text" name="customer_id" value="<?= htmlspecialchars($customer_id) ?>" required>

    <label>Salesperson ID</label>
    <input type="text" name="salesperson_id" value="<?= htmlspecialchars($salesperson_id) ?>" required>

    <label>Total Due</label>
    <input type="number" step="0.01" name="total_due" value="<?= htmlspecialchars($total_due) ?>" required>

    <label>Down Payment</label>
    <input type="number" step="0.01" name="down_payment" value="<?= htmlspecialchars($down_payment) ?>" required>

    <label>Financed Amount</label>
    <input type="number" step="0.01" name="financed_amount" value="<?= htmlspecialchars($financed_amount) ?>" required>

    <label>Sale Price</label>
    <input type="number" step="0.01" name="sale_price" value="<?= htmlspecialchars($sale_price) ?>" required>

    <label>Sale Date</label>
    <input type="date" name="sale_date" value="<?= htmlspecialchars($sale_date) ?>" required>

    <label>Salesperson Commission</label>
    <input type="number" step="0.01" name="salesperson_commission" value="<?= htmlspecialchars($salesperson_commission) ?>" required>

    <button type="submit">Save Sale</button>
    <a class="btn btn-secondary" href="sales.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>