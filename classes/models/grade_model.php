<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_feedback\models;

defined('MOODLE_INTERNAL') || die;

class grade_model extends base_model {

    /**
     * @var string
     * @wsdesc grade type
     * @wsrequired true
     */
    public $gradetype;

    /**
     * @var float
     * @wsdesc grade min
     * @wsrequired true
     */
    public $grademin;

    /**
     * @var float
     * @wsdesc grade max
     * @wsrequired true
     */
    public $grademax;

    /**
     * @var string[]
     * @wsdesc scalemenu
     * @wsrequired false
     */
    public $scalemenu;

    public function __construct($gradetype, $grademin, $grademax, $scalemenu = []) {
        $this->set_props_construct_args(func_get_args());
    }

    /**
     * This is here for IDE completion.
     * @param array|object $data
     * @return array
     * @throws \coding_exception
     */
    public static function from_data($data): grade_model {
        return parent::do_make_from_data($data);
    }
}