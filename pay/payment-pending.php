<?php
require_once('../assets/includes/core.php');
include 'header.php';
$configData = configItem();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $sm['config']['name']; ?> | <?= $sm['lang'][868]['text']; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/png" href="<?= $sm['theme']['favicon']['val']; ?>" sizes="32x32">    
    <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="../themes/default/css/crossplatform.css"/>
    <style type="text/css">
        body{
            font-family: 'Rubik' !important;
        }      
    </style>        
</head>

<body>
<div class="pt-4 mb-5 container" style="max-width: 600px" id="lwCheckoutForm">
    <div class="row">
        <div class="col-md-12">
            <form method="post" id="lwPaymentForm">
                <div class="card">
                    <div class="card-header box-shadow" style="background: #f6f6f6">
                        <center><img src="<?= $sm['theme']['logo']['val']; ?>"></center>
                        <h4 class="text-center"><?= $sm['lang'][884]['text']; ?></h4>
                    </div>
                    <div class="card-body" style="padding-top: 45px">
                        <center>
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#f8781c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12" y2="17"></line></svg>
                        </center>
                        <br>
                        <p class="text-center">
                            <?= $sm['lang'][885]['text']; ?>
                        </p> 
                        <center  style="margin-top: 55px;">
                            <a href="<?= $sm['config']['site_url'];?>"><?= $sm['lang'][883]['text']; ?> <?= $sm['config']['name'];?></a>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>    
</div>
<footer class="footer bg-light p-4 text-center">
    <small class="text-muted" style="font-size: 11px"><?= $sm['lang'][878]['text']; ?> <a href="<?= $sm['config']['site_url']; ?>"><?= $sm['config']['name']; ?></a> <?= $sm['lang'][879]['text']; ?>.<br><?= $sm['config']['name']; ?> <?= $sm['lang'][880]['text']; ?>.
</footer>



</body>

</html>
