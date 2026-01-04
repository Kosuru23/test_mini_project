document.addEventListener("DOMContentLoaded", () => {
    loadWines();            // Load the table
    loadCountryDropdowns(); // Populate the selectors
    loadWinetypeDropdowns();
    loadGrapeVarietyDropdowns();
});

function loadWines() {
    fetch("../api/wine_api.php")
    .then(response => response.json())
    .then(data => {
        let wineTableBody = document.getElementById("wineTableBody");
        wineTableBody.innerHTML = "";
        if (data.status === "success") {
            data.wine.forEach(wine => {
                let imageTag = wine.image_url 
                    ? `<img src="../uploads/${wine.image_url}" width="200" style="border-radius:4px;">` 
                    : `<span>No Image</span>`;

                wineTableBody.innerHTML += `
                <tr>
                    <td>${wine.wine_id}</td>
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
                            ${wine.wine_id}, 
                            '${wine.wine_name}', 
                            ${wine.wine_type_id}, 
                            ${wine.grape_variety_id}, 
                            '${wine.region}', 
                            ${wine.country_id}, 
                            '${wine.alcohol_percentage}', 
                            '${wine.quantity}', 
                            '${wine.price}', 
                            '${wine.description}', 
                            '${wine.image_url}'
                        )">Edit</button>
                        <button class="delete-btn" onclick="deleteWine(${wine.wine_id})">Delete</button>
                    </td>
                </tr>`;
            });
        }
    });
}

function loadCountryDropdowns() {
    fetch("../api/wine_api.php?fetch_countries=true")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Updated IDs to match the HTML corrections above
            const selectors = ["wine_country_id", "editCountryId"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Country</option>';
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
            const selectors = ["wine_type_id", "editTypeId"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Wine Type</option>';
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
            const selectors = ["grape_variety_id", "editVarietyId"]; 
            
            selectors.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Grape Variety</option>';
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