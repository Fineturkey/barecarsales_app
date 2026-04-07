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
        CASE
            WHEN v.vehicle_id IS NULL THEN CONCAT('MISSING VEHICLE (ID ', s.vehicle_id, ')')
            ELSE CONCAT(v.make, ' ', v.model, ' ', v.year)
        END AS vehicle_label,
        COALESCE(c.customer_id, s.customer_id) AS customer_id,
        CASE
            WHEN c.customer_id IS NULL THEN 'MISSING CUSTOMER'
            ELSE TRIM(CONCAT(c.first_name, ' ', c.last_name))
        END AS customer_name,
        COALESCE(e.employee_id, s.salesperson_employee_id) AS salesperson_id,
        CASE
            WHEN e.employee_id IS NULL THEN 'MISSING EMPLOYEE'
            ELSE TRIM(CONCAT(e.first_name, ' ', e.last_name))
        END AS salesperson_name
    FROM sale s
    LEFT JOIN vehicle v ON s.vehicle_id = v.vehicle_id
    LEFT JOIN customer c ON s.customer_id = c.customer_id
    LEFT JOIN employee e ON s.salesperson_employee_id = e.employee_id
    ORDER BY s.sale_id ASC
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
