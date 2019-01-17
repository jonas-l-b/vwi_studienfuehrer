<?php
include "sessionsStart.php";
include "header.php";
include "connect.php";
?>

<body>

<?php include "inc/nav.php" ?>

<div class="container">

	<h2>Infos rund ums Wiwi-Studium!</h2>
    <p>Hier steht dir Wissen zur Verfügung, das über Erfahrungen zu Veranstaltungen hinausgeht. Über den entsprechenden Button kannst du die Sammlung um dein Wissen erweitern!</p>
    
    <button id="newKnowledgeButton" class="btn btn-warning">Wissen hinzufügen</button>

    <br><br>

    <?php
    $sql = "SELECT * FROM info_categories";
    $result = mysqli_query($con, $sql);
    
    ?>
    <ul class="nav nav-pills">
    <?php
    $i = true;
    while($row = mysqli_fetch_assoc($result)){
        ?>
        <li <?php if($i) echo "class=\"active\""?>><a data-toggle="pill" href="#<?php echo $row['name']?>"><?php echo $row['name']?></a></li>
        <?php
        $i = false;
    }
    ?>
    </ul>
  
    <div class="tab-content">
        <?php
        $i = true;
        $result = mysqli_query($con, $sql);
        while($row = mysqli_fetch_assoc($result)){
            ?>
            <div id="<?php echo $row['name']?>" class="tab-pane fade <?php if($i) echo "in active"?>">
                <br>

                <?php
                $sql2 = "
                    SELECT info_content.id AS id, info_content.title AS title, info_content.content AS content, info_content.category AS category, info_content.time_stamp AS time_stamp, users.username AS username, users.user_ID AS user_id FROM info_content
                    JOIN users ON info_content.user_id = users.user_ID
                    WHERE category = '".$row['name']."'
                ";
				$result2 = mysqli_query($con, $sql2);
				
				if(mysqli_num_rows($result2) == 0){
					echo "<i>Hier ist noch kein Wissen verfügbar. Über den orangenen \"Wissen hinzufügen\"-Button kannst du direkt welches eintragen!</i>";
				}

                while($row2 = mysqli_fetch_assoc($result2)){

                    $displayEdit = "style=\"display:none;\"";
                    if($row2['user_id'] == $userRow['user_ID']){
                        $displayEdit = "";
                    }

                    ?>
                    <div style="border:solid lightgrey 1px; border-radius:3px; padding:10px">
                        <h4><?php echo $row2['title']?></h4>
                        <p><?php echo nl2br($row2['content'])?></p>
                        <hr style="margin:5px">
                        <div class="general-flex-container" style="font-size:11px">
                            <div>
                            <?php echo $row2['username']?>
                                &#124;
                            <?php echo time_elapsed_string($row2['time_stamp']);?>
                            </div>
                            <div>
								<button data-id="<?php echo $row2['id']?>" <?php echo $displayEdit?> type="button" class="editTrashButton changeKnowledgeButton"><span class="glyphicon glyphicon-pencil"></span></button>
								<button data-id="<?php echo $row2['id']?>" <?php echo $displayEdit?> type="button" class="editTrashButton deleteKnowledgeButton"><span class="glyphicon glyphicon-trash"></span></button>
                            </div>
                        </div>
                    </div>
                    <br>

                    <?php
                }
                ?>
            </div>
            <?php
            $i = false;
        }
        ?>
</div>

<!--Wissen hinzufügen START-->
<div id="KnowledgeModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Füge dein Wissen hinzu!</h4>
	</div>
	<div class="modal-body" id="knowledge-modal-body">
		<form id="knowledgeForm">
		<div class="form-group">
			<label for="category">Kategorie auswählen:</label>
			<select class="form-control" id="category" name="category">
				<?php
				$sql = "SELECT * FROM info_categories";
				$result = mysqli_query($con, $sql);
				while($row = mysqli_fetch_assoc($result)){
					?>
					<option value="<?php echo $row['name']?>"><?php echo $row['name']?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="title">Titel:</label>
			<input type="text" class="form-control" id="title" name="title" minlength="15">
		</div>
		<div class="form-group">
			<label for="content">Inhalt:</label>
			<textarea class="form-control" rows="5" id="content" name="content" minlength="30"></textarea>
		</div>
		
		<button type="button" id="submitKnowledgeButton" class="btn btn-primary">Abschicken</button>
	</form>
	</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<script>
$( document ).ready(function() {

	$('#newKnowledgeButton').click(function(){
		$('#KnowledgeModal').modal('show');
	});

	$("#submitKnowledgeButton").click(function(){
		$.ajax({
			type: "POST",
			url: "info_submit.php",
			data: $("#knowledgeForm").serialize(),
			success: function(data) {
				//alert(data);
				if(data.includes("erfolg")){
					$('#knowledge-modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Wissen wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
				}else{
					$('#knowledge-modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung deines Wissens ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
	});

});
</script>
<!--Wissen hinzufügen ENDE-->

<!--Wissen ändern START-->
<div id="changeKnowledgeModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Wissen ändern</h4>
			</div>
			<div class="modal-body" id="changeKnowledgeModalBodyAll">
				<div id="changeKnowledgeModalBody"></div>
				<button type="button" id="submitChangedKnowledgeButton" class="btn btn-primary">Abschicken</button> <!--Muss bereits bei pageload hier sein und kann nicht gesendet werden, da sonst click event nicht funktioniert-->
			</div>
		</div>
	</div>
</div>

<script>
$( document ).ready(function() {

	$('.changeKnowledgeButton').click(function(){
		var k_id = $(this).attr('data-id');
		$('#changeKnowledgeModal').modal('show');
		$.ajax({
			type: "POST",
			url: "get_info.php",
			data: "&k_id=" + k_id,
			success: function(data) {
				//alert(data);
				$('#changeKnowledgeModalBody').html(data);
			}
		});
	});

	$("#submitChangedKnowledgeButton").click(function(){
		$.ajax({
			type: "POST",
			url: "changed_info_submit.php",
			data: $("#changeKnowledgeForm").serialize(),
			success: function(data) {
				//alert(data);
				if(data.includes("erfolg")){
					$('#changeKnowledgeModalBodyAll').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Wissen wurde erfolgreich geändert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
				}else{
					$('#changeKnowledgeModalBodyAll').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Ändern deines Wissens ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
	});

});
</script>
<!--Wissen ändern ENDE-->

<!--Wissen löschen START-->
<div id="deleteKnowledgeModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Wissen löschen</h4>
			</div>
			<div class="modal-body" id="deleteKnowledgeModalBody">
				<div style="display:none" id="deleteId"></div>
				<p>Bist du dir sicher, dass du dein Wissen löschen möchtest? Dieser Schritt kann nicht widerrufen werden.</p>
				<br>
				<button type="button" id="submitDeleteKnowledgeButton" class="btn btn-danger">Unwiderruflich löschen</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Doch nicht löschen :)</button>
			</div>
		</div>
	</div>
</div>

<script>
$( document ).ready(function() {

	$('.deleteKnowledgeButton').click(function(){
		var k_id = $(this).attr('data-id');
		$('#deleteKnowledgeModal').modal('show');
		$('#deleteId').html(k_id);
	});

	$('#submitDeleteKnowledgeButton').click(function(){
		var k_id = $('#deleteId').html();
		$.ajax({
			type: "POST",
			url: "delete_info.php",
			data: "&k_id=" + k_id,
			success: function(data) {
				//alert(data);
				if(data.includes("erfolg")){
					$('#deleteKnowledgeModalBody').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Wissen wurde erfolgreich gelöscht!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
				}else{
					$('#deleteKnowledgeModalBody').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Löschen deines Wissens ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
	});

});
</script>
<!--Wissen löschen ENDE-->

</body>