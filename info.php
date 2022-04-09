<?php
echo "ciao"; 
$conn = new mysqli("localhost","Eugen","120901");
if ($conn->connect_error)
{
    die("conn failed: ". $conn->connect_error);
}
else
echo "<br/>" ;
echo "Connessione riuscita";
phpinfo();
?>
