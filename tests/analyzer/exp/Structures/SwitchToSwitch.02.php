<?php

$expected     = array('if($a === 31)  /**/  elseif($a === 32)  /**/  elseif($a === 33)  /**/  elseif($a === 34)  /**/  else  /**/  ',
                      'if($a === 1)  /**/  elseif($a === 2)  /**/  elseif($a === 3)  /**/  else  /**/  ',
                      'if($a === 11)  /**/  elseif($a === 12)  /**/  elseif($a === 13)  /**/  ',
                     );

$expected_not = array('if($a === 1) { /**/ } elseif($a === 2) { /**/ } else { /**/ } ',
                     );

?>