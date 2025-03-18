<?php
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT * FROM tbl_task ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
}

elseif ($method == 'POST') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['task']) || empty($data['task'])) {
        die(json_encode(["error" => "Task tidak boleh kosong"]));
    }

    $task = $conn->real_escape_string($data['task']);

    $sql = "INSERT INTO tbl_task (task, status_task, created_at) VALUES ('$task', 'pending', NOW())";

    if ($conn->query($sql)) {
        echo json_encode(["message" => "Task berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan task: " . $conn->error]);
    }
}



elseif ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'])) {
        echo json_encode(["error" => "ID task tidak ditemukan"]);
        exit;
    }

    $id = $data['id'];

    $query = "SELECT status_task FROM tbl_task WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $newStatus = ($row['status_task'] === 'pending') ? 'completed' : 'pending';

        // Update status
        $updateQuery = "UPDATE tbl_task SET status_task = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Status task diperbarui", "new_status" => $newStatus]);
        } else {
            echo json_encode(["error" => "Gagal memperbarui status task"]);
        }
    } else {
        echo json_encode(["error" => "Task tidak ditemukan"]);
    }
    $stmt->close();
}



elseif ($method == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM tbl_task WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Task berhasil dihapus"]);
    } else {
        echo json_encode(["message" => "Gagal menghapus task"]);
    }
    $stmt->close();
}


$conn->close();
?>
