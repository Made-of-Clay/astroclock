<?php
###############################################
#
# Time Machine now returns values I need for changing clock
# - change values on clock
# - add some transition effect for flair (addClass, transition, change data, removeClass, transition)
#
###############################################
date_default_timezone_set('America/New_York');

# Error Checking
if(isset($_GET['errors'])){
	ini_set('display_errors',1); 
	error_reporting(E_ALL);
}

function sanitize($dirty){
	return htmlspecialchars(strip_tags(trim($dirty)));
}

// if whatever get exists, run script but not markup
// else run markup, not script
$get = array();
foreach($_GET as $k=>$v){
	$get[sanitize($k)] = sanitize($v);
}
$isTmach = ($get['tmach'] == '1') ? true : false;

# Day of Year calculation
//$DoY = date('z');
$DoY = ($isTmach) ? date("z", mktime(0, 0, 0, $get['gotoMonth'], $get['gotoDay'], $get['gotoYear'])) : date('z');

# Moon Phase Script
require 'moonphase.inc.php';

$jsDate = $get['gotoYear'] . '-' . $get['gotoMonth'] . '-' . $get['gotoDay'];
$date = ($isTmach) ? $jsDate.date(' h:i:s T') : date('Y-m-d h:i:s T');
$moondata = phase(strtotime($date)); // only need indeces 0 & 2

# Moon Phase Calculation
require 'moon_check.php';

if($isTmach){ // only entered if AJAX calls this page
	$return = array(
		'DoY' 		=> $DoY,
		'msprite' 	=> $msprite,
		'moonage'	=> $md
	);
	
	#echo '<pre>'.print_r($get,true).'</pre>';
	echo json_encode($return);
} else {
	# Zodiac Markup
	$zod_arr = array(
		'aquarius' 		=> '9810',
		'pisces' 		=> '9811',
		'aries' 		=> '9800',
		'taurus' 		=> '9801',
		'gemini' 		=> '9802',
		'cancer' 		=> '9803',
		'leo' 			=> '9804',
		'virgo' 		=> '9805',
		'libra' 		=> '9806',
		'scorpius' 		=> '9807',
		'sagittarius' 	=> '9808',
		'capricorn' 	=> '9809'
	);
	$zods = '';
	/*for($x=1, $zods=''; $x<=sizeof($zod_arr); $x++){
		$zods .= '<li class="zicon">&#'.$zod_arr[$x-1].';</li>';
	}*/
	foreach($zod_arr as $k=>$v){
		$zods .= '<li class="zicon" title="'.ucfirst($k).'">&#'.$v.';</li>';
	}

	# Select Options Markup(s)
	for($x=1, $theYear = date('Y'), $gotoYear = $gotoMonth = $gotoDay = '', $yrCnt = 5, $moCnt = 12, $daCnt = 31; $x<=31; $x++, $theYear++){
		if($x <= $yrCnt){
			$gotoYear .= '<option value="'.$theYear.'">'.$theYear.'</option>';
		}
		if($x <= $moCnt){
			$moStr = date('F',mktime(0,0,0,$x,1,$theYear));
			$gotoMonth .= '<option value="'.$x.'">'.$moStr.' ('.$x.')</option>';
		}
		if($x <= $daCnt){
			$daStr = date('l',mktime(0,0,0,1,$x,$theYear));
			$gotoDay .= '<option value="'.$x.'">'.$x.'</option>';
		}
	}
	 
	# Clock Numbers
	$nums = '';
	for($x=1;$x<=12;$x++){
		$nums .= '<li class="number"></li>';
	}
// end else bracket after </html>
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 lt-ie10 lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 lt-ie10 lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 lt-ie10 lt-ie9" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9 lt-ie10" lang="en"> <![endif]-->
<!--[if IE 10]>   <html class="no-js ie10" lang="en"> <![endif]-->
<!--[if gt IE 10]><!-->
<html lang="en"><!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
	<title>Adam's Astro Clock</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<link rel="stylesheet" href="css/resetPlus_h5bp.css" media="screen" />
	<link rel="stylesheet" href="css/main.css" media="screen" />
	<style id="dynamicCSS">/* Dynamic CSS Stuff (because I am not yet using Sass) */
	#zodiac {
		-webkit-transform: rotate(-<?php echo $DoY; ?>deg);
		transform: rotate(-<?php echo $DoY; ?>deg); }
	#clock:after { background: <?php echo $msprite; ?>; }
	</style>

	<script src="js/modernizr-2.6.2.js"></script>
</head>

<body>
	<!--[if lt IE 9]><p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p><![endif]-->
	<div id="container">
		<h1 id="theTitle">Adam's <span>Astro</span> Clock</h1>
		<div id="astro_wrap" class="clearfix"><!-- everything positioned in here -->
			<div id="rays">
				<div id="coronaLarge" class="ray"></div>
				<div id="coronaSmall" class="ray"></div>
			</div><!-- outter-most ring; lowest z-index -->
			<ul id="zodiac"><!-- holds inner ring of symbols; rotates based on DoY -->
				<?php echo $zods; ?>
			</ul><!-- End #zodiac -->

			<div id="clock"><!-- holds numbers and hands; main ref point of clock; bg-img is moon phase -->
				<ol id="numbers">
					<?php echo $nums; ?>
				</ol><!-- End #numbers -->
				<div id="hourHand" class="hands"></div>
				<div id="minuteHand" class="hands"></div>
				<div id="secondHand" class="hands"></div>
			</div><!-- End #clock -->
		</div><!-- End #astro_wrap -->

		<div id="content">
			<fieldset id="time_machine">
				<select name="gotoYear" id="gotoYear" class="toTime">
					<option value="0">Year</option>
					<?php echo $gotoYear; ?>
				</select><!-- End #gotoYear -->

				<select name="gotoMonth" id="gotoMonth" class="toTime">
					<option value="0">Month</option>
					<?php echo $gotoMonth; ?>
				</select><!-- End #gotoMonth -->
		
				<select name="gotoDay" id="gotoDay" class="toTime">
					<option value="0">Day</option>
					<?php echo $gotoDay; ?>
				</select><!-- End #gotoDay -->

				<button id="time_travel">Time Travel!</button>
			</fieldset><!-- End #time_machine -->
		<div style="display:none">
			<p>High Life Cosby sweater pariatur, jean shorts Godard reprehenderit aute lo-fi laboris wayfarers direct trade. DIY synth master cleanse, elit odio ennui aesthetic aliqua distillery authentic Vice fugiat. Quinoa nihil cornhole accusamus raw denim Vice meggings Tumblr YOLO, skateboard bicycle rights 8-bit trust fund commodo. Gentrify dreamcatcher yr, hashtag gluten-free accusamus Portland roof party readymade meggings squid post-ironic. Semiotics aliqua Pitchfork bitters yr, nihil esse actually. Lo-fi sartorial sed Tonx officia biodiesel, 8-bit bespoke polaroid Tumblr twee shabby chic. Biodiesel synth cray, selvage duis Godard nesciunt.</p>
			<p>Nostrud tempor cray, PBR Wes Anderson farm-to-table non beard lo-fi selfies in. Street art occupy consectetur locavore photo booth distillery Neutra Tumblr. Bicycle rights fanny pack messenger bag, Tonx do viral chambray pop-up before they sold out duis trust fund labore. Next level Pitchfork labore, quinoa bespoke tousled raw denim readymade eiusmod actually Tumblr. Lomo veniam Marfa, literally shabby chic scenester nisi kogi Schlitz reprehenderit qui cred you probably haven't heard of them enim pariatur. Cred minim Etsy duis before they sold out, banh mi kitsch. Irony sed sartorial, cupidatat master cleanse mixtape gastropub keffiyeh.</p>
			<p>Reprehenderit gluten-free viral, est et kitsch kale chips PBR organic freegan pariatur. Drinking vinegar ethical keytar, accusamus American Apparel lo-fi labore aesthetic typewriter do umami sartorial Odd Future pop-up. Brunch qui tempor, retro ethical meggings elit velit 90's aesthetic mumblecore placeat cardigan Intelligentsia. Before they sold out aute Terry Richardson, direct trade cornhole ex tote bag chambray Neutra. Lo-fi vegan banjo, raw denim mumblecore disrupt officia Williamsburg ethnic Marfa American Apparel tote bag. Raw denim eu irure pork belly, cornhole freegan stumptown. Incididunt occaecat hella, McSweeney's biodiesel ex church-key before they sold out tempor sustainable synth Pinterest.</p>
		</div>
		<footer>
			&copy; 2013. Project by <a href="http://adamleis.com" class="external">Adam Leis</a>
		</footer>
		</div><!-- End #content -->
	</div><!-- End #container -->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.10.2.min.js"><\/script>')</script>
	<script src="js/main.js"></script>
</body>
</html><?php } ?>