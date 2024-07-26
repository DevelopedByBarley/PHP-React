<?php

function getConnect()
{
  $servername = $_SERVER['DB_HOST'];
  $username = $_SERVER['DB_USERNAME'];
  $password = $_SERVER['DB_PASSWORD'];
  $dbName = $_SERVER['DB_NAME'];

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbName;charset=utf8mb4", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

function safeQuote($conn, $value)
{
  if (is_null($value)) {
    return 'NULL';
  } else {
    return $conn->quote($value);
  }
}

function exportDatabaseUsingPDO($outputFile)
{
  try {
    $servername = $_SERVER['DB_HOST'];
    $username = $_SERVER['DB_USERNAME'];
    $password = $_SERVER['DB_PASSWORD'];
    $dbName = $_SERVER['DB_NAME'];

    // Connect to MySQL database
    $conn = new PDO("mysql:host=$servername;dbname=$dbName;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all tables in the database
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $output = fopen($outputFile, 'w');

    // Iterate through each table
    foreach ($tables as $table) {
      // Export table structure
      $stmt = $conn->query("SHOW CREATE TABLE `$table`");
      $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
      fwrite($output, "-- Table structure for `$table`\n");
      fwrite($output, $createTable['Create Table'] . ";\n\n");

      // Export table data
      $stmt = $conn->query("SELECT * FROM `$table`");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rowValues = array_map(function($value) use ($conn) {
          return safeQuote($conn, $value);
        }, $row);
        $rowValues = implode(", ", $rowValues);
        fwrite($output, "INSERT INTO `$table` VALUES ($rowValues);\n");
      }
      fwrite($output, "\n");
    } 

    fclose($output);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Example usage: Export database to a file using PHP PDO
$outputFile = 'backup/db/db.sql';  // Specify your desired output file path
if (defined('DATABASE_BACKUP_PERM') && DATABASE_BACKUP_PERM) {
  exportDatabaseUsingPDO($outputFile);
}
