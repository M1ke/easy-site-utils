<?php
require_once('../../../../Websites/php/simpletest/autorun.php');
$s['func-rel-to-script']='../';
require_once($s['func-rel-to-script'].'_functions.php');

class AllTests extends TestSuite {
    function __construct(){
        parent::__construct();
        $this->addFile('test_functions_string.php');
        $this->addFile('test_display.php');
    }
}
?>