function submitRegistration() {
    // Collect values
    const first = document.getElementById("first_name").value.trim();
    const last = document.getElementById("last_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    
    // Optional fields
    const middle = document.getElementById("middle_name").value.trim();
    const phone = document.getElementById("phone_number").value.trim();

    // ERROR CHECK: Verify mandatory fields
    if (!first || !last || !email || !password || !confirmPassword) {
        alert("All fields are required except Middle Initial and Phone Number!");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    let formData = new FormData(); //
    formData.append('first_name', first);
    formData.append('middle_name', middle);
    formData.append('last_name', last);
    formData.append('email', email);
    formData.append('phone_number', phone);
    formData.append('password', password);

    fetch("../api/user_api/process_register.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            window.location.href = "login.php";
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Registration Error:", error));
}

function submitLogin() {
    const email = document.getElementById("login_email").value.trim();
    const passwordField = document.getElementById("login_password");
    const password = passwordField.value;

    if (!email || !password) {
        alert("Please fill in all fields.");
        return;
    }

    let formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch("../api/user_api/process_login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = (data.user_type == 1) ? "../wine/index.php" : "../wine/shop.php";
        } else {
            // Display the specific error from PHP (e.g., "Incorrect password")
            alert(data.message); 
            passwordField.value = ""; // Clear password for security
            passwordField.focus();    // Put cursor back in password field
        }
    })
    .catch(error => console.error("Login Error:", error));
}