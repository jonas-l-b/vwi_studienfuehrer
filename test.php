
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<form id="spamForm">
	<div class="form-group">
		<label>Subject:</label>
		<input type="text" class="form-control" name="subject" id="mailSubject" required>
	</div>
	<div class="form-group">
		<label>Body:</label>
		<textarea class="form-control" rows="7" name="body" required></textarea>
	</div>

	<div class="form-group">
		<label>Mail:</label>
		<input type="email" class="form-control" name="email" id="testmail">
	</div>
	<button class="btn btn-primary" id="mailTestButton">Send</button>
</form>

<script>
$(document).ready(function(){

	$('#mailTestButton').on('click', function() {
		if($('#testmail').val() == ""){
			alert("Mail needed.");
			return false;
		}
		$("#spamForm").submit(function(){
			$.ajax({
				url: "spam_submit.php",
				type: "post",
				data: $("#spamForm").serialize(),
				success: function (data) {
					alert(data);
					location.reload();
				},
				error: function(data) {
					alert(data);
				}
			});
		});
	});
	
});
</script>