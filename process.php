<?php
require_once 'assets/database.php';
session_start();

/* =========================
   NIC helper (clean + validate)
========================= */
function clean_nic($nic) {
    $nic = strtoupper(trim($nic));
    $nic = preg_replace('/\s+/', '', $nic);        // remove spaces (hidden too)
    $nic = str_replace('-', '', $nic);             // remove dashes if any
    return $nic;
}

function is_valid_nic($nic) {
    $oldNICPattern = '/^[0-9]{9}[VX]$/';   // 123456789V or 123456789X
    $newNICPattern = '/^[0-9]{12}$/';      // 200012345678
    return preg_match($oldNICPattern, $nic) || preg_match($newNICPattern, $nic);
}

/* =========================
   VEHICLE OWNER LOGIN
========================= */
if (isset($_POST['owner_login'])) {

    $nic = isset($_POST['nic']) ? $_POST['nic'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $nic = clean_nic($nic);

    if (!is_valid_nic($nic)) {
        echo "<script>alert('Invalid NIC format. Please use old (e.g., 123456789V) or new format (e.g., 200012345678).'); window.history.back();</script>";
        exit;
    }

    $sql = "SELECT * FROM ownerregistertable WHERE nic_no = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('SQL Prepare Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit;
    }

    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['owner_id'] = $row['owner_id'];
            echo "<script>alert('Login successful!'); window.location.href='owner/Owner_Dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('No user found with this NIC. Please register first.'); window.history.back();</script>";
        exit;
    }
}

/* =========================
   VEHICLE OWNER REGISTRATION
========================= */
if (isset($_POST['owner_register'])) {

    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $nic_no = isset($_POST['nic_no']) ? $_POST['nic_no'] : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    $nic_no = clean_nic($nic_no);

    // NIC validate
    if (!is_valid_nic($nic_no)) {
        echo "<script>alert('Invalid NIC format. Please use old (e.g., 123456789V) or new format (e.g., 200012345678).'); window.history.back();</script>";
        exit;
    }

    // Password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO ownerregistertable (full_name, nic_no, email, phone, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('SQL Prepare Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit;
    }

    $stmt->bind_param("sssss", $full_name, $nic_no, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='owner/Owner_Login_Page.html';</script>";
        exit;
    } else {
        if (strpos($conn->error, 'Duplicate') !== false) {
            echo "<script>alert('NIC or Email already registered!'); window.history.back();</script>";
            exit;
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
            exit;
        }
    }
}

/* =========================
   UPLOAD DOCUMENTS
========================= */
if (isset($_POST['upload_documents'])) {

    if (!isset($_SESSION['owner_id'])) {
        echo "<script>alert('Please login first.'); window.location.href='owner/Owner_Login_Page.html';</script>";
        exit;
    }

    $reg_no = isset($_POST['reg_no']) ? trim($_POST['reg_no']) : '';
    $insurance = $_FILES['insurance'] ?? null;
    $emission = $_FILES['emission'] ?? null;
    $revenue = $_FILES['revenue'] ?? null;

    $owner_id = $_SESSION['owner_id'];

    $upload_dir = 'assets/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique file names to avoid overwrite
    $insurance_path = $insurance && $insurance['name'] ? $upload_dir . time() . "_insurance_" . basename($insurance['name']) : '';
    $emission_path  = $emission && $emission['name'] ? $upload_dir . time() . "_emission_" . basename($emission['name']) : '';
    $revenue_path   = $revenue && $revenue['name'] ? $upload_dir . time() . "_revenue_" . basename($revenue['name']) : '';

    if ($insurance && $insurance['tmp_name']) move_uploaded_file($insurance['tmp_name'], $insurance_path);
    if ($emission && $emission['tmp_name']) move_uploaded_file($emission['tmp_name'], $emission_path);
    if ($revenue && $revenue['tmp_name']) move_uploaded_file($revenue['tmp_name'], $revenue_path);

    include('assets/phpqrcode/qrlib.php');

    // Insert documents (one row per document type like your original)
    if ($insurance_path && file_exists($insurance_path)) {
        $sql = "INSERT INTO documents (vehicle_reg_no, owner_id, insurance) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $reg_no, $owner_id, $insurance_path);
        $stmt->execute();
    }

    if ($emission_path && file_exists($emission_path)) {
        $sql = "INSERT INTO documents (vehicle_reg_no, owner_id, emission) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $reg_no, $owner_id, $emission_path);
        $stmt->execute();
    }

    if ($revenue_path && file_exists($revenue_path)) {
        $sql = "INSERT INTO documents (vehicle_reg_no, owner_id, revenue) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $reg_no, $owner_id, $revenue_path);
        $stmt->execute();
    }

    // QR Code create once per vehicle
require_once('assets/phpqrcode/qrlib.php'); // <- MUST be before QRcode::png()

$qr_dir = "assets/qr/";
if (!is_dir($qr_dir)) {
    mkdir($qr_dir, 0777, true);
}

$reg_no = trim($reg_no); // important
if ($reg_no == "") {
    die("ERROR: Vehicle Reg No is empty!");
}

$file = $qr_dir . $reg_no . ".png";

// check existing qr in DB
$sql_check_qr = "SELECT * FROM qrcodes WHERE vehical_reg_no = ?";
$stmt_check_qr = $conn->prepare($sql_check_qr);
$stmt_check_qr->bind_param("s", $reg_no);
$stmt_check_qr->execute();
$result_check_qr = $stmt_check_qr->get_result();

if ($result_check_qr && $result_check_qr->num_rows > 0) {
    // already exists
    // (optional) show where it is
    // $row = $result_check_qr->fetch_assoc();
    // echo "QR already exists: " . $row['qr'];
} else {

    // generate qr
    QRcode::png($reg_no, $file, QR_ECLEVEL_L, 10, 2);

    if (!file_exists($file)) {
        die("ERROR: QR image not created. Check folder permissions: " . $qr_dir);
    }

    // insert to DB
    $sql_insert_qr = "INSERT INTO qrcodes (vehical_reg_no, qr) VALUES (?, ?)";
    $stmt_insert_qr = $conn->prepare($sql_insert_qr);
    $stmt_insert_qr->bind_param("ss", $reg_no, $file);
    $stmt_insert_qr->execute();
}

echo "<script>alert('Documents uploaded successfully!'); window.location.href='owner/Owner_Dashboard.php';</script>";
exit;

}

/* =========================
   DELETE DOCUMENT
========================= */
if (isset($_GET['delete_doc_id'])) {
    $delete_id = (int)$_GET['delete_doc_id'];

    $sql = "DELETE FROM documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Document deleted successfully!'); window.location.href='owner/manage-documents.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting document: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit;
    }
}

/* =========================
   LOGOUT
========================= */
if (isset($_GET['logout_id'])) {
    session_destroy();
    header("Location: ./");
    exit;
}

/* =========================
   OFFICER REGISTRATION
========================= */
if (isset($_POST['officer_register'])) {

    $full_name = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $police_id = isset($_POST['policeId']) ? trim($_POST['policeId']) : '';
    $branch = isset($_POST['branch']) ? trim($_POST['branch']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO officerregistertable (fullName, policeid, branch, email, phoneno, password)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<script>alert('SQL Prepare Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit;
    }

    $stmt->bind_param("ssssss", $full_name, $police_id, $branch, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='officer/Officer_Login_Page.html';</script>";
        exit;
    } else {
        if (strpos($conn->error, 'Duplicate') !== false) {
            echo "<script>alert('Police ID or Email already registered!'); window.history.back();</script>";
            exit;
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
            exit;
        }
    }
}

/* =========================
   OFFICER LOGIN
========================= */
if (isset($_POST['officer_login'])) {

    $police_id = isset($_POST['policeId']) ? trim($_POST['policeId']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $sql = "SELECT * FROM officerregistertable WHERE policeid = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<script>alert('SQL Prepare Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit;
    }

    $stmt->bind_param("s", $police_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['officer_id'] = $row['officerid'];

        if (password_verify($password, $row['password'])) {
            echo "<script>alert('Login successful!'); window.location.href='officer/Officer_Dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('No user found with this Police ID. Please register first.'); window.history.back();</script>";
        exit;
    }
}

// Default redirect (optional)
header("Location: ./");
exit;
?>