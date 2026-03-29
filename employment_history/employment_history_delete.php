<?php
include '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: employment_historys.php?msg=not_found");
    exit;
}

$stmt = $conn->prepare("DELETE FROM employment_history WHERE employment_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: employment_historys.php?msg=deleted");
        exit;
    } else {
        header("Location: employment_historys.php?msg=not_found");
        exit;
    }
} else {
    header("Location: employment_historys.php?msg=delete_blocked");
    exit;
}

$stmt->close();
$conn->close();
?>
