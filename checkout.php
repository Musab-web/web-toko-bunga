<?php

@include 'config.php';

function format_rupiah($angka){
    return 'Rp' . number_format($angka, 0, ',', '.');
}

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
}

if(isset($_POST['order'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);

    $flat = mysqli_real_escape_string($conn, $_POST['flat']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $pin_code = mysqli_real_escape_string($conn, $_POST['pin_code']);

    $address = "flat no. $flat, $street, $city, $state, $country - $pin_code";
    $placed_on = date('d-M-Y');

    if(empty($name) || empty($number) || empty($email) || empty($method) || empty($flat) || empty($street) || empty($city) || empty($state) || empty($country) || empty($pin_code)){
        $message[] = 'Harap lengkapi semua data sebelum checkout!';
    } else {
        $cart_total = 0;
        $cart_products = [];

        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if(mysqli_num_rows($cart_query) > 0){
            while($cart_item = mysqli_fetch_assoc($cart_query)){
                $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
                $sub_total = ($cart_item['price'] * $cart_item['quantity']);
                $cart_total += $sub_total;
            }
        }

        $total_products = implode(', ', $cart_products);

        $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

        if($cart_total == 0){
            $message[] = 'Keranjang anda kosong!';
        } elseif(mysqli_num_rows($order_query) > 0){
            $message[] = 'Pesanan sudah pernah dibuat!';
        } else {
            mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            $message[] = 'Pesanan berhasil dibuat!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css -->
   <link rel="stylesheet" href="css/stylev2.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>checkout order</h3>
    <p> <a href="home.php">home</a> / checkout </p>
</section>

<section class="display-order">
    <?php
        $grand_total = 0;
        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
    ?>    
    <p> <?php echo $fetch_cart['name'] ?> <span>(<?php echo 'Rp'.number_format($fetch_cart['price'],0,',','.').' x '.$fetch_cart['quantity']  ?>)</span> </p>
    <?php
        }
        }else{
            echo '<p class="empty">Keranjang anda kosong</p>';
        }
    ?>
    <div class="grand-total">Total belanja : <span><?php echo format_rupiah($grand_total); ?></span></div>
</section>

<section class="checkout">

    <form action="" method="POST">

        <h3>isi alamat anda</h3>

        <div class="flex">
            <div class="inputBox">
                <span>Nama Anda :</span>
                <input type="text" name="name" placeholder="masukkan nama">
            </div>
            <div class="inputBox">
                <span>Nomer HP :</span>
                <input type="number" name="number" min="0" placeholder="masukkan Nomer Hp">
            </div>
            <div class="inputBox">
                <span>Email Anda :</span>
                <input type="email" name="email" placeholder="masukkan email">
            </div>
            <div class="inputBox">
                <span>Metode Pembayaran :</span>
                <select name="method">
                    <option value="cash on delivery">Bayar Di Tempat</option>
                    <option value="credit card">Kartu Kredit</option>
                </select>
            </div>
            <div class="inputBox">
                <span>Nomor Numah :</span>
                <input type="text" name="flat" placeholder="nomor rumah">
            </div>
            <div class="inputBox">
                <span>Nama Jalan :</span>
                <input type="text" name="street" placeholder="nama jalan">
            </div>
            <div class="inputBox">
                <span>Provinsi :</span>
                <input type="text" name="city" placeholder="provinsi anda">
            </div>
            <div class="inputBox">
                <span>Kota :</span>
                <input type="text" name="state" placeholder="kota anda">
            </div>
            <div class="inputBox">
                <span>Kecamatan :</span>
                <input type="text" name="country" placeholder="kecamatan anda">
            </div>
            <div class="inputBox">
                <span>Kode Pos :</span>
                <input type="number" min="0" name="pin_code" placeholder="kode pos anda">
            </div>
        </div>

        <input type="submit" name="order" value="pesan sekarang" class="btn">

        <?php
            if(isset($message)){
                foreach($message as $msg){
                    echo '<p class="message">'.$msg.'</p>';
                }
            }
        ?>

    </form>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
