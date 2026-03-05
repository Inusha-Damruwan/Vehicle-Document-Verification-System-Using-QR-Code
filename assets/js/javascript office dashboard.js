document.addEventListener('DOMContentLoaded', function() {
    // Search button functionality
    const searchBtn = document.getElementById('searchBtn');
    const vehicleNumberInput = document.getElementById('vehicleNumber');
    
    searchBtn.addEventListener('click', function() {
        const vehicleNumber = vehicleNumberInput.value.trim();
        
        if (vehicleNumber === '') {
            alert('Please enter a vehicle number');
            return;
        }
        
        // In a real application, this would make an API call
        console.log(`Searching for vehicle: ${vehicleNumber}`);
        
        // Simulate search results (replace with actual API call)
        setTimeout(() => {
            alert(`Search results for ${vehicleNumber} would appear here`);
        }, 500);
    });
    
    // Allow search on Enter key press
    vehicleNumberInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });
    
    // Menu item click handlers
    const menuItems = document.querySelectorAll('.menu li');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            menuItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // In a real application, this would load the appropriate content
            console.log(`Selected menu: ${this.textContent.trim()}`);
        });
    });
});