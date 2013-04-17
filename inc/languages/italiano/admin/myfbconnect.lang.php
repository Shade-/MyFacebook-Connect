<?php
// installation
$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_pluginlibrary_missing'] = "<a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> è assente. Installalo prima di utilizzare MyFacebook Connect.";

// settings
$l['myfbconnect_settings'] = "Login con Facebook";
$l['myfbconnect_settings_desc'] = "Qui puoi gestire le impostazioni per il login con Facebook, come inserire ID e secret token della tua applicazione.";
$l['myfbconnect_settings_enable'] = "Interruttore generale";
$l['myfbconnect_settings_enable_desc'] = "Desideri utilizzare il login con Facebook per i tuoi utenti? Questo interruttore generale ti aiuta a disattivare con un click tutto il sistema.";
$l['myfbconnect_settings_appid'] = "App ID";
$l['myfbconnect_settings_appid_desc'] = "Inserisci l'ID della tua applicazione creata su Facebook. Verrà utilizzata insieme alla secret token per ottenere i dati degli utenti.";
$l['myfbconnect_settings_appsecret'] = "App Secret";
$l['myfbconnect_settings_appsecret_desc'] = "Inserisci la secret token della tua applicazione creata su Facebook. Verrà utilizzata insieme all\'ID per ottenere i dati degli utenti.";
$l['myfbconnect_settings_usergroup'] = "Gruppo post-registrazione";
$l['myfbconnect_settings_usergroup_desc'] = "Inserisci il gruppo in cui verrà inserito un utente dopo la registrazione con Facebook (che avviene automaticamente cliccando il pulsante Login con Facebook). Di default è il 2, quello dedicato ai membri registrati.";
$l['myfbconnect_settings_fastregistration'] = "Registrazione one-click";
$l['myfbconnect_settings_fastregistration_desc'] = "Se quest'opzione è abilitata, la registrazione degli utenti attraverso Facebook sarà processata immediatamente senza chiedere all'utente un nome utente personalizzato né quali informazioni sincronizzare con Facebook. Verrà utilizzato il nome e il cognome per il nome utente e nel caso in cui risultasse già registrato verrà chiesto di scegliere un nome utente differente. Di default verranno sincronizzate tutte le informazioni possibili.";
$l['myfbconnect_settings_passwordpm'] = "Invia MP alla registrazione";
$l['myfbconnect_settings_passwordpm_desc'] = "Se quest'opzione è abilitata, quando un utente si registra con Facebook viene inviato un MP contenente la password generata casualmente durante la registrazione.";
$l['myfbconnect_settings_requestpublishingperms'] = "Richiedi permessi di pubblicazione";
$l['myfbconnect_settings_requestpublishingperms_desc'] = "Se quest'opzione è abilitata, quando un utente autorizza la tua applicazione verranno richiesti anche permessi per postare sulla propria timeline.";
$l['myfbconnect_settings_passwordpm'] = "Invia MP alla registrazione";
$l['myfbconnect_settings_passwordpm_desc'] = "Se quest'opzione è abilitata, verrà inviato un MP contenente la password generata casualmente ad ogni utente che si registra con Facebook.";
$l['myfbconnect_settings_passwordpm_subject'] = "Titolo dell'MP";
$l['myfbconnect_settings_passwordpm_subject_desc'] = "Scegli un titolo da dare all'MP da inviare.";
$l['myfbconnect_settings_passwordpm_message'] = "Messaggio dell'MP";
  $l['myfbconnect_settings_passwordpm_message_desc'] = "Scrivi un messaggio chiaro e conciso contenente qualche informazione e la password generata. Le variabili {user} e {password} si riferiscono rispettivamente al nome utente del nuovo utente registrato e alla password generata casualmente.";
$l['myfbconnect_settings_passwordpm_fromid'] = "Mittente";
$l['myfbconnect_settings_passwordpm_fromid_desc'] = "Inserisci l'UID del mittente dell'MP. Di default è 0 che equivale all'utente predefinito MyBB Engine, ma puoi modificarlo a piacimento. Assicurati che l'UID esista.";
// custom fields support, yay!
$l['myfbconnect_settings_fbbday'] = "Sincronizza data di nascita";
$l['myfbconnect_settings_fbbday_desc'] = "Se vuoi importare la data di nascita (e lasciare che gli utenti decidano se farlo) abilita quest'opzione.";
$l['myfbconnect_settings_fblocation'] = "Sincronizza località";
$l['myfbconnect_settings_fblocation_desc'] = "Se vuoi importare la località (e lasciare che gli utenti decidano se farlo) abilita quest'opzione.";
$l['myfbconnect_settings_fblocationfield'] = "ID del Campo Profilo personalizzato della località";
$l['myfbconnect_settings_fblocationfield_desc'] = "Inserisci l'ID del Campo Profilo personalizzato in cui verrà inserita la località in sincronizzazione. Di default è impostato a 1 (così come MyBB lo imposta allo startup).";
$l['myfbconnect_settings_fbbio'] = "Sincronizza biografia";
$l['myfbconnect_settings_fbbio_desc'] = "Se vuoi importare la biografia (e lasciare che gli utenti decidano se farlo) abilita quest'opzione.";
$l['myfbconnect_settings_fbbiofield'] = "ID del Campo Profilo personalizzato della biografia";
$l['myfbconnect_settings_fbbiofield_desc'] = "Inserisci l'ID del Campo Profilo personalizzato in cui verrà inserita la biografia in sincronizzazione. Di default è impostato a 2 (così come MyBB lo imposta allo startup).";
$l['myfbconnect_settings_fbdetails'] = "Sincronizza nome e cognome";
$l['myfbconnect_settings_fbdetails_desc'] = "Se vuoi importare nome e cognome (e lasciare che gli utenti decidano se farlo) abilita quest'opzione.";
$l['myfbconnect_settings_fbdetailsfield'] = "ID del Campo Profilo personalizzato del nome e cognome";
$l['myfbconnect_settings_fbdetailsfield_desc'] = "Inserisci l'ID del Campo Profilo personalizzato in cui verranno inseriti nome e cognome in sincronizzazione. Di default è vuoto (MyBB non lo prevede, puoi crearlo tu)";
$l['myfbconnect_settings_fbsex'] = "Sincronizza sesso";
$l['myfbconnect_settings_fbsex_desc'] = "<b>Quest'opzione funziona solo per board italiane.</b> Se sei italiano, puoi abilitarla e usarla normalmente. I valori inseriti saranno <b>Uomo</b> e <b>Donna</b>, a prescindere da quelli impostati nel Campo Profilo personalizzato corrispondente.";
$l['myfbconnect_settings_fbsexfield'] = "ID del Campo Profilo personalizzato del sesso";
$l['myfbconnect_settings_fbsexfield_desc'] = "Inserisci l'ID del Campo Profilo personalizzato in cui verrà inserito il sesso in sincronizzazione. Di default è impostato a 3 (così come MyBB lo imposta allo startup)";

// default pm text
$l['myfbconnect_default_passwordpm_subject'] = "Nuova password";
$l['myfbconnect_default_passwordpm_message'] = "Benvenuto sul nostro Forum, {user}!

Siamo felici ti sia registrato attraverso Facebook. Abbiamo generato una password casuale per il tuo account che solo tu conosci e che serve a modificare le informazioni personali come l'email, il nome utente e la password stessa che per motivi di sicurezza devono essere modificati conoscendo la password dell'account. Tienila segreta o cambiala a piacimento al più presto!

La tua password casuale è: [b]{password}[/b]

Distinti saluti,
il Team del Forum";