<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    
   
    $query = "SELECT * FROM files WHERE id='$file_id' AND user_id='" . $_SESSION['user_id'] . "'";
    $result = mysqli_query($conn, $query);
    $file = mysqli_fetch_assoc($result);

    if ($file) {
       
        unlink($file['file_path']);
        
        
        $query = "DELETE FROM files WHERE id='$file_id'";
        mysqli_query($conn, $query);

        header('Location: manage_files.php');
    } else {
        echo "File not found.";
    }
}
?>
