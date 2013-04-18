<?php
// installation
$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_pluginlibrary_missing'] = "<a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> está faltando. Instale antes de usar MyFacebook Connect.";

// settings
$l['myfbconnect_settings'] = "Configurações de registro e login do facebook";
$l['myfbconnect_settings_desc'] = "Aqui você pode gerenciar registros e login em seu fórum, modificando API´s e demais opções de manuseio.";
$l['myfbconnect_settings_enable'] = "Chave Mestra";
$l['myfbconnect_settings_enable_desc'] = "Você deseja que seus usuários registrem-se e loguem-se em seu fórum usando o Facebook? Se um usuário estiver conectado ao Facebook - a sua conta será linkada.";
$l['myfbconnect_settings_appid'] = "App ID ( Identificação de Aplicativo ) ";
$l['myfbconnect_settings_appid_desc'] = "Adicione o seu AP ID Token de seu site de desenvolvimento de aplicativos do Facebook. Este será usado com o token secreto para autorizar uso do aplicativo.";
$l['myfbconnect_settings_appsecret'] = "App Secret ( App Secreto )";
$l['myfbconnect_settings_appsecret_desc'] = "Adicione o seu APP Token  secreto de seu site de desenvolvimento de aplicativos do Facebook. Este será usado com o token secreto para autorizar uso do aplicativo.";
$l['myfbconnect_settings_fastregistration'] = "Registro em 1-click";
$l['myfbconnect_settings_fastregistration_desc'] = "Se esta opção estiver habilitada, quando um usuário quiser utilizar o Facebook ele será questionado pelo seu aplicativo se este for o seu primeiro login, entçao ele será logado e registrado imediatamente sem questionar nome de usuário e outras informações sincronizadas.";
$l['myfbconnect_settings_usergroup'] = "Grupo de Usuários após registro";
$l['myfbconnect_settings_usergroup_desc'] = "Adicione o ID do grupo de usuários para identificar registro via MyFacebook Connect. Por padrão ( 2 ), equivalente ao membro registrado em seu fórum.";
$l['myfbconnect_settings_requestpublishingperms'] = "Requisitando permissões de publicação";
$l['myfbconnect_settings_requestpublishingperms_desc'] = "Se esta opção estiver habilitada o usuário será questionado por opções extras de publicação. <b>Esta opção deve ser deixada desabilitada (sem função em particular no momento). No futuro será crucial para postar no muro do usuário registrados ou logados em seu fórum.";
$l['myfbconnect_settings_passwordpm'] = "Envie mensagem pessoal após registro";
$l['myfbconnect_settings_passwordpm_desc'] = "Se esta opção estiver ativada, o usuário será notificado com um mensagem pessoal dizendo a sua senha gerada aleatoriamente sobre a sua inscrição.";
$l['myfbconnect_settings_passwordpm_subject'] = "Assunto da Mensagem Pessoal";
$l['myfbconnect_settings_passwordpm_subject_desc'] = "Escolha uma msg para nas mensagen pessoais geradas automaticamente.";
$l['myfbconnect_settings_passwordpm_message'] = "Mensagem Pessoal";
  $l['myfbconnect_settings_passwordpm_message_desc'] = "Escreva uma mensagem padrão que será enviado para os usuários registrados quando se registam com o Facebook. {user} e {password} são variáveis geradas aleatoriamente e referem-se ao nome de usuário e a senha gerada aleatoriamente por último: eles devem estar lá, mesmo se você modificar a mensagem padrão. HTML e BBCode são permitidas aqui.";
$l['myfbconnect_settings_passwordpm_fromid'] = "Mensagem Pessoal Enviada";
$l['myfbconnect_settings_passwordpm_fromid_desc'] = "Insira o UID do usuário que será o remetente da Mensagem Pessoal. Por padrão é definido como 0, que é o MyBB , mas você pode mudá-lo para o que quiser.";
// custom fields support, yay!
$l['myfbconnect_settings_fbbday'] = "Sincronizar data de nascimento";
$l['myfbconnect_settings_fbbday_desc'] = "Se você gostaria de importar data de nascimento do Facebook (e permitir que os usuários decidam sincronizá-lo) ative essa opção.";
$l['myfbconnect_settings_fblocation'] = "Sincronizar localidade";
$l['myfbconnect_settings_fblocation_desc'] = "Se você gostaria de importar Localização do Facebook (e permitir que os usuários decidam sincronizá-lo) habilite esta opção.";
$l['myfbconnect_settings_fblocationfield'] = "Id do Campos do Perfil sobre Localidade";
$l['myfbconnect_settings_fblocationfield_desc'] = "Insira o ID do perfil de campo personalizado que corresponde ao campo Local. Certifique-se de que é o ID correto! Padrão para 1 (padrão do MyBB)";
$l['myfbconnect_settings_fbbio'] = "Sincronizar biografia";
$l['myfbconnect_settings_fbbio_desc'] = "Se você gostaria de importar Biografia do Facebook (e permitir que os usuários decidam sincronizá-lo) habilite esta opção.";
$l['myfbconnect_settings_fbbiofield'] = "ID do campo de perfil sobre Biografia";
$l['myfbconnect_settings_fbbiofield_desc'] = "Insira o ID do perfil de campo personalizado que corresponde ao campo Biografia. Certifique-se de que é o ID correto! Padrão para 2 (default do MyBB)";
$l['myfbconnect_settings_fbdetails'] = "Sincronizar primeiro e último nome";
$l['myfbconnect_settings_fbdetails_desc'] = "Se você gostaria de importar primeiro e último nome do Facebook (e permitir que os usuários decidam sincronizá-lo) ative essa opção.";
$l['myfbconnect_settings_fbdetailsfield'] = "ID do campo de perfil sobre Primeiro e Último nome";
$l['myfbconnect_settings_fbdetailsfield_desc'] = "Insira o ID do perfil de campo personalizado que corresponde ao primeiro e último campo nome. Certifique-se de que é o ID correto! Padrão = null ( vazio ) (MyBB nçao possui)";
$l['myfbconnect_settings_fbsex'] = "Sincronizar gênero";
$l['myfbconnect_settings_fbsex_desc'] = "<b>Esta opção NÃO FUNCIONA corretamente no momento.</b>Infelizmente, o gênero é um dos maiores problemas que eu tenho enfrentado, já que é um campo de perfil personalizado, com valores que variam de fórum para fórum.. I'll work on it. The synchronization works (but it causes fields to be italian, so if you are used to PHP you can modify its function in inc/plugins/myfbconnect.php). Just leave it disabled if you don't want to use an useless button.";
$l['myfbconnect_settings_fbsexfield'] = "ID do campo de perfil sobre Gênero";
$l['myfbconnect_settings_fbsexfield_desc'] = "Insira o ID do perfil de campo personalizado que corresponde ao campo Gênero. Certifique-se de que é o ID correto! Padrão ( 3 )(padrão do MyBB)";

// default pm text
$l['myfbconnect_default_passwordpm_subject'] = "Senha nova";
$l['myfbconnect_default_passwordpm_message'] = "Bem-vindo ao fórum, querido {user}!

Estamos felizes que você está registrando com o Facebook. Nós geramos uma senha aleatória para você que você deve tomar nota em algum lugar, se você gostaria de mudar suas infos pessoais. Exigimos que por razões de segurança que você especifique sua senha quando você mudar as coisas, como e-mail, seu nome de usuário e a senha em si, de modo a manter em segredo!

A sua senha é : [b]{password}[/b]

Com carinho,
nossa equipe";
