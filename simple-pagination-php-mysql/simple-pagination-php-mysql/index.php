<?php 
include('header.php');
?>
<title>phpzag.com : Demo Create Simple Pagination with PHP and MySQL</title>
<?php include('container.php');?>
<div class="container">
	<h2>Simple Pagination with PHP and MySQL</h2>		
	<?php
	include_once("db_connect.php");
	$showRecordPerPage = 5;
	if(isset($_GET['page']) && !empty($_GET['page'])){
		$currentPage = $_GET['page'];
	}else{
		$currentPage = 1;
	}
	$startFrom = ($currentPage * $showRecordPerPage) - $showRecordPerPage;
	$totalEmpSQL = "SELECT * FROM table1";
	$allEmpResult = mysqli_query($conn, $totalEmpSQL);
	$totalEmployee = mysqli_num_rows($allEmpResult);
	$lastPage = ceil($totalEmployee/$showRecordPerPage);
	$firstPage = 1;
	$nextPage = $currentPage + 1;
	$previousPage = $currentPage - 1;
	$empSQL = "SELECT *
	FROM table1 LIMIT $startFrom, $showRecordPerPage";
	$empResult = mysqli_query($conn, $empSQL);		
	?>	
	<table class="table ">
	<thead> 
		<tr> 
			<th>Direction</th> 
			<th>Area</th> 
			<th>Images</th>
		</tr> 
	</thead> 
	<tbody> 
		<?php 
		while($emp = mysqli_fetch_assoc($empResult)){
		?>
			<tr> 
				<th scope="row"><?php echo $emp['Direction']; ?></th> 
				<td><?php echo $emp['Area']; ?></td> 
				<td><img src='<?php echo $emp['images']; ?>' width='150'></td> 
			</tr> 
		<?php } ?>
	</tbody> 
	</table>
	<nav aria-label="Page navigation">
	  <ul class="pagination">
	  <?php if($currentPage != $firstPage) { ?>
		<li class="page-item">
		  <a class="page-link" href="?page=<?php echo $firstPage ?>" tabindex="-1" aria-label="Previous">
			<span aria-hidden="true">First</span>			
		  </a>
		</li>
		<?php } ?>
		<?php if($currentPage >= 2) { ?>
			<li class="page-item"><a class="page-link" href="?page=<?php echo $previousPage ?>"><?php echo $previousPage ?></a></li>
		<?php } ?>
		<li class="page-item active"><a class="page-link" href="?page=<?php echo $currentPage ?>"><?php echo $currentPage ?></a></li>
		<?php if($currentPage != $lastPage) { ?>
			<li class="page-item"><a class="page-link" href="?page=<?php echo $nextPage ?>"><?php echo $nextPage ?></a></li>
			<li class="page-item">
			  <a class="page-link" href="?page=<?php echo $lastPage ?>" aria-label="Next">
				<span aria-hidden="true">Last</span>
			  </a>
			</li>
		<?php } ?>
	  </ul>
	</nav>
	
	<div style="margin:50px 0px 0px 0px;">
		<a class="btn btn-default read-more" style="background:#3399ff;color:white" href="http://www.phpzag.com/simple-code-for-pagination-in-php/">Back to Tutorial</a>		
	</div>
</div>
<?php include('footer.php');?>
