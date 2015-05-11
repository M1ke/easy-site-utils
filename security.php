<?php

/*
    simpleEncode and simpleDecode use openssl_encrypt and openssl_descrypt methods internally.
    $data - data to be encoded, required param
    $passwd - a key, use the same key while encoding and decoding, required param
    $method - aes-128-cbc default
    $options - 0 default
    $iv - a non-NULL Initialization Vector, must be 16 chars  
    Use these methods to hide url parameters. Not suitable for passwords as it is more of obfuscation rathern than encryption
*/

function simpleEncode($data, $passwd, $method='aes-128-cbc', $options=0, $iv='SomeA1AweS0meK5y') {
  return openssl_encrypt ($data, $method, $passwd, $options, $iv);
}

function simpleDecode($data, $passwd, $method='aes-128-cbc', $options=0, $iv='SomeA1AweS0meK5y') {
  return openssl_decrypt ($data, $method, $passwd, $options, $iv);
} 
