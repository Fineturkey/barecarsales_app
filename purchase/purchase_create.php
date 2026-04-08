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

// fetch vehicles for dropdown
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

// fetch employees for dropdown (buyers)
$buyers = [];
$buyers_res = $conn->query("
    SELECT employee_id, first_name, last_name
    FROM employee
    WHERE role IN ('buyer', 'both')
    ORDER BY last_name, first_name
");
if ($buyers_res) {
    while ($row = $buyers_res->fetch_assoc()) {
        $buyers[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = trim($_POST['vehicle_id'] ?? '');
    $buyer_employee_id = trim($_POST['buyer_employee_id'] ?? '');
    $seller_name = trim($_POST['seller_name'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $is_auction = isset($_POST['is_auction']) && (string) $_POST['is_auction'] === '1' ? 1 : 0;
    $price_paid = trim($_POST['price_paid'] ?? '');

    if ($vehicle_id === '' || !ctype_digit($vehicle_id)) {
        $errors[] = "Vehicle is required.";
    }

    if ($buyer_employee_id === '' || !ctype_digit($buyer_employee_id)) {
        $errors[] = "Buyer employee is required.";
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

        $vehicle_id_int = (int)$vehicle_id;
        $buyer_employee_id_int = (int)$buyer_employee_id;
        $price_paid_float = (float)$price_paid;

        $stmt->bind_param(
            "iisssid",
            $vehicle_id_int,
            $buyer_employee_id_int,
            $seller_name,
            $purchase_date,
            $location,
            $is_auction,
            $price_paid_float
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
    
    <label>Vehicle</label>
    <select name="vehicle_id" required>
        <option value="">Select vehicle</option>
        <?php foreach ($vehicles as $v): ?>
            <?php
            $vid = (string)$v['vehicle_id'];
            $vlabel = trim($v['make'] . ' ' . $v['model'] . ' ' . $v['year']);
            ?>
            <option value="<?= htmlspecialchars($vid) ?>" <?= $vehicle_id === $vid ? 'selected' : '' ?>>
                <?= htmlspecialchars($vlabel) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Buyer employee</label>
    <select name="buyer_employee_id" required>
        <option value="">-- Select buyer --</option>
        <?php foreach ($buyers as $b): ?>
            <option value="<?= htmlspecialchars((string) $b['employee_id']) ?>" <?= $buyer_employee_id === (string) $b['employee_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['employee_id'] . ' - ' . $b['last_name'] . ', ' . $b['first_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="form-field-own-line">
        <label for="seller_name">Sellers name</label>
        <input id="seller_name" type="text" name="seller_name" value="<?= htmlspecialchars($seller_name) ?>">
    </div>

    <label>Purchase Date</label>
    <input type="date" name="purchase_date" value="<?= htmlspecialchars($purchase_date) ?>" required>

    <div class="form-field-own-line">
        <label for="location">Location</label>
        <input id="location" type="text" name="location" value="<?= htmlspecialchars($location) ?>">
    </div>

    <label>Price Paid</label>
    <input type="text" name="price_paid" value="<?= htmlspecialchars($price_paid) ?>">

    <label>
        <input
            type="checkbox"
            name="is_auction"
            value="1"
            <?= $is_auction ? 'checked' : '' ?>
        >
        Auction
    </label>

    <button type="submit">Save purchase</button>
    <a class="btn btn-secondary" href="purchases.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>