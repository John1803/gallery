<?php
require "../vendor/autoload.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form</title>
    <link rel="stylesheet" href="http://yegor256.github.io/tacit/tacit.min.css">
</head>
<body>
<h1>Form</h1>
<form method="POST" action="upload.php" enctype="application/x-www-form-urlencoded">
    <label>Select file to upload:</label>
    <input type="text" name="newfile">
    <button type="submit">Upload</button>
</form>
</body>
</html>

