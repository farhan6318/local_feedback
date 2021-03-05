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

/**
 * Class base_model
 * Taken from format_visualsections\model
 * @copyright Guy Thomas dev@citri.city 2020
 * @package local_feedback
 */
namespace local_feedback\models;

defined('MOODLE_INTERNAL') || die;

use stdClass;

/**
 * Class base_model
 * Taken from format_visualsections\model
 * @copyright Guy Thomas dev@citri.city 2020
 * @package local_feedback
 */
abstract class base_model {

    /**
     * Get construct argument values hashed by argument name.
     * @param $args
     * @return array
     * @throws \ReflectionException
     * @throws \coding_exception
     */
    protected function get_hashed_construct_args($args): array {
        $r = new \ReflectionMethod(get_class($this), '__construct');
        $params = $r->getParameters();

        $argshashed = [];
        $a = 0;
        foreach ($params as $param) {
            if (!isset($args[$a])) {
                if ($param->isDefaultValueAvailable()) {
                    $value = $param->getDefaultValue();
                } else if ($param->allowsNull()) {
                    $value = null;
                } else {
                    $msg = 'Class '.$param->getClass().' __construct parameter '.$param->getName().' requires a value';
                    throw new \coding_exception($msg);
                }
            } else {
                $value = $args[$a];
            }
            $argshashed[$param->getName()] = $value;
            $a++;
        }
        return $argshashed;
    }

    /**
     * Provides models with a way to transform arguments on construction of the model.
     * @param array $args - argument values hashed by argument name.
     * @return array
     */
    protected function transform_construct_args(array $args): array {
        return $args; // No transformations by default.
    }

    /**
     * Set properties by construct arguments.
     * @param array $args
     */
    protected function set_props_construct_args(array $args) {
        $args = $this->get_hashed_construct_args($args);
        $args = $this->transform_construct_args($args);
        foreach ($args as $key => $val) {
            $this->$key = $val;
        }
    }

    public abstract static function from_data($data);

    /**
     * Make model from data.
     * @param object|array $data
     * @return base_model;
     */
    protected static function do_make_from_data($data): base_model {
        if (!is_object($data) && !is_array($data)) {
            throw new \coding_exception('$data must be an object or an array');
        }
        $data = (object) $data;
        $classname = get_called_class();
        $r = new \ReflectionMethod($classname, '__construct');
        $params = $r->getParameters();
        $constructargs = [];
        $optionalparams = [];
        foreach ($params as $param) {
            $paramname = $param->getName();
            $incval = false;
            if (!$param->isOptional()) {
                if (!isset($data->$paramname)) {
                    throw new \coding_exception('$data is missing required param "' . $paramname . '"');
                }
                $incval = true;
                $val = $data->$paramname;
            } else {
                $incval = true;
                $val = isset($data->$paramname) ? $data->$paramname : $param->getDefaultValue();
                $optionalparams[] = $paramname;
            }
            if ($incval) {
                $paramtype = $param->hasType() ? $param->getType()->getName() : '';
                if ($paramtype === 'int') {
                    if (is_number($val) && strval(intval($val)) === strval($val)) {
                        $val = intval($val);
                    } else if ($val === "") {
                        $val = null;
                    }
                } else if ($paramtype === 'null') {
                    if ($val === "") {
                        $val = null;
                    }
                }
                $constructargs[] = $val;
            }
        }

        return new $classname(...$constructargs);
    }

    /**
     * Merges this models properties into the passed in object.
     * @param stdClass $object
     * @return void
     */
    public function merge_properties_to_object(stdClass $object): void {
        $props = get_object_vars($this);
        foreach ($props as $key => $val) {
            $object->$key = $val;
        }
    }

    /**
     * Convert model to standard class (required for db insertion, etc).
     * @return stdClass
     */
    public function to_stdclass() {
        $object = new stdClass();
        $this->merge_properties_to_object($object);
        return $object;
    }

    /**
     * Make a db select array for a specific table using this models properties that occur in the table.
     * @param string $table
     * @param null|string $tabalias
     * @return array
     * @throws \ReflectionException
     */
    public static function make_db_select_array(string $table, ?string $tabalias = null): array {
        global $DB, $CFG;

        $classname = get_called_class();
        $r = new \ReflectionClass($classname);
        $props = $r->getProperties();
        $select = [];
        $cols = $DB->get_columns($table);
        $fields = array_keys($cols);
        foreach ($props as $prop) {
            $propname = $prop->getName();
            // Include properties that are both public and appear in the list of fields for this table.
            if ($prop->isPublic() && in_array($propname, $fields)) {
                $pfx = $tabalias ? $tabalias.'.' : '';
                $select[] = $pfx.$propname;
            }
        }
        return $select;
    }

    /**
     * Make a db select string for a specific table using this models properties that occur in the table.
     * @param string $table
     * @param null|string $tabalias
     * @return string
     * @throws \ReflectionException
     */
    public static function make_db_select_string(string $table, ?string $tabalias = null): string {
        return implode(', ', self::make_db_select_array($table, $tabalias));
    }
}