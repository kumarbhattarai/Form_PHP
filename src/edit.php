<?php
require_once 'db.php';
require_once 'functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM form_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if (!$user) {
    die("User not found.");
}

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = htmlspecialchars($_POST['user_name']);
    $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
    $phone = $_POST['phone'];
    
    // 1. Validate Inputs
    $inputValidation = validateInputs($user_email, $phone);
    if ($inputValidation["status"] === "error") {
        $message = $inputValidation["message"];
        $status = "error";
    } else {
        $new_file_path = $user['file_url'];

        // 2. Handle File Update
        if (isset($_FILES["user_file"]) && $_FILES["user_file"]["error"] == 0) {
            $fileValidation = validateFile($_FILES["user_file"]);
            if ($fileValidation["status"] === "error") {
                $message = $fileValidation["message"];
                $status = "error";
            } else {
                deleteFile($user['file_url']); // Delete old
                $new_file_path = handleUpload($_FILES["user_file"]);
                if (!$new_file_path) {
                    $message = "Failed to upload new file.";
                    $status = "error";
                    $new_file_path = $user['file_url'];
                }
            }
        }

        // 3. Update Database
        if ($status !== "error") {
            $stmt = $conn->prepare("UPDATE form_data SET username = ?, email = ?, phone = ?, file_url = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $user_name, $user_email, $phone, $new_file_path, $id);

            if ($stmt->execute()) {
                $message = "User updated successfully! Redirecting...";
                $status = "success";
                $user['username'] = $user_name;
                $user['email'] = $user_email;
                $user['phone'] = $phone;
                $user['file_url'] = $new_file_path;
            } else {
                $message = "Error updating user: " . $conn->error;
                $status = "error";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-vh-100 min-h-screen m-0 font-sans">
    <div class="bg-white p-6 rounded-lg shadow-md w-96 flex flex-col gap-4">
        <div class="flex justify-between items-center mb-2">
            <h1 class="text-xl font-bold text-gray-800">Edit User #<?php echo $id; ?></h1>
            <a href="index2.php" class="text-sm text-blue-600 hover:underline">Back to List</a>
        </div>

        <?php if ($message): ?>
            <div class="p-3 text-sm rounded <?php echo $status === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
            <?php if ($status === 'success'): ?>
                <script>
                    setTimeout(() => {
                        window.location.href = "index2.php";
                    }, 2000);
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <form id="editForm" method="POST" enctype="multipart/form-data" onsubmit="return validateEditForm()" class="flex flex-col gap-4">
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-600 mb-1">Name</label>
                <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['username']); ?>" required class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-600 mb-1">Email</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span id="emailError" class="text-red-500 text-xs mt-1 hidden">Invalid email address.</span>
            </div>
            
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-600 mb-1">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="9800000001" required class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span id="phoneError" class="text-red-500 text-xs mt-1 hidden">Invalid 10-digit phone number.</span>
            </div>

            <div class="flex flex-col items-start">
                <label class="text-xs font-semibold text-gray-600 mb-1">Profile Image</label>
                <div class="flex items-center gap-4">
                    <?php if ($user['file_url']): ?>
                        <img src="<?php echo htmlspecialchars($user['file_url']); ?>" alt="Current Avatar" class="w-12 h-12 object-cover rounded-full border border-gray-200">
                    <?php endif; ?>
                    <label for="user_file" class="text-sm text-blue-600 underline cursor-pointer">Change Image</label>
                    <input type="file" name="user_file" id="user_file" hidden class="text-sm text-gray-600 cursor-pointer">
                </div>
            </div>

            <div class="text-xs text-gray-400 italic">
                Note: Location updates are not available in this edit mode.
            </div>

            <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors cursor-pointer">Update User</button>
        </form>
    </div>

    <script>
        function validateEditForm() {
            const email = document.getElementById("user_email").value;
            const phone = document.getElementById("phone").value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^\d{10}$/;
            let isValid = true;

            if (!emailRegex.test(email)) {
                document.getElementById("emailError").classList.remove("hidden");
                isValid = false;
            } else {
                document.getElementById("emailError").classList.add("hidden");
            }

            if (!phoneRegex.test(phone)) {
                document.getElementById("phoneError").classList.remove("hidden");
                isValid = false;
            } else {
                document.getElementById("phoneError").classList.add("hidden");
            }

            return isValid;
        }
    </script>
</body>
</html>
