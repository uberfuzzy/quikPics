<?php
$ppath = "http://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
?><!DOCTYPE html>
<html>
<head>
<title>ubrfzy's quick image linker</title>
<style>
body { color: white; background: black; }
input.lnkbox { padding: 0px 0px 0px 10px; font-size: 10px; }
span.fil {}
span.siz, input.lnkbox { font-family: monospace; font-size: 12px; }
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

$clean = filter($raw, (( isset($_GET['verbose']) )?(1):(0)) );
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

		print "raw <input type=text value=\"".$fulfil."\" size=10 class=\"lnkbox\"><br>\n";
		print 'link <input type=text value="'."<a href='{$fulfil}'>text</a>".'" size=10 class="lnkbox">'."<br>\n";
		print "img <input type=text value=\"<img src='{$fulfil}'>\" size=10 class=\"lnkbox\"><br>\n";

		print "<br>\n";
		# make the image (downscaled) and link it to the real image
		print "<a href=\"$fulfil\" alt=\"link to image:{$fil}\" title=\"f:{$fil} s:{$sz}\" \"><img border=0 src=\"$fil\" width=\"{$w}px\" height=\"{$h}px\"></a>" . "<br>\n";

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
<center><small style="color: #4f4f4f; font-family: Verdana;">
<?php print " pass:[{$passc}] fail:[{$failc}] " .(($banc>0)?("ban: {$banc}"):("")) ."\n"; ?><br>
&copy; 2007-2014 c. 'uberfuzzy' stafford<br>
</small></center>
</body><?php
#*************************FUNC********************************
function c($c,$t) {
	return "<font color=\"$c\">$t</font>";
}

function filter($raw, $output=0) {
	global $passc;
	global $passc;
	global $failc;

	#list of crap to NOT display
	$vorbotten = array("*.php");

	foreach( $raw as $index => $f ) {
		foreach( $vorbotten as $BAN ) {
			if( fnmatch($BAN, $f) ) {
				$banc++;
				if( $output ) {
					print c('yellow','BAN')." [$BAN][$f]<br>\n";
				}
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
				if($output){print c('lime','pass')." [$e][$f]<br>\n";}
				continue 2;
			} else {
				#code used to be here, but now its not, so carry on
			}
		}

		if( $dump ) {
			if( $output ) {
				print c("red","fail?") . " [$f]<br>\n";
			}
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
