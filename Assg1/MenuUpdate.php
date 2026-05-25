<!--
Lab2, SCSM2223-25262 (MenuUpdate.php)
Group Name: Strange
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php
if (!isset($_REQUEST['id'])) {
  header("Location: MenuManage.php");
  exit();
}
$id = $_REQUEST['id'];

if (isset($_REQUEST['task'])) {
  $task = $_REQUEST['task'];
  if ($task == "Cancel") {
    header("Location: MenuManage.php");
    exit();
  } else if ($task == "Update") {
    $name = $_REQUEST['name'];
    $type = $_REQUEST['type'];
    $price = $_REQUEST['price'];

    $stmt = $pdo->prepare("UPDATE menus SET name=:name, type=:type, price=:price WHERE id=:id");
    try {
      $stmt->execute([':name' => $name, ':type' => $type, ':price' => $price, ':id' => $id]);
      $form_complete = TRUE;
      header("Location: MenuManage.php");
      exit();
    } catch (PDOException $ex) {
      echo "Database Error: " . $ex->getMessage();
    }
  }
}

// Select to display the menu data to be updated
$stmt = $pdo->prepare("SELECT * FROM menus WHERE id=:id");
try {
  $stmt->execute([':id' => $id]);
  $menu = $stmt->fetch();
} catch (PDOException $ex) {
  echo "Database Error: " . $ex->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Tasty Bites - Update Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="main_style.css" rel="stylesheet" type="text/css">
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
      <!-- Menu Section -->
      <td>
        <h2>Update Menu Item</h2>
        <table width="400" cellspacing="5">
          <form action="MenuUpdate.php" method="POST">
            <input type="hidden" name="id" value="<?= $menu['id'] ?>">

            <tr>
              <th align="right">Name:</th>
              <td>
                <input type="text" name="name" value="<?= $menu['name'] ?>" required>
              </td>
            </tr>
            <tr>
              <th align="right">Type:</th>
              <td>
                <select name="type" required>
                  <option value="Food" <?= $menu['type'] == 'Food' ? 'selected' : '' ?>>Food</option>
                  <option value="Drink" <?= $menu['type'] == 'Drink' ? 'selected' : '' ?>>Drink</option>
                </select>
              </td>
            </tr>
            <tr>
              <th align="right">Price (RM):</th>
              <td>
                <input type="number" step="0.01" name="price" value="<?= $menu['price'] ?>" required>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <input type="submit" name="task" value="Update">
                <input type="button" onclick="history.back()" value="Cancel">
              </td>
            </tr>
          </form>
        </table>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td align="center">
        <?php include 'libs/footer.php'; ?>
      </td>
    </tr>

  </table>

</body>

</html>