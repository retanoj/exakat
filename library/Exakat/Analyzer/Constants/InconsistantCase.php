<?php
/*
 * Copyright 2012-2016 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
 * This file is part of Exakat.
 *
 * Exakat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Exakat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Exakat.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://exakat.io/>.
 *
*/


namespace Exakat\Analyzer\Constants;

use Exakat\Analyzer\Analyzer;

class InconsistantCase extends Analyzer {

    public function analyze() {
        $mapping = <<<GREMLIN
if (it.get().value('code') == it.get().value('code').toLowerCase()) { 
    x2 = 'lower'; 
} else if (it.get().value('code') == it.get().value('code').toUpperCase()) { 
    x2 = 'upper'; 
} else {
    x2 = 'mixed'; 
}
GREMLIN;
        $storage = array('lowercase' => 'lower',
                         'UPPERCASE' => 'upper',
                         'Mixed'     => 'mixed');

        $this->atomIs(array('Null', 'Boolean'))
             ->raw('map{ '.$mapping.' }')
             ->raw('groupCount("gf").cap("gf").sideEffect{ s = it.get().values().sum(); }.next()');
        $types = (array) $this->rawQuery();

        $store = array();
        $total = 0;
        foreach($storage as $key => $v) {
            $c = empty($types[$v]) ? 0 : $types[$v];
            $store[] = array('key'   => $key,
                             'value' => $c);
            $total += $c;
        }
        Analyzer::$datastore->addRowAnalyzer($this->analyzerQuoted, $store);
        
        if ($total == 0) {
            return;
        }

        $types = array_filter($types, function ($x) use ($total) { return $x > 0 && $x / $total < 0.1; });
        $types = '["'.str_replace('\\', '\\\\', implode('", "', array_keys($types))).'"]';

        $this->atomIs(array('Null', 'Boolean'))
             ->raw('sideEffect{ '.$mapping.' }')
             ->raw('filter{ x2 in '.$types.'}')
             ->back('first');
        $this->prepareQuery();
    }
}

?>
