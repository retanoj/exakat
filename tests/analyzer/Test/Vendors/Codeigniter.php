<?php

namespace Test\Vendors;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class Codeigniter extends Analyzer {
    /* 1 methods */

    public function testVendors_Codeigniter01()  { $this->generic_test('Vendors/Codeigniter.01'); }
}
?>