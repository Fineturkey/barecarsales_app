<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        sale_id,
        vehicle_id,
        customer_id,
        salesperson_employee_id,
        sale_date,
        total_due,
        down_payment,
        financed_amount,
        sale_price,
        salesperson_commission
    FROM sale
    ORDER BY sale_id ASC
");
?>

<h2>Sale Table</h2>

<a class="btn" href="sale_create.php">Create New Sale</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Sale created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Sale updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Sale deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This sale cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Sale not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Sale ID</th>
        <th>Vehicle ID</th>
        <th>Customer ID</th>
        <th>Salesperson ID</th>
        <th>Sale Date</th>
        <th>Total Due</th>
        <th>Down Payment</th>
        <th>Financed Amount</th>
        <th>Sale Price</th>
        <th>Salesperson Commission</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['sale_id']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
            <td><?= htmlspecialchars($row['customer_id']) ?></td>
            <td><?= htmlspecialchars($row['salesperson_employee_id']) ?></td>
            <td><?= htmlspecialchars($row['sale_date']) ?></td>
            <td><?= htmlspecialchars($row['total_due']) ?></td>
            <td><?= htmlspecialchars($row['down_payment']) ?></td>
            <td><?= htmlspecialchars($row['financed_amount']) ?></td>
            <td><?= htmlspecialchars($row['sale_price']) ?></td>
            <td><?= htmlspecialchars($row['salesperson_commission']) ?></td>
            <td>
                <a class="btn" href="sale_edit.php?id=<?= $row['sale_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="sale_delete.php?id=<?= $row['sale_id'] ?>" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>