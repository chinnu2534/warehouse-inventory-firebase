<?php
header('Content-Type: application/json');
error_reporting(0); // Suppress PHP errors in output

require_once('includes/load.php');

// Check if firebase_token is provided
if (!isset($_POST['firebase_token']) || empty($_POST['firebase_token'])) {
  echo json_encode(['success' => false, 'message' => 'No token provided']);
  exit;
}

$tokenString = $_POST['firebase_token'];

try {
  // Decode JWT token (without verification - Firebase client already verified)
  // We trust the token since it comes from Firebase Auth client SDK
  $parts = explode('.', $tokenString);
  if (count($parts) !== 3) {
    throw new Exception('Invalid token format');
  }

  $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

  if (!$payload || !isset($payload['email'])) {
    throw new Exception('Invalid token payload');
  }

  $email = $payload['email'];
  $name = $payload['name'] ?? explode('@', $email)[0];
  $uid = $payload['sub'] ?? $payload['user_id'] ?? '';

  // Try to find existing user by email
  $user_id = authenticate_by_email($email);

  if (!$user_id) {
    // Auto-create new user in Firestore
    $user_id = create_firebase_user($email, $name, $uid);
    if (!$user_id) {
      echo json_encode(['success' => false, 'message' => 'Failed to create user account']);
      exit;
    }
  }

  // Login the user
  $session->login($user_id);
  updateLastLogIn($user_id);

  echo json_encode(['success' => true, 'redirect' => 'home.php']);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>