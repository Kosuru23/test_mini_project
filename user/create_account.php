<?php 
include '../api/auth_checker/admin_check.php'; 
include '../api/database.php';

$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Management - Wine MIS</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">Wine Management System</div>
            <ul class="menu">
                <li><a href="../wine/index.php">Wine</a></li>
                <li><a href="../wine/admin_order.php">Orders</a></li>
                <li><a href="create_account.php" class="active">Admin Management</a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div class="page-title">Admin Account Management</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <div class="register-container" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Register New Admin</h3>
                <form id="adminRegisterForm">
                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>First Name*</label>
                            <input type="text" id="first_name" required style="width: 100%; padding: 8px; margin: 5px 0;">
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" id="middle_name" style="width: 100%; padding: 8px; margin: 5px 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Last Name*</label>
                        <input type="text" id="last_name" required style="width: 100%; padding: 8px; margin: 5px 0;">
                    </div>
                    <div class="form-group">
                        <label>Email Address*</label>
                        <input type="email" id="email" required style="width: 100%; padding: 8px; margin: 5px 0;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="phone_number" style="width: 100%; padding: 8px; margin: 5px 0;">
                    </div>
                    <div class="form-group">
                        <label>Password*</label>
                        <input type="password" id="password" required style="width: 100%; padding: 8px; margin: 5px 0;">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password*</label>
                        <input type="password" id="confirm_password" required style="width: 100%; padding: 8px; margin: 5px 0;">
                    </div>
                    <button type="submit" class="add-btn" style="width: 100%; margin-top: 15px;">Create Admin Account</button>
                </form>
            </div>

            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>System Administrators</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            loadAdmins();
        });

        function loadAdmins() {
            fetch('../api/user_api/admin_list_api.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById("adminTableBody");
                tbody.innerHTML = "";
                data.admins.forEach(admin => {
                    const isActive = admin.user_id == <?php echo $_SESSION['user_id']; ?>;
                    const statusTag = isActive 
                        ? '<span class="status-pill status-paid">Active Now</span>' 
                        : '<span class="status-pill">Offline</span>';

                    tbody.innerHTML += `
                        <tr>
                            <td>${admin.first_name} ${admin.last_name}</td>
                            <td>${admin.email}</td>
                            <td>${statusTag}</td>
                            <td>
                                ${!isActive ? `<button class="delete-btn" onclick="deleteAdmin(${admin.user_id})">Remove</button>` : '<em>Current User</em>'}
                            </td>
                        </tr>
                    `;
                });
            });
        }

        document.getElementById('adminRegisterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Simple Password Validation
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return;
            }

            const data = {
                first_name: document.getElementById('first_name').value,
                middle_name: document.getElementById('middle_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                password: password,
                phone_number: document.getElementById('phone_number').value,
                user_type_id: 1
            };

            fetch('../api/user_api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if(data.status === "success") {
                    document.getElementById('adminRegisterForm').reset();
                    loadAdmins();
                }
            });
        });

        function deleteAdmin(id) {
            if(confirm("Are you sure you want to remove this admin?")) {
                fetch(`../api/user_api/admin_list_api.php?delete_id=${id}`, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    loadAdmins();
                });
            }
        }
    </script>
</body>
</html>