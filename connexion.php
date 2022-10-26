<?php
    require_once "fonction/fonction.php";
    require_once 'bdd/db.php';
    reconnect_cookie();

    if(isset($_SESSION['auth'])){
        header('Location: ./mon_compte.php');
        exit();
    }

    if(!empty($_POST) && !empty($_POST['email']) && !empty($_POST['password'])){
        $req=$pdo->prepare('SELECT * FROM users WHERE (email = :email) AND date_inscription IS NOT NULL');
        $req->execute(['email' => $_POST['email']]);
        $user = $req->fetch();
        $pass=$_POST['password'];
        $password= $user->password;
        $admin=$user->admin;

        $pass = crypt($_POST['password'], '$6$rounds=7562$MDPestAZERTY$');


        if(hash_equals($pass, $password )){
            $_SESSION['auth'] = $user;
            $_SESSION['flash']['success']="Vous êtes maintenant connecter";
            if($_POST['remember']){
                $remember_token=str_random(250);
                $req=$pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
                $req->execute([$remember_token, $user->id]);
                setcookie('remember', $user->id .'==' . $remember_token . sha1($user->id . 'CookieSecu'), time()+60*60*24*7);
            }
            if($admin==1){
                header('Location: ./espace_admin.php');
            }
            else{ 
                header('Location: ./mon_compte.php');
            }
            exit();
        }else{
            $_SESSION['flash']['danger']= 'Identifiant ou mot de passe incorrecte';
        }
    }