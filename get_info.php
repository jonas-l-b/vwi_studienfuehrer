<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php

$k_id = $_POST['k_id'];

$sql = "
    SELECT * FROM info_content
    WHERE id = $k_id
";

$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result) != 1){
    echo "error";
}else{
    $row = mysqli_fetch_assoc($result);

    //options of select
    $sql2 = "SELECT * FROM info_categories";
    $result2 = mysqli_query($con, $sql2);

    $options = "";
    while($row2 = mysqli_fetch_assoc($result2)){
        $options = $options . "<option value=\"".$row2['name']."\">".$row2['name']."</option>";
    }

    $form ="
        <form id=\"changeKnowledgeForm\">
            <div style=\"display:none\"class=\"form-group\">
                <input type=\"text\" class=\"form-control\" id=\"k_id\" name=\"k_id\" value=\"".$k_id."\">
            </div>
            <div class=\"form-group\">
                <label for=\"category\">Kategorie auswählen:</label>
                <select class=\"form-control\" id=\"category\" name=\"category\">
                    ".$options."
                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"title\">Titel:</label>
                <input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" minlength=\"15\" value=\"".$row['title']."\">
            </div>
            <div class=\"form-group\">
                <label for=\"content\">Inhalt:</label>
                <textarea class=\"form-control\" rows=\"5\" id=\"content\" name=\"content\" minlength=\"30\">".$row['content']."</textarea>
            </div>
        </form>
    ";

    echo $form;
}


?>