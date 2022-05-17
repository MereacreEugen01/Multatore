<?php 

//error_reporting(E_ALL);

function sendResponse($status = 200, $body = '', $conten_type = 'text/html')
{
    $HTTPHeader = 'HTTP/1.1 '.$status.' '.'UNKNOWN';

    //Scrivo nella parte privata della risposta HTTP
    header($HTTPHeader);
    header('Content-type: '.$conten_type);
    //Scrivo nel body della risposta
    echo $body; 
}

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
            //PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
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

    function nuovaMulta()
    {
        //echo "Hai chiesto di fare una nuova multa";
        $targa = $_POST['targa'];
        $matricola = $_POST['matricola'];
        $effrazioni = json_decode($_POST['effrazioni']);
        $luogo = $_POST['luogo'];
        $importo = $_POST['importo'];
        $foto = $_POST['foto'];
        $longitudine = $_POST['longitudine'];
        $latitudine = $_POST['latitudine'];

        if(isset($_POST['targa']) and isset($_POST['matricola']) and isset($_POST['effrazioni']) and isset($_POST['luogo']) and isset($_POST['importo']))
        {
            try
            { 
                $statement = $this->$db_connection->prepare("INSERT INTO multe VALUES (NULL,:matricola,DEFAULT,:targa,:luogo,:importo,:foto,:longitudine,:latitudine)");
                echo $targa;
                $statement->execute(['matricola'=>$matricola, 'targa' => $targa, 'importo'=>$importo, 'luogo' => $luogo, ':foto' => $foto, ':longitudine' => $longitudine, ':latitudine' => $latitudine]);
                echo "ciao 2";
                $idInserito =$this->$db_connection->lastInsertID();
                //echo $prova;
                echo "ciao 3";
			    foreach($effrazioni as $effrazione)
                {
					$statement = $this->$db_connection->prepare("INSERT INTO contiene VALUES(:id_Multa, :id_Effrazione)");
					$statement->execute([':id_Multa' => $idInserito, 'id_Effrazione' => $effrazione]);
			    }
                echo "La multa è stata inserita";
            }
            catch(PDOException $e)
            {
                //echo "non funziona";
                sendResponse(500, $e->getMessage());
            }
        }
        else 
        {
            sendResponse(500, json_encode("Si prega di verificare che tutti i valori siano inseriti"));
        }
    }

    function elencaMulte()
    {
       try
       {
            $matricola = $_POST['matricola'];

            if("" == trim($_POST['matricola']))
            {
                sendResponse(200, json_encode("Si prega di mettere l'ID dell'operatore"), "application/json");
            }
            else{

                $statement = $this->$db_connection->prepare("SELECT T1.ID_Multa, T1.Targa, T1.DataOra,T1.Luogo,T1.importo_da_pagare, GROUP_CONCAT( ' ', T3.Tipologia) AS TipologiaEffrazioni, GROUP_CONCAT(T3.ID_effrazione) AS IDEffrazioni FROM multe AS T1 INNER JOIN contiene AS T2 ON(T1.ID_Multa = T2.ID_Multa) INNER JOIN tipoeffrazione AS T3 ON(T2.ID_effrazione = T3.ID_effrazione) WHERE T1.ID_Operatore = :matricola GROUP BY T1.ID_Multa,T1.DataOra,T1.Luogo,T1.importo_da_pagare ORDER BY `T1`.`importo_da_pagare` ASC");
                $statement->execute([':matricola' => $matricola]);
                
                $risultati = $statement->fetchAll();

                sendResponse(200, json_encode($risultati), "application/json");
            }  
       }
       catch(PDOException $e)
       {
            sendResponse(500, $e->getMessage());
       }
    }

    function annullaMulta()
    {
        
        $multadaCanc = $_POST['id_Multa'];

        if(isset($_POST['id_Multa']))
        {
            try
            {
                
                $statement = $this->$db_connection->prepare("DELETE from multe where ID_Multa = :id");
                $statement->execute([':id' => $multadaCanc]);

                $risultati = $statement->fetchAll();
                sendResponse(200, json_encode($risultati), "application/json");
            }
            catch(PDOException $e)
            {
                //sendResponse(500, $e->getMessage());
            }
        }
        else
        {
            sendResponse(200, json_encode("Si prega di mettere l'ID della multa da cancellare"), "application/json");
        }
    }

    function richiediSanzioni()
    {
        $statement = $this->$db_connection->prepare("SELECT * FROM `tipoeffrazione`");
        $statement->execute();
        $risultati = $statement->fetchAll();
        sendResponse(200, json_encode($risultati), "application/json");
    }

    function autenticazione()
    {
        global $autenticato;
        global $serveAccesso;
        if(isset($_POST['key']))
        {
            try
        {
            $token = $_POST['key'];
            
            $statement = $this->$db_connection->prepare("SELECT Token, Durata_Token from operatore where Token = :token");
            $statement->execute([':token' => $token]);
            
            $risultati = $statement->fetchAll();
            //Il risultato della query è una matrice e mi interessa solo il primo valore del primo vettore           
            $vettoreTokenOttenuto = $risultati[0];
            //prendo il valore del token e lo metto in una variabile di supporto 
            //per poter verificare se il token è prensente nel database
            $tokenOttenuto = $vettoreTokenOttenuto['Token'];
            $durataToken = $vettoreTokenOttenuto['Durata_Token'];
            //se il risultato della query è vuoto allora il token o è scaduto o non è presente nel database
            if($tokenOttenuto == "")
            {
                //sendResponse(401, json_encode("Non è stato possibile autenticarti"), "application/json");
                $serveAccesso = true;
            }
            else{
                if($durataToken - time() < 0)
                {
                    $risposta = ['esito' => "Si prega di autetnicarsi nuovamente, il token è scaduto :c"];
                    sendResponse(401, json_encode($risposta), "application/json");
                    $serveAccesso = true;
                }
                else{
                    //echo "sei entrato";
                    $body = ['risQuery' => $risultati, 'msg' =>  "Sei stato autenticato"];
                    // sendResponse(200, json_encode($body), "application/json");
                     $autenticato = true;
                     //da correggere qua la variabile lucchetto per l'autenticazione  
                     //echo $durataToken;
                }

            }
        }
        catch(PDOException $e)
            {
            sendResponse(500, $e->getMessage());
            }
    }
        else{
            sendResponse(401, json_encode("Token mancante"), "application/json");
        }   
        return;
    }

    function accesso()
    {
        
        $matr = $_POST['matricola'];
        $hashPass = hash('sha512', $_POST['password']);
        
        
        $statement = $this->$db_connection->prepare("SELECT * from operatore where ID_Operatore = :matricola AND password = :password");
        $statement->execute([':matricola' => $matr, ':password' => $hashPass]);
        
        $risultati = $statement->fetchAll();
        $vettoreCredenzialiOttenuto = $risultati[0];
        if(isset($vettoreCredenzialiOttenuto))
        {
            $nuovoToken = hash("md5",$hashPass.time());

            $statement = $this->$db_connection->prepare("UPDATE operatore SET Token = :token WHERE operatore.ID_Operatore = :matricola AND operatore.password = :password");
            $statement->execute([':matricola' => $matr, ':password' => $hashPass,'token'=> $nuovoToken]);
            $statement = $this->$db_connection->prepare("UPDATE operatore SET Durata_token = :durata WHERE operatore.ID_Operatore = :matricola AND operatore.password = :password");
            $statement->execute([':matricola' => $matr, ':password' => $hashPass,'durata'=> time()+86400]);

            $risposta = ['token' => $nuovoToken, 'esito' => "Token aggiornato per 24h", ];
            sendresponse(200,json_encode($risposta),'application/json');   
        }
        else
        {
            $risposta = [ 'esito' => "Il codice matricola o la password sono errati o non presenti", ];
            sendresponse(200,json_encode($risposta),'application/json');   
        }
    }
}

//instanziamo oggetto Api
$api = new serviceApi();
$nome_funzione = $_POST['function'];


//router
$api->autenticazione();
if($autenticato == true)
{
    switch($nome_funzione)
    {
        case 'nuova_multa'://fatto
            $api->nuovaMulta();
        break;
    
        case 'elenco_multe'://fatto
           $api->elencaMulte();
        break;

        case 'annulla_multa': //fatto
            $api->annullaMulta();
        break;
    
        case 'sanzioni_disponibili'://fatto
            $api->richiediSanzioni();
        break;
    
        default: 
            sendResponse(404, "Funzione non trovata!");
        break;
    }
}
else if($serveAccesso == true){
    $api->accesso(); 
}
$api->__destruct();
?>