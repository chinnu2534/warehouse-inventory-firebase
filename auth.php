<?php
require_once('includes/load.php');
require_once('vendor/autoload.php'); // Required for Firebase Admin SDK

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

// Check if firebase_token is provided
if (!isset($_POST['firebase_token']) || empty($_POST['firebase_token'])) {
  echo json_encode(['success' => false, 'message' => 'No token provided']);
  exit;
}

$tokenString = $_POST['firebase_token'];

try {
  // NOTE: You must place your service account JSON file in the includes directory
  $serviceAccountPath = 'includes/service-account.json';

  if (!file_exists($serviceAccountPath)) {
    throw new Exception("Service account file not found at " . $serviceAccountPath);
  }

  $factory = (new Factory)->withServiceAccount($serviceAccountPath);
  $auth = $factory->createAuth();

  $verifiedIdToken = $auth->verifyIdToken($tokenString);
  $uid = $verifiedIdToken->claims()->get('sub');
  $email = $verifiedIdToken->claims()->get('email');
  $name = $verifiedIdToken->claims()->get('name') ?: explode('@', $email)[0];

  // Try to find existing user by email
  $user_id = authenticate_by_email($email);

  if (!$user_id) {
    // Auto-create new user in local database
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

} catch (FailedToVerifyToken $e) {
  echo json_encode(['success' => false, 'message' => 'Invalid token: ' . $e->getMessage()]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>