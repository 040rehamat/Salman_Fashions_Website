<?php
include '../db.php';
$notifs = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 10");

while ($n = $notifs->fetch_assoc()) {
  $cls = $n['is_read'] ? 'read' : 'unread';
  echo "<div class='notif-item $cls'>{$n['message']}<br><small>".date("d M, h:i A", strtotime($n['created_at']))."</small></div>";
}

// Optionally mark all as read
$conn->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
