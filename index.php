<?php 
session_start(); 
?>
<html>
<head><title>Sneha Karunakaran MP Final</title>
<meta charset="UTF-8">
</head>
<body>
<div align ="center"> <h2> ITMO 544 Final Project</h2></div>
<div align="right">
<ul>
<li><a href='gallery.php'/>View Images!</a></li>
<li><a href='introspection.php'/>DB Backup!</a></li>
</ul>

</div>
<?php
if((isset($_SESSION['introspec']))&&($_SESSION['introspec'])){
echo "MySQL dump in progress Admin has disabled form! Click on view Images to view gallery ";
}
else
{
//echo 'test';
//echo (isset($_SESSION['introspec']));
?>
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
</table>
</form>


</div>
<?php
}
?>
</body>
</html>
