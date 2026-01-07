<?php
require_once('vendor/autoload.php');

use Kreait\Firebase\Factory;

class FirestoreDB
{
  private $firestore;
  private static $instance = null;

  private function __construct()
  {
    $serviceAccountPath = __DIR__ . '/service-account.json';

    if (!file_exists($serviceAccountPath)) {
      throw new Exception("Firebase service account file not found at: " . $serviceAccountPath);
    }

    $factory = (new Factory)->withServiceAccount($serviceAccountPath);
    $this->firestore = $factory->createFirestore()->database();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getFirestore()
  {
    return $this->firestore;
  }

  // Get all documents from a collection
  public function find_all($collection)
  {
    $documents = $this->firestore->collection($collection)->documents();
    $results = [];
    foreach ($documents as $document) {
      if ($document->exists()) {
        $data = $document->data();
        $data['id'] = $document->id();
        $results[] = $data;
      }
    }
    return $results;
  }

  // Find document by ID
  public function find_by_id($collection, $id)
  {
    $document = $this->firestore->collection($collection)->document($id)->snapshot();
    if ($document->exists()) {
      $data = $document->data();
      $data['id'] = $document->id();
      return $data;
    }
    return null;
  }

  // Find document by field
  public function find_by_field($collection, $field, $value)
  {
    $query = $this->firestore->collection($collection)->where($field, '=', $value)->documents();
    foreach ($query as $document) {
      if ($document->exists()) {
        $data = $document->data();
        $data['id'] = $document->id();
        return $data;
      }
    }
    return null;
  }

  // Insert document
  public function insert($collection, $data)
  {
    $data['created_at'] = new \DateTime();
    $docRef = $this->firestore->collection($collection)->add($data);
    return $docRef->id();
  }

  // Update document
  public function update($collection, $id, $data)
  {
    $data['updated_at'] = new \DateTime();
    $this->firestore->collection($collection)->document($id)->set($data, ['merge' => true]);
    return true;
  }

  // Delete document
  public function delete($collection, $id)
  {
    $this->firestore->collection($collection)->document($id)->delete();
    return true;
  }

  // Count documents in collection
  public function count($collection)
  {
    $documents = $this->firestore->collection($collection)->documents();
    return iterator_count($documents);
  }

  // Escape function (no-op for Firestore, kept for compatibility)
  public function escape($str)
  {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}

// Global database instance
$db = FirestoreDB::getInstance();

?>