<?php
$ppath = "http://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
?><!DOCTYPE html>
<html>
<head>
<title>ubrfzy's quick image linker</title>
<style>
body { color: white; background: black; }
span.fil {}
span.siz, input.lnkbox { font-family: monospace; font-size: 12px; }
label[for] { cursor: pointer; margin-right: 5px;}
</style>
</head>
<body>
<?php
#************SETTINGS**********
$perrow = 5;
$maxw = 150;
$minh = 50;
#******************************

$raw = glob("*.*");

$clean = filterFiles($raw);
unset($raw);

print "<br>\n";

if($passc == 0) {
	print "<center>";
		print "folder empty";
	print "</center>";
} else {
	#******************* BEGIN MAIN LOGIC ***************
	print "<center>";
	print "<table border=0 cellpadding=5 cellspacing=0>\n";

	$cell = 0;
	foreach( $clean as $fil ) {
		if( ($cell % $perrow) == 0 ) {
			print "<tr>\n";
		}

		print "<td align=\"center\" valign=\"top\"><div>";
		$data = getimagesize($fil);
		$rat = ($data[0]/$data[1]);
			//echo "data=/".$data[0]."&times;".$data[1]."/<br>";
			//echo "rat=/$rat/<br>";

		if( $data[0] > $maxw ) {
			$w = $maxw;
			$h = floor($maxw / $rat);
			//echo "h/$h/<br>\n";
		} else {
			$w = $data[0];
		}

		if( $h < $minh ) {
			$h = $minh;
			$w = floor($h * $rat);
			if( $w > 300 ) {
				$w=300;
			}
		} else {

		}

		#get some info prepared for use
		$fulfil = ($ppath.$fil);
		$sz = size_readable (filesize($fil));

		#print the name at the top
		print "<span class=\"fil\">$fil</span><br>\n";

		#*****not modded  WxH****
		print "<span class=\"siz\">{$data[0]}&times;{$data[1]}</span><br>\n";
		//print $sz . "<br>\n";

		print "</div>\n";

		print "<div>\n";
		print "<label for='raw{$cell}' title='click to select box for copy' onclick=\"document.getElementById('raw{$cell}').select();\">raw</label>\n";
		$value = $fulfil;
		print "<input type=text id='raw{$cell}' value=\"{$value}\" title=\"{$value}\" size=6 class='lnkbox' readonly ><br>\n";

		print "<label for='link{$cell}' title='click to select box for copy' onclick=\"document.getElementById('link{$cell}').select();\">link</label>\n";
		$value = "<a href='{$fulfil}'>text</a>";
		print "<input type=text id='link{$cell}' value=\"{$value}\" title=\"{$value}\" size=6 class='lnkbox' readonly ><br>\n";

		print "<label for='img{$cell}' title='click to select box for copy' onclick=\"document.getElementById('img{$cell}').select();\">img</label>\n";
		$value = "<img src='{$fulfil}'>";
		print "<input type=text id='img{$cell}' value=\"{$value}\" title=\"{$value}\" size=6 class='lnkbox' readonly ><br>\n";
		print "</div>\n";

		print "<br>\n";
		# make the image (downscaled) and link it to the real image
		print "<a href=\"$fulfil\" alt=\"link to image:{$fil}\" title=\"f:{$fil} s:{$sz}\" \"><img border=0 src=\"$fil\" width=\"{$w}\" height=\"{$h}\"></a>" . "<br>\n";

		print "</td>";

		if( ($cell % $perrow) == ($perrow-1) ) {
			print "</tr>\n\n";
		}
		$cell++;
	}

	print "</table>\n";
	#************************ END MAIN LOGIC*********************
}

?>
<hr width="25%">
<center><small style="color: rgba(50%, 50%, 50%, 0.1); font-family: san-serif;">
&copy; 2007-2014 c. 'uberfuzzy' stafford<br>
</small></center>
<!-- <?php print " pass:[{$passc}] fail:[{$failc}] " .(($banc>0)?("ban: {$banc}"):("")) ."\n"; ?> -->
</body></html>
<?php
#*************************FUNC********************************
function filterFiles($raw) {
	global $passc;
	global $passc;
	global $failc;
	global $banc;

	#list of crap to NOT display
	$vorbotten = array("*.php");

	foreach( $raw as $index => $f ) {
		foreach( $vorbotten as $BAN ) {
			if( fnmatch($BAN, $f) ) {
				$banc++;
				unset($raw[$index]);
				continue 2;
			}
		}
	}

	$ext = array("jpg", "jpeg", "gif", "png");
	foreach( $raw as $f ) {
		$dump = true;
		foreach( $ext as $e ) {
			if( fnmatch("*.$e", strtolower($f)) ) {
				$dump = false;
				$clean[] = $f;
				$passc++;
				continue 2;
			} else {
				#code used to be here, but now its not, so carry on
			}
		}

		if( $dump ) {
			$failc++;
		}
	}
	return $clean;
}

function size_readable ($size, $retstring = null) {
	# adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
	$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	if ($retstring === null) { $retstring = '%01.2f %s'; }
	$lastsizestring = end($sizes);
	foreach ($sizes as $sizestring) {
		if ($size < 1024) { break; }
		if ($sizestring != $lastsizestring) { $size /= 1024; }
	}
	if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } # Bytes aren't normally fractional
	return sprintf($retstring, $size, $sizestring);
}
#***********************end func*****************************
?>
