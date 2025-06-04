<?php

@include 'config.php';

function format_rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:index.php');
}

// Variabel untuk menyimpan data bundling yang ingin diupdate
$update_id = '';
$name = '';
$price = '';
$description = '';
$old_image = '';
$update = false;

// Cek apakah ini mode update
if(isset($_GET['update'])){
   $update = true;
   $update_id = $_GET['update'];
   $select_bundling = mysqli_query($conn, "SELECT * FROM `bundling_menu` WHERE bundling_id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_bundling) > 0){
      $fetch = mysqli_fetch_assoc($select_bundling);
      $name = $fetch['bundling_name'];
      $price = $fetch['price'];
      $description = $fetch['description'];
      $old_image = $fetch['image'];
   }
}

// Handling ketika form disubmit untuk menambah bundling baru
if(isset($_POST['add_bundling'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_name = mysqli_query($conn, "SELECT bundling_name FROM `bundling_menu` WHERE bundling_name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_name) > 0){
      $message[] = 'nama bundling sudah ada!';
   }else{
      $insert_bundling = mysqli_query($conn, "INSERT INTO `bundling_menu`(bundling_name, description, price, image) VALUES('$name', '$description', '$price', '$image')") or die('query failed');

      if($insert_bundling){
         if($image_size > 2000000){
            $message[] = 'gambar terlalu besar!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'bundling berhasil ditambahkan!';
         }
      }
   }

}

// Handling ketika form disubmit untuk update bundling
if(isset($_POST['update_bundling'])){

   $update_id = $_POST['bundling_id'];
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   
   mysqli_query($conn, "UPDATE `bundling_menu` SET bundling_name = '$name', description = '$description', price = '$price' WHERE bundling_id = '$update_id'") or die('query failed');

   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $old_image = $_POST['old_image'];
   
   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'gambar terlalu besar!';
      }else{
         mysqli_query($conn, "UPDATE `bundling_menu` SET image = '$image' WHERE bundling_id = '$update_id'") or die('query failed');
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('uploaded_img/'.$old_image);
      }
   }

   $message[] = 'bundling berhasil diperbarui!';
   header('location:admin_bundling.php');
}

// Handling ketika request delete
if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_image = mysqli_query($conn, "SELECT image FROM `bundling_menu` WHERE bundling_id = '$delete_id'") or die('query failed');
   $fetch_image = mysqli_fetch_assoc($select_image);
   unlink('uploaded_img/'.$fetch_image['image']);
   mysqli_query($conn, "DELETE FROM `bundling_menu` WHERE bundling_id = '$delete_id'") or die('query failed');
   header('location:admin_bundling.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bundling</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      .add-products form .flex-btn {
         display: flex;
         gap: 1rem;
         margin-top: 1.5rem;
      }
      .add-products form .image-preview {
         max-width: 300px;
         margin: 1rem auto;
      }
      .add-products form .image-preview img {
         width: 100%;
         height: auto;
         object-fit: contain;
      }
      .option-btn {
         display: inline-block;
         background-color: #ffa64d;
         color: #fff;
         border-radius: .5rem;
         padding: 1rem 3rem;
         cursor: pointer;
         font-size: 1.8rem;
         text-decoration: none;
      }
      .option-btn:hover {
         background-color: #ff8000;
      }
   </style>

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="add-products">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3><?php echo ($update) ? 'update bundling' : 'add new bundling'; ?></h3>
      
      <?php if($update): ?>
      <input type="hidden" name="bundling_id" value="<?php echo $update_id; ?>">
      <input type="hidden" name="old_image" value="<?php echo $old_image; ?>">
      
      <div class="image-preview">
         <img src="uploaded_img/<?php echo $old_image; ?>" alt="Current Image">
      </div>
      <?php endif; ?>
      
      <input type="text" class="box" required placeholder="enter bundling name" name="name" value="<?php echo $name; ?>">
      <input type="number" min="0" class="box" required placeholder="enter bundling price" name="price" value="<?php echo $price; ?>">
      <textarea name="description" class="box" required placeholder="enter bundling description" cols="30" rows="10"><?php echo $description; ?></textarea>
      <input type="file" accept="image/jpg, image/jpeg, image/png" <?php echo (!$update) ? 'required' : ''; ?> class="box" name="image">
      
      <div class="flex-btn">
         <?php if($update): ?>
            <input type="submit" value="update bundling" name="update_bundling" class="btn">
            <a href="admin_bundling.php" class="option-btn">cancel</a>
         <?php else: ?>
            <input type="submit" value="add bundling" name="add_bundling" class="btn">
         <?php endif; ?>
      </div>
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_bundling = mysqli_query($conn, "SELECT * FROM `bundling_menu`") or die('query failed');
         if(mysqli_num_rows($select_bundling) > 0){
            while($fetch = mysqli_fetch_assoc($select_bundling)){
      ?>
      <div class="box">
         <div class="price"><?php echo format_rupiah($fetch['price']); ?>,-</div>
         <img class="image" src="uploaded_img/<?php echo $fetch['image']; ?>" alt="">
         <div class="name"><?php echo $fetch['bundling_name']; ?></div>
         <div class="details"><?php echo $fetch['description']; ?></div> 
         <div class="flex-btn">
            <a href="admin_bundling.php?update=<?php echo $fetch['bundling_id']; ?>" class="option-btn">update</a>
            <a href="admin_bundling.php?delete=<?php echo $fetch['bundling_id']; ?>" class="delete-btn" onclick="return confirm('delete this bundling?');">delete</a>
         </div>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">tidak ada bundling ditambahkan!</p>';
      }
      ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>