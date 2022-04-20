<?php 

function classResponde($status = 200, $body = '', $conten_type = 'text/html')
{
    //non ho ben capito sta parte
}


class serviceApi
{
    private $db_connection;
    //metodi
    function __construct()
    {
        $db_connection = new mysqli("localhost","Multatore","Picasso69");
        if ($db_connection->connect_error)
        {
            die("conn failed: ". $db_connection->connect_error);
        }
        else
        {
            echo "Connessione riuscita";
            echo "</br>";
        }
    }
    function __destruct()
    {

    }

    function elencaMulte()
    {
        echo "Hai chiesto di elencare le multe";
    }
    
}
//instanziamo oggetto Api
$api = new serviceApi();
$nome_funzione = $_GET['function'];
//echo "Richiesta fatta: "."$nome_funzione";
//router
//echo $nome_funzione;
switch($nome_funzione)
{
    case 'elencaMulte':
       $api->elencaMulte();
    break;

    default: 
    echo "C'è stato un problema ci scusiamo";
    break;
}




?>
