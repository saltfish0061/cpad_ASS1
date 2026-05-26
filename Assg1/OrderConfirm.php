<!--
Assignment 1, SCSM2223-25262 (OrderConfirm.php)
Group Name: Strange
-->
<?php require 'libs/db_connect_PDO.php'; ?>
<?php
// Read order info from POST method
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$food_id = $_POST['food_id'];
$qtyFood = $_POST['qtyFood'];
$drink_id = $_POST['drink_id'];
$qtyDrink = $_POST['qtyDrink'];
$delivery = $_POST['delivery'];
$comments = htmlspecialchars($_POST['comments']);

if ($qtyFood == '' && $qtyDrink == '') {
    header("Location: OrderForm.php");
    exit;
}

// User confirm to order
if (isset($_POST['button']) && $_POST['button'] == "Confirm") {
    // 1. Check by email if user is returned customer. If yes then just get user ID.
    // 2. If user is a new customer then need to register user (use 'email' and 'phone' 
    //    information as 'username' and 'password').
    // 3. Save customer's order info into database.
    $user_id = -1;
    $order_id = -1;
    
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $date = new DateTime('now');
    $datetime = $date->format('Y-m-d H:i:s');
    
    $stmt_userCheck = $pdo->prepare("SELECT * FROM users WHERE email=:email");
    
    $stmt_userSave = $pdo->prepare("INSERT INTO users (name, email, phone, username, password, role) VALUES (:name, :email, :phone, :username, :password, :role)");
    
    $stmt_orderSave = $pdo->prepare("INSERT INTO orders (user_id, datetime, delivery, comments, status) VALUES (:user_id, :datetime, :delivery, :comments, :status)");
    
    $stmt_orderCheck = $pdo->prepare("SELECT * FROM orders WHERE user_id=:user_id AND datetime=:datetime");
    
    $stmt_ordermenuSave = $pdo->prepare("INSERT INTO order_menus (order_id, menu_id, qty) VALUES (:order_id, :menu_id, :qty)");
    
    try {
      $stmt_userCheck->execute([':email'=>$email]);
      $user = $stmt_userCheck->fetch();
      if ($user) 
        {
          $user_id = $user['id'];
        } 
      else 
        {
          $stmt_userSave->execute([':name'=>$name,':email'=>$email,':phone'=>$phone,':username'=>$email,':password'=>$phone,':role'=>'CUSTOMER']);
          $stmt_userCheck->execute([':email'=>$email]);
          $user = $stmt_userCheck->fetch();
          $user_id = $user['id'];
        }
        
      $stmt_orderSave->execute([':user_id'=>$user_id,':datetime'=>$datetime,':delivery'=>$delivery,':comments'=>$comments,':status'=>'New']);
      $stmt_orderCheck->execute([':user_id'=>$user_id, ':datetime'=>$datetime]);
      $order = $stmt_orderCheck->fetch();
      $order_id = $order['id'];
      
      if ($qtyFood != '') 
        {
          $stmt_ordermenuSave->execute([':order_id'=>$order_id,':menu_id'=>$food_id,':qty'=>$qtyFood]);
        }
      
      if ($qtyDrink != '') 
        {
          $stmt_ordermenuSave->execute([':order_id'=>$order_id,':menu_id'=>$drink_id,':qty'=>$qtyDrink]);
        }    
    } catch (PDOException $ex) { 
      echo "Database Error: " . $ex->getMessage();
    }
    
    header("Location: Menu.php");
    exit;
}

// Query selected food & drink for confirmation display
$stmt_food = $pdo->prepare("SELECT * FROM menus WHERE id=$food_id");
$stmt_drink = $pdo->prepare("SELECT * FROM menus WHERE id=$drink_id");

$foodPrice = 0;
$drinkPrice = 0;
$totalPrice = 0;

$food = NULL;
$drink = NULL;

try {
  if ($qtyFood != '') {
    $stmt_food->execute();
    $food = $stmt_food->fetch();
    $foodPrice = $food['price'] * $qtyFood;
    $totalPrice += $foodPrice;
  }
  
  if ($qtyDrink != '') {
    $stmt_drink->execute();
    $drink = $stmt_drink->fetch();
    $drinkPrice = $drink['price'] * $qtyDrink;
    $totalPrice += $drinkPrice;
  }
  
} catch (PDOException $ex) { 
  echo "Database Error: " . $ex->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tasty Bites - Order Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="main_style.css"  rel="stylesheet" type="text/css">
</head>

<body>

<!-- Main Layout Table -->
<table border="0" width="100%">
  <!-- Header -->
  <tr>
    <td align="center">
        <?php include 'libs/header.php'; ?>
    </td>
  </tr>

  <!-- Navigation -->
  <tr>
    <td align="center">
        <?php include 'libs/navigation.php'; ?>
    </td>
  </tr>

  <!-- Content Row -->
  <tr>
    <!-- Order Form Section -->
    <td>
      <form action="OrderConfirm.php" method="POST">
        <input type="hidden" name="name" value="<?= $name ?>">
        <input type="hidden" name="email" value="<?= $email ?>">
        <input type="hidden" name="phone" value="<?= $phone ?>">
        <input type="hidden" name="food_id" value="<?= $food_id ?>">
        <input type="hidden" name="qtyFood" value="<?= $qtyFood ?>">
        <input type="hidden" name="drink_id" value="<?= $drink_id ?>">
        <input type="hidden" name="qtyDrink" value="<?= $qtyDrink ?>">
        <input type="hidden" name="delivery" value="<?= $delivery ?>">
        <input type="hidden" name="comments" value="<?= $comments ?>">
        
        <h2>Please Confirm Your Order</h2>
        <hr>
        <h3>Customer Information</h3>
        
        <table cellpadding="3">
          <tr>
            <th align="right">Name: </th>
            <td><?= $name ?></td>
          </tr>
          <tr>
            <th align="right">Email: </th>
            <td><?= $email ?></td>
          </tr>
          <tr>
            <th align="right">Phone Number: </th>
            <td><?= $phone ?></td>
          </tr>
          <tr>
            <th align="right">Delivery Option: </th>
            <td><?= $delivery ?></td>
          </tr>
        <tr>
          <th align="right">Date-Time: </th>
          <td id="clock"></td>
        </tr>
        </table>
        <br><hr>
        <h3>Order Details</h3>

        <table border="0" width="320">
          <tr>
            <th align="center">&nbsp; QTY &nbsp;</th>
            <th align="left">ITEM</th>
            <th align="right">PRICE (RM)</th>
          </tr>
<?php if ($qtyFood != '') { ?>
          <tr>
            <td align="center"><?= $qtyFood ?></td>
            <td><?= $food['name'] ?></td>
            <td align="right"><?= number_format($foodPrice, 2) ?></td>
          </tr>
<?php } ?>
<?php if ($qtyDrink != '') { ?>
          <tr>
            <td align="center"><?= $qtyDrink ?></td>
            <td><?= $drink['name'] ?></td>
            <td align="right"><?= number_format($drinkPrice, 2) ?></td>
          </tr>
<?php } ?>          
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          
          <tr>
            <th colspan="2" align="left">SUBTOTAL</th>
            <td align="right">RM <?= number_format($totalPrice, 2) ?></td>
          </tr>
          
          <tr>
            <th colspan="2" align="left">SST (6%)</th>
            <td align="right">RM <?= number_format($totalPrice * 0.06, 2) ?></td>
          </tr> 
          
          <tr>
            <th colspan="2" align="left">TOTAL</th>
            <td align="right">RM <?= number_format($totalPrice + $totalPrice * 0.06, 2) ?></td>
          </tr> 
        </table>
        
        <p>
          <b>Additional Comments: </b><i><?= $comments ?></i>
        </p>

        <p>
          <input type="submit" name="button" value="Confirm">
          <input type="button" onclick="history.back()" value="Cancel">
        </p>
      </form>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td colspan="2" align="center">
      <?php include 'libs/footer.php'; ?>
    </td>
  </tr>
</table>
</body>
</html>

<script>
// Function to update the display
function updateClock() {
    const now = new Date();
    // Format: "MM/DD/YYYY, HH:MM:SS AM/PM" (varies by locale)
    const liveTime = now.toLocaleString(); 
    
    // Update the HTML element with id="clock"
    document.getElementById('clock').textContent = liveTime;
}

// Call the function every 1000 milliseconds (1 second)
setInterval(updateClock, 1000);

// Initial call to prevent 1-second delay on page load
updateClock();
</script>
