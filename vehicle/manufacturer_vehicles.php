<?php
include '../db.php';
include '../header.php';

$manufacturer = '';
if (isset($_GET['manufacturer']) && $_GET['manufacturer'] !== '') {
    $raw = trim((string) $_GET['manufacturer']);
    // Allow only letters and spaces
    if (preg_match('/^[A-Za-z\s]+$/', $raw)) {
        $manufacturer = $raw;
    }
}

if ($manufacturer === '') {
    echo '<p class="message error">Please provide a manufacturer to search for.</p>';
    include '../footer.php';
    $conn->close();
    exit;
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
        current_status
    FROM vehicle
    WHERE make LIKE ?
    ORDER BY vehicle_id ASC
";

$stmt = $conn->prepare($select_sql);
$like_param = '%' . $manufacturer . '%';
$stmt->bind_param("s", $like_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Vehicles from <?= htmlspecialchars($manufacturer) ?> Manufacturer</h2>

<p class="message info">Showing vehicles from manufacturers matching "<?= htmlspecialchars($manufacturer) ?>" (independent of availability status).</p>

<a class="btn" href="vehicles.php">View All Vehicles</a>
<a class="btn" href="available_vehicles.php">Back to All Available Vehicles</a>

<?php if ($result->num_rows === 0): ?>
    <p class="message warning">No available vehicles found from "<?= htmlspecialchars($manufacturer) ?>".</p>
<?php else: ?>
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
<?php endif; ?>

<?php
include '../footer.php';
$stmt->close();
$conn->close();
?>