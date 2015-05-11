<?php

/*
    simpleEncode and simpleDecode use openssl_encrypt and openssl_descrypt methods internally.
    Use these methods to hide url parameters. Not suitable for passwords as it is more of obfuscation rathern than encryption
*/

function simpleEncode($data, $method='aes-128-cbc', $passwd='s0meSeCreTPa55w0Rd', $options=0, $iv='SomeA1AweS0meK5y') {
  return openssl_encrypt ($data, $method, $passwd, $options, $iv);
}

function simpleDecode($data, $method='aes-128-cbc', $passwd='s0meSeCreTPa55w0Rd', $options=0, $iv='SomeA1AweS0meK5y') {
  return openssl_decrypt ($data, $method, $passwd, $options, $iv);
} 
