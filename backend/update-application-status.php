<?php
require_once "db_connect.php"; // adjust path if needed

// Step 1: Get all unit_ids from apartment_units
$unitQuery = "SELECT unit_id FROM apartment_units";
$unitResult = $conn->query($unitQuery);

if ($unitResult && $unitResult->num_rows > 0) {
    while ($unitRow = $unitResult->fetch_assoc()) {
        $unit_id = $unitRow['unit_id'];

        // Step 2: Check if there is any approved application for this unit_id
        $statusCheckStmt = $conn->prepare("SELECT COUNT(*) FROM tenant_applications WHERE unit_id = ? AND application_status = 'Approved'");
        $statusCheckStmt->bind_param("i", $unit_id);
        $statusCheckStmt->execute();
        $statusCheckStmt->bind_result($approvedCount);
        $statusCheckStmt->fetch();
        $statusCheckStmt->close();

        // Step 3: Update availability_status based on the result
        if ($approvedCount > 0) {
            // Approved application exists — mark as Occupied
            $updateStmt = $conn->prepare("UPDATE apartment_units SET availability_status = 'Occupied' WHERE unit_id = ?");
        } else {
            // No approved application — mark as Available
            $updateStmt = $conn->prepare("UPDATE apartment_units SET availability_status = 'Available' WHERE unit_id = ?");
        }

        $updateStmt->bind_param("i", $unit_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
}

$data = json_decode(file_get_contents("php://input"), true);
$application_id = $data['application_id'];
$new_status = $data['application_status'];

$response = ["success" => false];

if ($application_id && $new_status) {
    // Get unit_id and user_id from the current application
    $stmt = $conn->prepare("SELECT user_id, unit_id FROM tenant_applications WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $unit_id);
    $stmt->fetch();
    $stmt->close();

    if ($new_status === "Approved") {
        // 1. Approve the selected application
        $stmt = $conn->prepare("UPDATE tenant_applications SET application_status = 'Approved' WHERE application_id = ?");
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $stmt->close();

        // 2. Reject other applications for the same unit
        $stmt = $conn->prepare("UPDATE tenant_applications SET application_status = 'Declined' WHERE unit_id = ? AND application_id != ?");
        $stmt->bind_param("ii", $unit_id, $application_id);
        $stmt->execute();
        $stmt->close();

        // 3. Reject other applications by the same user for other units
        $stmt = $conn->prepare("UPDATE tenant_applications SET application_status = 'Declined' WHERE user_id = ? AND application_id != ?");
        $stmt->bind_param("ii", $user_id, $application_id);
        $stmt->execute();
        $stmt->close();

        // 4. Mark the unit as Occupied
        $stmt = $conn->prepare("UPDATE apartment_units SET availability_status = 'Occupied' WHERE unit_id = ?");
        $stmt->bind_param("i", $unit_id);
        $stmt->execute();
        $stmt->close();

        // 5. Change the user_status to 'tenant'
        $stmt = $conn->prepare("UPDATE login_users SET user_status = 'tenant' WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $response["success"] = true;
    } else {
        // Just update the application status
        $stmt = $conn->prepare("UPDATE tenant_applications SET application_status = ? WHERE application_id = ?");
        $stmt->bind_param("si", $new_status, $application_id);
        $response["success"] = $stmt->execute();
        $stmt->close();
    }
}

header('Content-Type: application/json');
echo json_encode($response);