<?php
// Configuration and database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "musician_profiles";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS profiles (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    age INT(3) NOT NULL,
    email VARCHAR(100) NOT NULL,
    is_professional ENUM('yes', 'no') NOT NULL,
    primary_instrument VARCHAR(50) NOT NULL,
    secondary_instruments TEXT,
    years_of_experience INT(3) NOT NULL,
    experience_description TEXT,
    skill_level INT(2) NOT NULL,
    next_performance DATE,
    website VARCHAR(255) NOT NULL,
    demo_file VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    // Table created successfully or already exists
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle file upload
    $target_dir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Process the uploaded file
    $demo_file = "";
    if (isset($_FILES["demo"]) && $_FILES["demo"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["demo"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if file is an allowed format
        if ($fileType == "mp3" || $fileType == "wav") {
            // Check file size (10MB limit)
            if ($_FILES["demo"]["size"] <= 10000000) {
                if (move_uploaded_file($_FILES["demo"]["tmp_name"], $target_file)) {
                    $demo_file = basename($_FILES["demo"]["name"]);
                }
            }
        }
    }
    
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $age = (int)$_POST["age"];
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $status = mysqli_real_escape_string($conn, $_POST["status"]);
    $instrument = mysqli_real_escape_string($conn, $_POST["instrument"]);
    $years = (int)$_POST["years"];
    $experience = mysqli_real_escape_string($conn, $_POST["experience"]);
    $skill_level = (int)$_POST["skill_level"];
    $website = mysqli_real_escape_string($conn, $_POST["website"]);
    
    // Handle the next_performance date (optional field)
    $next_performance = !empty($_POST["next_performance"]) ? 
        "'" . mysqli_real_escape_string($conn, $_POST["next_performance"]) . "'" : "NULL";
    
    // Handle secondary instruments (checkboxes)
    $secondary_instruments = isset($_POST["other_instruments"]) ? 
        implode(",", $_POST["other_instruments"]) : "";
    $secondary_instruments = mysqli_real_escape_string($conn, $secondary_instruments);
    
    // Check if we're updating an existing record
    if (isset($_POST["id"]) && !empty($_POST["id"])) {
        $id = (int)$_POST["id"];
        
        // Update existing record
        $sql = "UPDATE profiles SET 
                full_name = '$name',
                age = $age,
                email = '$email',
                is_professional = '$status',
                primary_instrument = '$instrument',
                secondary_instruments = '$secondary_instruments',
                years_of_experience = $years,
                experience_description = '$experience',
                skill_level = $skill_level,
                next_performance = $next_performance,
                website = '$website'";
        
        // Only update the demo file if a new one was uploaded
        if (!empty($demo_file)) {
            $sql .= ", demo_file = '$demo_file'";
        }
        
        $sql .= " WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating profile: " . mysqli_error($conn)]);
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO profiles (
                full_name, age, email, is_professional, 
                primary_instrument, secondary_instruments, years_of_experience, 
                experience_description, skill_level, next_performance, 
                website, demo_file
            ) VALUES (
                '$name', $age, '$email', '$status', 
                '$instrument', '$secondary_instruments', $years, 
                '$experience', $skill_level, $next_performance, 
                '$website', '$demo_file'
            )";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Profile submitted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
        }
    }
} 
// Handle GET requests for listing, deletion, or fetching a profile
else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Delete a record
    if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
        $id = (int)$_GET["id"];
        $sql = "DELETE FROM profiles WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: " . $_SERVER["PHP_SELF"] . "?action=list");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }
    // Fetch a single record for editing
    else if (isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["id"])) {
        $id = (int)$_GET["id"];
        $sql = "SELECT * FROM profiles WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $profile = mysqli_fetch_assoc($result);
            echo json_encode(["success" => true, "profile" => $profile]);
        } else {
            echo json_encode(["success" => false, "message" => "Profile not found"]);
        }
    }
    // List all records
    else if (isset($_GET["action"]) && $_GET["action"] == "list") {
        $sql = "SELECT * FROM profiles";
        $result = mysqli_query($conn, $sql);
        
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Musician Profiles</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #1a1a1a;
                    color: #d7c7b2;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }
                h1 {
                    color: #ff9100;
                    text-align: center;
                    margin-bottom: 30px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                    background-color: #2a2a2a;
                    border-radius: 8px;
                    overflow: hidden;
                }
                th, td {
                    padding: 12px 15px;
                    text-align: left;
                    border-bottom: 1px solid #444;
                }
                th {
                    background-color: #333;
                    color: #ff9100;
                    font-weight: bold;
                }
                tr:hover {
                    background-color: #3a3a3a;
                }
                .actions {
                    display: flex;
                    gap: 10px;
                }
                .btn {
                    display: inline-block;
                    padding: 8px 12px;
                    background-color: #ff9100;
                    color: #1a1a1a;
                    border: none;
                    border-radius: 4px;
                    text-decoration: none;
                    cursor: pointer;
                    font-weight: bold;
                }
                .btn-danger {
                    background-color: #ff4d4d;
                }
                .btn-primary {
                    background-color: #4285f4;
                }
                .add-new {
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Musician Profiles</h1>
                <div class='add-new'>
                    <a href='index.html' class='btn btn-primary'>Add New Profile</a>
                </div>";
                
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Professional</th>
                    <th>Primary Instrument</th>
                    <th>Years Experience</th>
                    <th>Actions</th>
                </tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>" . $row['id'] . "</td>
                    <td>" . htmlspecialchars($row['full_name']) . "</td>
                    <td>" . $row['age'] . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . $row['is_professional'] . "</td>
                    <td>" . htmlspecialchars($row['primary_instrument']) . "</td>
                    <td>" . $row['years_of_experience'] . "</td>
                    <td class='actions'>
                        <a href='index.html?id=" . $row['id'] . "' class='btn'>Edit</a>
                        <a href='musician_profile.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this profile?');\">Delete</a>
                    </td>
                </tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No profiles found.</p>";
        }
        
        echo "</div>
        </body>
        </html>";
    }
    // Get a specific profile for the form
    else if (isset($_GET["id"])) {
        $id = (int)$_GET["id"];
        $sql = "SELECT * FROM profiles WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $profile = mysqli_fetch_assoc($result);
            echo json_encode(["success" => true, "profile" => $profile]);
        } else {
            echo json_encode(["success" => false, "message" => "Profile not found"]);
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>
