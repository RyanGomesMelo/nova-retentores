<?php
include 'config/db.php';

$query = "SELECT name, clicks FROM product ORDER BY clicks DESC LIMIT 10";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
