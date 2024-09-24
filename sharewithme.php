<?php
session_start();
include('db.php'); 

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];


$query = "
    SELECT f.*, u.username AS shared_by 
    FROM files f 
    JOIN users u ON f.shared_with = u.id 
    WHERE u.id = '$user_id'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files Shared With Me</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external stylesheet -->
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
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
        .view-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #007bff; /* Bootstrap primary color */
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    transition: background-color 0.3s, transform 0.3s; /* Smooth transition */
}

.view-button:hover {
    background-color: #0056b3; /* Darker shade on hover */
    transform: translateY(-2px); /* Slight lift effect */
}

.view-button:active {
    transform: translateY(0); /* Reset lift on click */
}
.header-container {
    display: flex; /* Use Flexbox to arrange items in a row */
    align-items: center; /* Center items vertically */
    justify-content: space-between; /* Space out items */
    padding: 20px; /* Add padding for better spacing */
}

.header-container h1 {
    margin: 0; /* Remove default margin */
    font-size: 24px; /* Adjust font size as needed */
    color: #333; /* Optional: set a color */
}

.back-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff; /* Bootstrap primary color */
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.3s; /* Smooth transition */
}

.back-button:hover {
    background-color: #0056b3; /* Darker shade on hover */
    transform: translateY(-2px); /* Slight lift effect */
}

.back-button:active {
    transform: translateY(0); /* Reset lift on click */
}

    </style>
</head>
<body>
    <div class="container">
    <div class="header-container">
    <h1>Files Shared With Me</h1>
    <a href="dashboard.php" class="back-button">Back to Dashboard</a>
</div>


        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Shared By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td><?php echo htmlspecialchars($file['shared_by']);  ?></td>
                            <td>
    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="view-button">View</a>
</td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No files shared with you.</p>
        <?php endif; ?>
        
       
    </div>
</body>
</html>
