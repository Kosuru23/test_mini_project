document.addEventListener("DOMContentLoaded", () => {
    loadDashboardStats();
    loadWines();            // Load the table
    loadCountryDropdowns(); // Populate the selectors
    loadWinetypeDropdowns();
    loadGrapeVarietyDropdowns();
});

let allWines = [];
let currentPage = 1;
const rowsPerPage = 10;

function loadWines() {
    fetch("../api/wine_api.php")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // 2. Store all data globally so we don't have to fetch again for every page
            allWines = data.wine;
            renderTable();
        }
    });
}

function updatePaginationControls() {
const totalPages = Math.ceil(allWines.length / rowsPerPage);
document.getElementById("pageInfo").innerText = `Page ${currentPage} of ${totalPages || 1}`;

// Disable buttons if at the start or end
document.getElementById("prevBtn").disabled = (currentPage === 1);
document.getElementById("nextBtn").disabled = (currentPage === totalPages || totalPages === 0);
}

function changePage(newPage) {
const totalPages = Math.ceil(allWines.length / rowsPerPage);
if (newPage < 1 || newPage > totalPages) return;

currentPage = newPage;
renderTable();
}

function loadCountryDropdowns() {
    fetch("../api/wine_api.php?fetch_countries=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["wine_country_id", "editCountryId", "filterCountry"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = id.startsWith('filter') ? '<option value="">All Countries</option>' : '<option value="">Select Country</option>';
                    data.countries.forEach(country => {
                        let option = document.createElement("option");
                        option.value = country.id; 
                        option.textContent = country.name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    });
}

function loadWinetypeDropdowns() {
    fetch("../api/wine_api.php?fetch_wine_type=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["wine_type_id", "editTypeId", "filterType"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = id.startsWith('filter') ? '<option value="">All Wine Types</option>' : '<option value="">Select Wine Type</option>';
                    data.wine_types.forEach(wine_type => {
                        let option = document.createElement("option");
                        option.value = wine_type.id; 
                        option.textContent = wine_type.wine_type_name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    });
}

function loadGrapeVarietyDropdowns() {
    fetch("../api/wine_api.php?fetch_grape_variety=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["grape_variety_id", "editVarietyId", "filterVariety"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = id.startsWith('filter') ? '<option value="">All Varieties</option>' : '<option value="">Select Grape Variety</option>';
                    data.grape_varieties.forEach(grape_variety => {
                        let option = document.createElement("option");
                        option.value = grape_variety.id;
                        option.textContent = grape_variety.variety_name;
                        dropdown.appendChild(option);
                    });
                }
            });
        }
    });
}

function addWine() {
    // 1. Define required fields (excluding image)
    const requiredFields = [
        { id: "wine_name", name: "Wine Name" },
        { id: "wine_type_id", name: "Wine Type" },
        { id: "grape_variety_id", name: "Grape Variety" },
        { id: "region", name: "Region" },
        { id: "wine_country_id", name: "Country" },
        { id: "alcohol_percentage", name: "Alcohol %" },
        { id: "quantity", name: "Quantity" },
        { id: "price", name: "Price" },
        { id: "description", name: "Description" }
    ];

    // 2. Validate fields
    for (let field of requiredFields) {
        let element = document.getElementById(field.id);
        if (!element.value || element.value.trim() === "") {
            alert(`Error: ${field.name} is required.`);
            element.focus(); // Highlights the missing field for the user
            return; // Stop the function here
        }
    }

    // 3. If validation passes, proceed with FormData
    let formData = new FormData();
    formData.append('wine_name', document.getElementById("wine_name").value);
    formData.append('wine_type', document.getElementById("wine_type_id").value);
    formData.append('grape_variety', document.getElementById("grape_variety_id").value);
    formData.append('region', document.getElementById("region").value);
    formData.append('country_id', document.getElementById("wine_country_id").value);
    formData.append('alcohol_percentage', document.getElementById("alcohol_percentage").value);
    formData.append('quantity', document.getElementById("quantity").value);
    formData.append('price', document.getElementById("price").value);
    formData.append('description', document.getElementById("description").value);

    let imageInput = document.getElementById("wine_image");
    if (imageInput.files[0]) {
        formData.append('image_file', imageInput.files[0]);
    }

    fetch("../api/wine_api.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeAddWineModal();
        loadWines();
    });
}

function updateWine() {
    // 1. Define required fields (excluding image)
    const requiredFields = [
        { id: "editWineName", name: "Wine Name" },
        { id: "editTypeId", name: "Wine Type" },
        { id: "editVarietyId", name: "Grape Variety" },
        { id: "editRegion", name: "Region" },
        { id: "editCountryId", name: "Country" },
        { id: "editAlcohol", name: "Alcohol %" },
        { id: "editQuantity", name: "Quantity" },
        { id: "editPrice", name: "Price" },
        { id: "editDescription", name: "Description" }
    ];

    // 2. Validate fields
    for (let field of requiredFields) {
        let element = document.getElementById(field.id);
        if (!element.value || element.value.trim() === "") {
            alert(`Error: ${field.name} is required.`);
            element.focus();
            return;
        }
    }

    // 3. Proceed with FormData if valid
    let formData = new FormData();
    formData.append('id', document.getElementById("editId").value);
    formData.append('wine_name', document.getElementById("editWineName").value);
    formData.append('wine_type', document.getElementById("editTypeId").value);
    formData.append('grape_variety', document.getElementById("editVarietyId").value);
    formData.append('region', document.getElementById("editRegion").value);
    formData.append('country_id', document.getElementById("editCountryId").value);
    formData.append('alcohol_percentage', document.getElementById("editAlcohol").value);
    formData.append('quantity', document.getElementById("editQuantity").value);
    formData.append('price', document.getElementById("editPrice").value);
    formData.append('description', document.getElementById("editDescription").value);
    formData.append('existing_image', document.getElementById("editImageUrl").value);

    let fileInput = document.getElementById("editImageFile");
    if (fileInput.files[0]) {
        formData.append('image_file', fileInput.files[0]);
    }

    fetch("../api/wine_api.php?action=update", {
        method: "POST", 
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Wine updated successfully!");
            closeModal();
            loadWines();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error updating wine:", error));
}

function openEditModal(id, name, type_id, variety_id, region, country_id, alcohol, quantity, price, desc, img) {
    document.getElementById("editId").value = id;
    document.getElementById("editWineName").value = name;
    document.getElementById("editTypeId").value = type_id;  
    document.getElementById("editVarietyId").value = variety_id;
    document.getElementById("editRegion").value = region;
    document.getElementById("editCountryId").value = country_id;
    document.getElementById("editAlcohol").value = alcohol;
    document.getElementById("editQuantity").value = quantity;
    document.getElementById("editPrice").value = price;
    document.getElementById("editDescription").value = desc;
    document.getElementById("editImageUrl").value = img;

    // Open the modal
    document.getElementById("editModal").style.display = "flex";
}

function deleteWine(id) {
    if (confirm("Are you sure?")) {
        fetch(`../api/wine_api.php?id=${id}`, { method: "DELETE" }) // Use correct API
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadWines();
        });
    }
}
function openAddWineModal() {
    document.getElementById("addWineModal").style.display = "flex";
}

function closeAddWineModal() {
    document.getElementById("addWineModal").style.display = "none";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}

function loadDashboardStats() {
    fetch('../api/dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                document.getElementById('stat-total-wines').innerText = data.stats.total_wines;
                document.getElementById('stat-total-revenue').innerText = "$" + data.stats.total_revenue;
                document.getElementById('stat-low-stock').innerText = data.stats.low_stock;
                document.getElementById('stat-total-orders').innerText = data.stats.total_orders;
            }
        })
        .catch(error => console.error('Error fetching stats:', error));
}

let filteredWines = []; // Global variable to hold current filtered set

function filterWines() {
    const searchTerm = document.getElementById("searchBox").value.toLowerCase();
    const typeId = document.getElementById("filterType").value;
    const varietyId = document.getElementById("filterVariety").value;
    const countryId = document.getElementById("filterCountry").value;

    // Filter the master list (allWines)
    filteredWines = allWines.filter(wine => {
        const matchesName = wine.wine_name.toLowerCase().includes(searchTerm);
        const matchesType = typeId === "" || wine.wine_type_id == typeId;
        const matchesVariety = varietyId === "" || wine.grape_variety_id == varietyId;
        const matchesCountry = countryId === "" || wine.country_id == countryId;

        return matchesName && matchesType && matchesVariety && matchesCountry;
    });

    currentPage = 1; // Reset to first page on search
    renderTable(filteredWines); 
}

// Modify your renderTable to accept an array (defaults to allWines)
function renderTable(dataToRender = allWines) {
    let wineTableBody = document.getElementById("wineTableBody");
    wineTableBody.innerHTML = "";

    // If we are currently filtering, use the filtered set
    const activeData = (document.getElementById("searchBox").value !== "" || 
                        document.getElementById("filterType").value !== "" ||
                        document.getElementById("filterVariety").value !== "" ||
                        document.getElementById("filterCountry").value !== "") 
                        ? filteredWines : allWines;

    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const paginatedWines = activeData.slice(startIndex, endIndex);

    paginatedWines.forEach((wine, index) => {
        const displayIndex = startIndex + index + 1;
        let imageTag = wine.image_url 
            ? `<img src="../uploads/${wine.image_url}" width="200" style="border-radius:4px;">` 
            : `<span>No Image</span>`;

        wineTableBody.innerHTML += `
        <tr>
            <td>${displayIndex}</td>
            <td>${imageTag}</td> 
            <td>${wine.wine_name}</td>
            <td>${wine.wine_type_name}</td>
            <td>${wine.variety_name}</td>
            <td>${wine.region}</td>
            <td>${wine.name}</td> 
            <td>${wine.alcohol_percentage}%</td>
            <td>${wine.quantity}</td>
            <td>${wine.price}</td>
            <td class="actions">
                <button class="edit-btn" onclick="openEditModal(
                    ${wine.wine_id}, '${wine.wine_name}', ${wine.wine_type_id}, 
                    ${wine.grape_variety_id}, '${wine.region}', ${wine.country_id}, 
                    '${wine.alcohol_percentage}', '${wine.quantity}', '${wine.price}', 
                    '${wine.description}', '${wine.image_url}')">Edit</button>
                <button class="delete-btn" onclick="deleteWine(${wine.wine_id})">Delete</button>
            </td>
        </tr>`;
    });

    updatePaginationControls(activeData.length);
}

function resetFilters() {
    document.getElementById("searchBox").value = "";
    document.getElementById("filterType").value = "";
    document.getElementById("filterVariety").value = "";
    document.getElementById("filterCountry").value = "";
    currentPage = 1;
    renderTable(allWines);
}

// Generic modal helpers
function openModal(id) {
    document.getElementById(id).style.display = "flex";
}

function closeModalId(id) {
    document.getElementById(id).style.display = "none";
}

// Logic to save new categories
function saveCategory(type) {
    let value = "";
    let payload = { action: "add_category", category_type: type };

    if (type === 'wine_type') value = document.getElementById("new_wine_type").value;
    else if (type === 'country') value = document.getElementById("new_country_name").value;
    else if (type === 'grape_variety') value = document.getElementById("new_variety_name").value;

    if (!value.trim()) {
        alert("Please enter a name.");
        return;
    }

    payload.name = value;

    fetch("../api/wine_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(payload)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            // Close the correct modal
            if (type === 'wine_type') closeModalId('addTypeModal');
            else if (type === 'country') closeModalId('addCountryModal');
            else if (type === 'grape_variety') closeModalId('addVarietyModal');
            
            // Refresh dropdowns so the new item appears in "Add Wine"
            loadCountryDropdowns();
            loadWinetypeDropdowns();
            loadGrapeVarietyDropdowns();
        }
    });
}