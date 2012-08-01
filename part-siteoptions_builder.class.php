<?php
//load the plugins folder class builder if it exists, otherwise, use whatever version we happened to have in this file
if(file_exists (WP_PLUGIN_DIR.'/siteoptions_builder.class.php')) : include_once(WP_PLUGIN_DIR.'/siteoptions_builder.class.php');
else :
?>
<?php
/*
Official SiteOptions builder file
Version 1.1.1
*/
if(!defined('SM_SITEOP_PREFIX')) define('SM_SITEOP_PREFIX', 'sm_option_');
define('SM_SITEOP_DEBUG', FALSE);

//stop dbug calls from throwing errors when the sm-debug-bar plugin is not active
if(!function_exists('dbug')){function dbug(){}}
dbug('Version 1.1.1');

class sm_options_container
{
    // property declaration
    public $parts;			public $parent_id;
	public $capability;		public $id;
	public $notifications;	
	
	public function __construct($i, $args = array()) {
		$this->id = preg_replace('/_/','-',$i);
		$this->parts = array();
		$this->parent_id = '';
		$this->capability = 'read';
		$this->notifications = array();
	}
	
	public function add_part($part) {
		$this->parts[] = $part;
	}
	
	public function save_options() {
		$any_updated = false;
		foreach($this->parts as $part) {
			if(is_a($part, 'sm_option')) $updated = $part->update_option(); 
			else $updated = $part->save_options();
			if($updated) $any_updated = true;
		}
		if(defined('SM_SITEOP_DEBUG') && SM_SITEOP_DEBUG) dbug("Save Options ($this->id): ".var_export($updated, true));
		if($any_updated) $this->notifications['update'] = '<div class="updated">Your options have been saved!</div>';
		return $any_updated;
	}
	
	public function echo_notifications() {
		if(defined('SM_SITEOP_DEBUG') && SM_SITEOP_DEBUG) dbug($this->notifications);
		foreach ($this->notifications as $notify_html) echo $notify_html;
	}
	
}


class sm_options_page extends sm_options_container
{
    // property declaration
	public $page_title;		public $menu_title;
	public $libraries;		public $icon;
	public $disable_styles;	public $theme_page;

    // method declaration
    public function __construct($args = array()) {
		$defaults = array(
		    'parts' => array(),
			'parent_id' => 'options-general.php',
			'page_title' => 'SM Site Options',
			'menu_title' => 'SM Site Options',
			'capability' => 'manage_options',
			'id' => 'sm-site-options',
			'icon' => 'icon-options-general',
			'libraries' => array(),
			'disable_styles' => FALSE,
			'theme_page' => FALSE
		);
		$args = array_merge($defaults, $args);
		parent::__construct($args['id']);
		foreach($args as $name => $value)
			$this->$name = $value;
    }
	
	public function build() {
		add_action('admin_menu', array($this, 'build_page'));
		add_action('admin_print_scripts', array($this, 'media_upload_scripts'));
		add_action('admin_print_styles', array($this, 'media_upload_styles'));
		//TODO - add if statement to determine if media uploader scripts should be enqueued or not
		
	}	
	
	public function build_page() {
		if($this->theme_page) add_theme_page($this->page_title, $this->menu_title, $this->capability, $this->id, array($this, 'build_parts'));
		//else add_submenu_page( $this->parent_id, $this->page_title, $this->menu_title, $this->capability, $this->id, array($this, 'build_parts') );	
	}
	
	public function build_parts() {
		echo '<div id="smSiteOptions">';
		if(!$this->disable_styles) $this->inline_styles();
		
		//build the header
		if($this->icon && !empty($this->icon)) echo "<div class=\"icon32\" id=\"$this->icon\"><br></div>";
		echo "<h2>$this->page_title</h2>";
		echo '<div id="smOptionsContent">';
		
		//build the navigation menu if turned on for this page object
		//TODO - convert if statement and its dependencies to allow javascript navigation to be disabled using libraries array
		if(true) {
			echo '<div id="smOptionsNav"><ul>';
			foreach($this->parts as $key => $part) {
				dbug($part);
				if(((intval($key)+1) % 2) == 0) $part->classes[] = 'even';
				echo '<li id="'.$part->id.'-nav"><a href="#'.$part->id.'">'.$part->title.'</a></li>';
			}
			echo '</ul></div>';
		}
		
		if(isset($_POST['submit']) && $_POST['submit'] && $this->save_options())
			$this->echo_notifications();
		echo '<div id="smOptions"><form method="post" onReset="return confirm(\'Do you really want to reset ALL site options? (You will still need to click save changes)\')"><ul style="list-style: none;">';
		//build the content parts
		foreach($this->parts as $key => $part) {
			if(((intval($key)+1) % 2) == 0) $part->classes[] = 'even';
			if(defined('SM_SITEOP_DEBUG') && SM_SITEOP_DEBUG) echo $part->id.'[$part->echo_html()]->['.__CLASS__.'->echo_html()]<br />';
			$part->echo_html();	
		}
		
		echo '<li><p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="submit"><input type="reset" value="Reset" class="button-primary" name="reset"></p>'
			. '</li></ul></form></div>';
		echo '<div class="clear"></div></div>';//end of #smOptionsContent
		echo '</div>';//end of #smSiteOptions;
	}
	
	public function inline_styles() { 
		?>
	<style>
		#smOptionsContent {
			background: #F1F1F1; 
			border: 1px solid #D8D8D8; 
			border-top-right-radius: 0.7em; border-bottom-right-radius: 0.7em; border-bottom-left-radius: 0.7em; 
			margin-right: 12px;
			width: 704px;
		}
		#smSiteOptions .icon32 { margin: -8px 8px 0 0; }
		#smOptionsNav { float: left; width: 180px; }
		#smOptionsNav li { border-top: 1px solid #FFF; margin: 0px; border-bottom: 1px solid #D8D8D8;  }
		#smOptionsNav li.active { background: #FFF; border-right: #FFF;}
		#smOptionsNav li a { display:block; font-family:Georgia, Arial, serif; font-size: 13px; padding: 12px; text-decoration: none;}
		#smOptions { background: #FFF; float: left; padding: 12px; width: 500px;
		border-top-right-radius: 0.7em; border-bottom-right-radius: 0.7em; border-bottom-left-radius: 0.7em; }
		#smOptionsContent .section { display: none; }
		#smOptionsContent .section.active { display: block; }
		
		/* TODO - finish setting up javascript to detect image size and provide smaller image with preview text button
		#smOptionsContent .img-preview { max-height: 36px; overflow: hidden; padding: 12px; margin-top: -15px;  }
		#smOptionsContent .img-preview.full-preview { height: auto; overflow: visible; padding: 12px; margin-top: -15px;  }
		*/
		.clear { clear:both; }
		.section { padding: 5px; margin-right: 20px; }
		.section h3 { border-bottom: 1px solid #999; margin:0 0 10px; padding: 0 0 5px; }
		li.even.option { background-color: #ccc; }
		label {  width:200px; display:block; float:left; }
		label.option-label {  width:auto; display: inherit; float: none; }
		/* On/Off Switch */
		form a.switch { display:block; margin-left:200px; height:30px; width:70px; text-indent:-9999px;	background: url(http://lh3.googleusercontent.com/-knVz5thrqgw/Ts05srH_FbI/AAAAAAAAAB4/X1UaAz9Ejn0/s70/bg-checkbox.gif) no-repeat;
		} 
		form .off { background-position: 0 0!important; }
		form .on { background-position: 0 -31px!important; }
		input[disabled='disabled'] { background-color: #CCC; }
		.description { margin-left: 200px; margin-bottom: 15px; }
		img.active { border: 1px solid #da172d; padding: 2px !important; border-radius: .6em;  }
		img.colorpicker { padding: 3px; position: relative; top: 6px;}
		@media (min-width: 1100px){
			#smOptionsContent { width: 904px;}
			#smOptions { width:700px;}
		}
		@media (min-width: 1300px){
			#smOptionsContent { width: 1104px;}
			#smOptions { width:900px;}
		}
    </style>
<script>
	jQuery(document).ready(function() {
		jQuery(' input:checkbox.onOffSwitch').each(function(i){
			// add clas on if box is checked
			if(jQuery(this).is(':checked'))	addclass = 'on';
			else addclass = '';
			// create on/off link for switch grphic	
			jQuery(this).before('<a href="#" id="button-'+jQuery(this).attr('id')+'" class="switch '+addclass+'"></a>');
			// hide the check box
			jQuery(this).hide();
		});
		
		jQuery('form a.switch').click(function(e) {
			e.preventDefault();
			// change switch class
			jQuery(this).toggleClass('on').toggleClass('off');
			// save check box object
			thebox = jQuery(this).attr('id');
			thebox = '#'+thebox.replace('button-', '');
			// check and uncheck box
			if(jQuery(thebox).is(':checked')) jQuery(thebox).removeAttr('checked');
			else jQuery(thebox).attr('checked','checked');
		});
		
		
		jQuery('#smOptionsNav li a').click(function(){
			var active = jQuery((jQuery(this).attr('href'))+', '+(jQuery(this).attr('href')+'-nav')).addClass('active'); 
			jQuery(active).siblings().removeClass('active'); 
			return false;
		});
		//load hashed section if avail, otherwise load first section
		if(hash = window.location.hash) {console.log(jQuery(hash+'-nav a').trigger('click'));}
		else{jQuery('#smOptionsNav li:first a').trigger('click');}
		
		//prepare the media uploader tool
		storeSendToEditor = window.send_to_editor;
        newSendToEditor   = function(html) {
                                    imgurl = jQuery('img',html).attr('src');
                                    jQuery("#" + uploadID.name).val(imgurl);
                                    tb_remove();
                                    window.send_to_editor = storeSendToEditor;
							};
		
	});
	
	function sm_option_media_uploader(id) {
            window.send_to_editor = newSendToEditor;
            uploadID = id;
            formfield = jQuery('.upload').attr('name');
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            return false;
	}

</script>
        <?php
	}
	
	public function media_upload_scripts() {
		//media uploader
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		//colorpicker
		wp_enqueue_script( 'farbtastic' );
	}
	
	public function media_upload_styles() {
		//media uploader
		wp_enqueue_style('thickbox');
		//colorpicker
		wp_enqueue_style( 'farbtastic' );
	}
	
}

class sm_section extends sm_options_container {
	public $wrapper;	public $type;
	public $classes;	public $title;
	
	public function __construct($i, $args = array() ) {
		parent::__construct($i);
		$defaults = array(
		    'wrapper' => array('<ul>','</ul><div class="hr-divider"></div>'),
			'type' => 'default',
			'classes' => array('section', 'active'),
			'title' => 'My Custom Section'
		);
		$args = array_merge($defaults, $args);
		foreach($args as $name => $value)
			$this->$name = $value;
    }
	
	private function get_classes($echo = false) {
		$the_classes = '';
		foreach($this->classes as $class) {
			if(!empty($the_classes)) $the_classes .= ' ';
			$the_classes .= $class;
		}
		if(!empty($the_classes)) $the_classes = 'class="'.$the_classes.'"';
		if($echo) echo $the_classes; 
		return $the_classes;
	}
	
	public function echo_html() {
		$option = '<li id="'.$this->id.'" '.$this->get_classes().">";
		if(!empty($this->title)) $option .= "<h3>$this->title</h3>";
		$option .= $this->wrapper[0];
		foreach($this->parts as $part) {
			if($appendHTML = $part->get_html()) $option .= $appendHTML;
			else {echo $option; unset($option); $part->echo_html();}
		}
		$option .= $this->wrapper[1];
		$option .= '</li>';
		echo apply_filters('echo_html_option', $option);
		unset($option);
	}
}

class sm_option
{
    // property declaration
    public $id;				public $type;
	public $label;			public $default_value;
	public $classes;		public $rel;
	public $atts;			public $width;
	public $height;			public $length;
	public $wrapper;		public $description;
	
	

    // method declaration
    public function __construct($i, $args = array() ) {
		extract($args);
		$this->id = $i;
		$defaults = array(
			'type' => 'text',
			'label' => 'Option 1',
			'default_value' => '',
			'classes' => array('option'),
			'rel' => '',
			'atts' => array(),
			'width' => '',
			'height' => '',
			'length' => '',
			'wrapper' => array('<li>','</li>'),
			'description' => '',
			'atts' => array('disabled' => NULL)
		);
		$args = array_merge($defaults, $args);
		
		foreach($args as $name => $value)
			$this->$name = $value;
    }
	
	public function get_classes($echo = false) {
		$the_classes = '';
		foreach($this->classes as $class) {
			if(!empty($the_classes)) $the_classes .= ' ';
			$the_classes .= $class;
		}
		if(!empty($the_classes)) $the_classes = 'class="'.$the_classes.'"';
		if($echo) echo $the_classes; 
		return $the_classes;
	}
	
	public function update_option() {
		//delete_option('sm_option_');
		//delete_option('sm_option_ground_to_user');
		if(!isset($_POST[$this->id])) $_POST[$this->id] = '';
		
		if($_POST[$this->id] == '') 
			$updated = delete_option(SM_SITEOP_PREFIX.$this->id);
		else
			$updated = update_option(SM_SITEOP_PREFIX.$this->id, $_POST[$this->id]);
		return $updated;
	}
}

class sm_textfield extends sm_option 
{
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<label>$this->label</label>";
		if($this->atts['disabled']) $disabled = 'disabled="disabled"'; else $disabled = '';
		$html .= "<input id=\"$this->id\" name=\"$this->id\" type=\"text\" value=\"".get_option(SM_SITEOP_PREFIX.$this->id)."\" ".$disabled." />";
		if($this->description) $html .= '<div class="description clear">'.$this->description.'</div>';
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}	
}


class sm_textarea extends sm_option 
{
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<label>$this->label</label>";
		if($this->atts['disabled']) $disabled = 'disabled="disabled"';
		$html .= "<textarea id=\"$this->id\" name=\"$this->id\" cols=\"50\" rows=\"10\" ".$disabled.">".stripslashes (get_option(SM_SITEOP_PREFIX.$this->id) )."</textarea>";
		if($this->description) $html .= '<div class="description clear">'.$this->description.'</div>';
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}	
}

class sm_dropdown extends sm_option 
{
	public $values;
	
	public function __construct($i, $v) {
		parent::__construct($i);
		$this->values = ( !empty($v) ) ?  $v : array();	
	}
	
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<select id=\"$this->id\" name=\"$this->id\" value=\"".get_option(SM_SITEOP_PREFIX.$this->id)."\" />";
		$html .= '<option value="">Select a Value</option>';
		$stored_value = get_option(SM_SITEOP_PREFIX.$this->id);
		foreach($this->values as $key => $value) {
			if($value == $stored_value) $selected = 'selected="selected"'; else $selected='';
			$html .= "<option value=\"$value\" $selected>$value</option>";	
		}
		$html .= '</select> Value: '.get_option(SM_SITEOP_PREFIX.$this->id);
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}
}


class sm_checkbox extends sm_option 
{
	
	public $value;
	
	public function __construct($i, $args=array()) {
		parent::__construct($i, $args);
		$defaults = array(
		    'value' => ''
		);
		$args = array_merge($defaults, $args);
		
		foreach($args as $name => $value)
			$this->$name = $value;		
	}
	
	public function get_html() {
		if(!isset($display)) $display ='';
		$html = $this->wrapper[0];
		if(get_option(SM_SITEOP_PREFIX.$this->id) == $this->value) { $checked=" checked=\"checked\""; } else { $checked=""; }
		$html .= "<label>$this->label</label>";
		$html .= "<input type=\"checkbox\" value=\"$this->value\" id=\"$this->id\" name=\"$this->id\"".$checked.$display.$this->get_classes().">";
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}
	
}


class sm_radio_buttons extends sm_option 
{
	public $values;
	
	public function __construct($i, $v) {
		parent::__construct($i);
		$this->values = ( !empty($v) ) ?  $v : array();	
	}
	
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<label>$this->label</label>";
		
		$html .= "<div style=\"float:left;\">";
		foreach($this->values as $key => $value) {
			if( !is_numeric($key) ) { $radioLabel = $key; } else { $radioLabel = $value; }
			
			if( get_option(SM_SITEOP_PREFIX.$this->id) ) { $selectedVal = get_option(SM_SITEOP_PREFIX.$this->id); }
			else if( isset( $this->default_value) ) { $selectedVal = $this->default_value; }
			else { $selectedVal =''; }
				
			if($selectedVal == $value) { $checked=" checked=\"checked\""; } else { $checked=""; }
			
      		$html .= "<label class=\"option-label\"><input type=\"radio\" name=\"$this->id\" value=\"$value\" id=\"$this->id\" $checked /> $radioLabel</label>";
			$html .= "<div class=\"clear\"></div>";
		}
		$html .= "</div>";
		$html .= "<div class=\"clear\"></div>";
		if($this->description) $html .= '<br /><div class="description clear">'.$this->description.'</div>';
		$html .= $this->wrapper[1];
		return $html;
	}
	
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}
}

class sm_media_upload extends sm_option 
{
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<label>$this->label</label>";
		if($this->atts['disabled']) $disabled = 'disabled="disabled"';
		$html .= "<input id=\"$this->id\" name=\"$this->id\" type=\"text\" value=\"".get_option(SM_SITEOP_PREFIX.$this->id)."\" ".$disabled." />";
		$html .= '<input id="'.$this->id.'_button" type="button" value="Upload Image" onclick="sm_option_media_uploader('.$this->id.')"'.$disabled.'/><input id="'.$this->id.'_reset" type="button" value="X" onclick="jQuery(\'#'.$this->id.'\').val(\'\');" />';
		if($this->description) $html .= '<div class="description clear">'.$this->description.'</div>';
		if(get_option(SM_SITEOP_PREFIX.$this->id)) $html .= "<div class=\"clear\"></div><div class=\"img-preview description collapsed\"><img id=\"image_$this->id\" src=\"".get_option(SM_SITEOP_PREFIX.$this->id)."\" /></div>";
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}	
}

class sm_include_file extends sm_option
{
	public $filename;
	
	public function __construct($i,$f,$v = array()) {
		parent::__construct($i);
		$this->values = ( !empty($v) ) ?  $v : array();	
		$this->filename = ( !empty($f) ) ?  $f : 'set_the_filename.php';	
	}
	public function get_html() {
		return false;
	}
	public function echo_html() {
		if(!empty($this->filename))	include_once($this->filename);
	}
}

class sm_color_picker extends sm_option 
{
	public function get_html() {
		$html = $this->wrapper[0];
		$html .= "<label>$this->label</label>";
		if($this->atts['disabled']) $disabled = 'disabled="disabled"';
		if($the_color = get_option(SM_SITEOP_PREFIX.$this->id)) {} else $the_color = '#ffffff';
		$html .= "<input id=\"$this->id\" name=\"$this->id\" type=\"text\" value=\"".$the_color."\" ".$disabled." /> <img class=\"colorpicker\" src=\"http://openiconlibrary.sourceforge.net/gallery2/open_icon_library-full/icons/png/22x22/actions/color-fill.png\" />";
		$html .= '<div id="'.$this->id.'_palette" class="sm_palettes"></div>';
		$html .= '
	<script type="text/javascript">
	var prev_palette = jQuery("");
	jQuery(document).ready(function() {
		jQuery("#'.$this->id.'_palette").hide();
		jQuery("#'.$this->id.'_palette").farbtastic("#'.$this->id.'");
		jQuery("#'.$this->id.'").next().click(function(){
			curr_palette = jQuery("#'.$this->id.'_palette");
			if(curr_palette.attr("id") == prev_palette.attr("id")) { curr_palette.slideToggle(); curr_palette.prev().toggleClass("active"); }
			else {
				jQuery(".sm_palettes").hide();
				curr_palette.prev().addClass("active");
				prev_palette.prev().removeClass("active");
				var pos   = jQuery(this).offset();
				var width = 0;//jQuery(this).width();
				jQuery("#'.$this->id.'_palette").css({"position":"absolute", "left": (pos.left - 100 + width) + "px", "top":(pos.top - 100) + "px" });			
				curr_palette.slideDown();
			}
			prev_palette = curr_palette;
		});
	});
	</script>';
		if($this->description) $html .= '<div class="description clear">'.$this->description.'</div>';
		$html .= "<div class=\"clear\"></div>";
		$html .= $this->wrapper[1];
		return $html;
	}
	public function echo_html() {
		$html = $this->get_html();
		echo apply_filters('echo_html_option', $html);
	}	
}


endif;