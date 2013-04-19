<?php
/*
Plugin Name: Happy Collision Portfolio
Description: Model for a media portfolio.
Author: Don Denton
Version: 1.0
Author URI: http://happycollision.com
Depends: Simple Fields
*/



if(!function_exists('simple_fields_register_field_group')):

if(function_exists('hcwarn')){
	hcwarn('Happy Collision Portfolio cannot run because Simple Fields is not installed. Or Simple Fields is installed, but not loading early enough. Try clicking the "Plugins" link on the left-hand side of the screen. That will reload this page the proper way. If this message disappears, you are fine. If not, contact your developer.',true);
}

function hc_portfolio(){
	return false;
}


else:

function hc_portfolio(){
	return true;
}


Class HCPortfolio{
	// All the 'name' fields should be unique. Or at least, no two 'name' fields of different 'type' values should be the same. This could cause data to be mistaken for something else and overwritten.
	public static $all_simple_fields =
		array(
			'video'=>array(
				array(
					'slug' => "hc_video_name",
					'name' => 'Video Name',
					'description' => 'The name of the video clip you are uploading. For example: "Oklahoma Slideshow" or "Oklahoma Clips".',
					'type' => 'text'
				)
				,array(
					'slug' => "hc_video_image",
					'name' => 'Placeholder Image',
					'description' => 'Upload an image file as a placeholder for the video.',
					'type' => 'file'
				)
				,array(
					'slug' => "hc_video_mp4",
					'name' => 'MP4 Video',
					'description' => 'Upload the .mp4 version here, if you have it.',
					'type' => 'file'
				)
				,array(
					'slug' => "hc_video_webm",
					'name' => 'WEBM Video',
					'description' => 'Upload the .webm version here, if you have it.',
					'type' => 'file'
				)
				,array(
					'slug' => "hc_video_ogg",
					'name' => 'OGG/Theora Video',
					'description' => 'Upload the .ogg version here, if you have it.',
					'type' => 'file'
				)
				,array(
					'slug' => "hc_video_flv",
					'name' => 'FLV Video',
					'description' => 'Upload the .flv (or .f4v) version here, if you have it.',
					'type' => 'file'
				)
				,array(
					'slug' => "hc_video_mov",
					'name' => 'MOV or M4V Video',
					'description' => 'Upload the .mov or .m4v version here, if you have it.',
					'type' => 'file'
				)
			)
			,'audio'=>array(
				array(
					'slug' => "hc_audio_name",
					'name' => 'Audio Name',
					'description' => 'The name of the audio clip you are uploading. For example: "H2$ Overture".',
					'type' => 'text'
				)
				,array(
					'slug' => "hc_audio",
					'name' => 'MP3 Audio',
					//'description' => '',
					'type' => 'file'
				)
			)
			,'image_gallery'=>array(
				array(
					'slug' => "hc_image_gallery_item",
					'name' => 'Gallery Image',
					//'description' => '',
					'type' => 'file'
				)
			)
			,'single_image'=>array(
				array(
					'slug' => "hc_image_item",
					'name' => 'Single Image',
					//'description' => '',
					'type' => 'file'
				)
			)
		);
	
	//Just easiest to manually copy from above. Seriously.
	private static $html5_field_names = 
		array(
			'MP4 Video'
			,'WEBM Video'
			,'OGG/Theora Video'
		);
	private static $video_types = 
		array(
			'mp4'=>array(
				'html5'=>true
				,'mime'=>'video/mp4'
			)
			,'webm'=>array(
				'html5'=>true
				,'mime'=>'video/webm'
			)
			,'ogv'=>array(
				'html5'=>true
				,'mime'=>'video/ogg'
			)
			,'flv'=>array(
				'html5'=>false
				,'mime'=>'video/flv'
			)
			,'f4v'=>array(
				'html5'=>false
				,'mime'=>'video/flv'
			)
			,'mov'=>array(
				'html5'=>false
				,'mime'=>'video/quicktime'
			)
			,'m4v'=>array(
				'html5'=>false
				,'mime'=>'video/quicktime'
			)
		);
	private static $file_field_names;
	
	private $video_data;
	private $have_html5 = false;
	private $audio_data;
	private $project_data;
	private $all_items_data;
	private $single_link;
	private $single_name;
	
	//Will contain arrays of ready to print HTML.
	private $all_items_output = array();
	private $video_items_output = array();
	private $audio_items_output = array();
		
	
	function __construct($post_id){
		if(self::$file_field_names == null) self::file_field_names();
		$this->video_data = $this->get_video_values($post_id); 
		$this->audio_data = $this->get_audio_values($post_id);
		$this->project_data = simple_fields_get_post_group_values($post_id, "Project Properties", true, 2);
		
		$this->all_items_data = array(
			'videos' => $this->video_data
			,'audio' => $this->audio_data
		);
	}
	
	private function get_video_values($post_id){
		$videos = simple_fields_get_post_group_values($post_id, "Video Item(s)", true, 2);
		$this->check_for_html5_video($videos);
		$this->hc_portfolio_prepare_file_paths($videos);
		return $videos;
	}

	private function get_audio_values($post_id){
		$audio = simple_fields_get_post_group_values($post_id, "Audio Item(s)", true, 2);
		$this->hc_portfolio_prepare_file_paths($audio);
		return $audio;
	}

	private function un_ms_file_rewrite_path($url_to_file){
		//Fix iOS issue if this blog uses ms-file rewrite. If not, this code does no harm.
		
		$basedir = wp_upload_dir();
		$basedir = $basedir['basedir'];
		$date_folder = NULL;
		preg_match('#/[0-9]{4}/[0-1]{1}[0-9]{1}#', $url_to_file, $date_folder);
		$path_to_file = $basedir . $date_folder[0] . '/' . basename($url_to_file);
		//at this point, we have the full unix path to the file. This is bad on the internet.
		
		//strip the unnecessary unix stuff at the beginning and replace it with the domain name
		$path_to_file = strstr($path_to_file, 'wp-content');
		
		//build the domain-based path
		$url_build = wp_upload_dir();
		$url_build = $url_build['baseurl'];
		$url_build = substr($url_build, 0, strpos($url_build, 'wp-content'));
		
		return $url_build . $path_to_file;
	}

	private function hc_portfolio_prepare_file_paths(&$array_of_file_data){
		//changing variable name for clarity
		$files = &$array_of_file_data;

		foreach($files as &$file){
			
			foreach($file as $field_name => &$field){
				//Is this an attachment field? Wouldn't want to accidentally just look up a random URL for what is supposed to actually be a number.
				if(!in_array($field_name, self::$file_field_names)) continue;
				if($field == 0) {
					//remove unused URL fields
					unset($file[$field_name]);
					continue;
				}
				
				$field = $this->un_ms_file_rewrite_path(wp_get_attachment_url($field));
				
			}
		}			
	}
	
	private static function file_field_names(){
		$output = array();
		$field_group_array = self::$all_simple_fields;
		foreach($field_group_array as $field_group){
			foreach($field_group as $field){
				if($field['type'] === 'file'){
					$output[] = $field['name'];
				}
			}
		}
		self::$file_field_names = $output;
	}
	
	private function check_for_html5_video($videos){
		foreach($videos as $video_data)
		//Check if we have an HTML5 video available
		foreach(static::$html5_field_names as $type){
			if($video_data[$type] != false){
				//we have at least one HTML5 video type
				$this->have_html5 = true;
				break;
			}
		}
	}
	
	public function echo_video($title_tag = null, $width = null, $height = null){
		if(empty($this->video_items_output)) $this->prepare_video_output($title_tag,$width,$height);
		
		foreach($this->video_items_output as $output) echo $output;
	}
	
	private function prepare_video_output($title_tag=null,$width=null,$height=null){
		if(empty($this->video_data)) return;
		foreach($this->video_data as $clip){
			unset($sources);
			$player_video_url = '';
			$output_buffer = '';
			foreach($clip as $field_name => $property){
				if(!in_array($field_name, self::$file_field_names)) continue;
				$ext = pathinfo($property,PATHINFO_EXTENSION);
				
				if(isset(self::$video_types[$ext])){
					if(self::$video_types[$ext]['html5']==true){
						$sources[] = '<source src="'.$property.'" type="'.self::$video_types[$ext]['mime'].'" />';
					}else{
						$player_video_url = $property;
						break;
					}
				}
			}
			//Move on if there is no video to see here.
			if(!isset($sources) && $player_video_url === '') continue;
			
			$title = $this->prepare_item_title('video',$title_tag,$clip);
			if(isset($clip['Placeholder Image'])){
				$init = "<video width=\"{$width}\" height=\"{$height}\" controls poster=\"{$clip['Placeholder Image']}\">";
			}else{
				$init = "<video width=\"{$width}\" height=\"{$height}\" controls>";
			}
		
			
			$player = 
				'<object width="100%" height="360" type="application/x-shockwave-flash" data="'.plugins_url('/flowplayer-3.2.15.swf', __FILE__).'">'.
					'<param name="movie" value="'.plugins_url('/flowplayer-3.2.15.swf', __FILE__).'" />'.
					'<param name="allowfullscreen" value="true" />'.
					'<param name="flashvars" value="config={\'clip\': {\'url\': \''.$player_video_url.'\', \'autoPlay\':false, \'autoBuffering\':true, \'scaling\':\'fit\'}, \'canvas\':{\'backgroundColor\':\'#000\'}}" />'.
				'</object>';
			
			$destroy = '</video>';
			
			//Okay. Plug it all in.
			$output_buffer = $title;
			if(isset($sources)){
				$output_buffer .= $init;
				foreach($sources as $source){
					$output_buffer .= $source;
				}
			}
			if($player_video_url !== ''){
				$output_buffer .= $player;
			}
			if(isset($sources)){
				$output_buffer .= $destroy;
			}
			
			$this->video_items_output[] = $output_buffer;
			$this->all_items_output[] = $output_buffer;
		}
	}
	
	private function prepare_audio_output($title_tag=null,$width=null,$height=null){
		if(empty($this->audio_data)) return;
		foreach($this->audio_data as $clip){
			$title = $this->prepare_item_title('audio',$title_tag,$clip);
			$output_buffer = 
				"$title
				<audio controls>
					<source src=\"{$clip['MP3 Audio']}\" type=\"\" />
				</audio>";
			$this->audio_items_output[] = $output_buffer;
			$this->all_items_output[] = $output_buffer;
		}
	}

	private function prepare_item_title($kind,$title_tag='h4',$single_array){
		if($title_tag==null) $title_tag = 'h4';
		return "<{$title_tag}>" . $this->get_item_title($kind,$single_array) . "</{$title_tag}>";
	}
	
	private function get_item_title($kind, $single_array){
		switch ($kind):
			case 'video':
				return $single_array['Video Name'];
				break;
			case 'audio':
				return $single_array['Audio Name'];
				break;
		endswitch;
	}
	
	private function populate_all_items(){
		$this->prepare_video_output();
		$this->prepare_audio_output();
	}
	
	//Public Functions
	public function title($id=null){
		//Title of projet is stored as Wordpress Post Title
		echo get_the_title($id=null);
	}
	
	public function description(){
		echo $this->project_data['Project Description'];
	}
	
	public function location(){
		echo $this->project_data[0]['Project Location'];
	}
	
	public function date($format=null){
		if($format == null){
			echo $this->project_data['Project Date'];
			return;
		}
		echo date($format,strtotime($this->project_data['Project Date']));
	}
	
	public function all_items($tag=null,$class=null){
		if(empty($this->all_items_output)) $this->populate_all_items();
		$tag = $tag == null ? 'div' : $tag;
		$class = $class == null ? 'item' : $class;
		foreach($this->all_items_output as $item){
			echo "<{$tag} class=\"{$class}\">$item</{$tag}>";
		}
	}
	
	public function count_items(){
		if(empty($this->all_items_output)) $this->populate_all_items();
		return count($this->all_items_output);
	}
	
	public function single_link(){
		if(!empty($this->single_link)){
			echo $this->single_link;
			return;
		}
		if(empty($this->all_items_data)) return false;
		$check_array = array(
			'MP3 Audio',
			'MP4 Video',
			'WEBM Video',
			'OGG/Theora Video',
			'MOV or M4V Video',
			'FLV Video'
		);
		
		foreach($this->all_items_data as $category){
			foreach($category as $item){
				foreach($item as $field_name => $possible_url){
					if(in_array($field_name, $check_array)){
						$this->single_link = $possible_url;
						echo $this->single_link;
						return;
					}
				}
			}
		}
		return false;
	}
	
	public function single_name(){
		if(!empty($this->single_name)) {
			echo $this->single_name;
			return;
		}
		if(empty($this->all_items_data)) return false;
		$check_array = array(
			'Video Name',
			'Audio Name'
		);
		
		foreach($this->all_items_data as $category){
			foreach($category as $item){
				foreach($item as $field_name => $possible_name){
					if(in_array($field_name, $check_array)){
						$this->single_name = $possible_name;
						echo $this->single_name;
						return;
					}
				}
			}
		}

	}
}
require_once('hc_portfolio_post-type.php');
require_once('simple_fields_call.php');

endif;