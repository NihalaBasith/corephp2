<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $file_id = $_POST['file_id'];
    $shared_user_id = $_POST['share_with']; 

   
    $query = "UPDATE files SET shared_with='$shared_user_id' WHERE id='$file_id' AND user_id='" . $_SESSION['user_id'] . "'";
    if (mysqli_query($conn, $query)) {
        echo "<p class='success'>File shared successfully!</p>";
    } else {
        echo "<p class='error'>Error sharing file: " . mysqli_error($conn) . "</p>";
    }
}


$user_query = "SELECT id, username FROM users";
$user_result = mysqli_query($conn, $user_query);

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Share File</title>
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

            select {
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            button {
                padding: 10px;
                background-color: #007bff; /* Button color */
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: #0056b3; /* Darker button color on hover */
            }

            .success {
                color: #28a745; /* Success message color */
                text-align: center;
            }

            .error {
                color: #dc3545; /* Error message color */
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
            <h1>Share File</h1>
            <form method="POST">
                <input type='hidden' name='file_id' value='<?php echo htmlspecialchars($file_id); ?>'>
                <label for="share_with">Share with (Name):</label>
                <select name='share_with' required>
                    <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
                        <option value='<?php echo htmlspecialchars($user['id']); ?>'><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php } ?>
                </select>
                <button type='submit'>Share</button>
            </form>
            <a class="cancel" href="dashboard.php">Cancel</a>
        </div>
    </body>
    </html>
    <?php
}
?>
