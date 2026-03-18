<?php
require_once 'db.php';

$sql = "SELECT * FROM form_data ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Lists</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8">
    <div class="max-w-[98%] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-800">User Lists</h1>
            <div class="flex gap-2">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                    <span class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm self-center">User deleted successfully!</span>
                <?php endif; ?>
                <a href="index.html" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add New User</a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">profile</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['id']; ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <span class="block truncate max-w-[150px]" title="<?php echo htmlspecialchars($row['city'] . ', ' . $row['country']); ?>">
                                        <?php echo htmlspecialchars($row['city']); ?>, <?php echo htmlspecialchars($row['country']); ?>
                                    </span>
                                    <span class="text-xs text-gray-400"><?php echo htmlspecialchars($row['ip']); ?></span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-blue-600 ">
                                    <?php if ($row['file_url']): ?>
                                        <a href="<?php echo htmlspecialchars($row['file_url']); ?>"><img  src="<?php echo htmlspecialchars($row['file_url']); ?>" alt="" class="w-12 h-12 object-cover rounded-full shadow-sm"></a>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">No file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['created_at']; ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
