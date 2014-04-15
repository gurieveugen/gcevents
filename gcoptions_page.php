<?php
class GCOptionsPage extends GCBase
{
    //                          __              __      
    //   _________  ____  _____/ /_____ _____  / /______
    //  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
    // / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
    // \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
    const PARENT_PAGE = 'edit.php?post_type=gcevent';
    const LABEL_KEY   = 'options';
    const NONE        = 666;
    const EVERY_DAY   = 1;
    const EVERY_WEEK  = 2;
    const EVERY_MONTH = 3;
    const EVERY_YEAR  = 4;
    const CUSTOM      = 5;
    //                __  _                 
    //   ____  ____  / /_(_)___  ____  _____
    //  / __ \/ __ \/ __/ / __ \/ __ \/ ___/
    // / /_/ / /_/ / /_/ / /_/ / / / (__  ) 
    // \____/ .___/\__/_/\____/_/ /_/____/  
    //     /_/                              
    private $options;

    //                    __  __              __    
    //    ____ ___  ___  / /_/ /_  ____  ____/ /____
    //   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
    //  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
    // /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_submenu_page(self::PARENT_PAGE, $this->l(self::LABEL_KEY), $this->l(self::LABEL_KEY), 'administrator', __FILE__, array($this, 'create_admin_page'), 'favicon.ico'); 
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        $this->options = $this->getAllOptions();       

        ?>
        <div class="wrap">
            <?php screen_icon(); ?>                 
            <form method="post" action="options.php">
            <?php                
                settings_fields('gc_options_page');   
                do_settings_sections(__FILE__);
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Get all options
     */
    public function getAllOptions()
    {
        return get_option('gcoptions');
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting('gc_options_page', 'gcoptions', array($this, 'sanitize'));
        add_settings_section('default_settings', $this->l('default_options'), null, __FILE__); 

        add_settings_field('currency_symbol', $this->l('currency_symbol'), array($this, 'currency_symbol_callback'), __FILE__, 'default_settings');
        add_settings_field('website', $this->l('website'), array($this, 'website_callback'), __FILE__, 'default_settings');
        add_settings_field('all_day', $this->l('all_day_event'), array($this, 'all_day_callback'), __FILE__, 'default_settings');
        add_settings_field('start_date', $this->l('start_date_and_time'), array($this, 'start_date_callback'), __FILE__, 'default_settings');
        add_settings_field('end_date', $this->l('end_date_and_time'), array($this, 'end_date_callback'), __FILE__, 'default_settings');
        add_settings_field('recurrence', $this->l('recurrence'), array($this, 'recurrence_callback'), __FILE__, 'default_settings');
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();     

        if(isset($input['recurrence'])) $new_input['recurrence'] = intval($input['recurrence']);
        if(isset($input['website'])) $new_input['website'] = sanitize_text_field($input['website']);
        if(isset($input['currency_symbol'])) $new_input['currency_symbol'] = sanitize_text_field($input['currency_symbol']);
        if(isset($input['all_day'])) $new_input['all_day'] = sanitize_text_field($input['all_day']);
        
        // =========================================================
        // Start Date and Time
        // =========================================================
        if(isset($input['start_date'])) $new_input['start_date'] = date('Y-m-d', strtotime($input['start_date']));         
        if(isset($input['start_hour'])) $new_input['start_hour'] = $this->limit(intval($input['start_hour']), 1, 24);
        if(isset($input['start_minute'])) $new_input['start_minute'] = $this->limit(intval($input['start_minute']), 0, 59);

        // =========================================================
        // End Date and Time
        // =========================================================
        if(isset($input['end_date'])) $new_input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
        if(isset($input['end_hour'])) $new_input['end_hour'] = $this->limit(intval($input['end_hour']), 1, 24);
        if(isset($input['end_minute'])) $new_input['end_minute'] = $this->limit(intval($input['end_minute']), 0, 59);

        return $new_input;
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function currency_symbol_callback()
    {
        printf('<input type="text" id="currency_symbol" name="gcoptions[currency_symbol]" value="%s" />', isset($this->options['currency_symbol']) ? esc_attr($this->options['currency_symbol']) : '');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function website_callback()
    {
        printf('<input type="text" id="website" name="gcoptions[website]" value="%s" />', isset($this->options['website']) ? esc_attr($this->options['website']) : '');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function all_day_callback()
    {
        echo '<input type="hidden" name="gcoptions[all_day]" value="off" />';
        echo '<input type="checkbox" id="all_day" name="gcoptions[all_day]" value="on" '.$this->checked(($this->options['all_day'] == 'on')).'/>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function start_date_callback()
    {        
        echo '<input type="text" id="start_date" name="gcoptions[start_date]" value="'.$this->options['start_date'].'" class="date-input datepicker" /> ';
        $this->l('time', true);
        echo ' '.$this->getTimeSelect('gcoptions[start_hour]', 24, intval($this->options['start_hour'])).':';
        echo $this->getTimeSelect('gcoptions[start_minute]', 59, intval($this->options['start_minute']));        
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function end_date_callback()
    {
        printf('<input type="text" id="end_date" name="gcoptions[end_date]" value="%s" class="datepicker date-input" /> ', isset($this->options['end_date']) ? esc_attr($this->options['end_date']) : '');
        $this->l('time', true);
        echo ' '.$this->getTimeSelect('gcoptions[end_hour]', 24, intval($this->options['end_hour'])).':';
        echo $this->getTimeSelect('gcoptions[end_minute]', 59, intval($this->options['end_minute']));        
    } 

    /** 
     * Get the settings option array and print one of its values
     */
    public function recurrence_callback()
    {
        $recurrence = array_flip(array(self::NONE, self::EVERY_DAY, self::EVERY_WEEK, self::EVERY_MONTH, self::CUSTOM));

        foreach ($recurrence as $key => $value) 
        {
            $recurrence[$key] = $this->getConstName(__CLASS__, $key);
        }       
        ?>
        <select name="gcoptions[recurrence]" id="recurrence">
            <?php

            foreach ($recurrence as $key => $value) 
            {
                ?>
                <option value="<?php echo $key; ?>"<?php echo $this->selected(($key == $this->options['recurrence']));?>><?php $this->l($value, true); ?></option>
                <?php
            }
            ?>                              
        </select>
        <?php        
    }
}