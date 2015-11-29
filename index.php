<?php session_start(); ?>
<html>
<head><title>Hello app</title>
<meta charset="UTF-8">
</head>
<body>
<div align="right">
<ul>
<li><a href='gallery.php'/>View Images!</a></li>
<li><a href='index.php'/>DB Backup!</a></li>
</ul>

</div>
<div align="center">
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="result.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
    <!-- Name of input element determines name in $_FILES array -->
<table>
<tr>
<td bgcolor="#7FFFD4">Enter Name of user: </td>
<td><input type="text" name="firstname"></td>
</tr>
 <tr> 
<td bgcolor="#7FFFD4">Send this file:</td>
<td><input name="userfile" type="file" accept="image/png,image/jpeg"/></td>
</tr>   
<tr>
<td bgcolor="#7FFFD4">Enter Email of user: </td>
<td><input type="email" name="useremail"></td>
</tr>
<tr>
<td bgcolor="#7FFFD4">Enter Phone of user (1-XXX-XXX-XXXX): </td>
<td><input type="phone" name="phone"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Send File" />
</tr>
</form>


</div>

</body>
</html>
