<?php

/**
 * CakeRecaptcha
 * 
 * https://github.com/tiimgreen/cake-recaptcha
 *
 */

class RecaptchaResponse {

  public $success;
  public $errorCodes;

  /**
   * Contructor
   *
   * @param boolean $success - response from google - pass/fail
   * @param array $errorCodes - response from google - error codes
   */
  function RecaptchaResponse($success = null, $errorCodes = null) {
    if (isset($success)) {
      $this->success = $success;
    }

    if (isset($errorCodes)) {
      $this->errorCodes = $errorCodes;
    }
  }
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

    curl_close($ch);

    return $data;
  }

  /**
   * Verifies the users input and returns result/error codes.
   *
   * @param string $response - the user response token ($_POST['g-recaptcha-response']).
   * @param string $ipAddress - the user's IP address.
   *
   * @return RecaptchaResponse $recaptchaResponse
   */
  public function verifyResponse($response, $ipAddress = null) {
    if ($response == '' || ! isset($response)) {
      return new RecaptchaResponse(0, ['missing-response-token']);
    }

    $params = [
      'secret' => $this->_secret,
      'response' => $response
    ];

    if (isset($ipAddress)) {
      $params['remoteip'] = $ipAddress;
    }

    $result = json_decode($this->_submitGET($params), true);

    // Needs to be verbose because PHP.
    $recaptchaResponse = new RecaptchaResponse($result['success'] == true ? 1 : 0);

    if ($result['success'] == false) {
      $recaptchaResponse->errorCodes = $result['error-codes'];
    }

    return $recaptchaResponse;
  }
}