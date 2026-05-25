<!--
Lab2, SCSM2223-25262 (MenuUpdate.php)
Group Name: ???
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php 
$id = $_REQUEST['id'];
$task = $_REQUEST['task'];

if ($task == "Cancel") {
    // ???
    
} else if ($task == "Update") {
  // ???
}

// Select to display the menu data to be updated
// ???
?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - Update Menu</title>
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
    <!-- Menu Section -->
    <td>
      <h2>Update Menu Item</h2>
      <table width="400" cellspacing="5">
        <form action="MenuUpdate.php" method="POST">
          <input type="hidden" name="id" value="">
          
          <tr>
            <th align="right">Name:</th>
            <td>
              <input type="text" name="name" value="" required>
            </td>
          </tr>
          <tr>
            <th align="right">Type:</th>
            <td>
              <input type="text" name="type" value="" required>
            </td>
          </tr>
          <tr>
            <th align="right">Price (RM):</th>
            <td>
              <input type="number" step="0.01" name="price" value="" required>
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
