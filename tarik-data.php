<html>

<head>
	<title>Contoh Koneksi Mesin Absensi Mengunakan SOAP Web Service</title>
	<!-- <link rel="stylesheet" href="./assets/style.css"> -->
</head>

<body bgcolor="#caffcb">

	<H3>Result</H3>

	<?php
	// $IP = $HTTP_GET_VARS["ip"];
	// $Key = $HTTP_GET_VARS["key"];
	// if ($IP == "") $IP = "10.5.4.115";
	// if ($Key == "") $Key = "0";

	// $IP = $_GET['ip'];
	// $Key = '0';

	// list data ip finger machine
	$IP = [
		'10.5.4.115',
		'10.5.4.117',
		'10.5.4.108'
	];

	// default key is 0
	$Key = '0';


	?>

	<!-- <form action="tarik-data.php">
		IP Address: <input type="text" name="ip" value="<?php $IP ?>" size=15><BR>
		Comm Key: <input type="text" name="key" size="5" value="<?= $Key ?>"><BR><BR>
		<input type="Submit" value="Download">
	</form> -->
	<BR>



	<?php

	for ($i = 1; $i < count($IP); $i++) {
		$IIP = $IP[$i];

		// ping ip machine
		exec("ping -n 3 $IIP", $output, $result);

		if ($result == 0) {
			echo "Ping successful!";
		} else {

			echo "Ping unsuccessful!";
		}


		// jika ip tidak kosong dan ping ok
		if (($IIP != "") && ($result == 0)) { ?>
			<table cellspacing="2" cellpadding="2" border="1">
				<!-- <tr align="center">
					<td><B>UserID</B></td>
					<td width="200"><B>Tanggal & Jam</B></td>
					<td><B>Verifikasi</B></td>
					<td><B>Status</B></td>
				</tr> -->
				<?php
				$Connect = fsockopen($IIP, "80", $errno, $errstr, 1);
				if ($Connect) {
					$soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . $Key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
					$newLine = "\r\n";

					// var_dump($soap_request);
					// die;
					fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
					fputs($Connect, "Content-Type: text/xml" . $newLine);
					fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
					fputs($Connect, $soap_request . $newLine);
					$buffer = "";
					while ($Response = fgets($Connect, 1024)) {
						$buffer = $buffer . $Response;
					}
				} else {
					echo "Koneksi Gagal";
				}

				include_once("parse.php");
				$buffer = Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
				$buffer = explode("\r\n", $buffer);
				for ($a = 0; $a < count($buffer); $a++) {
					$data = Parse_Data($buffer[$a], "<Row>", "</Row>");
					$PIN = Parse_Data($data, "<PIN>", "</PIN>");
					$DateTime = Parse_Data($data, "<DateTime>", "</DateTime>");
					$Verified = Parse_Data($data, "<Verified>", "</Verified>");
					$Status = Parse_Data($data, "<Status>", "</Status>");

					// print_r($buffer);
					// die;
				?>



					<!-- koneksi ke datbase mysql -->
					<?php
					$servername = "localhost";
					$database = "finger";
					$username = "root";
					$password = "";

					// untuk tulisan bercetak tebal silakan sesuaikan dengan detail database Anda
					// membuat koneksi
					$conn = mysqli_connect($servername, $username, $password, $database);
					// mengecek koneksi
					if (!$conn) {
						die("Koneksi gagal: " . mysqli_connect_error());
					} else {
						// echo "Koneksi berhasil";
					}



					$sql = "INSERT INTO download_att (empno, date_att, ip_machine) VALUES ('$PIN','$DateTime','$IIP')";

					if (mysqli_multi_query($conn, $sql)) {
						// echo "New records created successfully" . "<br>";
					} else {
						echo "Error: " . $sql . "<br>" . mysqli_error($conn);
					}

					mysqli_close($conn);

					?>


					<!-- <tr align="center">
						<td><?php echo $PIN ?></td>
						<td><?= $DateTime ?></td>
						<td><?= $Verified ?></td>
						<td><?= $Status ?></td>
					</tr> -->


				<?php } ?> <!-- looping parse_data -->

				<?php echo "IP Machine " . $IIP . "<br>"; ?>
				<?php echo "Downloaded " . count($buffer) . " data" ?>
				<br><br>

			</table>
		<?php } ?> <!-- end if -->

	<?php } ?> <!-- end for -->



	<!-- <script src="./assets/script.js"></script> -->
</body>

</html>