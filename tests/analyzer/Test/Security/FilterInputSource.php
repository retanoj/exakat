<?php

namespace Test\Security;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class FilterInputSource extends Analyzer {
    /* 1 methods */

    public function testSecurity_FilterInputSource01()  { $this->generic_test('Security/FilterInputSource.01'); }
}
?>