<?php 
session_start();
include "connect.php";
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/asset/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sevillana&display=swap" rel="stylesheet">
    <title>shop</title>
</head>
<style>
  .profile{
    border: solid 0.5px;
    border-radius: 20px;
  }
</style>
<body>
  
<nav class="navbar navbar-expand-lg navbar-light bg-light">   
  <div class="container-fluid">     
    <a class="navbar-brand" href="dashboard.php"><i class="fas fa-microchip me-2"></i>Internet of Thing</a>     
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">       
      <span class="navbar-toggler-icon"></span>     
    </button>     
    <div class="collapse navbar-collapse" id="navbarSupportedContent">       
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">         
        <li class="nav-item">           
          <a class="nav-link active" aria-current="page" href="dashboard.php">
            <i class="fas fa-home me-1"></i>หน้าแรก
          </a>         
        </li>         
        <li class="nav-item">           
          <a class="nav-link" href="notify.php">
            <i class="fas fa-bell me-1"></i>การบันทึกเเจ้งเตือน
          </a>         
        </li>         
        <li class="nav-item dropdown">           
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">             
            <i class="fas fa-database me-1"></i>ข้อมูล           
          </a>           
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">             
            <li><a class="dropdown-item" href="device.php"><i class="fas fa-tools me-2"></i>ข้อมูลอุปกรณ์</a></li>             
            <li><a class="dropdown-item" href="document.php"><i class="fab fa-telegram me-2"></i>ข้อมูลการใช้งาน bot telegram</a></li>           
          </ul>         
        </li>       
      </ul>         
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 ms-3 me-5">   
        <li class="nav-item dropdown">     
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">       
            <div class="profile">         
              <?php    
                if(isset($_SESSION['picture'])){     
                  echo '<img src="' . $_SESSION['picture'] . '" style="width: 35px; border-radius: 50%;" alt="User Profile">';   
                }   
                else{     
                  echo '<img src="asset/img/user.png"style="width: 35px; border-radius: 50%;" alt="User Profile">';    
                }            
              ?>       
            </div>       
            <?php            
              if(isset($_SESSION['name'])){             
                echo $_SESSION['name'];           
              }else{             
                echo "No name";           
              }       
            ?>       
          </a>     
          <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">       
            <li><a class="dropdown-item" href="setting.php"><i class="fas fa-cog me-2"></i>ตั้งค่าข้อมูลผู้ใช้</a></li>       
            <li><hr class="dropdown-divider"></li>       
            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a></li>     
          </ul>   
        </li> 
      </ul>       
    </div>   
  </div> 
</nav>
