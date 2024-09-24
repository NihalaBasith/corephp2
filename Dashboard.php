<?php
session_start();
include 'db.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];


$user_query = "SELECT username FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$username = $user['username'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (in_array($_FILES['file']['type'], $allowed_types) && $_FILES['file']['size'] <= $max_size) {
        $upload_dir = "uploads/$user_id/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['file']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
           
            $query = "INSERT INTO files (user_id, filename, file_path) VALUES ('$user_id', '$file_name', '$file_path')";
            mysqli_query($conn, $query);
            $_SESSION['upload_message'] = "File uploaded successfully!"; // Store success message
            header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page
            exit();
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "Invalid file type or size!";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_file'])) {
    $file_id = $_POST['file_id'];
    $delete_query = "DELETE FROM files WHERE id='$file_id' AND user_id='$user_id'";
    mysqli_query($conn, $delete_query);
    $_SESSION['upload_message'] = "File deleted successfully!"; 
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
   
}

function getFiles($conn, $user_id, $searchTerm = '') {
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm); 


    $file_query = "
        SELECT files.id, files.filename 
        FROM files 
        WHERE files.user_id = '$user_id'
    ";


    if (!empty($searchTerm)) {
        $file_query .= " AND files.filename LIKE '%$searchTerm%'";
    }

    // Execute the query
    $file_result = mysqli_query($conn, $file_query);
    
    return $file_result; 
}


$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$file_result = getFiles($conn, $user_id, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #007bff;
            color: white;
        }

        h1 {
            margin: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin: 20px 0 10px;
        }

        form {
            margin: 20px 0;
        }

        input[type="file"], input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background: #007BFF;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .file-list {
            width: 100%;
            border-collapse: collapse;
        }

        .file-list th, .file-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .file-list th {
            background-color: #f4f4f4;
        }

        .file-actions form {
            display: inline;
        }
        .logout {
            color: white;
            text-decoration: none;
        }

        .logout:hover {
            text-decoration: underline;
        }
        .message {
            background-color: #d4edda; /* Light green background */
            color: #155724; /* Dark green text */
            border: 1px solid #c3e6cb; /* Border color */
            padding: 10px;
            margin: 20px 0;
            text-align: center;
            border-radius: 5px;
            display: none; /* Hide initially */
        }

        .message.show {
            display: block; /* Show the message */
        }
        .files-header {
    display: flex; /* Use Flexbox to arrange items in a row */
    align-items: center; /* Center items vertically */
    justify-content: space-between; /* Space out items */
    padding: 20px; /* Add padding for better spacing */
}

.files-header h2 {
    margin: 0; /* Remove default margin */
    font-size: 24px; /* Adjust font size as needed */
    color: #333; /* Optional: set a color */
}

.shared-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.3s; 
}

.shared-button:hover {
    background-color: #45a049; 
    transform: translateY(-2px); 
}

.shared-button:active {
    transform: translateY(0); /* Reset lift on click */
}

    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <a href="logout.php" class="logout">Logout</a>
    </header>

    <div class="container">
    <?php if (isset($_SESSION['upload_message'])): ?>
            <div class="message show">
                <?php 
                echo htmlspecialchars($_SESSION['upload_message']);
                unset($_SESSION['upload_message']); 
                ?>
            </div>
        <?php endif; ?>
        <div class="search-upload">
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <button type="submit">Upload</button>
            </form>

            <!-- Search Form -->
            <form method="GET">
                <input type="text" name="search" placeholder="Search for files..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
<div>
<div class="files-header">
    <h2>My Files</h2>
    <a href="sharewithme.php" class="shared-button">Shared with Me</a>
</div>

        <br>
    </div>
        <table class="file-list">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Filename</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($file_result) > 0) {
                    $i = 1;
                    while ($file = mysqli_fetch_assoc($file_result)) {
                        echo "<tr>";
                        echo "<td>$i</td>";
                        echo "<td>" . htmlspecialchars($file['filename']) . "</td>";
                        echo "<td class='file-actions'>";

                      
                        echo "<form action='view_file.php' method='GET' style='display: inline;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($file['id']) . "'>
                        <button type='submit' class='view-btn'>View</button>
                        </form>";


                       
                        echo "<form action='Rename.php' method='GET' style='display: inline;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($file['id']) . "'>
                                <button type='submit' class='rename-btn'>Rename</button>
                              </form>";

                       
                        echo "<form method='POST' style='display: inline;'>
                                <input type='hidden' name='file_id' value='" . htmlspecialchars($file['id']) . "'>
                                <button type='submit' name='delete_file' class='delete-btn'>Delete</button>
                              </form>";

                        
                        echo "<form action='share.php' method='GET' style='display: inline;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($file['id']) . "'>
                                <button type='submit' class='share-btn'>Share</button>
                              </form>";

                        echo "</td>";
                        echo "</tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='3'>No files found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
