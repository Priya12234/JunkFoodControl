<?php
session_start();


function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["status" => false, "message" => "Unauthorized"]);
        exit();
    }
}
function requireAdmin()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["status" => false, "message" => "Forbidden"]);
        exit();
    }
}