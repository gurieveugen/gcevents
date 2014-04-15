<?php

class GCVenuesPost extends GCBase{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		parent::__construct();
		add_action('init', array($this, 'createPostTypeVenues'));
		add_action('do_meta_boxes', array($this, 'metaBoxGCVenues'));
		add_action('save_post', array($this, 'updateGCVenues'), 0);
	}

	/**
	 * Create GC venues post type
	 */
	public function createPostTypeVenues()
	{

		$post_labels = array(
			'name'               => $this->l('gc_venues'),
			'singular_name'      => $this->l('gc_venue'),
			'add_new'            => $this->l('add_new'),
			'add_new_item'       => $this->l('add_new_gc_venue'),
			'edit_item'          => $this->l('edit_gc_venue'),
			'new_item'           => $this->l('new_gc_venue'),
			'all_items'          => $this->l('venues'),
			'view_item'          => $this->l('view_gc_venue'),
			'search_items'       => $this->l('search_gc_venues'),
			'not_found'          => $this->l('no_gc_venues_found'),
			'not_found_in_trash' => $this->l('no_gc_venues_found_in_trash'),
			'parent_item_colon'  => '',
			'menu_name'          => $this->l('gc_venues'));

		$post_args = array(
			'labels'             => $post_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=gcevent',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gcvenue' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', ));

		register_post_type('gcvenue', $post_args);
	}

	/**
	 * Add GCvenues meata box
	 */
	public function metaBoxGCVenues()
	{
		add_meta_box('metaBoxGCVenues', $this->l('venue_information'), array($this, 'metaBoxGCvenues_func'), 'gcvenue', 'normal', 'high');
	}

	/**
	 * Show GCvenues meata box
	 * @param  object $post
	 */
	public function metaBoxGCVenues_func($post)
	{
		$gcvenues = get_post_meta($post->ID, 'gcvenues', false);
		$gcvenues = $gcvenues[0];
		if($gcvenues) extract($gcvenues);
		?>
		<table class="gctable">
			<tbody>
				<tr>
					<td><label for="gcvenues_phone"><?php $this->l('phone', true); ?> : </label></td>
					<td><input type="text" name="gcvenues[phone]" id="gcvenues_phone" value="<?php echo $phone; ?>"></td>
				</tr>
				<tr>
					<td><label for="gcvenues_website"><?php $this->l('website', true); ?> : </label></td>
					<td><input type="text" name="gcvenues[website]" id="gcvenues_website" value="<?php echo $website; ?>"></td>
				</tr>				
				<tr>
					<td><label for="gcvenues_postal_code"><?php $this->l('postal_code', true); ?> : </label></td>
					<td><input type="text" name="gcvenues[postal_code]" id="gcvenues_postal_code" value="<?php echo $postal_code; ?>"></td>
				</tr>
				<tr>
					<td><label for="gcvenues_country"><?php $this->l('country', true); ?> : </label></td>
					<td><?php echo $this->constructSelectControl($this->constructCountries(), 'gcvenues[country]', $country, true); ?></td>
				</tr>
				<tr>
					<td><label for="gcvenues_city"><?php $this->l('city', true); ?> : </label></td>
					<td><input type="text" name="gcvenues[city]" id="gcvenues_city" value="<?php echo $city; ?>"></td>
				</tr>
				<tr>
					<td><label for="gcvenues_address"><?php $this->l('address', true); ?> : </label></td>
					<td><input type="text" name="gcvenues[address]" id="gcvenues_address" value="<?php echo $address; ?>"></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="gcvenues_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>">
		<?php
		
	}

	/**
	 * Update GC venues data
	 * @param  integer $post_id
	 * @return integer
	 */
	public function updateGCVenues($post_id)
	{
		if(!wp_verify_nonce($_POST['gcvenues_nonce'], __FILE__)) return false; 
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false; 
		if(!current_user_can('edit_post', $post_id)) return false; 

		if(isset($_POST['gcvenues']))
		{
			update_post_meta($post_id, 'gcvenues', $_POST['gcvenues']);
		}

		return $post_id;
	}

	/**
	 * Get HTML select control
	 * @param  array  $options 
	 * @param  string $current 
	 * @param  string $name    
	 * @return string
	 */
	public function getSelectControl($options, $current, $name)
	{
		$output = '<select name="'.$name.'">';
		foreach ($options as $key => $value) 
		{
			$output.= '<option value="'.$key.'"'.$this->selected(($key == $current)).'>'.$value.'</option>';
		}
		$output.= '</select>';

		return $output;
	}

	

	/**
	 * Construct countries array
	 * @return array
	 */
	public function constructCountries()
	{
		$arr = array('AD' => $this->l('AD'), 'AE' => $this->l('AE'), 'AF' => $this->l('AF'), 'AG' => $this->l('AG'), 
					'AI' => $this->l('AI'), 'AL' => $this->l('AL'), 'AM' => $this->l('AM'), 'AN' => $this->l('AN'), 
					'AO' => $this->l('AO'), 'AQ' => $this->l('AQ'), 'AR' => $this->l('AR'), 'AS' => $this->l('AS'), 
					'AT' => $this->l('AT'), 'AU' => $this->l('AU'), 'AW' => $this->l('AW'), 'AZ' => $this->l('AZ'), 
					'BA' => $this->l('BA'), 'BB' => $this->l('BB'), 'BD' => $this->l('BD'), 'BE' => $this->l('BE'), 
					'BF' => $this->l('BF'), 'BG' => $this->l('BG'), 'BH' => $this->l('BH'), 'BI' => $this->l('BI'), 
					'BJ' => $this->l('BJ'), 'BM' => $this->l('BM'), 'BN' => $this->l('BN'), 'BO' => $this->l('BO'), 
					'BR' => $this->l('BR'), 'BS' => $this->l('BS'), 'BT' => $this->l('BT'), 'BV' => $this->l('BV'), 
					'BW' => $this->l('BW'), 'BY' => $this->l('BY'), 'BZ' => $this->l('BZ'), 'CA' => $this->l('CA'), 
					'CC' => $this->l('CC'), 'CF' => $this->l('CF'), 'CG' => $this->l('CG'), 'CH' => $this->l('CH'), 
					'CI' => $this->l('CI'), 'CK' => $this->l('CK'), 'CL' => $this->l('CL'), 'CM' => $this->l('CM'), 
					'CN' => $this->l('CN'), 'CO' => $this->l('CO'), 'CR' => $this->l('CR'), 'CU' => $this->l('CU'), 
					'CV' => $this->l('CV'), 'CX' => $this->l('CX'), 'CY' => $this->l('CY'), 'CZ' => $this->l('CZ'), 
					'DE' => $this->l('DE'), 'DJ' => $this->l('DJ'), 'DK' => $this->l('DK'), 'DM' => $this->l('DM'), 
					'DO' => $this->l('DO'), 'DZ' => $this->l('DZ'), 'EC' => $this->l('EC'), 'EE' => $this->l('EE'), 
					'EG' => $this->l('EG'), 'EH' => $this->l('EH'), 'ER' => $this->l('ER'), 'ES' => $this->l('ES'), 
					'ET' => $this->l('ET'), 'FI' => $this->l('FI'), 'FJ' => $this->l('FJ'), 'FK' => $this->l('FK'), 
					'FM' => $this->l('FM'), 'FO' => $this->l('FO'), 'FR' => $this->l('FR'), 'FX' => $this->l('FX'), 
					'GA' => $this->l('GA'), 'GB' => $this->l('GB'), 'GD' => $this->l('GD'), 'GE' => $this->l('GE'), 
					'GF' => $this->l('GF'), 'GH' => $this->l('GH'), 'GI' => $this->l('GI'), 'GL' => $this->l('GL'), 
					'GM' => $this->l('GM'), 'GN' => $this->l('GN'), 'GP' => $this->l('GP'), 'GQ' => $this->l('GQ'), 
					'GR' => $this->l('GR'), 'GS' => $this->l('GS'), 'GT' => $this->l('GT'), 'GU' => $this->l('GU'), 
					'GW' => $this->l('GW'), 'GY' => $this->l('GY'), 'HK' => $this->l('HK'), 'HM' => $this->l('HM'), 
					'HN' => $this->l('HN'), 'HR' => $this->l('HR'), 'HT' => $this->l('HT'), 'HU' => $this->l('HU'), 
					'ID' => $this->l('ID'), 'IE' => $this->l('IE'), 'IL' => $this->l('IL'), 'IN' => $this->l('IN'), 
					'IO' => $this->l('IO'), 'IQ' => $this->l('IQ'), 'IR' => $this->l('IR'), 'IS' => $this->l('IS'), 
					'IT' => $this->l('IT'), 'JM' => $this->l('JM'), 'JO' => $this->l('JO'), 'JP' => $this->l('JP'), 
					'KE' => $this->l('KE'), 'KG' => $this->l('KG'), 'KH' => $this->l('KH'), 'KI' => $this->l('KI'), 
					'KM' => $this->l('KM'), 'KN' => $this->l('KN'), 'KP' => $this->l('KP'), 'KR' => $this->l('KR'), 
					'KW' => $this->l('KW'), 'KY' => $this->l('KY'), 'KZ' => $this->l('KZ'), 'LA' => $this->l('LA'), 
					'LB' => $this->l('LB'), 'LC' => $this->l('LC'), 'LI' => $this->l('LI'), 'LK' => $this->l('LK'), 
					'LR' => $this->l('LR'), 'LS' => $this->l('LS'), 'LT' => $this->l('LT'), 'LU' => $this->l('LU'), 
					'LV' => $this->l('LV'), 'LY' => $this->l('LY'), 'MA' => $this->l('MA'), 'MC' => $this->l('MC'), 
					'MD' => $this->l('MD'), 'MG' => $this->l('MG'), 'MH' => $this->l('MH'), 'MK' => $this->l('MK'), 
					'ML' => $this->l('ML'), 'MM' => $this->l('MM'), 'MN' => $this->l('MN'), 'MO' => $this->l('MO'), 
					'MP' => $this->l('MP'), 'MQ' => $this->l('MQ'), 'MR' => $this->l('MR'), 'MS' => $this->l('MS'), 
					'MT' => $this->l('MT'), 'MU' => $this->l('MU'), 'MV' => $this->l('MV'), 'MW' => $this->l('MW'), 
					'MX' => $this->l('MX'), 'MY' => $this->l('MY'), 'MZ' => $this->l('MZ'), 'NA' => $this->l('NA'), 
					'NC' => $this->l('NC'), 'NE' => $this->l('NE'), 'NF' => $this->l('NF'), 'NG' => $this->l('NG'), 
					'NI' => $this->l('NI'), 'NL' => $this->l('NL'), 'NO' => $this->l('NO'), 'NP' => $this->l('NP'), 
					'NR' => $this->l('NR'), 'NU' => $this->l('NU'), 'NZ' => $this->l('NZ'), 'OM' => $this->l('OM'), 
					'PA' => $this->l('PA'), 'PE' => $this->l('PE'), 'PF' => $this->l('PF'), 'PG' => $this->l('PG'), 
					'PH' => $this->l('PH'), 'PK' => $this->l('PK'), 'PL' => $this->l('PL'), 'PM' => $this->l('PM'), 
					'PN' => $this->l('PN'), 'PR' => $this->l('PR'), 'PT' => $this->l('PT'), 'PW' => $this->l('PW'), 
					'PY' => $this->l('PY'), 'QA' => $this->l('QA'), 'RE' => $this->l('RE'), 'RO' => $this->l('RO'), 
					'RU' => $this->l('RU'), 'RW' => $this->l('RW'), 'SA' => $this->l('SA'), 'SB' => $this->l('SB'), 
					'SC' => $this->l('SC'), 'SD' => $this->l('SD'), 'SE' => $this->l('SE'), 'SG' => $this->l('SG'), 
					'SH' => $this->l('SH'), 'SI' => $this->l('SI'), 'SJ' => $this->l('SJ'), 'SK' => $this->l('SK'), 
					'SL' => $this->l('SL'), 'SM' => $this->l('SM'), 'SN' => $this->l('SN'), 'SO' => $this->l('SO'), 
					'SR' => $this->l('SR'), 'ST' => $this->l('ST'), 'SV' => $this->l('SV'), 'SY' => $this->l('SY'), 
					'SZ' => $this->l('SZ'), 'TC' => $this->l('TC'), 'TD' => $this->l('TD'), 'TF' => $this->l('TF'), 
					'TG' => $this->l('TG'), 'TH' => $this->l('TH'), 'TJ' => $this->l('TJ'), 'TK' => $this->l('TK'), 
					'TM' => $this->l('TM'), 'TN' => $this->l('TN'), 'TO' => $this->l('TO'), 'TP' => $this->l('TP'), 
					'TR' => $this->l('TR'), 'TT' => $this->l('TT'), 'TV' => $this->l('TV'), 'TW' => $this->l('TW'), 
					'TZ' => $this->l('TZ'), 'UA' => $this->l('UA'), 'UG' => $this->l('UG'), 'UM' => $this->l('UM'), 
					'US' => $this->l('US'), 'UY' => $this->l('UY'), 'UZ' => $this->l('UZ'), 'VA' => $this->l('VA'), 
					'VC' => $this->l('VC'), 'VE' => $this->l('VE'), 'VG' => $this->l('VG'), 'VI' => $this->l('VI'), 
					'VN' => $this->l('VN'), 'VU' => $this->l('VU'), 'WF' => $this->l('WF'), 'WS' => $this->l('WS'), 
					'YE' => $this->l('YE'), 'YT' => $this->l('YT'), 'YU' => $this->l('YU'), 'ZA' => $this->l('ZA'), 
					'ZM' => $this->l('ZM'), 'ZR' => $this->l('ZR'), 'ZW' => $this->l('ZW'));
		
		asort($arr);
		return $arr;
	}

}