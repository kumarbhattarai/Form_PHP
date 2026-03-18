<?php
require_once 'db.php';
require_once 'functions.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Fetch record to get file path
    $stmt = $conn->prepare("SELECT file_url FROM form_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        deleteFile($row['file_url']); // Shared deletion logic
    }
    $stmt->close();

    // 2. Delete record from database
    $stmt = $conn->prepare("DELETE FROM form_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index2.php?status=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: index2.php");
}

$conn->close();
?>
