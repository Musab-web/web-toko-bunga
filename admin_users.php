<?php
@include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:index.php');
};

// Hapus User
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

// Edit User
if(isset($_POST['update_user'])){
   $update_id = $_POST['update_id'];
   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
   $update_user_type = $_POST['update_user_type'];
   
   mysqli_query($conn, "UPDATE `users` SET name = '$update_name', email = '$update_email', user_type = '$update_user_type' WHERE id = '$update_id'") or die('query failed');
   
   $message[] = 'User updated successfully!';
   header('location:admin_users.php');
}

// Tambah User
if(isset($_POST['add_user'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, md5($_POST['password']));
   $user_type = $_POST['user_type'];
   
   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');
   
   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'User already exists!';
   }else{
      mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$password', '$user_type')") or die('query failed');
      $message[] = 'User registered successfully!';
      header('location:admin_users.php');
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users Management</title>
   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <!-- custom admin css file link -->
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      /* Styling untuk form tambah user */
      .add-users form{
         max-width: 70rem;
         border-radius: .5rem;
         background-color: var(--white);
         border:var(--border);
         padding:2rem;
         margin:0 auto;
         margin-bottom: 2rem;
         box-shadow: var(--box-shadow);
      }

      .add-users form h3{
         font-size: 2.5rem;
         text-transform: uppercase;
         color:var(--black);
         margin-bottom: 1rem;
      }

      .add-users form .flex{
         display: flex;
         flex-wrap: wrap;
         gap:1.5rem;
      }

      .add-users form .flex .inputBox{
         flex:1 1 40rem;
      }

      .add-users form .flex .inputBox input,
      .add-users form .flex .inputBox select{
         width: 100%;
         margin:1rem 0;
         font-size: 1.8rem;
         color:var(--black);
         border-radius: .5rem;
         background-color: var(--light-bg);
         padding:1.2rem 1.4rem;
         border:var(--border);
      }

      /* Styling untuk form edit user */
      .edit-user-form{
         max-width: 70rem;
         margin:0 auto;
         margin-bottom: 2rem;
      }

      .edit-user-form form{
         background-color: var(--white);
         border:var(--border);
         border-radius: .5rem;
         padding:2rem;
         box-shadow: var(--box-shadow);
      }

      .edit-user-form form input,
      .edit-user-form form select{
         width: 100%;
         margin:1rem 0;
         font-size: 1.8rem;
         color:var(--black);
         border-radius: .5rem;
         background-color: var(--light-bg);
         padding:1.2rem 1.4rem;
         border:var(--border);
      }

      .flex-btn{
         display: flex;
         gap:1rem;
      }
   </style>
</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<!-- Tampilkan pesan -->
<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<!-- Form tambah user baru -->
<section class="add-users">
   <h1 class="title">Add New User</h1>
   <form action="" method="post">
      <div class="flex">
         <div class="inputBox">
            <input type="text" name="name" placeholder="Enter username" required class="box">
            <input type="email" name="email" placeholder="Enter email" required class="box">
            <input type="password" name="password" placeholder="Enter password" required class="box">
            <select name="user_type" class="box">
               <option value="user">User</option>
               <option value="admin">Admin</option>
            </select>
            <input type="submit" name="add_user" value="Add User" class="btn">
         </div>
      </div>
   </form>
</section>

<!-- Form edit user -->
<section class="edit-user-form">
   <?php
      if(isset($_GET['edit'])){
         $edit_id = $_GET['edit'];
         $edit_query = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$edit_id'") or die('query failed');
         if(mysqli_num_rows($edit_query) > 0){
            $fetch_edit = mysqli_fetch_assoc($edit_query);
   ?>
   <h1 class="title">Edit User</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
      <input type="text" name="update_name" value="<?php echo $fetch_edit['name']; ?>" required placeholder="Enter username" class="box">
      <input type="email" name="update_email" value="<?php echo $fetch_edit['email']; ?>" required placeholder="Enter email" class="box">
      <select name="update_user_type" class="box">
         <option value="user" <?php if($fetch_edit['user_type'] == 'user'){ echo 'selected'; } ?>>User</option>
         <option value="admin" <?php if($fetch_edit['user_type'] == 'admin'){ echo 'selected'; } ?>>Admin</option>
      </select>
      <div class="flex-btn">
         <input type="submit" name="update_user" value="Update User" class="btn">
         <a href="admin_users.php" class="option-btn">Cancel</a>
      </div>
   </form>
   <?php
         }
      }
   ?>
</section>

<!-- Menampilkan daftar user -->
<section class="users">
   <h1 class="title">Users Account</h1>
   <div class="box-container">
   <?php
      $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
      if(mysqli_num_rows($select_users) > 0){
         while($fetch_users = mysqli_fetch_assoc($select_users)){
   ?>
   <div class="box">
      <p>User ID : <span><?php echo $fetch_users['id']; ?></span></p>
      <p>Username : <span><?php echo $fetch_users['name']; ?></span></p>
      <p>Email : <span><?php echo $fetch_users['email']; ?></span></p>
      <p>User Type : <span style="color:<?php if($fetch_users['user_type'] == 'admin'){ echo 'var(--orange)'; }; ?>"><?php echo $fetch_users['user_type']; ?></span></p>
      <div class="flex-btn">
         <a href="admin_users.php?edit=<?php echo $fetch_users['id']; ?>" class="option-btn">Edit</a>
         <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">Delete</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No users available!</p>';
      }
   ?>
   </div>
</section>

<script src="js/admin_script.js"></script>

</body>
</html>