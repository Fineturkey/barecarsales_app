<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        s.sale_id,
        s.sale_date,
        s.total_due,
        s.down_payment,
        s.financed_amount,
        s.sale_price,
        s.salesperson_commission,
        CONCAT(v.make, ' ', v.model, ' ', v.year) AS vehicle_label,
        c.customer_id,
        TRIM(CONCAT(c.first_name, ' ', c.last_name)) AS customer_name,
        e.employee_id AS salesperson_id,
        TRIM(CONCAT(e.first_name, ' ', e.last_name)) AS salesperson_name
    FROM sale s
    INNER JOIN vehicle v ON s.vehicle_id = v.vehicle_id
    INNER JOIN customer c ON s.customer_id = c.customer_id
    INNER JOIN employee e ON s.salesperson_employee_id = e.employee_id
    ORDER BY s.sale_id ASC
");
?>

<h2>Sales</h2>

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
        <th>Vehicle</th>
        <th>Customer</th>
        <th>Salesperson</th>
        <th>Sale Date</th>
        <th>Total Due</th>
        <th>Down Payment</th>
        <th>Financed Amount</th>
        <th>Sale Price</th>
        <th>Salesperson Commission</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="11"><em>No sales found.</em></td></tr>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['sale_id']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_label']) ?></td>
            <td><?= htmlspecialchars($row['customer_id'] . ' - ' . $row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['salesperson_id'] . ' - ' .$row['salesperson_name']) ?></td>
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
