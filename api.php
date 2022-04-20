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
        echo "Hai chiesto di fare una nuova multa";
    }

    function elencaMulte()
    {
       // echo "Hai chiesto di elencare le multe";
        sendResponse(200, "Hai chiesto di elencare le multe", "application/json");

    }

    function annullaMulta()
    {
        //echo "Hai chiesto di annullare una multa";
    }

    function richiediSanzioni()
    {
        //echo "Hai chiesto di elencare le sanzioni";
    }

    function autenticazione()
    {
        
        try
        {
            $token = $_POST['key'];
            $miaQuery = "SELECT Token from operatore where Token = '$token'";
            $statement = $this->$db_connection->query($miaQuery, PDO::FETCH_ASSOC);

            $risultati = []; 

            foreach($statement as $row)
            {
                $risultati [] = $row;
            } 
            //Il risultato della query è una matrice e mi interessa solo il primo valore del primo vettore           
            $vettoreTokenOttenuto = $risultati[0];
            //prendo il valore del token e lo metto in una variabile di supporto 
            //per poter verificare se il token è prensente nel database
            $tokenOttenuto = $vettoreTokenOttenuto['Token'];
            //se il risultato della query è vuoto allora il token o è scaduto o non è presente nel database
            if($tokenOttenuto == "")
            {
                sendResponse(200, json_encode($risultati), "application/json");
                sendResponse(200, "Non è stato possibile autenticarti", "application/json");
                sendResponse(200, json_encode($this->$autenticato), "application/json");


            }
            else{
                echo "Sei stato autenticato";
                sendResponse(200, json_encode($risultati), "application/json");
                $this->$autenticato = true;
                sendResponse(200, json_encode($this->$autenticato), "application/json"); 
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
//sendResponse(200, json_encode($this->$autenticato), "application/json");
if($autenticato == true)
{
    switch($nome_funzione)
    {
        case 'nuova_multa':
            $api->elencaMulte();
        break;
    
        case 'elenco_multe':
           $api->elencaMulte();
        break;

    
        case 'annulla_multa':
            $api->elencaMulte();
        break;
    
        case 'sanzioni_disponibili':
            $api->elencaMulte();
        break;
    
        default: 
            sendResponse(404, "Funzione non trovata!");
        break;
    }
}

?>