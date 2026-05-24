// DOM Loaded event listener to ensure the script runs after the DOM is fully loaded
// DOMContentLoaded event listener to ensure the script runs after the DOM is fully loaded



const userName = document.getElementById('user_name');
const patientId = document.getElementById('patient_id');

document.addEventListener('DOMContentLoaded', function() {
    fetchUserData();
});



// logout Logic

const logoutbtn = document.getElementById(logoutbtn);

logoutbtn.addEventListener("click", () => {
    window.location.href("../php/logout.php");
});







// This function is used to fetch data from the api/json ednpoint and populate the dashboard with user-specific information
function fetchUserData() {
    fetch('../php/api.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.log(data.error)
            return;
        }

        userName.innerHTML = data.user_name + ' ' + data.user_surname;
        patientId.innerHTML = "Patient ID: p-" + data.user_id;
    })
    .catch(error => {
        console.error('Error fecthing user data:', error);
    });
}
