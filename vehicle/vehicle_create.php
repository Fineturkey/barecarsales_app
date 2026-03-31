<?php
include '../db.php';

$errors = [];
$vin = '';
$make = '';
$model = '';
$year = '';
$color = '';
$miles = '';
$vehicle_condition = '';
$book_price = '';
$style = '';
$interior_color = '';
$current_status = 'in_stock';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $current_status = trim($_POST['current_status'] ?? 'in_stock');

    if ($make === '') {
        $errors[] = 'Make is required.';
    }

    if ($model === '') {
        $errors[] = 'Model is required.';
    }

    if ($year !== '' && (!ctype_digit($year) || (int)$year < 1800 || (int)$year > 2100)) {
        $errors[] = 'Year must be a valid whole number between 1800 and 2100.';
    }

    if ($miles !== '' && !ctype_digit($miles)) {
        $errors[] = 'Miles must be a whole number.';
    }

    if ($book_price !== '' && !is_numeric($book_price)) {
        $errors[] = 'Book price must be a number.';
    }

    $valid_statuses = ['in_stock', 'sold', 'repairing'];
    if (!in_array($current_status, $valid_statuses, true)) {
        $current_status = 'in_stock';
    }

    if (empty($errors)) {
        $year_int = $year !== '' ? (int)$year : null;
        $miles_int = $miles !== '' ? (int)$miles : null;
        $book_price_float = $book_price !== '' ? (float)$book_price : null;

        $stmt = $conn->prepare(
            'INSERT INTO vehicle (
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt === false) {
            $errors[] = 'Prepare failed: ' . $conn->error;
        } else {
            $stmt->bind_param(
                'sssisssdsss',
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
                $current_status
            );

            if ($stmt->execute()) {
                $stmt->close();
                header('Location: vehicles.php?msg=created');
                exit;
            }

            $errors[] = 'Insert failed: ' . $stmt->error;
            $stmt->close();
        }
    }
}

include '../header.php';
?>

<h2>Add Vehicle</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>VIN</label>
    <input type="text" name="vin" value="<?= htmlspecialchars($vin) ?>">

    <label>Make</label>
    <input type="text" id="make" name="make" value="<?= htmlspecialchars($make) ?>" required>

    <label>Model</label>
    <input type="text" id="model" name="model" value="<?= htmlspecialchars($model) ?>" required>

    <label>Year</label>
    <input type="number" id="year" name="year" value="<?= htmlspecialchars($year) ?>" min="1800" max="2100">

    <label>Color</label>
    <input type="text" id="color" name="color" value="<?= htmlspecialchars($color) ?>">

    <label>Miles</label>
    <input type="number" id="miles" name="miles" value="<?= htmlspecialchars($miles) ?>" min="0">

    <label>Condition</label>
    <input type="text" id="vehicle_condition" name="vehicle_condition" value="<?= htmlspecialchars($vehicle_condition) ?>">

    <label>Book Price</label>
    <input type="number" step="0.01" id="book_price" name="book_price" value="<?= htmlspecialchars($book_price) ?>">

    <label>Style</label>
    <input type="text" id="style" name="style" value="<?= htmlspecialchars($style) ?>">

    <label>Interior Color</label>
    <input type="text" id="interior_color" name="interior_color" value="<?= htmlspecialchars($interior_color) ?>">

    <label>Current Status</label>
    <select name="current_status">
        <option value="in_stock" <?= $current_status === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
        <option value="sold" <?= $current_status === 'sold' ? 'selected' : '' ?>>Sold</option>
        <option value="repairing" <?= $current_status === 'repairing' ? 'selected' : '' ?>>Repairing</option>
    </select>

    <button type="submit">Add Vehicle</button>
    <a class="btn btn-secondary" href="vehicles.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>