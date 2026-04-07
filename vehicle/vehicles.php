<?php
include '../db.php';
include '../header.php';

$max_miles = null;
if (isset($_GET['max_miles']) && $_GET['max_miles'] !== '') {
    $raw = trim((string) $_GET['max_miles']);
    if (ctype_digit($raw) && (int) $raw > 0) {
        $max_miles = (int) $raw;
    }
}

$select_sql = "
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
        current_status,
        has_warranty
    FROM vehicle
";

if ($max_miles !== null) {
    $stmt = $conn->prepare($select_sql . " WHERE miles IS NOT NULL AND miles < ? ORDER BY vehicle_id ASC");
    $stmt->bind_param("i", $max_miles);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($select_sql . " ORDER BY vehicle_id ASC");
}
?>

<h2>Vehicle Table</h2>

<a class="btn" href="vehicle_create.php">Add New vehicle</a>

<form method="get" action="vehicles.php" style="margin: 1rem 0; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
    <label for="max_miles">Show vehicles under</label>
    <input type="number" id="max_miles" name="max_miles" min="1" step="1"
        value="<?= $max_miles !== null ? htmlspecialchars((string) $max_miles) : '' ?>"
        placeholder="e.g. 50000" style="width: 8rem;">
    <span>miles</span>
    <button type="submit" class="btn">Apply</button>
    <?php if ($max_miles !== null): ?>
        <a class="btn" href="vehicles.php">Clear filter</a>
    <?php endif; ?>
</form>

<?php if ($max_miles !== null): ?>
    <p class="message success" style="margin-bottom: 1rem;">Showing vehicles with mileage under <?= htmlspecialchars((string) $max_miles) ?>.</p>
<?php endif; ?>

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
        <th>Vehicle ID</th>
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
        <th>Warranty</th>
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
            <td><?= $row['has_warranty'] ? 'Yes' : 'No' ?></td>
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
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>