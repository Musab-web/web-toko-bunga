<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/stylev2.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>about us</h3>
    <p> <a href="home.php">home</a> / about </p>
</section>

<section class="about">

    <div class="flex">

        <div class="image">
            <img src="images/about-img-1.png" alt="">
        </div>

        <div class="content">
            <h3>apa yang kamu perlukan?</h3>
            <p>semua tersedia di toko kami silahkan melihat produk yang tersedia di toko kami.</p>
            <a href="shop.php" class="btn">belanja sekarang</a>
        </div>

    </div>

    <div class="flex">

        <div class="content">
            <h3>memiliki pertnyaan tentang kami?</h3>
            <p>silahkan hubungi toko kami pada jam kerja akan kami layani dengan sebaik mungkin.</p>
            <a href="contact.php" class="btn">kontak kami</a>
        </div>

        <div class="image">
            <img src="images/about-img-2.jpg" alt="">
        </div>

    </div>

    <div class="flex">

        <div class="image">
            <img src="images/about-img-3.jpg" alt="">
        </div>

        <div class="content">
            <h3>masih ragu dengan kami?</h3>
            <p>kami meiliki berberapa riview dari pelanggan kami.</p>
            <a href="#reviews" class="btn">pelanggan reviews</a>
        </div>

    </div>

</section>

<section class="reviews" id="reviews">

    <h1 class="title">pelanggan reviews</h1>

    <div class="box-container">

        <div class="box">
            <img src="images/pic-1.png" alt="">
            <p>pelayanan cukup baik.</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>leonhart</h3>
        </div>

        <div class="box">
            <img src="images/pic-2.png" alt="">
            <p>koleksi bunga mungkin terbatas tapi terlihat bagus.</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <h3>Olivia</h3>
        </div>

        <div class="box">
            <img src="images/pic-3.png" alt="">
            <p>pelayanan karyawan cukup baik.</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>alex jhon</h3>
        </div>
    </div>

</section>











<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>