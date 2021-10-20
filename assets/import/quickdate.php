<?php
error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json; charset=utf-8');
require_once('../includes/core.php');

$sm['admin_ajax'] = true;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$arr = array();
	$arr['error'] = 0;
	$qd_db_host = secureEncode($_POST['db_host']);
	$qd_db_name = secureEncode($_POST['db_name']);
	$qd_db_username = secureEncode($_POST['db_user']);
	$qd_db_password = secureEncode($_POST['db_pass']);
	$qd_url = secureEncode($_POST['url']);	

	$method = secureEncode($_POST['method']);	

	$check_bar = substr($qd_url, -1);
	if($check_bar != '/'){
		$qd_url = $qd_url.'/';	
	}

	$qd_mysqli = new mysqli($qd_db_host, $qd_db_username, $qd_db_password,$qd_db_name);
	if (mysqli_connect_errno($qd_mysqli)) {
	    $arr['error'] = 1;
	    $arr['msg'] = mysqli_connect_error();
	    exit(json_encode($arr));
	}
	$qd_mysqli->set_charset('utf8mb4');
	
	if(empty($qd_url)){
		$arr['error'] = 1;
		$arr['msg'] = 'Site url cant be empty, its needed for find the location of the media files';
		exit(json_encode($arr));
	}

	if($method == 'check'){
		$installUsers = 0;
		$query = $qd_mysqli->query("SELECT email,username,avater,id FROM users ORDER BY id DESC");
		while($row = $query->fetch_assoc()){
			$checkUser = getData('users','email','WHERE email = "'.$row['email'].'"');
			if($checkUser == 'noData'){
				$installUsers++;
				$row['status'] = 'New User';
				$arr['import'][] = $row['id'];
			} else {
				$row['status'] = 'Already in database';
			}
			$arr['install_users'] = $installUsers;
			$arr['users'][] = $row;
		}
		$arr['total'] = $query->num_rows;
		echo json_encode($arr);
	}

	if($method == 'add'){
		$id = secureEncode($_POST['id']);
		$mail = secureEncode($_POST['mail']);	

		$q = $qd_mysqli->query("SELECT * FROM users WHERE id = '".$id."'");
		if(isset($q->num_rows) && !empty($q->num_rows)){
			while($row = $q->fetch_assoc()){
				$arr['username'] = $row['username'];

				$pswd = $row['password'];
				$lang = $sm['plugins']['settings']['defaultLang'];

				$name = '';
				if(!empty($row['first_name'])){
					$name = $row['first_name'];
				}
				if(!empty($row['last_name'])){
					$name = $name.' '.$row['last_name'];
				}			

				$birthDate = $row['birthday'];
				$birthDate = explode("-", $birthDate);
				$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
				? ((date("Y") - $birthDate[0]) - 1)
				: (date("Y") - $birthDate[0]));
				$birthday = date('F', mktime(0, 0, 0, $birthDate[1], 10)).' '.$birthDate[2].', '.$birthDate[0];
				
				if($row['gender'] == 'Male' || $row['gender'] == 4525){
					$gender = 1;
					$looking = 2;
				} else {
					$gender = 2;
					$looking = 1;
				}
				
				$city = '';
				$country = code_to_country($row['country']);

				$fake = 0;
				if($row['src'] == 'Fake'){
					$fake = 1;
				}

				$avater = $row['avater'];
				$today = date('w');
				$date = date('m/d/Y', time());
				$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$country;
				$sage = '18,30,1';
				$password = '';

				$photos = getQDPhotos($row['id']);

				$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,fake,online_day,password,ip,last_access,username,join_date_time,imported) VALUES ('".$name."', '".$row['email']."','".$pswd."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$row['lat']."','".$row['lng']."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','".$sage."',0,'".$fake."','".$today."','".$password."','".$row['ip']."','".time()."','".$row['username']."','".time()."','quickdate')";					
				if ($mysqli->query($query) === TRUE) {
					$last_id = $mysqli->insert_id;
					
					$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");

					$free_premium = 0;
					$allG = count(siteGenders($lang));
					$allG = $allG + 1;					
					if($sm['plugins']['rewards']['freePremiumGender'] == $gender || $sm['plugins']['rewards']['freePremiumGender'] == $allG){
						$free_premium = $sm['plugins']['rewards']['freePremium'];
					}
					$time = time();	
					$extra = 86400 * $free_premium;
					$premium = $time + $extra;
					$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");

					if(count($photos) > 0){
						$a = 0;
						foreach ($photos as $p) {
							$a++;
							$file_name = rand(0,19992).time().'.jpg';
							$thumb_name = 'thumb_'.$file_name;
							$filepath = sprintf('../sources/uploads/%s', $file_name);
							$thumbpath = sprintf('../sources/uploads/%s', $thumb_name);
							$photo = $sm['config']['site_url'].'assets/sources/uploads/'.$file_name;
							file_put_contents($filepath, file_get_contents($qd_url.$p));

							$thumbSrc = str_replace('_full', '_avater', $p);
							file_put_contents($thumbpath, file_get_contents($qd_url.$thumbSrc));

							$thumb = $sm['config']['site_url'].'assets/sources/uploads/thumb_'.$file_name;
							if($a == 1){
								$mysqli->query("INSERT INTO users_photos (u_id,photo,profile,thumb,approved,time) VALUES ('".$last_id."','".$photo."',1,'".$thumb."',1,'".time()."')");
							} else {
								$mysqli->query("INSERT INTO users_photos (u_id,photo,profile,thumb,approved,time) VALUES ('".$last_id."','".$photo."',0,'".$thumb."',1,'".time()."')");
							}						
						}
					}

					if($fake == 1){
						$file_name = rand(0,19992).time().'.jpg';
						$filepath = sprintf('../sources/uploads/%s', $file_name);
						$photo = $sm['config']['site_url'].'assets/sources/uploads/'.$file_name;
						file_put_contents($filepath, file_get_contents($qd_url.$avater));
						$mysqli->query("INSERT INTO users_photos (u_id,photo,profile,thumb,approved,fake,time) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1,1,'".time()."')");
					}

					$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
					$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");	

					if($sm['plugins']['email']['enabled'] == 'Yes' && $mail == 'Yes'){
						welcomeMailNotification($name,$row['email'],'');						
					}
					echo json_encode($arr);
				}			
			}
		}
	}	
}


function code_to_country( $code ){
$code = strtoupper($code);
$countryList = array(
    'AF' => 'Afghanistan',
    'AX' => 'Aland Islands',
    'AL' => 'Albania',
    'DZ' => 'Algeria',
    'AS' => 'American Samoa',
    'AD' => 'Andorra',
    'AO' => 'Angola',
    'AI' => 'Anguilla',
    'AQ' => 'Antarctica',
    'AG' => 'Antigua and Barbuda',
    'AR' => 'Argentina',
    'AM' => 'Armenia',
    'AW' => 'Aruba',
    'AU' => 'Australia',
    'AT' => 'Austria',
    'AZ' => 'Azerbaijan',
    'BS' => 'Bahamas the',
    'BH' => 'Bahrain',
    'BD' => 'Bangladesh',
    'BB' => 'Barbados',
    'BY' => 'Belarus',
    'BE' => 'Belgium',
    'BZ' => 'Belize',
    'BJ' => 'Benin',
    'BM' => 'Bermuda',
    'BT' => 'Bhutan',
    'BO' => 'Bolivia',
    'BA' => 'Bosnia and Herzegovina',
    'BW' => 'Botswana',
    'BV' => 'Bouvet Island (Bouvetoya)',
    'BR' => 'Brazil',
    'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
    'VG' => 'British Virgin Islands',
    'BN' => 'Brunei Darussalam',
    'BG' => 'Bulgaria',
    'BF' => 'Burkina Faso',
    'BI' => 'Burundi',
    'KH' => 'Cambodia',
    'CM' => 'Cameroon',
    'CA' => 'Canada',
    'CV' => 'Cape Verde',
    'KY' => 'Cayman Islands',
    'CF' => 'Central African Republic',
    'TD' => 'Chad',
    'CL' => 'Chile',
    'CN' => 'China',
    'CX' => 'Christmas Island',
    'CC' => 'Cocos (Keeling) Islands',
    'CO' => 'Colombia',
    'KM' => 'Comoros the',
    'CD' => 'Congo',
    'CG' => 'Congo the',
    'CK' => 'Cook Islands',
    'CR' => 'Costa Rica',
    'CI' => 'Cote d\'Ivoire',
    'HR' => 'Croatia',
    'CU' => 'Cuba',
    'CY' => 'Cyprus',
    'CZ' => 'Czech Republic',
    'DK' => 'Denmark',
    'DJ' => 'Djibouti',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'EC' => 'Ecuador',
    'EG' => 'Egypt',
    'SV' => 'El Salvador',
    'GQ' => 'Equatorial Guinea',
    'ER' => 'Eritrea',
    'EE' => 'Estonia',
    'ET' => 'Ethiopia',
    'FO' => 'Faroe Islands',
    'FK' => 'Falkland Islands (Malvinas)',
    'FJ' => 'Fiji the Fiji Islands',
    'FI' => 'Finland',
    'FR' => 'France, French Republic',
    'GF' => 'French Guiana',
    'PF' => 'French Polynesia',
    'TF' => 'French Southern Territories',
    'GA' => 'Gabon',
    'GM' => 'Gambia the',
    'GE' => 'Georgia',
    'DE' => 'Germany',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GR' => 'Greece',
    'GL' => 'Greenland',
    'GD' => 'Grenada',
    'GP' => 'Guadeloupe',
    'GU' => 'Guam',
    'GT' => 'Guatemala',
    'GG' => 'Guernsey',
    'GN' => 'Guinea',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HT' => 'Haiti',
    'HM' => 'Heard Island and McDonald Islands',
    'VA' => 'Holy See (Vatican City State)',
    'HN' => 'Honduras',
    'HK' => 'Hong Kong',
    'HU' => 'Hungary',
    'IS' => 'Iceland',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'IR' => 'Iran',
    'IQ' => 'Iraq',
    'IE' => 'Ireland',
    'IM' => 'Isle of Man',
    'IL' => 'Israel',
    'IT' => 'Italy',
    'JM' => 'Jamaica',
    'JP' => 'Japan',
    'JE' => 'Jersey',
    'JO' => 'Jordan',
    'KZ' => 'Kazakhstan',
    'KE' => 'Kenya',
    'KI' => 'Kiribati',
    'KP' => 'Korea',
    'KR' => 'Korea',
    'KW' => 'Kuwait',
    'KG' => 'Kyrgyz Republic',
    'LA' => 'Lao',
    'LV' => 'Latvia',
    'LB' => 'Lebanon',
    'LS' => 'Lesotho',
    'LR' => 'Liberia',
    'LY' => 'Libyan Arab Jamahiriya',
    'LI' => 'Liechtenstein',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'MO' => 'Macao',
    'MK' => 'Macedonia',
    'MG' => 'Madagascar',
    'MW' => 'Malawi',
    'MY' => 'Malaysia',
    'MV' => 'Maldives',
    'ML' => 'Mali',
    'MT' => 'Malta',
    'MH' => 'Marshall Islands',
    'MQ' => 'Martinique',
    'MR' => 'Mauritania',
    'MU' => 'Mauritius',
    'YT' => 'Mayotte',
    'MX' => 'Mexico',
    'FM' => 'Micronesia',
    'MD' => 'Moldova',
    'MC' => 'Monaco',
    'MN' => 'Mongolia',
    'ME' => 'Montenegro',
    'MS' => 'Montserrat',
    'MA' => 'Morocco',
    'MZ' => 'Mozambique',
    'MM' => 'Myanmar',
    'NA' => 'Namibia',
    'NR' => 'Nauru',
    'NP' => 'Nepal',
    'AN' => 'Netherlands Antilles',
    'NL' => 'Netherlands the',
    'NC' => 'New Caledonia',
    'NZ' => 'New Zealand',
    'NI' => 'Nicaragua',
    'NE' => 'Niger',
    'NG' => 'Nigeria',
    'NU' => 'Niue',
    'NF' => 'Norfolk Island',
    'MP' => 'Northern Mariana Islands',
    'NO' => 'Norway',
    'OM' => 'Oman',
    'PK' => 'Pakistan',
    'PW' => 'Palau',
    'PS' => 'Palestinian Territory',
    'PA' => 'Panama',
    'PG' => 'Papua New Guinea',
    'PY' => 'Paraguay',
    'PE' => 'Peru',
    'PH' => 'Philippines',
    'PN' => 'Pitcairn Islands',
    'PL' => 'Poland',
    'PT' => 'Portugal, Portuguese Republic',
    'PR' => 'Puerto Rico',
    'QA' => 'Qatar',
    'RE' => 'Reunion',
    'RO' => 'Romania',
    'RU' => 'Russian Federation',
    'RW' => 'Rwanda',
    'BL' => 'Saint Barthelemy',
    'SH' => 'Saint Helena',
    'KN' => 'Saint Kitts and Nevis',
    'LC' => 'Saint Lucia',
    'MF' => 'Saint Martin',
    'PM' => 'Saint Pierre and Miquelon',
    'VC' => 'Saint Vincent and the Grenadines',
    'WS' => 'Samoa',
    'SM' => 'San Marino',
    'ST' => 'Sao Tome and Principe',
    'SA' => 'Saudi Arabia',
    'SN' => 'Senegal',
    'RS' => 'Serbia',
    'SC' => 'Seychelles',
    'SL' => 'Sierra Leone',
    'SG' => 'Singapore',
    'SK' => 'Slovakia',
    'SI' => 'Slovenia',
    'SB' => 'Solomon Islands',
    'SO' => 'Somalia',
    'ZA' => 'South Africa',
    'GS' => 'South Georgia and the South Sandwich Islands',
    'ES' => 'Spain',
    'LK' => 'Sri Lanka',
    'SD' => 'Sudan',
    'SR' => 'Suriname',
    'SJ' => 'Svalbard & Jan Mayen Islands',
    'SZ' => 'Swaziland',
    'SE' => 'Sweden',
    'CH' => 'Switzerland, Swiss Confederation',
    'SY' => 'Syrian Arab Republic',
    'TW' => 'Taiwan',
    'TJ' => 'Tajikistan',
    'TZ' => 'Tanzania',
    'TH' => 'Thailand',
    'TL' => 'Timor-Leste',
    'TG' => 'Togo',
    'TK' => 'Tokelau',
    'TO' => 'Tonga',
    'TT' => 'Trinidad and Tobago',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TM' => 'Turkmenistan',
    'TC' => 'Turks and Caicos Islands',
    'TV' => 'Tuvalu',
    'UG' => 'Uganda',
    'UA' => 'Ukraine',
    'AE' => 'United Arab Emirates',
    'GB' => 'United Kingdom',
    'US' => 'United States of America',
    'UM' => 'United States Minor Outlying Islands',
    'VI' => 'United States Virgin Islands',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VU' => 'Vanuatu',
    'VE' => 'Venezuela',
    'VN' => 'Vietnam',
    'WF' => 'Wallis and Futuna',
    'EH' => 'Western Sahara',
    'YE' => 'Yemen',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe'
);
if( !$countryList[$code] ) return $code;
else return $countryList[$code];
}

function getQDPhotos($user){
	global $qd_mysqli;
	$result = array();
	$q = $qd_mysqli->query("SELECT * FROM mediafiles WHERE user_id = '".$user."' ORDER BY id DESC");
	if(isset($q->num_rows) && !empty($q->num_rows)){
		while($r = $q->fetch_assoc()){
			$result[] = $r['file'];
		}		
	}
	return $result;	
}