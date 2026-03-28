<?php
include '../db.php';

$errors = [];
$customer_id = '';
$employer_name = '';
$job_title = '';
$supervisor_name = '';
$supervisor_phone = '';
$employer_address = '';
$start_date = '';
$end_date = '';
$is_current = 0;

// Fetch customers for dropdown
$customers = [];
$result = $conn->query("SELECT customer_id, first_name, last_name 
                        FROM customer 
                        ORDER BY last_name, first_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = trim($_POST['customer_id'] ?? '');
    $employer_name = trim($_POST['employer_name'] ?? '');
    $job_title = trim($_POST['job_title'] ?? '');
    $supervisor_name = trim($_POST['supervisor_name'] ?? '');
    $supervisor_phone = trim($_POST['supervisor_phone'] ?? '');
    $employer_address = trim($_POST['employer_address'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $is_current = isset($_POST['is_current']) ? 1 : 0;

    // Validation
    if ($customer_id === '' || !ctype_digit($customer_id)) {
        $errors[] = "Please select a valid customer.";
    }
    if ($employer_name === '') {
        $errors[] = "Employer name is required.";
    }
    if ($job_title === '') {
        $errors[] = "Job title is required.";
    }
    if ($supervisor_name === '') {
        $errors[] = "Supervisor name is required.";
    }
    if ($supervisor_phone === '') {
        $errors[] = "Supervisor phone is required.";
    }
    if ($start_date === '') {
        $errors[] = "Start date is required.";
    }
    if (!$is_current && $end_date === '') {
        $errors[] = "End date is required if this job is not current.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO employment_history (
                customer_id,
                employer_name,
                job_title,
                supervisor_name,
                supervisor_phone,
                employer_address,
                start_date,
                end_date,
                is_current
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssssi",
            $customer_id,
            $employer_name,
            $job_title,
            $supervisor_name,
            $supervisor_phone,
            $employer_address,
            $start_date,
            $end_date,
            $is_current
        );

        if ($stmt->execute()) {
            header("Location: employmenthistory.php?msg=created");
            exit;
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../header.php';
?>

<h2>Add Employment History</h2>

<?php foreach ($errors as $error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form method="post">
    <label>Customer</label>
    <select name="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['customer_id'] ?>" <?= $cust['customer_id'] == $customer_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($cust['last_name'] . ', ' . $cust['first_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Company Name</label>
    <input type="text" name="employer_name" value="<?= htmlspecialchars($employer_name) ?>" required>

    <label>Job Title</label>
    <input type="text" name="job_title" value="<?= htmlspecialchars($job_title) ?>" required>

    <label>Supervisor Name</label>
    <input type="text" name="supervisor_name" value="<?= htmlspecialchars($supervisor_name) ?>" required>

    <label>Company Phone</label>
    <input type="text" name="supervisor_phone" value="<?= htmlspecialchars($supervisor_phone) ?>" required>

    <label>Company Address</label>
    <input type="text" name="employer_address" value="<?= htmlspecialchars($employer_address) ?>">

    <label>Start Date</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>

    <label>End Date</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">

    <label>
        <input type="checkbox" name="is_current" value="1" <?= $is_current ? 'checked' : '' ?>> Current Job
    </label>

    <button type="submit">Save Employment History</button>
    <a class="btn btn-secondary" href="employmenthistory.php">Cancel</a>
</form>

<?php
include '../footer.php';
$conn->close();
?>