<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        employee_id,
        first_name,
        last_name,
        phone,
        role
    FROM employee
    ORDER BY employee_id ASC
");
?>

<h2>employee Table</h2>

<a class="btn" href="employee_create.php">Add New employee</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">employee created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">employee updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">employee deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This employee cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">employee not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>employee ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Role</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['employee_id']) ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <a class="btn" href="employee_edit.php?id=<?= $row['employee_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="employee_delete.php?id=<?= $row['employee_id'] ?>" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>