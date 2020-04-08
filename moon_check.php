<?php
/**
 * New Moon Array
 * - temporary solution to calculating moon phases... must find more dynamic solution for 2015+
 * * check year against array
 * * loop through each - substr on "-"
 * * check index 0 for current month - if matched, save info, check next index, if no match, continue
 * * get day of year for today and matched date - find difference, calculate moon phase
 */
$bgPos = array('0 0','50% 0','100% 0','0 100%','50% 100%','100% 100%');
$bgImgChunks = 'url(images/sprite_chunks.png)';
$bgImgWaxCre = 'url(images/sprite_waxingCrescent.png)';
$bgImgWaxGib = 'url(images/sprite_waxingGibbous.png)';
$bgImgWanCre = 'url(images/sprite_waningCrescent.png)';
$bgImgWanGib = 'url(images/sprite_waningGibbous.png)';
$bgRepeat = ' no-repeat ';

$waxCres = $wanCres = $waxGib = $wanGib = array();
foreach($bgPos as $bgp){
	array_push($waxCres, $bgImgWaxCre.$bgRepeat.$bgp);
	array_push($wanCres, $bgImgWanCre.$bgRepeat.$bgp);
	array_push($waxGib, $bgImgWaxGib.$bgRepeat.$bgp);
	array_push($wanGib, $bgImgWanGib.$bgRepeat.$bgp);
}

$moon_sprite = array(
	'chunks' => array(
		'first_quarter'	=> $bgImgChunks.$bgRepeat.'136% 0',
		'new_moon'		=> $bgImgChunks.$bgRepeat.'50% 0',
		'last_quarter'	=> $bgImgChunks.$bgRepeat.'-32% 0'),
	'waxingCrescent'  => $waxCres,
	'waningCrescent'  => $wanCres,
	'waxingGibbous'  => $waxGib,
	'waningGibbous'  => $wanGib
);

$md = floor($moondata[2]); // moon age 0-29
if(isset($_GET['moonage'])) echo "md: $md<br>";
if(isset($_GET['moon_sprite'])) echo '<pre>'.print_r($moon_sprite).'</pre>';

$msprite = 'none';
if($md >= 0 && $md < 1 || $md >= 29 && $md < 30) { // new moon
	$msprite = $moon_sprite['chunks']['new_moon']; }
elseif($md >= 1 && $md < 2) {
	$msprite = $moon_sprite['waxingCrescent'][0]; }
elseif($md >= 2 && $md < 3) {
	$msprite = $moon_sprite['waxingCrescent'][1]; }
elseif($md >= 3 && $md < 4) {
	$msprite = $moon_sprite['waxingCrescent'][2]; }
elseif($md >= 4 && $md < 5) {
	$msprite = $moon_sprite['waxingCrescent'][3]; }
elseif($md >= 5 && $md < 6) {
	$msprite = $moon_sprite['waxingCrescent'][4]; }
elseif($md >= 6 && $md < 7) {
	$msprite = $moon_sprite['waxingCrescent'][5]; }
elseif($md >= 7 && $md < 8) { // first quarter
	$msprite = $moon_sprite['chunks']['first_quarter']; }
elseif($md >= 8 && $md < 9) {
	$msprite = $moon_sprite['waxingGibbous'][0]; }
elseif($md >= 9 && $md < 10) {
	$msprite = $moon_sprite['waxingGibbous'][1]; }
elseif($md >= 10 && $md < 11) {
	$msprite = $moon_sprite['waxingGibbous'][2]; }
elseif($md >= 11 && $md < 12) {
	$msprite = $moon_sprite['waxingGibbous'][3]; }
elseif($md >= 12 && $md < 13) {
	$msprite = $moon_sprite['waxingGibbous'][4]; }
elseif($md >= 13 && $md < 14) {
	$msprite = $moon_sprite['waxingGibbous'][5]; }
elseif($md >= 14 && $md < 15) { // full moon
	$msprite = 'none'; }
elseif($md >= 15 && $md < 16) {
	$msprite = $moon_sprite['waningCrescent'][0]; }
elseif($md >= 16 && $md < 17) {
	$msprite = $moon_sprite['waningCrescent'][1]; }
elseif($md >= 17 && $md < 18) {
	$msprite = $moon_sprite['waningCrescent'][2]; }
elseif($md >= 18 && $md < 19) {
	$msprite = $moon_sprite['waningCrescent'][3]; }
elseif($md >= 19 && $md < 20) {
	$msprite = $moon_sprite['waningCrescent'][4]; }
elseif($md >= 20 && $md < 21) {
	$msprite = $moon_sprite['waningCrescent'][5]; }
elseif($md >= 21 && $md < 22) { // last quarter
	$msprite = $moon_sprite['chunks']['last_quarter']; }
elseif($md >= 22 && $md < 23) {
	$msprite = $moon_sprite['waningGibbous'][0]; }
elseif($md >= 23 && $md < 24) {
	$msprite = $moon_sprite['waningGibbous'][1]; }
elseif($md >= 24 && $md < 25) {
	$msprite = $moon_sprite['waningGibbous'][2]; }
elseif($md >= 25 && $md < 26) {
	$msprite = $moon_sprite['waningGibbous'][3]; }
elseif($md >= 26 && $md < 27) {
	$msprite = $moon_sprite['waningGibbous'][4]; }
elseif($md >= 27 && $md < 29) {
	$msprite = $moon_sprite['waningGibbous'][5]; }
/*

New Moon
-----
0.00662315856021 ~ 0.195585771204		'2013-06-08 15:57:37 EST'
0.00659884087214 ~ 0.19486765556		'2013-07-08 07:15:33 EST'
0.00642885730343 ~ 0.18984794071		'2013-08-06 21:51:57 EST'
0.00638070247316 ~ 0.188425900224		'2013-09-05 11:37:23 EST'
0.00676094257833 ~ 0.19965461437		'2013-10-05 00:35:44 EST'

First Quarter
-----
0.25585705599  ~ 7.5556094813			'2013-01-18 23:46:11 EST'
0.256106591924 ~ 7.56297842435			'2013-02-17 20:31:44 EST'
0.256495202824 ~ 7.57445433298			'2013-03-19 17:27:49 EST'
0.256681304204 ~ 7.5799500163			'2013-04-18 12:32:12 EST'
0.256708676884 ~ 7.58075834766			'2013-05-18 04:35:49 EST'

Full Moon
-----
0.506479163745 ~ 14.9566278595			'2013-01-27 04:39:38 EST'
0.506492389749 ~ 14.9570184312			'2013-02-25 20:27:19 EST'
0.50689905754  ~ 14.9690275705			'2013-03-27 09:28:28 EST'
0.507702615207 ~ 14.9927571014			'2013-04-25 19:58:14 EST'
0.508479185327 ~ 15.0156896742			'2013-05-25 04:26:02 EST'

Last Quarter
-----
0.75722473701  ~ 22.3612922469			'2013-01-05 03:59:00 EST'
0.757545600426 ~ 22.3707675325			'2013-02-03 13:57:30 EST'
0.757775521572 ~ 22.3775572393			'2013-03-04 21:53:55 EST'
0.75769962276  ~ 22.3753159027			'2013-04-03 04:37:40 EST'
0.757308356967 ~ 22.3637615935			'2013-05-02 11:15:16 EST'
 */