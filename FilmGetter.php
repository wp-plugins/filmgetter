<?php
/*
Plugin Name: FilmGetter
Plugin URI: http://dun.se/plugins/
Description: Gets the Movie info from TheMoveDB.
Version: 0.1
Author: Håkan Nylén
Author URI: http://dun.se
License: GPL2
*/
/*  Copyright 2010 Håkan Nylén (email : hakan@dun.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//The Functions, plz don't edit here if you don't know what's happening.

require_once("TMDb.php");

//fix activation methods and deactivation methods for hooks.
register_activation_hook(__FILE__, 'FilmGetter_install');
register_deactivation_hook(__FILE__, 'FilmGetter_uninstall');

//install the plugin, create table and options.
function FilmGetter_install()
{
	global $wpdb;
	$table = $wpdb->prefix."FilmGetter"; //prefix for the tables in database
    $structure = "CREATE TABLE $table (
        id INT(12) NOT NULL AUTO_INCREMENT,
        movie_name VARCHAR(80) NOT NULL,
        movie_release VARCHAR(20) NOT NULL,
        movie_rate VARCHAR(20) NOT NULL,
        movie_trailer VARCHAR(20) NOT NULL,
        movie_plot VARCHAR(350) NOT NULL,
        movie_url VARCHAR(120) NOT NULL,
        movie_imdb VARCHAR(120) NOT NULL,
        movie_pic VARCHAR(150) NOT NULL,
	UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);
	add_option("TMDbApi", "", "", "yes");
}

// uninstall the plugin, deletes the table, not the option
// can be good to have it left, so you don't need to write it again after reactivation.
function FilmGetter_uninstall()
{
	global $wpdb;
	$table = $wpdb->prefix."FilmGetter"; //prefix for the tables in database
	remove_filter('the_content', 'FilmGetter_parse_film', 2);
	remove_filter('the_content', 'FilmGetter_parse_imdb', 2);
	$structure = "DROP TABLE ".$table.";";
	$wpdb->query($structure);
}

//show the movies information with pic for the tag [film]
function FilmGetter_show_film($name)
{
	global $wpdb;
	$table = $wpdb->prefix."FilmGetter"; //prefix for the tables in database
    $sql = "SELECT * FROM ".$table." WHERE movie_name LIKE '{$name}' LIMIT 1";
    
    $results = $wpdb->get_results($sql);
    
    if(count($results) > 0)
	{
		foreach($results as $result)
		{
			$content .= "<div style='display:block;margin-top:5px;clear:bloth;width:100%;height:100px;margin-bottom:10px;'><img src='".$result->movie_pic."' style='float:left;width:80px;height:100px;margin-right:5px;' /><strong>".$result->movie_name."</strong><br />".$result->movie_release." - ".$result->movie_rate."<br />".$result->movie_plot."<br /><a href='".$result->movie_trailer."'>Trailer</a> - <a href='".$result->movie_url."'>TMDb</a> - <a href='".$result->movie_imdb."'>IMDb</a></div>";
			$content .= "<div style='display:block;width:100%;clear:both;heeight:30px;'></div>";
		}				
	}

    
    return $content;
}

//get the movie's imdb url and post it for the tag [imdb]
function FilmGetter_show_imdb($name)
{
	global $wpdb;
	$table = $wpdb->prefix."FilmGetter"; //prefix for the tables in database
    $sql = "SELECT * FROM ".$table." WHERE movie_name LIKE '{$name}' LIMIT 1";
    
    $results = $wpdb->get_results($sql);
    
    if(count($results) > 0)
	{
		foreach($results as $result)
		{
			$content .= "<a href='".$result->movie_imdb."'>IMDb</a>";
		}				
	}

    
    return $content;
}

//importing the movie-information from TMDb and add it to the database.
function FilmGetter_add_film($id)
{
	global $wpdb;
	$tmdbapi = get_option("TMDbApi", "");
	$table = $wpdb->prefix."FilmGetter"; //prefix for the tables in database
	
	$tmdb_xml = new TMDb($tmdbapi,TMDb::XML);
	$xml = simpleXMLToArray(simplexml_load_string($tmdb_xml->getMovie($id)));
	
	$name = mysql_real_escape_string($xml[movies][movie][name]);
	$rate = $xml[movies][movie][rating];
	$trailer = $xml[movies][movie][trailer];
	$plot = mysql_real_escape_string($xml[movies][movie][overview]);
	$release = $xml[movies][movie][released];
	$pic = $xml[movies][movie][images][image][0][url];
	$url = $xml[movies][movie][url];
	$imdb = "http://www.imdb.com/title/".$xml[movies][movie][imdb_id]."/";
	
	$structure = "INSERT INTO ".$table." (movie_name, movie_release, movie_pic, movie_rate, movie_trailer, movie_plot, movie_url, movie_imdb) VALUES('{$name}', '{$release}', '{$pic}', '{$rate}', '{$trailer}', '{$plot}', '{$url}', '{$imdb}');";
    $wpdb->query($structure);
}

//fix parse for the tag [film]
function FilmGetter_parse_film($content)
{
	$search = "/\[film\](.*?)\[\/film\]/is";
	preg_match($search, $content, $filmid);
	$film = FilmGetter_show_film($filmid[1]);
	$changedContent = preg_replace($search, "$film", $content);

	return $changedContent;
}

//fix parse for the tag [imdb]
function FilmGetter_parse_imdb($content)
{
	$search = "/\[imdb\](.*?)\[\/imdb\]/is";
	preg_match($search, $content, $filmid);
	$film = FilmGetter_show_imdb($filmid[1]);
	$changedContent = preg_replace($search, "$film", $content);

	return $changedContent;
}
add_filter('the_content', 'FilmGetter_parse_film', 2);
add_filter('the_content', 'FilmGetter_parse_imdb', 2);


//handle the whole admin section.
function FilmGetter_menu()
{
	  	global $wpdb;

if (isset($_POST['update_FilmGetterSettings'])) {
                        if (isset($_POST['FilmGetterTMDbAPI'])) {
                            update_option('TMDbApi', $_POST['FilmGetterTMDbAPI']);
                        }   
                        ?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "FilmGetter");?></strong></p></div>
<?
}
if (isset($_POST['add_movie'])) {
                        if (isset($_POST['movie_id'])) {
                            FilmGetter_add_film($_POST['movie_id']);
                        }   
                        ?>
<div class="updated"><p><strong><?php _e("Movie Added", "FilmGetter");?></strong></p></div>
<?php
}
?>
    <div class="wrap">
	<h2>FilmGetter</h2>
	<p>write [film] and then the movie name and then [/film] to show the info on your pages and articles, getting on this 	database.</p>
	
	<p>Sadly, you need to add the Movie manuelly in this version to get the info about the movie.</p>
	
	
	<h3>General Settings</h3>
	<form method="post">
	<label for="FilmGetterTMDbAPI">TMDb API:</label><input name="FilmGetterTMDbAPI" value="<?php echo get_option("TMDbApi", ""); ?>" />
	<p>You get the API on <a href="http://api.themoviedb.org/2.1">http://api.themoviedb.org/2.1</a>.</p>
	<div class="submit">
	<input type="submit" name="update_FilmGetterSettings" value="<?php _e('Update Settings', 'FilmGetter'); ?>" /></div>
	</form>

	<h3>Show all movies in the database</h3>
	<?php
	$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."FilmGetter");
	foreach($result as $resulti)
	{
    	echo "<div style='display:block;clear:both;margin-top:5px;'><img src='".$resulti->movie_pic."' style='float:left;width:80px;height:100px;margin-right:5px;' /><strong>".$resulti->movie_name."</strong><br />".$resulti->movie_release." - ".$resulti->movie_rate."<br />".$resulti->movie_plot."<br /><a href='".$resulti->movie_trailer."'>Trailer</a> - <a href='".$resulti->movie_url."'>TMDb</a> - <a href='".$resulti->movie_imdb."'>IMDb</a></div>";
    	echo '<div style="width:100%;display:block;clear:both;height:5px;"></div>';
	}
	?>
	<div style="display:block;width:100%;clear:both;"></div>
	<h3>Add Movie</h3>
	<form method="post">
	<label for="movie_id">TMDb ID or IMDB ID:</label><input name="movie_id" />
	<div class="submit">
	<input type="submit" name="add_movie" value="<?php _e('Add Movie', 'FilmGetter') ?>" /></div>
	</form>
	</div>
	<?
}

//adds the admin page
function FilmGetter_admin_actions()
{
	add_options_page('FilmGetter', 'FilmGetter', 1, 'FilmGetter', 'FilmGetter_menu');
}
 
add_action('admin_menu', 'FilmGetter_admin_actions');




/*
function FilmGetter_meta_box() {
echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="myplugin_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'My Post Section Title', 'myplugin_textdomain' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

    // Use nonce for verification

  echo '<input type="hidden" name="myplugin_noncename" id="myplugin_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry

  echo '<label for="myplugin_new_field">' . __("Description for this field", 'myplugin_textdomain' ) . '</label> ';
  echo '<input type="text" name="myplugin_new_field" value="whatever" size="25" />';

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}
function FilmGetter_add_meta_box() {
	add_meta_box('FilmGetter_post_form', __('FilmGetter', 'FilmGetter'), 'FilmGetter_meta_box', 'post', 'side');
}
add_action('admin_init', 'FilmGetter_add_meta_box');
*/


function simpleXMLToArray($xml,
                    $flattenValues=true,
                    $flattenAttributes = true,
                    $flattenChildren=true,
                    $valueKey='@value',
                    $attributesKey='@attributes',
                    $childrenKey='@children'){

        $return = array();
        if(!($xml instanceof SimpleXMLElement)){return $return;}
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if(strlen($_value)==0){$_value = null;};

        if($_value!==null){
            if(!$flattenValues){$return[$valueKey] = $_value;}
            else{$return = $_value;}
        }

        $children = array();
        $first = true;
        foreach($xml->children() as $elementName => $child){
            $value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
            if(isset($children[$elementName])){
                if($first){
                    $temp = $children[$elementName];
                    unset($children[$elementName]);
                    $children[$elementName][] = $temp;
                    $first=false;
                }
                $children[$elementName][] = $value;
            }
            else{
                $children[$elementName] = $value;
            }
        }
        if(count($children)>0){
            if(!$flattenChildren){$return[$childrenKey] = $children;}
            else{$return = array_merge($return,$children);}
        }

        $attributes = array();
        foreach($xml->attributes() as $name=>$value){
            $attributes[$name] = trim($value);
        }
        if(count($attributes)>0){
            if(!$flattenAttributes){$return[$attributesKey] = $attributes;}
            else{$return = array_merge($return, $attributes);}
        }
       
        return $return;
    }
?>