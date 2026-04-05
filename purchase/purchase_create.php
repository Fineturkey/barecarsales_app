<?php
include '../db.php';

$errors = [];
$vehicle_id = '';
$buyer_employee_id = '';
$seller_name = '';
$purchase_date = '';
$location = '';
$is_auction = 0;
$price_paid = ''; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $buyer_employee_id = trim($_POST['buyer_employee_id'] ?? '');
    $seller_name = trim($_POST['seller_name'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $is_auction = isset($_POST['is_auction']) && (string) $_POST['is_auction'] === '1' ? 1 : 0;
    $price_paid = trim($_POST['price_paid'] ?? '');

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

    if ($price_paid === '') {
        $errors[] = "Total price must be listed";
    }


    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO purchase (
                vehicle_id,
                buyer_employee_id,
                seller_name,
                purchase_date,
                location,
                is_auction,
                price_paid
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssid",
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

    <label>Sellers name</label>
    <input type="text" name="seller_name" value="<?= htmlspecialchars($seller_name) ?>">

    <label>Purchase Date</label>
    <input type="date" name="purchase_date" value="<?= htmlspecialchars($purchase_date) ?>" required>

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