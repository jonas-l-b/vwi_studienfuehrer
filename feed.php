<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<div class="row">
		
		<?php
		$feedLimit = 50;
		?>
	
		<div class="col-md-5">
			<h3>Neuste Kommentare</h3>
			<br>
			
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, ratings.time_stamp AS time_stamp, comment
				FROM ratings
				JOIN subjects ON ratings.subject_ID = subjects.ID
				JOIN users ON ratings.user_ID = users.user_ID
				ORDER BY ratings.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<div class="row">
					<div class="col-md-1">
						<div style="text-align:right; font-size:2em">
							<?php echo $count?>
						</div>
					</div>
					<div class="col-md-11">
						<p>
							<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
							<br>
							<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
						</p>
						<p class="more well" style="border-radius:3px;">
							<?php echo $row['comment']?>
						</p>
					</div>
				</div>
				<?php
				if($count < $feedLimit && $count != mysqli_num_rows($result)) echo "<hr>"; //Set $feedLimit above
			}
			?>
		</div>
		<div class="col-md-2"></div>
		<div class="col-md-5">
			<h3>Neuste Fragen</h3>
			<br>
			
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, questions.time_stamp AS time_stamp, question
				FROM questions
				JOIN subjects ON questions.subject_ID = subjects.ID
				JOIN users ON questions.user_ID = users.user_ID
				ORDER BY questions.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<div class="row">
					<div class="col-md-1">
						<div style="text-align:right; font-size:2em">
							<?php echo $count?>
						</div>
					</div>
					<div class="col-md-11">
						<p>
							<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
							<br>
							<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
						</p>
						<p class="more well" style="border-radius:3px;">
							<?php echo $row['question']?>
						</p>
					</div>
				</div>
				<?php
				if($count < $feedLimit && $count != mysqli_num_rows($result)) echo "<hr>"; //Set $feedLimit above
			}
			?>
		</div>
	</div>

</div>





</body>