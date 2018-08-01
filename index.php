<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Encode data with Huffman code</title>
	<meta name="author" content="Yuriy Lisovskiy">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
<div style="width: 100%">
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<p><strong>Enter data to encode</strong>:</p>
		<input name="data_to_encode" type="text" placeholder="Type text..." max="500">
		<ul style="list-style: none;">
			<li><input type="checkbox" name="get_hex" title="Hex of encoded string">Hex of encoded string</li>
			<li><input type="checkbox" name="show_sizes" title="Show sizes" checked>Show sizes</li>
		</ul>
		<p>
			<button type="submit" name="do_send">Encode</button>
		</p>
	</form>
	<?php
	include_once('utils/huffman.php');

	$errors = [];
	$warnings = [];

	if (isset($_POST['do_send'])) {
		echo '<hr>';
		$input = trim($_POST['data_to_encode']);
		verify_input($input);
		if (empty($errors)) {
			if (!empty($warnings)) {
				echo warning(array_shift($warnings));
			}
			$huffman = new HuffmanCode();
			print_res("Input", $input, $huffman->size_of_string($input));
			$input = normalize_input($input);
			try {
				$encoded = $huffman->encode($input);
				$res = $encoded;
				if (isset($_POST['get_hex'])) {
					$res = strval($huffman->base_convert_arbitrary($encoded, 2, 16));
				}
				print_res("Encoded", $res, $huffman->size_of_hex($encoded));
				try {
					$decoded = $huffman->decode($encoded);
					print_res("Decoded", $decoded, $huffman->size_of_string($decoded));
				}
				catch (Exception $exc) {
					echo err($exc->getMessage());
				}
			}
			catch (Exception $exc) {
				echo err($exc->getMessage());
			}
		}
		else {
			echo err(array_shift($errors));
		}
	}

	function normalize_input($input) {
		return str_replace('$', '?', str_replace('0', '?', str_replace('1', '?', $input)));
	}

	function verify_input(&$input) {
		if (preg_match('/[01^$]/', $input)) {
			global $warnings;
			$warnings[] = "input contains forbidden character which has been replaced by '?' symbol";
		}
		if ($input == '') {
			global $errors;
			$errors[] = "input is empty";
		}
	}

	function err($msg) {
		return "<div style='color: red;'>*".$msg."</div>";
	}

	function warning($msg) {
		return "<div style='color: darkorange;'>*".$msg."</div>";
	}

	function print_res($title, $input, $size)
	{
		$msg = "	<p>$title:</p>
		<ul><li>Data: $input</li>";
		if (isset($_POST['show_sizes'])) {
			$msg.= "<li>Size: $size bytes</li>";
		}
		$msg.= "</ul>";
		echo $msg;
	}

	?>
</div>
</body>
</html>
