<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        customer_id,
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
    ORDER BY customer_id ASC
");
?>

<h2>Customer Table</h2>

<a class="btn" href="customer_create.php">Add New Customer</a>
<a class="btn" href="/barecarsales_app/employment_history/employment_historys.php">Manage Customer Employment History</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Customer created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Customer updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Customer deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This customer cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Customer not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Customer ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>Zip</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Taxpayer ID</th>
        <th>Late Payment Count</th>
        <th>Avg Days Late</th>
        <th>Reported to Credit Bureau</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['customer_id']) ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td><?= htmlspecialchars($row['state']) ?></td>
            <td><?= htmlspecialchars($row['zip']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
            <td><?= htmlspecialchars($row['taxpayer_id']) ?></td>
            <td><?= htmlspecialchars($row['late_payment_count']) ?></td>
            <td><?= htmlspecialchars($row['avg_days_late']) ?></td>
            <td><?= $row['reported_to_credit_bureau'] ? 'Yes' : 'No' ?></td>
            <td>
                <a class="btn" href="customer_edit.php?id=<?= $row['customer_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="customer_delete.php?id=<?= $row['customer_id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>