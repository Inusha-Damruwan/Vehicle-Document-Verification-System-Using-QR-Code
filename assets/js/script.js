document.getElementById("loginForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const nic = document.getElementById("nic").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!nic || !password) {
    alert("Please fill out all fields.");
    return;
  }

  // Simple simulation
  alert("Login successful (simulation)");
  // You can redirect or send data to backend here
});
//document uploard and register page
document.getElementById("registerForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const inputs = this.querySelectorAll("input[type='password']");
  if (inputs[0].value !== inputs[1].value) {
    alert("Passwords do not match!");
  } else {
    alert("Registration successful (simulated).");
    // Send to backend or redirect
  }
});

document.getElementById("uploadForm")?.addEventListener("submit", function(e) {
  e.preventDefault();
  alert("Documents uploaded (simulated).");
});

//police officer dashboard
function searchVehicle() {
  const input = document.getElementById("vehicleInput").value;
  const results = document.getElementById("results");

  if (input.trim() === "") {
    alert("Please enter a vehicle number");
    return;
  }

  const newResult = document.createElement("p");
  newResult.textContent = $const; input; any; Pending;
  results.prepend(newResult);
  document.getElementById("vehicleInput").value = "";
}

//owner dashboard 

function uploadDocument() {
  alert("Upload New Document function clicked!");
}

function manageDocuments() {
  alert("Manage My Documents function clicked!");
}

//manage document

function deleteRow(btn) {
  const row = btn.parentNode.parentNode;
  const documentName = row.cells[0].textContent;
  if (confirm(`Are you sure you want to delete "${documentName}"?`)) {
    row.remove();
  }
}

function editRow(btn) {
  const row = btn.parentNode.parentNode;
  const doc = row.cells[0].textContent;
  const newStatus = prompt(`Edit status for "${doc}" (e.g., Verified, Pending):`, row.cells[1].textContent);
  if (newStatus) {
    row.cells[1].textContent = newStatus;
  }
}

//forgot password

function submitForm(event) {
  event.preventDefault(); // Prevent form from submitting

  const email = document.getElementById('email').value;

  // Validate email
  if (!email) {
    alert("Please enter a valid email address.");
    return;
  }

  // Call the backend API to send reset password link
  fetch('http://localhost:3000/reset-password', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email })
  })
  .then(response => response.json())
  .then(data => {
    if (data.message) {
      alert(data.message);  // Show success message
    } else {
      alert("An error occurred.");
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Something went wrong. Please try again.');
  });
}