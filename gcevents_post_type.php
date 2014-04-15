<?php

class GCEventsPost extends GCBase{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const NONE         = 666;
	const EVERY_DAY    = 1;
	const EVERY_WEEK   = 2;
	const EVERY_MONTH  = 3;
	const EVERY_YEAR   = 4;
	const CUSTOM       = 5;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		parent::__construct('gcevents_ru.php');
		// =========================================================
		// HOOKS
		// =========================================================
		add_action('init', array($this, 'createPostTypeEvents'));
		add_action('add_meta_boxes', array($this, 'metaBoxGCEvents'));
		add_action('save_post', array($this, 'saveGCEvents'), 0);	
		add_shortcode('tickets', array($this, 'displayUpcomingTickets'));
		add_image_size('ticket-image', 280, 400, true);
	}

	/**
	 * Display upcoming tickets
	 * @param  array $args
	 * @return array      
	 */
	public function displayUpcomingTickets($args)
	{
		$count  = (isset($args['count'])) ? intval($args['count']) : -1;
		$offset = (isset($args['offset'])) ? intval($args['offset']) : 0;

		$upcoming = $this->getUpcomingEvents($count, $offset);
		foreach ($upcoming as $key => $value) 
		{
			$img_src   = 'http://placehold.it/600x300/0092c3/fff';
 			if(has_post_thumbnail($value->ID))
 			{
 				$img_src = wp_get_attachment_image_src(get_post_thumbnail_id($value->ID), 'ticket-image');
 				$img_src = $img_src[0];
 			}
 			// =========================================================
 			// Initialize cost
 			// =========================================================
 			if($value->meta['cost'] == "") $cost = "";
 			else if(intval($value->meta['cost']) == 0) $cost = "Свободный вход!";
 			else $cost = $value->meta['cost'].' '.$value->meta['currency_symbol'].'  <a class="btn-ticket" href="'.$value->meta['website'].'"><i class="fa-rub fa-2x"></i><span>Купить билеты</span></a>';

 			// =========================================================
 			// Initialize output
 			// =========================================================
 			$str.= '<div class="row ticket">';
 			$str.= '<div class="col-md-3 col-lg-3">';
 			$str.= '<img src="'.$img_src.'" alt="'.$value->post_title.'" >';
 			$str.= '<span class="cost">'.$cost.'</span>';
 			$str.= '</div>';
 			$str.= '<div class="col-md-9 col-lg-9">';
 			$str.= '<h3>'.$value->post_title.'</h3>';
 			$str.= $value->post_content;
 			$str.= '</div>';
 			$str.= '</div>';
		}

		return $str;
	}

	/**
	 * Create GCEvents post type and his taxonomies
	 */
	public function createPostTypeEvents()
	{

		$post_labels = array(
			'name'               => $this->l('gc_events'),
			'singular_name'      => $this->l('gc_event'),
			'add_new'            => $this->l('add_new'),
			'add_new_item'       => $this->l('add_new_gc_event'),
			'edit_item'          => $this->l('edit_gc_event'),
			'new_item'           => $this->l('new_gc_event'),
			'all_items'          => $this->l('events'),
			'view_item'          => $this->l('view_gc_event'),
			'search_items'       => $this->l('search_gc_events'),
			'not_found'          => $this->l('no_gc_events_found'),
			'not_found_in_trash' => $this->l('no_gc_events_found_in_trash'),
			'parent_item_colon'  => '',
			'menu_name'          => $this->l('gc_events'));

		$post_args = array(
			'labels'             => $post_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gcevent' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies'         => array('event_cat'),
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ));

		$tax_labels = array(
			'name'              => $this->l('event_categories'),
			'singular_name'     => $this->l('event_category'),
			'search_items'      => $this->l('search_event_categories'),
			'all_items'         => $this->l('all_event_categories'),
			'parent_item'       => $this->l('parent_event_category'),
			'parent_item_colon' => $this->l('parent_event_category'),
			'edit_item'         => $this->l('edit_event_category'),
			'update_item'       => $this->l('update_event_category'),
			'add_new_item'      => $this->l('add_new_event_category'),
			'new_item_name'     => $this->l('new_event_category_name'),
			'menu_name'         => $this->l('event_category'));

		$tax_args = array(
			'hierarchical'      => true,
			'labels'            => $tax_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'event_cat' ));

		register_post_type('gcevent', $post_args);
		register_taxonomy('event_cat', array('gcevent'), $tax_args);
	}

	/**
	 * Add GCEvents meata box
	 */
	public function metaBoxGCEvents($post_type)
	{
		$post_types = array('gcevent');
		if(in_array($post_type, $post_types))
		{
			add_meta_box('metaBoxGCEvents', $this->l('the_gc_event_settings'), array($this, 'metaBoxGCEventsRender'), $post_type, 'normal', 'high');	
		}
		
	}

	/**
	 * render GCEvents Meta box
	 */
	public function metaBoxGCEventsRender($post)
	{
		$gcevents    = get_post_meta($post->ID, 'gcevents', true);		
		$gcevents    = $this->setDefaultMeta($gcevents);
		$gcvenue     = $this->getAllPosts('gcvenue');
		$gcorganizer = $this->getAllPosts('gcorganizer');
		$recurrence  = $this->getAllRecurrence();

		
		wp_nonce_field( 'gcevent_options_box', 'gcevent_options_box_nonce' );

		?>
		<fieldset class="gccontrol_group">
			<legend><?php $this->l('event_time_and_date', true); ?></legend>
			<hr>
			<table class="gctable">
				<tbody>
					<tr>
						<td><label for="gcevents_all_day"><?php $this->l('all_day_event', true); ?>:</label></td>
						<td><input type="hidden" name="gcevents[all_day]" value="off"><input type="checkbox" onchange="allDayChange(this)" name="gcevents[all_day]" id="gcevents_all_day"<?php echo $this->checked(($gcevents['all_day'] == 'on')); ?>></td>
					</tr>
					<tr>
						<td><label for="gcevents_start_date"><?php $this->l('start_date_and_time', true); ?>:</label></td>
						<td>
							<input type="text" name="gcevents[start_date]" class="date-input datepicker" id="gcevents_start_date" value="<?php echo $gcevents['start_date']; ?>">
							<div class="time">
								<?php 
								$this->l('time', true);
								echo ' '.$this->getTimeSelect('gcevents[start_hour]', 24, intval($gcevents['start_hour'])).':';
								echo $this->getTimeSelect('gcevents[start_minute]', 59, intval($gcevents['start_minute']));        
								?>	
							</div>
						</td>
					</tr>
					<tr>
						<td><label for="gcevents_end_date"><?php $this->l('end_date_and_time', true); ?>:</label></td>
						<td>
							<input type="text" name="gcevents[end_date]"  class="date-input datepicker" id="gcevents_end_date" value="<?php echo $gcevents['end_date']; ?>">
							<div class="time">
								<?php
								$this->l('time', true);
								echo ' '.$this->getTimeSelect('gcevents[end_hour]', 24, intval($gcevents['end_hour'])).':';
								echo $this->getTimeSelect('gcevents[end_minute]', 59, intval($gcevents['end_minute']));        
								?>	
							</div>
						</td>
					</tr>
					<tr>
						<td><label for="gcevents_recurrence"><?php $this->l('recurrence', true); ?>:</label></td>
						<td>
							<?php echo $this->constructSelectControl($recurrence, 'gcevents[recurrence]', $gcevents['recurrence'], false); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="gccontrol_group">
			<legend><?php $this->l('event_location_details', true); ?></legend>
			<hr>
			<table class="gctable">
				<tbody>
					<tr>
						<td><label for="gcevents_venue"><?php $this->l('gc_venue', true); ?>:</label></td>
						<td>
							<?php echo $this->constructSelectControl($gcvenue, 'gcevents[venue]', $gcevents['venue']); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="gccontrol_group">
			<legend><?php $this->l('event_organizer_details', true); ?></legend>
			<hr>
			<table class="gctable">
				<tbody>
					<tr>
						<td><label for="gcevents_organizer"><?php $this->l('gc_organizer', true); ?>:</label></td>
						<td>
							<?php echo $this->constructSelectControl($gcorganizer, 'gcevents[organizer]', $gcevents['organizer']); ?>							
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="gccontrol_group">
			<legend><?php $this->l('event_website', true); ?></legend>
			<hr>
			<table class="gctable">
				<tbody>
					<tr>
						<td><label for="gcevents_website"><?php $this->l('website', true); ?>:</label></td>
						<td><input type="text" name="gcevents[website]" id="gcevents_website" value="<?php echo $gcevents['website']; ?>"></td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="gccontrol_group">
			<legend><?php $this->l('event_cost', true); ?></legend>
			<hr>
			<table class="gctable">
				<tbody>
					<tr>
						<td><label for="gcevents_currency_symbol"><?php $this->l('currency_symbol', true); ?>:</label></td>
						<td><input type="text" name="gcevents[currency_symbol]" id="gcevents_currency_symbol" value="<?php echo $gcevents['currency_symbol']; ?>"></td>
					</tr>
					<tr>
						<td><label for="gcevents_cost"><?php $this->l('event_cost', true); ?>:</label></td>
						<td><input type="text" name="gcevents[cost]" id="gcevents_cost" value="<?php echo $gcevents['cost']; ?>"></td>
					</tr>
					<tr>
						<td></td>
						<td><small><?php $this->l('event_cost_msg', true); ?></small></td>
					</tr>
				</tbody>
			</table>
		</fieldset>		
		<?php
	}

	/**
	 * Save post
	 * @param  integer $post_id [description]
	 * @return [type]          [description]
	 */
	public function saveGCEvents($post_id)
	{
		// =========================================================
		// Check nonce
		// =========================================================
		if(!isset( $_POST['gcevent_options_box_nonce'])) return $post_id;
		if(!wp_verify_nonce($_POST['gcevent_options_box_nonce'], 'gcevent_options_box')) return $post_id;
		if(defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

		// =========================================================
		// Check the user's permissions.
		// =========================================================
		if ( 'page' == $_POST['post_type'] ) 
		{			
			if (!current_user_can( 'edit_page', $post_id)) return $post_id;
		} 
		else 
		{
			if(!current_user_can( 'edit_post', $post_id)) return $post_id;
		}

		// =========================================================
		// Save
		// =========================================================		
		if(isset($_POST['gcevents']))
		{
			update_post_meta($post_id, 'gcevents', $_POST['gcevents']);
		}

		return $post_id;
	}

	/**
	 * Get all posts
	 * @param  mixed $post_type 
	 * @return array           
	 */
	public function getAllPosts($post_type)
	{
		$all = array(
			'posts_per_page'   => -1,
			'offset'           => 0,			
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => $post_type,
			'post_status'      => 'publish');

		$arr = get_posts($all);
		foreach ($arr as $key => $value) 
		{
			$new_arr[$value->ID] = $value->post_title;
		}
		return $new_arr;
	}

	/**
	 * Get upcoming events
	 * @param  integer $count
	 * @return array
	 */
	public function getUpcomingEvents($count = -1, $offset = 0)
	{
		$all     = array(
			'posts_per_page'   => -1,
			'offset'           => 0,			
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'gcevent',
			'post_status'      => 'publish');

		$arr     = get_posts($all);
		$current = 1;

		// =========================================================
		// How much items were returned
		// =========================================================
		if($count < 0)
		{
			$count = count($arr);
		}
		if($count < $offset)
		{
			$offset = 0;
		}

		foreach ($arr as $key => $value) 
		{
			$meta = get_post_meta($value->ID, 'gcevents', true);	
			if(isset($meta['start_date']))
			{
				$sort_arr[$value->ID]  = strtotime($meta['start_date']);	
				$value->meta           = $meta;
				$items_arr[$value->ID] = $value;
			}	
		}
		// =========================================================
		// Sort by Start Date
		// =========================================================
		arsort($sort_arr);		
		// =========================================================
		// Initialize sorted items
		// =========================================================		
		foreach ($sort_arr as $key => $value) 
		{
			if($current <= $count AND $current > $offset)
			{
				$res_arr[$key] = $items_arr[$key];
			}
			$current++;
		}
		
		
		return $res_arr;
	}

	/**
	 * Get all recurrence 
	 * @return array
	 */
	public function getAllRecurrence()
	{
		$recurrence 	= array_flip(array(self::NONE, self::EVERY_DAY, self::EVERY_WEEK, self::EVERY_MONTH, self::CUSTOM));

		foreach ($recurrence as $key => $value) 
		{
			$recurrence[$key] = $this->l($this->getConstName(__CLASS__, $key));
		}

		return $recurrence;
	}
	
	/**
	 * Set default values for Meta Data
	 * @param array $arr
	 */
	public function setDefaultMeta($arr)
	{
		$default = $GLOBALS['gcevents']->gcoptions_page->getAllOptions();
		foreach ($default as $key => $value) 
		{
			if(!isset($arr[$key]))
			{
				$arr[$key] = $value;
			}
		}
		return $arr;
	}
}