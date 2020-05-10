<?php
include __DIR__."/../vendor/autoload.php";
session_start();

    try {
        $pay = new \Dpsoft\Pay\Pay();
        $result = $pay->verify($_SESSION['amount'],$_SESSION['invoice_id']);

    }catch (Throwable $exception){

    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/milligram/1.3.0/milligram.css">

    <title>Pay.ir Callback Sample</title>
</head>
<body class="container">

<h1>Pay.ir Callback Sample</h1>
<blockquote>
    <p><em><?=isset($exception)?'Exception':'' ?></em></p>
    <p><em><?=isset($exception)?$exception->getMessage():null ?></em></p>
    <?php if(!empty($result)) {?>
        <p><em>Token = <?= $result['token'] ?></em></p>
        <p><em>Card Number = <?= $result['cardNumber'] ?></em></p>
        <p><em>Transaction Id = <?= $result['transId'] ?></em></p>
        <p><em>Invoice Id = <?= $_SESSION['invoice_id'] ?></em></p>
        <p><em>Amount = <?= $_SESSION['amount'] ?></em></p>

    <?php } ?>
</blockquote>

</body>
</html>