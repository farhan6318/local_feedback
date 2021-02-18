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
 * Create swagger json output based on web service params.
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require($CFG->libdir.'/externallib.php');

use local_feedback\service\base_service;

class webservice_to_swagger extends base_service {
    private $dir;

    /**
     * webservice_to_swagger constructor.
     */
    protected function __construct() {
        global $CFG;
        $this->dir = $CFG->dirroot.'/local/feedback/classes/webservice';
    }

    /**
     * @return webservice_to_swagger
     */
    public static function instance(): webservice_to_swagger {
        return self::get_instance();
    }

    /**
     * Get files from webservice class directory.
     * @return array
     */
    private function get_class_files() {
        $files = scandir($this->dir);
        $files = array_filter($files, function($value) {
            return $value !== '.' && $value !== '..';
        });
        return $files;
    }

    /**
     * Get function definition from classname.
     * @param string $class
     * @return array|null
     */
    private function get_function_def_from_classname(string $class): ?array {
        global $CFG;

        static $functions = [];

        if (empty($functions)) {
            require_once($CFG->dirroot . '/local/feedback/db/services.php');
        }

        $def = null;
        foreach ($functions as $key => $item) {
            if ($item['classname'] === $class) {
                $def = $item;
                $def['_key'] = $key;
                break;
            }
        }

        return $def;
    }

    /**
     * Get swagger type from moodle param type.
     * @param string $paramtype
     * @return string
     */
    private function get_swaggertype_from_paramtype(string $paramtype): string {
        switch ($paramtype) {
            case PARAM_FLOAT:
                $type = 'number';
                break;
            case PARAM_BOOL:
                $type = 'boolean';
                break;
            case PARAM_INT:
                $type = 'integer';
                break;
            default:
                $type = 'string';
        }
        return $type;
    }

    /**
     * Build webservice item from external_single_structure, external_multiple_structure or external_value.
     * @param external_description $sp
     * @return object
     */
    private function build_wsitem(external_description $sp): stdClass {
        if ($sp instanceof external_single_structure) {
            $props = (object) $this->build_wsitems($sp->keys);
            $required = [];
            foreach ($props as $propkey => &$prop) {
                if (!empty($prop->required)) {
                    $required[] = $propkey;
                }
                // The individual properties don't have a 'required' flag.
                // Instead the list of what is required is added to an array at the object level.
                unset($prop->required);
            }
            $return = (object) [
                    'type' => 'object',
                    'properties' => $props
            ];
            if (!empty($required)) {
                // List of all required props.
                $return->required = $required;
            }
            return $return;
        } else if ($sp instanceof external_value) {
            $type = $this->get_swaggertype_from_paramtype($sp->type);
            return (object) [
                    'type' => $type,
                    'required' => $sp->required
            ];
        } else if ($sp instanceof external_multiple_structure) {
            if ($sp->content instanceof external_value) {
                return (object) [
                        'type' => 'array',
                        'items' => (object) ['type' => $this->get_swaggertype_from_paramtype($sp->content->type)]
                ];
            }
            return (object) [
                    'type' => 'array',
                    'items' => $this->build_wsitem($sp->content)
            ];
        }
    }

    /**
     * Build web service items from service params.
     * @param array $sps
     * @return array
     */
    private function build_wsitems(array $sps): array {
        $items = [];
        foreach ($sps as $key => $sp) {
            $items[$key] = $this->build_wsitem($sp);
        }
        return $items;
    }

    /**
     * Build request body.
     * @param string $class
     * @return object
     */
    private function build_requestbody(string $class): stdClass {
        $sps = $class::service_parameters();
        $body = (object) [
                'required' => !empty($sps),
                'content' => (object)
                [
                        'application/json' => (object) [
                                'schema' => (object) [
                                        'type' => 'object',
                                        'properties' => (object) $this->build_wsitems($sps->keys)
                                ]
                        ]
                ]
        ];
        return $body;
    }

    /**
     * Build response body.
     * @param string $class
     * @return object
     */
    private function build_responsebody(string $class): stdClass {
        $sps = $class::service_returns();
        $body = (object) [
                'description' => 'Web service for '.$class,
                'content' => (object)
                [
                        'application/json' => (object) [
                                'schema' => (object) [
                                        'type' => 'object',
                                        'properties' => (object) $this->build_wsitems($sps->keys)
                                ]
                        ]
                ]
        ];
        return $body;
    }

    /**
     * Get the release version of this plugin.
     * @return string
     */
    private function get_release_version(): string {
        global $CFG;
        $plugin = null;
        require($CFG->dirroot.'/local/feedback/version.php');
        return $plugin->release;
    }

    /**
     * Output json string to command line.
     */
    public function to_swagger() {

        $swagger = (object) [
                'openapi' => '3.0.0',
                'info' => [
                        'description' => 'Titus Learning general webservices.',
                        'version' => $this->get_release_version(),
                        'title' => 'Titus Learning general webservices.',
                        'contact' => [
                                'email' => 'guy.thomas@tituslearning.com'
                        ]
                ],
                'tags' => [],
                'paths' => []
        ];
        $files = $this->get_class_files();
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            require_once($this->dir.'/'.$file);
            $class = 'local_feedback\\webservice\\'.basename ($file, '.php');
            $rqbody = $this->build_requestbody($class);
            $respbody = $this->build_responsebody($class);
            $def = $this->get_function_def_from_classname($class);
            $path = $def['_key'];
            $desc = !empty($def['description']) ? $def['description'] : null;
            $swagger->paths["/$path"] = (object) [
                    'post' => (object) [
                            'tags' => (strpos($desc, 'MWP ONLY')) ? ['Moodle Workplace Only'] : ['Moodle'],
                            'summary' => $desc,
                            'requestBody' => $rqbody,
                            'responses' => (object) [
                                    '200' => $respbody
                            ]
                    ]
            ];

        }

        mtrace(json_encode($swagger));
    }
}

webservice_to_swagger::instance()->to_swagger();
