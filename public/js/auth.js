$(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault();
        var email = $("#email").val();
        var password = $("#password").val();

        // Send login request to Laravel backend
        $.ajax({
            url: "/api/auth/login",
            method: "POST",
            data: {
                email: email,
                password: password,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                alert("Login successful");
                // Redirect to dashboard or home page
            },
            error: function (xhr, status, error) {
                alert("Login failed: " + xhr.responseJSON.message);
            },
        });
    });

    $("#signupForm").submit(function (e) {
        e.preventDefault();
        var name = $("#name").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var confirmPassword = $("#confirmPassword").val();

        if (password !== confirmPassword) {
            alert("Passwords do not match");
            return;
        }

        // Send signup request to Laravel backend
        $.ajax({
            url: "/api/auth/signup",
            method: "POST",
            data: {
                name: name,
                email: email,
                password: password,
                password_confirmation: confirmPassword,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                alert("Signup successful");
                // Redirect to login page or dashboard
            },
            error: function (xhr, status, error) {
                alert("Signup failed: " + xhr.responseJSON.message);
            },
        });
    });
});
