<?php
// no direct access
defined ( "_JEXEC" ) or die ( "destricted access" );

/**
 * Translation model class for Translation
 *
 * @package joomla.admin
 * @subpackage com_translation
 * @since 1.6
 *       
 */
class TranslationModelTranslation extends JModelItem {
	
	public $path;	
	public $tag_en;
	public $tag_zh;
	public $path_en;
	public $path_zh;
	
	public function __construct(){
		$this->path = JPATH_BASE . DS . 'language';
		$this->tag_en = 'en-GB';
		$this->tag_zh = 'lzy-CN';
		$this->path_en = $this->path . DS . $this->tag_en;
		$this->path_zh = $this->path . DS . $this->tag_zh;
		parent::__construct();
	}
	/**
	 * method to create language file if not exit;
	 */
	public function getFile() {
		// copy the file from en language dir if zh language dir is not exit
		if (! file_exists ( $this->path_zh )) {
			@mkdir ( $this->path_zh );
			// copy all the file from en language dir
			$this->copy_files ( $this->path_en, $this->path_zh, $this->tag_en, $this->tag_zh, $filesCopy = '' );
		}else {
			// copy the file from en language dir if not exist in zh language dir
			$this->explode_filename ( $this->path_en, $this->path_zh, $this->tag_en, $this->tag_zh );
		}
		$this->insert_data($this->path_zh);
	}
	/**
	 * method to copy file from $src to $dst
	 * 
	 * @param $sourse the
	 *        	source dir
	 * @param $dest the
	 *        	directy dir
	 * @param $search the
	 *        	string if include the $search that will be replased by $replace
	 * @param $replace will
	 *        	replace the sting of $search
	 * @param $filesCopy the
	 *        	file is assigned to copy
	 *        	
	 */
	public function copy_files($sourse, $dest, $search, $replace, $filesCopy = '') {
		if ($filesCopy == '') {
			$files = scandir ( $sourse );
		} else {
			$files = $filesCopy;
		}
		foreach ( $files as $file ) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir ( $sourse . DS . $file )) {
					copy_files ( $sourse . DS . $file, $dest . DS . $file, $search, $replace );
				} else {
					copy ( $sourse . DS . $file, $dest . DS . str_replace ( $search, $replace, $file ) );
				}
			}
		}
	}
	
	/**
	 * method to scan a dir and return the files's name if is not in the $dest dir
	 * 
	 * @param $sourse the
	 *        	source dir
	 * @param $dest the
	 *        	directy dir
	 * @param $search the
	 *        	string if include the $search that will be replased by $replace
	 * @param $replace will
	 *        	replace the sting of $search
	 * @param $filesCopy the
	 *        	file is assigned to copy
	 *        	
	 */
	public function explode_filename($path_en, $path_zh, $search, $replace) {
		$files_en = scandir ( $path_en );
		$files_zh = scandir ( $path_zh );
		foreach ( $files_en as $key => $val ) {
			$temp [$key] = str_replace ( $search, $replace, $val );
			if (in_array ( $temp [$key], $files_zh )) {
				unset ( $files_en [$key] );
			}
		}
		$this->copy_files ( $path_en, $path_zh, $search, $replace, $files_en );
	}
	/**
	 * according the file path and method to read the content to a string
	 *
	 * @param string $path
	 *        	the file path
	 * @param mixture $return_type
	 *        	string or array,
	 *        	if set $return_type=str,the function will return a string,
	 *        	if you set $return_type=arr,the function will return array,
	 *        	default is arr;
	 *        	
	 * @return mixture depend on the param $return_type
	 */
	public function read_file($file, $return_type = "arr") {
		// Determine whether a file exists
		if (file_exists ( $file )) {
			// Determine whether a file is readable
			if (is_readable ( $file )) {
				// The file is exist,readable,the next do what you want
				if ($return_type == "str")
					$str_content = file_get_contents ( $file );
				else
					$str_content = file ( $file );
				return $str_content;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * filter the array if the value is null
	 *
	 * @param string $file
	 *        	the file of translated.
	 *        	
	 * @return string $res the string of key+value,like:('$key','$val'),in order to insert into the database.
	 */
	public function filter_string($file) {
		$arr = $this->read_file ( $file );
		$i=0;
		$res = array();
		if (! is_null ( $arr )) {
			foreach ( $arr as $key => $val ) {
				if ($val == PHP_EOL || substr ( $val, 0, 1 ) == ";" ||strlen($val)<2)
					continue;
				else {
						$tmp = explode ( "=", $val, 2 );
						$res[$i][0] = trim ( $tmp [0] );
						$res[$i++][1] = trim ( $tmp [1] );
				}
			}
			return $res;
		} else {
			return false;
		}
	}
	
	/**
	 * method to insert the data to the database.
	 * @param	the language files dir to insert into the database
	 * @return	array $values the data will insert into the database.
	 */
	public function getData($path) {
		$files=scandir($path);
		$values=array();
		if(!is_null($files)){
			foreach ($files as $file){
				if($file!='.' && $file!='..' && substr($file, -3)=='ini'){
					$res=$this->filter_string($path.DS.$file);
					$values=array_merge($res,$values);//this will remove the duplicate values
				}
			}
		}
		return $values;
	}
	/**
	 * method to insert the data to the database.
	 * @param array $values the data will insert into the database
	 * @return null
	 * */
	public function getInsert($values){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->insert($db->quoteName('#__translation'));
		$query->columns($db->quoteName(array('name', 'content')));
		foreach ($values as $value){
			$query->values(implode(',',$db->quote($value)));
		}
		$db->setQuery($query);
		$result = $db->execute();
	}
	/**
	 * 
	 * */
	public function getExcute($values){
		$temp=array();
		foreach ($values as $key=>$value){
			
			$temp[]=$value;
		}
	}
}
