<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    // cek user
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = $email AND password = $password");
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $data = $result->fetch_assoc();

        // cek password
        if($password == $data['password']){

            $_SESSION['login'] = true;
            $_SESSION['email'] = $data['email'];

            header("Location: dashboard.php");
            exit;

        }else{
            echo "Password salah!";
        }

    }else{
        echo "Email tidak ditemukan!";
    }

}else{
    header("Location: login.php");
    exit;
}
?>
