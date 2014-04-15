<?php
/**
 * Register new widget
 */
add_action('widgets_init', create_function('', 'register_widget( "GCCalendar" );'));

class GCCalendar extends WP_Widget {
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct() 
	{
		$widget_ops     = array('classname' => 'block-calendar', 'description' => 'Виджет отображает календарь событий' );
		parent::__construct('calendar', 'Календарь', $widget_ops);
	}

	function widget( $args, $instance ) 
	{
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );		

		echo $before_widget;
		if ($title) echo $before_title.$title.$after_title;	
		// =========================================================
		// Print media block
		// =========================================================
		$events = $this->getUpcomingEvents();
		$first  = true;
		echo '<script>var codropsEvents = {';
		foreach ($events as $key => $value) 
		{
			if(!$first) echo ','."\n";
			else $first = false;

			echo '\''.date('m-d-Y', $key).'\' : \''.$value.'\'';
		}
		echo '};</script>';
		?>

		<div class="custom-calendar-wrap">
			<div id="custom-inner" class="custom-inner">
				<div class="custom-header clearfix">
					<nav>
						<span id="custom-prev" class="custom-prev"></span>
						<span id="custom-next" class="custom-next"></span>
					</nav>
					<h2 id="custom-month" class="custom-month"></h2>
					<h3 id="custom-year" class="custom-year"></h3>
				</div>
				<div id="gccalendar" class="fc-calendar-container"></div>
			</div>
		</div>
		<?php
		echo $after_widget;
	}

	function form($instance) 
	{		
		$title = $instance['title'];		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>		
		<?php
	}

	function update( $new_instance, $old_instance ) 
	{
		$instance          = $old_instance;		
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;
	}

	/**
	 * Get all upcoming events to java module
	 * @return array
	 */
	private function getUpcomingEvents()
	{
		$out    = array();
		$events = $GLOBALS['gcevents']->gcevent_post->getUpcomingEvents();

		foreach ($events as $key => $value) 
		{
			$start_date = strtotime($value->meta['start_date']);

			if(isset($out[$start_date]))
			{
				$out[$start_date].= '<a href="'.get_permalink($value->ID).'">'.$value->post_title.'</a>';
			}
			else
			{
				$out[$start_date] = '<a href="'.get_permalink($value->ID).'">'.$value->post_title.'</a>';	
			}
		}

		return $out;
	}

}