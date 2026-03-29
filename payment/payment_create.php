<?php
include '../db.php';

$errors = [];
$customer_id = '';
$sale_id = '';
$amount = '';
$payment_date = '';
$due_date = '';
$paid_date = '';
$bank_account = '';
$is_late = 0;
$days_late = 0;

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
$customer_id_selected = isset($_POST['customer_id']) ? trim($_POST['customer_id'] ?? '') : (isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : '');
if ($customer_id_selected && ctype_digit($customer_id_selected)) {
    $stmt = $conn->prepare("SELECT sale_id, sale_date, sale_price, total_due 
                            FROM sale 
                            WHERE customer_id = ? 
                            ORDER BY sale_date DESC");
    $stmt->bind_param("i", $customer_id_selected);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = trim($_POST['customer_id'] ?? '');
    $sale_id = trim($_POST['sale_id'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $payment_date = trim($_POST['payment_date'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $paid_date = trim($_POST['paid_date'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');
    $is_late = isset($_POST['is_late']) ? 1 : 0;
    $days_late = trim($_POST['days_late'] ?? '0');

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
            INSERT INTO payment (
                customer_id,
                sale_id,
                amount,
                payment_date,
                due_date,
                paid_date,
                bank_account,
                is_late,
                days_late
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $customer_id_int = (int)$customer_id;
        $sale_id_int = (int)$sale_id;
        $amount_float = (float)$amount;

        $stmt->bind_param(
            "iidssssii",
            $customer_id_int,
            $sale_id_int,
            $amount_float,
            $payment_date,
            $due_date,
            $paid_date,
            $bank_account,
            $is_late,
            $days_late
        );

        if ($stmt->execute()) {
            header("Location: payments.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add Payment</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Customer</label>
    <select name="customer_id" id="customer_id" required onchange="this.form.submit()">
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
    <input type="date" id="payment_date" name="payment_date" value="<?= htmlspecialchars($payment_date) ?>" required>

    <label>Due Date</label>
    <input type="date" id="due_date" name="due_date" value="<?= htmlspecialchars($due_date) ?>" required>

    <label>Paid Date</label>
    <input type="date" id="paid_date" name="paid_date" value="<?= htmlspecialchars($paid_date) ?>">

    <label>Bank Account</label>
    <input type="text" name="bank_account" value="<?= htmlspecialchars($bank_account) ?>">

    <label>
        Is Late: <span id="is_late_display">No</span>
        <input type="hidden" id="is_late" name="is_late" value="0">
    </label>

    <label>
        || Days Late: <span id="days_late_display">0</span>
        <input type="hidden" id="days_late" name="days_late" value="0">
    </label>

    <script>
        const dueDateInput = document.getElementById('due_date');
        const paidDateInput = document.getElementById('paid_date');
        const daysLateInput = document.getElementById('days_late');
        const isLateInput = document.getElementById('is_late');
        const daysLateDisplay = document.getElementById('days_late_display');
        const isLateDisplay = document.getElementById('is_late_display');

        function calculateDaysLate() {
            const dueDate = new Date(dueDateInput.value);
            const paidDate = new Date(paidDateInput.value);

            if (!dueDateInput.value || !paidDateInput.value) {
                return;
            }

            // Calculate difference in days
            const timeDiff = paidDate - dueDate;
            const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

            if (daysDiff > 0) {
                daysLateInput.value = daysDiff;
                daysLateDisplay.textContent = daysDiff;
                isLateInput.value = 1;
                isLateDisplay.textContent = 'Yes';
            } else {
                daysLateInput.value = 0;
                daysLateDisplay.textContent = '0';
                isLateInput.value = 0;
                isLateDisplay.textContent = 'No';
            }
        }

        dueDateInput.addEventListener('change', calculateDaysLate);
        paidDateInput.addEventListener('change', calculateDaysLate);
    </script>

    <button type="submit">Save Payment</button>
    <a class="btn btn-secondary" href="payments.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>
 