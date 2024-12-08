$(document).ready(function() {
    // Handle the logout button click
    $('#logout-btn').click(function() {
        console.log('Logout button clicked'); // Debugging line
        // Send a logout request to the API
        $.ajax({
            type: 'POST',
            url: '/api/auth/logout', // The endpoint for logout
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('jwt_token') },
            success: function(response) {
                // Clear all items from local storage
                localStorage.clear(); // This will remove all tokens and data stored in local storage
                // Redirect to the login page
                window.location.href = 'login.html';
            },
            error: function(xhr, status, error) {
                // Handle errors (if any)
                alert('Logout failed! Please try again.');
            }
        });
    });
});
