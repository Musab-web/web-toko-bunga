<?php

@include 'config.php';

function format_rupiah($angka){
    return 'Rp' . number_format($angka, 0, ',', '.');
}

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
};

if(isset($_POST['add_bundling_to_cart'])){

    $bundling_id = $_POST['bundling_id'];
    $bundling_name = $_POST['bundling_name'];
    $bundling_price = $_POST['bundling_price'];
    $bundling_image = $_POST['bundling_image'];
    $bundling_quantity = $_POST['bundling_quantity'];

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$bundling_name' AND user_id = '$user_id'") or die('query failed');

    if(mysqli_num_rows($check_cart_numbers) > 0){
        $message[] = 'bundling already added to cart';
    }else{
        mysqli_query($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES('$user_id', '$bundling_id', '$bundling_name', '$bundling_price', '$bundling_quantity', '$bundling_image')") or die('query failed');
        $message[] = 'bundling added to cart';
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bundling Shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/stylev2.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Bundling Packages</h3>
    <p> <a href="home.php">home</a> / bundling </p>
</section>

<section class="products">

   <h1 class="title">Our Bundling Deals</h1>

   <div class="box-container">

      <?php
         $select_bundling = mysqli_query($conn, "SELECT * FROM `bundling_menu`") or die('query failed');
         if(mysqli_num_rows($select_bundling) > 0){
            while($fetch_bundling = mysqli_fetch_assoc($select_bundling)){
      ?>
      <form action="" method="POST" class="box">
         <div class="price"><?php echo format_rupiah($fetch_bundling['price']); ?></div>
         <img src="uploaded_img/<?php echo $fetch_bundling['image']; ?>" alt="" class="image">
         <div class="name"><?php echo $fetch_bundling['bundling_name']; ?></div>
         <div class="description"><?php echo $fetch_bundling['description']; ?></div>
         <input type="number" name="bundling_quantity" value="1" min="1" class="qty">
         <input type="hidden" name="bundling_id" value="<?php echo $fetch_bundling['bundling_id']; ?>">
         <input type="hidden" name="bundling_name" value="<?php echo $fetch_bundling['bundling_name']; ?>">
         <input type="hidden" name="bundling_price" value="<?php echo $fetch_bundling['price']; ?>">
         <input type="hidden" name="bundling_image" value="<?php echo $fetch_bundling['image']; ?>">
         <input type="submit" value="add bundling to cart" name="add_bundling_to_cart" class="btn">
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no bundling packages added yet!</p>';
      }
      ?>

   </div>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
