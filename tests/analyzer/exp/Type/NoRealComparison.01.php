<?php

$expected     = array('1.2 == 2', 
                      '3 != 3.4', 
                      '1.2 == ( 2 / 3 )', 
                      '( 1.2 ) == 4'
);

$expected_not = array('( 1.2) == 4', 
                      'floor(1.2) == 2',
                      '4 == 5');

?>