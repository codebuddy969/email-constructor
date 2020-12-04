<?php
    if( isset($_POST['config']) && !empty($_POST['config']) && isset($_POST['name']) && !empty($_POST['name']) ) {
        $json_data = json_encode($posts);
        file_put_contents("configs/{$_POST['name']}.json", $_POST['config']);
        echo json_encode('Template saved');
    }

    if( isset($_POST['template_name']) && !empty($_POST['template_name']) ) {
        file_put_contents("configs/{$_POST['template_name']}.json", "[]");
        echo json_encode('Template cleared');
    }
?>