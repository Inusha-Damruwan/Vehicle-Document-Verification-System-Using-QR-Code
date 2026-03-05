<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        
        h1 {
            color: #333;
        }
        
        #scanner-container {
            margin: 20px auto;
            width: 100%;
            max-width: 500px;
            position: relative;
        }
        
        #scanner {
            width: 100%;
            height: auto;
            border: 3px solid #333;
            border-radius: 5px;
        }
        
        #result {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 20px;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        .hidden {
            display: none;
        }
        
        #document-results {
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>QR Code Scanner</h1>
    
    <div id="scanner-container">
        <video id="scanner"></video>
    </div>
    
    <div id="result">Scan a QR code to see the result here</div>
    
    <form id="process-form" class="hidden" method="post">
        <input type="hidden" id="qr-data" name="qr_data">
        <button type="submit">Process QR Data</button>
    </form>
    
    <button id="start-btn">Start Scanner</button>
    <button id="stop-btn" class="hidden">Stop Scanner</button>
    
    <div id="document-results"></div>
    
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });
            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');
            const resultDiv = document.getElementById('result');
            const processForm = document.getElementById('process-form');
            const qrDataInput = document.getElementById('qr-data');
            const documentResults = document.getElementById('document-results');
            
            let isScanning = false;
            
            // Check for camera availability
            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    startBtn.addEventListener('click', startScanner);
                    stopBtn.addEventListener('click', stopScanner);
                } else {
                    resultDiv.textContent = 'No cameras found. Please ensure you have a camera connected.';
                    startBtn.disabled = true;
                }
            }).catch(function(e) {
                console.error(e);
                resultDiv.textContent = 'Error accessing camera: ' + e;
                startBtn.disabled = true;
            });
            
            scanner.addListener('scan', function(content) {
                resultDiv.textContent = 'Scanned: ' + content;
                qrDataInput.value = content;
                processForm.classList.remove('hidden');
                stopScanner();
            });
            
            function startScanner() {
                Instascan.Camera.getCameras().then(function(cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                        isScanning = true;
                        startBtn.classList.add('hidden');
                        stopBtn.classList.remove('hidden');
                        resultDiv.textContent = 'Scanning...';
                        processForm.classList.add('hidden');
                        documentResults.innerHTML = '';
                    } else {
                        resultDiv.textContent = 'No cameras available.';
                    }
                });
            }
            
            function stopScanner() {
                scanner.stop();
                isScanning = false;
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                
                if (resultDiv.textContent === 'Scanning...') {
                    resultDiv.textContent = 'Scanner stopped';
                }
            }
            
            // Handle form submission with AJAX
            processForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    documentResults.innerHTML = data;
                })
                .catch(error => {
                    documentResults.innerHTML = '<p>Error processing request: ' + error + '</p>';
                });
            });
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_data'])) {
        // Database configuration
        require_once '../assets/database.php';
        
        // Check connection
        if ($conn->connect_error) {
            die('<p>Database connection failed: ' . $conn->connect_error . '</p>');
        }
        
        $qrData = $conn->real_escape_string(trim($_POST['qr_data']));
        
        if (empty($qrData)) {
            echo '<p>QR code data is empty</p>';
            exit;
        }
        
        $sql = "SELECT * FROM documents WHERE vehicle_reg_no LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $qrData . "%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                if ($row['emission'] == NULL && $row['revenue'] == NULL) {
                    $doc = $row['insurance'];
                    $doc_name = "Insurance Policy";
                } else if ($row['insurance'] == NULL && $row['revenue'] == NULL) {
                    $doc = $row['emission'];
                    $doc_name = "Emission Test Report";
                } else if ($row['insurance'] == NULL && $row['emission'] == NULL) {
                    $doc = $row['revenue'];
                    $doc_name = "Revenue License";
                } else {
                    $doc_name = "Unknown Document";
                    $doc = "";
                }
                
                if (!empty($doc)) {
                    echo "<p>{$doc_name} for Vehicle No: {$row['vehicle_reg_no']} - <a href='../{$doc}' target='_blank'>View Document</a></p>";
                } else {
                    echo "<p>{$doc_name} for Vehicle No: {$row['vehicle_reg_no']}</p>";
                }
            }
        } else {
            echo "<p>No records found for vehicle number: " . htmlspecialchars($qrData) . "</p>";
        }
        
        $conn->close();
        exit;
    }
    ?>
</body>
</html>