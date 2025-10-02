<?php
// Simple health check file that always returns HTTP 200
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'timestamp' => time()]);
exit(0);
