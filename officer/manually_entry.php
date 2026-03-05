<?php
require_once '../assets/database.php';
session_start();
if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('You must be logged in to view this page.'); window.location.href='login.php';</script>";
    exit;
}
else{
    $officer_id = $_SESSION['officer_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Officer Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
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
      max-width: 1100px;
      width: 100%;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #003366;
      color: white;
      padding: 30px 20px;
    }

    .sidebar h3 {
      margin-bottom: 20px;
      font-size: 22px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      margin: 15px 0;
      cursor: pointer;
      padding: 8px;
      border-radius: 5px;
      transition: background 0.3s;
    }

    .sidebar ul li:hover {
      background-color: #004c99;
    }

    /* Main Content */
    .main-content {
      flex-grow: 1;
      padding: 40px;
    }

    .main-content h2 {
      margin-bottom: 30px;
      text-align: center;
      color: #003366;
    }

    /* Search Box */
    .search-box {
      background-color: #cce6ff;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .search-box h3 {
      margin-bottom: 15px;
      color: #003366;
    }

    .search-box input {
      padding: 10px;
      width: 70%;
      margin-right: 10px;
      border: 1px solid #999;
      border-radius: 5px;
    }

    .search-box button {
      padding: 10px 20px;
      background-color: #003366;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .search-box button:hover {
      background-color: #0055cc;
    }

    /* Result Box */
    .result-box {
      background-color: #cce6ff;
      padding: 20px;
      border-radius: 8px;
    }

    .result-box h3 {
      margin-bottom: 15px;
      color: #003366;
    }

    .result-box p {
      background-color: white;
      padding: 10px;
      margin: 5px 0;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <h3>Officer Panel</h3>
      <ul>
        <li onclick='location.href="Officer_Dashboard.php"'>Dashboard</li>
        <li onclick='location.href="scan.php"'>Scan QR</li>
        <li>Manual Entry</li>
        <li onclick='location.href="../process.php?logout_id=<?= $officer_id;?>"'>Logout</li>
      </ul>
    </div>

    <div class="main-content">
      <h2>Welcome Officer</h2>

      <div class="search-box">
        <h3>Search Vehicle by Number</h3>
        <form action="#" method="POST">
          <input type="text" id="vehicleInput" name = "vehicle_reg_no" placeholder="Enter Vehicle Number" required />
          <button type  = "submit" name = "search_vehicle">Search</button>
        </form>
      </div>

      <div class="result-box">
        <h3>Recent Scan Result</h3>
        <div id="results">
          <?php
          if(isset($_POST['search_vehicle'])) {
              $vehicle_reg_no = $_POST['vehicle_reg_no'];
              $sql = "SELECT * FROM documents WHERE vehicle_reg_no LIKE ?";
              $stmt = $conn->prepare($sql);
              $searchTerm = "%" . $vehicle_reg_no . "%";
              $stmt->bind_param("s", $searchTerm);
              $stmt->execute();
              $result = $stmt->get_result();
              if($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      $id = $row['id'];
                      if ($row['emission'] == NULL && $row['revenue'] == NULL) {
                        $doc = $row['insurance'];
                        $doc_name = "Insurance Policy";
                      }
                      else if ($row['insurance'] == NULL && $row['revenue'] == NULL) {
                        $doc = $row['emission'];
                        $doc_name = "Emission Test Report";
                      }
                      else if ($row['insurance'] == NULL && $row['emission'] == NULL) {
                        $doc = $row['revenue'];
                        $doc_name = "Revenue License";
                      }
                      else {
                        echo "<td data-label='Document'>Unknown Document</td>";
                      }
                      echo "<p>{$doc_name} for Vehicle No: {$row['vehicle_reg_no']} - <a href='../{$doc}' target='_blank'>View Document</a></p>";
                  }
              } else {
                  echo "<p>No records found for vehicle number: " . htmlspecialchars($vehicle_reg_no) . "</p>";
              }
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    function searchVehicle() {
      const input = document.getElementById("vehicleInput").value;
      alert("Searching for: " + input);
    }
  </script>
</body>
</html>
