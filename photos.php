<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6thsemproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$showRecordPerPage = 4;
if (isset($_GET['page']) && !empty($_GET['page'])) {
	$currentPage = $_GET['page'];
} else {
	$currentPage = 1;
}
$startFrom = ($currentPage * $showRecordPerPage) - $showRecordPerPage;
$totalEmpSQL = "SELECT * FROM hotels";
$allEmpResult = mysqli_query($conn, $totalEmpSQL);
$totalEmployee = mysqli_num_rows($allEmpResult);
$lastPage = ceil($totalEmployee / $showRecordPerPage);
$firstPage = 1;
$nextPage = $currentPage + 1;
$previousPage = $currentPage - 1;
$empSQL = "SELECT *
	FROM hotels LIMIT $startFrom, $showRecordPerPage";
$empResult = mysqli_query($conn, $empSQL);
$c = mysqli_num_rows($empResult);
?>
<?php

if ($c > 0) {
	echo '<h1 style="text-align:center;"><b>North</b></h1>';
	echo '<table width="100%" border="0" style="text-align:center;"><tr><td>';
	echo '<table width="100%" border="1" cellspacing="10" cellpadding="10" style="margin: 0 auto;">';

	$count = 0;
	while ($row2 = mysqli_fetch_assoc($empResult)) {
		if ($count % 2 == 0) {
			echo '<tr>';
		}

		echo '<td width="195" height="250" style="text-align:center;">';
		echo '<img src="' . $row2["Images"] . '" width="190" height="150" border="2"><br><br>';
		echo '<input type="text" size="10" value="' . htmlspecialchars($row2["Area"]) . '">';
		echo '</td>';

		$count++;

		if ($count % 2 == 0) {
			echo '</tr>';
		}
	}
	if ($count % 2 != 0) {
		echo '</tr>';
	}

	echo '</table></td></tr></table>';
}
?>
<div style="text-align: center; margin-top: 20px;">
	<ul style="list-style: none; display: inline-flex; gap: 10px; padding: 0;">
		<?php if ($currentPage != $firstPage) { ?>
			<li class="page-item">
				<a class="page-link" href="?page=<?php echo $firstPage ?>" tabindex="-1" aria-label="Previous">
					<span aria-hidden="true">First</span>
				</a>
			</li>
		<?php } ?>
		<?php if ($currentPage >= 2) { ?>
			<li class="page-item"><a class="page-link"
					href="?page=<?php echo $previousPage ?>"><?php echo $previousPage ?></a></li>
		<?php } ?>
		<li class="page-item active"><a class="page-link"
				href="?page=<?php echo $currentPage ?>"><?php echo $currentPage ?></a></li>
		<?php if ($currentPage != $lastPage) { ?>
			<li class="page-item"><a class="page-link" href="?page=<?php echo $nextPage ?>"><?php echo $nextPage ?></a></li>
			<li class="page-item">
				<a class="page-link" href="?page=<?php echo $lastPage ?>" aria-label="Next">
					<span aria-hidden="true">Last</span>
				</a>
			</li>
		<?php } ?>
	</ul>
</div>


</html>