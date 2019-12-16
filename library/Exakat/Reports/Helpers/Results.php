<?php
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

namespace Exakat\Reports\Helpers;

use SQLite3Result;

class Results {
    private $sqlite       = null;
    private $count        = -1;
    private $values       = null;
    private $options     = array();

    public function __construct(SQLite3Result $res, $options = array()) {
        $this->res = $res;
        
        $this->options = $options;
        $this->options['phpsyntax'] = $this->options['phpsyntax']  ?? array();
    }

    public function load() : int {
        $this->values = array();
        while($row = $this->res->fetchArray(\SQLITE3_ASSOC)) {
            foreach ($this->options['phpsyntax'] as $source => $destination) {
                $row[$destination] = PHPSyntax($row[$source]);
            }
            $this->values[] = $row;
            ++$this->count;
        }

        return $this->count;
    }

    public function getCount() : int {
        return $this->count;
    }

    public function getColumn($column) : array {
        if ($this->values === null) {
            $this->load();
        }

        return array_column($this->values, $column);
    }

    public function toArray() : array {
        if ($this->values === null) {
            $this->load();
        }

        return $this->values;
    }
}

?>