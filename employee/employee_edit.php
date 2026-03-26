<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$stmt = $conn->prepare("
    SELECT
        first_name,
        last_name,
        phone,
        role
    FROM employee
    WHERE employee_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    header("Location: employees.php?msg=not_found");
    exit;
}

$first_name = $employee['first_name'];
$last_name = $employee['last_name'];
$phone = $employee['phone'];
$role = $role['role'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($first_name === '') {
        $errors[] = "First name is required.";
    }

    if ($last_name === '') {
        $errors[] = "Last name is required.";
    }

    if ($phone !== '') {
        $errors[] = "Phone number is required.";
    }

    if ($avg_days_late !== '') {
        $errors[] = "Role is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE employee
            SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                role = ?,
            WHERE employee_id = ?
        ");

        $stmt->bind_param(
            "ssss",
            $first_name,
            $last_name,
            $phone,
            $role,
        );

        if ($stmt->execute()) {
            header("Location: employees.php?msg=updated");
            exit;
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Edit employee</h2>

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

    <label>Role</label>
    <input type="text" name="role" value="<?= htmlspecialchars($role) ?>">


    <button type="submit">Update employee</button>
    <a class="btn btn-secondary" href="employees.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>