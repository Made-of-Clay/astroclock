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

			<article id="info">
				<h2>Welcome!</h2>
				<p>Greetings and welcome to Adam&#39;s Astro Clock! The intentions of this project are primarily to educate and demonstrate the wonderful nature of astronomical clocks to any who are interested. It is my hope that you will find this interesting and educational.</p>

				<h2>What are astronomical clocks?</h2>
				<p>Astronomical clocks do not just tell the time of day. They can tell the time of month and year as well. For time of month, the most &ldquo;natural&rdquo; way, that is to say, the most nature-like way is with the lunar phases.</p>

				<h2>Adam&rsquo;s Astro Clock v1.0</h2>
				<p>(put this text in separate box next to universal content so that future versions are relevant with text)</p>
				<p>This clock uses the moon phases to tell what phase the moon is in and, for anyone knowledgeable of lunar phases, the time of month.</p>
				<blockquote><i>Technical Jargon: </i>This is being calculated using a PHP script translated from Perl by Stephen A. Zarkos. There is some fine tuning that needs done to make it more accurate, but the results so far are pleasing. In the next version, I would like to use SVG to dynamically calculate the placement of the lunar phases. Currently, I am using a PNG image sprite, so I am limited to whatever images I have created already. The only drawback to using SVG as of now is that many mobile devices <u>will not render SVG</u> yet, which is greatly disappointing.</blockquote>
				<p>The time of year can be displayed as well using the zodiacal constellations. The chosen constellation telling the time of year is whichever constellation is at the meridian with the sun at noon. Knowing the full circle of constellations, ancient astronomers would know what constellation is up at midnight at a given time of year, and be able to deduce that the sun meets the meridian while &ldquo;in&rdquo; the constellation opposite the midnight constellation. The symbols used on Adam&#39;s Astro Clock are the astrological signs, as opposed to the actual constellations, but this is only for representational purposes. I would like for future versions to have the actual start patterns that make up the constellation so that they may be a little more easily recognizable to those who know the constellations better than the zodiac iconography.</p>

				<h2>Why astronomical clocks?</h2>
				<p>Starting off as vanity projects (to some extent), they showcased a certain builder&rsquo;s expertise and skills in mechanics, planning, and so many other factors that play into building these outstanding devices. If the project was not focusing on the builder, it was showing off the exuberant wealth of the project&rsquo;s patron, because these clocks simply were not cheap.</p>
				<p>The Gothic era offered more interest in philosophical implications behind the astronomical clocks. In a highly Christian age, people fancied the idea of an ordered cosmos. The astronomical clock was a great representation of how super complex devices must have some governing intelligence behind them. Eventually, these clocks would fade out of focus for a couple of centuries only to be brought back during the Renaissance.</p>
				<p>During the Renaissance, the astronomical clocks came back into focus as education devices, teaching rather than supporting philosophical notions (primarily, at least). This is when the Prague Astronomical Clock was built, and when many others were built initially. Not many clocks exist, and the Prague clock is easily the oldest surviving. Others have been rebuilt based on specifications.</p>

				<h2>Why I chose to build Adam&#39;s Astro Clock</h2>
				<p>I first learned about astronomical clocks in a Northern Renaissance art history class. The Prague Astronomical Clock was my first, and it captured my attention immediately. I wrote a report on timekeeping methods and particularly relished the study put into the astronomical clock. These kinds of clocks have a more broad focus than just daily chronometry (time-telling), and almost always focusing on celestial events (hense &quot;astronomical&quot;).</p>
				<p>At some point early on in my web career, I decided to make a web-based astronomical clock. It would be a few years before I would get serious about it, but that has worked to my benefit. My abilities now are considerably more suitable for the challenge.</p>

				<h2>When were astronomical clocks built?</h2>
				<p>Astronomical clocks started gaining popularity first in the Gothic Era. The clocks had more of a philosophical use then, as the concept of an ordered universe was appealing to the Christians of the time. In addition to the philosophical value, the clocks did well to bolster the prestige of both the clockmaker &amp; the patron; these clocks were anything but cheap and easy to build.</p>
				<p>After the clocks began to fade from popularity, a couple centuries passed and the Renaissance Age came, bringing with it new-found interest in the educational value of these extraordinary clocks.</p>

				<h2>Where are there astronomical clocks today?</h2>
				<p>The oldest surviving astronomical clock is currently in Prague Town Square and was built during the Renaissance Era. Most other clocks do not exist in their original state, but have been recreated based on detailed documentation.</p>
				<p>One notable clock exists in the Cathedrale Notre-Dame in France. It is the 3<sup>rd</sup> version of the clock. More can be read here [link] on this clock. Another is the Olomouc clock in the Czec Republic, which is a surviving piece from the first years of communism. More details can be studied here [outsideprague.com/Olomouc/astronomical_clock.html].</p>
				<p>There are some modern astro cock builders today. One hobbyist I discovered [astroclocks.nl] has several versions of actual mechanical clocks he has built. I may be looking to some of his designs for inspiration. Aside from hobbyist clockmakers, astronomical clocks can be purchased at places with astronomy-related products.&nbsp;</p>
			</article>

			<footer>&copy; 2013. Project by <a href="http://adamleis.com" class="external">Adam Leis</a></footer>
		</div><!-- End #content -->
	</div><!-- End #container -->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.10.2.min.js"><\/script>')</script>
	<script src="js/main.js"></script>
</body>
</html><?php } ?>