<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$database = "project"; // Update this to your actual database name

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    
}
echo "Connected successfully";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the uploaded CSV file (remove empty rows and columns)
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
        $csvData = file_get_contents($_FILES["csvFile"]["tmp_name"]);
        $csvData = processCSV($csvData);

        // Debugging output
        echo "CSV Data:<br>";
        echo $csvData;

        // Store the processed CSV data in a database table
        $sql = "CREATE TABLE IF NOT EXISTS processed_csv (
            id INT AUTO_INCREMENT PRIMARY KEY,
            csv_data TEXT
        )";

        if ($conn->query($sql) === TRUE) {
            $stmt = $conn->prepare("INSERT INTO processed_csv (csv_data) VALUES (?)");
            $stmt->bind_param("s", $csvData);

            if ($stmt->execute()) {
                echo "CSV data processed and stored in the database successfully.";
            } else {
                echo "Error inserting data: " . $stmt->error;
            }
        } else {
            echo "Error creating table: " . $conn->error;
        }
    }
}



$conn->close();

function processCSV($csvData) {
    // Implement CSV processing logic here (e.g., remove empty rows and columns)
    // This example removes rows with any empty cells
    $rows = explode("\n", $csvData);
    $filteredRows = array_filter($rows, function($row) {
        $cells = str_getcsv($row, ",");
        return !in_array("", $cells, true);
    });
    return implode("\n", $filteredRows);
}
?>
