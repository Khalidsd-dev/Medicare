// Run script after DOM loads
document.addEventListener('DOMContentLoaded', function () {

    // User Elements
    const userName = document.getElementById('user_name');
    const userId = document.getElementById('user_id');
    const userInitials = document.getElementById('initials');

    // Logout Button
    const logoutbtn = document.getElementById('logoutbtn');

    // Fetch User Data
    fetchUserData();



    // Fetch User Data Function
    function fetchUserData() {

        fetch('../php/api.php')
            .then(response => response.json())

            .then(data => {

                // Handle API errors
                if (data.error) {
                    console.log(data.error);
                    return;
                }

                // Populate Dashboard
                userName.textContent =
                    data.user_name + ' ' + data.user_surname;

                userId.textContent =
                    "ID: " + data.user_id;

                userInitials.textContent =
                    data.user_initials;

                // Role Debugging
                console.log("USER ROLE:", data.user_role);

            })

            .catch(error => {
                console.error(
                    'Error fetching user data:',
                    error
                );
            });
    }

});