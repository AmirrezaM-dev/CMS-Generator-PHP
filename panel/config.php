<?php
	$sub_name="panel_";
	class clientInfo{
		private $BrowsersArr,$httpUserAgent,$user_browser,$OsArr,$user_os;
		public function UserIP(){
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				//ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				//ip pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
		public function UserBrowsers(){
			$this->httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$this->user_browser = "Unknown Browser";
			if(preg_match('/MSIE/i',$this->httpUserAgent) && !preg_match('/Opera/i',$this->httpUserAgent)){
			  $this->user_browser = 'Internet Explorer';
			}elseif(preg_match('/Firefox/i',$this->httpUserAgent)){
			  $this->user_browser = 'Mozilla Firefox';
			}elseif(preg_match('/OPR/i',$this->httpUserAgent)){
			  $this->user_browser = 'Opera';
			}elseif(preg_match('/Chrome/i',$this->httpUserAgent) && !preg_match('/Edge/i',$this->httpUserAgent)){
			  $this->user_browser = 'Google Chrome';
			}elseif(preg_match('/Safari/i',$this->httpUserAgent) && !preg_match('/Edge/i',$this->httpUserAgent)){
			  $this->user_browser = 'Apple Safari';
			}elseif(preg_match('/Netscape/i',$this->httpUserAgent)){
			  $this->user_browser = 'Netscape';
			}elseif(preg_match('/Edge/i',$this->httpUserAgent)){
			  $this->user_browser = 'Edge';
			}elseif(preg_match('/Trident/i',$this->httpUserAgent)){
			  $this->user_browser = 'Internet Explorer';
			}
			return $this->user_browser;
		}
		public function UserOs() {
			$this->httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$this->user_os = "Unknown OS Platform";
			$this->OsArr = array(
				'/windows nt 10/i' => 'Windows 10',
				'/windows nt 6.3/i' => 'Windows 8.1',
				'/windows nt 6.2/i' => 'Windows 8',
				'/windows nt 6.1/i' => 'Windows 7',
				'/windows nt 6.0/i' => 'Windows Vista',
				'/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
				'/windows nt 5.1/i' => 'Windows XP',
				'/windows xp/i' => 'Windows XP',
				'/windows nt 5.0/i' => 'Windows 2000',
				'/windows me/i' => 'Windows ME',
				'/win98/i' => 'Windows 98',
				'/win95/i' => 'Windows 95',
				'/win16/i' => 'Windows 3.11',
				'/macintosh|mac os x/i' => 'Mac OS X',
				'/mac_powerpc/i' => 'Mac OS 9',
				'/linux/i' => 'Linux',
				'/ubuntu/i' => 'Ubuntu',
				'/iphone/i' => 'iPhone',
				'/ipod/i' => 'iPod',
				'/ipad/i' => 'iPad',
				'/android/i' => 'Android',
				'/blackberry/i' => 'BlackBerry',
				'/webos/i' => 'Mobile'
			);
			foreach($this->OsArr as $regex => $value){
				if(preg_match($regex, $this->httpUserAgent)){
					$this->user_os = $value;
				}
			}
			return $this->user_os;
		}
		public function str_help($str){
			return htmlentities(stripslashes(htmlspecialchars($str)),ENT_QUOTES,"UTF-8");
		}
	}
	function str_help($str){
		return preg_replace('/[\r\n]+/', '<br>', htmlentities(stripslashes(htmlspecialchars($str)),ENT_QUOTES,"UTF-8"));
	}
	$last_name=[
		"permissions"=>[
			"table_column_permission"=>"table_column",
			"tables_permission"=>"table",
			"columns_permission"=>"column",
			"menu_permission"=>"menus"
		]
	];
	$data_text=[
		"fa"=>[
			"a"=>"انتخاب همه",
			"-1"=>"کامل",
			"c"=>"افزودن",
			"create"=>"افزودن",
			"r"=>"خواندن",
			"read"=>"خواندن",
			"u"=>"ویرایش",
			"update"=>"ویرایش",
			"d"=>"حذف",
			"delete"=>"حذف",
			'0'=>'0',
			'1'=>'1',
			'2'=>'2',
			'3'=>'3',
			'999'=>'999',
			'-999'=>'-999'
		],
		"en"=>[
			"a"=>"Select All",
			"-1"=>"Complete",
			"c"=>"Create",
			"create"=>"Create",
			"r"=>"Read",
			"read"=>"Read",
			"u"=>"Update",
			"update"=>"Update",
			"d"=>"Delete",
			"delete"=>"Delete",
			'0'=>'0',
			'1'=>'1',
			'2'=>'2',
			'3'=>'3',
			'999'=>'999',
			'-999'=>'-999'
		]
	];
	$permission_power_list=[
		["Select All","-1","انتخاب همه"],
		["0","0"],
		["1","1"],
		["2","2"],
		["3","3"],
		["999","999"],
		["-999","-999"]
	];
	$permission_name_list=[
		["create"],
		["read"],
		["update"],
		["delete"],
		["-1"]
	];
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	$audio_format=[".aa", "aac", "aax", "act", "aiff", "alac", "amr", "ape", "au", "awb", "dss", "flac", "gsm", "m4a", "m4b", "m4p", "mp3", "mpc", "ogg", "oga", "mogg", "opus", "ra", "raw", "rf64", "sln", "tta", "voc", "vox", "wav", "wma", "wv", "8svx", "cda"];$video_format=[".webm", "mkv", "flv", "flv", "vob", "ogv", "drc", "gif", "gifv", "mng", "avi", "mts", "m2ts", "ts", "mov", "qt", "wmv", "yuv", "rm", "rmvb", "viv", "asf", "amv", "mp4", "m4p (with drm)", "m4v", "mpg", "mp2", "mpeg", "mpe", "mpv", "mpg", "mpeg", "m2v", "m4v", "svi", "3gp", "3g2", "mxf", "roq", "nsv", "flv", "f4v", "f4p", "f4a", "f4b"];$zipped_format=[".?mn", "?q?", "7z", "aapkg", "aac", "ace", "alz", "apk", "appx", "at3", "bke", "arc", "arc", "arj", "ass", "sas", "b", "ba", "big", "bin", "bjsn", "bkf", "bzip2", "bld", "cab", "c4", "cals", "xaml", "clipflair", "clipflair.zip", "cpt", "sea", "daa", "deb", "dmg", "ddz", "dn", "dpe", "egg", "egt", "ecab", "ezip", "esd", "ess", "exe", "flipchart", "gbp", "gbs", "ggp", "gsc", "gho", "ghs", "gif", "gzip", "gz", "html", "ipg", "jar", "lbr", "lawrence", "lbr", "lqr", "lha", "lzh", "lzip", "lz", "lzo", "lzma", "lzx", "mbw", "mhtml", "mpq", "bin", "nl2pkg", "nth", "oar", "osk", "osr", "osz", "pak", "par", "par2", "paf", "pea", "php", "pyk", "pk3", "pk4", "py", "pyw", "rar", "rag", "rags", "rax", "rblx", "rpm", "sb", "sb2", "sb3", "sen", "sit", "sitx", "sis", "sisx", "skb", "sq", "swm", "szs", "tar", "tgz", "tar.gz", "tb", "tib", "uha", "uue", "viv", "vol", "vsa", "wax", "wim", "xap", "xz", "z", "zoo", "zip"];$disk_format=[".iso", "nrg", "img", "adf", "adz", "dms", "dsk", "d64", "sdi", "mds", "mdx", "dmg", "cdi", "cue", "cif", "c2d", "daa", "b6t"];$threeds_format=[".3dxml", "3mf", "acp", "amf", "aec", "ar", "art", "asc", "asm", "bin, bim", "brep", "c3d", "ccc", "ccm", "ccs", "cad", "catdrawing", "catpart", "catproduct", "catprocess", "cgr", "ckd", "ckt", "co", "drw", "dft", "dgn", "dgk", "dmt", "dxf", "dwb", "dwf", "dwg", "easm", "edrw", "emb", "eprt", "escpcb", "escsch", "esw", "excellon", "exp", "f3d", "fcstd", "fm", "fmz", "g", "gbr", "glm", "grb", "gtc", "iam", "icd", "idw", "ifc", "iges", "io", "ipn", "ipt", "jt", "mcd", "mdg", "model", "ocd", "par", "pipe", "pln", "prt", "psm", "psmodel", "pwi", "pyt", "skp", "rlf", "rvm", "rvt", "rfa", "s12", "scad", "scdoc", "sldasm", "slddrw", "sldprt", "dotxsi", "step", "stl", "std", "tct", "tcw", "unv", "vc6", "vlm", "vs", "wrl", "x_b", "x_t", "xe", "zofzproj"];$electronic_design_format=[".brd", "bsdl", "cdl", "cpf", "def", "dspf", "edif", "fsdb", "gdsii", "hex", "lef", "lib", "ms12", "oasis", "openaccess", "psf", "psfxl", "sdc", "sdf", "spef", "spi, cir", "srec, s19", "sst2", "stil", "sv", "s*p", "tlf", "upf", "v", "vcd", "vhd", "vhdl", "wgl"];$database_format=[".4db" , "4dd" , "4dindy" , "4dindx" , "4dr" , "accdb" , "accde" , "adt" , "apr" , "box" , "chml" , "daf" , "dat" , "dat" , "db" , "db" , "dbf" , "dta" , "egt" , "ess" , "eap" , "fdb" , "fdb" , "fp" , "fp3" , "fp5" , "fp7" , "frm" , "gdb" , "gtable" , "kexi" , "kexic" , "kexis" , "ldb" , "lirs" , "mda" , "mdb" , "adp" , "mde" , "mdf" , "myd" , "myi" , "ncf" , "nsf" , "ntf" , "nv2" , "odb" , "ora" , "pcontact" , "pdb" , "pdi" , "pdx" , "prc" , "sql" , "rec" , "rel" , "rin" , "sdb" , "sdf" , "sqlite" , "udl" , "wadata" , "waindx" , "wamodel" , "wajournal" , "wdb" , "wmdb"];$document_format=[".0" , "1st" , "600" , "602" , "abw" , "acl" , "afp" , "ami" , "amigaguide", "ans" , "asc" , "aww" , "ccf" , "csv" , "cwk" , "dbk" , "dita" , "doc" , "docm" , "docx" , "dot" , "dotx" , "dwd" , "egt" , "epub" , "ezw" , "fdx" , "ftm" , "ftx" , "gdoc" , "html" , "hwp" , "hwpml" , "log" , "lwp" , "mbp" , "md" , "me" , "mcw" , "mobi" , "nb" , "nb" , "nbp" , "neis" , "nt" , "nq" , "odm" , "odoc" , "odt" , "osheet" , "ott"];$image_formats=["jfif", "jpeg", "exif", "tiff", "gif", "bmp", "png", "ppm", "pgm", "pbm", "pnm", "webp", "heif", "bat", "cgm", "svg"];
	function timetostr($datetime) {
		if($datetime>0){
			$now = new DateTime;
			$ago = new DateTime(date("Y/m/d H:i:s",$datetime));
			$diff = $now->diff($ago);
			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;
			$string = array(
				'y' => ($GLOBALS['user_language']=="en" ? "year":"سال"),
				'm' => ($GLOBALS['user_language']=="en" ? "month":"ماه"),
				'w' => ($GLOBALS['user_language']=="en" ? "week":"هفته"),
				'd' => ($GLOBALS['user_language']=="en" ? "day":"روز"),
				'h' => ($GLOBALS['user_language']=="en" ? "hour":"ساعت"),
				'i' => ($GLOBALS['user_language']=="en" ? "minute":"دقیقه"),
				's' => ($GLOBALS['user_language']=="en" ? "second":"ثانیه"),
			);
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? ($GLOBALS['user_language']=="en" ? "s":"") : '');
				} else {
					unset($string[$k]);
				}
			}
			$string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ($GLOBALS['user_language']=="en" ? " ago":" قبل") : ($GLOBALS['user_language']=="en" ? "just now":"همین الان");
		}else{
			echo ($GLOBALS['user_language']=="en" ? "never":"هرگز");
		}
	}
	function sizeToString($bytes, $precision = 0) {
		$units = array(($GLOBALS['user_language']=="en" ? "B":"بایت"), ($GLOBALS['user_language']=="en" ? "KB":"کیلوبایت"), ($GLOBALS['user_language']=="en" ? "MB":"مگابایت"), ($GLOBALS['user_language']=="en" ? "GB":"گیگابایت"), ($GLOBALS['user_language']=="en" ? "TB":"ترابایت"));
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow));
		return round($bytes, $precision) . ' ' . $units[$pow];
	}
?>