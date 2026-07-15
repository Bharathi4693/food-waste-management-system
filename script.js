// Live Total Consumption Calculator
function calculateTotal() {

    let morning = parseInt(document.getElementById("morning").value) || 0;
    let afternoon = parseInt(document.getElementById("afternoon").value) || 0;
    let evening = parseInt(document.getElementById("evening").value) || 0;
    let night = parseInt(document.getElementById("night").value) || 0;

    let total = morning + afternoon + evening + night;

    document.getElementById("total_display").innerText = total;
}


// Prevent Negative Values
function validateForm() {

    let inputs = document.querySelectorAll("input[type='number']");

    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value < 0) {
            alert("Quantity cannot be negative!");
            return false;
        }
    }

    return true;
}


// Auto Detect Day Type (for Forecast Page)
function detectDayType() {

    let dateInput = document.getElementById("date");
    if (!dateInput) return;

    let selectedDate = new Date(dateInput.value);
    let day = selectedDate.getDay();

    let result = document.getElementById("day_result");

    if (day === 0 || day === 6) {
        result.innerText = "Weekend Day Detected";
    } else {
        result.innerText = "Normal Day Detected";
    }
}