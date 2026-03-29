<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: sales.php?msg=not_found");
    exit;
}

$stmt = $conn->prepare("DELETE FROM sale WHERE sale_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: sales.php?msg=deleted");
        exit;
    } else {
        header("Location: sales.php?msg=not_found");
        exit;
    }
} else {
    header("Location: sales.php?msg=delete_blocked");
    exit;
}

$stmt->close();
$conn->close();
?>