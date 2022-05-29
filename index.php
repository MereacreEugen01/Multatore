
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gruppo 4 Login</title>
   

</head>
<link rel="stylesheet" href="style.css">
<body>
    <div class="wrapper">
       

        <form action="pagina.php" method="post" class= "formLogin">
        <h2>Multe Online Login</h2>
        <p>Inserisci le credenziali per accedere</p>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" >
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</body>
</html>