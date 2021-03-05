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

use local_feedback\exceptions\webservice_exception;

defined('MOODLE_INTERNAL') || die;

class batch_model extends base_model {

    /**
     * @var int
     * @wsdesc Page number.
     * @wsrequired false
     */
    public $page;

    /**
     * @var int
     * @wsdesc Number of items per page.
     * @wsrequired false
     */
    public $perpage;

    public function __construct(int $page = 1, int $perpage = 100) {
        $this->set_props_construct_args(func_get_args());
        if ($this->page < 1) {
            throw new webservice_exception('page cannot be less than 1');
        }
        if ($this->perpage < 1) {
            throw new webservice_exception('perpage cannot be less than 1');
        }
    }

    /**
     * This is here for IDE completion.
     * @param array|object $data
     * @return array
     * @throws \coding_exception
     */
    public static function from_data($data): batch_model {
        return parent::do_make_from_data($data);
    }
}