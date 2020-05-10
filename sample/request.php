<?php
include __DIR__."/../vendor/autoload.php";
session_start();

if (isset($_POST['api_key'])){
    try {
        $pay = new \Dpsoft\Pay\Pay($_POST['api_key']);
        $result = $pay->request($_POST['callback_url'],$_POST['amount']);
        $_SESSION['amount']=$_POST['amount'];
        $_SESSION['invoice_id']=$result['invoice_id'];

        $pay->redirectToBank();
        exit();
    }catch (Throwable $exception){

    }
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

    <title>Pay.ir Request Sample</title>
</head>
<body class="container">

<h1>Pay.ir Sample</h1>
<blockquote>
    <p><em><?=isset($exception)?'Exception':'' ?></em></p>
    <p><em><?=isset($exception)?$exception->getMessage():null ?></em></p>
</blockquote>
<form action="" method="post">
    <label for="api_key">Api Key</label><input type="text" name="api_key" id="api_key" value="<?= $_POST['api_key']??'test' ?>" placeholder="For testing environment enter 'test'">
    <label for="amount">Amounts In Rial</label><input type="number" name="amount" id="amount" value="<?= $_POST['amount']??null ?>">
    <label for="callbackUrl">Callback URL</label><input type="url" name="callback_url" id="callbackUrl" value="<?= $_POST['callback_url']??"http://{$_SERVER['HTTP_HOST']}/callback.php" ?>">
    <input type="submit" value="submit">
</form>

</body>
</html>