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
 * Web service auto configuration tool.
 * @author    Guy Thomas <guy.thomas@tituslearning.com>
 * @copyright Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com) / 2019 Titus Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_feedback\service;

defined('MOODLE_INTERNAL') || die();

use local_feedback\password;
use webservice;

require_once(__DIR__ . "/../../../../webservice/lib.php");
require_once(__DIR__ . "/../../../../user/lib.php");

/**
 * Web service auto configuration tool.
 * @author    Guy Thomas <guy.thomas@tituslearning.com>
 * @copyright Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com) / 2019 Titus Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auto_config extends base_service {

    /**
     * @var \stdClass - web user
     */
    public $user;

    /**
     * @var \stdClass - role
     */
    public $role;

    /**
     * @var string - token
     */
    public $token;

    /**
     * Username of web service user account.
     */
    const WEB_USERNAME = 'feedbackapi_webuser';

    /**
     * Name of webservice.
     */
    const WEB_SERVICE_NAME = 'Feedback Generation API Services';

    /**
     * Name of role web service user assigned to.
     */
    const WEB_ROLE = 'feedbackapi_webservice';

    /**
     * This method is protected - you should use the "instance" method.
     * auto_config constructor.
     */
    protected function __construct() {

    }

    /**
     * Get instance of this service.
     * @return auto_config
     */
    public static function instance(): auto_config {
        return static::get_instance();
    }

    /**
     * Main configuration.
     */
    public function configure() {
        $this->create_user();
        $this->create_role();
        $this->configure_web_service();
    }

    /**
     * Create web service user.
     * @throws \moodle_exception.
     */
    private function create_user() {
        global $DB;

        $webuserpwd = strval(new password());

        $user = self::get_web_user();
        if ($user) {
            $user->password = $webuserpwd;
            $user->policyagreed = 1;
            user_update_user($user);
            $this->user = $user;
            return;
        }

        $user = create_user_record(static::WEB_USERNAME, $webuserpwd);
        $user->policyagreed = 1;
        $user->password = $webuserpwd;
        $tmparr = explode(' ', static::WEB_SERVICE_NAME);
        $user->firstname = $tmparr[0];
        $user->lastname = $tmparr[1];
        $user->email = static::WEB_USERNAME.'@test.local'; // Fake email address.
        user_update_user($user);
        $this->user = $user;
    }

    /**
     * Get an array of capabilities required to use the web service.
     * @return array
     * @throws \coding_exception
     */
    private function get_service_caps(string $servicesfilepath): array {
        $functions = [];
        require_once($servicesfilepath);

        if (empty($functions)) {
            throw new \coding_exception('services.php does not seem to contain any functions!');
        }

        // Base web service capabilities.
        $caps = [
                "webservice/rest:use",
                "webservice/restful:use"
        ];

        // Build list of caps based on what lives in the services.php file.
        foreach ($functions as $function) {
            $capabilities = $function['capabilities'] ?? null;
            if (!empty($capabilities)) {
                $capabilities = array_map('trim', explode(',', $capabilities));
                $caps = array_unique(array_merge($caps, $capabilities));
            }
        }

        // Filter the capabilities to only include those that are core OR where the plugin is installed.
        $caps = array_filter($caps, function($cap) {
            if (strpos($cap, 'moodle/') !== 0) {
                // Make sure the plugin exists.
                $tmparr = explode(':', $cap);
                $plugin = str_replace('/', '_', $tmparr[0]);
                if (\core_component::get_component_directory($plugin) === null) {
                    // The plugin specified in the services.php file does not exist, so don't include this capability.
                    return false;
                }
            }
            return true;
        });

        return $caps;
    }

    /**
     * Create web service role.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function create_role() {
        global $DB, $CFG;

        $role = $DB->get_record('role', ['shortname' => static::WEB_ROLE]);
        if ($role) {
            $roleid = $role->id;
            $this->role = $role;
        } else {
            $roleid = create_role(
                    static::WEB_SERVICE_NAME,
                    static::WEB_ROLE, 'Role for '.static::WEB_SERVICE_NAME, 'teacher');
            $this->role = $DB->get_record('role', ['id' => $roleid]);
        }

        $contextid = \context_system::instance()->id;

        $caps = $this->get_service_caps($CFG->dirroot.'/local/feedback/db/services.php');

        foreach ($caps as $cap) {
            assign_capability($cap, CAP_ALLOW, $roleid, $contextid);
        }

        // Add teacher archetype caps to role.
        $caps = get_default_capabilities('teacher');
        foreach ($caps as $cap => $permission) {
            assign_capability($cap, $permission, $roleid, $contextid);
        }

        // Allow role to be allocated at system level.
        set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);

        // Assign user to role.
        role_assign($roleid, $this->user->id, $contextid);
    }

    /**
     * Enable web service.
     */
    private function configure_web_service() {
        global $CFG;

        set_config('enablewebservices', 1);

        // Enable REST protocol.
        $webservice = 'restful'; // We want to enable the restful web service protocol.
        $availablewebservices = \core_component::get_plugin_list('webservice');
        $activewebservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
        foreach ($activewebservices as $key => $active) {
            if (empty($availablewebservices[$active])) {
                unset($activewebservices[$key]);
            }
        }
        if (!in_array($webservice, $activewebservices)) {
            $activewebservices[] = $webservice;
            $activewebservices = array_unique($activewebservices);
        }
        set_config('webserviceprotocols', implode(',', $activewebservices));

        $this->enable_web_service();
        $this->create_ws_token();
    }

    /**
     * Enable feedbackapi web service.
     * @throws \coding_exception
     */
    private function enable_web_service() {
        global $DB;

        $webservicemanager = new webservice();

        $servicedata = (object) [
                'name' => self::WEB_SERVICE_NAME,
                'component' => 'local_feedback',
                'timecreated' => time(),
                'timemodified' => time(),
                'shortname' => 'local_feedback',
                'restrictedusers' => 0,
                'enabled' => 1,
                'downloadfiles' => 1,
                'uploadfiles' => 1
        ];

        $row = $DB->get_record('external_services', ['component' => 'local_feedback']);
        if (!$row) {
            $servicedata->id = $webservicemanager->add_external_service($servicedata);
            $servicedata->timecreated = time();
            $params = array(
                    'objectid' => $servicedata->id
            );
            $event = \core\event\webservice_service_created::create($params);
            $event->trigger();
        } else {
            $servicedata->id = $row->id;
            $webservicemanager->update_external_service($servicedata);
            $params = array(
                    'objectid' => $servicedata->id
            );
            $event = \core\event\webservice_service_updated::create($params);
            $event->trigger();
        }
    }

    /**
     * Create web service token.
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function create_ws_token() {
        global $DB;

        // Create token for Feedback.
        $webservicemanager = new webservice();
        $service = $webservicemanager->get_external_service_by_shortname('local_feedback');
        $context = \context_system::instance();
        $existing = $DB->get_record('external_tokens', [
                        'userid' => $this->user->id,
                        'externalserviceid' => $service->id,
                        'contextid' => $context->id
                ]
        );
        if ($existing) {
            $this->token = $existing->token;
        } else {
            $this->token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id,
                    $this->user->id, $context);
        }
    }

    /**
     * Get feedbackapi web user.
     * @return bool|\stdClass
     * @throws \dml_exception
     */
    public static function get_web_user() {
        global $DB;
        return $DB->get_record('user', ['username' => static::WEB_USERNAME]);
    }

    /**
     * Get a web service token record.
     *
     * @return stdClass
     * @throws \dml_exception
     * @throws \webservice_access_exception
     */
    public static function get_ws_token() {
        $webapiuser = self::get_web_user();
        $username = static::WEB_USERNAME;
        if (!$webapiuser) {
            $msg = "The web user '$username' does not exist. Has auto configure been run?";
            throw new \webservice_access_exception($msg);
        }
        $webservicelib = new webservice();
        $tokens = $webservicelib->get_user_ws_tokens($webapiuser->id);
        if (empty($tokens)) {
            $msg = "There are no web service tokens attributed to '$username'. Has auto configure been run?";
            throw new \webservice_access_exception($msg);
        }
        if (count($tokens) > 1) {
            $msg = "There are multiple web service tokens attributed to '$username'. There should only be one token.";
            throw new \webservice_access_exception($msg);
        }
        $wstoken = reset($tokens);
        return $wstoken;
    }
}