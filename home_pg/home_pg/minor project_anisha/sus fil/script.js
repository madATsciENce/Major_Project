function openPopup() {
    document.getElementById("filter-popup").style.display = "block";
}

function closePopup() {
    document.getElementById("filter-popup").style.display = "none";
}

// Handle price slider changes
const priceSlider = document.getElementById("price-slider");
const minValue = document.getElementById("min-value");
const maxValue = document.getElementById("max-value");

priceSlider.addEventListener("input", function() {
    minValue.textContent = "₹" + this.value;
    maxValue.textContent = "₹" + this.max + "+";
});

// Handle filter application
const applyFiltersBtn = document.getElementById("apply-filters");
applyFiltersBtn.addEventListener("click", function() {
    // Get filter values (e.g., selected type, price range)
    // Make AJAX call to server to filter data based on selected values
    // Update the display with the filtered results
    closePopup(); // Close the pop-up after applying filters
});

// Initialize pop-up
const openBtn = document.getElementById("open-filter");
openBtn.addEventListener("click", openPopup);