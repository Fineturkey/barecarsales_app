<?php
include '../db.php';
include '../header.php';

// Fetch all payments
$result = $conn->query("
    SELECT p.payment_id, p.customer_id, c.first_name, c.last_name,
           p.sale_id, s.sale_price, p.amount, p.payment_date, p.due_date, p.paid_date
    FROM payment p
    JOIN customer c ON p.customer_id = c.customer_id
    JOIN sale s ON p.sale_id = s.sale_id
    ORDER BY p.payment_date DESC
");
?>

<h2>Payments</h2>

<a class="btn" href="payment_create.php">Add Payment</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Payment created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Payment updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Payment deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Payment not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Customer</th>
        <th>Sale ID</th>
        <th>Sale Price</th>
        <th>Amount</th>
        <th>Payment Date</th>
        <th>Due Date</th>
        <th>Paid Date</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['customer_id'] . ' - ' . $row['last_name'] . ', ' . $row['first_name']) ?></td>
            <td><?= $row['sale_id'] ?></td>
            <td>$<?= number_format($row['sale_price'], 2) ?></td>
            <td>$<?= number_format($row['amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['payment_date']) ?></td>
            <td><?= htmlspecialchars($row['due_date']) ?></td>
            <td><?= htmlspecialchars($row['paid_date'] ?? 'Pending') ?></td>
            <td>
                <a class="btn" href="payment_edit.php?id=<?= $row['payment_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="payment_delete.php?id=<?= $row['payment_id'] ?>" onclick="return confirm('Are you sure you want to delete this payment?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>
