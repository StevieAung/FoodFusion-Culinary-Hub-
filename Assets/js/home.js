document.addEventListener("DOMContentLoaded", function () {
  // The pop-up logic has been moved to home_page.php to directly use PHP session state.
});

/// Password toggle function
function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const icon = document.querySelector(`#${inputId} + .btn i`);
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("bi-eye-fill");
    icon.classList.add("bi-eye-slash-fill");
  } else {
    input.type = "password";
    icon.classList.remove("bi-eye-slash-fill");
    icon.classList.add("bi-eye-fill");
  }
}

// Registration AJAX
const registrationForm = document.getElementById("registration-form");
if (registrationForm) {
  registrationForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const msg = document.getElementById("reg-message");
    fetch("register.php", {
      method: "POST",
      body: new FormData(this),
    })
      .then((res) => res.json())
      .then((data) => {
        msg.classList.remove("d-none", "text-success", "text-danger");
        msg.classList.add(
          data.status === "success" ? "text-success" : "text-danger"
        );
        msg.textContent = data.message;
        if (data.status === "success") this.reset();
      })
      .catch((err) => {
        msg.classList.remove("d-none", "text-success");
        msg.classList.add("text-danger");
        msg.textContent = "Registration failed. Try again.";
      });
  });
}

// Login AJAX
const loginForm = document.getElementById("login-form");
if (loginForm) {
  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const msg = document.getElementById("login-message");
    fetch("login.php", {
      method: "POST",
      body: new FormData(this),
    })
      .then((res) => res.json())
      .then((data) => {
        msg.classList.remove("d-none", "text-success", "text-danger");
        msg.classList.add(
          data.status === "success" ? "text-success" : "text-danger"
        );
        msg.textContent = data.message;
        if (data.status === "success") {
          // Reload page after successful login
          setTimeout(() => location.reload(), 1000);
        }
      })
      .catch((err) => {
        msg.classList.remove("d-none", "text-success");
        msg.classList.add("text-danger");
        msg.textContent = "Login failed. Try again.";
      });
  });
}
// Logout AJAX
function logoutUser() {
  fetch("logout.php", {
    method: "POST",
  })
    .then((response) => response.text())
    .then((data) => {
      // Optionally, redirect after logout
      window.location.href = "home_page.php";
    })
    .catch((err) => console.error("Logout failed", err));
}
