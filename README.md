#Feedback web services

##Installation
Put this plugin in your local directory - e.g:
moodle/local/feedback

NOTE: This plugin depends on the webservice plugin - webservice_restful. You must have this third party plugin installed
in order for the Feedback web services plugin to work.

Visit moodle/admin.php to install the plugins

##Configuration
Navigate to [yourmoodleurl]/local/feedback/wstoken.php
This will generate a token for you, which you can then use to make API calls.

##API calls
The end point for API calls will be
[yourmoodleurl]/webservice/restful/server.php/[resource]

Here are the headers you must send per request:

Content-Type: application/json
Authorization: [yourwstoken]
HTTP_ACCEPT: application/json
HTTP_CONTENT_TYPE: application/json

#Third party code
This plugin uses some code from the GPL3 Moodle plugin tool_ally by Blackboard Ltd.