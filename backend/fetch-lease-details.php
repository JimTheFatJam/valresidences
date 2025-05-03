<?php
session_start();
require 'db_connect.php'; // Adjust path as needed

$response = ['success' => false, 'message' => 'Something went wrong'];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Get user_id from login_users
    $stmt = $conn->prepare("SELECT user_id FROM login_users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    if ($stmt->fetch()) {
        $stmt->close();

        // Get unit_id from tenant_applications
        $stmt = $conn->prepare("SELECT unit_id FROM tenant_applications WHERE user_id = ? AND application_status = 'Approved'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($unit_id);
        if ($stmt->fetch()) {
            $stmt->close();

            // Get apartment unit details
            $stmt = $conn->prepare("SELECT apartment_id, unit_number, total_floors, living_area, bedroom_count, tb_count, balcony, parking_space, pet_friendly, lease_term, rent_price, month_deposit, month_advance, furnished_status FROM apartment_units WHERE unit_id = ?");
            $stmt->bind_param("i", $unit_id);
            $stmt->execute();
            $stmt->bind_result($apartment_id, $unit_number, $total_floors, $living_area, $bedroom_count, $tb_count, $balcony, $parking_space, $pet_friendly, $lease_term, $rent_price, $month_deposit, $month_advance, $furnished_status);
            if ($stmt->fetch()) {
                $stmt->close();

                // Get subdivision address
                $stmt = $conn->prepare("SELECT subdivision_address FROM apartment_listings WHERE apartment_id = ?");
                $stmt->bind_param("i", $apartment_id);
                $stmt->execute();
                $stmt->bind_result($subdivision_address);
                $stmt->fetch();

                $response = [
                    'success' => true,
                    'data' => [
                        'unit_number' => $unit_number,
                        'total_floors' => $total_floors,
                        'living_area' => $living_area,
                        'bedroom_count' => $bedroom_count,
                        'tb_count' => $tb_count,
                        'balcony' => $balcony,
                        'parking_space' => $parking_space,
                        'pet_friendly' => $pet_friendly,
                        'lease_term' => $lease_term,
                        'rent_price' => $rent_price,
                        'month_deposit' => $month_deposit,
                        'month_advance' => $month_advance,
                        'furnished_status' => $furnished_status,
                        'subdivision_address' => $subdivision_address
                    ]
                ];
            }
        }
    }
}

echo json_encode($response);
?>