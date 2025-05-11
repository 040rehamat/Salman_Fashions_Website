<?php
include '../db.php';
$count = $conn->query("SELECT COUNT(*) as total FROM admin_notifications WHERE is_read = 0")->fetch_assoc();
echo $count['total'];
