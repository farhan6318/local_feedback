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

/**
 * This is used for responses that require batching.
 * Class batch_output_model
 * @package local_feedback\models
 */
class batch_output_model extends batch_model {

    /**
     * @var string
     * @wsdesc Total pages that can be returned.
     * @wsrequired true
     */
    public $totalpages;

    /**
     * @var int
     * @wsdesc Total items that can be returned.
     * @wsrequired false
     */
    public $totalitems;


    public function __construct(int $page, int $perpage, int $totalpages, int $totalitems) {
        $this->set_props_construct_args(func_get_args());
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