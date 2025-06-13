<head>
	<script>
		// Optional JavaScript refresh after 5 seconds
		setTimeout(() => {
			location.reload();
		}, 7000);
	</script>
</head>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$b = "Select * from South";
$c = mysqli_query($conn, $b);
?>
<form method="post" action="delete.php">
	<table>
		<thead>
			<tr>
				<th>Select</th>
				<th>Area</th>
				<th>Hyperlink</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while ($d = mysqli_fetch_assoc($c)) {
			?>

				<tr>
					<td> <input type="checkbox" name="delete_ids[]" value="<?php echo $d['Area']; ?>" </td>
					<td> <?php echo $d['Area'] ?></td>
					<td> <a href="<?php echo $d['Hyperlink'] ?>"><?php echo $d['Hyperlink']; ?></a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<input type="Submit" name="Delete" value="Delete">
</form>
<?php
if (isset($_POST['Delete'])) {
	if (isset($_POST['delete_ids'])) {
		foreach ($_POST['delete_ids'] as $area) {
			$area_safe = $conn->real_escape_string($area);
			$sql = "DELETE FROM South WHERE Area = '$area_safe' ";
			mysqli_query($conn, $sql);
		}
		echo "Selected rows are deleted";
	} else {
		echo "not deleted";
	}
}

?>