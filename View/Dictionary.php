<?php 

    $dict_months = array (
        'English' => array (
            'January','February','March',"April",'May','June','July','August',
            'September','October','November','December'
            ),
        'Spanish' => array ('Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
        ),
        'French'=>array('Janvier',htmlentities('Février'),'Mars','Avril','Mai',
                'Juin','Juillet',htmlentities('Août'),'Septembre','Octobre',
                'Novembre',htmlentities('Décembre')
        )
    );

    $dict_errors = array (
        'English'=> array (
            'USER_TAKEN'=>"Username is already taken",
            "DATABASE"=> "Experiencing Technical Difficulties",
            "NOT_DEACTIVATED"=>"Could not deactivate user",
            "UNKNOWN_ACTION"=>"Requested action is not available",
            'msg_txt_too_long'=>"Your message text was too long",
            'name_already_taken' => 'User name is already taken',
            'invalid_user_pass' => 'Invalid username/password',
            'unknown' => 'An unknown error has occured',
            'server_error' => 'Server is down, please try again later',
            'missing_input' => 'There is missing information',
            'missing_email' => 'Email address is not defined',
            'missing_user' => 'Username is not defined',
            'missing_password' => 'Password is not defined',
            'invalid_user' => "User cannot be validated",
            'not_logged_in' => "You are not logged in",
            'no_action' => 'What, exactly, do you want me to do!'
            ),
        'French'=> array (
            'msg_txt_too_long'=>htmlentities("Votre message était trop long"),
           'USER_TAKEN'=>htmlentities("Le nom d'utilisateur est déjà pris"),
            "DATABASE"=> htmlentities("Rencontrez des difficultés techniques"),
            "NOT_DEACTIVATED"=>htmlentities("Impossible de désactiver l'utilisateur"),
            "UNKNOWN_ACTION"=>htmlentities("L'action demandée n'est pas disponible"),

            'name_already_taken' => htmlentities('Nom d\'utilisateur est déjà pris'),
            'invalid_user_pass' => htmlentities('Invalid nom d\'utilisateur / mot de passe'),
            'unknown' => 'Une erreur inconnue est survenue',
            'server_error' => htmlentities('Server ne marche pas, s\'il vous plaît réessayer plus tard'),
            'missing_input' => 'Il y a des informations manquantes',
            'missing_email' => htmlentities('Courriel ne se définit pas'),
            'missing_user' => htmlentities('Nom d\'utilisateur est pas défini '),
            'missing_password' => htmlentities('Mot de passe est pas défini '),
            'invalid_user' => htmlentities('L\'utilisateur ne peut pas être validée'),
            'not_logged_in' => htmlentities("Vous n'êtes pas connecté"),
            'no_action' => 'Qu\'est-ce, exactement, voulez-vous que je fasse !'
            ),
        'Spanish'=> array (
            'msg_txt_too_long'=>htmlentities("Tu mensaje de texto fue demasiado largo"),
           'USER_TAKEN'=>htmlentities("El nombre de usuario ya está en uso"),
            "DATABASE"=> htmlentities("Experimentando dificultades técnicas"),
            "NOT_DEACTIVATED"=>htmlentities("No se pudo desactivar el usuario"),
            "UNKNOWN_ACTION"=>htmlentities("La acción solicitada no está disponible"),

            'name_already_taken' =>htmlentities('Este nombre de usuario ya está tomado'),
            'invalid_user_pass' =>  htmlentities('Usuario / contraseña invalida'),
            'unknown' => 'Un error desconocido ha ocurrido',
            'server_error' => htmlentities('Servidor no funciona, por favor intente de nuevo más tarde'),
            'missing_input' => htmlentities('No hay información que falta'),
            'missing_email' => htmlentities('dirección de correo electrónico no está definido'),
            'missing_user' => htmlentities('Nombre de usuario no está definido'),
            'missing_password' => htmlentities('La contraseña no se define'),
            'invalid_user' => 'El usuario no puede ser validado',
            'not_logged_in' => "Usted no se ha identificado",
            'no_action' => htmlentities('¿Qué, exactamente, qué quieres que haga!')
            )
            
        );
    $dict = array (
        'English' => array(
            'dict_refresh' => htmlentities("Refresh"),
            'dict_all_quiet'=>htmlentities("All quiet here ..."),
            'dict_submit'=>htmlentities("Submit"),
            'dict_prev_data'=> htmlentities("Previous Data"),
            'dict_how_many_hours' => htmlentities("How many hours of previous chat?"),
            'dict_login'=>'Login',
            'dict_logoff'=>'Log off',
            'dict_current_user'=>'Currently logged in as',
            'dict_login_title'=>'CompSci Chat Room Login',
            'dict_chat_room'=>'Chat Room',
            'dict_title'=>'CompSci Chat Room',
            'dict_username'=>'Username',
            'dict_password'=>'Password',
            'dict_email'=>'email',
            'dict_new_user'=>'New User',
            'dict_cancel'=>'Cancel',
            'dict_create_account'=>'Create Account',
            'dict_deactivate_account'=>'Deactivate Account',
            'dict_user'=>'User',
            'dict_user_info'=>'User Information',
            'dict_missing_username' => 'You must specify a username',
            'dict_missing_email' => 'You must specify an email address',
            'dict_missing_password' => 'You must specify a password',
            'dict_processing' => 'Processing...',
            'dict_pass_not_same'=>'The two passwords do not match',
            'dict_select_langage'=>'Select language'
            ),
        'French' => array(
            'dict_refresh' => htmlentities("rafraîchir"),
            'dict_all_quiet'=>htmlentities("Tout est calme ici ..."),
            'dict_submit'=>htmlentities("Allons-y"),
            'dict_prev_data'=> htmlentities("Données Précédent"),
            'dict_how_many_hours' => htmlentities("Combien d'heures de discussion précédente?"),
            'dict_login'=>htmlentities('S\'identifier'),
            'dict_logoff'=>htmlentities('Se Déconnecter'),
            'dict_current_user'=>htmlentities('Actuellement connecté en tant que',ENT_QUOTES,'UTF-8'),
            'dict_login_title'=>'CompSci Chat Room Connexion',
            'dict_title'=>'CompSci Chat Room',
            'dict_chat_room'=>'Chat Room',
            'dict_username'=>htmlentities('Nom d\'utilisateur'),
            'dict_password'=>'Mot de passe',
            'dict_email'=>'courriel',
            'dict_new_user'=>'Nouvel Utilisateur',
            'dict_cancel'=>'Annuler',
            'dict_create_account'=>htmlentities('Créer un Compte'),
             'dict_deactivate_account'=>htmlentities('Désactiver le Compte'),
           'dict_user' => 'Utilisateur',
            'dict_user_info'=>'Informations Utilisateur',
            'dict_missing_username' => htmlentities('Vous devez spécifier un nom d\'utilisateur'),
            'dict_missing_email' => htmlentities('Vous devez spécifier une courriel'),
            'dict_missing_password' => htmlentities('Vous devez spécifier un mot de passe'),
            'dict_processing' => 'En traitement...',
            'dict_pass_not_same'=>'Les deux mots de passe ne correspondent pas',
            'dict_select_langage'=>'Choisir la langue'
            ),
        'Spanish' => array(
            'dict_refresh' => htmlentities("Refrescar"),
            'dict_all_quiet'=>htmlentities("Todo tranquilo aquí ..."),
            'dict_submit'=>htmlentities("Enviar"),
            'dict_prev_data'=> htmlentities("Los Datos Anteriores"),
            'dict_how_many_hours' => htmlentities("¿Cuántas horas de charla anterior?"),
            'dict_login'=>htmlentities('Iniciar sesión'),
            'dict_logoff'=>'Desconectarse',
            'dict_current_user'=>htmlentities('Actualmente está conectado como'),
            'dict_login_title'=>'CompSci Sala de Chat Entrar',
            'dict_title'=>'CompSci Sala de Chat',
            'dict_chat_room'=>'Sala de Chat',
            'dict_username'=>'Nombre de usuario',
            'dict_password'=>htmlentities('Contraseña'),
            'dict_email'=>htmlentities('correo electrónico'),
            'dict_new_user'=>'Nuevo Usuario',
            'dict_cancel'=>'Cancelar',
            'dict_create_account'=>'Crear una Cuenta',
             'dict_deactivate_account'=>htmlentities('Desactivar Cuenta'),
            'dict_user'=>'Usuario',
            'dict_user_info' => htmlentities('Información de usuario'),
            'dict_missing_username' => 'Debe especificar un nombre de usuario',
            'dict_missing_email' => htmlentities('Debe especificar una dirección de correo electrónico'),
            'dict_missing_password' => htmlentities('Debe especificar una contraseña'),
            'dict_processing' => 'tratamiento...',
            'dict_pass_not_same'=>htmlentities('Las dos contraseñas no coinciden'),
            'dict_select_langage'=>'Seleccione el idioma'
            )
        )
?>