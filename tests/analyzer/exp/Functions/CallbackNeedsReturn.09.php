<?php

$expected     = array('spl_autoload_register(E::class . \'::d\')', 
                      'spl_autoload_register(\A\B\C::class . \'::d\')', 
                      'spl_autoload_register(\'\A\B\C::d\')',
                     );

$expected_not = array('spl_autoload_register(E::class . \'::f\')', 
                     );

?>