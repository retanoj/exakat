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

namespace Exakat\Analyzer\Classes;

use Exakat\Analyzer\Analyzer;

class IncompatibleSignature74 extends Analyzer {
    protected $phpVersion = '7.4+';

    public function dependsOn(): array {
        return array('Complete/OverwrittenMethods',
                    );
    }

    public function analyze() {
        // non-matching reference
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->isNot('visibility', 'private')
             ->outIs('ARGUMENT')
             ->savePropertyAs('rank', 'ranked')
             ->savePropertyAs('reference', 'ref')
             ->inIs('ARGUMENT')
             ->outIs('OVERWRITE')
             ->outIs('ARGUMENT')
             ->samePropertyAs('rank', 'ranked')
             ->notSamePropertyAs('reference', 'ref')
             ->back('first');
        $this->prepareQuery();

        // non-matching argument count :
        // abstract : exact count
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->savePropertyAs('count', 'counted')
             ->outIs('OVERWRITE')
             ->is('abstract', true) //then, it is not private
             ->notSamePropertyAs('count', 'counted')
             ->back('first');
        $this->prepareQuery();

        // non-matching argument count :
        // non-abstract : count may be more but not less
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->isNot('visibility', 'private')
             ->savePropertyAs('count', 'counted')
             ->outIs('OVERWRITE')
             ->isNot('abstract', true)
             ->isMore('count', 'counted')
             ->back('first');
        $this->prepareQuery();

        // non-matching typehint
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->isNot('visibility', 'private')
             ->outIs('ARGUMENT')
             ->savePropertyAs('rank', 'ranked')
             ->outIs('TYPEHINT')
             ->savePropertyAs('fullnspath', 'typehint')
             ->back('first')

             ->outIs('OVERWRITE')
             ->outIs('ARGUMENT')
             ->samePropertyAs('rank', 'ranked')
             ->outIs('TYPEHINT')
             ->notSamePropertyAs('fullnspath', 'typehint')
             ->back('first');
        $this->prepareQuery();

        // non-matching return typehint
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->hasOut('OVERWRITE')
             ->savePropertyAs('id', 'method')
             ->isNot('visibility', 'private')
             ->not(
                $this->side()
                     ->outIs('RETURNTYPE')
                     ->inIs('DEFINITION')
                     ->atomIs(array('Class', 'Interface'))
                     ->goToAllImplements(self::INCLUDE_SELF)
                     ->outIs('DEFINITION')
                     ->inIs('RETURNTYPE')
                     ->atomIs(array('Method', 'Magicmethod'))
                     ->inIs('OVERWRITE')
                     ->atomIs(array('Method', 'Magicmethod'))
                     ->samePropertyAs('id', 'method')
             )
             ->not(
                $this->side()
                     ->outIs('RETURNTYPE')
                     ->hasNoIn('DEFINITION')
             )
             ->back('first')
             ->outIs('RETURNTYPE')
             ->savePropertyAs('fullnspath', 'typehint')
             ->back('first')

             ->outIs('OVERWRITE')
             ->outIs('RETURNTYPE')
             ->notSamePropertyAs('fullnspath', 'typehint')
             ->back('first');
        $this->prepareQuery();

        // non-matching nullable
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->isNot('visibility', 'private')
             ->outIs('ARGUMENT')
             ->savePropertyAs('rank', 'ranked')
             ->outIs('TYPEHINT')
             ->isNot('nullable', true)
             ->savePropertyAs('nullable', 'n')
             ->back('first')

             ->outIs('OVERWRITE')
             ->outWithRank('ARGUMENT', 'ranked')
             ->outIs('TYPEHINT')
             ->notSamePropertyAs('nullable', 'n')
             ->back('first');
        $this->prepareQuery();

        // non-matching return nullable
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->isNot('visibility', 'private')
             ->outIs('RETURNTYPE')
             ->is('nullable', true)
             ->savePropertyAs('nullable', 'n')
             ->back('first')

             ->outIs('OVERWRITE')
             ->outIs('RETURNTYPE')
             ->notSamePropertyAs('nullable', 'n')

             ->back('first');
        $this->prepareQuery();

        // non-matching visibility
        $this->atomIs(self::FUNCTIONS_METHOD)
             ->savePropertyAs('visibility', 'v')
             ->outIs('OVERWRITE')
             ->raw(<<<'GREMLIN'
filter{ 
    if (it.get().properties("visibility").any()) { 
        if (v == "private") {
            it.get().value("visibility") in ["protected", "none", "public"];
        } else if (v == "protected") {
            it.get().value("visibility") in ["none", "public"];
        } else {
            false;
        }
    } else { 
        visibility != false; 
    }
}
GREMLIN
)
             ->back('first');
        $this->prepareQuery();
    }
}

?>
