<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Information System</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../javascript/functions.js"></script>
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">Management Information System</div>
            <ul class="menu">
                <li><a href="#">Departments</a></li>
                <li><a href="../employees/">Wine</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">Orders</a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="status-container">
        Database Connection Status: <span class="status success">Connected Successfully</span>
    </div>

    <div class="container">
        <div class="page-header">
            <div class="page-title">Wine List</div>
            <button class="add-btn" onclick="openAddWineModal()">Add Wine</button>
        </div>

        <div class="search-filter">
            <input type="text" id="searchBox" placeholder="Search by Name..." onkeyup="filterEmployees()">
            <select id="filterSex" onchange="filterEmployees()">
                <option value="">Filter by Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <select id="filterJobTitle" onchange="filterEmployees()">
                <option value="">Select Job Title</option>
                <option value="Project Manager">Project Manager</option>
                <option value="Software Engineer">Software Engineer</option>
            </select>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Wine Image</th>
                    <th>Wine Name</th>
                    <th>Type</th>
                    <th>Variety</th>
                    <th>Region</th>
                    <th>Country</th>
                    <th>Alcohol %</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="wineTableBody"></tbody>
        </table>

    <div id="addWineModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddWineModal()">&times;</span>
            <h3>Enter Wine Details</h3>
            <div class="form-grid">
                <input type="text" id="wine_name" placeholder="Wine Name">
                <select id="wine_type_id"></select>
                <select id="grape_variety_id"></select>
                <input type="text" id="region" placeholder="Region">
                <select id="wine_country_id"></select>
                <input type="number" step="0.1" id="alcohol_percentage" placeholder="Alcohol %">
                <input type="number" id="quantity" placeholder="Quantity">
                <input type="number" id="price" placeholder="Price">
                <input type="text" id="description" placeholder="Description">
                <div style="grid-column: span 2;">
                    <label for="wine_image">Wine Photo:</label>
                    <input type="file" id="wine_image" accept="image/*">
                </div>
            </div>
            <button onclick="addWine()">Save Wine Details</button>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Update Wine</h3>
            <input type="hidden" id="editId">
            <div class="form-grid">
                <input type="text" id="editWineName" placeholder="Wine Name">
                <select id="editTypeId"></select>
                <select id="editVarietyId"></select>
                <input type="text" id="editRegion" placeholder="Region">
                <select id="editCountryId"></select>
                <input type="number" step="0.1" id="editAlcohol" placeholder="Alcohol %">
                <input type="number" id="editQuantity" placeholder="Quantity">
                <input type="number" id="editPrice" placeholder="Price">
                <input type="text" id="editDescription" placeholder="Description">
                
                <input type="hidden" id="editImageUrl"> 
                
                <div style="grid-column: span 2;">
                    <label>Change Wine Photo (Optional):</label>
                    <input type="file" id="editImageFile" accept="image/*">
                </div>
            </div>
            <button onclick="updateWine()">Save Changes</button>
        </div>
    </div>

    <script src="wine.js"></script>
</body>
</html>