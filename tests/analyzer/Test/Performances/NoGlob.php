<?php

namespace Test\Performances;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class NoGlob extends Analyzer {
    /* 1 methods */

    public function testPerformances_NoGlob01()  { $this->generic_test('Performances/NoGlob.01'); }
}
?>