<?php
/* credenziali database */
define('DB_SERVER', '178.62.218.132');
define('DB_USERNAME', 'Multatore');
define('DB_PASSWORD', 'Picasso69');
define('DB_NAME', 'db_multe');
 
/* tentativo connessione database mySQL */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// controllo connessione
if($link === false){
    die("ERRORE: Non è stato possibile connettersi" . mysqli_connect_error());
}
else{
    echo "ciao";
}
?>
