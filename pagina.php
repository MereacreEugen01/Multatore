<link rel="stylesheet" href="style.css">

<?php 


//echo $user. " ".$pass;

$autenticato = false;
$serveAccesso = false;


class serviceApi
{
    private $db_connection;
    //metodi
    function __construct()
    {
        $servername = "localhost";
        $username = "Multatore";
        $password = "Picasso69";
        $dbname = "db_multe";
        $dsn = "mysql:host=$servername;dbname=$dbname";

        $options = [
            //PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
           // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
          ];

        try
        {
            $this->$db_connection = new PDO($dsn,$username,$password,$options);
        }
        catch(PDOException $e)
        {
            sendResponse(500, $e->getMessage());
        }
    }

    function __destruct()
    {
        $db_connection = null;
    }

    function accesso()
    {
        global $autenticato;
        global $serveAccesso;

        $user = $_POST['username'];
        $hashPass = hash('sha512', $_POST['password']);

        $statement = $this->$db_connection->prepare("SELECT accessoDB from operatore where username = :user AND password = :pass");
        $statement->execute([':user' => $user, 'pass' => $hashPass]);
        $risultati = $statement->fetchAll();

        $vettoreTokenOttenuto = $risultati[0];

       
            //prendo il valore del token e lo metto in una variabile di supporto 
            //per poter verificare se il token è prensente nel database
            $tokenOttenuto = $vettoreTokenOttenuto['Token'];
            //$durataToken = $vettoreTokenOttenuto['Durata_Token'];
        

        //$vettoreCredenzialiOttenuto = $risultati[0];
        //print_r($vettoreCredenzialiOttenuto) ;
        $durataToken = $vettoreTokenOttenuto['accessoDB'];
        if(isset($durataToken)){
            if($durataToken == 1)
            {
                //echo "hai accesso al db";
                //action="query.php";
                //exec ('/amministratore.php');
                //include 'amministratore.php';
                $this->elencamulte();
            }
            else
            {
                echo"non hai accesso al db";
            }
        }
        else
        {
            echo "Controllare che le credenziali siano giuste !";
        }      
    }

    function elencamulte()
    {
        $statement = $this->$db_connection->prepare("SELECT T1.ID_Multa, T4.username, T1.Targa, T1.DataOra,T1.Luogo,T1.importo_da_pagare, GROUP_CONCAT( ' ', T3.Tipologia) AS TipologiaEffrazioni, GROUP_CONCAT(T3.ID_effrazione) AS IDEffrazioni FROM multe AS T1 INNER JOIN contiene AS T2 ON(T1.ID_Multa = T2.ID_Multa) INNER JOIN tipoeffrazione AS T3 ON(T2.ID_effrazione = T3.ID_effrazione) INNER JOIN operatore AS T4 ON (T1.ID_Operatore = T4.ID_Operatore)  GROUP BY T1.ID_Multa,T1.DataOra,T1.Luogo,T1.importo_da_pagare ORDER BY `T1`.`importo_da_pagare` ASC");//WHERE T1.ID_Operatore = :matricola
        $statement->execute();
        $risultati = $statement->fetchAll();
        //print_r($risultati['DataOra']);
        //echo "Voglio stampare le multe";
        
        echo "<table border id = \"tabella\">";
            echo "<tr>";
            echo "<th>ID Multa</th>";
            echo "<th>Operatore</th>";
            echo "<th>Targa</th>";
            echo "<th>Data e Ora</th>";
            echo "<th>Luogo</th>";
            echo "<th>Importo da pagare</th>";
            echo "<th>Effrazioni</th>";
            //echo "<th>Id Effrazioni</th>";
            echo "</tr>";
            
            foreach($risultati as $valore)
            {
                echo "<tr>";
                echo "<td>$valore[ID_Multa]</td>";
                echo "<td>$valore[username]</td>";
                echo "<td>$valore[Targa]</td>";
                echo "<td>$valore[DataOra]</td>";
                echo "<td>$valore[Luogo]</td>";
                echo "<td>$valore[importo_da_pagare]</td>";
                echo "<td>$valore[TipologiaEffrazioni]</td>";
                //echo "<td>$valore[IDEffrazioni]</td>";
                echo "</tr>";
                //echo "ciao";
            }
            echo "</table>";
            
    }
}

$api = new serviceApi();
$api->accesso();
$api->__destruct();
?>