<?php

namespace Test\Dump;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class IndentationLevels extends Analyzer {
    /* 1 methods */

    public function testDump_IndentationLevels01()  { $this->generic_test('Dump/IndentationLevels.01'); }
}
?>