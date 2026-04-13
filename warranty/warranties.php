<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        warranty_id,
        sale_id,
        vehicle_id,
        customer_id,
        employee_id,
        warranty_name,
        warranty_sale_date,
        start_date,
        length_months,
        cost,
        deductible,
        items_covered,
        total_cost,
        monthly_cost,
        warranty_commission
    FROM warranty
    ORDER BY warranty_id ASC
");
?>

<h2>Warranties</h2>

<a class="btn" href="warranty_create.php">Add New Warranty</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Warranty created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Warranty updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Warranty deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This warranty cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Warranty not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Warranty ID</th>
        <th>Sale ID</th>
        <th>Vehicle ID</th>
        <th>Customer ID</th>
        <th>Employee ID</th>
        <th>Warranty Name</th>
        <th>Warranty Sale Date</th>
        <th>Start Date</th>
        <th>Length Months</th>
        <th>Cost</th>
        <th>Deductible</th>
        <th>Items Covered</th>
        <th>Total Cost</th>
        <th>Monthly Cost</th>
        <th>Warranty Commission</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="16"><em>No warranties found.</em></td></tr>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['warranty_id']) ?></td>
            <td><?= htmlspecialchars($row['sale_id']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
            <td><?= htmlspecialchars($row['customer_id']) ?></td>
            <td><?= htmlspecialchars($row['employee_id']) ?></td>
            <td><?= htmlspecialchars($row['warranty_name']) ?></td>
            <td><?= htmlspecialchars($row['warranty_sale_date']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['length_months']) ?></td>
            <td><?= htmlspecialchars($row['cost']) ?></td>
            <td><?= htmlspecialchars($row['deductible']) ?></td>
            <td><?= htmlspecialchars($row['items_covered']) ?></td>
            <td><?= htmlspecialchars($row['total_cost']) ?></td>
            <td><?= htmlspecialchars($row['monthly_cost']) ?></td>
            <td><?= htmlspecialchars($row['warranty_commission']) ?></td>
            <td>
                <a class="btn" href="warranty_edit.php?id=<?= $row['warranty_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="warranty_delete.php?id=<?= $row['warranty_id'] ?>" onclick="return confirm('Are you sure you want to delete this warranty?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>