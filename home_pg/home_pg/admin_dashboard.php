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

// Handle add, edit, delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_place'])) {
        $area = $_POST['area'];
        $direction = $_POST['direction'];
        $images = $_POST['images'];
        $hyperlink = $_POST['hyperlink'];

        $stmt = $conn->prepare("INSERT INTO table2 (Area, Direction, Images, Hyperlink) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $area, $direction, $images, $hyperlink);
        $stmt->execute();
        $stmt->close();

        // Log the add action
        $admin_email = $_SESSION['admin_email'];
        $action = "Add Place";
        $details = "Added place: $area, Direction: $direction";
        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_email, action, details) VALUES (?, ?, ?)");
        $log_stmt->bind_param("sss", $admin_email, $action, $details);
        $log_stmt->execute();
        $log_stmt->close();

    } elseif (isset($_POST['edit_place'])) {
        $id = $_POST['id'];
        $area = $_POST['area'];
        $direction = $_POST['direction'];
        $images = $_POST['images'];
        $hyperlink = $_POST['hyperlink'];

        $stmt = $conn->prepare("UPDATE table2 SET Area=?, Direction=?, Images=?, Hyperlink=? WHERE id=?");
        $stmt->bind_param("ssssi", $area, $direction, $images, $hyperlink, $id);
        $stmt->execute();
        $stmt->close();

        // Log the edit action
        $admin_email = $_SESSION['admin_email'];
        $action = "Edit Place";
        $details = "Edited place ID: $id, New Area: $area, Direction: $direction";
        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_email, action, details) VALUES (?, ?, ?)");
        $log_stmt->bind_param("sss", $admin_email, $action, $details);
        $log_stmt->execute();
        $log_stmt->close();

    } elseif (isset($_POST['delete_place'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM table2 WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Log the delete action
        $admin_email = $_SESSION['admin_email'];
        $action = "Delete Place";
        $details = "Deleted place ID: $id";
        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_email, action, details) VALUES (?, ?, ?)");
        $log_stmt->bind_param("sss", $admin_email, $action, $details);
        $log_stmt->execute();
        $log_stmt->close();
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_direction = isset($_GET['direction']) ? $conn->real_escape_string($_GET['direction']) : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$where_clauses = [];
if ($search !== '') {
    $where_clauses[] = "(Area LIKE '%$search%' OR Images LIKE '%$search%' OR Hyperlink LIKE '%$search%')";
}
if ($filter_direction !== '' && in_array($filter_direction, ['North', 'South'])) {
    $where_clauses[] = "Direction = '$filter_direction'";
}

$where_sql = '';
if (count($where_clauses) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM table2 $where_sql";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total = $count_row['total'];
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM table2 $where_sql LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
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

    input[type="text"],
    select {
        width: 100%;
        padding: 6px;
        margin: 4px 0;
        box-sizing: border-box;
    }

    button {
        background-color: #3c00a0;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 4px;
    }

    button:hover {
        background-color: #290073;
    }

    form {
        margin-bottom: 20px;
    }
    </style>
    <style>
    /* Responsive styles */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
        }

        thead tr {
            display: none;
        }

        tr {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }

        td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: left;
        }

        td:before {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            font-weight: bold;
        }

        td:nth-of-type(1):before {
            content: "ID";
        }

        td:nth-of-type(2):before {
            content: "Area";
        }

        td:nth-of-type(3):before {
            content: "Direction";
        }

        td:nth-of-type(4):before {
            content: "Images";
        }

        td:nth-of-type(5):before {
            content: "Hyperlink";
        }

        td:nth-of-type(6):before {
            content: "Actions";
        }

        input[type="text"],
        select,
        button {
            width: 100% !important;
            margin-bottom: 10px;
        }

        form {
            margin-bottom: 30px;
        }
    }
    </style>
</head>

<body>
    <h1>Admin Dashboard</h1>

    <h2>Add New Place</h2>
    <form method="post">
        <label>Area:</label>
        <input type="text" name="area" required />
        <label>Direction:</label>
        <select name="direction" required>
            <option value="">Select Direction</option>
            <option value="North">North</option>
            <option value="South">South</option>
        </select>
        <label>Images URL:</label>
        <input type="text" name="images" required />
        <label>Hyperlink:</label>
        <input type="text" name="hyperlink" required />
        <button type="submit" name="add_place">Add Place</button>
    </form>

    <h2>Manage Places</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Area</th>
                <th>Direction</th>
                <th>Images</th>
                <th>Hyperlink</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <form method="post">
                    <td><?php echo $row['id']; ?><input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
                    </td>
                    <td><input type="text" name="area" value="<?php echo htmlspecialchars($row['Area']); ?>" required />
                    </td>
                    <td>
                        <select name="direction" required>
                            <option value="North" <?php if ($row['Direction'] == 'North') echo 'selected'; ?>>North
                            </option>
                            <option value="South" <?php if ($row['Direction'] == 'South') echo 'selected'; ?>>South
                            </option>
                        </select>
                    </td>
                    <td><input type="text" name="images" value="<?php echo htmlspecialchars($row['Images']); ?>"
                            required /></td>
                    <td><input type="text" name="hyperlink" value="<?php echo htmlspecialchars($row['Hyperlink']); ?>"
                            required /></td>
                    <td>
                        <button type="submit" name="edit_place">Edit</button>
                        <button type="submit" name="delete_place"
                            onclick="return confirm('Are you sure you want to delete this place?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination controls -->
    <div style="text-align:center; margin-bottom: 20px;">
        <?php if ($page > 1): ?>
        <a
            href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&direction=<?php echo urlencode($filter_direction); ?>">Previous</a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <?php if ($p == $page): ?>
        <strong><?php echo $p; ?></strong>
        <?php else: ?>
        <a
            href="?page=<?php echo $p; ?>&search=<?php echo urlencode($search); ?>&direction=<?php echo urlencode($filter_direction); ?>"><?php echo $p; ?></a>
        <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
        <a
            href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&direction=<?php echo urlencode($filter_direction); ?>">Next</a>
        <?php endif; ?>
    </div>

    <a href="admin_signin.php">Logout</a>
</body>

</html>