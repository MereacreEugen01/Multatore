<?php 

error_reporting(E_ALL);

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
        try
        {
            
            $statement = $this->$db_connection->prepare("INSERT INTO multe VALUES (NULL,:matricola,DEFAULT,:luogo,:importo,:foto,:longitudine,:latitudine)");

            $statement->execute(['matricola'=>$matricola, 'importo'=>$importo, 'luogo' => $luogo, ':foto' => $foto, ':longitudine' => $longitudine, ':latitudine' => $latitudine]);
            
            $idInserito =$this->$db_connection->lastInsertID();
            echo $prova;

			foreach($effrazioni as $effrazione)
            {
					$statement = $this->$db_connection->prepare("INSERT INTO contiene VALUES(:id_Multa, :id_Effrazione)");
					$statement->execute([':id_Multa' => $idInserito, 'id_Effrazione' => $effrazione]);
			}
        }
        catch(PDOException $e)
        {
            //echo "non funziona";
            sendResponse(500, $e->getMessage());
        }
    }

    function elencaMulte()
    {
       try
       {
           //////////////////////////Da fare meglio più i controlli 
            $matricola = $_POST['matricola'];

            if("" == trim($_POST['matricola']))
            {
                sendResponse(200, json_encode("Si prega di mettere l'ID dell'operatore"), "application/json");
                //echo $matricola;
            }
            else{
                //$statement = $this->$db_connection->prepare("INSERT INTO multe VALUES (NULL,:matricola,DEFAULT,:luogo,:importo,:foto,:longitudine,:latitudine)");
                $statement = $this->$db_connection->prepare("SELECT * FROM multe WHERE ID_Operatore = :matricola");

                $statement->execute([':matricola' => $matricola]);

                //$miaQuery = "SELECT * FROM multe WHERE ID_Operatore = $matricola";
                //$statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);

                $risultati = $statement->fetchAll();
                //echo $miaQuery;
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
        //echo "Hai chiesto di annullare una multa";
        //DELETE FROM table_name WHERE condition
        /*
        $statement = $this->$db_connection->prepare("SELECT * FROM multe WHERE ID_Operatore = :matricola");
        $statement->execute([':matricola' => $matricola]);
        */

        //////////////////////////Da fare meglio più i controlli 
        $multadaCanc = $_POST['id_Multa'];
        $statement = $this->$db_connection->prepare("DELETE from multe where ID_Multa = :id");
        //$miaQuery = "DELETE from multe where ID_Multa = $multadaCanc";
        if("" == trim($_POST['matricola']))
        {
            try
            {
                $statement->execute([':id' => $multadaCanc]);
                $risultati = $statement->fetchAll();
                //echo $miaQuery;
                sendResponse(200, json_encode($risultati), "application/json");
            }
            catch(PDOException $e)
            {
                sendResponse(500, $e->getMessage());
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
        try
        {
            $token = $_POST['key'];
            /*
            $statement = $this->$db_connection->prepare("SELECT * FROM `tipoeffrazione`");
            $statement->execute();
            */
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
                sendResponse(401, json_encode("Non è stato possibile autenticarti"), "application/json");
            }
            else{
                if($durataToken - time() < 0)
                {
                    $risposta = ['esito' => "Si prega di autetnicarsi nuovamente, il token è scaduto :c"];
                    sendResponse(401, json_encode($risposta), "application/json");
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
        return;
    }

    function accesso()
    {
        
        $matr = $_POST['Matr'];
        $pass = $_POST['Pass'];
        //$miaQuery = "SELECT * from operatore where ID_Operatore = '$user' AND password = '$pass'";
        //echo $miaQuery;
        
        $statement = $this->$db_connection->prepare("SELECT * from operatore where ID_Operatore = :matricola AND password = :password");
        $statement->execute([':matricola' => $matr, ':password' => $pass]);
        
        //$statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);  
        $risultati = $statement->fetchAll();
        $vettoreCredenzialiOttenuto = $risultati[0];
        if(isset($vettoreCredenzialiOttenuto))
        {
           // echo "sono state trovate le credenziali";
            $nuovoToken = hash("md5",$pass.time());
            //echo $nuovoToken;

            //$miaQuery = "UPDATE operatore SET Token = '$nuovoToken' WHERE operatore.ID_Operatore = '$user' AND operatore.password = '$pass'";
            $statement = $this->$db_connection->prepare("UPDATE operatore SET Token = :token WHERE operatore.ID_Operatore = :matricola AND operatore.password = :password");
            $statement->execute([':matricola' => $matr, ':password' => $pass,'token'=> $nuovoToken]);
            $statement = $this->$db_connection->prepare("UPDATE operatore SET Durata_token = :durata WHERE operatore.ID_Operatore = :matricola AND operatore.password = :password");
            $statement->execute([':matricola' => $matr, ':password' => $pass,'durata'=> time()+86400]);

            //echo $miaQuery;
            //$statement = $this->$db_connection->query($miaQuery);
            $risposta = ['token' => $nuovoToken, 'esito' => "Token aggiornato per 24h", ];
            sendresponse(200,json_encode($risposta),'application/json');   
        }
        else
        {
            $risposta = [ 'esito' => "Il codice matricola o la password sono errati ", ];
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
        case 'nuova_multa'://DA FINIRE PER BENE, MANCANO I CONTROLLI
            $api->nuovaMulta();
        break;
    
        case 'elenco_multe'://DA FINIRE PER BENE, MANCANO I CONTROLLI
            //echo hash("md5","ciao".time());
           $api->elencaMulte();
        break;

        case 'annulla_multa'://quasi fatto manca cancellare più multe insieme //DA FINIRE PER BENE, MANCANO I CONTROLLI
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
else{
    $api->accesso(); //DA FINIRE PER BENE, MANCANO I CONTROLLI
}

?>