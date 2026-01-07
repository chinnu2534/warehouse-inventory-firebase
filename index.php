<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn()) {
  redirect('home.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page">
  <div class="text-center">
    <h1>Welcome</h1>
    <p id="auth-message">Sign in to start your session</p>
  </div>
  <?php echo display_msg($msg); ?>
  
  <!-- Login Form -->
  <form id="login-form" class="clearfix">
    <div class="form-group">
      <label for="email" class="control-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
    </div>
    <div class="form-group">
      <label for="password" class="control-label">Password</label>
      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="form-group">
      <button type="submit" id="login-btn" class="btn btn-info pull-right">Login</button>
    </div>
  </form>
  
  <!-- Signup Form (hidden by default) -->
  <form id="signup-form" class="clearfix" style="display:none;">
    <div class="form-group">
      <label for="signup-name" class="control-label">Full Name</label>
      <input type="text" class="form-control" id="signup-name" name="name" placeholder="Full Name" required>
    </div>
    <div class="form-group">
      <label for="signup-email" class="control-label">Email</label>
      <input type="email" class="form-control" id="signup-email" name="email" placeholder="Email" required>
    </div>
    <div class="form-group">
      <label for="signup-password" class="control-label">Password</label>
      <input type="password" id="signup-password" name="password" class="form-control" placeholder="Password (min 6 chars)" required minlength="6">
    </div>
    <div class="form-group">
      <button type="submit" id="signup-btn" class="btn btn-success pull-right">Sign Up</button>
    </div>
  </form>
  
  <div class="text-center" style="margin-top: 15px;">
    <a href="#" id="toggle-auth">Don't have an account? Sign Up</a>
  </div>
  
  <div id="firebase-error" class="text-danger text-center" style="margin-top: 10px;"></div>

  <script type="module">
    import { auth } from './js/firebase-config.js';
    import { signInWithEmailAndPassword, createUserWithEmailAndPassword, updateProfile } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-auth.js";

    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const toggleLink = document.getElementById('toggle-auth');
    const authMessage = document.getElementById('auth-message');
    const errorDiv = document.getElementById('firebase-error');
    let isLoginMode = true;

    // Toggle between login and signup
    toggleLink.addEventListener('click', (e) => {
      e.preventDefault();
      isLoginMode = !isLoginMode;
      errorDiv.textContent = '';
      if (isLoginMode) {
        loginForm.style.display = 'block';
        signupForm.style.display = 'none';
        toggleLink.textContent = "Don't have an account? Sign Up";
        authMessage.textContent = 'Sign in to start your session';
      } else {
        loginForm.style.display = 'none';
        signupForm.style.display = 'block';
        toggleLink.textContent = "Already have an account? Login";
        authMessage.textContent = 'Create a new account';
      }
    });

    // Send token to backend
    async function sendTokenToBackend(idToken) {
      const response = await fetch('auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'firebase_token=' + encodeURIComponent(idToken)
      });
      const result = await response.json();
      if (result.success) {
        window.location.href = result.redirect || 'home.php';
      } else {
        throw new Error(result.message || 'Authentication failed');
      }
    }

    // Login handler
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const btn = document.getElementById('login-btn');

      errorDiv.textContent = '';
      btn.disabled = true;
      btn.textContent = 'Authenticating...';

      try {
        const userCredential = await signInWithEmailAndPassword(auth, email, password);
        const idToken = await userCredential.user.getIdToken();
        await sendTokenToBackend(idToken);
      } catch (error) {
        console.error(error);
        errorDiv.textContent = error.message;
        btn.disabled = false;
        btn.textContent = 'Login';
      }
    });

    // Signup handler
    signupForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('signup-name').value;
      const email = document.getElementById('signup-email').value;
      const password = document.getElementById('signup-password').value;
      const btn = document.getElementById('signup-btn');

      errorDiv.textContent = '';
      btn.disabled = true;
      btn.textContent = 'Creating Account...';

      try {
        const userCredential = await createUserWithEmailAndPassword(auth, email, password);
        // Update display name in Firebase
        await updateProfile(userCredential.user, { displayName: name });
        const idToken = await userCredential.user.getIdToken(true); // Force refresh to get updated claims
        await sendTokenToBackend(idToken);
      } catch (error) {
        console.error(error);
        errorDiv.textContent = error.message;
        btn.disabled = false;
        btn.textContent = 'Sign Up';
      }
    });
  </script>
</div>
<?php include_once('layouts/footer.php'); ?>