<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT payment_id, customer_id, sale_id, amount, payment_date, due_date, paid_date, bank_account
    FROM payment
    WHERE payment_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    header("Location: payments.php?msg=not_found");
    exit;
}

$customer_id = $payment['customer_id'];
$sale_id = $payment['sale_id'];
$amount = $payment['amount'];
$payment_date = $payment['payment_date'];
$due_date = $payment['due_date'];
$paid_date = $payment['paid_date'];
$bank_account = $payment['bank_account'];
$is_late = $payment['is_late'];
$days_late = $payment['days_late'];

// Fetch customers for dropdown
$customers = [];
$result = $conn->query("SELECT customer_id, first_name, last_name 
                        FROM customer 
                        ORDER BY last_name, first_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// Fetch sales for the selected customer
$sales = [];
$stmt = $conn->prepare("SELECT sale_id, sale_date, sale_price, total_due 
                        FROM sale 
                        WHERE customer_id = ? 
                        ORDER BY sale_date DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = trim($_POST['customer_id'] ?? '');
    $sale_id = trim($_POST['sale_id'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $payment_date = trim($_POST['payment_date'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $paid_date = trim($_POST['paid_date'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');

    // Validation
    if ($customer_id === '' || !ctype_digit($customer_id)) {
        $errors[] = "Please select a valid customer.";
    }
    if ($sale_id === '' || !ctype_digit($sale_id)) {
        $errors[] = "Please select a valid sale.";
    }
    if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) {
        $errors[] = "Amount is required and must be greater than 0.";
    }
    if ($payment_date === '') {
        $errors[] = "Payment date is required.";
    }
    if ($due_date === '') {
        $errors[] = "Due date is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE payment SET
                customer_id = ?,
                sale_id = ?,
                amount = ?,
                payment_date = ?,
                due_date = ?,
                paid_date = ?,
                bank_account = ?
            WHERE payment_id = ?
        ");

        $customer_id_int = (int)$customer_id;
        $sale_id_int = (int)$sale_id;
        $amount_float = (float)$amount;

        $stmt->bind_param(
            "iidsssi",
            $customer_id_int,
            $sale_id_int,
            $amount_float,
            $payment_date,
            $due_date,
            $paid_date,
            $bank_account,
            $id
        );

        if ($stmt->execute()) {
            header("Location: payments.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Payment</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Customer</label>
    <select name="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['customer_id'] ?>" <?= $cust['customer_id'] == $customer_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($cust['customer_id'] . ' - ' . $cust['last_name'] . ', ' . $cust['first_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Sale</label>
    <select name="sale_id" required>
        <option value="">-- Select Sale --</option>
        <?php foreach ($sales as $sale): ?>
            <option value="<?= $sale['sale_id'] ?>" <?= $sale['sale_id'] == $sale_id ? 'selected' : '' ?>>
                Sale #<?= $sale['sale_id'] ?> - Date: <?= htmlspecialchars($sale['sale_date']) ?> - Price: $<?= number_format($sale['sale_price'], 2) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Amount</label>
    <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($amount) ?>" required>

    <label>Payment Date</label>
    <input type="date" name="payment_date" value="<?= htmlspecialchars($payment_date) ?>" required>

    <label>Due Date</label>
    <input type="date" name="due_date" value="<?= htmlspecialchars($due_date) ?>" required>

    <label>Paid Date</label>
    <input type="date" name="paid_date" value="<?= htmlspecialchars($paid_date) ?>">

    <label>Bank Account</label>
    <input type="text" name="bank_account" value="<?= htmlspecialchars($bank_account) ?>">

    <button type="submit">Update Payment</button>
    <a class="btn btn-secondary" href="payments.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>
