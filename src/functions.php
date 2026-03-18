<?php
/**
 * Shared functions for the Website project
 */

/**
 * Validates file extension and size
 */
function validateFile($file) {
    if (!isset($file) || $file["error"] != 0) {
        return ["status" => "error", "message" => "File upload error or no file provided."];
    }

    $file_size = $file["size"];
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    $allowed_exts = ["jpeg", "jpg", "png"];
    $max_size = 4 * 1024 * 1024; // 4MB

    if (!in_array($file_ext, $allowed_exts)) {
        return ["status" => "error", "message" => "Invalid file type. Only JPEG, JPG, and PNG are allowed."];
    }

    if ($file_size > $max_size) {
        return ["status" => "error", "message" => "File size exceeds 4MB limit."];
    }

    return ["status" => "success"];
}

/**
 * Handles the actual file move and naming
 */
function handleUpload($file, $target_dir = "public/uploads/") {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $new_file_name = time() . rand(1, 1000) . "_" . basename($file["name"]);
    $target_file = $target_dir . $new_file_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}

/**
 * Safely deletes a file from server
 */
function deleteFile($file_path) {
    if ($file_path && file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

/**
 * Server-side Regex for Email and Phone
 */
function validateInputs($email, $phone) {
    $emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    $phoneRegex = "/^\d{10}$/";

    if (!preg_match($emailRegex, $email)) {
        return ["status" => "error", "message" => "Invalid email format."];
    }
    if (!preg_match($phoneRegex, $phone)) {
        return ["status" => "error", "message" => "Phone number must be exactly 10 digits."];
    }
    return ["status" => "success"];
}
?>
