<?php
session_start();
include('db.php'); // Include your database connection file

if (isset($_GET['id'])) {
    $file_id = intval($_GET['id']); // Get the file ID and ensure it's an integer

    // Fetch the file details from the database
    $query = "SELECT * FROM files WHERE id = $file_id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $file = mysqli_fetch_assoc($result);
        $file_path = htmlspecialchars($file['file_path']); // Path to the uploaded file

        // Check if file exists
        if (file_exists($file_path)) {
            echo "<h1>View Image</h1>";
            echo "<img src='$file_path' alt='Image' style='max-width: 100%; height: auto;'>"; // Display the image
            echo "<br><a href='javascript:history.back()'>Go Back</a>"; // Link to go back to the previous page
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid file ID.";
    }
} else {
    echo "No file ID provided.";
}
?>
<style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f9f9f9;
    }

    img {
        max-width: 80%;
        height: auto;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    a {
        margin-top: 20px;
        text-decoration: none;
        color: #007bff;
    }
</style>

