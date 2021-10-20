<?php
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

$sm['admin_ajax'] = true;
switch ($_POST['action']) {

	case 'updatePricing':
		$id = secureEncode($_POST['id']);
		$type = secureEncode($_POST['type']);
		$col = secureEncode($_POST['col']);
		$val = secureEncode($_POST['val']);

		if($type == 'credits'){
			$mysqli->query("UPDATE config_credits SET $col = '".$val."' where id = '".$id."'");	
		}
		if($type == 'premium'){
			$mysqli->query("UPDATE config_premium SET $col = '".$val."' where id = '".$id."'");		
		}
		if($type == 'feature'){
			$mysqli->query("UPDATE config_prices SET price = '".$val."' where feature = '".$id."'");		
		}		
	break;

	case 'updateModeratorPermission':
		$id = secureEncode($_POST['id']);
		$setting = secureEncode($_POST['setting']);
		$val = secureEncode($_POST['val']);
		$mysqli->query("INSERT INTO moderators_permission (id,setting,setting_val) 
		VALUES ('".$id."','".$setting."','".$val."') ON DUPLICATE KEY UPDATE setting_val = '".$val."'");
			
	break;

	case 'updateDataProfile':
		$uid = secureEncode($_POST['uid']);
		$method = secureEncode($_POST['method']);
		$col = secureEncode($_POST['col']);
		$custom = secureEncode($_POST['custom']);
		$val = secureEncode($_POST['val']);
		$time = time();	

		if($method == 'addPremium'){
			$premiumDays = $val;
			$val = 1;
			$extra = 86400 * $premiumDays;
			$premium = $time + $extra;
			$mysqli->query("UPDATE users_premium set premium = '".$premium."' where uid = '".$uid."' ");	
		}

		$extraValues = '';
		if($method == 'setAdministrator'){
			$extraValues = ',moderator = "'.$custom.'"';
		}

		if($method == 'setFakeUser'){
			$mysqli->query("UPDATE users_photos SET $col = '".$val."' WHERE u_id = '".$uid."'");
		}

		if($method == 'addToSpotlight'){
			getUserInfo($uid,1);
			$lat = $sm['profile']['lat'];
			$lng = $sm['profile']['lng'];
			$photo = $sm['profile']['profile_photo'];
			$lang = $sm['profile']['lang'];	

			$query = "INSERT INTO spotlight (u_id,time,lat,lng,photo,lang,country)
			 VALUES ('".$uid."', '".$time."', '".$lat."', '".$lng."', '".$photo."', '".$lang."', '".$sm['profile']['country']."') ON DUPLICATE KEY UPDATE time = '".$time."'";
			$mysqli->query($query);			
			break;
		}

		$mysqli->query("UPDATE users SET $col = '".$val."' $extraValues WHERE id = '".$uid."'");		
	break;		

	case 'updateAccounts':
		$id = secureEncode($_POST['id']);
		$type = secureEncode($_POST['type']);
		$col = secureEncode($_POST['col']);
		$val = secureEncode($_POST['val']);
		$mysqli->query("UPDATE config_accounts SET $col = '".$val."' where type = '".$id."'");		
	break;	
	
	case 'manageGift':
		$id = secureEncode($_POST['id']);
		$icon = secureEncode($_POST['icon']);
		$name = secureEncode($_POST['name']);
		$price = secureEncode($_POST['price']);

		if($id > 0){
			$mysqli->query('UPDATE gifts SET gift = "'.$name.'", price = "'.$price.'", icon = "'.$icon.'" 
				WHERE id = "'.$id.'" ');
		} else {
			$mysqli->query('INSERT INTO gifts (gift,price,icon) VALUES ("'.$name.'","'.$price.'","'.$icon.'")');
		}
	    $arr = array();
	    $arr['gifts'] = getGiftsAdmin();
		echo json_encode($arr); 		
	break;

	case 'addLanguage':
		$name = secureEncode($_POST['name']);
		$prefix = secureEncode($_POST['prefix']);
		$name = ucfirst($name);
		$prefix = strtolower($prefix);

		$query = 'INSERT INTO languages (name,prefix) VALUES ("'.$name.'","'.$prefix.'")';
  		if ($mysqli->query($query) === TRUE) {
  			$last_id = $mysqli->insert_id;

  			$langTables = ['site_lang','app_lang','email_lang','landing_lang','seo_lang','config_genders','config_profile_questions','config_profile_answers'];
  			foreach ($langTables as $table) {
	  			$english = getArray($table,'where lang_id = 1','id desc');
				foreach ($english as $lang) {
					if($table == 'config_genders'){
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,name,sex) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['name'].'","'.$lang['sex'].'")');
					} else if($table == 'config_profile_questions'){
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,question,method,q_order) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['question'].'","'.$lang['method'].'","'.$lang['q_order'].'")');
					} else if($table == 'config_profile_answers'){
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,answer,qid) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['answer'].'","'.$lang['qid'].'")');
					} else if($table == 'seo_lang'){
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,text,page) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['text'].'","'.$lang['page'].'")');
					} else if($table == 'landing_lang'){
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,text,theme,preset) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['text'].'","'.$lang['theme'].'","'.$lang['preset'].'")');
					} else {
						$mysqli->query('INSERT INTO '.$table.' (id,lang_id,text) 
						VALUES ('.$lang['id'].','.$last_id.',"'.$lang['text'].'")');
					}
				} 
  			} 			
  		}			
	    $arr = array();
		echo json_encode($arr); 		
	break;	

	case 'deleteLanguage':
		$id = secureEncode($_POST['id']);
		$mysqli->query('DELETE FROM languages WHERE id = "'.$id.'"');
		$mysqli->query('DELETE FROM site_lang WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM app_lang WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM email_lang WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM config_profile_answers WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM config_genders WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM config_profile_questions WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM seo_lang WHERE lang_id = "'.$id.'"');
		$mysqli->query('DELETE FROM landing_lang WHERE lang_id = "'.$id.'"');		
		$mysqli->query('UPDATE users set lang = 1 WHERE lang = "'.$id.'"');
	    $arr = array();
		echo json_encode($arr); 		
	break;

	case 'removeFromSpotlight':
		$time = secureEncode($_POST['time']);
		$mysqli->query('DELETE FROM spotlight WHERE time = "'.$time.'"');
	    $arr = array();
		echo json_encode($arr); 		
	break;	

	case 'updateOnlineDay':
		$uid = secureEncode($_POST['uid']);
		$mon = secureEncode($_POST['monday']);
		$tue = secureEncode($_POST['tuesday']);
		$wed = secureEncode($_POST['wednesday']);
		$thu = secureEncode($_POST['thursday']);
		$fri = secureEncode($_POST['friday']);
		$sat = secureEncode($_POST['saturday']);
		$sun = secureEncode($_POST['sunday']);

		$mysqli->query('INSERT INTO users_online_day (uid,mon,tue,wed,thu,fri,sat,sun) VALUES
		("'.$uid.'","'.$mon.'","'.$tue.'","'.$wed.'","'.$thu.'","'.$fri.'","'.$sat.'","'.$sun.'")
		ON DUPLICATE KEY UPDATE mon = "'.$mon.'",tue = "'.$tue.'",wed = "'.$wed.'",thu = "'.$thu.'",fri = "'.$fri.'",sat = "'.$sat.'", sun ="'.$sun.'"');

		$today = date('w');
		$today = 'day'.$today;
		cronUpdateOnlineDay($today);
		
	    $arr = array();
		echo json_encode($arr); 	
	break;

	case 'updateOnlineDayCron':
		$today = date('w');
		$today = 'day'.$today;
		cronUpdateOnlineDay($today);
		
	    $arr = array();
		echo json_encode($arr); 	
	break;

	case 'removeGift':
		$id = secureEncode($_POST['id']);
		$mysqli->query('DELETE FROM gifts WHERE id = "'.$id.'" ');
	    $arr = array();
	    $arr['gifts'] = getGiftsAdmin();
		echo json_encode($arr); 		
	break;

	case 'deleteChatMessage':
		$id = secureEncode($_POST['id']);
		$mysqli->query('DELETE FROM chat WHERE id = "'.$id.'" ');
		echo json_encode($arr); 		
	break;	

	case 'deleteVideocall':
		$videocall = secureEncode($_POST['videocall']);
		

		$video1 = getData('videocall','c_id_video','where call_date = "'.$videocall.'"');
		if($video1 != 'noData'){
			$video1 = str_replace($sm['config']['site_url'], '../', $video1);
			unlink($video1);
		}

		$video2 = getData('videocall','r_id_video','where call_date = "'.$videocall.'"');
		if($video2 != 'noData'){
			$video2 = str_replace($sm['config']['site_url'], '../', $video2);
			unlink($video2);
		}

		error_log($video2);
		error_log($video1);

		$mysqli->query('DELETE FROM videocall WHERE call_date = "'.$videocall.'" ');
	    $arr = array();
		echo json_encode($arr); 		
	break;

	case 'deleteLive':
		$live = secureEncode($_POST['live']);
		
		$mysqli->query('DELETE FROM live WHERE id = "'.$live.'" ');
	    $arr = array();
		echo json_encode($arr); 		
	break;			

	case 'manageInterest':
		$id = secureEncode($_POST['id']);
		$icon = secureEncode($_POST['icon']);
		$name = secureEncode($_POST['name']);

		if($id > 0){
			$mysqli->query('UPDATE interest SET name = "'.$name.'", icon = "'.$icon.'" 
				WHERE id = "'.$id.'" ');
		} else {
			$mysqli->query('INSERT INTO interest (name,icon) VALUES ("'.$name.'","'.$icon.'")');
		}
	    $arr = array();
	    $arr['interest'] = getInterestsAdmin();
		echo json_encode($arr); 		
	break;

	case 'removeInterest':
		$id = secureEncode($_POST['id']);
		$mysqli->query('DELETE FROM interest WHERE id = "'.$id.'" ');
	    $arr = array();
	    $arr['interest'] = getInterestsAdmin();
		echo json_encode($arr); 		
	break;		

	case 'updatePreset':
		$theme = secureEncode($_POST['theme']);
		$preset = secureEncode($_POST['preset']);
		$type = secureEncode($_POST['themeType']);
	    if($type == 'Desktop'){
	      $mysqli->query("UPDATE settings SET setting_val = '".$preset."' where setting = 'desktopThemePreset'");
	    }
	    if($type == 'Landing'){
	      $mysqli->query("UPDATE settings SET setting_val = '".$preset."' where setting = 'landingThemePreset'");
	      $mysqli->query("UPDATE settings SET setting_val = '".$theme."' where setting = 'landingTheme'");
	    }	    		
	break;
	case 'removeModerator':
		$mod = secureEncode($_POST['val']);
	    $mysqli->query("DELETE FROM moderators where id = '".$mod."'");	
	    $mysqli->query("DELETE FROM moderators_permission where id = '".$mod."'");	
	    
	break;	
	case 'editCurrentPreset':
		$preset = secureEncode($_POST['preset']);
		$action = secureEncode($_POST['editAction']);
		$val = secureEncode($_POST['val']);
		$time = time();
	    if($action == 'rename'){
	      $mysqli->query("UPDATE theme_preset SET preset_alias = '".$val."',theme_modification = '".$time."' where preset = '".$preset."'");
	    }
	    $arr = array();

	    if($action == 'duplicate'){
	      $alias = $val.' Clone';
	      $newPreset = $preset.'-'.rand(0,100);
	      $presetFilter = 'WHERE preset = "'.$preset.'"';
	      $base = getData('theme_preset','preset_base',$presetFilter);
	      $data = getData('theme_preset','theme_settings',$presetFilter);
	      $theme = getData('theme_preset','theme',$presetFilter);
	      $landing = getData('theme_preset','landing',$presetFilter);
		  $mysqli->query("INSERT INTO theme_preset (preset,preset_alias,preset_base,theme,theme_settings,author,theme_modification,landing)
		    	VALUES ('".$newPreset."','".$alias."','".$base."','".$theme."','".$data."','".$sm['user']['name']."','".time()."','".$landing."')");

		  if($landing == 1){
		  	$arr['reload'] = 'Landing';
		  } else {
		  	$arr['reload'] = 'Desktop';
		  }
		  
		  $fonts = getArray('theme_preset_fonts',$presetFilter,'font DESC');
		  foreach ($fonts as $font) {
			$mysqli->query("INSERT INTO theme_preset_fonts (preset,font,setting) VALUES ('".$newPreset."','".$font['font']."','".$font['setting']."')");		  	
		  }

		  if($landing == 1){
			  $lang = getArray('landing_lang','WHERE theme = "'.$theme.'"','id ASC');
			  foreach ($lang as $l) {
				$mysqli->query("INSERT INTO landing_lang (id,lang_id,preset,theme,text) VALUES ('".$l['id']."','".$l['lang_id']."','".$newPreset."','".$theme."','".$l['text']."')");		  	
			  }		  	
		  }

		  echo json_encode($arr);
	    }
	    if($action == 'delete'){
	    	$mysqli->query("DELETE FROM theme_preset where preset = '".$preset."'");
	    }    		
	break;	
	case 'addPreset':
		$theme = secureEncode($_POST['theme']);
		$preset = secureEncode($_POST['preset']);
		$base = secureEncode($_POST['base']);


		if(!empty($_POST['data'])){
			$data = $_POST['data'];
		} else {
			$data = getData('theme_preset','theme_settings','WHERE preset = "'.$theme.'"');	
		}
		
		$landing = getData('theme_preset','landing','WHERE preset = "'.$theme.'"');
		
		if($landing == 'noData'){
			$landing = 0;
		}

		$alias = secureEncode($_POST['alias']);
	    $mysqli->query("INSERT INTO theme_preset (preset,preset_alias,preset_base,theme,theme_settings,author,theme_modification,landing)
	    	VALUES ('".$preset."','".$alias."','".$base."','".$theme."','".$data."','".$sm['user']['name']."','".time()."','".$landing."')");
	    
		$fonts = getArray('theme_preset_fonts','WHERE preset = "'.$theme.'"','font DESC');
		foreach ($fonts as $font) {
			$mysqli->query("INSERT INTO theme_preset_fonts (preset,font,setting) VALUES ('".$preset."','".$font['font']."','".$font['setting']."')");		  	
		}

		if($landing == 1){
		  $lang = getArray('landing_lang','WHERE theme = "'.$theme.'"','id ASC');
		  foreach ($lang as $l) {
			$mysqli->query("INSERT INTO landing_lang (id,lang_id,preset,theme,text) VALUES ('".$l['id']."','".$l['lang_id']."','".$preset."','".$theme."','".$l['text']."')");		  	
		  }		  	
		}

		echo $landing;
    		
	break;

	case 'exportJSON':
		$preset = secureEncode($_POST['preset']);
		$themeFilter = 'preset = "'.$preset.'"';
		$fileName = secureEncode($_POST['name']);
		$fileName = $fileName.time();
		$data = array();
		$arr = array();
		$data = getDataArray('theme_preset',$themeFilter);
		$fonts = getArray('theme_preset_fonts','WHERE '.$themeFilter,'font DESC');
		$data['fonts'] = $fonts;
		$data = json_encode($data);
		file_put_contents('../assets/sources/presets/'.$fileName.'.json', $data);

		$arr['url'] = $sm['config']['site_url'].'assets/sources/presets/'.$fileName.'.json';
		$arr['name'] = $fileName;
		echo json_encode($arr);		   		
	break;

	case 'exportJSONLanguage':
		$id = secureEncode($_POST['id']);
		$name = secureEncode($_POST['name']);
		$prefix = secureEncode($_POST['prefix']);
		$langFilter = 'WHERE lang_id = '.$id;
		$langFilterLanding = 'WHERE lang_id = '.$id.' AND preset = "'.$sm['settings']['landingThemePreset'].'"';
		$order = 'id ASC';
		$fileName = secureEncode($name);
		$fileName = $fileName.time();
		$data = array();
		$arr = array();

		$data['name'] = $name;
		$data['prefix'] = $prefix;
		$data['site_lang'] = getArray('site_lang',$langFilter,$order);
		$data['app_lang'] = getArray('app_lang',$langFilter,$order);
		$data['email_lang'] = getArray('email_lang',$langFilter,$order);
		$data['seo_lang'] = getArray('seo_lang',$langFilter,$order);
		
		$data['questions_lang'] = getArray('config_profile_questions',$langFilter,$order);
		$data['answer_lang'] = getArray('config_profile_answers',$langFilter,$order);
		$data['gender_lang'] = getArray('config_genders',$langFilter,$order);

		$data['landing_lang'] = getArray('landing_lang',$langFilterLanding,$order);

		$data = json_encode($data,JSON_UNESCAPED_UNICODE);
		file_put_contents('../assets/sources/presets/'.$fileName.'.json', $data);

		$arr['url'] = $sm['config']['site_url'].'assets/sources/presets/'.$fileName.'.json';
		$arr['name'] = $fileName;
		echo json_encode($arr);		   		
	break;


	case 'updateTheme':
		$theme = secureEncode($_POST['theme']);
		$setting = secureEncode($_POST['setting']);
		$type = secureEncode($_POST['type']);
		$preset = secureEncode($_POST['preset']);
		$val = secureEncode($_POST['val']);
		$time = time();

		$themeFilter = 'WHERE theme = "'.$theme.'" AND preset = "'.$preset.'"';
		$sm['preset'] = json_decode(getData('theme_preset','theme_settings',$themeFilter),true);

		$themeSettingsVal = $val;
		if (strpos($val, ':') !== false && $type == 'font') {
			$valArr = explode(':', $val);
			$themeSettingsVal = $valArr[0];
		} 
		$mysqli->query("UPDATE theme_settings SET setting_val = '".$themeSettingsVal."' where theme = '".$theme."' and setting = '".$setting."'");

		if(isset($_POST['gradient'])){
			$gradient = secureEncode($_POST['gradient']);
			if (strpos($val, 'gradient') !== false) {
			    $mysqli->query("UPDATE theme_settings SET setting_val = 'Yes' where theme = '".$theme."' and setting = '".$gradient."'");
				$sm['preset'][$gradient]['val'] = 'Yes';			    
			} else {
				$mysqli->query("UPDATE theme_settings SET setting_val = 'No' where theme = '".$theme."' and setting = '".$gradient."'");
				$sm['preset'][$gradient]['val'] = 'No';			
			}
		}

		if($val == 'Left-Menu'){
			$wide = secureEncode($_POST['wide']);
			$mysqli->query("UPDATE theme_settings SET setting_val = 'No' where theme = '".$theme."' and setting = 'design_style_wide'");
			$sm['preset']['design_style_wide']['val'] = 'No';
		}

		if($val == 'Top-Menu'){
			$wide = secureEncode($_POST['wide']);
			$mysqli->query("UPDATE theme_settings SET setting_val = 'Yes' where theme = '".$theme."' and setting = 'design_style_wide'");
			$sm['preset']['design_style_wide']['val'] = 'Yes';
		}		


		//update preset
		$sm['preset'][$setting]['val'] = $themeSettingsVal;
		$preset_val = json_encode($sm['preset']);

		
		$themeTablesFilter = 'WHERE theme = "'.$theme.'"';
		$themeTables = getSelectedArray('setting,setting_val','theme_settings',$themeTablesFilter,'setting');
		foreach ($themeTables as $check) {
			if(!array_key_exists($check['setting'],$sm['preset'])){
				$sm['preset'][$check['setting']]['val'] = $check['setting_val'];
			}
		}
		$preset_val = json_encode($sm['preset']);

		$mysqli->query("INSERT INTO theme_preset (preset,theme,theme_settings,author) VALUES ('".$preset."','".$theme."','".$preset_val."','".$sm['user']['id']."') ON DUPLICATE KEY UPDATE theme_settings = '".$preset_val."',theme_modification = '".$time."'");

		if($type == 'font'){
			$mysqli->query("DELETE FROM theme_preset_fonts WHERE preset = '".$preset."' AND setting = '".$setting."'");	
			$mysqli->query("INSERT INTO theme_preset_fonts (preset,font,setting) VALUES ('".$preset."','".$val."','".$setting."')");
		}

	break;	
	case 'changePage':	
		$page = secureEncode($_POST['page']);
		$plugin = secureEncode($_POST['plugin']);
		$category = secureEncode($_POST['category']);
		if(!empty($plugin)){
			$_GET['plugin'] = $plugin;
			$_GET['category'] = $category;
		}
		if(!empty($category)){
			$_GET['category'] = $category;
		}
		if($page == 'themes'){
			$_GET['type'] = $plugin;
		}
		if($page == 'editLanguage'){
			$filter = 'id = '.$plugin;
			$sm['editLang'] = getDataArray('languages',$filter);
			$sm['editLang']['prefix'] = getData('languages','prefix','WHERE id = '.$plugin);
			if($category == 'site_lang'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Website';
			}
			if($category == 'app_lang'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Mobile';
			}
			if($category == 'email_lang'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Email';
			}
			if($category == 'gender'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Gender';
			}
			if($category == 'questions'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Profile Questions';
			}
			if($category == 'landing_lang'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Landing '.$sm['settings']['landingTheme'];
			}	
			if($category == 'seo_lang'){
				$sm['editLang']['table'] = $category;
				$sm['editLang']['title'] = 'Seo Pages';
			}																		
			
		}						
		$sm['content'] = requestAdministratorPage($page);
		echo $sm['content'];		
	break;

	case 'changePagePlugin':	
		$sm['content'] = requestAdministratorPage($page);
		echo $sm['content'];		
	break;		

	case 'getCitiesByCountry':
		$arr=array();	
		$time = time()-300;
		$country = secureEncode($_POST['country']);
		$cities = getArrayDSelected('city','users','where country ="'.$country.'"');
		$i=0;
		foreach ($cities as $val) { 
			$arr[$i]['city'] = $val['city'];
			$i++;
		}
		echo json_encode($arr);
	break;

	case 'search_users':
		$data = array();
		$arr=array();
		$time = time()-300;

		$gender = secureEncode($_POST['gender']);
		$age1 = secureEncode($_POST['age1']);
		$age2 = secureEncode($_POST['age2']);
		$order = secureEncode($_POST['order']);
		$date = secureEncode($_POST['date']);
		$dateEnabled = secureEncode($_POST['dateEnabled']);
		$fake = secureEncode($_POST['fake']);
		$real = secureEncode($_POST['realUser']);
		$premium = secureEncode($_POST['premium']);
		$online = secureEncode($_POST['online']);
		$searchInput = secureEncode($_POST['search']);
		$verified = secureEncode($_POST['verified']);
		$withStory = secureEncode($_POST['withStory']);

		$country = secureEncode($_POST['country']);
		$city = secureEncode($_POST['city']);
		$date = str_replace(' ', '', $date);
		$date = explode('to',$date);
		$date1 = $date[0];
		$date2 = $date[1];
		$filter = 'WHERE age BETWEEN '.$age1.' AND '.$age2;

		if($searchInput != ''){
			$filter.=" AND id = '".$searchInput."' OR name LIKE '%$searchInput%' OR email LIKE '%$searchInput%' OR ip LIKE '%$searchInput%'";
		}
		if($gender != 'all'){
			$filter.=' AND gender ='.$gender;
		}
		if($online == 'on'){
			$filter.=' AND last_access >='.$time;
		}
		if($premium == 'on'){
			$filter.=' AND premium = 1';
		}
		if($verified == 'on'){
			$filter.=' AND verified = 1';
		}		
		if($fake == 'on' || $real == 'on' || $fake == 'off' || $real == 'off'){
			if($fake == 'on' && $real == 'on'){
				$filter.=' AND fake >= 0';
			} else if($fake == 'off' && $real == 'on'){
				$filter.=' AND fake = 0';
			} else if($fake == 'on' && $real == 'off'){
				$filter.=' AND fake > 0';
			} else {
				$filter.=' AND fake = -1';
			}	
		}		
		if($dateEnabled == 'on'){
			$filter.=' AND join_date BETWEEN "'.$date1.'" AND "'.$date2.'"';
		}

		if($country != 'all'){
			$filter.=' AND country = "'.$country.'"';
		}

		if($city != 'all'){
			$filter.=' AND city = "'.$city.'"';
		}							

		if($sm['user']['admin'] == 2){
			$filter.=' AND admin = 0';
		}

		$search = getSelectedArray('id,name,email,age,city,country,fake,admin,last_access,credits,premium,ip,online_day,verified,moderator','users',$filter,$order.' desc','LIMIT 0,500');
		$i=0;
		if(isset($search)){
			foreach ($search as $value) { 
				
				$today = date('w');
				$onlineNow = '';
				$userPremium = 'No';
				$ip = $value['ip'];
				if($value['last_access'] >= $time || $value['fake'] == 1 && $value['online_day'] == $today){
					$onlineNow = 'avatar-online lg';
				}
				if($value['fake'] == 1){
					$badge = '<span class="badge badge-info">Fake user</span>';
					$ip = 'ip.fake.user';
				} else {
					$badge = '<span class="badge badge-success">User</span>';
				}

				if($value['admin'] == 1){
					$badge = '<span class="badge badge-warning">ADMIN</span>';
				}
				if($value['admin'] == 2){
					$badge = '<span class="badge badge-dark">'.$value['moderator'].'</span>';
				}				
				if($value['premium'] == 1){
					$userPremium = 'Yes';
				}

				$userVerified = '';
				if($value['verified'] == 1){
					$userVerified = '<i class="material-icons" style="color:#1CC5F7;font-size:13px">verified_user</i>';
				}

				$photo = "'".profilePhoto($value['id'])."'";
				
				$banEmail = "'".'email,'.$value['email'].','.$value['ip']."'";
				$banIP = "'".'ip,'.$value['ip']."'";	


				$storyFrom = $sm['plugins']['story']['days'];
				$time = time();	
				$extra = 86400 * $storyFrom;
				$storyFrom = $time - $extra;
				$storiesFilter = 'where uid = '.$value['id'].' and storyTime >'.$storyFrom.' and deleted = 0';

				$checkStory = selectC('users_story',$storiesFilter);
				$userStories = json_encode(getUserStories($value['name'],profilePhoto($value['id']),$storiesFilter,'storyTime ASC'),JSON_UNESCAPED_UNICODE);

				$onClickStories = '';
				$storyEl = "'.asd'";
				$storyBorder = '';
				if($checkStory > 0){
					$storyBorder = 'style="border:3px solid #7F17A9;cursor:pointer"';
					$onClickStories = 'onclick="openStoryDiscover('.$storyEl.','.$value['id'].',true);" ';
				}

				if($withStory == 'on'){
					if($checkStory == 0){
						continue;
					}
				}

				if($value['credits'] < 0){
					$value['credits'] = 0;
					$mysqli->query("UPDATE users set credits = 0 where id = '".$value['id']."'");
				}

				$goToEditMediaUser = "goTo('mediaPhotos','All',".$value['id'].")";
				$data[$i]='
			      <tr>
			          <td>
			              <div class="custom-control custom-checkbox">
			                  <input type="checkbox" onclick="checkUser(this,'.$value['id'].','.$photo.')" class="custom-control-input" data-check-user="'.$value['id'].'" data-check-user-photo="'.profilePhoto($value['id']).'" id="checkuser_'.$value['id'].'">
			                  <label class="custom-control-label" style="cursor: pointer;" for="checkuser_'.$value['id'].'">
			                  <span class="text-hide">Check</span></label>
			              </div>
			          </td>

			          <td style="max-width:230px;overflow-x:hidden">
			              <div class="media align-items-center" >
			                  <div class="avatar avatar-md mr-3 '.$onlineNow.'" '.$onClickStories.'  style="width:55px;height:55px">
			                      <img src="'.profilePhoto($value['id']).'" class="avatar-img rounded-circle avatar-online box-shadow" '.$storyBorder.'>
			                  </div>
			                  <div class="media-body">
			                      <strong class="js-lists-values-employee-name"><a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['id'].'"  style="color:#333">'.$value['name'].' ,'.$value['age'].' '.$userVerified.'</a></strong><br>
			                      <span class="text-muted js-lists-values-employee-title">
			                      ID: '.$value['id'].'</span><br>
			                      <span class="text-muted js-lists-values-employee-title">'.$value['email'].'</span>			                      
			                  </div>
			              </div>
			          </td>
			          <td style="min-width:150px;">
			              <div class="media align-items-center">
			                  <div class="media-body">
			                      <strong>'.$value['city'].'</strong>
			                      <br>
			                      <span class="text-muted">'.$value['country'].'</span>
			                  </div>
			              </div>
			          </td>                                                    
			          <td>'.$badge.'</td>
			          <td style="min-width:130px;"><small class="text-muted">'.time_elapsed_string($value['last_access']).'</small></td>
			          <td>'.$value['credits'].'</td>
			          <td><strong>'.$userPremium.'</strong></td>
			          <td><strong>'.$ip.'</strong></td>		                                
			          <td>
			              <div class="dropdown ml-auto" data-table-dropdown>
			                  <a href="#" data-toggle="dropdown" data-caret="false" class="btn btn-light text-muted"><i class="material-icons">more_vert</i></a>
			                  <div class="dropdown-menu dropdown-menu-right">
			                      <a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['id'].'">Open live profile</a>
			                      <div class="dropdown-divider"></div>
			                      <a class="dropdown-item" style="font-size: 13px" href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['id'].'" >Edit account</a>
			                      <a class="dropdown-item" style="font-size: 13px" href="javascript:;"
			                      onclick="'.$goToEditMediaUser.'">Edit media files</a>			                      
			                      <div class="dropdown-divider"></div>
			                      <a class="dropdown-item" href="#" style="font-size: 13px" 
			                      onclick="adminDeleteProfile('.$value['id'].')">Delete account</a>
			                      <div class="dropdown-divider"></div>
					              <a class="dropdown-item" href="#" 
					              onclick="adminDeleteProfile('.$value['id'].',0,'.$banEmail.')" style="font-size: 13px">
					                Delete user and ban email
					              </a>                          
					              <a class="dropdown-item" href="#" 
					              onclick="adminDeleteProfile('.$value['id'].',0,'.$banIP.')" style="font-size: 13px">
					                Delete user and ban IP
					              </a>			                      
			                  </div>
			              </div>
			          </td>
			      </tr>';

			      $i++;
			}
		} else {
			$data = 'Nothing found';
		}

		$arr['data'] = $data;
		$arr['total'] = $i;
		echo json_encode($arr);
	break;

	case 'search_banned':
    $data = array();
    $arr=array();
    $time = time()-300;

    $filter = '';
    $dropdown = '';
    $cAvatar = '';
    $dAvatar = '';
    $reportedCol = '';

    $type = secureEncode($_POST['type']);
    $col = 'moderator';
    if($type == 'users'){
    	$order = 'banned_date';
    	if(!empty($_POST['search'])){
    		$search = secureEncode($_POST['search']);
    		$filter.=" WHERE email LIKE '%$search%' ";
    	} else {
    		$filter.="";
    	}
    	$search = getArray('blocked_users',$filter,$order.' desc','');
    }

    if($type == 'ip'){
    	$order = 'banned_date';
    	if(!empty($_POST['search'])){
    		$search = secureEncode($_POST['search']);
    		$filter.=" WHERE ip LIKE '%$search%' ";
    	} else {
    		$filter.="";
    	}    	
    	$search = getArray('blocked_ips',$filter,$order.' desc','');
    }  

    if($type == 'reported'){
    	$order = 'reported_date';
    	$filter = 'WHERE viewed = 0';
    	$search = getArray('reports',$filter,$order.' desc','');
    }        
    
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 

      	if($type == 'users'){
      		$id = $value['id'];
      		$val = $value['email'];
      		$checkData = $value['id'];
      		$userId = $value['banned_by'];
      		$timeago = $value['banned_date'];
      		$adminUpdateDataCustomVal = "'unbanUser'";

			$dropdown = '<a class="dropdown-item" href="javascript:;" 
			onclick="adminUpdateData('.$id.',0,1,'.$adminUpdateDataCustomVal.')">Unban email</a>';
	    	$cAvatar = '
	    		<img src="'.profilePhoto($value['banned_by']).'" class="avatar-img rounded">
	    	'; 			     		
      	}

      	if($type == 'ip'){
      		$id = $value['ip'];
      		$val = $value['ip'];
      		$checkData = $value['ip'];
      		$userId = $value['banned_by'];
      		$timeago = $value['banned_date'];

      		$adminUpdateDataCustomVal = "'unbanIP'";
      		$adminUpdateDataIp = "'".$id."'"; 
			$dropdown = '<a class="dropdown-item" href="javascript:;" 
			onclick="adminUpdateData('.$adminUpdateDataIp.',0,1,'.$adminUpdateDataCustomVal.')">Unban IP</a>';
	    	$cAvatar = '
	    		<img src="'.profilePhoto($value['banned_by']).'" class="avatar-img rounded">
	    	'; 			      		
      	}

      	if($type == 'reported'){

      		$id = $i.'22900'.$value['reported'];
      		$userId = $value['reported_by'];
      		$col = 'city';
      		$checkData = $i.'22900'.$value['reported'];
      		$val = $value['reason'];
      		$timeago = $value['reported_date'];
			$banEmail = "'".'email,'.getData('users','email','where id ='.$value['reported']).','.getData('users','ip','where id ='.$value['reported'])."'";
			$banIP = "'".'ip,'.getData('users','ip','where id ='.$value['reported'])."'"; 

			$adminUpdateDataCustomVal = "'removeFromReportList'"; 

			$dropdown = '<a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['reported'].'">Open live profile</a>
			  <a class="dropdown-item" style="font-size: 13px" href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['reported'].'" target="_blank">Edit account</a>
			  <div class="dropdown-divider"></div>
			  <a class="dropdown-item" href="#" style="font-size: 13px" 
			  onclick="adminUpdateData('.$value['reported'].',0,1,'.$adminUpdateDataCustomVal.')">Remove from report list</a>			  
			  <div class="dropdown-divider"></div>
			  <a class="dropdown-item" href="#" style="font-size: 13px" 
			  onclick="adminDeleteProfile('.$value['reported'].')">Delete account</a>
			  <div class="dropdown-divider"></div>
			  <a class="dropdown-item" href="#" 
			  onclick="adminDeleteProfile('.$value['reported'].',0,'.$banEmail.')" style="font-size: 13px">
			    Delete user and ban email
			  </a>                          
			  <a class="dropdown-item" href="#" 
			  onclick="adminDeleteProfile('.$value['reported'].',0,'.$banIP.')" style="font-size: 13px">
			    Delete user and ban IP
			  </a>';

			$cAvatar = '
				<img src="'.profilePhoto($value['reported_by']).'" class="avatar-img rounded">
			'; 			  
			$dAvatar = '
				<img src="'.profilePhoto($value['reported']).'" class="avatar-img rounded">
			'; 		    	
		   $reportedCol = '
	        <td>
	            <div class="media align-items-center">
	                <div class="avatar avatar-sm mr-3 ">
	                    '.$dAvatar.'
	                </div>
	                <div class="media-body">
	                    <strong class="js-lists-values-employee-name">'.getData('users','name','where id ='.$value['reported']).'</strong><span class="badge badge-warning"></span><br>
	                    <span class="text-muted js-lists-values-employee-title"><small>'.getData('users','city','where id ='.$value['reported']).'</small></span>
	                </div>
	            </div>
	        </td> 
		   ';      		
		}

      	$uName = getData('users','name','where id ='.$userId);
      	$uMod = getData('users',$col,'where id ='.$userId);

        $data[$i]='
            <tr>
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" onclick="checkData(this,'.$checkData.')" class="custom-control-input" data-check-search="'.$checkData.'" id="checkcall_'.$id.'">
                        <label class="custom-control-label" style="cursor: pointer;" for="checkcall_'.$id.'">
                        <span class="text-hide">Check</span></label>
                    </div>
                </td>
                '.$reportedCol.'
                <td>'.$val.'</td>
                <td><small class="text-muted">'.time_elapsed_string($timeago).'</small></td>                
                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            '.$cAvatar.'
                        </div>
                        <div class="media-body">
                            <strong class="js-lists-values-employee-name">'.$uName.'</strong><span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title"><small>'.$uMod.'</small></span>
                        </div>
                    </div>
                </td>                                                                                              
                <td>
                    <div class="dropdown ml-auto">
                        <a href="#" data-toggle="dropdown" data-caret="false" class="btn btn-light text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            '.$dropdown.'
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;

	case 'search_withdrawals':
    $data = array();
    $arr=array();
    $time = time()-300;

    $filter = '';

	$search = getArray('users_withdraw',$filter,'status DESC, id DESC','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 

      	$id = $value['id'];
      	$userId = $value['u_id'];
 
    	$adminUpdateDataCustomVal1 = "'withdrawComplete'";
    	$adminUpdateDataCustomVal2 = "'withdrawCanceled'"; 
    	$uName = getData('users','name','where id ='.$userId);

    	if($value['status'] == 'Pending'){
			$dropdown = '
			  <a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['u_id'].'">Open live profile</a>
			  <a class="dropdown-item" style="font-size: 13px" href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['u_id'].'" target="_blank">Edit User</a>		  
			  <div class="dropdown-divider"></div>
			  <a class="dropdown-item" href="#" style="font-size: 13px" 
			  onclick="adminUpdateData('.$id.',0,1,'.$adminUpdateDataCustomVal1.')">
			  Withdrawal Complete</a>			  
			  <div class="dropdown-divider"></div>
			  <a class="dropdown-item" href="#" style="font-size: 13px" 
			  onclick="adminUpdateData('.$id.',0,1,'.$adminUpdateDataCustomVal2.')">
			  Cancel Withdrawal</a>'; 
    	} else {
			$dropdown = '
			  <a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['u_id'].'">Open live profile</a>
			  <a class="dropdown-item" style="font-size: 13px" href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['u_id'].'" target="_blank">Edit User</a>'; 
    	}
      	
      	$uEmail = getData('users','email','where id ='.$userId);
      	$uPaypal = getData('users','paypal','where id ='.$userId);

        $data[$i]='
            <tr class="data-search-verifications">
                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            <img src="'.profilePhoto($userId).'" class="avatar-img rounded">
                        </div>
                        <div class="media-body">
                            <strong class="js-lists-values-employee-name">'.$uName.'</strong><span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title">'.$uEmail.'</span>
                        </div>
                    </div>
                </td>
                <td>
                	'.$sm['plugins']['settings']['currency'].' '.$value['withdraw_amount'].'</small>
                </td>
                <td>
                	'.$uPaypal.'</small>
                </td>                
                <td>
                	'.$value['withdraw_date'].'
                </td>
                <td>
                	'.$value['status'].'
                </td>                    
                <td>
                    <div class="dropdown ml-auto">
                        <a href="#" data-toggle="dropdown" data-caret="false" class="btn btn-light text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            '.$dropdown.'
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;

	case 'search_verifications':
    $data = array();
    $arr=array();
    $time = time()-300;

    $filter = 'WHERE status = "No"';

	$search = getArray('users_verification',$filter,'time ASC','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 

      	$checkData = $value['time'];
      	$id = $value['time'];
      	$userId = $value['uid'];
      	$timeago = $value['time'];
    	$uploadedPhoto = '
    		<img src="'.$value['media'].'" class="avatar-img rounded">
    	';
    	$uploadedPhotoOnClick = "'".$value['media']."'"; 
    	$adminUpdateDataCustomVal1 = "'approveUserVerification'";
    	$adminUpdateDataCustomVal2 = "'noapproveUserVerification'"; 
    	$uName = getData('users','name','where id ='.$userId);

		$dropdown = '
		  <a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['uid'].'">Open live profile</a>
		  <div class="dropdown-divider"></div>
		  <a class="dropdown-item" href="#" style="font-size: 13px" 
		  onclick="adminUpdateData('.$userId.',0,1,'.$adminUpdateDataCustomVal1.')">
		  Approve '.$uName.'</a>			  
		  <div class="dropdown-divider"></div>
		  <a class="dropdown-item" href="#" style="font-size: 13px" 
		  onclick="adminUpdateData('.$userId.',0,1,'.$adminUpdateDataCustomVal2.')">
		  No Approve '.$uName.'</a>'; 

      	
      	$uEmail = getData('users','email','where id ='.$userId);

        $data[$i]='
            <tr class="data-search-verifications">
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" onclick="checkData(this,'.$checkData.')" class="custom-control-input" data-check-search="'.$checkData.'" id="checkcall_'.$id.'">
                        <label class="custom-control-label" style="cursor: pointer;" for="checkcall_'.$id.'">
                        <span class="text-hide">Check</span></label>
                    </div>
                </td>
                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            <img src="'.profilePhoto($userId).'" class="avatar-img rounded">
                        </div>
                        <div class="media-body">
                            <strong class="js-lists-values-employee-name">'.$uName.'</strong><span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title">'.$uEmail.'</span>
                        </div>
                    </div>
                </td>
                <td onclick="showImageVerification('.$uploadedPhotoOnClick.')" style="cursor:pointer">
	                <div class="avatar avatar-sm mr-3" style="margin:0 auto;cursor:pointer">
	                	'.$uploadedPhoto.'
	                </div>
                </td>
                <td>
                	<small class="text-muted">'.time_elapsed_string($timeago).'</small>
                </td>                                                                               
                <td>
                    <div class="dropdown ml-auto">
                        <a href="#" data-toggle="dropdown" data-caret="false" class="btn btn-light text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            '.$dropdown.'
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;

	case 'search_media':
    $data = array();
    $arr=array();
    $time = time()-300;

    $searchInput = secureEncode($_POST['search']);
    $ptype = secureEncode($_POST['ptype']);
    $status = secureEncode($_POST['status']);
    $uploaded = secureEncode($_POST['uploaded']);
    $mediatype = secureEncode($_POST['mediatype']);

    if($mediatype == 'All'){
    	$filter = 'WHERE time >= 0';
    	$table = 'users_photos';	
    }

    if($mediatype == 'photo'){
    	$filter = 'WHERE video = 0 AND story = 0';
    	$table = 'users_photos';	
    }

    if($mediatype == 'video'){
    	$filter = 'WHERE video = 1 AND story = 0';
    	$table = 'users_photos';	
    }

    if($mediatype == 'story'){
    	$filter = 'WHERE story > 0';
    	$table = 'users_photos';	
    }        
    
	if($searchInput != ''){
		$filter.=" AND u_id = ".$searchInput;
	}

	if($status != 'All'){
		$filter.=' AND approved ='.$status;
	}

	if($uploaded != 'All'){
		$filter.=' AND fake ='.$uploaded;
	}	

	if($ptype != 'All'){
		$filter.=' AND blocked ='.$ptype.' AND private = '.$ptype;
	}	  

	if($searchInput != ''){
		$arr['searchUserId'] = $searchInput;
		$arr['searchUserPhoto'] = profilePhoto($searchInput);
		$arr['searchUserName'] = getData('users','name','WHERE id = '.$searchInput);
	} else {
		$arr['searchUserId'] = '';
	}	
	
	$search = getArray($table,$filter,'time DESC','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 

		$arr['searchUserId'] = '';
		if($searchInput != ''){
			$arr['searchUserPhoto'] = profilePhoto($value['u_id']);
			$arr['searchUserId'] = $value['u_id'];
			$arr['searchUserName'] = getData('users','name','WHERE id = '.$value['u_id']);
		}
      	$checkData = $value['id'];
      	$id = $value['id'];
      	$userId = $value['u_id'];
      	$timeago = $value['time'];

    	$uploadedPhoto = '
    		<img src="'.$value['thumb'].'" class="avatar-img rounded">
    	';
    	 
    	$adminUpdateDataCustomVal = "'updateMediaAdmin'";
    	$adminUpdateDataVal = '';

    	$uName = getData('users','name','where id ='.$userId);
    	$uEmail = getData('users','email','where id ='.$userId);
    	$uPhoto = profilePhoto($userId);
    	$uploadedPhotoOnClick = "'".$value['photo']."',".$userId.",'".$uName."','".$uEmail."','".$uPhoto."' ";



		$play_video = "'".$value['photo']."'";
      	$uName = getData('users','name','where id ='.$userId);

        if($value['video'] == 1){
        	$searchMedia = '
        	<td onclick="playVideo('.$play_video.')" style="position:relative;cursor:pointer;max-width:70px">
            	<video class="avatar avatar-img rounded avatar-lg mr-3 media-loading"
            	onclick="playVideo('.$play_video.')" style="width:60px;height:60px;cursor:pointer;border:0px solid #fff">
            		<source src="'.$value['photo'].'"/>
            	</video>
            	<div style="position:absolute;bottom:10px;left:15px;z-index:9">
            		<i class="material-icons" style="color:#fff;font-size:24px;cursor:pointer" onclick="playVideo('.$play_video.')">play_arrow</i>
            	</div>
            </td>
        	';
        	$tdOnClick = 'onclick="playVideo('.$play_video.')"';
        }  else {
        	$searchMedia = ' 
        		<td onclick="showMediaAdmin('.$uploadedPhotoOnClick.')" style="cursor:pointer;max-width:70px">
		            <div class="avatar avatar-lg mr-3 media-loading" style="margin:0 auto;cursor:pointer;">
		            	<a class="s-lightbox" href="'.$value['photo'].'" data-s-lightbox-caption="">'.$uploadedPhoto.'
		            	</a>
		            </div>
	            </td>
        	';
        	$tdOnClick = '';
        }

        $mediaType = 'Photo';
        $mediatypeLabel = 'primary';
        if($value['video'] == 1){
        	$mediaType = 'Video';
        	$mediatypeLabel = 'dark';
        }

        if($value['story'] > 0){
        	$mediaType = 'Story';
        	$mediatypeLabel = 'warning';
        }        

        $mediaArray = array();
        
        $mediaArray['mediaType'] = $mediaType;
        $mediaArray['mediaId'] = $value['id'];
        $mediaArray['mediaIdStory'] = $value['story'];
        $mediaArray['mediaUid'] = $value['u_id'];
        $mediaArray['mediaPhoto'] = $value['photo'];
        $mediaArray['mediaThumb'] = $value['thumb'];

        $mediaPublicDropdown = '';

  		$privateDropDown = 'none';
  		$publicDropDown = 'none';

        if($value['blocked'] == 1 || $value['private'] == 1){
        	$mediaPublic = 'Private';
        	$publicDropDown = 'block';
        	$publicLabel = 'dark';    	
        } else {
        	$mediaPublic = 'Public';
      	    $privateDropDown = 'block';
      	    $publicLabel = 'light';	
        }

		$mediaArray['action'] = 'updateMedia';
		$mediaArray['method'] = 'mediaSetPublic';

	    if($value['story'] == 0){
			$mediaPublicArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaPublicArrayOnClick = "'adminUpdateData(".$mediaPublicArrayOnClick.")'";	        	
	   		$mediaPublicDropdown.= '<a class="dropdown-item" data-media-dropdown-public="'.$id.'" href="javascript:;" style="font-size: 13px;display:'.$publicDropDown.'" 
		  onclick='.$mediaPublicArrayOnClick.'> Set Public </a>';
		} 


    	$mediaArray['method'] = 'mediaSetPrivate';

        if($value['story'] == 0){
			$mediaPublicArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaPublicArrayOnClick = "'adminUpdateData(".$mediaPublicArrayOnClick.")'";	        	
       		$mediaPublicDropdown.= '<a class="dropdown-item" data-media-dropdown-private="'.$id.'" href="javascript:;" style="font-size: 13px;display:'.$privateDropDown.'" 
		  onclick='.$mediaPublicArrayOnClick.'> Set private </a>';
		}

		$approveDropdown = '';

        $approved = '<span class="badge badge-success">Visible</span>';

        $approvedMedia = 'none';
        if($value['approved'] == 0){
        	$approvedMedia = 'block';
        	$approved = '<span class="badge badge-warning">Pending Review</span>'; 
        }
        $pendingMedia = 'none';
        if($value['approved'] == 1){
        	$pendingMedia = 'block';
        	$approved = '<span class="badge badge-success">Visible</span>';
        }        

    	$mediaArray['method'] = 'approveMedia';
    	$mediaArray['val'] = 1;
    	$mediaArray['html'] = '<span class="badge badge-success">Visible</span>';
		$mediaApproveArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
        $mediaApproveArrayOnClick = "'adminUpdateData(".$mediaApproveArrayOnClick.")'"; 
               	
   		$approveDropdown.= '<a class="dropdown-item" data-media-dropdown-approve="'.$id.'" href="javascript:;" style="font-size: 13px;display:'.$approvedMedia.'" 
	  onclick='.$mediaApproveArrayOnClick.'> Approve media</a>';        	
    	      
    	$mediaArray['method'] = 'approveMedia';
    	$mediaArray['val'] = 0;
    	$mediaArray['html'] = '<span class="badge badge-warning">Pending Review</span>';
		$mediaApproveArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
        $mediaApproveArrayOnClick = "'adminUpdateData(".$mediaApproveArrayOnClick.")'"; 
               	
   		$approveDropdown.= '<a class="dropdown-item" data-media-dropdown-pending="'.$id.'" href="javascript:;" style="font-size: 13px;display:'.$pendingMedia.'" 
	  onclick='.$mediaApproveArrayOnClick.'> Change to Pending</a>';        	
    	
              

        if($value['approved'] == 2 && $value['story'] == 0){
        	$mediaArray['method'] = 'approveMedia';
        	$mediaArray['val'] = 1;
        	$mediaArray['html'] = '<span class="badge badge-success">Visible</span>';
			$mediaApproveArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaApproveArrayOnClick = "'adminUpdateData(".$mediaApproveArrayOnClick.")'"; 

       		$approveDropdown.= '<a class="dropdown-item" data-media-dropdown-approve="'.$id.'" href="javascript:;" style="font-size: 13px;" 
		  onclick='.$mediaApproveArrayOnClick.'> Approve media </a>
		  <div class="dropdown-divider"></div>';
        	$approved = '<span class="badge badge-danger">Deleted by user</span>';
        } 

        $uploadToProfileDropdown = '';
        if($value['story'] > 0){
        	$mediaArray['action'] = 'updateMedia';
        	$mediaArray['method'] = 'uploadToProfile';
			$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'"; 

       		$uploadToProfileDropdown = '<a class="dropdown-item" data-media-dropdown-uploadTo="'.$id.'" href="javascript:;" style="font-size: 13px;" 
		  onclick='.$mediaArrayOnClick.'> Upload to profile </a>
		  <div class="dropdown-divider"></div>';
        }

        $uploadToStoryDropdown = '';
        if($value['story'] == 0){
        	$mediaArray['action'] = 'updateMedia';
        	$mediaArray['method'] = 'uploadToStory';
			$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'"; 

       		$uploadToStoryDropdown = '<a class="dropdown-item" data-media-dropdown-uploadTo="'.$id.'" href="javascript:;" style="font-size: 13px;" 
		  onclick='.$mediaArrayOnClick.'> Upload to Story </a>
		  <div class="dropdown-divider"></div>';
        }        

        $setAsProfilePhotoDropdown = '';
        if($value['story'] == 0 && $value['video'] == 0 && $value['profile'] == 0){
        	$mediaArray['action'] = 'updateMedia';
        	$mediaArray['method'] = 'setAsProfilePhoto';
			$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'"; 

       		$setAsProfilePhotoDropdown = '
       		<a class="dropdown-item" data-media-dropdown-uploadTo="'.$id.'" href="javascript:;" style="font-size: 13px;" 
		  onclick='.$mediaArrayOnClick.'> Set as profile photo </a>
		  <div class="dropdown-divider"></div>';
        }   


		$storyFrom = $sm['plugins']['story']['days'];
		$time = time();	
		$extra = 86400 * $storyFrom;
		$storyFrom = $time - $extra;
		
		if($value['story'] > 0 && $value['approved'] == 1){
			$storiesFilter = 'where id = '.$value['story'];
			$checkStory = getData('users_story','storyTime',$storiesFilter);
			if($checkStory > $storyFrom){
				$approved = '<span class="badge badge-success">Visible</span>';
			} else {
				$approved = '<span class="badge badge-light">No visible</span>';

	        	$mediaArray['action'] = 'updateMedia';
	        	$mediaArray['method'] = 'reUploadStory';
	        	$mediaArray['html'] = '<span class="badge badge-success">Visible</span>';
				$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
		        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'"; 
	       		$uploadToStoryDropdown = '
				<div class="dropdown-divider" data-media-dropdown-reupload-story="'.$id.'"></div>
	       		<a class="dropdown-item" data-media-dropdown-reupload-story="'.$id.'" href="javascript:;" style="font-size: 13px;" 
			  onclick='.$mediaArrayOnClick.'>Re-upload Story</a>
			  <div class="dropdown-divider" data-media-dropdown-reupload-story="'.$id.'"></div>';
			}
		}

		if($value['story'] > 0 && $value['approved'] == 1){
			$storiesFilter = 'where id = '.$value['story'];
			$checkStory = getData('users_story','storyTime',$storiesFilter);
			if($checkStory > $storyFrom){
				$approved = '<span class="badge badge-success">Visible</span>';
			} else {
				$approved = '<span class="badge badge-light">No visible</span>';

	        	$mediaArray['action'] = 'updateMedia';
	        	$mediaArray['method'] = 'reUploadStory';
	        	$mediaArray['html'] = '<span class="badge badge-success">Visible</span>';
				$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
		        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'"; 
	       		$uploadToStoryDropdown = '
				<div class="dropdown-divider" data-media-dropdown-reupload-story="'.$id.'"></div>
	       		<a class="dropdown-item" data-media-dropdown-reupload-story="'.$id.'" href="javascript:;" style="font-size: 13px;" 
			  onclick='.$mediaArrayOnClick.'>Re-upload Story</a>
			  <div class="dropdown-divider" data-media-dropdown-reupload-story="'.$id.'"></div>';
			}
		}		

		$storyPriceDropdown = '';
        if($value['story'] > 0){
        	$checkStoryPrice = getData('users_story','credits','WHERE id = '.$value['story']);

        	if($checkStoryPrice > 0){
        		$mediaPublic = $checkStoryPrice.' Credits';
        	} else {
        		$mediaPublic = 'FREE';   		
        	}

        	$mediaArray['action'] = 'updateMedia';
        	$mediaArray['method'] = 'changeCreditPrice';
			$mediaArrayOnClick = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
	        $mediaArrayOnClick = "'adminUpdateData(".$mediaArrayOnClick.")'";

	        $storyPrices = explode(',',$sm['plugins']['story']['storyCreditsValues']);
       		$storyPriceDropdown = '
       		<div class="dropdown-item" data-media-dropdown-price-story="'.$id.'"
       		 style="font-size: 13px;">Story price</div>
       			<form>
	       			<select class="form-control" id="storyPriceSelect'.$id.'" 
	       			style="width:80%!important;margin-left:10%" onchange='.$mediaArrayOnClick.'>
	       				<option value="0">FREE</option>';
		       			foreach ($storyPrices as $price) {
		       				$selected = '';
		       				if($price == $checkStoryPrice){
		       					$selected = 'selected';
		       				}
		       				if($price == 0){
		       					$text = 'Free';
		       				} else {
		       					$text = $price.' Credits';
		       				}
		       				$storyPriceDropdown.='<option value="'.$price.'" '.$selected.'>
		       				'.$text.'</option>';
		       			}
	       				$storyPriceDropdown.='
	       			</select>
       			</form>
		  <div class="dropdown-divider" data-media-dropdown-price-story="'.$id.'"></div>';             	
        }		

        $mediaArray['action'] = 'deleteMedia';
        $deleteMedia = json_encode($mediaArray,JSON_UNESCAPED_UNICODE);
        $deleteMediaOnClick = "'adminDeleteData(".$deleteMedia.")'";	

		$dropdown = '
		  <a class="dropdown-item" style="font-size: 13px" target="_blank" href="'.$sm['config']['site_url'].'@'.$value['u_id'].'">Open live profile</a>
		  <a class="dropdown-item" style="font-size: 13px" href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['u_id'].'" target="_blank">Edit User</a>		  
		  <div class="dropdown-divider"></div>
		  '.$setAsProfilePhotoDropdown.'
		  '.$mediaPublicDropdown.'
		  '.$approveDropdown.'
		  '.$uploadToStoryDropdown.'
		  '.$uploadToProfileDropdown.'
		  '.$storyPriceDropdown.'
		  <a class="dropdown-item" href="#" style="font-size: 13px;color: #b50000" 
		  onclick='.$deleteMediaOnClick.'>
		  Delete media</a>'; 

		$userTd = '
	        <td style="min-width:180px;cursor:pointer;" onclick="searchMediaById('.$userId.')">
	            <div class="media align-items-center">
	                <div class="avatar avatar-sm mr-3" style="width:34px;height:34px;border-radius:50%">
	                    <img data-media-profile-photo="'.$userId.'" src="'.profilePhoto($userId).'" class="avatar-img rounded" style="border-radius:50%!important">
	                </div>
	                <div class="media-body">
	                    <strong class="js-lists-values-employee-name">'.$uName.'</strong><span class="badge badge-warning"></span><br>
	                    <span class="text-muted js-lists-values-employee-title">ID: '.$userId.'</span>
	                </div>
	            </div>
	        </td> 
		';

		if(strpos($_SERVER['HTTP_REFERER'], 'admin&p=user&id=') == true){
			$userTd = '';
		}

        $data[$i]='
            <tr class="data-search-verifications" data-media-i="'.$i.'" data-media-id="'.$id.'" data-media-id-story="'.$value['story'].'" data-media-type="'.$mediaType.'" data-media-src="'.$value['photo'].'">
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" onclick="checkData(this,'.$checkData.')" class="custom-control-input" data-check-search="'.$checkData.'" id="checkcall_'.$id.'">
                        <label class="custom-control-label" style="cursor: pointer;" for="checkcall_'.$id.'">
                        <span class="text-hide">Check</span></label>
                    </div>
                </td>
                '.$searchMedia.'
                <td style="min-width:110px">
                	<small class="badge badge-'.$mediatypeLabel.'">'.$mediaType.'</small>
                </td>                
                <td style="min-width:115px">
                	<small class="text-muted">'.time_elapsed_string($timeago).'</small>
                </td>  
                <td style="min-width:85px">
                	<small class="badge badge-'.$publicLabel.'" data-media-public="'.$id.'">
                		'.$mediaPublic.'
                	</small>
                </td>
                <td style="min-width:105px">
                	<small class="text-muted" id="approveMedia'.$id.'">'.$approved.'</small>
                </td>
                '.$userTd.'                                 
                <td>
                    <div class="dropdown ml-auto" data-table-dropdown>
                        <a href="#" data-toggle="dropdown"  data-caret="false" class="btn btn-light text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            '.$dropdown.'
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;	

	case 'getData':
		$table = secureEncode($_POST['table']);
		$col = secureEncode($_POST['col']);
		$filter = secureEncode($_POST['filter']);
		echo getData($table,$col,$filter);
	break;

	case 'updateMedia':

		$id = secureEncode($_POST['mediaId']);
		$storyId = secureEncode($_POST['mediaIdStory']);
		$type = secureEncode($_POST['mediaType']);	
		$method = secureEncode($_POST['method']);

		if($method == 'uploadToProfile'){
			$time = time();
			$uid = secureEncode($_POST['mediaUid']);
			$p = secureEncode($_POST['mediaPhoto']);
			$t = secureEncode($_POST['mediaThumb']);
			if($type == 'Video'){
				$video = 1;
			} else {
				$video = 0;
			}
			$fake = getData('users','fake','WHERE id ='.$uid);
			if($type == 'Story'){
				$storyType = getData('users_story','storyType','WHERE id ='.$storyId);
				if($storyType == 'video'){
					$video = 1;
				} else {
					$video = 0;
				}
			}
			$mysqli->query("INSERT INTO users_photos (u_id,photo,thumb,approved,video,time,fake)
			VALUES ('".$uid."','".$p."', '".$t."',1,'".$video."','".$time."',".$fake.")");

			exit;	
		}

		if($method == 'changeCreditPrice'){
			$val = secureEncode($_POST['val']);
			$mysqli->query('UPDATE users_story SET credits = "'.$val.'" WHERE id = "'.$storyId.'"');
			exit;
		}

		if($method == 'reUploadStory'){
			$time = time();
			$mysqli->query('UPDATE users_story SET storyTime = "'.$time.'" WHERE id = "'.$storyId.'"');
			$mysqli->query('UPDATE users_photos SET time = "'.$time.'" WHERE story = "'.$storyId.'"');
			exit;
		}

		if($method == 'uploadToStory'){
			$time = time();
			$uid = secureEncode($_POST['mediaUid']);
			$p = secureEncode($_POST['mediaPhoto']);
			$t = secureEncode($_POST['mediaThumb']);
			if($type == 'Video'){
				$video = 'video';
			} else {
				$video = 'image';
			}
			$lat = getData('users','lat','WHERE id ='.$uid);
			$lng = getData('users','lng','WHERE id ='.$uid);
			$fake = getData('users','fake','WHERE id ='.$uid);
      		$query = "INSERT INTO users_story (uid,storyTime,story,storyType,lat,lng,review)
      			 VALUES ('".$uid."','".$time."','".$p."','".$video."','".$lat."','".$lng."','No')";
      		if ($mysqli->query($query) === TRUE) {
      			$last_id = $mysqli->insert_id;
				if($type == 'Video'){
					$video = 1;
				} else {
					$video = 0;
				}      			
				$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,story,approved,fake)
      			 VALUES ('".$uid."','".$time."','".$p."','".$p."','".$video."',".$last_id.",1,".$fake.")");	
      		}

			exit;	
		}	

		$updateData = '';

		if($method == 'mediaSetPrivate'){
			$updateData = 'private = 1, blocked = 1';
		}

		if($method == 'mediaSetPublic'){
			$updateData = 'private = 0, blocked = 0';
		}

		if($method == 'approveMedia'){
			$val = secureEncode($_POST['val']);
			$updateData = 'approved = '.$val;
			if($storyId > 0){
				$mysqli->query('UPDATE users_story SET review = "No" WHERE id = "'.$storyId.'"');
			}
		}

		if($method == 'setAsProfilePhoto'){
			$mediaUid = secureEncode($_POST['mediaUid']);
			$mysqli->query('UPDATE users_photos SET profile = 0 WHERE u_id = '.$mediaUid);
			$updateData = 'profile = 1';
		}

		$mysqli->query('UPDATE users_photos SET '.$updateData.' WHERE id = "'.$id.'"');
	break;

	case 'search_videocalls':
    $data = array();
    $arr=array();
    $time = time()-300;

    $order = 'call_date';
    $filter = '';
    if(isset($_POST['uid'])){
    	$uid = secureEncode($_POST['uid']);
    	$filter = 'WHERE c_id = "'.$uid.'" OR r_id = "'.$uid.'"';
    }
    $search = getArray('videocall',$filter,$order.' desc','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 
        
        $onlineNow = '';

        $c_video = false;
        $r_video = false;

        if(!empty($value['c_id_video'])){
          $c_video = true;
        }
        if(!empty($value['r_id_video'])){
          $r_video = true;
        }       
        if($value['status'] == 1){
          $badge = '<span class="badge badge-success">ANSWERED</span>';
        } else {
          $badge = '<span class="badge badge-white">NOT ANSWERED</span>';
        }

        $c_photo = "'".profilePhoto($value['c_id'])."'";
        $r_photo = "'".profilePhoto($value['r_id'])."'";  
        $c_video = "'".$value['c_id_video']."'";  
        $r_video = "'".$value['r_id_video']."'";    

        if(!empty($value['c_id_video'])){
        	$cAvatar = '
            	<video class="avatar-img rounded"
            	onclick="playVideo('.$c_video.')" style="width:100%;height:100%;cursor:pointer;border:2px solid #fff">
            		<source src="'.$value['c_id_video'].'"/>
            	</video>
            	<div style="position:absolute;bottom:0px;left:-3px;z-index:9">
            		<i class="material-icons" style="color:#fff;font-size:24px;cursor:pointer" onclick="playVideo('.$c_video.')">play_arrow</i>
            	</div>
        	';
        }  else {
        	$cAvatar = '
        		<img src="'.profilePhoto($value['c_id']).'" class="avatar-img rounded">
        	';
        }
        if(!empty($value['r_id_video'])){
        	$rAvatar = '
            	<video class="avatar-img rounded"
            	onclick="playVideo('.$r_video.')" style="width:100%;height:100%;cursor:pointer;border:2px solid #fff">
            		<source src="'.$value['r_id_video'].'"/>
            	</video>
            	<div style="position:absolute;bottom:0px;left:-3px;z-index:9">
            		<i class="material-icons" style="color:#fff;font-size:24px;cursor:pointer" onclick="playVideo('.$r_video.')">play_arrow</i>
            	</div>
        	';
        }  else {
        	$rAvatar = '
        		<img src="'.profilePhoto($value['r_id']).'" class="avatar-img rounded">
        	';
        }

        if(empty($value['duration'])){
        	$duration = '00:00';
        } else {
        	$duration = $value['duration'];
        }
        $call_id = $value['call_date'];

        $onclickData = array();
        $onclickData['action'] = 'deleteVideocall';
        $onclickData['videocall'] = $call_id;

        $onclickData = json_encode($onclickData,JSON_UNESCAPED_UNICODE);

        $onclick = "'adminDeleteData(".$onclickData.")'";	
        $data[$i]='

            <tr>
                <td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" onclick="checkData(this,'.$call_id.')" class="custom-control-input" data-check-search="'.$call_id.'" id="checkcall_'.$call_id.'">
                        <label class="custom-control-label" style="cursor: pointer;" for="checkcall_'.$call_id.'">
                        <span class="text-hide">Check</span></label>
                    </div>
                </td>

                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            '.$cAvatar.'
                        </div>
                        <div class="media-body">
                        	<a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['c_id'].'" target="_blank" style="text-decoration:none;color:auto">
                            <strong class="js-lists-values-employee-name">'.getData('users','name','where id ='.$value['c_id']).' , '.getData('users','age','where id ='.$value['c_id']).'</strong> <span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title"><small>'.getData('users','credits','where id ='.$value['c_id']).' credits</small></span>
                            </a>
                        </div>
                    </div>
                </td>
                
                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            '.$rAvatar.'
                        </div>
                        <div class="media-body">
                        	<a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['r_id'].'" target="_blank" style="text-decoration:none;color:auto">                        
                            <strong class="js-lists-values-employee-name">'.getData('users','name','where id ='.$value['r_id']).' , '.getData('users','age','where id ='.$value['r_id']).'</strong><br>
                            <span class="text-muted js-lists-values-employee-title"><small>'.getData('users','credits','where id ='.$value['r_id']).' credits</small></span>    
                            </a>                        
                        </div>
                    </div>
                </td>                                                    
                <td>'.$duration.'</td>
                <td><small class="text-muted">'.time_elapsed_string($value['call_date']).'</small></td>
                <td>'.$badge.'</td>                                 
                <td>
                    <div class="dropdown ml-auto">
                        <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            <a class="dropdown-item" href="#" onclick='.$onclick.'>Delete call</a>
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;

	case 'search_live':
    $data = array();
    $arr=array();
    $time = time()-300;

    $order = 'id';
    $filter = '';
    if(isset($_POST['uid'])){
    	$uid = secureEncode($_POST['uid']);
    	$filter = 'WHERE uid = "'.$uid.'"';
    }
    $search = getArray('live',$filter,$order.' desc','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 
        
        $live_id = $value['id'];

        $onclickData = array();
        $onclickData['action'] = 'deleteLive';
        $onclickData['live'] = $live_id;
        $onclickData = json_encode($onclickData,JSON_UNESCAPED_UNICODE);

        $onclick = "'adminDeleteData(".$onclickData.")'";	

		$checkBanned = getData('live_streamer_banned','uid','WHERE uid = '.$value['uid']); 
		if($checkBanned != 'noData'){
			$streamerBanned = '<a class="dropdown-item" href="#" onclick="endStreamAdmin('.$value['uid'].',2)">Unban streamer</a>';
		} else {
			$streamerBanned = '<a class="dropdown-item" href="#" onclick="endStreamAdmin('.$value['uid'].',1)">Ban streamer</a>';
		}

        if($value['end_time'] == 0){
        	$duration = '<span class="badge badge-success">Live now</span>';
        	$viewers = $value['viewers'];
        	$dropdown = '<a class="dropdown-item" href="#" onclick="endStreamAdmin('.$value['uid'].')">End stream</a><a class="dropdown-item" href="#" onclick="endStreamAdmin('.$value['uid'].',3)">End stream and Ban Streamer</a>';
        } else {
        	$seconds = $value['end_time'] - $value['start_time'];
        	$duration = gmdate("H:i:s", $seconds);
        	$viewers = '-';
        	$dropdown = '<a class="dropdown-item" href="#" onclick='.$onclick.'>Delete live</a>'.$streamerBanned;
        }

    	$sAvatar = '<img src="'.profilePhoto($value['uid']).'" class="avatar-img rounded">';
       	
       	
       	$customTxt = secureEncode($value['custom_text']);
       	if($customTxt == ''){
       		$customTxt = 'Empty message';
       	}

        $data[$i]='

            <tr>

                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            '.$sAvatar.'
                        </div>
                        <div class="media-body">
                        	<a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['uid'].'" target="_blank" style="text-decoration:none;color:auto">
                            <strong class="js-lists-values-employee-name">'.getData('users','name','where id ='.$value['uid']).' , '.getData('users','age','where id ='.$value['uid']).'</strong> <span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title"><small>'.getData('users','credits','where id ='.$value['uid']).' credits</small></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td><small class="text-muted">'.$customTxt.'</small></td>
                <td><small class="text-muted">'.time_elapsed_string($value['start_time']).'</small></td>
                <td>'.$duration.'</td>
                <td>'.$viewers.'</td>                                 
                <td>
                    <div class="dropdown ml-auto">
                        <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">                          
                            '.$dropdown.'
                        </div>
                    </div>
                </td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;		

	case 'search_orders':
    $data = array();
    $arr=array();
    $time = time()-300;

    $order = 'order_date';
    $filter = 'where order_status = "success"';
    if(isset($_POST['uid'])){
    	$uid = secureEncode($_POST['uid']);
    	$filter = 'WHERE uid = "'.$uid.'"';
    }
    $search = getArray('orders',$filter,$order.' desc','');
    $i=0;
    if(isset($search)){
      foreach ($search as $value) { 
        
        $order_id = $value['order_id'];
    	$sAvatar = '<img src="'.profilePhoto($value['user_id']).'" class="avatar-img rounded">';
       	
        if($value['order_type'] = 'credits'){
            $packageId = $value['order_package']+1;
            $credits = getData('config_credits','credits','WHERE id = "'.$packageId.'"');
            $price = $sm['plugins']['settings']['currencySymbol'].' '.getData('config_credits','price','WHERE id = "'.$packageId.'"');  
            $actionText = $credits.' Credits';             
        } else {
            $packageId = $value['order_package']+1;
            $days = getData('config_premium','days','WHERE id = "'.$packageId.'"');
            $actionText = $days.' days';
            $price = $sm['plugins']['settings']['currencySymbol'].' '.getData('config_premium','price','WHERE id = "'.$packageId.'"');
        }       	
        $data[$i]='

            <tr>
                <td>
                    <div class="media align-items-center">
                        <div class="avatar avatar-sm mr-3 ">
                            '.$sAvatar.'
                        </div>
                        <div class="media-body">
                        	<a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$value['user_id'].'" target="_blank" style="text-decoration:none;color:auto">
                            <strong class="js-lists-values-employee-name">'.getData('users','name','where id ='.$value['user_id']).' , '.getData('users','age','where id ='.$value['user_id']).'</strong> <span class="badge badge-warning"></span><br>
                            <span class="text-muted js-lists-values-employee-title"><small>'.getData('users','credits','where id ='.$value['user_id']).' credits</small></span>
                            </a>
                        </div>
                    </div>
                </td>            
            	<td><small class="text-muted">'.$value['order_id'].'</small></td>
            	<td><small class="text-muted">'.$price.'</small></td>
            	<td><small class="text-muted">'.$actionText.'</small></td>
                <td><small class="text-muted">'.ucfirst($value['order_gateway']).'</small></td>
                <td><small class="text-muted">'.time_elapsed_string($value['order_date']).'</small></td>
				<td><small class="text-muted">'.$value['raw_data'].'</small></td>
            </tr>';

            $i++;
      }
    } else {
      $data = 'Nothing found';
    }

    $arr['data'] = $data;
    $arr['total'] = $i;
    echo json_encode($arr);
	break;

	case 'usearch':
		$data = secureEncode($_POST['dat']);
		echo searchUser($data);		
	break;	


	case 'updatePlugin':
		$plugin = secureEncode($_POST['plugin']);

		$setting = secureEncode($_POST['setting']);
		$premium = secureEncode($_POST['premium']);

		if(!isset($_POST['globalKey'])){
			exit; // Protection 1
		}
		if($_POST['globalKey'] != $sm['settings']['license']){
			exit; // Protection 2
		}
		
		$val = $_POST['val']; //This cant be escaped because requires to allow custom HTML for the ADMINS but its protected by the two protections the globalKey which is the license key of the client, the license key is private and noone has access to it

		$val = str_replace("'", "''", $val);

		$mysqli->query("UPDATE plugins_settings SET setting_val = '".$val."' where plugin = '".$plugin."' and setting = '".$setting."'");	

		$mysqli->query("INSERT INTO plugins_settings_values(plugin,setting,setting_val) VALUES ('".$plugin."','".$setting."','".$val."') ON DUPLICATE KEY UPDATE setting_val = '".$val."'");			

		if($setting == 'enabled'){
			if($val == 'Yes'){
				$val = 1;				
			} else {
				$val = 0;
			}
			$mysqli->query("UPDATE plugins SET enabled = '".$val."' where name = '".$plugin."'");		
		}

		if($setting == 'forceSSL'){
			if($val == 'Yes'){
				$mysqli->query("UPDATE users_photos SET photo = REPLACE(photo,'http://','https://')");
				$presetList = getArray('theme_preset','','theme_modification ASC');
				foreach ($presetList as $preset) {	
					$mysqli->query("UPDATE theme_preset SET theme_settings = REPLACE(theme_settings,'http://','https://') WHERE preset = '".$preset['preset']."'");
				}			
			} else {
				$mysqli->query("UPDATE users_photos SET photo = REPLACE(photo,'https://','http://')");
				$presetList = getArray('theme_preset','','theme_modification ASC');
				foreach ($presetList as $preset) {	
					$mysqli->query("UPDATE theme_preset SET theme_settings = REPLACE(theme_settings,'https://','http://') WHERE preset = '".$preset['preset']."'");
				}				
			}		
		}

		if($setting == 'moderators'){
			$moderators = explode(',',$val);
			$moderationList = getArray('moderation_list','','moderation ASC');

			foreach ($moderators as $moderator) {
				$mysqli->query("INSERT INTO moderators (id) VALUES ('".$moderator."')");
				foreach ($moderationList as $data) {
					$mysqli->query("INSERT INTO moderators_permission (id,setting,setting_val)
					VALUES ('".$moderator."','".$data['moderation']."','No')");
				}																								
			}
		}		
	break;

	case 'testsmtp':
		$arr = array();
		$arr['response'] = testMailNotification();
		$arr['sent'] = 'Ok';
		if (strpos($arr['response'], 'Error') !== false) {
		    $arr['sent'] = 'Error';
		}		
		echo json_encode($arr);		
	break;	
	case 'lang_visible':
		$lang = secureEncode($_POST['id']);
		$val = secureEncode($_POST['val']);
		$mysqli->query("UPDATE languages SET visible = '".$val."' where id = '".$lang."'");	

	break;	

	case 'loadChatAdmin':
		$uid = secureEncode($_POST['uid']);
		$cid = secureEncode($_POST['cid']);

		echo getChatControlPanel($uid,$cid);
	break;

	case 'push':
		$title = secureEncode($_POST['app_push_title']);
		$body = secureEncode($_POST['app_push_body']);
		$image = secureEncode($_POST['app_push_image']);
		appUsers($title,$body,$image);	
	break;
	case 'apps':
		$llogo = secureEncode($_POST['app_logo_login']);
		$logo = secureEncode($_POST['app_logo']);
		$main = secureEncode($_POST['app_first_color']);
		$second = secureEncode($_POST['app_second_color']);
		$mysqli->query("UPDATE config_app SET first_color = '".$main."', second_color = '".$second."', logo = '".$logo."', logo_login = '".$llogo."'");		
	break;
	case 'fakeu':
		$visit = secureEncode($_POST['fakeu_visit']);
		$like = secureEncode($_POST['fakeu_like']);
		$fcountry = secureEncode($_POST['fakeu_country']);
		$fapi = secureEncode($_POST['fakeu_api']);
		$fAI = secureEncode($_POST['fakeu_ai']);
		$fAiChance = secureEncode($_POST['fakeu_respond']);			
		$visit = str_replace('%', '', $visit);
		$like = str_replace('%', '', $like);	
		$mysqli->query("UPDATE config SET fcountry = '".$fcountry."', visit_back = '".$visit."', like_back = '".$like."', fAI = '".$fAI."', fapi = '".$fapi."', fAiChance = '".$fAiChance."'");		
	break;
	case 'engage':
		$e = secureEncode($_POST['engage']);
		$et = secureEncode($_POST['engage_time']);
		$el = secureEncode($_POST['engage_limit']);
		$mysqli->query("UPDATE config SET fEngage = '".$e."', fEngageTime = '".$et."', fEngageLimit = '".$el."'");		
	break;	
	case 'updateThemeSettings':
		$theme = secureEncode($_POST['theme']);
		$s = secureEncode($_POST['setting']);
		$sval = secureEncode($_POST['setting_val']);				
		$mysqli->query("UPDATE theme_settings SET setting_val = '".$sval."' where setting = '".$s."' and theme = '".$theme."'");			
	break;
	case 'aikey':
		$aikey = secureEncode($_POST['fakeu_aiapikey']);
		$mysqli->query("UPDATE config SET fapiKey = '".$aikey."'");		
	break;		
	case 'edit_u':
		$uid = secureEncode($_POST['edit_id']);	
		$name = secureEncode($_POST['edit_name']);
		$email = secureEncode($_POST['edit_email']);
		$age = secureEncode($_POST['edit_age']);
		$city = secureEncode($_POST['edit_city']);
		$country = secureEncode($_POST['edit_country']);
		$premium = secureEncode($_POST['edit_premium']);		
		$gender = secureEncode($_POST['edit_gender']);
		$lang = secureEncode($_POST['edit_lang']);
		$credits = secureEncode($_POST['edit_credits']);		
		$admin = secureEncode($_POST['edit_admin']);
		$verified = secureEncode($_POST['edit_verified']);		
		$mysqli->query("UPDATE users SET name = '".$name."' , email = '".$email."' , city = '".$city."', country = '".$country."',
					   age = '".$age."', gender = '".$gender."', credits = '".$credits."',
					   lang = '".$lang."', admin = '".$admin."', verified = '".$verified."' WHERE id = '".$uid."'");
		if($premium != ''){	
			$time = time();	
			$extra = 86400 * $premium;
			$premium = $time + $extra;
			$mysqli->query("UPDATE users_premium set premium = '".$premium."' where uid = '".$uid."' ");
		}
	break;	

	
	case 'approveUserVerification':
		$uid = secureEncode($_POST['uid']);
		$approve = secureEncode($_POST['approve']);
		if($approve == 1){
			$mysqli->query("UPDATE users SET verified = 1 where id = '".$uid."'");
			$mysqli->query("UPDATE users_verification SET verify = 1,status = 'Approved' where uid = '".$uid."'");
		} else {
			$mysqli->query("UPDATE users_verification SET verify = 0,status = 'Denied' where uid = '".$uid."'");	
		}
				
	break;	
	case 'removeFromReportList':
		$uid = secureEncode($_POST['uid']);
		$mysqli->query("UPDATE reports SET viewed = 1 where reported = '".$uid."'");
	break;

	case 'withdrawComplete':
		$id = secureEncode($_POST['id']);
		$mysqli->query("UPDATE users_withdraw SET status = 'Complete' where id = '".$id."'");	
	break;

	case 'withdrawCanceled':
		$id = secureEncode($_POST['id']);
		$mysqli->query("UPDATE users_withdraw SET status = 'Canceled' where id = '".$id."'");	
	break;	
	
	case 'editlang':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['lid']);
		$table = secureEncode($_POST['table']);
		$landingPreset = secureEncode($_POST['landingPreset']);
		$landing = secureEncode($_POST['landing']);
		$theme = secureEncode($_POST['theme']);
		if(!empty($theme) && $theme != 'No'){

			if($landing == $theme){
				$mysqli->query("UPDATE $table SET text = '$val' where id = '$lid' and lang_id = '$langid' and theme = '$theme' and preset = '$landingPreset'");
			} else {
				$mysqli->query("UPDATE $table SET text = '$val' where id = '$lid' and lang_id = '$langid' and theme = '$theme'");
			}
		} else {
			$mysqli->query("UPDATE $table SET text = '$val' where id = '$lid' and lang_id = '$langid'");	
		}
		
	break;
	case 'editemaillang':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['lid']);
		$mysqli->query("UPDATE email_lang SET text = '$val' where id = '$lid' and lang_id = '$langid'");
	break;	
	case 'editlangt':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['lid']);
		$mysqli->query("UPDATE twoo_lang SET text = '$val' where id = '$lid' and lang_id = '$langid'");
	break;	
	case 'editlanga':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['lid']);
		$mysqli->query("UPDATE app_lang SET text = '$val' where id = '$lid' and lang_id = '$langid'");
	break;
	case 'editlangseo':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['id']);
		$page = secureEncode($_POST['page']);
		$mysqli->query("UPDATE seo_lang SET text = '$val' where id = '$lid' and lang_id = '$langid' and page = '$page'");
	break;
	case 'editlanglanding':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$lid = secureEncode($_POST['lid']);
		$page = secureEncode($_POST['page']);
		$mysqli->query("UPDATE app_lang SET text = '$val' where id = '$lid' and lang_id = '$langid' and page = '$page'");
	break;		
	case 'editlanggender':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$id = secureEncode($_POST['id']);
		$mysqli->query("UPDATE config_genders SET name = '$val' where id = '$id' and lang_id = '$langid'");
	break;
	case 'editlangq':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$id = secureEncode($_POST['id']);
		$mysqli->query("UPDATE config_profile_questions SET question = '$val' where id = '$id' and lang_id = '$langid'");
	break;
	case 'editlanganswer':
		$langid = secureEncode($_POST['langid']);
		$val = secureEncode($_POST['val']);
		$id = secureEncode($_POST['id']);
		$qid = secureEncode($_POST['qid']);
		$mysqli->query("UPDATE config_profile_answers SET answer = '$val' where id = '$id' and qid = '$qid' and lang_id = '$langid'");
	break;				
	case 'gift':
		$giftid = secureEncode($_POST['giftid']);
		$val = secureEncode($_POST['val']);
		$mysqli->query("UPDATE gifts SET price = '$val' where id = '$giftid'");
	break;	
	case 'change_theme':
		$col = secureEncode($_POST['col']);
		$folder = secureEncode($_POST['folder']);
		$mysqli->query("UPDATE config SET $col = '$folder'");
	break;		
	case 'website':
		$name = secureEncode($_POST['site_name']);
		$email = secureEncode($_POST['site_email']);		
		$title = secureEncode($_POST['site_title']);
		$desc = secureEncode($_POST['site_desc']);
		$keywords = secureEncode($_POST['site_keywords']);
		$lang = secureEncode($_POST['site_lang']);
		$review = secureEncode($_POST['site_photo_review']);
		$email_verification = secureEncode($_POST['site_email_verification']);
		$credits = secureEncode($_POST['site_free_credits']);
		$premium = secureEncode($_POST['site_free_premium']);
		$wm = secureEncode($_POST['site_wm']);
		$dc = secureEncode($_POST['site_dc']);		
		$logo = secureEncode($_POST['site_logo']);
		$logoL = secureEncode($_POST['site_logo_landing']);
		$mobile = secureEncode($_POST['site_mobile']);	
		$mysqli->query("UPDATE config SET name = '$name', email = '$email', photo_review = '$review', title = '$title', description = '$desc', keywords = '$keywords', lang = '$lang', logo = '$logo', email_verification = '$email_verification', free_credits = '$credits', free_premium = '$premium', logo_landing = '$logoL', mobile_site = '$mobile', wm = '$wm', dc = '$dc'");
	break;
	case 'updateAnswer':
		$q = secureEncode($_POST['qid']);
		$a = secureEncode($_POST['answer']);
		$id = secureEncode($_POST['answerId']);
		if($a == ''){
			$mysqli->query("DELETE FROM config_profile_answers where id = '".$id."' and qid = '".$q."'");				
		} else {
			$query = $mysqli->query("SELECT * FROM languages order by id ASC");
				if ($query->num_rows > 0) { 
				while($re = $query->fetch_object()){  
					$mysqli->query("INSERT INTO config_profile_answers (id,qid,answer,lang_id)
					VALUES ('".$id."','".$q."','".$a."','".$re->id."') ON DUPLICATE KEY UPDATE answer = '".$a."'");		
				}
			}							
		}
		echo getAbsolutePageAdmin('questionsAjax');
	break;
	case 'smtp':
		$host = secureEncode($_POST['email_host']);
		$port = secureEncode($_POST['email_port']);		
		$username = secureEncode($_POST['email_email']);
		$password = secureEncode($_POST['email_pswd']);		
		$mysqli->query("UPDATE config_email SET host = '$host', port = '$port', user = '$username', password = '$password'");
	break;

	case 'rt':
		$pusher_id = secureEncode($_POST['pusher_id']);
		$pusher_key = secureEncode($_POST['pusher_key']);		
		$pusher_secret = secureEncode($_POST['pusher_secret']);
		$pusher_clauster = secureEncode($_POST['pusher_clauster']);		
		$mysqli->query("UPDATE config SET pusher_id = '$pusher_id', pusher_key = '$pusher_key', pusher_secret = '$pusher_secret', pusher_clauster = '$pusher_clauster'");
	break;		
	case 'vserver':
		$host = secureEncode($_POST['videocall_host']);	
		$mysqli->query("UPDATE config SET videocall = '$host'");
	break;			
	case 'social-connect':
		$id = secureEncode($_POST['fb_id']);	
		$key = secureEncode($_POST['fb_key']);	
		$google_key = secureEncode($_POST['google_key']);	
		$google_secret = secureEncode($_POST['google_secret']);	
		$twitter_key = secureEncode($_POST['twitter_key']);	
		$twitter_secret = secureEncode($_POST['twitter_secret']);	
		$instagram_key = secureEncode($_POST['instagram_key']);	
		$instagram_secret = secureEncode($_POST['instagram_secret']);	
		$mysqli->query("UPDATE config SET fb_app_id = '$id', fb_app_secret = '$key', twitter_key = '$twitter_key', twitter_secret = '$twitter_secret',
		instagram_key = '$instagram_key',instagram_secret = '$instagram_secret', google_key = '$google_key', google_secret = '$google_secret'");
	break;	
	case 'paypal':
		$id = secureEncode($_POST['site_paypal']);			
		$mysqli->query("UPDATE config SET paypal = '$id'");
	break;	
	case 'geokey':
		$id = secureEncode($_POST['google_maps']);			
		$mysqli->query("UPDATE config SET google_maps = '$id'");
	break;		
	case 'fortumo':
		$id = secureEncode($_POST['site_fortumo_service']);	
		$secret = secureEncode($_POST['site_fortumo_secret']);			
		$mysqli->query("UPDATE config SET fortumo_service = '$id', fortumo_secret = '$secret'");
	break;
	case 'stripe':
		$id = secureEncode($_POST['site_stripe_pub']);	
		$secret = secureEncode($_POST['site_stripe_secret']);			
		$mysqli->query("UPDATE config SET stripe_pub = '$id', stripe_secret = '$secret'");
	break;	
	case 'paygol':
		$id = secureEncode($_POST['site_paygol']);			
		$mysqli->query("UPDATE config SET paygol = '$id'");
	break;
	case 'currency':
		$id = secureEncode($_POST['site_currency']);			
		$mysqli->query("UPDATE config SET currency = '$id'");
	break;	
	case 'prices':
		$p1 = secureEncode($_POST['site_price_private']);	
		$p2 = secureEncode($_POST['site_price_spotlight']);	
		$p3 = secureEncode($_POST['site_price_chat']);	
		$p4 = secureEncode($_POST['site_price_boost']);	
		$p5 = secureEncode($_POST['site_price_discover']);	
		$p6 = secureEncode($_POST['site_price_first']);			
		$mysqli->query("UPDATE config_prices SET private = '$p1', spotlight = '$p2', chat = '$p3', boost = '$p4', discover = '$p5', first = '$p6'");
	break;	
	case 's3':
		$p1 = secureEncode($_POST['s3_bucket']);	
		$p2 = secureEncode($_POST['s3_key']);	
		$p3 = secureEncode($_POST['s3_secret']);			
		$mysqli->query("UPDATE config SET s3_bucket = '$p1', s3 = '$p2', s3_key = '$p3'");
	break;		
	case 'credits':
		$c1 = secureEncode($_POST['credits1']);	
		$c2 = secureEncode($_POST['credits2']);	
		$c3 = secureEncode($_POST['credits3']);	
		$c4 = secureEncode($_POST['credits4']);	
		$c5 = secureEncode($_POST['credits5']);			
		$mysqli->query("UPDATE config_credits SET price = '$c1' where id = 1");
		$mysqli->query("UPDATE config_credits SET price = '$c2' where id = 2");
		$mysqli->query("UPDATE config_credits SET price = '$c3' where id = 3");
		$mysqli->query("UPDATE config_credits SET price = '$c4' where id = 4");
		$mysqli->query("UPDATE config_credits SET price = '$c5' where id = 5");		
	break;	
	case 'premium':
		$c1 = secureEncode($_POST['premium1']);	
		$c2 = secureEncode($_POST['premium2']);	
		$c3 = secureEncode($_POST['premium3']);			
		$mysqli->query("UPDATE config_premium SET price = '$c1' where id = 1");
		$mysqli->query("UPDATE config_premium SET price = '$c2' where id = 2");
		$mysqli->query("UPDATE config_premium SET price = '$c3' where id = 3");		
	break;	
	case 'premium_acc':
		$c1 = secureEncode($_POST['site_premium_chat']);	
		$c2 = secureEncode($_POST['site_premium_videocall']);	
		$c3 = secureEncode($_POST['site_premium_private']);
		$c4 = secureEncode($_POST['site_premium_fans']);	
		$c5 = secureEncode($_POST['site_premium_visits']);
		$c6 = secureEncode($_POST['site_premium_mobile_ads']);		
		$mysqli->query("UPDATE config_accounts SET chat = '$c1' , videocall = '$c2' , private = '$c3', fans = '$c4', visits = '$c5', mobile_ads = '$c6' where type = 2");	
	break;
	case 'basic_acc':
		$c1 = secureEncode($_POST['site_basic_chat']);	
		$c2 = secureEncode($_POST['site_basic_videocall']);	
		$c3 = secureEncode($_POST['site_basic_private']);
		$c4 = secureEncode($_POST['site_basic_fans']);	
		$c5 = secureEncode($_POST['site_basic_visits']);
		$c6 = secureEncode($_POST['site_basic_mobile_ads']);		
		$mysqli->query("UPDATE config_accounts SET chat = '$c1' , videocall = '$c2' , private = '$c3', fans = '$c4', visits = '$c5', mobile_ads = '$c6' where type = 1");	
	break;	
		
	case 'login':
		$id = secureEncode($_POST['id']);	
		$password = secureEncode($_POST['pass']);			
		$user_check = $mysqli->query("SELECT * FROM users WHERE name = '".$id."'");
		if($user_check->num_rows == 0 ){
			echo 0;
			exit;
		}
		$pass = $user_check->fetch_object();
		if($password == $pass->screen_name) { 
			if($pass->admin == 1){
				$_SESSION['user'] = $pass->id;
				echo 1; 
			}else{
				echo 0; 
			}
			exit;	
		} else {
			echo 0;	
			exit;		
		}
	break;		
	case 'photo':
		$pid = secureEncode($_POST['photoid']);
		$m = secureEncode($_POST['method']);
		if($m == 1){
			$mysqli->query("UPDATE users_photos SET approved = 1 WHERE id ='$pid'");	
		}
		if($m == 2){
			$mysqli->query("UPDATE users_photos SET approved = 2 WHERE id ='$pid'");	
		}		
		if($m == 3){				
			$mysqli->query("UPDATE users_photos SET approved = 1 , blocked = 1 WHERE id ='$pid'");	
		}
	break;

	case 'unbanEmail':
		$id = secureEncode($_POST['email']);
		$email = getData('blocked_users','email','where id ='.$id);
		$mysqli->query('DELETE FROM blocked_users WHERE id = '.$id);

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
			$activity = 'Email '.$email.' has been unbanned by '.$sm['user']['name'];
			activity('system',$activity,'Unbanned '.$email.'');	
		}	
	break;

	case 'deleteMedia':
		$id = secureEncode($_POST['mediaId']);
		$type = secureEncode($_POST['mediaType']);
		
		$mediaPhoto = getData('users_photos','photo','where id = '.$id);
		$mediaPhoto = str_replace($sm['config']['site_url'], '../', $mediaPhoto);
		$mediaThumb = getData('users_photos','thumb','where id = '.$id);
		$mediaThumb = str_replace($sm['config']['site_url'], '../', $mediaThumb);
		unlink($mediaPhoto);
		unlink($mediaThumb);

		$mysqli->query('DELETE FROM users_photos WHERE id = '.$id);

		if($type == 'Story'){
			$mysqli->query('DELETE FROM users_story WHERE id = '.secureEncode($_POST['mediaIdStory']));
		}

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
			$activity = 'Media '.$id.' has been deleted';
			activity('system',$activity,'Media deleted');	
		}	
	break;	

	case 'unbanIP':
		$ip = secureEncode($_POST['ip']);
		$mysqli->query('DELETE FROM blocked_ips WHERE ip = "'.$ip.'"');

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
			$activity = 'IP '.$ip.' has been unbanned by '.$sm['user']['name'];
			activity('system',$activity,'Unbanned IP '.$ip);	
		}	
	break;		

	case 'delete_profile':
		$uid = secureEncode($_POST['uid']);
		$banData = secureEncode($_POST['ban']);
		$dataBan = explode(',',$banData);
		$ban = $dataBan[0];
		$val = $dataBan[1];
		
		$time = time();
		$rand = rand(0,9999).$time;
		if($uid == $sm['user']['id']){
			exit;
		}
		if($ban == 'email'){
			if($val == 'No'){
				$val = getData('users','email','where id ='.$uid);
				$ip = getData('users','ip','where id ='.$uid);
			} else {
				$ip = $dataBan[2];
			}
			
			$mysqli->query("INSERT INTO blocked_users(id,email,banned_date,banned_by,ip) VALUES
				('".$rand."','".$val."','".$time."','".$sm['user']['id']."','".$ip."')");
		}	
		if($ban == 'ip'){
			if($val == 'No'){
				$val = getData('users','ip','where id ='.$uid);
			}
			$mysqli->query("INSERT INTO blocked_ips(id,ip,banned_date,banned_by) VALUES
				('".$rand."','".$val."','".$time."','".$sm['user']['id']."')");
		}	

		$mysqli->query("DELETE FROM reports WHERE reported = '".$uid."'");	
		$mysqli->query("DELETE FROM users WHERE id = '".$uid."'");
		$mysqli->query("DELETE FROM spotlight WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM chat WHERE s_id = '".$uid."'");	
		$mysqli->query("DELETE FROM chat WHERE r_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_visits WHERE u1 = '".$uid."'");	
		$mysqli->query("DELETE FROM users_visits WHERE u2 = '".$uid."'");			
		$mysqli->query("DELETE FROM users_likes WHERE u1 = '".$uid."'");
		$mysqli->query("DELETE FROM users_likes WHERE u2 = '".$uid."'");		
		$mysqli->query("DELETE FROM users_photos WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_profile_questions WHERE uid = '".$uid."'");
		$mysqli->query("DELETE FROM users_interest WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_chats WHERE uid = '".$uid."'");
		$mysqli->query("DELETE FROM users_withdraw WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_verification WHERE uid = '".$uid."'");
		$mysqli->query("DELETE FROM users_premium WHERE uid = '".$uid."'");	
		$mysqli->query("DELETE FROM users_videocall WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_story WHERE uid = '".$uid."'");
		$mysqli->query("DELETE FROM users_notifications WHERE uid = '".$uid."'");

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
			$activity = 'User ID ('.$uid.') has been deleted from the database';
			activity('system',$activity,'Deleted '.$uid.'');	
		}	

	break;	
}
//CLOSE DB CONNECTION
$mysqli->close();