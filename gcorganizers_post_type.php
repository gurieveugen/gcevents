<?php

class GCOrganizersPost extends GCBase{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		parent::__construct('gcevents_ru.php');
		add_action('init', array($this, 'createPostTypeOrganizers'));
		add_action('do_meta_boxes', array($this, 'metaBoxGCOrganizers'));
		add_action('save_post', array($this, 'updateGCOrganizers'), 0);
	}

	/**
	 * Create GC Organizers post type
	 */
	public function createPostTypeOrganizers()
	{

		$post_labels = array(
			'name'               => $this->l('gc_organizers'),
			'singular_name'      => $this->l('gc_organizer'),
			'add_new'            => $this->l('add_new'),
			'add_new_item'       => $this->l('add_new_gc_organizer'),
			'edit_item'          => $this->l('edit_gc_organizer'),
			'new_item'           => $this->l('new_gc_organizer'),
			'all_items'          => $this->l('organizers'),
			'view_item'          => $this->l('view_gc_organizer'),
			'search_items'       => $this->l('search_gc_organizers'),
			'not_found'          => $this->l('no_gc_organizers_found'),
			'not_found_in_trash' => $this->l('no_gc_organizers_found_in_trash'),
			'parent_item_colon'  => '',
			'menu_name'          => $this->l('gc_organizers'));

		$post_args = array(
			'labels'             => $post_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=gcevent',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gcorganizer' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', ));

		register_post_type('gcorganizer', $post_args);
	}

	/**
	 * Add GCOrganizers meata box
	 */
	public function metaBoxGCOrganizers()
	{
		add_meta_box('metaBoxGCOrganizers', $this->l('organizer_information'), array($this, 'metaBoxGCOrganizers_func'), 'gcorganizer', 'normal', 'high');
	}

	/**
	 * Show GCOrganizers meata box
	 * @param  object $post
	 */
	public function metaBoxGCOrganizers_func($post)
	{
		$gcorganizers = get_post_meta($post->ID, 'gcorganizers', false);
		$gcorganizers = $gcorganizers[0];
		if($gcorganizers) extract($gcorganizers);
		?>
		<table class="gctable">
			<tbody>
				<tr>
					<td><label for="gcorganizers_phone"><?php $this->l('phone', true); ?> : </label></td>
					<td><input type="text" name="gcorganizers[phone]" id="gcorganizers_phone" value="<?php echo $phone; ?>"></td>
				</tr>
				<tr>
					<td><label for="gcorganizers_website"><?php $this->l('website', true); ?> : </label></td>
					<td><input type="text" name="gcorganizers[website]" id="gcorganizers_website" value="<?php echo $website; ?>"></td>
				</tr>
				<tr>
					<td><label for="gcorganizers_email"><?php $this->l('email', true); ?> : </label></td>
					<td><input type="text" name="gcorganizers[email]" id="gcorganizers_email" value="<?php echo $email; ?>"></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="gcorganizers_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>">
		<?php
	}

	/**
	 * Update GC Organizers data
	 * @param  integer $post_id
	 * @return integer
	 */
	public function updateGCOrganizers($post_id)
	{
		if(!wp_verify_nonce($_POST['gcorganizers_nonce'], __FILE__)) return false; 
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false; 
		if(!current_user_can('edit_post', $post_id)) return false; 

		if(isset($_POST['gcorganizers']))
		{
			update_post_meta($post_id, 'gcorganizers', $_POST['gcorganizers']);
		}

		return $post_id;
	}
}