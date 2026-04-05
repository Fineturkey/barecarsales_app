<?php
include '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        vin,
        make,
        model,
        year,
        color,
        miles,
        vehicle_condition,
        book_price,
        style,
        interior_color,
        current_status
    FROM vehicle
    WHERE vehicle_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();
$stmt->close();

if (!$vehicle) {
    header("Location: vehicles.php?msg=not_found");
    exit;
}

$vin = $vehicle['vin'];
$make = $vehicle['make'];
$model = $vehicle['model'];
$year = $vehicle['year'];
$color = $vehicle['color'];
$miles = $vehicle['miles'];
$vehicle_condition = $vehicle['vehicle_condition'];
$book_price = $vehicle['book_price'];
$style = $vehicle['style'];
$interior_color = $vehicle['interior_color'];
$current_status = $vehicle['current_status'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vin = trim($_POST['vin'] ?? '');
    $make = trim($_POST['make'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $miles = trim($_POST['miles'] ?? '');
    $vehicle_condition = trim($_POST['vehicle_condition'] ?? '');
    $book_price = trim($_POST['book_price'] ?? '');
    $style = trim($_POST['style'] ?? '');
    $interior_color = trim($_POST['interior_color'] ?? '');
    $current_status = trim($_POST['current_status'] ?? '');

    if ($vin === '') {
        $errors[] = 'VIN is required.';
    }

    if ($make === '') {
        $errors[] = 'Make is required.';
    }

    if ($model === '') {
        $errors[] = 'Model is required.';
    }

    if ($year !== '' && !is_numeric($year)) {
        $errors[] = 'Year must be a number.';
    }

    if ($miles !== '' && !is_numeric($miles)) {
        $errors[] = 'Miles must be a number.';
    }

    if ($book_price !== '' && !is_numeric($book_price)) {
        $errors[] = 'Book price must be a number.';
    }

    $valid_statuses = ['in_stock', 'sold', 'repairing'];
    if (!in_array($current_status, $valid_statuses, true)) {
        $current_status = 'in_stock';
    }

    if (empty($errors)) {
        $year_int = $year !== '' ? (int) $year : null;
        $miles_int = $miles !== '' ? (int) $miles : null;
        $book_price_float = $book_price !== '' ? (float) $book_price : null;

        $stmt = $conn->prepare("
            UPDATE vehicle
            SET
                vin = ?,
                make = ?,
                model = ?,
                year = ?,
                color = ?,
                miles = ?,
                vehicle_condition = ?,
                book_price = ?,
                style = ?,
                interior_color = ?,
                current_status = ?
            WHERE vehicle_id = ?
        ");

        $stmt->bind_param(
            "sssisisdsssi",
            $vin,
            $make,
            $model,
            $year_int,
            $color,
            $miles_int,
            $vehicle_condition,
            $book_price_float,
            $style,
            $interior_color,
            $current_status,
            $id
        );

        if ($stmt->execute()) {
            header("Location: vehicles.php?msg=updated");
            exit;
        } else {
            $errors[] = "Error updating vehicle: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Vehicle</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>VIN</label>
    <input type="text" name="vin" value="<?= htmlspecialchars($vin) ?>" required>

    <label>Make</label>
    <input type="text" name="make" value="<?= htmlspecialchars($make) ?>" required>

    <label>Model</label>
    <input type="text" name="model" value="<?= htmlspecialchars($model) ?>" required>

    <label>Year</label>
    <input type="text" name="year" value="<?= htmlspecialchars($year) ?>">

    <label>Color</label>
    <input type="text" name="color" value="<?= htmlspecialchars($color) ?>">

    <label>Miles</label>
    <input type="text" name="miles" value="<?= htmlspecialchars($miles) ?>">

    <label>Condition</label>
    <input type="text" name="vehicle_condition" value="<?= htmlspecialchars($vehicle_condition) ?>">

    <label>Book Price</label>
    <input type="text" name="book_price" value="<?= htmlspecialchars($book_price) ?>">

    <label>Style</label>
    <input type="text" name="style" value="<?= htmlspecialchars($style) ?>">

    <label>Interior Color</label>
    <input type="text" name="interior_color" value="<?= htmlspecialchars($interior_color) ?>">

    <label>Status</label>
    <select name="current_status">
        <option value="in_stock" <?= $current_status === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
        <option value="sold" <?= $current_status === 'sold' ? 'selected' : '' ?>>Sold</option>
        <option value="repairing" <?= $current_status === 'repairing' ? 'selected' : '' ?>>Repairing</option>
    </select>

    <br><br>
    <button type="submit">Update Vehicle</button>
    <a class="btn btn-secondary" href="vehicles.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>