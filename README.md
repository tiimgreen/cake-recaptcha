# Cake Recaptcha

For use with CakePHP 1.3

### Usage

Place the recaptcha.php file in `app/vendors`.

The JS files must be placed before the `</head>` tag:

```html
  ...
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
```

Place the HTML within the form:

```php
  ...
  <div class="g-recaptcha" data-sitekey="your_public_site_key"></div>
<?php echo $form->end('Send')?>
```

In your php controller:

```php
App::import('Vendor', 'Recaptcha');

$recaptcha = new Recaptcha('your_secret_key');

if (isset($this->data)) {
  $resp = $recaptcha->verifyResponse($_POST['g-recaptcha-response']);

  if ($resp->success) {
    // Continue...
  } else {
    trigger_error($resp->errorCodes);
  }
}
```