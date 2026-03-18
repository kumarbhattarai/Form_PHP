<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $user_name = htmlspecialchars($_POST['user_name']);
    $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
    $phone = $_POST['phone'];
    $utm_source = $_POST['utm_source'] ?? '';
    $utm_medium = $_POST['utm_medium'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $ip = $_POST['ip'] ?? '';

    $response = ["status" => "error", "message" => "An unknown error occurred."];

    // 1. Validate Inputs (Email/Phone)
    $inputValidation = validateInputs($user_email, $phone);
    if ($inputValidation["status"] === "error") {
        echo json_encode($inputValidation);
        exit;
    }

    // 2. Handle File Upload
    $file_path = "";
    if (isset($_FILES["user_file"]) && $_FILES["user_file"]["error"] != 4) {
        $fileValidation = validateFile($_FILES["user_file"]);
        if ($fileValidation["status"] === "error") {
            echo json_encode($fileValidation);
            exit;
        }

        $file_path = handleUpload($_FILES["user_file"]);
        if (!$file_path) {
            echo json_encode(["status" => "error", "message" => "Failed to save file."]);
            exit;
        }
    }

    // 3. Insert into DB
    $stmt = $conn->prepare("INSERT INTO form_data (username, email, phone, utm_source, utm_medium, city, country, ip, file_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $user_name, $user_email, $phone, $utm_source, $utm_medium, $city, $country, $ip, $file_path);

    if ($stmt->execute()) {
        require_once 'services/send_mail.php';
        sendConfirmationEmail($user_email, $user_name, $phone, $file_path);
        // Submit to Formspree API
        $formspree_url = "https://formspree.io/f/xknlowgr";
        $ch = curl_init($formspree_url);
        
        $formspree_data = [
            'email' => $user_email,
            'phone' => $phone,
            'Fname' => $user_name,
            'description'=>[$utm_source,$utm_medium],
            'Location' => $country,
            'address' => $city,
            'ip' => $ip,
            'file' => $file_path
        ];
        
        // Formspree requires JSON payload
        $payload = json_encode($formspree_data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request and close connection
        curl_exec($ch);
        curl_close($ch);

        $response = ["status" => "success", "message" => "Form submitted successfully! Redirecting..."];
    } else {
        $response["message"] = "Database error: " . $stmt->error;
    }
    
    echo json_encode($response);
    $stmt->close();
}
$conn->close();
?>