<?php

namespace Drupal\commerce_sbiepay;

use Drupal\Component\Utility\Crypt;

class SBIePayEncryption {

  /**
   * @param type $plainText
   * @param type $key
   *
   * @return type
   */
  public  function encrypt($plainText,  $key) {
    $algo='aes-128-cbc';
    $iv=substr($key, 0, 16);
    $cipherText = openssl_encrypt(
      $plainText,
      $algo,
      $key,
      OPENSSL_RAW_DATA,
      $iv
    );
    $cipherText = base64_encode($cipherText);

    return $cipherText;
  }

  public function decrypt($cipherText,  $key) {
    $algo='aes-128-cbc';

    $iv=substr($key, 0, 16);
    $cipherText = base64_decode($cipherText);	
        $plaintext = openssl_decrypt(
        $cipherText,
        $algo,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    
    return $plaintext;
  }

}
