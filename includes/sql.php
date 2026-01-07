<?php
require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows
/*--------------------------------------------------------------*/
function find_all($collection)
{
  global $db;
  return $db->find_all($collection);
}

/*--------------------------------------------------------------*/
/* Find data by id
/*--------------------------------------------------------------*/
function find_by_id($collection, $id)
{
  global $db;
  return $db->find_by_id($collection, $id);
}

/*--------------------------------------------------------------*/
/* Delete by id
/*--------------------------------------------------------------*/
function delete_by_id($collection, $id)
{
  global $db;
  return $db->delete($collection, $id);
}

/*--------------------------------------------------------------*/
/* Count documents
/*--------------------------------------------------------------*/
function count_by_id($collection)
{
  global $db;
  return ['total' => $db->count($collection)];
}

/*--------------------------------------------------------------*/
/* Authenticate by email (Firebase Auth)
/*--------------------------------------------------------------*/
function authenticate_by_email($email)
{
  global $db;
  $user = $db->find_by_field('users', 'email', $email);
  return $user ? $user['id'] : false;
}

/*--------------------------------------------------------------*/
/* Create user from Firebase Auth
/*--------------------------------------------------------------*/
function create_firebase_user($email, $name, $firebase_uid)
{
  global $db;
  $username = explode('@', $email)[0];

  $userData = [
    'name' => $name,
    'username' => $username,
    'email' => $email,
    'firebase_uid' => $firebase_uid,
    'user_level' => 2, // Default user level
    'status' => '1',
    'last_login' => null
  ];

  return $db->insert('users', $userData);
}

/*--------------------------------------------------------------*/
/* Find current logged in user
/*--------------------------------------------------------------*/
function current_user()
{
  static $current_user;
  global $db;
  if (!$current_user) {
    if (isset($_SESSION['user_id'])) {
      $current_user = find_by_id('users', $_SESSION['user_id']);
    }
  }
  return $current_user;
}

/*--------------------------------------------------------------*/
/* Find all users
/*--------------------------------------------------------------*/
function find_all_user()
{
  global $db;
  $users = $db->find_all('users');
  $groups = $db->find_all('user_groups');

  // Join group names
  foreach ($users as &$user) {
    foreach ($groups as $group) {
      if (
        isset($group['group_level']) && isset($user['user_level']) &&
        $group['group_level'] == $user['user_level']
      ) {
        $user['group_name'] = $group['group_name'];
        break;
      }
    }
  }
  return $users;
}

/*--------------------------------------------------------------*/
/* Update last login
/*--------------------------------------------------------------*/
function updateLastLogIn($user_id)
{
  global $db;
  return $db->update('users', $user_id, ['last_login' => date('Y-m-d H:i:s')]);
}

/*--------------------------------------------------------------*/
/* Find group by level
/*--------------------------------------------------------------*/
function find_by_groupLevel($level)
{
  global $db;
  return $db->find_by_field('user_groups', 'group_level', (string) $level);
}

/*--------------------------------------------------------------*/
/* Find group by name
/*--------------------------------------------------------------*/
function find_by_groupName($val)
{
  global $db;
  $group = $db->find_by_field('user_groups', 'group_name', $val);
  return $group ? false : true; // Returns true if NOT found (for validation)
}

/*--------------------------------------------------------------*/
/* Page access control
/*--------------------------------------------------------------*/
function page_require_level($require_level)
{
  global $session;
  $current_user = current_user();
  $login_level = find_by_groupLevel($current_user['user_level'] ?? 0);

  if (!$session->isUserLoggedIn()) {
    $session->msg('d', 'Please login...');
    redirect('index.php', false);
  } elseif ($login_level && isset($login_level['group_status']) && $login_level['group_status'] === '0') {
    $session->msg('d', 'This level user has been banned!');
    redirect('home.php', false);
  } elseif (isset($current_user['user_level']) && $current_user['user_level'] <= (int) $require_level) {
    return true;
  } else {
    $session->msg("d", "Sorry! you don't have permission to view the page.");
    redirect('home.php', false);
  }
}

/*--------------------------------------------------------------*/
/* Join product table (products with categories and media)
/*--------------------------------------------------------------*/
function join_product_table()
{
  global $db;
  $products = $db->find_all('products');
  $categories = $db->find_all('categories');
  $media = $db->find_all('media');

  foreach ($products as &$product) {
    // Join category
    foreach ($categories as $cat) {
      if (isset($product['categorie_id']) && $cat['id'] == $product['categorie_id']) {
        $product['categorie'] = $cat['name'];
        break;
      }
    }
    // Join media
    foreach ($media as $m) {
      if (isset($product['media_id']) && $m['id'] == $product['media_id']) {
        $product['image'] = $m['file_name'];
        break;
      }
    }
    $product['categorie'] = $product['categorie'] ?? 'Uncategorized';
    $product['image'] = $product['image'] ?? '';
  }
  return $products;
}

/*--------------------------------------------------------------*/
/* Find product by title
/*--------------------------------------------------------------*/
function find_product_by_title($product_name)
{
  global $db;
  $products = $db->find_all('products');
  $results = [];
  foreach ($products as $product) {
    if (stripos($product['name'], $product_name) !== false) {
      $results[] = $product;
      if (count($results) >= 5)
        break;
    }
  }
  return $results;
}

/*--------------------------------------------------------------*/
/* Find product info by title
/*--------------------------------------------------------------*/
function find_all_product_info_by_title($title)
{
  global $db;
  $product = $db->find_by_field('products', 'name', $title);
  return $product ? [$product] : [];
}

/*--------------------------------------------------------------*/
/* Update product quantity
/*--------------------------------------------------------------*/
function update_product_qty($qty, $p_id)
{
  global $db;
  $product = $db->find_by_id('products', $p_id);
  if ($product) {
    $newQty = (int) $product['quantity'] - (int) $qty;
    return $db->update('products', $p_id, ['quantity' => $newQty]);
  }
  return false;
}

/*--------------------------------------------------------------*/
/* Find recent products
/*--------------------------------------------------------------*/
function find_recent_product_added($limit)
{
  $products = join_product_table();
  usort($products, function ($a, $b) {
    return strtotime($b['date'] ?? '0') - strtotime($a['date'] ?? '0');
  });
  return array_slice($products, 0, $limit);
}

/*--------------------------------------------------------------*/
/* Find all sales
/*--------------------------------------------------------------*/
function find_all_sale()
{
  global $db;
  $sales = $db->find_all('sales');
  $products = $db->find_all('products');

  foreach ($sales as &$sale) {
    foreach ($products as $product) {
      if ($product['id'] == ($sale['product_id'] ?? '')) {
        $sale['name'] = $product['name'];
        break;
      }
    }
    $sale['name'] = $sale['name'] ?? 'Unknown Product';
  }

  usort($sales, function ($a, $b) {
    return strtotime($b['date'] ?? '0') - strtotime($a['date'] ?? '0');
  });

  return $sales;
}

/*--------------------------------------------------------------*/
/* Find recent sales
/*--------------------------------------------------------------*/
function find_recent_sale_added($limit)
{
  $sales = find_all_sale();
  return array_slice($sales, 0, $limit);
}

/*--------------------------------------------------------------*/
/* Find highest selling products
/*--------------------------------------------------------------*/
function find_higest_saleing_product($limit)
{
  global $db;
  $sales = $db->find_all('sales');
  $products = $db->find_all('products');

  $productSales = [];
  foreach ($sales as $sale) {
    $pid = $sale['product_id'] ?? '';
    if (!isset($productSales[$pid])) {
      $productSales[$pid] = ['totalSold' => 0, 'totalQty' => 0, 'name' => 'Unknown'];
    }
    $productSales[$pid]['totalSold']++;
    $productSales[$pid]['totalQty'] += (int) ($sale['qty'] ?? 0);
  }

  foreach ($products as $product) {
    if (isset($productSales[$product['id']])) {
      $productSales[$product['id']]['name'] = $product['name'];
    }
  }

  uasort($productSales, function ($a, $b) {
    return $b['totalQty'] - $a['totalQty'];
  });

  return array_slice(array_values($productSales), 0, $limit);
}

?>