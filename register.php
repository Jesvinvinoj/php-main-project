<?php
$servername = "your_servername";
$username = "your_username";
$password = "your_password";
$dbname = "your_dbname";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "Error: Email already exists.";
        exit();
    }

    // File upload handling
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
    if ($check === false) {
        echo "Error: File is not an image.";
        exit();
    }

    // Check file size
    if ($_FILES["profileImage"]["size"] > 500000) {
        echo "Error: File is too large.";
        exit();
    }

    // Allow certain file formats
    $allowedFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
        exit();
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Error: Sorry, your file was not uploaded.";
        exit();
    } else {
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            echo "The file ". basename( $_FILES["profileImage"]["name"]). " has been uploaded.";
        } else {
            echo "Error: Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert user data into the database
    $insertUserQuery = "INSERT INTO users (email, password, profile_image) VALUES ('$email', '$hashedPassword', '$targetFile')";

    if (mysqli_query($conn, $insertUserQuery)) {
        // Registration successful
        header("Location: index.html"); // Redirect to home page
        exit();
    } else {
        // Registration failed
        echo "Error: " . $insertUserQuery . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
