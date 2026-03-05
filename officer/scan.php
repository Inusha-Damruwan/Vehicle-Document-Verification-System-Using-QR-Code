<?php
session_start();
// (Optional) officer login check
// if (!isset($_SESSION['officer_id'])) {
//   header("Location: Officer_Login_Page.html");
//   exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QR Code Scanner</title>

  <style>
    body{
      margin:0;
      font-family: Arial, sans-serif;
      background: url('../assets/backgroundimage.avif') no-repeat center center fixed;
      background-size: cover;
      min-height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      padding:20px;
    }
    .box{
      width: 100%;
      max-width: 1000px;
      background: rgba(255,255,255,0.92);
      border-radius: 14px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      padding: 25px;
    }

    /* Top bar */
    .topbar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      margin-bottom: 10px;
    }
    h1{ margin:0; }

    .navbtn{
      padding:10px 14px;
      border:none;
      border-radius:8px;
      font-weight:bold;
      cursor:pointer;
      font-size:14px;
      text-decoration:none;
      display:inline-block;
      background:#0b4a7a;
      color:#fff;
    }
    .navbtn:hover{ opacity:0.9; }

    #reader{
      width:100%;
      max-width: 520px;
      margin: 15px auto;
      border:2px solid #333;
      border-radius: 10px;
      overflow:hidden;
      background:#fff;
      min-height: 260px;
    }
    .row{
      display:flex;
      gap:20px;
      flex-wrap:wrap;
      align-items:flex-start;
      justify-content:space-between;
    }
    .controls{
      flex: 1;
      min-width: 250px;
    }
    .controls input{
      width:100%;
      padding: 12px;
      border:1px solid #aaa;
      border-radius:8px;
      font-size:16px;
    }
    .controls button{
      margin-top:12px;
      padding:12px 16px;
      border:none;
      border-radius:8px;
      font-weight:bold;
      cursor:pointer;
      font-size:15px;
    }
    .start{ background:#2e7d32; color:#fff; }
    .stop{ background:#c62828; color:#fff; margin-left:10px; }
    .hint{ margin-top:10px; color:#444; font-size:14px;}
    .error{ margin-top:10px; color:#b00020; font-size:14px; font-weight:bold;}
  </style>

  <!-- QR Scanner Library (CDN) -->
  <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

<div class="box">

  <!-- ✅ Back to Dashboard button -->
  <div class="topbar">
    <h1>QR Code Scanner</h1>
    <a class="navbtn" href="Officer_Dashboard.php">⬅ Back to Dashboard</a>
  </div>

  <div class="row">
    <div style="flex:1; min-width:280px;">
      <div id="reader"></div>
    </div>

    <div class="controls">
      <label><b>Scan result</b></label>
      <input type="text" id="result" placeholder="Scan a QR code to see the result" readonly />

      <div>
        <button class="start" id="startBtn" type="button">Start Scanner</button>
        <button class="stop" id="stopBtn" type="button" disabled>Stop</button>
      </div>

      <div class="hint">
        ✅ Tip: First time browser ask “Allow Camera” 
      </div>
      <div class="error" id="errBox"></div>
    </div>
  </div>
</div>

<script>
  const resultInput = document.getElementById("result");
  const errBox = document.getElementById("errBox");
  const startBtn = document.getElementById("startBtn");
  const stopBtn  = document.getElementById("stopBtn");

  let html5QrCode = null;
  let isRunning = false;

  function showError(msg){
    errBox.textContent = msg || "";
  }

  async function startScanner(){
    showError("");

    try {
      if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
      }

      const devices = await Html5Qrcode.getCameras();
      if (!devices || devices.length === 0) {
        showError("No camera found. Please connect a camera or check permissions.");
        return;
      }

      // Prefer back camera if available
      const cameraId = devices[devices.length - 1].id;

      await html5QrCode.start(
        cameraId,
        { fps: 10, qrbox: 250 },
        (decodedText) => {
          resultInput.value = decodedText;

          // ✅ Optional: redirect to manual entry page with scanned value
          window.location.href = "manual_entry.php?reg_no=" + encodeURIComponent(decodedText);
        },
        () => {}
      );

      isRunning = true;
      startBtn.disabled = true;
      stopBtn.disabled = false;

    } catch (err) {
      console.error(err);
      showError("Camera start failed. Allow camera permission and try again.");
    }
  }

  async function stopScanner(){
    showError("");
    try{
      if (html5QrCode && isRunning) {
        await html5QrCode.stop();
        await html5QrCode.clear();
      }
      isRunning = false;
      startBtn.disabled = false;
      stopBtn.disabled = true;
    }catch(err){
      console.error(err);
      showError("Stop failed. Refresh page and try again.");
    }
  }

  startBtn.addEventListener("click", startScanner);
  stopBtn.addEventListener("click", stopScanner);
</script>

</body>
</html>