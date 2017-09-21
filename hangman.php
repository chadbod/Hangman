<?php
session_start();
?>

<html>
<head>
	<title>Hangman</title>
</head>
<body>
<fieldset>
<legend><h1>HANGMAN: <font color='red'>THE REKTONING</font></h1></legend>

<?php
//empty space char
$empt = "_";

//checks the number of correct guesses made
function countDisp($arr) {
	global $empt;
	$count = 0;
	foreach ($arr as $val) {
		if ($val != $empt) {
			$count++;
		}
	}
	return $count;
}

//hangman display possibilities
$hangman = array(
'<pre>|-----------||----
|
|
|
|
|
|
|
|
|
|
|
|
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|
|
|
|
|
|
|
|
|
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|           |
|           |
|           |
|           |
|           |
|           |
|          
|           
|            
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|           |
|           |
|           |
|           |
|           |
|           |
|          / 
|         /   
|        /     
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|           |
|           |
|           |
|           |
|           |
|           |
|          / \
|         /   \
|        /     \
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|           |
|          /|
|         / |
|        /  |
|           |
|           |
|          / \
|         /   \
|        /     \
------------------</pre><br>',
'<pre>|-----------||----
|           __
|          /  \
|          \__/
|           |
|          /|\
|         / | \
|        /  |  \
|           |
|           |
|          / \
|         /   \
|        /     \
------------------</pre><br>'
);

//restart function
if (ISSET($_POST['restart'])) {
	session_unset();
}

//counts number of incorrect guesses
if (!ISSET($_POST['start']) and !ISSET($_SESSION['prog'])) {
	$_SESSION['fails'] = 0; //track number of incorrect guesses made
	echo '<embed src="menu.mp3" autostart="true" hidden="true">';

?>
	<!--Start screen form. can start game and choose difficulty-->
	<p>Hey, kid! You think you're tough, huh? You must, considering you've stumbled into <font color='red'>HANGMAN: THE REKTONING</font>. Your Slaya Word &#8482; is about to be selected. Are you ready to step into the arena?</p>
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="submit" name="start" value="Start!"><br />
		Difficulty: <select name="mode">
			<option value="normal" title="Gives traditional, normal words">Normal</option>
			<option value="hard" title="Better watch out! Random words with numbers appear!">Hard</option>
			<option value="ultra" title="Not for the n00bs!">Impossible</option>
		</select><br />
	</form>

<?php
echo end($hangman);
}

else {
	
	$msg = null;
	$hint = null;
	$_SESSION['prog'] = true; //game in progress
	
	if (ISSET($_POST['submit'])) {
		
		//puts word and split word into sessions
		$word = $_SESSION['word'];
		$split = $_SESSION['split'];
		
		if ($_POST['guess'] != null and $_POST['guess'] != " ") {
			$dupl = false; //used to see if guess has already been made
			$guess = strtolower($_POST['guess']); //inputted guess taken as lowercase

			if (!in_array($guess,$_SESSION['fill']) and !in_array($guess,$_SESSION['try'])) {
				
				//check if guess is valid. if so, put guess into word printed on screen
				$x = 0;
				$valid = false;
				foreach ($split as $letter) {
					if ($letter == $guess) {
						$_SESSION['fill'][$x] = $guess;
						$valid = true;
					}
					$x++;
				}
				//if guess was incorrect, put it into incorrect guesses array
				if ($valid == false) {
					$_SESSION['try'][] = $guess;
					$_SESSION['fails']++;
					$msg = "<font color='red'><strong>That character doesn't exist!</strong></font>";
				}
				else {
					$_SESSION['fails'] = 0; //resets fail counter for hint
					$msg = "<font color='green'><strong>That was correct! Keep going!</strong></font>";
				}
			}
			else {
				$msg = "<font color='red'><strong>You've already tried that. Choose a different character!</strong></font>";
			}
		}
		else {
			$msg = "<font color='red'><strong>You did not make a valid guess!</strong></font>";
		}
	}
	else { //initial data before submit is hit
		//creates random word if mode is hard or ultra
		$_SESSION['mode'] = $_POST['mode'];
		if ($_POST['mode'] == "hard" or $_POST['mode'] == "ultra") {
			if ($_POST['mode'] == "hard") {
				$stuff = "abcdefghijklmnopqrstuvwxyz1234567890";
				$min = 5; //min word size
				$max = 50; //max word size
			}
			else {
				$stuff = "abcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()";
				$min = 1;
				$max = 16;
			}
			
			$stuff = str_split($stuff);
			$count = count($stuff) - 1;

			$word = null;
			for ($y=0 ; $y<mt_rand($min,$max) ; $y++) {
					$rand = $stuff[mt_rand(0,$count - 1)]; //gets random letter
					$word = $word . $rand; //appends letter onto word
			}
			
			$wordlist[] = $word; //turn word into array because the program uses the word as an array
		}
		//normal mode has standard list of words
		elseif ($_POST['mode'] == "normal") {
			$wordlist = array(
			'cat',
			'dog',
			'shirt',
			'blue',
			'ephemeral',
			'parent',
			'guitar',
			'music',
			'computer',
			'science',
			'hangman',
			'thompson',
			'david',
			'kid',
			'game',
			'sun',
			'nature',
			'tree',
			'happy',
			'smile',
			'nerd',
			'fun',
			'code',
			'run',
			'jump',
			'mouse',
			'letter',
			'vowel',
			'zebra',
			'canada',
			'earth',
			'gold',
			'diamond',
			'internet',
			'health',
			'flower',
			'candy'
			);
		}

		$last_index = count($wordlist) - 1;
		$rand_index = mt_rand(0,$last_index);
		$_SESSION['word'] = $wordlist[$rand_index]; //select random word from word array
		$_SESSION['split'] = str_split($_SESSION['word']); //split up word into its characters in an array
		$_SESSION['fill'] = array(); //displays decodable word on screen
		$_SESSION['try'] = array(); //stores incorrect guesses
		//initially make decodable word printed on screen the engimatic character
		for ($x=0 ; $x<count($_SESSION['split']) ; $x++) {
			array_push($_SESSION['fill'],$empt);
		}
	}
	
	echo $hangman[count($_SESSION['try'])]; //prints hangman based on number of incorrect guesses
	
	//prints decodable word
	foreach ($_SESSION['fill'] as $char) {
		echo $char . " ";
	}
	
	//checks for win or lost, displays respective messsage
	if (countDisp($_SESSION['fill']) == count($_SESSION['split']) or count($_SESSION['try']) == 6) {
		if (countDisp($_SESSION['fill']) == count($_SESSION['split'])) {
			$msg = "<font color='green'><strong>You win!</strong></font> You properly decoded <em>" . $word . "</em>. Good job! Play again?";
			echo '<embed src="win.mp3" autostart="true" hidden="true">';
		}
		else {
			$msg = "<font color='red'><strong>You lose!</strong></font> Your word was <em>" . $word . "</em>. Maybe you'll do better next time! Play again?";
			echo '<embed src="sad.mp3" autostart="true" hidden="true">';
		}
?>
		<!--only restart button is displayed when the game ends-->
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<br /><input type="submit" name="restart" value="Restart" style="background-color: skyblue">
		</form>
<?php
	}
	else {
		//hint display
		if ($_SESSION['fails'] > 2 and $_SESSION['mode'] != "ultra") {
			reset($_SESSION['split']);
			$f = current($_SESSION['split']);
			$l = end($_SESSION['split']);
			$hint = "<br /><font color='green'>Hint: the first letter of the word is " . $f . " and the last letter is " . $l . ".</font>";
		}
?>

		<br /><br />
		<!--game guess input, restart button included-->
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="text" name="guess" placeholder="Guess a letter!" maxlength="1" autofocus>
			<input type="submit" name="submit" value="Go!">
			<input type="submit" name="restart" value="Restart">
		</form>

<?php
		//displays incorrect letters guessed
		if (!empty($_SESSION['try'])) {
			echo "<strong><font color='blue'>You've incorrectly tried:</font></strong> ";
			foreach ($_SESSION['try'] as $char) {
				echo $char . " ";
			}
		}
	}
	
	//print message and hint
	echo "<br /><br />" . $msg . "<br />" . $hint;
}
?>

</fieldset>
</body>
</html>