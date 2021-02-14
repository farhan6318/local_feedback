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

defined('MOODLE_INTERNAL') || die();

use local_feedback\definition_helper;

/**
 * Testable version of definition_helper.
 * Class definition_helper_testable
 */
class definition_helper_testable extends definition_helper {
    /**
     * Magic method for getting protected / private properties.
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($name) {
        return $this->$name;
    }

    /**
     * Magic method for setting protected / private properties.
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws \coding_exception
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Magic method to allow protected / private methods to be called.
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        $reflection = new ReflectionObject($this);
        $parentreflection = $reflection->getParentClass();
        $method = $parentreflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this, $arguments);
    }
}

/**
 * Class borrowed from theme_snap for testing purposes.
 * Class course_toc_module
 */
class course_toc_footer implements \renderable {

    /**
     * @var boolean
     */
    public $canaddnewsection;

    /**
     * @var string
     */
    public $imgurladdnewsection;

    /**
     * @var string
     */
    public $imgurltools;

}

class course_toc_progress {

    /**
     * @var stdClass
     * @wsparam {
     *     "complete": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Number of items completed"
     *     },
     *     "total": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Total items to complete"
     *     }
     * };
     */
    public $progress;

    /**
     * @var bool - completed?
     */
    public $completed;

    /**
     * @var string - pixurl for completed
     */
    public $pixcompleted;
}

class course_toc_chapter implements \renderable {

    /**
     * @var bool
     */
    public $outputlink;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $classes;

    /**
     * @var bool
     */
    public $iscurrent;

    /**
     * @var string
     */
    public $availabilityclass;

    /**
     * @var string
     */
    public $availabilitystatus;

    /**
     * @var course_toc_progress
     */
    public $progress;

    /**
     * @var string
     */
    public $url;

}

/**
 * Class borrowed from theme_snap for testing purposes.
 * Class course_toc_module
 */
class course_toc_module implements \renderable {

    /**
     * @var string module name
     */
    public $modname;

    /**
     * @var boolean is this module visible to the current user?
     */
    public $uservisible;

    /**
     * @var \moodle_url url to module icon
     */
    public $iconurl;

    /**
     * @var string formatted name of module
     */
    public $formattedname;

    /**
     * @var string - any screen reader info to display
     */
    public $srinfo;

    /**
     * @var string - hash bang #section-x&module-x
     */
    public $url;

    /**
     * @var int - course module id
     */
    public $cmid;

}

/**
 * Class borrowed from theme_snap for testing purposes.
 * Class course_toc
 */
class course_toc {

    /**
     * @var bool
     */
    public $formatsupportstoc = false;

    /**
     * @var course_toc_module[]
     */
    public $modules = [];

    /**
     * @var \stdClass
     * @wsparam {
     *     chapters: {
     *        type: course_toc_chapter[],
     *        description: "An array of course_toc_chapter objects"
     *     },
     *     listlarge: {
     *        type: PARAM_ALPHAEXT,
     *        description: "list-large css class when TOC has more than 9 chapters"
     *     }
     * };
     */
    public $chapters;

    /**
     * @var course_toc_footer
     */
    public $footer;

    /**
     * @var \stdClass
     */
    protected $course;

    /**
     * @var \format_base
     */
    protected $format;

    /**
     * @var int
     */
    protected $numsections;
}

/**
 * Simple class for testing.
 * Class wsdocs_teeth
 */
class wsdocs_teeth {
    /**
     * @var string type of teeth
     */
    public $type;

    /**
     * @var string left or right
     */
    public $side;

    /**
     * @var boolean top if true, else bottom
     */
    public $top;
}

class wsdocs_testing {
    /**
     * @var string My head
     */
    public $head;

    /**
     * @var string My shoulders
     * @wsrequired
     */
    public $shoulders;

    /**
     * @var string
     * @wstype PARAM_ALPHA
     * @wsdesc A description of my knees.
     * @wsallownull false
     */
    public $knees;

    /**
     * @var int
     * @wstype PARAM_INT
     * @wsdescription Count of my toes.
     */
    public $toes;

    /**
     * @var string
     * @wstype PARAM_TEXT
     * @wsdesc A description of my ears.
     * @wsrequired true
     */
    public $ears;

    /**
     * @var stdClass
     * @wsparam {
     *     tongue: {
     *         type: PARAM_INT,
     *         description: "Length of tongue"
     *     },
     *     teeth: {
     *         type: wsdocs_teeth[],
     *         description: "Array of teeth"
     *     }
     * };
     */
    public $mouth;
}

class var_nodescription {
    /**
     * @var str
     */
    public $something;
}

class var_strarray {
    /**
     * @var str[]
     */
    public $something;
}

class wstype_textarray {
    /**
     * @wstype PARAM_TEXT[]
     */
    public $something;
}

class wsparam_notype {
    /**
     * @wsparam {
     *     doohicky: {
     *         description: "An amazing thing."
     *     }
     * };
     */
    public $something;
}

/**
 * Tests for webservice definition healper.
 * @author    Guy Thomas <osdev@blackboard.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_feedback_webservice_definition_helper_test extends advanced_testcase {

    public function test_classname() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $toc = new course_toc($course);
        $definitionhelper = new definition_helper_testable($toc);
        $classname = $definitionhelper->classname;
        $this->assertTrue(!empty($classname));
    }

    public function test_usenamespaces() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $toc = new course_toc($course);
        $definitionhelper = new definition_helper_testable($toc);
        $namespaces = $definitionhelper->usenamespaces;
        $this->assertTrue(!empty($namespaces));
    }

    public function test_structure() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $toc = new course_toc($course);
        $definitionhelper = new definition_helper($toc);
        $definition = $definitionhelper->get_definition();
        $this->assertTrue(isset($definition['formatsupportstoc']));
        $this->assertTrue($definition['formatsupportstoc'] instanceof external_value);
    }

    public function test_wsdocs() {
        $definitionhelper = new definition_helper('wsdocs_testing');
        $definition = $definitionhelper->get_definition();

        $expecteds = [
                'head' => [
                        'instanceof' => 'external_value',
                        'type' => PARAM_RAW,
                        'desc' => 'My head',
                        'required' => false,
                        'allownull' => true
                ],
                'shoulders' => [
                        'instanceof' => 'external_value',
                        'type' => PARAM_RAW,
                        'desc' => 'My shoulders',
                        'required' => true,
                        'allownull' => true
                ],
                'knees' => [
                        'instanceof' => 'external_value',
                        'type' => PARAM_ALPHA,
                        'desc' => 'A description of my knees.',
                        'required' => false,
                        'allownull' => false
                ],
                'toes' => [
                        'instanceof' => 'external_value',
                        'type' => PARAM_INT,
                        'desc' => 'Count of my toes.',
                        'required' => false,
                        'allownull' => true
                ],
                'ears' => [
                        'instanceof' => 'external_value',
                        'type' => PARAM_TEXT,
                        'desc' => 'A description of my ears.',
                        'required' => true,
                        'allownull' => true
                ]
        ];

        foreach ($expecteds as $name => $expected) {
            $this->assertTrue(isset($definition[$name]));
            $this->assertTrue($definition[$name] instanceof $expected['instanceof']);
            $this->assertEquals($expected['type'], $definition[$name]->type);
            $this->assertEquals($expected['desc'], $definition[$name]->desc);
            $this->assertEquals($expected['required'], $definition[$name]->required);
            $this->assertEquals($expected['allownull'], $definition[$name]->allownull);
        }

    }

    public function test_convert_ws_param_to_object() {

        $this->resetAfterTest();

        $comment = <<<EOF
     * @wsparam {
     *     "complete": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Number of items completed"
     *     },
     *     "total": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Total items to complete"
     *     }
     * };
EOF;

        $course = $this->getDataGenerator()->create_course();
        $toc = new course_toc($course);
        $definitionhelper = new definition_helper_testable($toc);
        $retval = $definitionhelper->convert_ws_param_to_object($comment);
        $this->assertTrue(is_array($retval));
        $this->assertCount(2, $retval);
        $obj = $retval[0];
        $isarr = $retval[1];
        $this->assertTrue(is_object($obj));
        $this->assertFalse($isarr);
        $this->assertTrue(!empty($obj->complete));
        $this->assertTrue($obj->complete instanceof external_value);
        $this->assertTrue(!empty($obj->complete->type));
        $this->assertTrue(!empty($obj->complete->required));
        $this->assertTrue(!empty($obj->complete->desc));
        $this->assertEquals(PARAM_INT, $obj->complete->type);
        $this->assertEquals(true, $obj->complete->required);
        $this->assertEquals('Number of items completed', $obj->complete->desc);
    }

    public function test_convert_param_array() {

        $this->resetAfterTest();

        $definitionhelper = new definition_helper_testable(new var_strarray());
        $definition = $definitionhelper->get_definition();
        $this->assertNotEmpty($definition['something']);
        $this->assertTrue($definition['something'] instanceof external_multiple_structure);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content->type === PARAM_RAW);
    }

    public function test_convert_wstype_array() {

        $this->resetAfterTest();

        $definitionhelper = new definition_helper_testable(new wstype_textarray());
        $definition = $definitionhelper->get_definition();
        $this->assertNotEmpty($definition['something']);
        $this->assertTrue($definition['something'] instanceof external_multiple_structure);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content->type === PARAM_TEXT);
    }



    public function test_convert_wsparam_array() {

        $this->resetAfterTest();

        $definitionhelper = new definition_helper_testable(new var_strarray());
        $definition = $definitionhelper->get_definition();
        $this->assertNotEmpty($definition['something']);
        $this->assertTrue($definition['something'] instanceof external_multiple_structure);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content instanceof external_value);
        $this->assertTrue($definition['something']->content->type === PARAM_RAW);
    }

    public function test_convert_ws_param_array_to_object() {

        $this->resetAfterTest();

        $comment = <<<EOF
     * @wsparam {
     *     "complete": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Number of items completed"
     *     },
     *     "total": {
     *         "type": PARAM_INT,
     *         "required": true,
     *         "description": "Total items to complete"
     *     }
     * }[];
EOF;

        $course = $this->getDataGenerator()->create_course();
        $toc = new course_toc($course);
        $definitionhelper = new definition_helper_testable($toc);
        $retval = $definitionhelper->convert_ws_param_to_object($comment);
        $this->assertTrue(is_array($retval));
        $this->assertCount(2, $retval);
        $obj = $retval[0];
        $isarr = $retval[1];
        $this->assertTrue(is_object($obj));
        $this->assertTrue($isarr);
        $this->assertTrue(!empty($obj->complete));
        $this->assertTrue($obj->complete instanceof external_value);
        $this->assertTrue(!empty($obj->complete->type));
        $this->assertTrue(!empty($obj->complete->required));
        $this->assertTrue(!empty($obj->complete->desc));
        $this->assertEquals(PARAM_INT, $obj->complete->type);
        $this->assertEquals(true, $obj->complete->required);
        $this->assertEquals('Number of items completed', $obj->complete->desc);
    }

    public function test_convert_ws_param_no_type() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Type not specified');
        new definition_helper_testable(new wsparam_notype());
    }

    public function test_convert_var_no_description() {
        $helper = new definition_helper_testable(new var_nodescription());
        $definition = $helper->get_definition();
        $this->assertArrayHasKey('something', $definition);
        $something = $definition['something'];
        $this->assertTrue($something instanceof external_value);
        $this->assertEmpty($something->desc);
    }

    public function test_cache_definition() {
        $classname = 'wsdocs_testing';
        $helper = new definition_helper_testable($classname);
        $definition = $helper->get_definition();

        // Wipe cache so we can test nothing in cache.
        $cache = cache::make('local_feedback', 'webservicedefinitions');
        $data = $cache->delete($classname);

        // Test empty cache.
        $cached = $helper->get_definition_from_cache($classname);
        $this->assertFalse($cached);

        // Test recover from cache.
        $helper->cache_definition($classname, $definition);
        $cached = $helper->get_definition_from_cache($classname);
        $this->assertNotEmpty($cached);
    }

}
