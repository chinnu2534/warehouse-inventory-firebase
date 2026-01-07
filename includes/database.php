<?php
/**
 * Firebase Firestore REST API Client
 * Lightweight alternative to the full Firebase SDK
 */

class FirestoreDB
{
  private $projectId;
  private $accessToken;
  private $baseUrl;
  private static $instance = null;

  private function __construct()
  {
    // Read service account
    $serviceAccountPath = __DIR__ . '/service-account.json';

    if (!file_exists($serviceAccountPath)) {
      throw new Exception("Firebase service account file not found");
    }

    $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
    $this->projectId = $serviceAccount['project_id'];
    $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";

    // Get access token using service account
    $this->accessToken = $this->getAccessToken($serviceAccount);
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function getAccessToken($serviceAccount)
  {
    $now = time();
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claim = base64_encode(json_encode([
      'iss' => $serviceAccount['client_email'],
      'scope' => 'https://www.googleapis.com/auth/datastore',
      'aud' => 'https://oauth2.googleapis.com/token',
      'iat' => $now,
      'exp' => $now + 3600
    ]));

    $signature = '';
    openssl_sign("$header.$claim", $signature, $serviceAccount['private_key'], 'SHA256');
    $jwt = "$header.$claim." . base64_encode($signature);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
      ])
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    return $response['access_token'] ?? null;
  }

  private function request($method, $path, $data = null)
  {
    $url = $this->baseUrl . $path;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $this->accessToken,
        'Content-Type: application/json'
      ]
    ]);

    if ($method === 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PATCH') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
  }

  private function parseDocument($doc)
  {
    if (!isset($doc['fields']))
      return null;

    $data = ['id' => basename($doc['name'])];
    foreach ($doc['fields'] as $key => $value) {
      $data[$key] = $this->parseValue($value);
    }
    return $data;
  }

  private function parseValue($value)
  {
    if (isset($value['stringValue']))
      return $value['stringValue'];
    if (isset($value['integerValue']))
      return (int) $value['integerValue'];
    if (isset($value['doubleValue']))
      return (float) $value['doubleValue'];
    if (isset($value['booleanValue']))
      return $value['booleanValue'];
    if (isset($value['timestampValue']))
      return $value['timestampValue'];
    if (isset($value['nullValue']))
      return null;
    return null;
  }

  private function encodeValue($value)
  {
    if (is_null($value))
      return ['nullValue' => null];
    if (is_bool($value))
      return ['booleanValue' => $value];
    if (is_int($value))
      return ['integerValue' => (string) $value];
    if (is_float($value))
      return ['doubleValue' => $value];
    return ['stringValue' => (string) $value];
  }

  public function find_all($collection)
  {
    $response = $this->request('GET', "/$collection");
    $results = [];
    if (isset($response['documents'])) {
      foreach ($response['documents'] as $doc) {
        $results[] = $this->parseDocument($doc);
      }
    }
    return $results;
  }

  public function find_by_id($collection, $id)
  {
    $response = $this->request('GET', "/$collection/$id");
    return $this->parseDocument($response);
  }

  public function find_by_field($collection, $field, $value)
  {
    $docs = $this->find_all($collection);
    foreach ($docs as $doc) {
      if (isset($doc[$field]) && $doc[$field] === $value) {
        return $doc;
      }
    }
    return null;
  }

  public function insert($collection, $data)
  {
    $fields = [];
    foreach ($data as $key => $value) {
      $fields[$key] = $this->encodeValue($value);
    }

    $response = $this->request('POST', "/$collection", ['fields' => $fields]);
    return isset($response['name']) ? basename($response['name']) : null;
  }

  public function update($collection, $id, $data)
  {
    $fields = [];
    foreach ($data as $key => $value) {
      $fields[$key] = $this->encodeValue($value);
    }

    $this->request('PATCH', "/$collection/$id", ['fields' => $fields]);
    return true;
  }

  public function delete($collection, $id)
  {
    $this->request('DELETE', "/$collection/$id");
    return true;
  }

  public function count($collection)
  {
    return count($this->find_all($collection));
  }

  public function escape($str)
  {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}

$db = FirestoreDB::getInstance();

?>