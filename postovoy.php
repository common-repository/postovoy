<?php
/*
Plugin Name: Постовой
Version: 1.2
Plugin URI: http://style4web.ru/wp-plagin/postovoy/
Description: Плагин для простого добовления постовых. Не забутьте настроить плагин в разделе <a href="options-general.php?page=dr_postovoy/postovoy.php">настройки</a>.
Author: DrNemo
Author URI: http://style4web.ru/
*/
/*  Copyright 2008-2009  DrNemo  (email: drnemo@bk.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$version = '1.2';//версия
$gLang=get_option('dr_postovoy_lang');//язык
//дефаултовое положение постового
add_option('dr_postovoy_position', 'buttom');
//дефаултовое значения для стиля
add_option('dr_postovoy_class', '');
//дефаултовое значения для вывода на главную
add_option('dr_postovoy_check', true);
//дефаултовое значения высоты поля
add_option('dr_postovoy_height',200);
//Язык
add_option('dr_postovoy_lang','ru');

//выводим постовой в запись
add_action('the_content', 'startPostovoy');
//сохраняем постовой 
add_action('wp_insert_post', 'postovoySave');
//выводим поле постового
add_action('admin_menu', 'postovoyMetaBox');
//добовляем ссылку в настройки
add_action('admin_menu', 'postovoyAddMenu');
//вывод постового
function startPostovoy($text=''){
	global $post, $more;
	$post_Check=get_option('dr_postovoy_check');
	if(!$post_Check){
		if($more) return postovoy($text);
		else return $text;
	}else return postovoy($text);	
}
function postovoy($text = ''){
	global $post;
	$post_Class=get_option('dr_postovoy_class');
	$post_Position=get_option('dr_postovoy_position');
	$postov=get_post_meta($post->ID,'postovoy',true);
	if($postov!=''){	
	    if($post_Class!='')$postovoy = "\n"."<div class='$post_Class'>";
		else $postovoy = "\n"."<div>";
    	$postovoy.= nl2br(get_post_meta($post->ID,'postovoy',true));
	    $postovoy.= "</div>\n ";
		if($post_Position=='top') return $postovoy.$text;
	    else  return $text.$postovoy;
	}else{
		return $text;
	}
}

function postovoySave($pID) {
	if (isset($_POST['PostPostovoy']))add_post_meta($pID,'postovoy',$_POST['PostPostovoy'], true) or update_post_meta($pID, 'postovoy', $_POST['PostPostovoy']);
}
//Поиск обновлений
function drVersion(){
    $fp = fsockopen ("www.style4web.ru", 80);
    $headers = "GET /file/wp-plagin/postovoy/postovoy.txt HTTP/1.1\r\n";
    $headers .= "Host: www.style4web.ru\r\n";
    $headers .= "Connection: Close\r\n\r\n";
    fwrite ($fp, $headers);$str = '';
    while (!feof ($fp)){$str .= fgets($fp, 1024);}
    fclose($fp);$version = 'неизвестна';
    if(strpos($str,'postovoy:')!= FALSE){$version = substr($str,strpos($str, 'postovoy:')+9);}
    return $version;
}
function postovoyMetaBox() {
	if(function_exists('add_meta_box')){
		add_meta_box('postovoy','Постовой','postovoyMeta','post');
		add_meta_box('postovoy','Постовой','postovoyMeta','page');
	}
}
//Вывод в редакторе
function postovoyMeta(){
	global $post;
	$gLang=get_option('dr_postovoy_lang');
	$dir=substr(__FILE__,0,strpos(__FILE__,'postovoy.php')).'lang';
	@include "lang/$gLang.php";
	$postovoy=get_post_meta($post->ID,'postovoy',true);
	?><textarea name="PostPostovoy" style="height:<?=get_option('dr_postovoy_height')?>px" tabindex="6" id="excerpt"><?=$postovoy?></textarea><br /><?=$LANG[12]?><?
}
//Страница настроек
function postovoyOptionsPage(){
    global $version,$gLang;
	if (isset($_POST['update_options'])) {
		update_option('dr_postovoy_class', $_POST['classDiv']);
		update_option('dr_postovoy_position', $_POST['positionDiv']);
		update_option('dr_postovoy_height', $_POST['heightDiv']);
		update_option('dr_postovoy_lang', $_POST['langDiv']);
		if(isset($_POST['checkDiv'])) update_option('dr_postovoy_check', true);
		else update_option('dr_postovoy_check', false);
	}
	$gLang=get_option('dr_postovoy_lang');
	$dir=substr(__FILE__,0,strpos(__FILE__,'postovoy.php')).'lang';
	@include "lang/$gLang.php";
	if(isset($_POST['update_options'])) echo '<div id="message" class="updated fade"><p><strong>'.$LANG[11].'</strong></p></div>';
	$post_Class=get_option('dr_postovoy_class');
	$post_Position=get_option('dr_postovoy_position');
	$post_Check=get_option('dr_postovoy_check');
	$post_Height=get_option('dr_postovoy_height');
	$post_Lang=get_option('dr_postovoy_lang');
	$newVersion = drVersion();
    //Список языков
	$d = dir($dir);
	while (false !== ($entry = $d->read())){
		if($entry!='..' and $entry!='.'){$k=substr($entry,0,strpos($entry,'.php'));$lang[$k]=$k;}
	}
	$d->close();
	?>
	<div class="wrap">
    <h2><?=$LANG[1]?></h2>
    <p><?if ($version == $newVersion) echo str_replace('{version}',$version,$LANG[7][1]);
	else if($version > $newVersion){?>Да ты маргинал, батенька) твоя версия <strong><?=$version?></strong>!<?}else{  
	$print=str_replace('{version}',$version,$LANG[7][2]);$print=str_replace('{new_version}',$newVersion,$print);echo $print;}?></p>
    <form method="post"><fieldset class="options">
    	<?if($post_Position=='top') $list='<option value="top" selected="selected">'.$LANG[8][1].'</option><option value="buttom">'.$LANG[8][2].'</option>';
		else $list='<option value="top">'.$LANG[8][1].'</option><option value="buttom" selected="selected">'.$LANG[8][2].'</option>';?>
        <table cellpadding="2" cellspacing="0" width="100%"><tr>
       		<th width="300" align="left"><label for="classDivPostovoy"><?=$LANG[2]?></label></th>
        	<td><input name="classDiv" type="text" id="classDivPostovoy" value="<?=$post_Class?>" class="regular-text code" /></td>
       	</tr><tr>
        	<th align="left"><label for="positionDivPostovoy"><?=$LANG[3]?></label></th>
            <td><select name="positionDiv" id="positionDivPostovoy"><?=$list?></select></td>
      	</tr><tr>
            <th align="left"><label for="checkDivPostovoy"><?=$LANG[4]?></label></th>
            <td><input type="checkbox" value="Yes" <?if($post_Check)echo 'checked';?> name="checkDiv" id="checkDivPostovoy" style="vertical-align:middle"> <?=$LANG[9]?></td>
      	</tr><tr>
            <th align="left"><label for="heighDivPostovoy"><?=$LANG[5]?></label></th>
            <td><input type="text" value="<?=$post_Height?>" class="regular-text code" id="heighDivPostovoy" name="heightDiv"</td>
      	</tr><tr>
           	<th align="left"><label for="langDivPostovoy"><?=$LANG[6]?></label></th>
            <td><select name="langDiv" id="langDivPostovoy"><?=select_list($lang,$post_Lang)?></select></td>
        </tr></table>
        <p class="submit"><input type="submit" class="button-primary" name="update_options" value="<?=$LANG[10]?> &raquo;" /></p>
   	</fieldset></form></div><?php
}
//добовление ссылки
function postovoyAddMenu() {
	global $version;
	add_options_page('Postovoy', "Постовой $version", 8, __FILE__, 'postovoyOptionsPage');
}
//Вывод списков
function select_list($mas,$id=false){
    if(count($mas)!=0){
        foreach($mas as $index => $val){
            if(!$id)$list.='<option value="'.$index.'">'.$val.'</option>';
            else{
                if($index==$id) $list.='<option value="'.$index.'" selected="selected">'.$val.'</option>';
                else $list.='<option value="'.$index.'">'.$val.'</option>';
            }
        }
    }return $list;
}
?>