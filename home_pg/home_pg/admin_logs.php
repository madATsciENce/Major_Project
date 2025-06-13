<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_signin.php");
    exit();
}

// Check role for access control
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] != 'superadmin') {
    echo "Access denied. You do not have sufficient permissions.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch logs with pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) as total FROM admin_logs");
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM admin_logs ORDER BY timestamp DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Logs</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #3c00a0;
        color: white;
    }

    a {
        color: #3c00a0;
        text-decoration: none;
        margin: 0 5px;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <h1>Admin Activity Logs</h1>
    <table>
        <thead>
            <tr>
                <th>Admin Email</th>
                <th>Action</th>
                <th>Details</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['admin_email']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>

</html>