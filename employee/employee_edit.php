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
$role = $employee['role'];

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

    if ($phone === '') {
        $errors[] = "Phone number is required.";
    }

    if ($role === '') {
        $errors[] = "Role is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE employee
            SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                role = ?
            WHERE employee_id = ?
        ");

        $stmt->bind_param(
            "ssssi",
            $first_name,
            $last_name,
            $phone,
            $role,
            $id
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

<h2>Edit Employee</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>First Name</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>

    <label>Last Name</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

    <label>Role</label>
    <select name="role" required>
        <option value="">— Select role —</option>
        <option value="buyer" <?= $role === 'buyer' ? 'selected' : '' ?>>Buyer</option>
        <option value="salesperson" <?= $role === 'salesperson' ? 'selected' : '' ?>>Salesperson</option>
        <option value="both" <?= $role === 'both' ? 'selected' : '' ?>>Both</option>
    </select>


    <button type="submit">Update Employee</button>
    <a class="btn btn-secondary" href="employees.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>