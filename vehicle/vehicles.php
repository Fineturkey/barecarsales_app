<?php
include '../db.php';
include '../header.php';

$result = $conn->query("
    SELECT
        vehicle_id,
        vin,
        make,
        model,
        year,
        color,
        miles,
        vehicle_condition,
        book_price,
        style,
        interior_color,
        current_status
    FROM vehicle
    ORDER BY vehicle_id ASC
");
?>

<h2>vehicle Table</h2>

<a class="btn" href="vehicle_create.php">Add New vehicle</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">vehicle created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">vehicle updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">vehicle deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_blocked'): ?>
        <div class="message error">This vehicle cannot be deleted because related records exist in another table.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">vehicle not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>vehicle ID</th>
        <th>VIN</th>
        <th>Make</th>
        <th>Model</th>
        <th>Year</th>
        <th>Color</th>
        <th>Miles</th>
        <th>Condition</th>
        <th>Book Price</th>
        <th>Style</th>
        <th>Interior Color</th>
        <th>Current Status</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
            <td><?= htmlspecialchars($row['vin']) ?></td>
            <td><?= htmlspecialchars($row['make']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['year']) ?></td>
            <td><?= htmlspecialchars($row['color']) ?></td>
            <td><?= htmlspecialchars($row['miles']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_condition']) ?></td>
            <td><?= htmlspecialchars($row['book_price']) ?></td>
            <td><?= htmlspecialchars($row['style']) ?></td>
            <td><?= htmlspecialchars($row['interior_color']) ?></td>
            <td><?= htmlspecialchars($row['current_status']) ?></td>
            <td>
                <a class="btn" href="vehicle_edit.php?id=<?= $row['vehicle_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="vehicle_delete.php?id=<?= $row['vehicle_id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this Vehicle?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>