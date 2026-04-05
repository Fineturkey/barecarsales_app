<?php
include '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        vehicle_id,
        buyer_employee_id,
        seller_name,
        purchase_date,
        location,
        is_auction,
        price_paid
    FROM purchase
    WHERE purchase_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();
$stmt->close();

if (!$purchase) {
    header("Location: purchases.php?msg=not_found");
    exit;
}

$vehicle_id = $purchase['vehicle_id'];
$buyer_employee_id = $purchase['buyer_employee_id'];
$seller_name = $purchase['seller_name'];
$purchase_date = $purchase['purchase_date'];
$location = $purchase['location'];
$is_auction = $purchase['is_auction'];
$price_paid = $purchase['price_paid'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $buyer_employee_id = trim($_POST['buyer_employee_id'] ?? '');
    $seller_name = trim($_POST['seller_name'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $is_auction = isset($_POST['is_auction']) ? 1 : 0;
    $price_paid = trim($_POST['price_paid'] ?? '');


    if ($seller_name === '') {
        $errors[] = "Seller name is required.";
    }

    if ($purchase_date === '') {
        $errors[] = "Purchase date is required.";
    }

    if ($price_paid === '' || !is_numeric($price_paid)) {
        $errors[] = "Price paid must be numeric.";
    }

    if (empty($errors)) {

        $vehicle_id_int = (int)$vehicle_id;
        $buyer_employee_id_int = (int)$buyer_employee_id;
        $price_paid_float = (float)$price_paid;

        $stmt = $conn->prepare("
            UPDATE purchase
            SET
                vehicle_id = ?,
                buyer_employee_id = ?,
                seller_name = ?,
                purchase_date = ?,
                location = ?,
                is_auction = ?,
                price_paid = ?
            WHERE purchase_id = ?
        ");

        $stmt->bind_param(
            "iisssidi",
            $vehicle_id_int,
            $buyer_employee_id_int,
            $seller_name,
            $purchase_date,
            $location,
            $is_auction,
            $price_paid_float,
            $id
        );

        if ($stmt->execute()) {
            header("Location: purchases.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Purchase</h2>

<?php foreach ($errors as $error): ?>
<div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">

<label>Vehicle ID</label>
<input type="number" name="vehicle_id" value="<?= htmlspecialchars($vehicle_id) ?>" required>

<label>Buyer Employee ID</label>
<input type="number" name="buyer_employee_id" value="<?= htmlspecialchars($buyer_employee_id) ?>" required>

<label>Seller Name</label>
<input type="text" name="seller_name" value="<?= htmlspecialchars($seller_name) ?>" required>

<label>Purchase Date</label>
<input type="date" name="purchase_date" value="<?= htmlspecialchars($purchase_date) ?>" required>

<label>Location</label>
<input type="text" name="location" value="<?= htmlspecialchars($location) ?>">

<label>
<input type="checkbox" name="is_auction" value="1"
<?= $is_auction ? 'checked' : '' ?>>
Auction Purchase
</label>

<label>Price Paid</label>
<input type="number" step="0.01" name="price_paid" value="<?= htmlspecialchars($price_paid) ?>" required>

<button type="submit">Update Purchase</button>
<a class="btn btn-secondary" href="purchases.php">Cancel</a>

</form>

<?php
include '../footer.php';
$conn->close();
?>