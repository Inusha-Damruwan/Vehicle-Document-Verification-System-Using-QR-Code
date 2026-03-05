
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Vehicle Document</title>
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
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      max-width: 500px;
      width: 100%;
    }

    .upload-box {
      display: flex;
      flex-direction: column;
    }

    .upload-box h3 {
      text-align: center;
      color: #003366;
      margin-bottom: 25px;
      font-size: 24px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-top: 15px;
      font-weight: bold;
      color: #333;
    }

    input[type="file"] , input[type="text"] {
      margin-top: 8px;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #f9f9f9;
    }

    button {
      margin-top: 30px;
      padding: 12px;
      background-color: #003366;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0055aa;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="upload-box">
      <h3>Upload Vehicle Document</h3>

      <form action="../process.php" method="post" enctype="multipart/form-data">

        <label for="reg_no">Vehicle Registration Number</label>
        <input type="text" id="reg_no" placeholder = "Enter vehicle registration number" name="reg_no" required>

        <label>Insurance Policy</label>
        <input type="file" name="insurance">

        <label>Emission Test Report</label>
        <input type="file" name="emission">

        <label>Revenue License</label>
        <input type="file" name="revenue">

        <button type="submit" name = "upload_documents">Submit Documents</button>
      </form>
    </div>
  </div>

  <script>
    // Optional: alert on successful submit (demo only)
    document.querySelector("form").addEventListener("submit", function(e) {
      alert("Your documents have been submitted!");
    });
  </script>
</body>
</html>
