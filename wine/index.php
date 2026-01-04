<?php include '../api/auth_checker/admin_check.php'; ?>
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
            <div class="logo">Wine Management System</div>
            <ul class="menu">
                <li><a href="index.php">Wine</a></li>
                <li><a href="admin_order.php">Orders</a></li>
                <li><a href="../user/create_account.php">Create Admin Account</a></li>
                <li><a href="../api/user_api/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div class="page-title">Wine List</div>
            <button class="add-btn" onclick="openAddWineModal()">Add Wine</button>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Wines</h3>
                <p id="stat-total-wines">0</p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p id="stat-total-revenue">$0.00</p>
            </div>
            <div class="stat-card danger">
                <h3>Low Stock Alerts</h3>
                <p id="stat-low-stock">0</p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p id="stat-total-orders">0</p>
            </div>
        </div>
        
        <div class="search-filter">
            <input type="text" id="searchBox" placeholder="Search by Wine Name..." onkeyup="filterWines()">
            
            <select id="filterType" onchange="filterWines()">
                <option value="">All Wine Types</option>
                </select>
            
            <select id="filterVariety" onchange="filterWines()">
                <option value="">All Varieties</option>
                </select>

            <select id="filterCountry" onchange="filterWines()">
                <option value="">All Countries</option>
                </select>
            
            <button class="add-btn" onclick="resetFilters()" style="background-color: #6c757d;">Reset</button>
        </div>
        
        <div class="management-controls">
            <button class="secondary-btn" onclick="openModal('addTypeModal')">+ Add Wine Type</button>
            <button class="secondary-btn" onclick="openModal('addCountryModal')">+ Add Country</button>
            <button class="secondary-btn" onclick="openModal('addVarietyModal')">+ Add Variety</button>
        </div>

        <div id="addTypeModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModalId('addTypeModal')">&times;</span>
                <h3>Add New Wine Type</h3>
                <div class="form-group">
                    <label for="new_wine_type">Type Name:</label>
                    <input type="text" id="new_wine_type" placeholder="e.g., Sparkling Red">
                </div>
                <button onclick="saveCategory('wine_type')">Save Type</button>
            </div>
        </div>

        <div id="addCountryModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModalId('addCountryModal')">&times;</span>
                <h3>Add New Country</h3>
                <div class="form-group">
                    <label for="new_country_name">Country Name:</label>
                    <input type="text" id="new_country_name" placeholder="e.g., Spain">
                </div>
                <button onclick="saveCategory('country')">Save Country</button>
            </div>
        </div>

        <div id="addVarietyModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModalId('addVarietyModal')">&times;</span>
                <h3>Add New Grape Variety</h3>
                <div class="form-group">
                    <label for="new_grape_variety">Grape Variety:</label>
                    <input type="text" id="new_variety_name" placeholder="e.g., Tempranillo">
                </div>
                <button onclick="saveCategory('grape_variety')">Save Variety</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
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

        <div class="pagination-container">
            <button id="prevBtn" onclick="changePage(currentPage - 1)">Previous</button>
            <span id="pageInfo"></span>
            <button id="nextBtn" onclick="changePage(currentPage + 1)">Next</button>
        </div>

    <div id="addWineModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddWineModal()">&times;</span>
            <h3>Enter Wine Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="wine_name">Wine Name:</label>
                    <input type="text" id="wine_name" placeholder="Enter wine name">
                </div>
                <div class="form-group">
                    <label for="wine_type_id">Wine Type:</label>
                    <select id="wine_type_id"></select>
                </div>
                <div class="form-group">
                    <label for="grape_variety_id">Grape Variety:</label>
                    <select id="grape_variety_id"></select>
                </div>
                <div class="form-group">
                    <label for="region">Region:</label>
                    <input type="text" id="region" placeholder="Enter region">
                </div>
                <div class="form-group">
                    <label for="wine_country_id">Country:</label>
                    <select id="wine_country_id"></select>
                </div>
                <div class="form-group">
                    <label for="alcohol_percentage">Alcohol %:</label>
                    <input type="number" step="0.1" id="alcohol_percentage" placeholder="0.0">
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" placeholder="0.00">
                </div>
                <div class="form-group full-width">
                    <label for="description">Description:</label>
                    <textarea id="description" placeholder="Enter wine description"></textarea>
                </div>
                <div class="form-group full-width">
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
            <h3>Update Wine Information</h3>
            
            <input type="hidden" id="editId">
            <input type="hidden" id="editImageUrl"> 

            <div class="form-grid">
                <div class="form-group">
                    <label for="editWineName">Wine Name:</label>
                    <input type="text" id="editWineName" placeholder="Wine Name">
                </div>
                <div class="form-group">
                    <label for="editTypeId">Wine Type:</label>
                    <select id="editTypeId"></select>
                </div>
                <div class="form-group">
                    <label for="editVarietyId">Grape Variety:</label>
                    <select id="editVarietyId"></select>
                </div>
                <div class="form-group">
                    <label for="editRegion">Region:</label>
                    <input type="text" id="editRegion" placeholder="Region">
                </div>
                <div class="form-group">
                    <label for="editCountryId">Country:</label>
                    <select id="editCountryId"></select>
                </div>
                <div class="form-group">
                    <label for="editAlcohol">Alcohol %:</label>
                    <input type="number" step="0.1" id="editAlcohol" placeholder="Alcohol %">
                </div>
                <div class="form-group">
                    <label for="editQuantity">Current Stock:</label>
                    <input type="number" id="editQuantity" placeholder="Quantity">
                </div>
                <div class="form-group">
                    <label for="editPrice">Unit Price ($):</label>
                    <input type="number" id="editPrice" placeholder="Price">
                </div>
                <div class="form-group full-width">
                    <label for="editDescription">Product Description:</label>
                    <textarea id="editDescription" placeholder="Description"></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label for="editImageFile">Update Wine Photo (Leave blank to keep current):</label>
                    <input type="file" id="editImageFile" accept="image/*">
                </div>
            </div>
            <button onclick="updateWine()">Save Changes</button>
        </div>
    </div>

    <script src="wine.js"></script>
</body>
</html>