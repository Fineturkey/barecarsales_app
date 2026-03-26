<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        first_name,
        last_name,
        phone,
        address,
        city,
        state,
        zip,
        gender,
        date_of_birth,
        taxpayer_id,
        late_payment_count,
        avg_days_late,
        reported_to_credit_bureau
    FROM customer
    WHERE customer_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    header("Location: customers.php?msg=not_found");
    exit;
}

$first_name = $customer['first_name'];
$last_name = $customer['last_name'];
$phone = $customer['phone'];
$address = $customer['address'];
$city = $customer['city'];
$state = $customer['state'];
$zip = $customer['zip'];
$gender = $customer['gender'];
$date_of_birth = $customer['date_of_birth'];
$taxpayer_id = $customer['taxpayer_id'];
$late_payment_count = $customer['late_payment_count'];
$avg_days_late = $customer['avg_days_late'];
$reported_to_credit_bureau = $customer['reported_to_credit_bureau'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $taxpayer_id = trim($_POST['taxpayer_id'] ?? '');
    $late_payment_count = trim($_POST['late_payment_count'] ?? '0');
    $avg_days_late = trim($_POST['avg_days_late'] ?? '0');
    $reported_to_credit_bureau = isset($_POST['reported_to_credit_bureau']) ? 1 : 0;

    if ($first_name === '') {
        $errors[] = "First name is required.";
    }

    if ($last_name === '') {
        $errors[] = "Last name is required.";
    }

    if ($late_payment_count !== '' && !ctype_digit($late_payment_count)) {
        $errors[] = "Late payment count must be a whole number.";
    }

    if ($avg_days_late !== '' && !is_numeric($avg_days_late)) {
        $errors[] = "Average days late must be numeric.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE customer
            SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                address = ?,
                city = ?,
                state = ?,
                zip = ?,
                gender = ?,
                date_of_birth = ?,
                taxpayer_id = ?,
                late_payment_count = ?,
                avg_days_late = ?,
                reported_to_credit_bureau = ?
            WHERE customer_id = ?
        ");

        $late_payment_count_int = (int)$late_payment_count;
        $avg_days_late_float = (float)$avg_days_late;

        $stmt->bind_param(
            "ssssssssssidii",
            $first_name,
            $last_name,
            $phone,
            $address,
            $city,
            $state,
            $zip,
            $gender,
            $date_of_birth,
            $taxpayer_id,
            $late_payment_count_int,
            $avg_days_late_float,
            $reported_to_credit_bureau,
            $id
        );

        if ($stmt->execute()) {
            header("Location: customers.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit Customer</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>First Name</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>

    <label>Last Name</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">

    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($address) ?>">

    <label>City</label>
    <input type="text" name="city" value="<?= htmlspecialchars($city) ?>">

    <label>State</label>
    <input type="text" name="state" value="<?= htmlspecialchars($state) ?>">

    <label>Zip</label>
    <input type="text" name="zip" value="<?= htmlspecialchars($zip) ?>">

    <label>Gender</label>
    <input type="text" name="gender" value="<?= htmlspecialchars($gender) ?>">

    <label>Date of Birth</label>
    <input type="date" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>">

    <label>Taxpayer ID</label>
    <input type="text" name="taxpayer_id" value="<?= htmlspecialchars($taxpayer_id) ?>">

    <label>Late Payment Count</label>
    <input type="number" name="late_payment_count" value="<?= htmlspecialchars($late_payment_count) ?>">

    <label>Average Days Late</label>
    <input type="number" step="0.01" name="avg_days_late" value="<?= htmlspecialchars($avg_days_late) ?>">

    <label>
        <input
            type="checkbox"
            name="reported_to_credit_bureau"
            value="1"
            <?= $reported_to_credit_bureau ? 'checked' : '' ?>
        >
        Reported to Credit Bureau
    </label>

    <button type="submit">Update Customer</button>
    <a class="btn btn-secondary" href="customers.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>