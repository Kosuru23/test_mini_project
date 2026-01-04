<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE"); // Removed PUT for file compatibility
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include '../class/Wine.php';
include '../class/Utility.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$wine = new Wine($db);
$method = $_SERVER['REQUEST_METHOD'];

$utility = new Utility($db);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Fetch a specific wine
            $wineData = $wine->getWineById($_GET['id']);
            echo $wineData ? json_encode(["status" => "success", "wine" => $wineData]) : json_encode(["status" => "error", "message" => "Wine not found"]);
        } 
        elseif (isset($_GET['fetch_countries'])) {
            // Fetch only countries using the Utility class
            $countries = $utility->getCountries();
            echo json_encode(["status" => "success", "countries" => $countries]);
        } 
        elseif (isset($_GET['fetch_wine_type'])) {
            // Fetch only wine types using the Utility class
            $wine_types = $utility->getWineTypes();
            echo json_encode(["status" => "success", "wine_types" => $wine_types]);
        } 
        elseif (isset($_GET['fetch_grape_variety'])) {
            // Fetch only grape varieties using the Utility class
            $grape_varieties = $utility->getGrapeVarieties();
            echo json_encode(["status" => "success", "grape_varieties" => $grape_varieties]);
        } 
        else {
            // Default: Fetch all wines
            $wines = $wine->getAllWines();
            echo json_encode(["status" => "success", "wine" => $wines]);
        }
        break;

    case 'POST':
        // Capture text data from $_POST
        $id = $_POST['id'] ?? null; // If ID exists, we are updating
        $name = $_POST['wine_name'] ?? '';
        $type = $_POST['wine_type'] ?? '';
        $variety = $_POST['grape_variety'] ?? '';
        $region = $_POST['region'] ?? '';
        $country_id = $_POST['country_id'] ?? '';
        $alcohol = $_POST['alcohol_percentage'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $desc = $_POST['description'] ?? '';
        
        // Start with the existing image if updating, or default if new
        $image_url = $_POST['existing_image'] ?? 'default.png'; 

        // Handle File Upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
            $filename = time() . "_" . uniqid() . "." . $file_ext;
            
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $upload_dir . $filename)) {
                $image_url = $filename;
            }
        }

        if ($id) {
            // Logic for UPDATING existing wine
            $result = $wine->updateWine($id, $name, $type, $variety, $region, $country_id, $alcohol, $quantity, $price, $desc, $image_url);
            echo json_encode(["status" => $result ? "success" : "error", "message" => $result ? "Wine updated" : "Update failed"]);
        } else {
            // Logic for ADDING new wine
            $result = $wine->addWine($name, $type, $variety, $region, $country_id, $alcohol, $quantity, $price, $desc, $image_url);
            echo json_encode(["status" => $result ? "success" : "error", "message" => $result ? "Wine added" : "Addition failed"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $result = $wine->deleteWine($_GET['id']);
            echo json_encode(["status" => $result ? "success" : "error", "message" => $result ? "Deleted" : "Delete failed"]);
        }
        break;
}
?>