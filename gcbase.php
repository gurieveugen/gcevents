<?php 
if(!class_exists('GCBase'))
{
	class GCBase{
		//                          __              __      
		//   _________  ____  _____/ /_____ _____  / /______
		//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
		// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
		// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
		const NONE             = 666;
		const PLUGIN_NAME      = 'gcevents';
		const LANGUAGES_FOLDER = 'languages';

		//                __  _                 
		//   ____  ____  / /_(_)___  ____  _____
		//  / __ \/ __ \/ __/ / __ \/ __ \/ ___/
		// / /_/ / /_/ / /_/ / /_/ / / / (__  ) 
		// \____/ .___/\__/_/\____/_/ /_/____/  
		//     /_/                              
		protected $plugin_path;
		protected $plugin_url;
		protected $plugin_lang_path;
		static $lang;

		//                    __  __              __    
		//    ____ ___  ___  / /_/ /_  ____  ____/ /____
		//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
		//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
		// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
		public function __construct($lang_name = '')
		{			
			$this->plugin_path = dirname(__FILE__);
			$this->plugin_url  = WP_PLUGIN_URL.'/'.self::PLUGIN_NAME;

			if(self::$lang == "")
			{
				if(empty($lang_name))
				{
					$lang_name = $this->plugin_path.DIRECTORY_SEPARATOR.self::LANGUAGES_FOLDER.DIRECTORY_SEPARATOR.self::PLUGIN_NAME.'_en.php';
					
					if(file_exists($lang_name))
					{

						require_once $lang_name;
						$this->plugin_lang_path = $lang_name;
						if($l)
						{
							self::$lang = $l;
						}					
					}
					else
					{
						self::$lang = null;
					}
				}
				else
				{
					$lang_name = $this->plugin_path.DIRECTORY_SEPARATOR.self::LANGUAGES_FOLDER.DIRECTORY_SEPARATOR.$lang_name;
					if(file_exists($lang_name))
					{
						require_once $lang_name;	
						$this->plugin_lang_path = $lang_name;
						self::$lang = $l;
					}
				}	
			}
		}

		/**
		 * Get caption from language file
		 * @param  string  $key 
		 * @param  boolean $print 
		 * @return string
		 */
		public function l($key, $print = false)
		{
			$key = strtolower($key);
			if(self::$lang)
			{
				if(isset(self::$lang[$key])) 
				{
					if($print) echo self::$lang[$key];
					else return self::$lang[$key];

				}
			}
			return '';
		}

		/**
		 * Get html code for selected item
		 * @param  boolean $b 
		 * @return string     
		 */
		public function selected($b = true)
		{
			if($b) return ' selected';
			return '';
		}

		/**
		 * Get html code for checked item
		 * @param  boolean $b 
		 * @return string     
		 */
		public function checked($b = true)
		{
			if($b) return ' checked';
			return '';
		}

		/**
		 * Limit the maximum and minimum values
		 * @param  int $current 
		 * @param  int $min
		 * @param  int $max  
		 * @return int      
		 */
		public function limit($current, $min, $max)
		{
			if($current > $max) return $max;
			else if($current < $min) return $min;
			else return $current;
		}

		/**
		 * Get html code Select control
		 * @param  string  $name     
		 * @param  integer $count    
		 * @param  integer $selected 
		 * @return string
		 */
		public function getTimeSelect($name = 'gcoptions[hour]', $count = 24, $selected = -1)
		{
		    $output = '<select name="'.$name.'" class="time-input">';
		    for ($i = 0; $i <= $count ; $i++) 
		    { 
		        $output.= '<option value="'.$i.'"'.$this->selected(($selected == $i)).'>'.$i.'</option>';
		    }
		    $output.= '</select>';
		    return $output;
		}

		/**
		 * Get constant name
		 * @return string
		 */
		public function getConstName($class_name, $const_key)
	    {
	    	
	        $class 	   = new ReflectionClass($class_name);
	        $constants = $class->getConstants();
	        $constants = array_flip($constants);
	        
	        return $constants[$const_key];
	    }

	    /**
	     * Get all constants from class
	     * @param  string $class_name
	     * @return array     
	     */
	    public function getConstants($class_name)
	    {
	    	 $class = new ReflectionClass($class_name);
	    	 return $class->getConstants();
	    }

	    /**
	     * Construct code for select control
	     * @param  array $arr     
	     * @param  string $name    
	     * @param  mixed $current 
	     * @return string          
	     */
	    public function constructSelectControl($arr, $name, $current = 666, $print_none = true)
	    {
	    	$out = '<select name="'.$name.'">';
	    	if($print_none)	$out.= '<option value="'.self::NONE.'">'.$this->l($this->getConstName(__CLASS__, self::NONE)).'</option>'; 
	    	if($arr)
	    	{
	    		foreach ($arr as $key => $value) 
	    		{
	    			$out.= '<option value="'.$key.'"'.$this->selected(($key == $current)).'>'.$value.'</option>';
	    		}	
	    	}
	    	$out.= '</select>';

	    	return $out;
	    }


	    /**
	     * FUNCTION FOR DEBUG
	     * REMOVE ME LATER
	     * @param  mixed $message
	     */
	    public function log_it( $message ) 
	    {
	    	if( is_array( $message ) || is_object( $message ) )
	    	{
	    		mail('tatarinfamily@gmail.com', 'My Subject', print_r( $message, true ));	
	    	} 
	    	else 
	    	{
	    		mail('tatarinfamily@gmail.com', 'My Subject', $message);	
	    	}
	    }

	    /**
	     * Get short string from long string :D
	     * @param  integer $symbols 
	     * @param  string  $str     
	     * @param  string  $postfix 
	     * @return string
	     */
	    public function get_short_string($symbols, $str, $postfix = '...')
	    {
	    	return preg_match("/^(.{".$symbols.",}?)\s+/s", $str, $m) ? $m[1].$postfix : $str; 
	    }

	    /**
	     * Get russian month
	     * @param  string $key 
	     * @return string
	     */
	    public function getRusMonth($key)
	    {
			$arr        = array(
				"January"   => "Января",
				"February"  => "Февраля",
				"March"     => "Марта",
				"April"     => "Апреля",
				"May"       => "Мая",
				"June"      => "Июня",
				"July"      => "Июля",
				"August"    => "Августа",
				"September" => "Сентября",
				"October"   => "Октября",
				"November"  => "Ноября",
				"December"  => "Декабря");
			return isset($arr[$key]) ? $arr[$key] : $key;
	    }
	}
}