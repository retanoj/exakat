<?php declare(strict_types = 1);
/*
 * Copyright 2012-2019 Damien Seguy – Exakat SAS <contact(at)exakat.io>
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

namespace Exakat\Analyzer\Variables;

use Exakat\Analyzer\Analyzer;

class AssignedTwiceOrMore extends Analyzer {
    public function analyze() {
        // function foo() { $a = 1; $a = 2;}
        $this->atomIs(self::FUNCTIONS_ALL)
             ->outIs('DEFINITION')
             ->atomIs('Variabledefinition')
             ->variableIsAssigned(2)
             ->outIs('DEFINITION')
             ->hasParent('Assignation', 'LEFT')
             ->inIs('LEFT')
             ->hasNoParent('For', array('EXPRESSION', 'INIT'))
             ->outIs('RIGHT')
             ->atomIs(array('Integer', 'Float', 'Boolean', 'Null', 'Heredoc', 'String'))
             ->hasNoOut('CONCAT')
             ->inIs('RIGHT')
             ->outIs('LEFT');
        $this->prepareQuery();
    }
}

?>
