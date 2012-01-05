<?php


include_once( dirname(__FILE__) . '/libs/Wol/Bootstrap.php');

spl_autoload_register(array('Wol_Bootstrap', 'autoload'));

$dirConf = dirname(__FILE__) . '/config/'; // The config files are here
	
session_start ();
   
	$USER = Wol_User::getInstance();
	$authentifier = Ldap_Authentication::getInstance();
	$mailer = Mailing_Sender::getInstance();  			
	$manager = Wol_Manager::getInstance();
	$usersManager = Wol_ManageUsers::getInstance();
	$hostsManager = Wol_ManageHosts::getInstance();
	$farmsManager = Wol_ManageFarms::getInstance();
	$userGroupManager = Wol_ManageUserGroups::getInstance();
	$hostGroupManager = Wol_ManageHostGroups::getInstance();
	$logger = Wol_Log_Logger::getInstance();
	$scheduler = Wol_Scheduler_CronManager::getInstance();
	$invitManager = Invitation_InvitManager::getInstance();
	$textManager = Mailing_TextManager::getinstance();
	$statsManager = Statistics_ManageStats::getinstance();
	$statsManager->loadConf($dirConf);

   if (! ( isset($db) && (db instanceof Wol_DB_Database) )) {
		$db = Wol_DB_Database::getInstance();
	}
	$db->connect();

	$authentifier->connect($dirConf);

	$urlOnOff = "http://" . $_SERVER['SERVER_NAME'] . strstr($_SERVER['REQUEST_URI'], "?");
	
   if (isset($_GET['ajax']) && ($_GET['ajax'] == "true") && isset($_GET['action'])) {
	
		$action = $_GET['action'];

		switch ($action) {
			case 'connect' :


				session_unset();
				$username = $_POST['username'];
				$password = $_POST['password'];

				$USER->authenticate($username, $password);
				$returnValues = array(
					'username' => $USER->getLogin(),
					'userid' => $USER->getId(),
					'admin' => $USER->isAuthenticatedAndAdmin(),
					'auth' => $USER->isAuthenticated(),
					'ldap' => $USER->IsLdapUser()
				);
				if ($USER->isAuthenticatedAndAdmin()) {
					$returnValues['userGroups'] = $userGroupManager->getSerialized();
					$returnValues['hostGroups'] = $hostGroupManager->getSerialized();
					$returnValues['farms'] = $farmsManager->getSerialized(false, false, false);
				}
				$_SESSION['idUser'] = $USER->getId();
				$_SESSION['login'] = $USER->getLogin();
				$_SESSION['pass'] = $password;
				$logger->addLog('connect', $USER->getId(), '', $USER->isAuthenticated());

				echo json_encode($returnValues);
			    
			break;
			
			case 'userpage':
				if ($USER->isAuthenticated()) {

				  $user_id = $_SESSION['idUser'];

					$informations = array( 
		   			'admin' => $USER->isAuthenticatedAndAdmin(),
						'hosts' => $manager->getSerializedHostsFromUser($user_id),
						'farms' => $manager->getSerializedFarmsFromUser($user_id)
					);

					$_SESSION['currentPage'] = 'userpage';
					echo json_encode($informations);	
				}
			break;
			
			case 'wol' :
				if ( (isset($_POST['id'])) && ($USER->isAuthenticated()) && (isset($_POST['type'])) ) {
					switch($_POST['type']) {
						case 'host' : $goodManager = $hostsManager;
						break;
						case 'farm' : $goodManager = $farmsManager;
						break;
						case 'group' : $goodManager = $hostGroupManager;
						break;
					}
					$return = $goodManager->getByID($_POST['id'])->wakeOnLan();
					$logger->addLog('wakeon', $_SESSION['idUser'], $_SESSION['login'] . ' send Magic packet to wake on ' . $_POST['type'] . " " . $goodManager->getByID($_POST['id'])->getName(), $return);
					echo json_encode (array('return' => $return));
				}	
			break;
			
			case 'turnoff' :
				if ( (isset($_POST['id'])) && ($USER->isAuthenticated()) && (isset($_POST['type'])) ) {
					switch($_POST['type']) {
						case 'host' : $goodManager = $hostsManager;
						break;
						case 'farm' : $goodManager = $farmsManager;
						break;
						case 'group' : $goodManager = $hostGroupManager;
						break;
					}
					$return = $goodManager->getByID($_POST['id'])->turnOff();
					$logger->addLog('turnoff', $_SESSION['idUser'], $_SESSION['login'] . ' asked to turn off ' . $_POST['type'] . " " . $goodManager->getByID($_POST['id'])->getName(), $return);
					echo json_encode (array('return' => $return));
				}	
			break;
			
			case 'getid' :
				if ($USER->isAuthenticated()) {
					echo json_encode(array('id' => $_SESSION['idUser']));	
				}
			break;
			
			case 'logout' :
				session_unset();
				echo json_encode(true);
			break;
		
			
			case 'getrefreshpage' :
				if ($USER->isAuthenticated()) {
					$return = array(
						'page' => $_SESSION['currentPage'], 
						'id' => (isset($_SESSION['currentPageID']) ? $_SESSION['currentPageID'] : 0)
					);
					echo json_encode ($return);
				}
			break;

			case 'setrefreshpage' :
				if ($USER->isAuthenticated()) {
					if (isset($_POST['page'])) {
						$_SESSION['currentPage'] = $_POST['page'];
					}
				}
			break;
			
			case 'getuserlist' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = $usersManager->getLoginsAndID();
					echo json_encode ($return);	
				}
			break;
			
			case 'adduser' :
				if ( (isset($_POST['login'])) && (isset($_POST['email'])) && (isset($_POST['pass'])) && ($USER->isAuthenticatedAndAdmin())) {
					$return = $usersManager->createUser($_POST['login'], $_POST['email'], $_POST['pass'], 'user');

					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $_POST['login'],
						'PASSWORD' => $_POST['pass']
					);
					$mailer->sendMail('addUser', $tokensMail, array($_POST['email']));
					
					$logger->addLog('addUser', $_SESSION['idUser'], $_SESSION['login'] . ' added new user with login ' . $_POST['login'] . ' and email ' . $_POST['email'], $return);
					echo json_encode (array('return' => $return));	
				}
			break;
			
			case 'changeemail':	
				if ( (isset($_POST['idUser'])) && (isset($_POST['newEmail'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$oldMail = $usersManager->getByID($_POST['idUser'])->getEmail();
					$return = $usersManager->changeEmail($_POST['idUser'], $_POST['newEmail']);

					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'OLDMAIL' => $oldMail,
						'NEWMAIL' => $_POST['newEmail']
					);
// 					$mailer->sendMail('changeEmail', $tokensMail, array($_POST['newEmail'], $_POST['oldEmail']));					
					
					$logger->addLog('changeEmail', $_SESSION['idUser'], $_SESSION['login'] . ' changed email of user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . '. New email : ' . $_POST['newEmail'], $return);					
					echo json_encode (array('return' => $return));				
				}
			break;
			
			case 'changerole':
				if ( (isset($_POST['idUser'])) && (isset($_POST['newRole'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $usersManager->changeRole($_POST['idUser'], $_POST['newRole']);
					
					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'NEWROLE' => $_POST['newRole']
					);
					$mailer->sendMail('changeRole', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );	
					
					$logger->addLog('changeRole', $_SESSION['idUser'], $_SESSION['login'] . ' changed role of user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . '. New role : ' . $_POST['newRole'], $return);					
					echo json_encode (array('return' => $return));				
				}
			break;
			
			case 'changepass':	
				if ( (isset($_POST['id'])) && (isset($_POST['newPass'])) && ($USER->isAuthenticated()) && ($_SESSION['idUser'] == $_POST['id']) ) {
					if ($return = $usersManager->changePassword($_POST['id'], $_POST['newPass'])) {
						
						// sends the email
						$tokensMail = array(
							'URL' => $urlOnOff,
							'LOGIN' => $usersManager->getByID($_POST['id'])->getLogin(),
						);
						$mailer->sendMail('changePassword', $tokensMail, array($usersManager->getByID($_POST['id'])->getEmail()) );	

						$logger->addLog('changePassword', $_SESSION['idUser'], $_SESSION['login'] . ' changed password of user ' . $usersManager->getByID($_POST['id'])->getLogin(), $return);					
						echo json_encode (true);
					}	else {
						echo json_encode (false);
					}	
				}
			break;		

			case 'addhosttouser' :
				if ( (isset($_POST['idUser'])) && (isset($_POST['idHost'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $usersManager->addHost($_POST['idUser'], $_POST['idHost']);
					
					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'HOSTNAME' => $hostsManager->getByID($_POST['idHost'])->getName(),
						'MAC' => $hostsManager->getByID($_POST['idHost'])->getMac()
					);
					$mailer->sendMail('addHostToUser', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );	
					
					$logger->addLog('addHostToUser', $_SESSION['idUser'], $_SESSION['login'] . ' added host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' to user ' . $usersManager->getByID($_POST['idUser'])->getLogin(), $return);					
					echo json_encode (array('return' => $return));
				}
			break;

			case 'removeHostUser':
				if ( (isset($_POST['idUser'])) && (isset($_POST['idHost'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $usersManager->removeHost($_POST['idUser'], $_POST['idHost']);

					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'HOSTNAME' => $hostsManager->getByID($_POST['idHost'])->getName(),
						'MAC' => $hostsManager->getByID($_POST['idHost'])->getMac()
					);
					$mailer->sendMail('removeHostToUser', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );	
					
					$logger->addLog('removeHostToUser', $_SESSION['idUser'], $_SESSION['login'] . ' removed host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' to user ' . $usersManager->getByID($_POST['idUser'])->getLogin(), $return);				
					echo json_encode (array('return' => $return));				
				}			
			break;
	
			case 'addhost':
				if ( (isset($_POST['hostname'])) && (isset($_POST['mac'])) && (isset($_POST['ip'])) && (isset($_POST['owner'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$mac2enter = '';
					for ($i=0;$i<6;$i++) {
					   $mac2enter .= substr ($_POST['mac'], 3 * $i, 2) ;
  				   } 
					$return = $hostsManager->createHost ($mac2enter, $_POST['hostname'], $_POST['ip'], $_POST['owner']);
					$logger->addLog('addHost', $_SESSION['idUser'], $_SESSION['login'] . ' created new host with hostname ' . $_POST['hostname'] .' and mac ' . $_POST['mac'], $return);				
					echo json_encode (array('return' => $return));		
				}
			break;
			
			case 'changeip' :
				if ( (isset($_POST['idHost'])) && (isset($_POST['newIP'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $hostsManager->changeInetAddr ($_POST['idHost'], $_POST['newIP']);
					$logger->addLog('changeIP', $_SESSION['idUser'], $_SESSION['login'] . ' changed ip of host ' . $hostsManager->getByID($_POST['idHost'])->getName() .' to value ' . $_POST['newIP'], $return);				
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'changeowner' :
				if ( (isset($_POST['idHost'])) && (isset($_POST['idNewOwner'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $hostsManager->changeOwner($_POST['idHost'], $_POST['idNewOwner']);
					$logger->addLog('changeOwner', $_SESSION['idUser'], $_SESSION['login'] . ' changed owner of host ' . $hostsManager->getByID($_POST['idHost'])->getName() .'. New owner : user number ' . $_POST['idNewOwner'], $return);						
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'changemac' :
				if ( (isset($_POST['idHost'])) && (isset($_POST['newMac'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$mac2enter = '';
					for ($i=0;$i<6;$i++) {
					   $mac2enter .= substr ($_POST['newMac'], 3 * $i, 2) ;
  				   } 
					$return = $hostsManager->changeMac ($_POST['idHost'], $mac2enter);
					$logger->addLog('changeMac', $_SESSION['idUser'], $_SESSION['login'] . ' changed mac address of host ' . $hostsManager->getByID($_POST['idHost'])->getName() .' to value ' . wordwrap($_POST['newMac'], 2, ":", 1), $return );						
					echo json_encode (array('return' => $return));
				}
			break;

			case 'deluser' :
				if (isset($_POST['id']) && ($USER->isAuthenticatedAndAdmin()) ) {
					$emailDeleted = $usersManager->getByID($_POST['id'])->getEmail();
					$loginDeleted = $usersManager->getByID($_POST['id'])->getLogin();
					$return = $usersManager->deleteUser($_POST['id']);
					
					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $loginDeleted
					);
					$mailer->sendMail('deleteUser', $tokensMail, array($emailDeleted) );	

					$logger->addLog('deleteUser', $_SESSION['idUser'], $_SESSION['login'] . ' deleted user ' . $loginDeleted . " (email : " . $emailDeleted . ")", $return );						
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'delhost' :
			 	if (isset($_POST['id']) && ($USER->isAuthenticatedAndAdmin()) ) {

					$listOfAssociatedUsers = $manager->getUsersAssociated($_POST['id']);

					// sends the emails
					foreach ($listOfAssociatedUsers as $user) {
						$tokensMail = array(
							'URL' => $urlOnOff,
							'LOGIN' => $user->getLogin(),
							'HOSTNAME' => $hostsManager->getByID($_POST['id'])->getName(),
							'MAC' => $hostsManager->getByID($_POST['id'])->getMac()
						);
						$mailer->sendMail('deleteHost', $tokensMail, array($user->getEmail()));			 		
			 		}
			 		
			 		$nameDeleted = $hostsManager->getByID($_POST['id'])->getName();
					$return = $hostsManager->deleteHost($_POST['id']);
					$logger->addLog('deleteHost', $_SESSION['idUser'], $_SESSION['login'] . ' deleted host ' . $nameDeleted, $return );
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'remhostfromfarm' :
				if ( (isset($_POST['idHost'])) && (isset($_POST['idFarm'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $farmsManager->removeHost ($_POST['idFarm'], $_POST['idHost']);
					$logger->addLog('removeHostFromFarm', $_SESSION['idUser'], $_SESSION['login'] . ' removed host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' from farm ' . $farmsManager->getByID($_POST['idFarm'])->getName() , $return );
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'remuserfromfarm' :
				if ( (isset($_POST['idUser'])) && (isset($_POST['idFarm'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $farmsManager->removeUser ($_POST['idFarm'], $_POST['idUser']);
					
					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'FARMNAME' => $farmsManager->getByID($_POST['idFarm'])->getName(),
					);
					$mailer->sendMail('removeUserFromFarm', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );	
					
					$logger->addLog('removeUserFromFarm', $_SESSION['idUser'], $_SESSION['login'] . ' removed user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . ' from farm ' . $farmsManager->getByID($_POST['idFarm'])->getName() , $return );
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'adduserinfarm' :
				if ( (isset($_POST['idUser'])) && (isset($_POST['idFarm'])) && ($USER->isAuthenticatedAndAdmin()) ) {	
					$return = $farmsManager->addUser($_POST['idFarm'], $_POST['idUser']);
					
					// sends the email
					$tokensMail = array(
						'URL' => $urlOnOff,
						'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
						'FARMNAME' => $farmsManager->getByID($_POST['idFarm'])->getName(),
					);
					$mailer->sendMail('addUserInFarm', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );		
	
					$logger->addLog('addUserInFarm', $_SESSION['idUser'], $_SESSION['login'] . ' added user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . ' to farm ' . $farmsManager->getByID($_POST['idFarm'])->getName() , $return );
					echo json_encode (array('return' => $return));			
				}			
			break;		
		
			case 'addhostinfarm' :
				if ( (isset($_POST['idHost'])) && (isset($_POST['idFarm'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $farmsManager->addHost($_POST['idFarm'], $_POST['idHost']);
					$logger->addLog('addHostInFarm', $_SESSION['idUser'], $_SESSION['login'] . ' added host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' to farm ' . $farmsManager->getByID($_POST['idFarm'])->getName() , $return );
					echo json_encode (array('return' => $return));					
				}
			break;
			
			case 'delfarm' :
				if ( (isset($_POST['id'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$farmDeleted = $farmsManager->getByID($_POST['id'])->getName();
					
					// sends the email
					foreach ($farmsManager->getByID($_POST['id'])->getUsers() as $user) {
						$tokensMail = array(
							'URL' => $urlOnOff,
							'LOGIN' => $user->getLogin(),
							'FARMNAME' => $farmDeleted,
						);
						$mailer->sendMail('deleteFarm', $tokensMail, array($user->getEmail()) );						
					}
					
					$return = $farmsManager->deleteFarm ($_POST['id']);					
					$logger->addLog('deleteFarm', $_SESSION['idUser'], $_SESSION['login'] . ' deleted farm ' . $farmDeleted, $return );
					echo json_encode (array('return' => $return));
				}
			break;
			
			case 'addfarm' :
				if ( (isset($_POST['farmname'])) && ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $farmsManager->createFarm ($_POST['farmname']);
					$logger->addLog('addFarm', $_SESSION['idUser'], $_SESSION['login'] . ' created new farm with farmname ' . $_POST['farmname'], $return);	
					echo json_encode ($return);
				}
			break;
			
			case 'seeallhosts' :
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $hostsManager->getSerialized();
					$_SESSION['currentPage'] = 'allhosts';
					echo json_encode (array( "hosts" => $return));
				}
			break;
			
			case 'seeallfarms' :
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					$return = $farmsManager->getSerialized(false, false, true); 
					$_SESSION['currentPage'] = 'allfarms';
					echo json_encode (array( "farms" => $return));
				}
			break;

			case 'seeallhostgroups' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = $hostGroupManager->getSerialized();
					$_SESSION['currentPage'] = 'allhostgroups';
					echo json_encode (array( "groups" => $return));
				} else if ($USER->isAuthenticated()) { /* if the user is not admin, we only send groups he has rights on */
					$return = array();
					$hostGroups = $hostGroupManager->getHostGroups();
					$userGroups = $userGroupManager->getUserGroups();
					foreach ( $hostGroups as $hGroup ) {
						$added = false;
						foreach ( $userGroups as $uGroup ) {
							if ($added!==false) {
								break;
							}
							if ($hGroup->getLevel() >= $uGroup->getLevel()) {
								if ( $uGroup->isInGroup($_SESSION['idUser']) ) {
									$return[] = $hGroup->getSerialized(false, $_SESSION['idUser']);
									$added = true;
								}
							}
						}
					}
					$_SESSION['currentPage'] = 'allhostgroups';
					echo json_encode (array( "groups" => $return));
				}
			break;			
			
			case 'seelogs' :
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					$_SESSION['currentPage'] = 'seelogs';

					echo json_encode ( array( 
						"logs" => $logger->getSerializedLogs( 
							(isset($_POST['idUser'])) ? $_POST['idUser'] : false,
							(isset($_POST['idEvent'])) ? $_POST['idEvent'] : false,
							(isset($_POST['date'])) ? $_POST['date'] : false					
						), 
						"users" => $usersManager->getLoginsAndID(), 
						"events" => $logger->getEventNamesAndID()
					) );
				}
			break;
			
			case 'schedule' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['typeOfSubject'])) && (isset($_POST['idSubject'])) && (isset($_POST['idTask'])) ) {
					
					/* Creates a new task */
					$newTask = new Wol_Scheduler_Task ();
					$newTask->setTypeOfSubject($_POST['typeOfSubject']);
					$newTask->setIDSubject($_POST['idSubject']);
					$newTask->setIDTask($_POST['idTask']);
										
					/* Check all sent parameters and set the task values */					
					if (isset($_POST['minutes'])) {
						$newTask->setMinute($_POST['minutes']);
					} else { $newTask->setMinute('*'); }
					if (isset($_POST['hours'])) {
						$newTask->setHour($_POST['hours']);							
					} else { $newTask->setHour('*'); }
					if (isset($_POST['dom'])) {
						$newTask->setDayOfMonth($_POST['dom']);							
					} else { $newTask->setDayOfMonth('*'); }
					if (isset($_POST['month'])) {
						$newTask->setMonth($_POST['month']);							
					} else { $newTask->setMonth('*'); }
					if (isset($_POST['dow'])) {
						$newTask->setDayOfWeek($_POST['dow']);							
					} else { $newTask->setDayOfWeek('*'); }

					/* Adds the task to Database and crontab */
					$return = $scheduler->addScheduledTask($newTask);	
					
					$logger->addLog('schedule', $_SESSION['idUser'], $_SESSION['login'] . ' scheduled a new cron task : ' . $return, $return !== false);
				
					echo json_encode ($return);
				}
			break;

			
			case 'getScheduledTasks' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = array( 
						'tasks' => $scheduler->getSerialized(),
					);
					echo json_encode ($return);
				}
			break;
			
			case 'deleteScheduledTask' :
				if ( $USER->isAuthenticatedAndAdmin() && (isset($_POST['idTask'])) ) {
					$return = $scheduler->removeScheduledTask($_POST['idTask']);
					echo json_encode ($return);
				}
			break;	
					
			case 'getUserGroups' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if (isset($_POST['id'])) {
						
						$groups = $userGroupManager->getByID($_POST['id'])->getSerialized(true);
						$users = $usersManager->getSerialized();
						$groups['allUsers'] = $users;
						echo json_encode ($groups);
					}
					$_SESSION['currentPage'] = 'userGroups'; $_SESSION['currentPageID'] = $_POST['id'];
				}
			break;		
			
			case 'addUserToGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['idUser'])) && (isset($_POST['idGroup']))) {
						$return = $userGroupManager->addUserToGroup($_POST['idGroup'], $_POST['idUser']);

						// sends the email
						$tokensMail = array(
							'URL' => $urlOnOff,
							'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
							'GROUPNAME' => $userGroupManager->getByID($_POST['idGroup'])->getName(),
						);
						$mailer->sendMail('addUserToGroup', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );		
							
						
						$logger->addLog('addUserToGroup', $_SESSION['idUser'], $_SESSION['login'] . ' added user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . ' to group ' . $userGroupManager->getByID($_POST['idGroup'])->getName(), $return);
					}
				}
				echo json_encode ($return);
			break;				

			case 'removeUserFromGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['idUser'])) && (isset($_POST['idGroup']))) {
						$return = $userGroupManager->removeUserFromGroup($_POST['idGroup'], $_POST['idUser']);
						
						// sends the email
						$tokensMail = array(
							'URL' => $urlOnOff,
							'LOGIN' => $usersManager->getByID($_POST['idUser'])->getLogin(),
							'GROUPNAME' => $userGroupManager->getByID($_POST['idGroup'])->getName(),
						);
						$mailer->sendMail('removeUserFromGroup', $tokensMail, array($usersManager->getByID($_POST['idUser'])->getEmail()) );		

						$logger->addLog('removeUserFromGroup', $_SESSION['idUser'], $_SESSION['login'] . ' deleted user ' . $usersManager->getByID($_POST['idUser'])->getLogin() . ' from group ' . $userGroupManager->getByID($_POST['idGroup'])->getName(), $return);
					}
				}
				echo json_encode ($return);
			break;
			
			case 'createUserGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['name'])) && (isset($_POST['level']))) {
						$return = $userGroupManager->createUserGroup($_POST['name'], $_POST['level']);
						$logger->addLog('createUserGroup', $_SESSION['idUser'], $_SESSION['login'] . ' created the group ' . $_POST['name'] . ' with level ' . $_POST['level'], $return);
						echo json_encode($return);
					}
				}
			break;
			
			case 'deleteUserGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if (isset($_POST['idGroup'])) {
						$groupName = $userGroupManager->getByID($_POST['idGroup'])->getName();
						
						// sends the email
						foreach ($userGroupManager->getByID($_POST['idGroup'])->getUsers() as $user) {
							$tokensMail = array(
								'URL' => $urlOnOff,
								'LOGIN' => $user->getLogin(),
								'GROUPNAME' => $groupName,
							);
							$mailer->sendMail('deleteUserGroup', $tokensMail, array($user->getEmail()) );						
						}
	
						$return = $userGroupManager->deleteUserGroup($_POST['idGroup']);				
						$logger->addLog('deleteUserGroup', $_SESSION['idUser'], $_SESSION['login'] . ' deleted the group ' . $groupName, $return);
					}
				}
			break;
			
			case 'getHostGroups' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if (isset($_POST['id'])) {
						$groups = $hostGroupManager->getByID($_POST['id'])->getSerialized(true);
						$hosts = $hostsManager->getSerialized();
						$groups['allHosts'] = $hosts;
						echo json_encode ($groups);
					}
					$_SESSION['currentPage'] = 'hostGroups'; $_SESSION['currentPageID'] = $_POST['id'];
				}
			break;		
			
			case 'addHostToGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['idHost'])) && (isset($_POST['idGroup']))) {
						$return = $hostGroupManager->addHostToGroup($_POST['idGroup'], $_POST['idHost']);
						$logger->addLog('addHostToGroup', $_SESSION['idUser'], $_SESSION['login'] . ' added the host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' to group ' . $hostGroupManager->getByID($_POST['idGroup'])->getName(), $return);					
					}
				}
				echo json_encode ($return);
			break;				

			case 'removeHostFromGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['idHost'])) && (isset($_POST['idGroup']))) {
						$return = $hostGroupManager->removeHostFromGroup($_POST['idGroup'], $_POST['idHost']);
						$logger->addLog('removeHostFromGroup', $_SESSION['idUser'], $_SESSION['login'] . ' removed the host ' . $hostsManager->getByID($_POST['idHost'])->getName() . ' from group ' . $hostGroupManager->getByID($_POST['idGroup'])->getName(), $return);
					}
				}
				echo json_encode ($return);
			break;
			
			case 'createHostGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ((isset($_POST['name'])) && (isset($_POST['level']))) {
						$return = $hostGroupManager->createHostGroup($_POST['name'], $_POST['level']);
						$logger->addLog('createHostGroup', $_SESSION['idUser'], $_SESSION['login'] . ' created the hostgroup ' . $_POST['name'] . ' with level ' . $_POST['level'], $return);
						echo json_encode ($return);
					}
				}
			break;
			
			case 'deleteHostGroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if (isset($_POST['idGroup'])) {
						$return = $hostGroupManager->deleteHostGroup($_POST['idGroup']);
						$logger->addLog('deleteHostGroup', $_SESSION['idUser'], $_SESSION['login'] . ' deleted the hostgroup ' . $hostGroupManager->getByID($_POST['idGroup'])->getName(), $return);
					}
				}
			break;							
			
			case 'getFarms' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if (isset($_POST['id'])) {
						$farms = $farmsManager->getByID($_POST['id'])->getSerialized(true, true, true);
						$hosts = $hostsManager->getSerialized();
						$users = $usersManager->getSerialized();
						$farms['allHosts'] = $hosts;
						$farms['allUsers'] = $users;
						echo json_encode ($farms);
					}
					$_SESSION['currentPage'] = 'getFarms'; $_SESSION['currentPageID'] = $_POST['id'];
				}
			break;
			
			case 'manageusers' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$informations = array(
						'users' => $usersManager->getSerialized(true, false),
						'hosts' => $hostsManager->getSerialized(),
						'ldapOK' => $authentifier->ldapIsUsed()
					);
					echo json_encode($informations);
				}
				$_SESSION['currentPage'] = 'manageusers';
			break;

			case 'managehosts' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$informations = array(
						'users' => $usersManager->getSerialized(true, false), /* To choose the owner in select list */
						'hosts' => $hostsManager->getSerialized()
					);
					echo json_encode($informations);
				}
				$_SESSION['currentPage'] = 'managehosts';
			break;

			case 'getHostsFarmsAndGroups' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$informations = array(
						'hosts' => $hostsManager->getSerialized(),
						'farms' => $farmsManager->getSerialized(true, false, false),
						'groups' => $hostGroupManager->getSerialized(true)
					);
					echo json_encode($informations);
				}
			break;		
			
			case 'manageperms' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$informations = array(
						'hostGroups' => $hostGroupManager->getSerialized(true),
						'userGroups' => $userGroupManager->getSerialized(true)
					);
					echo json_encode($informations);
				}
				$_SESSION['currentPage'] = 'manageperms';
			break;

			case 'changepermhostgroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ( (isset($_POST['idHostGroup'])) && (isset($_POST['level'])) ) {
						$return = $hostGroupManager->changeLevel($_POST['idHostGroup'], $_POST['level']);
						$logger->addLog('changepermhostgroup', $_SESSION['idUser'], $_SESSION['login'] . ' changed the permission of host group' . $hostGroupManager->getByID($_POST['idHostGroup'])->getName() . ' to value ' . $_POST['level'], $return);
						echo json_encode($return);
					}
				}
			break;
			
			case 'changepermusergroup' : 
				if ( ($USER->isAuthenticatedAndAdmin()) ) {
					if ( (isset($_POST['idUserGroup'])) && (isset($_POST['level'])) ) {
						$return = $userGroupManager->changeLevel($_POST['idUserGroup'], $_POST['level']);
						$logger->addLog('changepermusergroup', $_SESSION['idUser'], $_SESSION['login'] . ' changed the permission of user group' . $userGroupManager->getByID($_POST['idUserGroup'])->getName() . ' to value ' . $_POST['level'], $return);
						echo json_encode($return);
					}
				}
			break;
			
			case 'forgotpass' :
				if (isset($_POST['emailOfUser'])) {
					$pass = false;
					if ( ($user = $usersManager->getByEmail($_POST['emailOfUser'])) !== false ) {
						if ($user instanceof Wol_UserLdap) {
							echo json_encode(array("return" => false, "error" => "Can't set a new password for you. You are logged in with LDAP."));
						} else {
							$pass = $usersManager->generatePassForUser($user);
							$logger->addAnonymousLog('generatePass', 'Ask new password for user with email ' . $_POST['emailOfUser'], ($pass !== false));
							
							// sends the email
							$tokensMail = array(
								'URL' => $urlOnOff,
								'LOGIN' => $user->getLogin(),
								'NEWPASS' => $pass,
							);
							$mailer->sendMail('generatePass', $tokensMail, array($user->getEmail()) );		

							echo json_encode(array("return" => $pass !== false, "error" => "An error occured"));
						}
					} else {
						echo json_encode(array("return" => false));
					}
				}
			break;
			
			case 'hostsdiscover' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = $hostsManager->hostsDiscover();
					echo json_encode(array("hosts" => $return, "users" => $usersManager->getSerialized(true) /* To choose the owner in select list */));
				}
			break;	
			
			case 'managehostsofuser' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['idUser'])) ) {
					$user = $usersManager->getByID($_POST['idUser']);
					$hostsOfUser = $user->getHosts();
					$idHostsOfUser = array();
					foreach ($hostsOfUser as $hou) {
						$idHostsOfUser[] = $hou->getID();	
					}
					$return = array(
						'id' => $user->getID(),
						'login' => $user->getLogin(),
						'role' => $user->getRole(),
						'hostsofuser' => $idHostsOfUser,
						'allhosts' => $hostsManager->getSerialized(),
						'customNames' => $usersManager->getCustomHostnamesForUser($_POST['idUser'])
					);
					$_SESSION['currentPage'] = 'managehostsofuser'; $_SESSION['currentPageID'] = $_POST['idUser'];
					echo json_encode($return);
				}			
			break;
			
			case 'changehostnameforuser' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['idUser'])) && (isset($_POST['idHost'])) && (isset($_POST['newName'])) ) {
					$usersManager->setHostNameForUser ($_POST['idUser'], $_POST['idHost'], $_POST['newName']);
					echo json_encode($return);
				}			
			break;
			
			case 'getcurrentldapconfig' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$conf = $authentifier->getLdapConfig ($dirConf);
					echo json_encode($conf);
					$_SESSION['currentPage'] = 'ldapconf';
				}					
			break;
			
			case 'getdefaultldapconfig' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$conf = $authentifier->getDefaultLdapConfig ($dirConf);
					echo json_encode($conf);
				}					
			break;
			
			case 'setldapconfig' :
				if ($USER->isAuthenticatedAndAdmin()) {
					if ( (isset($_POST['useldap'])) && (isset($_POST['port'])) && (isset($_POST['host'])) && (isset($_POST['basedn']))
					&& (isset($_POST['binddn'])) && (isset($_POST['bindpw'])) && (isset($_POST['usersdn'])) && (isset($_POST['userloginattr']))
					&& (isset($_POST['userpwdattr'])) && (isset($_POST['usermailattr'])) && (isset($_POST['useridattr'])) ) {
						$params = array(
							"useldap" => $_POST['useldap'],
							"port" => $_POST['port'],
							"host" => $_POST['host'],
							"basedn" => $_POST['basedn'],
							"binddn" => $_POST['binddn'],
							"bindpw" => $_POST['bindpw'],
							"usersdn" => $_POST['usersdn'],
							"userloginattr" => $_POST['userloginattr'],
							"userpwdattr" => $_POST['userpwdattr'],
							"usermailattr" => $_POST['usermailattr'],
							"useridattr" => $_POST['useridattr']						
						);						
						echo json_encode($authentifier->setLdapConfig ($dirConf, $params));									
					}
				}								
			break;
			
			case 'changefarmview' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['idFarm'])) && (isset($_POST['seeall'])) ) {
					$return = $farmsManager->changeUserView($_POST['idFarm'], $_POST['seeall']);
					echo json_encode($return);
				}
			break;
			
			case 'importusersformldap' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$ldapUsers = $authentifier->getAllUsers();
					foreach ($ldapUsers as $user) {
						if ( $usersManager->getByLdapID($user['ldapID']) === false ) {
							if ( ($user['email'] === null) || ($user['email'] === "") ) {
								$user['email'] = "no email for " . $user['login'];
							}
							$usersManager->createLdapUser($user['login'], $user['email'], $user['ldapID'], 'user');
						}
					}
				echo json_encode(true);
				}
			break;
			
			case 'viewinvit' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = array(
						'invitations' => $invitManager->getSerialized(),
						'hosts' => $hostsManager->getSerialized(true),
						'farms' => $farmsManager->getSerialized(true, false, false),
					);	
					echo json_encode($return);
				}
				$_SESSION['currentPage'] = 'viewinvit';
			break;
					
			case 'delinvit' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['id'])) ) {
					$return = $invitManager->deleteInvitation($_POST['id']);
					echo json_encode($return);	
				}			
			break;
			
			case 'addinvit' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['people'])) && (isset($_POST['project'])) && (isset($_POST['typeOfSubject']))
				&& (isset($_POST['idSubject'])) && (isset($_POST['endDay'])) && (isset($_POST['endMonth'])) && (isset($_POST['endYear'])) && (isset($_POST['email'])) ) {
					
					$toHash = $_POST['people'] . $_POST['project'] . $_POST['email'] . date(DATE_RFC822);
					$hash = md5($toHash);
					
					if ( (isset($_POST['beginDay'])) && (isset($_POST['beginMonth'])) && (isset($_POST['beginYear'])) ) {
						$return = $invitManager-> addInvitation ($_POST['people'], $_POST['project'], $_POST['typeOfSubject'], $_POST['idSubject'], $_POST['email'], $hash,
																				$_POST['beginDay'], $_POST['beginMonth'], $_POST['beginYear'],
																				$_POST['endDay'], $_POST['endMonth'], $_POST['endYear']);
					} else {
						$return = $invitManager->addInvitationFromNow ($_POST['people'], $_POST['project'], $_POST['typeOfSubject'], $_POST['idSubject'], $_POST['email'], $hash,
																						$_POST['endDay'], $_POST['endMonth'], $_POST['endYear']);
					}
					
					if ($return === true) {
						$goodManager = ( ($_POST['typeOfSubject'] === 'host') ? $hostsManager : $farmsManager );
						$url = "http://" . $_SERVER['SERVER_NAME'] . strstr($_SERVER['REQUEST_URI'], "?", true) . 
								 "?ajax=true&action=externhash&hash=" . $hash;
						$mailer->sendInvitation($_POST['people'], $_POST['project'], $_POST['email'], $_POST['typeOfSubject'], 
														$goodManager->getByID($_POST['idSubject'])->getName(),  $url, 
														$_POST['beginDay'] ."-". $_POST['beginMonth'] ."-". $_POST['beginYear'], 
														$_POST['endDay'] ."-". $_POST['endMonth'] ."-". $_POST['endYear'],
														$usersManager->getByID($_SESSION['idUser'])->getEmail());
					}
					echo json_encode($return);	
				}					
			break;
			
			case 'getStatus' :
				if ( ($USER->isAuthenticated()) && (isset($_POST['id'])) ) {
					$hostsManager->updateStatus($_POST['id']);
					$status = $hostsManager->getByID($_POST['id'])->getStatus();
					echo json_encode(array('state' => $status, 'idHost' => $_POST['id']));
				}
			break;

			case 'externhash' :
				require dirname(__FILE__) . "/templates/invitPageForExternUser.html";
				if (isset($_GET['hash'])) {
					$invit = $invitManager->getByHash($_GET['hash']);
					if ($invitManager->isValidRequest($_GET['hash'])) {
						$invit->getToPower()->wakeOnLan();
						$message = 'The ' . (($invit->getToPower() instanceof Wol_Host)?'host':'farm') . " " . $invit->getToPower()->getName() . " has been sent a wake on message !" ;
						$people = $invit->getPeopleName();
						$project = "Project " . $invit->getProjectName();
					} else {
						if ($invit === false) {
							$message = "Sorry, this is an invalid hash";
							$people = '';
							$project = '';
						} else {
							$message = "Sorry, this link is valid from " . $invit->getBegin() . " to " . $invit->getEnd() . ".";
							$people = $invit->getPeopleName();
							$project = "Project " . $invit->getProjectName();
						}	
					}
				} else {
					$message = "Sorry, this is an invalid hash";
				}
				echo '<script type="text/javascript" >' . PHP_EOL .
					'$("#message").html("' . $message . '");' . PHP_EOL .
					'$("#project").html("' . $project . '");' . PHP_EOL .
					'$("#peopleName").html("' . $people . '");' . PHP_EOL .									
				'</script>';
			break;
			
			case 'getCustomStats':
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['idHost'])) ) {
					if ( ($hostsStats = $statsManager->getByHostID($_POST['idHost'])) !== false ) { 
						$return = $hostsStats->getSerialized();
					} else {
						$return = false;
					}
				}
				$_SESSION['currentPage'] = 'stats';
				echo json_encode($return);
			break;

			case 'getDailyStats' :
				if ( ($USER->isAuthenticatedAndAdmin()) && (isset($_POST['day'])) && (isset($_POST['month'])) && (isset($_POST['year'])) ) {
					$return = $statsManager->getHostsOnDate($_POST['day'], $_POST['month'], $_POST['year']);
				}
				echo json_encode($return);
				$_SESSION['currentPage'] = 'stats';
			break;
						
			case 'getHostsNamesAndID' :
				if ($USER->isAuthenticatedAndAdmin()) {
					echo json_encode(array('hosts' => $hostsManager->getSerialized(true)));		
				}		
			break;
		
			case 'getStatsSettings' :
				if ($USER->isAuthenticatedAndAdmin()) {
					$return = $statsManager->getStatConfig($dirConf);
					echo json_encode($return);
				}
				$_SESSION['currentPage'] = 'statsconf';			
			break;
			
			case 'setstatconfig' :
				if ($USER->isAuthenticatedAndAdmin()) {
					if ( (isset($_POST['nbWeeks'])) && (isset($_POST['refresh'])) ) {
						$params = array(
							"weeks_to_keep" => $_POST['nbWeeks'],
							"refresh_minutes" => $_POST['refresh']				
						);
						$statsManager->setStatConfig($dirConf, $params);
						echo json_encode(true);									
					}
				}						
			break;

			case 'getActionsAndLangs' :
				if ($USER->isAuthenticated()) {
					$return = array(
						'actions' => $textManager->getMailableActions(),
						'lang' => $textManager->getLangs()
					);
					echo json_encode($return);
				}
			break;

			case 'getMailText' :
				if ($USER->isAuthenticated() ){
					
					if ( (isset($_POST['action'])) && (isset($_POST['lang'])) ) {
						if ($textManager->existsTextByActionID ($_POST['action'], $_POST['lang']) ) {
							echo json_encode($textManager->getData ($_POST['action'], $_POST['lang']));	
						} else {
							echo json_encode($textManager->getData ($_POST['action']));	
						}
					}
				}
			break;
			
			case 'setMailText':
				if ($USER->isAuthenticatedAndAdmin()) {
					if ( (isset($_POST['action'])) && (isset($_POST['lang'])) && (isset($_POST['sender'])) && (isset($_POST['subject'])) && (isset($_POST['text'])) && (isset($_POST['isActive'])) ) {
						$return = $textManager->setMailSettings ($_POST['action'], $_POST['lang'], $_POST['sender'], $_POST['subject'], $_POST['text'], (($_POST['isActive']==='true')?true:false));
					}
					echo json_encode($return);
				}
			break;

			case 'recoversession':
				  if($USER->isAuthenticated()){
				      
				      $values = array('auth' => 'true',
						      'login' => $_SESSION['login'],
						      'password' => $_SESSION['pass']
				      );

				  } else {
				      $values = array('auth' => 'false');
				 }
			echo json_encode($values);
			break;
				
			default: break;
		}

	exit(0);

	}

require dirname(__FILE__) . "/header.php";
require dirname(__FILE__) . "/footer.php";
