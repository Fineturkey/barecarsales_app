<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        vehicle_id,
        buyer_employee_id,
        seller_name,
        purchase_date,
        location,
        is_auction,
        price_paid
    FROM purchase
    ORDER BY purchase_id ASC
");
?>

<h2>purchase Table</h2>

<a class="btn" href="purchase_create.php">Add New purchase</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">purchase created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">purchase updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">purchase deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This purchase cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">purchase not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>purchase ID</th>
        <th>Vehicle ID</th>
        <th>Buyer Employee ID</th>
        <th>Seller Name</th>
        <th>Purchase Date</th>
        <th>Location</th>
        <th>Is auction</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['purchase_id']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
            <td><?= htmlspecialchars($row['buyer_employee_id']) ?></td>
            <td><?= htmlspecialchars($row['seller_name']) ?></td>
            <td><?= htmlspecialchars($row['purchase_date']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['price_paid']) ?></td>
            <td><?= $row['is_auction'] ? 'Yes' : 'No' ?></td>
            <td>
                <a class="btn" href="purchase_edit.php?id=<?= $row['purchase_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="purchase_delete.php?id=<?= $row['purchase_id'] ?>" onclick="return confirm('Are you sure you want to delete this purchase?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>