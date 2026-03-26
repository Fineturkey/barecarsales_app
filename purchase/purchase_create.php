<?php
include '../db.php';

$errors = [];
$purchase_id = '';
$vehicle_id = '';
$buyer_employee_id = '';
$seller_name = '';
$purchase_date = '';
$location = '';
$is_auction = '';
$price_paid = ''; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $purchase_id = trim($_POST['purchase_id'] ?? '');
    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $buyer_employee_id = trim($_POST['buyer_employee_id'] ?? '');
    $seller_name = trim($_POST['seller_name'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $is_auction = trim($_POST['is_auction'] ?? '');
    $price_paid = trim($_POST['price_paid'] ?? '');

    if ($purchase_id === '') {
        $errors[] = "purchase_id is required.";
    }

    if ($vehicle_id === '') {
        $errors[] = "Vewhicle ID is required";
    }

    if ($buyer_employee_id === '') {
        $errors[] = "Buyer employee ID is required.";
    }

    if ($seller_name === '') {
        $errors[] = "Seller name is required.";
    }

    if ($purchase_date === '') {
        $errors[] = "Purchase date is required.";
    }

    if ($is_auction === '') {
        $errors[] = "Is it for auction?.";
    }

    if ($price_paid === '') {
        $errors[] = "Total price must be listed";
    }


    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO purchase (
                purchase_id,
                vehicle_id,
                buyer_employee_id,
                seller_name,
                purchase_date,
                location,
                is_auction,
                price_paid
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $late_payment_count_int = (int)$late_payment_count;
        $avg_days_late_float = (float)$avg_days_late;

        $stmt->bind_param(
            "iiisssid",
            $purchase_id,
            $vehicle_id,
            $buyer_employee_id,
            $seller_name,
            $purchase_date,
            $location,
            $is_auction,
            $price_paid
        );

        if ($stmt->execute()) {
            header("Location: purchases.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add purchase</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    
    <label>Vehicle ID</label>
    <input type="text" name="vehicle_id" value="<?= htmlspecialchars($vehicle_id) ?>" required>

    <label>Buyer employee ID</label>
    <input type="text" name="buyer_employee_id" value="<?= htmlspecialchars($buyer_employee_id) ?>">

    <label>Selelrs name</label>
    <input type="text" name="seller_name" value="<?= htmlspecialchars($seller_name) ?>">

    <label>Purchase dDate</label>
    <input type="text" name="purchase_date" value="<?= htmlspecialchars($purchase_date) ?>">

    <label>Location</label>
    <input type="text" name="location" value="<?= htmlspecialchars($location) ?>">

    <label>Price Paid</label>
    <input type="text" name="price_paid" value="<?= htmlspecialchars($price_paid) ?>">

    <label>
        <input
            type="checkbox"
            name="is_auction"
            value="1"
            <?= $is_auction ? 'checked' : '' ?>
        >
        Is for auction
    </label>

    <button type="submit">Save purchase</button>
    <a class="btn btn-secondary" href="purchases.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>