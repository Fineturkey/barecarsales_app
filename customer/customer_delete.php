<?php
die("THIS IS THE REAL customer_delete.php");
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: customers.php?msg=not_found");
    exit;
}

echo "Runing delete on customer for ID = " . $id;
exit;

$stmt = $conn->prepare("DELETE FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: customers.php?msg=deleted");
        exit;
    } else {
        header("Location: customers.php?msg=not_found");
        exit;
    }
} else {
    header("Location: customers.php?msg=delete_blocked");
    exit;
}

$stmt->close();
$conn->close();
?>