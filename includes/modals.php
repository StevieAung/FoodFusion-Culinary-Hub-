<!-- modals.php -->

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow overflow-hidden">
      <div class="row g-0">
        <!-- Left Image -->
        <div class="col-md-6 d-none d-md-block position-relative p-0">
          <img src="../Assets/images/bg.jpg" alt="Culinary background" class="modal-left-img">
        </div>

        <!-- Right Form -->
        <div class="col-12 col-md-6 p-3 p-md-4 position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                  data-bs-dismiss="modal" aria-label="Close"></button>

          <h5 class="fw-bold text-warning mb-3">Login</h5>
          <form id="login-form">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email</label>
              <input type="email" name="email" id="loginEmail"
                     class="form-control rounded-pill form-control-sm" required>
            </div>

            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="loginPassword"
                       class="form-control rounded-pill form-control-sm" required>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                        onclick="togglePassword('loginPassword')">
                  <i class="bi bi-eye-fill" id="loginEye"></i>
                </button>
              </div>
            </div>

            <div id="login-message" class="text-center mb-3 d-none"></div>
            <button type="submit" class="btn btn-warning w-100 rounded-pill btn-sm fw-bold">Login</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow overflow-hidden">
      <div class="row g-0">
        <!-- Left Image -->
        <div class="col-md-6 d-none d-md-block position-relative p-0">
          <img src="../Assets/images/bg.jpg" alt="Culinary background" class="modal-left-img">
        </div>

        <!-- Right Form -->
        <div class="col-12 col-md-6 p-3 p-md-4 position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                  data-bs-dismiss="modal" aria-label="Close"></button>

          <h5 class="fw-bold text-warning mb-3">Join Us</h5>
          <form id="registration-form">
            <div class="mb-2">
              <label for="regFirstName" class="form-label">First Name</label>
              <input type="text" name="first_name" id="regFirstName"
                     class="form-control rounded-pill form-control-sm" required>
            </div>
            <div class="mb-2">
              <label for="regLastName" class="form-label">Last Name</label>
              <input type="text" name="last_name" id="regLastName"
                     class="form-control rounded-pill form-control-sm" required>
            </div>
            <div class="mb-2">
              <label for="regEmail" class="form-label">Email</label>
              <input type="email" name="email" id="regEmail"
                     class="form-control rounded-pill form-control-sm" required>
            </div>

            <div class="mb-2">
              <label for="regPassword" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="regPassword"
                       class="form-control rounded-pill form-control-sm" required>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                        onclick="togglePassword('regPassword')">
                  <i class="bi bi-eye-fill" id="regEye"></i>
                </button>
              </div>
            </div>

            <div id="reg-message" class="text-center mb-3 d-none"></div>
            <button type="submit" class="btn btn-warning w-100 rounded-pill btn-sm fw-bold">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Auto-show Join Us Modal after 3 seconds -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    }, 3000);
});

// Password toggle function
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
</script>
