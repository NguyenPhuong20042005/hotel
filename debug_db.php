<?php
require __DIR__ . '/config/database.php';
$db = Database::getConnection();
$driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "DRIVER=" . $driver . PHP_EOL;
foreach ($db->query("SELECT COUNT(*) as c FROM hotels") as $row) {
    echo "HOTEL_COUNT=" . $row['c'] . PHP_EOL;
}
foreach ($db->query("SELECT id, name, city, image_url FROM hotels ORDER BY id") as $row) {
    echo $row['id'] . " | " . $row['name'] . " | " . $row['city'] . " | " . $row['image_url'] . PHP_EOL;
}
