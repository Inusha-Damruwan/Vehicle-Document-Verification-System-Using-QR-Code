<?php
session_start();
require_once '../assets/database.php';
if (!isset($_SESSION['owner_id'])) {
  $owner_id = $_SESSION['owner_id'];
    // Redirect to login page with an alert if not logged in
    echo "<script>alert('You must be logged in to view this page.'); window.location.href='../login.php';</script>";
    exit;
}
else{
    $owner_id = $_SESSION['owner_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vehicle Owner Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: url('../assets/backgroundimage.avif') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      display: flex;
      width: 100%;
    }

    .sidebar {
      width: 220px;
      background-color: #2c3e50;
      color: #ecf0f1;
      padding: 20px;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar li {
      padding: 10px 0;
      border-bottom: 1px solid #34495e;
      cursor: pointer;
    }

    .sidebar li:hover {
      background-color: #34495e;
    }

    .main-content {
      flex-grow: 1;
      padding: 30px;
      background-color: #fff;
    }

    .main-content h1 {
      margin-bottom: 30px;
    }

    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .card {
      background-color: #ecf0f1;
      padding: 20px;
      border-radius: 10px;
      flex: 1 1 250px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .card h3 {
      margin-bottom: 15px;
    }

    .card p {
      margin: 8px 0;
      font-size: 16px;
    }

    .card a {
      display: block;
      text-align: center;
      margin-top: 10px;
      padding: 10px;
      background-color: #2980b9;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
    }

    .card a:hover {
      background-color: #1f6391;
    }

    .qr-card img {
      margin-top: 10px;
      width: 100px;
      height: 100px;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
      }

      .main-content {
        padding: 15px;
      }

      .cards {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h2>Owner Dashboard</h2>
      <ul>
        <li onclick="location.href='Owner_Dashboard.php'">Dashboard</li>
        <li onclick='location.href="../process.php?logout_id=<?= $owner_id;?>"'>Logout</li>
      </ul>
    </aside>

    <main class="main-content">
      <h1>Welcome Vehicle Owner</h1>

      <div class="cards">
        <div class="card">
          <h3>Dashboard</h3>
          <p>Verified: <span id="verified">3</span></p>
          <p>Pending: <span id="pending">1</span></p>
          <p>Expired: <span id="expired">0</span></p>
        </div>

        <div class="card">
  <h3>Quick Actions</h3>
  <a href="document-upload.php">Upload New Document</a>
  <a href="manage-documents.php">Manage My Documents</a>
</div>


        <div class="card qr-card">
          <h3>My Vehicle QR Code</h3>
          <?php
          $sql = "SELECT q.vehical_reg_no , q.qr FROM qrcodes q JOIN documents d ON d.vehicle_reg_no = q.vehical_reg_no WHERE d.owner_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $owner_id);  
          $stmt->execute();
          $result = $stmt->get_result();
            if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $vehicleRegNo = $row['vehical_reg_no'];
              $qrCode = $row['qr'];
              // Image with onclick to open modal
              echo "<img src='../{$qrCode}' alt='QR Code for Vehicle' id='qrImage' style='cursor:pointer;' onclick='openModal()'>";
              echo "<p><strong>$vehicleRegNo</strong></p>";
              // Modal HTML
              echo "
              <div id='qrModal' style='display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); justify-content:center; align-items:center;'>
              <span style='position:absolute; top:30px; right:40px; font-size:40px; color:#fff; cursor:pointer;' onclick='closeModal()'>&times;</span>
                <img src='../{$qrCode}' alt='QR Code' style='width:200px; height:200px; max-width:95vw; max-height:95vh; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,0.3);'>
              </div>
              <script>
              function openModal() {
                document.getElementById('qrModal').style.display = 'flex';
              }
              function closeModal() {
                document.getElementById('qrModal').style.display = 'none';
              }
              // Optional: close modal when clicking outside image
              document.addEventListener('click', function(e) {
                var modal = document.getElementById('qrModal');
                var img = document.getElementById('qrImage');
                if (modal.style.display === 'flex' && !modal.contains(e.target) && e.target !== img) {
                closeModal();
                }
              }, true);
              </script>
              ";
          } else {
              echo "<p>No QR code available for your vehicle.</p>";
          }
          ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>