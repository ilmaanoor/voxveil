<?php
require_once 'php/config.php';
$q = $conn->query("PRAGMA table_info(user_profiles)");
while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    echo "Column: " . $row['name'] . " (" . $row['type'] . ")\n";
}
?>
