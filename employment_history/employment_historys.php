<?php
include '../db.php';
include '../header.php';

// Fetch employment history entries
$result = $conn->query("
    SELECT eh.employment_id, eh.customer_id, c.first_name, c.last_name,
           eh.employer_name, eh.job_title, eh.supervisor_name, eh.supervisor_phone, eh.employer_address,
           eh.start_date, eh.end_date, eh.is_current
    FROM employment_history eh
    JOIN customer c ON eh.customer_id = c.customer_id
    ORDER BY eh.employment_id ASC
");
?>

<h2>Customer Employment History</h2>

<a class="btn" href="employment_history_create.php">Add Customer Employment</a>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'created'): ?>
        <div class="message success">Employment history created successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="message success">Employment history updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="message success">Employment history deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'not_found'): ?>
        <div class="message error">Entry not found.</div>
    <?php endif; ?>
<?php endif; ?>

<table>
    <tr>
        <th>Customer</th>
        <th>Employer Name</th>
        <th>Job Title</th>
        <th>Supervisor Name</th>
        <th>Supervisor Phone</th>
        <th>Employer Address</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Current Job</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['employer_name']) ?></td>
            <td><?= htmlspecialchars($row['job_title']) ?></td>
            <td><?= htmlspecialchars($row['supervisor_name']) ?></td>
            <td><?= htmlspecialchars($row['supervisor_phone']) ?></td>
            <td><?= htmlspecialchars($row['employer_address']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td><?= $row['is_current'] ? 'Yes' : 'No' ?></td>
            <td>
                <a class="btn" href="employment_history_edit.php?id=<?= $row['employment_id'] ?>">Edit</a>
                <a class="btn btn-danger" href="employment_history_delete.php?id=<?= $row['employment_id'] ?>" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
include '../footer.php';
$conn->close();
?>