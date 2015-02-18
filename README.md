# Cake Recaptcha

For use with CakePHP 1.3

### Usage

The JS files ust be placed before the `</head>` tag:

```html
  ...
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
```

```php
App::import('Vendor', 'Recaptcha');

App::import('Vendor', 'Recaptcha');

$recaptcha = new Recaptcha(your_secret_key);


if (isset($this->data)) {
  $resp = $recaptcha->verifyResponse($_POST['g-recaptcha-response']);

  if ($resp->success) {
    // Continue...
  } else {
    trigger_error($resp->errorCodes);
  }
}
```