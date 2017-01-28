<html>
<head>
<style>
body {
    font-family: "Trebuchet MS", Helvetica, sans-serif;
	overflow-x:hidden;
}
#footer {
   position:absolute;
   bottom:0;
   width:100%;
   height:120px;
}
</style>
<title>Dots and Boxes</title>
</head>
<body onload="restartGame();" onresize="">
<center>
<p style="font-size: 30px;"><span id="humanscore">0</span>&nbsp;&nbsp;vs&nbsp;&nbsp;<span id="aiscore">0</span></p>
<?php
if (isset($_GET['size'])) {
	if (is_numeric($_GET['size'])) {
		if ($_GET['size'] >= 1) {
			if ($_GET['size'] > 30) {
				$size = 30;
			} else {
				$size = $_GET['size'];
			}
		} else {
			$size = 3;
		}
	} else {
		$size = 3;
	}
} else {
	$size = 3;
}
echo '<table id="gameboard" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"><tbody>';
for ($i = 0; $i < $size; $i++) {
	echo '<tr>';
	for ($j = 0; $j < $size; $j++) {
		echo '<td align="center"><img src="black.gif" border="0" width="8" height="8"></td><td align="center"><a href="javascript:putHumanMove('.$j.','.$i.',0)"><img src="blank.gif" id="he'.$i.'a'.$j.'" border="0" width="36" height="8"></a></td>';
	}
	echo '<td align="center"><img src="black.gif" width="8" height="8"></td></tr><tr>';
	for ($k = 0; $k < $size; $k++) {
		echo '<td align="center"><a href="javascript:putHumanMove('.$k.','.$i.',3)"><img src="blank.gif" id="ve'.$i.'a'.$k.'" border="0" width="8" height="36"></a></td><td align="center"><img id="sq'.$i.'a'.$k.'" src="blank.gif" border="0" width="36" height="36"></td>';
	}
	echo '<td align="center"><a href="javascript:putHumanMove('.($size - 1).','.$i.',1)"><img src="blank.gif" id="ve'.$i.'a'.$size.'" border="0" width="8" height="36"></a></td>';
	echo '</tr>';
}
echo '<tr>';
for ($l = 0; $l < $size; $l++) {
	echo '<td align="center"><img src="black.gif" border="0" width="8" height="8"></td><td align="center"><a href="javascript:putHumanMove('.$l.','.($size - 1).',2)"><img src="blank.gif" id="he'.$size.'a'.$l.'" border="0" width="36" height="8"></a></td>
';
}
echo '<td align="center"><img src="black.gif" border="0" width="8" height="8"></td></tr>';
echo '</tbody></table>';
?>
<span id="gameend"></span>
<form style="position:absolute; top: 5px; left:5px;"action="" method="GET">
<input type="number" name="size">
<input type="submit" value="Set Board Size">
</form>	
<div id="footer"><table style="font-size: 40px;"><tr><td align="center" width="200px">Human</td><td align="center" width="200px">Tie</td><td align="center" width="200px">Computer</td></tr><tr><td id="totalhumanscore" align="center">0</td><td id="totalties" align="center">0</td><td id="totalaiscore" align="center">0</td></tr></table></div>
</center>
<script>
if (getCookie('computer') != "") {
	var totalAIScore = parseInt(getCookie('computer'));
} else {
	var totalAIScore = 0;
}
if (getCookie('human') != "") {
	var totalHumanScore = parseInt(getCookie('human'));
} else {
	var totalHumanScore = 0;
}
if (getCookie('ties') != "") {
	var totalTies = parseInt(getCookie('ties'));
} else {
	var totalTies = 0;
}
document.getElementById('totalaiscore').innerHTML = totalAIScore;
document.getElementById('totalhumanscore').innerHTML = totalHumanScore;
document.getElementById('totalties').innerHTML = totalTies;
var startisai = 0;
var board = new Array(<?php echo $size ?>);
for (var i = 0; i < <?php echo $size ?>; i++) {
	board[i] = new Array(<?php echo $size ?>);
}
var AIScore = 0;
var HumanScore = 0;

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function setCookieOfWins() {
	document.cookie = "computer=" + totalAIScore.toString();
	document.cookie = "human=" + totalHumanScore.toString();
	document.cookie = "ties=" + totalTies.toString();
}

function restartGame() {
	for (var j = 0; j < <?php echo $size ?>; j++) {
		for (var k = 0; k < <?php echo $size ?>; k++) {
			board[j][k] = 0;
			document.getElementById('sq' + j.toString() + 'a' + k.toString()).src = 'blank.gif';
		}
	}
	AIScore = 0;
	HumanScore = 0;
	document.getElementById('humanscore').innerHTML = HumanScore;
	document.getElementById('aiscore').innerHTML = AIScore;
	document.getElementById('gameend').innerHTML = "";
	for (var a = 0; a < <?php echo $size ?>; a++) {
		for (var b = 0; b < <?php echo $size + 1 ?>; b++) {
			document.getElementById('he' + b.toString() + 'a' + a.toString()).src = 'blank.gif';
			document.getElementById('ve' + a.toString() + 'a' + b.toString()).src = 'blank.gif';
		}
	}
	if (startisai == 1) {
		doAIMove();
	}
}

function putHumanMove(x, y, side, player) {
	if (player === undefined) {
		player = 'Human';
	}
	if (Math.floor(board[x][y] % Math.pow(2, side + 1) / Math.pow(2, side)) == 0) {
		if (player == 'Human') {
			var isAITurn = 1;
		} else {
			var isAITurn = 0;
		}
		if (side == 0) {
			board[x][y] += 1;
			if (y != 0) {
				board[x][y - 1] += 4;
			}
			document.getElementById('he' + y.toString() + 'a' + x.toString()).src = 'black.gif';
			if (board[x][y] == 15) {
				if (player == 'Human') {
					HumanScore += 1;
					isAITurn = 0;
				} else {
					AIScore += 1;
					isAITurn = 1;
				}
				document.getElementById('sq' + y.toString() + 'a' + x.toString()).src = player + '.gif';
			}
			if (y != 0) {
				if (board[x][y - 1] == 15) {
					if (player == 'Human') {
						HumanScore += 1;
						isAITurn = 0;
					} else {
						AIScore += 1;
						isAITurn = 1;
					}
					document.getElementById('sq' + (y - 1).toString() + 'a' + x.toString()).src = player + '.gif';
				}
			}
		} else if (side == 1) {
			board[x][y] += 2;
			if (x != <?php echo $size - 1 ?>) {
				board[x + 1][y] += 8;
			}
			document.getElementById('ve' + y.toString() + 'a' + (x + 1).toString()).src = 'black.gif';
			if (board[x][y] == 15) {
				if (player == 'Human') {
					HumanScore += 1;
					isAITurn = 0;
				} else {
					AIScore += 1;
					isAITurn = 1;
				}
				document.getElementById('sq' + y.toString() + 'a' + x.toString()).src = player + '.gif';
			}
			if (x != <?php echo $size - 1 ?>) {
				if (board[x + 1][y] == 15) {
					if (player == 'Human') {
						HumanScore += 1;
						isAITurn = 0;
					} else {
						AIScore += 1;
						isAITurn = 1;
					}
					document.getElementById('sq' + y.toString() + 'a' + (x + 1).toString()).src = player + '.gif';
				}
			}
		} else if (side == 2) {
			board[x][y] += 4;
			if (y != <?php echo $size - 1 ?>) {
				board[x][y + 1] += 1;
			}
			document.getElementById('he' + (y + 1).toString() + 'a' + x.toString()).src = 'black.gif';
			if (board[x][y] == 15) {
				if (player == 'Human') {
					HumanScore += 1;
					isAITurn = 0;
				} else {
					AIScore += 1;
					isAITurn = 1;
				}
				document.getElementById('sq' + y.toString() + 'a' + x.toString()).src = player + '.gif';
			}
			if (y != <?php echo $size - 1 ?>) {
				if (board[x][y + 1] == 15) {
					if (player == 'Human') {
						HumanScore += 1;
						isAITurn = 0;
					} else {
						AIScore += 1;
						isAITurn = 1;
					}
					document.getElementById('sq' + (y + 1).toString() + 'a' + x.toString()).src = player + '.gif';
				}
			}
		} else if (side == 3) {
			board[x][y] += 8;
			if (x != 0) {
				board[x - 1][y] += 2;
			}
			document.getElementById('ve' + y.toString() + 'a' + x.toString()).src = 'black.gif';
			if (board[x][y] == 15) {
				if (player == 'Human') {
					HumanScore += 1;
					isAITurn = 0;
				} else {
					AIScore += 1;
					isAITurn = 1;
				}
				document.getElementById('sq' + y.toString() + 'a' + x.toString()).src = player + '.gif';
			}
			if (x != 0) {
				if (board[x - 1][y] == 15) {
					if (player == 'Human') {
						HumanScore += 1;
						isAITurn = 0;
					} else {
						AIScore += 1;
						isAITurn = 1;
					}
					document.getElementById('sq' + y.toString() + 'a' + (x - 1).toString()).src = player + '.gif';
				}
			}
		}
		if (isAITurn == 1) {
			setTimeout(doAIMove, 50);
		}
	}
	document.getElementById('humanscore').innerHTML = HumanScore;
	document.getElementById('aiscore').innerHTML = AIScore;
	
	if (HumanScore + AIScore == <?php echo $size * $size ?>) {
		displayWinandRestart();
	}
}

function displayWinandRestart() {
	if (AIScore > HumanScore) {
		totalAIScore += 1;
		document.getElementById('gameend').innerHTML = "<h1>Computer Won!</h1>";
		document.getElementById('totalaiscore').innerHTML = totalAIScore;
		startisai = 0;
	} else if (AIScore < HumanScore) {
		totalHumanScore += 1;
		document.getElementById('gameend').innerHTML = "<h1>Human Won!</h1>";
		document.getElementById('totalhumanscore').innerHTML = totalHumanScore;
		startisai = 1;
	} else {
		totalTies += 1;
		document.getElementById('gameend').innerHTML = "<h1>We Tied!</h1>";
		document.getElementById('totalties').innerHTML = totalTies;
	}
	document.getElementById('gameend').innerHTML += '<button onclick="restartGame();">Restart</button>';
	setCookieOfWins();
}

function doAIMove() {
	var boxToPut = -1;
	var boxToPut1 = -1;
	var boxToPut2 = -1;
	var sideToPut = -1;
	var isstillgoing = 1;
    for (var k = 0; k < <?php echo $size ?>; k++) {
        for (var l = 0; l < <?php echo $size ?>; l++) {
            if (check3Box(board[l][k]) != -1) {
                boxToPut1 = l;
                boxToPut2 = k;
                sideToPut = check3Box(board[l][k]);
            }
        }
    }
	if (boxToPut1 != -1) {
		putHumanMove(boxToPut1, boxToPut2, sideToPut, 'AI');
    } else {
		var randomstart = Math.floor(Math.random() * <?php echo $size * $size * 4 ?>);
        for (var m = randomstart; m < randomstart + <?php echo $size * $size * 4 ?>; m++) {
            if (isstillgoing == 1) {
				boxToPut = (m % <?php echo $size * $size * 4 ?>) % <?php echo $size * $size ?>;
				sideToPut = Math.floor((m % <?php echo $size * $size * 4 ?>) / <?php echo $size * $size ?>);
				var danknumber = Math.floor(boxToPut / <?php echo $size ?>);
				var boxvalue = board[boxToPut % <?php echo $size ?>][danknumber];
				if (sideToPut == 0) {
					if (danknumber == 0) {
						var boxvalue2 = 0;
					} else {
						var boxvalue2 = board[boxToPut % <?php echo $size ?>][danknumber - 1];
					}
					if ((boxvalue == 0 || boxvalue == 2 || boxvalue == 4 || boxvalue == 8) && (boxvalue2 == 0 || boxvalue2 == 1 || boxvalue2 == 2 || boxvalue2 == 8))  {
						putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
						isstillgoing = 0;
					}
				} else if (sideToPut == 1) {
					if (boxToPut % <?php echo $size ?> == <?php echo $size - 1 ?>) {
						var boxvalue2 = 0;
					} else {
						var boxvalue2 = board[boxToPut % <?php echo $size ?> + 1][danknumber];
					}
					if ((boxvalue == 0 || boxvalue == 1 || boxvalue == 4 || boxvalue == 8) && (boxvalue2 == 0 || boxvalue2 == 1 || boxvalue2 == 2 || boxvalue2 == 4)) {
						putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
						isstillgoing = 0;
					}
				} else if (sideToPut == 2) {
					if (danknumber == <?php echo $size - 1 ?>) {
						var boxvalue2 = 0;
					} else {
						var boxvalue2 = board[boxToPut % <?php echo $size ?>][danknumber + 1];
					}
					if ((boxvalue == 0 || boxvalue == 1 || boxvalue == 2 || boxvalue == 8) && (boxvalue2 == 0 || boxvalue2 == 2 || boxvalue2 == 4 || boxvalue2 == 8)) {
						putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
						isstillgoing = 0;
					}
				} else if (sideToPut == 3) {
					if (boxToPut % <?php echo $size ?> == 0) {
						var boxvalue2 = 0;
					} else {
						var boxvalue2 = board[boxToPut % <?php echo $size ?> - 1][danknumber];
					}
					if ((boxvalue == 0 || boxvalue == 1 || boxvalue == 2 || boxvalue == 4) && (boxvalue2 == 0 || boxvalue2 == 1 || boxvalue2 == 4 || boxvalue2 == 8)) {
						putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
						isstillgoing = 0;
					}
				}
			}
        }
		if (isstillgoing) {
			for (var n = randomstart; n < randomstart + <?php echo $size * $size * 4 ?>; n++) {
				if (isstillgoing) {
					boxToPut = (n % <?php echo $size * $size * 4 ?>) % <?php echo $size * $size ?>;
					sideToPut = Math.floor((n % <?php echo $size * $size * 4 ?>) / <?php echo $size * $size ?>);
					var danknumber = Math.floor(boxToPut / <?php echo $size ?>);
					var boxvalue = board[boxToPut % <?php echo $size ?>][danknumber];
					if (sideToPut == 0) {
						if (boxvalue == 0 || boxvalue == 2 || boxvalue == 4 || boxvalue == 6 || boxvalue == 8 || boxvalue == 10 || boxvalue == 12 || boxvalue == 14)  {
							putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
							isstillgoing = 0;
						}
					} else if (sideToPut == 1) {
						if (boxvalue == 0 || boxvalue == 1 || boxvalue == 4 || boxvalue == 5 || boxvalue == 8 || boxvalue == 9 || boxvalue == 12 || boxvalue == 13) {
							putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
							isstillgoing = 0;
						}
					} else if (sideToPut == 2) {
						if (boxvalue == 0 || boxvalue == 1 || boxvalue == 2 || boxvalue == 3 || boxvalue == 8 || boxvalue == 9 || boxvalue == 10 || boxvalue == 11) {
							putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
							isstillgoing = 0;
						}
					} else if (sideToPut == 3) {
						if (boxvalue == 0 || boxvalue == 1 || boxvalue == 2 || boxvalue == 3 || boxvalue == 4 || boxvalue == 5 || boxvalue == 6 || boxvalue == 7) {
							putHumanMove(boxToPut % <?php echo $size ?>, danknumber, sideToPut, 'AI');
							isstillgoing = 0;
						}
					}
				}
			}
		}
	}
}

function check3Box(number) {
    if (number == 13) {
        return 1;
    } else if (number == 11) {
        return 2;
    } else if (number == 7) {
        return 3;
    } else if (number == 14) {
        return 0;
    } else {
        return -1;
    }
}
</script>
</body>
</html>