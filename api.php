<?php 

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

        try
        {
            $this->$db_connection = new PDO($dsn,$username,$password);
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
        $matricola = $_POST['operatore'];
        $effrazione[] = $_POST['effrazione'];
        $luogo = $_POST['luogo'];
        $importo = $_POST['importo'];
        $foto = $_POST['foto'];
        $longitudine = $_POST['longitudine'];
        $latitudine = $_POST['latitudine'];
        try
        {
            $miaQuery = "INSERT INTO multe VALUES (NULL, '$matricola', DEFAULT, '$luogo','$importo','$foto','$longitudine','$latitudine')";
            //echo $miaQuery; 
           ///// $statement = $this->$db_connection->query($miaQuery); // in questo modo funziona ma non è sicuro per l'sql injection
            /*  non funziona
            $statement = $this->db_connection->prepare("INSERT INTO multe VALUES (NULL,:id_operatore,DEFAULT,:luogo,:importo,:foto,:longitudine,:latitudine)");
            $statement->excetute([':id:operatore' => $matricola, ':luogo' => $luogo, ':importo' => $importo, ':foto' => $foto, ':longitudine' => $longitudine, ':latitudine' => $latitudine]);
            */
            //$statement->execute( [ $matricola,  $luogo,$importo,$foto,$longitudine,$latitudine]);
            //sendResponse(200, json_encode($statement), "application/json");
            //echo "funziona";

            //per farsi restituire l'ultima multa inserita 
           //////////////// $miaQuery = "SELECT * FROM multe WHERE ID_Multa = ( SELECT max(ID_Multa) FROM multe )";
            ///////////////$statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
           /////////////// $risultati = $statement->fetchAll();
           ///////////////// $vettoreIdOttenuto = $risultati[0];
            //prendo il valore del token e lo metto in una variabile di supporto 
            //per poter verificare se il token è prensente nel database
           /////////////// $IdOttenuto = $vettoreIdOttenuto['ID_Multa'];
           // echo $IdOttenuto;
          // echo json_encode($_POST['effrazione']);
          /*
            $arrAssco = [
                'nome' => 'luca',
                'cognome' => 'rossi',
                'eta' => 25
            ];
            foreach($arrAssco as $chiave => $valore)
            {
                echo $chiave.$valore;
            }
           */
            

        /*
            $statement = $this->db_connection->prepare("INSERT INTO libri VALUES (NULL,?,?,?)");

            //$statement->execute([':titolo' => $_GET['titolo'], ':autore'=> $_GET['autore'], ':prezzo' => $_GET['prezzo']])
            $statement->execute( [ $_GET['titolo'],  $_GET['autore'], $_GET['prezzo'] ] );
            //$statement->execute( [ $_GET['titolo'],  $_GET['autore'], $_GET['prezzo'] ] );
        */

        //echo json_encode($effrazione);
        //$statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            echo "non funziona";
            sendResponse(300, $e->getMessage());
        }
        

    }

    function elencaMulte()
    {
       try
       {
            $matricola = $_POST['matricola'];
            $miaQuery = "SELECT * FROM multe WHERE ID_Operatore = $matricola";
            $statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
            $risultati = $statement->fetchAll();
            //echo $miaQuery;
            sendResponse(200, json_encode($risultati), "application/json");
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
        $multadaCanc = $_POST['ID_Multa'];
        $miaQuery = "DELETE from multe where ID_Multa = $multadaCanc";
        try
        {
            $statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
            $risultati = $statement->fetchAll();
            //echo $miaQuery;
            sendResponse(200, json_encode($risultati), "application/json");
        }
        catch(PDOException $e)
        {
            sendResponse(500, $e->getMessage());
        }

    }

    function richiediSanzioni()
    {
        $miaQuery = "SELECT * FROM `tipoeffrazione`";
        $statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
        $risultati = $statement->fetchAll();
        sendResponse(200, json_encode($risultati), "application/json");
    }

    function autenticazione()
    {
        global $autenticato;
        try
        {
            hash("md5","ciao");
            $token = $_POST['key'];
            $miaQuery = "SELECT Token from operatore where Token = '$token'";
            $statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);
            /*
            $risultati = []; 

            foreach($statement as $row)
            {
                $risultati [] = $row;
            } 
            */
            $risultati = $statement->fetchAll();
            //Il risultato della query è una matrice e mi interessa solo il primo valore del primo vettore           
            $vettoreTokenOttenuto = $risultati[0];
            //prendo il valore del token e lo metto in una variabile di supporto 
            //per poter verificare se il token è prensente nel database
            $tokenOttenuto = $vettoreTokenOttenuto['Token'];
            //se il risultato della query è vuoto allora il token o è scaduto o non è presente nel database
            if($tokenOttenuto == "")
            {
                sendResponse(401, json_encode("Non è stato possibile autenticarti"), "application/json");
            }
            else{
                $body = ['risQuery' => $risultati, 'msg' =>  "Sei stato autenticato"];
                //sendResponse(200, json_encode($body), "application/json");
                $autenticato = true;
                //da correggere qua la variabile lucchetto per l'autenticazione  
            }
        }
        catch(PDOException $e)
        {
            sendResponse(500, $e->getMessage());
        }
        return;
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
        case 'nuova_multa'://quasi fatto manca passare più effrazioni
            $api->nuovaMulta();
        break;
    
        case 'elenco_multe'://fatto
           $api->elencaMulte();
        break;

        case 'annulla_multa'://quasi fatto manca cancellare più multe insieme
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

?>