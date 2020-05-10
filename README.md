# Pay.ir online payment - درگاه پرداخت Pay.ir به زبان PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dpsoft/pay.ir.svg?style=flat-square)](https://packagist.org/packages/dpsoft/pay.ir)
[![Total Downloads](https://img.shields.io/packagist/dt/dpsoft/pay.ir.svg?style=flat-square)](https://packagist.org/packages/dpsoft/pay.ir)

## Installation

You can install the package via composer:

```bash
composer require dpsoft/pay.ir
```

## Usage

copy `sample` directory to server. Open `request.php` in browser and bala balah ...

### Request
```php
try {
    $pay = new \Dpsoft\Pay\Pay($apiKey);
    $result = $pay->request($callbackUrl,$amount);
    //save amount and invoice id to forther use
    $_SESSION['amount']=$amount;
    $_SESSION['invoice_id']=$result['invoice_id'];

    $pay->redirectToBank();
    exit();
}catch (Throwable $exception){
    echo $exception->getMessage();
}
```

### Response
```php
try {
    $pay = new \Dpsoft\Pay\Pay();
    $result = $pay->verify($_SESSION['amount'],$_SESSION['invoice_id']);
    //save result. The keys are: card_number,transaction_id and token for example $result['token']
    echo "Successfull transaction.";
}catch (Throwable $exception){
    echo "Error in transaction: ";
}
```
### Testing

``` bash
composer test
```

### Security

If you discover any security related issues, please email info@dpsoft.ir instead of using the issue tracker.

## Credits

- [Dpsoft](https://github.com/dpsoft)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
