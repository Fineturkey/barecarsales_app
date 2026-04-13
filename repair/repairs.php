<?php
include '../db.php';
include '../header.php';

$result = $conn->query("SELECT repair_id, purchase_id, problem_description, est_repair_cost, actual_cost FROM repair ORDER BY repair_id ASC");
?>

<h2>Repairs</h2>

<a class="btn" href="repair_create.php">Add New Repair</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Repair created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Repair updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Repair deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This repair cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Repair not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Repair ID</th>
        <th>Purchase ID</th>
        <th>Problem Description</th>
        <th>Estimated Repair Cost</th>
        <th>Actual Cost</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="6"><em>No repairs found.</em></td></tr>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['repair_id']) ?></td>
            <td><?= htmlspecialchars($row['purchase_id']) ?></td>
            <td><?= htmlspecialchars($row['problem_description']) ?></td>
            <td><?= htmlspecialchars($row['est_repair_cost']) ?></td>
            <td><?= htmlspecialchars($row['actual_cost']) ?></td>
            <td>
                <a class="btn" href="repair_edit.php?id=<?= $row['repair_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="repair_delete.php?id=<?= $row['repair_id'] ?>" onclick="return confirm('Are you sure you want to delete this repair?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>