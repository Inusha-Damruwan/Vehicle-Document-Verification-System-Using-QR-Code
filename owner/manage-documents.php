<?php
require_once '../assets/database.php';
session_start();

$owner_id = $_SESSION['owner_id'] ?? null;

if (!$owner_id) {
    echo "<script>alert('You must login first'); window.location.href='Owner_Login_Page.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Documents</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
background:url('../assets/backgroundimage.avif') no-repeat center center fixed;
background-size:cover;
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
}

.overlay{
background:rgba(0,0,0,0.75);
padding:40px;
max-width:900px;
width:100%;
border-radius:15px;
color:white;
box-shadow:0 0 20px rgba(0,0,0,0.6);
}

h1{
text-align:center;
margin-bottom:25px;
}

.back-btn{
margin-bottom:20px;
}

.back-btn a{
text-decoration:none;
}

.back-btn button{
background:#007BFF;
color:white;
padding:10px 15px;
border:none;
border-radius:6px;
cursor:pointer;
font-weight:bold;
}

.back-btn button:hover{
background:#0056b3;
}

table{
width:100%;
border-collapse:collapse;
background:white;
color:black;
border-radius:10px;
overflow:hidden;
}

th,td{
padding:15px;
border-bottom:1px solid #ccc;
text-align:left;
}

th{
background:#003366;
color:white;
}

button{
padding:8px 12px;
border:none;
border-radius:5px;
cursor:pointer;
font-weight:bold;
}

.view-btn{
background:#4CAF50;
color:white;
}

.delete-btn{
background:#f44336;
color:white;
}

</style>

</head>

<body>

<div class="overlay">

<div class="back-btn">
<a href="Owner_Dashboard.php">
<button>← Back to Dashboard</button>
</a>
</div>

<h1>Manage Uploaded Documents</h1>

<table>

<thead>
<tr>
<th>Document</th>
<th>Vehicle No</th>
<th>Status</th>
<th>Expires</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php

$sql="SELECT * FROM documents WHERE owner_id=?";
$stmt=$conn->prepare($sql);
$stmt->bind_param("i",$owner_id);
$stmt->execute();
$result=$stmt->get_result();

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

$id=$row['id'];

$doc=null;
$docName="Unknown Document";

if($row['emission']==NULL && $row['revenue']==NULL){
$doc=$row['insurance'];
$docName="Insurance Policy";
}
elseif($row['insurance']==NULL && $row['revenue']==NULL){
$doc=$row['emission'];
$docName="Emission Test Report";
}
elseif($row['insurance']==NULL && $row['emission']==NULL){
$doc=$row['revenue'];
$docName="Revenue License";
}

$statusRaw=$row['status'] ?? 0;
$expDate=$row['exp_date'] ?? null;

if($statusRaw==0){
$statusText="Un verified";
}else{
$statusText="Verified";
}

$expDateText=$expDate ? $expDate : "N/A";

echo "<tr>

<td>{$docName}</td>

<td>{$row['vehicle_reg_no']}</td>

<td>{$statusText}</td>

<td>{$expDateText}</td>

<td>";

if($doc){
echo "<a href='../{$doc}' target='_blank'>
<button class='view-btn'>View</button>
</a>";
}

echo "<a href='../process.php?delete_doc_id={$id}' onclick=\"return confirm('Delete this document?');\">
<button class='delete-btn'>Delete</button>
</a>

</td>

</tr>";

}

}
else{

echo "<tr>
<td colspan='5' style='text-align:center;'>No documents uploaded</td>
</tr>";

}

?>

</tbody>
</table>

</div>

</body>
</html>