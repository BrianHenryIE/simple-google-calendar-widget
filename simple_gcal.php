<?php
/*
Plugin Name: Simple Google Calendar Widget
Description: Widget that displays events from a public google calendar
Author: Nico Boehr, Brian Henry
Version: 0.5
License: GPL3
*/

/*
    Simple Google calendar widget for Wordpress
    Copyright (C) 2012 Nico Boehr

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/




class Simple_Gcal_Widget extends WP_Widget
{

    public function __construct()
    {
        // load our textdomain
        load_plugin_textdomain('simple_gcal', false, basename( dirname( __FILE__ ) ) . '/languages' );

        parent::__construct('Simple_Gcal_Widget', 'Simple Google Calendar Widget', array('description' => __('Displays events from a public Google Calendar', 'simple_gcal')));
    }

    private function getTransientId()
    {
        return 'wp_gcal_widget_'.$this->id;
    }

    private function getData($instance)
    {
        $widgetId = $this->id;
        $calId = $instance['calendar_id'];
        $transientId = $this->getTransientId();

        if(false === ($data = get_transient($transientId))) {
            $data = $this->fetch($instance);
            set_transient($transientId, $data, $instance['cache_time']*60);
        }

        return $data;
    }

    private function clearData()
    {
        return delete_transient($this->getTransientId());
    }

    private function fetch($instance){

        require_once realpath(dirname(__FILE__) . '/google-api-php-client/autoload.php');

        if( !session_id() ) {
            session_start();
        }

        $client = new Google_Client();

        $client->setApplicationName($instance['application_name']);

        $service = new Google_Service_Calendar($client);

        if (isset($_SESSION['service_token'])) {
            $client->setAccessToken($_SESSION['service_token']);
        }

        $key = file_get_contents(dirname(__FILE__) . '/' . $instance['key_file_location']);

        $cred = new Google_Auth_AssertionCredentials(
            $instance['service_account_name'],
            array('https://www.googleapis.com/auth/calendar'),
            $key
        );

        $client->setAssertionCredentials($cred);

        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }

        $_SESSION['service_token'] = $client->getAccessToken();

        $optParams = array( 'maxResults'=>$instance['event_count'], 'timeMin'=>date("c"), 'orderBy'=>'startTime', 'singleEvents'=>true);

        $events = $service->events->listEvents($instance['calendar_id'], $optParams);

        $out = array();
        $i = 0;

        while(true) {
            foreach ($events->getItems() as $event) {

                $out[$i] = new StdClass;

                $out[$i]->title = $event->getSummary();
                if($out[$i]->from = $event->getStart()->dateTime!=null){
                    $out[$i]->from = $event->getStart()->dateTime;
                    $out[$i]->allday = false;
                }else{
                    $out[$i]->from = $event->getStart()->date;
                    $out[$i]->allday = true;
                }
                $out[$i]->to = $event->getEnd()->dateTime;
                $out[$i]->where = $event->getLocation();
                $out[$i]->description = $event->getDescription();
                $out[$i]->htmlLink = $event->getHtmlLink();

                $i++;
            }
            $pageToken = $events->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $events = $service->events->listEvents('primary', $optParams);
            } else {
                break;
            }
        }

        return $out;
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if(isset($instance['title'])) {
            echo $args['before_title'], $instance['title'], $args['after_title'];
        }

        $data = $this->getData($instance);
        date_default_timezone_set(get_option('timezone_string'));
        echo '<ol class="eventlist">';
        foreach($data as $e) {
// echo '<li><span class="date">', strftime(__('<span class="day">%d</span>%b', 'simple_gcal'), $e->from), '</span>';
            echo '<li><span class="date">'.date('l, jS F',  strtotime($e->from)).'</span>';

            echo '<a href="', htmlspecialchars($e->htmlLink),'" class="eventlink" ';
            if($instance['targetblank']) {
                echo 'target="_blank" ';
            }
            if(!empty($e->where)) {
                echo 'title="', sprintf(__('Location: %s', 'simple_gcal'), htmlspecialchars($e->where)), '" ';
            }
            echo '>', htmlspecialchars($e->title), '</a>';
            echo '</li>';
        }
        echo '</ol>';
        date_default_timezone_set('UTC');
        echo '<br class="clear" />';
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        $instance['calendar_id'] = htmlspecialchars($new_instance['calendar_id']);

        $instance['application_name'] =  htmlspecialchars($new_instance['application_name']);

        $instance['client_id'] =  htmlspecialchars($new_instance['client_id']);

        $instance['service_account_name'] =  htmlspecialchars($new_instance['service_account_name']);

        $instance['key_file_location'] =  htmlspecialchars($new_instance['key_file_location']);

        $instance['targetblank'] = $new_instance['targetblank']==1?1:0;

        $instance['cache_time'] = $new_instance['cache_time'];
        if(is_numeric($new_instance['cache_time']) && $new_instance['cache_time'] > 1) {
            $instance['cache_time'] = $new_instance['cache_time'];
        } else {
            $instance['cache_time'] = 60;
        }

        $instance['event_count'] = $new_instance['event_count'];
        if(is_numeric($new_instance['event_count']) && $new_instance['event_count'] > 1) {
            $instance['event_count'] = $new_instance['event_count'];
        } else {
            $instance['event_count'] = 5;
        }

        // delete our transient cache
        $this->clearData();

        return $instance;
    }

    public function form($instance)
    {
        $default = array(
            'title' => __('Events', 'simple_gcal'),
            'cache_time' => 60
        );
        $instance = wp_parse_args((array) $instance, $default);

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($instance['title']); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('calendar_id'); ?>"><?php _e('Calendar ID:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('calendar_id'); ?>" name="<?php echo $this->get_field_name('calendar_id'); ?>" type="text" value="<?php echo attribute_escape($instance['calendar_id']); ?>" />
        </p>

        <p><a taget="_parent" href="https://console.developers.google.com/project/">Google API Settings</a></p>
        <p>
            <label for="<?php echo $this->get_field_id('application_name'); ?>"><?php _e('Application Name:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('application_name'); ?>" name="<?php echo $this->get_field_name('application_name'); ?>" type="text" value="<?php echo attribute_escape($instance['application_name']); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('client_id'); ?>"><?php _e('Client ID:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('client_id'); ?>" name="<?php echo $this->get_field_name('client_id'); ?>" type="text" value="<?php echo attribute_escape($instance['client_id']); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('service_account_name'); ?>"><?php _e('Service Account Name/Email Address:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('service_account_name'); ?>" name="<?php echo $this->get_field_name('service_account_name'); ?>" type="text" value="<?php echo attribute_escape($instance['service_account_name']); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('key_file_location'); ?>"><?php _e('.p12 Key File Location:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('key_file_location'); ?>" name="<?php echo $this->get_field_name('key_file_location'); ?>" type="text" value="<?php echo attribute_escape($instance['key_file_location']); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('targetblank'); ?>"><?php _e('Open event details in new window:', 'simple_gcal'); ?></label>
            <input name="<?php echo $this->get_field_name('targetblank'); ?>" type="hidden" value="0" />
            <input id="<?php echo $this->get_field_id('targetblank'); ?>" name="<?php echo $this->get_field_name('targetblank'); ?>" type="checkbox" value="1" <?php if($instance['targetblank'] == 1) { echo 'checked="checked" '; } ?>/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('event_count'); ?>"><?php _e('Number of events displayed:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('event_count'); ?>" name="<?php echo $this->get_field_name('event_count'); ?>" type="text" value="<?php echo attribute_escape($instance['event_count']); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Cache expiration time in minutes:', 'simple_gcal'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo attribute_escape($instance['cache_time']); ?>" />
        </p>
        <p>
            <?php _e('Need <a href="http://wordpress.org/extend/plugins/simple-google-calendar-widget/" target="_blank">help</a>?', 'simple_gcal'); ?>
        </p>
    <?php
    }

}

add_action('widgets_init', create_function('', 'return register_widget("Simple_Gcal_Widget");'));
