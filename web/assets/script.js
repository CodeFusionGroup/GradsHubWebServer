
// Function to check passwords match
function checkPass(){

    // Password fields
    let password = document.getElementById('inputPassword1');
    let confirmPassword = document.getElementById('inputPassword2');
    //Submit button
    let submitBtn = document.getElementById('submitBtn');
    // Message
    let message = document.getElementById('confirm-message');
    

    //Set the colors we will be using ...
    let good_color = "#66cc66";
    let bad_color  = "#ff6666";

    if(password.value == confirmPassword.value){
        // Passwords match
        confirmPassword.style.backgroundColor = good_color;
        //Button
        submitBtn.disabled = false;
        submitBtn.style.cursor = 'pointer';
        //Message
        // message.innerHTML = ' <img src="../../assets/check.png" alt="Passwords match" height="50" >';
    }else{
        // Passwords dont match
        confirmPassword.style.backgroundColor = bad_color;
        //Disable button
        submitBtn.style.cursor = 'not-allowed';
        submitBtn.disabled = true;
    }

}