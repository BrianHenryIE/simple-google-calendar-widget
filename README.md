
Updated November 2014 to support Google OAuth 2.0

To install:


To configure:

Log into: https://console.developers.google.com/project/

Click Create Project, call it something nice like MyWordpressCalendar.

Under APIs & auth turn on Calendar API.

Under Credentials, click Create new Client ID and select Service account.

Upload your .p12 file to the plugin directory.





Share hte calendar with the service account email address



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