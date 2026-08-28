function checkPassword() {

    let password = document.getElementById("passwordInput").value;
    let result = document.getElementById("passwordResult");

    if (password.length === 0) {
        result.innerHTML = "Please enter a password.";
        return;
    }

    let score = 0;
    let checks = "";

    
    if (password.length >= 10) {
        score++;
        checks += "✓ At least 10 characters<br>";
    } else {
        checks += "✗ Use at least 10 characters<br>";
    }

    
    if (/[A-Z]/.test(password)) {
        score++;
        checks += "✓ Contains an uppercase letter<br>";
    } else {
        checks += "✗ Add an uppercase letter<br>";
    }

    
    if (/[a-z]/.test(password)) {
        score++;
        checks += "✓ Contains a lowercase letter<br>";
    } else {
        checks += "✗ Add a lowercase letter<br>";
    }

    if (/[0-9]/.test(password)) {
        score++;
        checks += "✓ Contains a number<br>";
    } else {
        checks += "✗ Add a number<br>";
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        score++;
        checks += "✓ Contains a special character<br>";
    } else {
        checks += "✗ Add a special character<br>";
    }

    let strength;

    if (score <= 2) {
        strength = "Weak";
    } else if (score <= 4) {
        strength = "Medium";
    } else {
        strength = "Strong";
    }

    result.innerHTML =
        "<strong>Password Strength: " + strength + "</strong><br><br>" +
        checks;
}
function toggleUserMenu() {

    const menu = document.getElementById("userDropdown");

    menu.classList.toggle("show");

}