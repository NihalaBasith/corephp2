<?php
session_start();
include 'db.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle rename logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $file_id = $_POST['file_id'];
    $new_name = mysqli_real_escape_string($conn, $_POST['new_name']);

    // Update filename in the database
    $query = "UPDATE files SET filename='$new_name' WHERE id='$file_id' AND user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        header('Location: dashboard.php'); // Redirect back to the manage files page
        exit();
    } else {
        echo "<p class='error'>Error renaming file: " . mysqli_error($conn) . "</p>";
    }
}

// Get file ID from the query string
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Fetch the current filename
    $file_query = "SELECT filename FROM files WHERE id='$file_id' AND user_id='$user_id'";
    $file_result = mysqli_query($conn, $file_query);
    $file = mysqli_fetch_assoc($file_result);

    if ($file) {
        $current_name = $file['filename'];
    } else {
        echo "<p class='error'>File not found.</p>";
        exit();
    }
} else {
    echo "<p class='error'>No file specified.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rename File</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to an external stylesheet -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: #dc3545;
            text-align: center;
        }

        .cancel {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .cancel:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rename File</h1>
        <form method="POST">
            <input type="hidden" name="file_id" value="<?php echo htmlspecialchars($file_id); ?>">
            <label for="new_name">New Name:</label>
            <input type="text" name="new_name" value="<?php echo htmlspecialchars($current_name); ?>" required>
            <button type="submit">Rename</button>
        </form>
        <a class="cancel" href="dashboard.php">Cancel</a>
    </div>
</body>
</html>
