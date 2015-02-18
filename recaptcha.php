<?php

class RecaptchaResponse {
  public $success;
  public $errorCodes;
}

class Recaptcha {

  private $_secret;
  private $_verifyUrl = "https://www.google.com/recaptcha/api/siteverify";

  /**
   * Contructor
   *
   * @param string $secret
   */
  function Recaptcha($secret) {
    if ($secret == '' || ! isset($secret)) {
      trigger_error("You need to provide a secret key, get one here: 'https://www.google.com/recaptcha/intro/index.html'");
    }

    $this->_secret = $secret;
  }

  /**
   * Encodes GET URL with params into query string format.
   *
   * @param array $params
   *
   * @return string - encoded URL
   */
  private function _encodeURL($params) {
    return $this->_verifyUrl .= '?' . http_build_query($params);
  }

  /**
   * Submits a Get request to Google with parameters.
   *
   * @param array $params
   *
   * @return string $data - response from server
   */
  private function _submitGET($params) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->_encodeURL($params)); // Set URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return output
    curl_setopt($ch, CURLOPT_HEADER, false); // Don't return headers

    $data = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $data;
  }

  /**
   * Submits a Get request to Google with parameters.
   *
   * @param string $response - the user response token ($_POST['g-recaptcha-response']).
   * @param string $ipAddress - the user's IP address.
   *
   * @return RecaptchaResponse $recaptchaResponse
   */
  public function verifyResponse($response, $ipAddress = null) {
    if ($response == '' || ! isset($response)) {
      $recaptchaResponse = new RecaptchaResponse();
      $recaptchaResponse->success = 0;
      $recaptchaResponse->errorCodes = [
        'missing-response-token'
      ];
      return $recaptchaResponse;
    }

    $params = [
      'secret' => $this->_secret,
      'response' => $response
    ];

    if (isset($ipAddress)) {
      $params['remoteip'] = $ipAddress;
    }

    $getReponse = $this->_submitGET($params);

    $result = json_decode($getReponse, true);

    $recaptchaResponse = new RecaptchaResponse();
    $recaptchaResponse->success = $result['success'] == true ? 1 : 0;

    if ($result['success'] == false) {
      $recaptchaResponse->errorCodes = $result['error-codes'];
    }

    return $recaptchaResponse;
  }
}