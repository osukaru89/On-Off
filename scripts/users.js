/**
 * Gets the data of all users, and generates a page to manage them.
 * Calls the actions manageusers and adduser
 * Uses the templates "usersAdmin.html" and "addUserForm.html"
 * @function
 */
function manageUsers () {
	deletePeriodicGetStatus();
	$.ajax({
	  	url: "?ajax=true&action=manageusers",
	  	type: 'post',
	  	success: function(data){	
	  		if (data == null) { 
 				logout(); 
 				return false; 
 			}
 			
			var saveData=data;
		  	var users = data['users'];  	
			var hosts = data['hosts'];		
				
			$('#content').html('<div id="generalWrapper">');

			$.get('templates/usersAdmin.html', function(data) {
				$("#generalWrapper").html( $.tmpl(data, saveData) );
				
  				$(".manageHostsUser").button();
  				$("#importFromLdap").button();
  				
				$( "#dialog-usersAdmin" ).dialog({
					autoOpen: false,
					resizable: false,
					height:240,
					modal: true,
					buttons: {
						"OK": function() {
							deleteUser ($(".toDelete")[0].id);
							$(".toDelete td").removeClass("ui-state-error");
							$(this).dialog("close");
						},
						Cancel: function() {
							$(".toDelete td").removeClass("ui-state-error");
							$(this).dialog("close");
						}
					}
				});
					
				$(".delUser").button().click( function() {
					$("tr#" + this.id + " td").addClass("ui-state-error");
					$("tr#" + this.id).addClass("toDelete");
					$("#dialog-usersAdmin").dialog("open");
					return false;
				});  		
			
				$.get('templates/addUserForm.html', function(data) {
  					$.tmpl(data ,saveData).appendTo( "#generalWrapper" );
  					$('.buttonAddUser').button();
  					
  					$("#addu").submit(function() {  
    	      		var login = $("input#Login").val();
   	      		var email = $("input#Email").val();
     	     			var pass = $("input#Password").val();
     	      		var confpass = $("input#ConfirmPassword").val();
     	     			var regexpEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
     	      		var regexpLogin = /^[a-z]([0-9a-z_])+$/i; 
						var regexpPass = /^([0-9a-zA-Z])+$/;

						if ( (login.length < 3) || (login.length > 20) ) {
     	      			$("#formadduser .ui-state-error").html('<p>Login must be between 3 and 20 chars </p>'); return false;
     	      		} else if (! regexpLogin.test(login)) {
     	      			$("#formadduser .ui-state-error").html("<p>Username may consist of letters, numbers and underscores, and begin with a letter</p>");  return false; 
     	      		} else if (! regexpEmail.test(email)) {
     	     			 	$("#formadduser .ui-state-error").html("<p>Please, enter a valid email adress</p>"); return false; 
     	      		} else if ( (pass.length < 3) || (pass.length > 20) ) {
     	     			 	$("#formadduser .ui-state-error").html('<p>Password must be between 3 and 20 chars </p>'); return false; 
     	     			} else if (! regexpPass.test(pass)) {
     	      			$("#formadduser .ui-state-error").html("<p>Password may consist of letters and numbers</p>"); return false; 
     	      		} else if ( pass != confpass ) {
     	      			$("#formadduser .ui-state-error").html("<p>Password and confirmation do not match</p>"); return false; 
     	      		} else {
  	         			$.ajax ({
     	      	 			url: "?ajax=true&action=adduser",
					 			type: 'post',
					 			data : "login=" + login + "&email=" + email + "&pass=" + pass,
					 			success : function (data) {
									manageUsers();
					 			},
				    			dataType: 'json'
     	      			});
     	      			
     	      			$(".ui-state-error").html('');
     	      			return false; 
   	      		}
 					});
				});
			}); 	
   	},
  		dataType: 'json'
	});
}

/**
 * Send an ajax request to delete the user. 
 * When the response is received, the page is refreshed.
 * Calls the action deluser
 * @function
 * @param {Number} id The id of the user we want to delete
 */
function deleteUser(id) {
	$.ajax({
 		url: "?ajax=true&action=deluser",
 		type: 'post',
 		data : "&id=" + id,
 		success :function (data) { 
 			manageUsers(); 
 		},    
  		dataType: 'json'
	});
}

/**
 * Opens a form in a dialog window to change the users's email address. 
 * Sends an ajax request to update the address if it is valid.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action changeemail
 * Uses the template "changeEmailForm.html"
 * @function
 * @param {Number} idUser The user id
 * @param {String} login The username
 */
function changeEmail (idUser, login) {
	$.get('templates/changeEmailForm.html', function(data) {
		$("#changeEmail").remove();
		var oldval = $("#emailUserAdmin" + idUser).text();
		$('#content').append( $.tmpl(data, {"login" : login, "oldval" : oldval}) );
		$("#" + idUser + " td").addClass("ui-state-error");
 		$("#changeEmail").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Change": function() {
      			var email = $("#email").val();
      			console.log($("#email"));
     				var regexpEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      			if (! regexpEmail.test(email)) {
      				console.log(email);
						$("#error").html("<p>Please, enter a valid email adress</p>"); return false;
					} else {     	
     					$.ajax ({
     				 		url: "?ajax=true&action=changeemail",
					 		type: 'post',
					 		data : "idUser=" + idUser + "&newEmail=" + email ,
					 		success : function (data) { 
					 			manageUsers(); 
						 	},    
	   	 				dataType: 'json'
    					});
    				}
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
   				return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
				}
			}
		}).dialog( "open" );
	});
}

/**
 * Opens a form in a dialog window to change the users's role. 
 * Sends an ajax request to update the role.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action changerole
 * Uses the template "changeRoleForm.html"
 * @function
 * @param {Number} idUser The user id
 * @param {String} login The username
 */
function changeRole (idUser, login) {
	$.get('templates/changeRoleForm.html', function(data) {
		$("#changeRole").remove();
		$('#content').append( $.tmpl(data, {"login" : login}) );
		$("#" + idUser + " td").addClass("ui-state-error");
 		$("#changeRole").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Change": function() {
    	 			var role = $("select#role").val();	    	      	
    	 			$.ajax ({
   	  				url: "?ajax=true&action=changerole",
						type: 'post',
						  
						data : "idUser=" + idUser + "&newRole=" + role ,
						success : function (data) { 
							manageUsers ();
						},    
		  				dataType: 'json'
 	   			});      
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
					return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
					$("td").removeClass("ui-state-error");
				}
			}
		}).dialog( "open" );
	});
}

/**
 * Asks the server to delete a relationship host <--> user, with ajax request.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action removeHostUser
 * @function
 * @param {Number} idUser The user id
 * @param {Number} idHost The host id
 */
function removeUserHost ( idHost, idUser ) {
	$.ajax({
 		url: "?ajax=true&action=removeHostUser",
 		type: 'post',
 		data : "&idHost=" + idHost + "&idUser=" + idUser,
 		error :function (data) { 
			manageHostsOfUser(idUser);
 		}, 
 		success :function (data) { 
			manageHostsOfUser(idUser);
 		},    
  		dataType: 'json'
	});
}

/**
 * Asks the server to create a relationship host <--> user, with ajax request.
 * When the response of ajax request is received, the page is refreshed.
 * Calls the action addhosttouser
 * @function
 * @param {Number} idUser The user id
 * @param {Number} idHost The host id
 */
function addUserHost ( idHost, idUser ) {
	$.ajax ({
		url: "?ajax=true&action=addhosttouser",
		type: 'post',
		data : "idUser=" + idUser + "&idHost=" + idHost ,
		success : function (data) {
			manageHostsOfUser(idUser);
		},    
		dataType: 'json'
	});
}

/**
 * Gets the list of hosts related to an user, and generates a page to manage their relationships
 * (delete or add relationships, add custom names for hosts...)
 * Calls the action managehostsofuser
 * Uses the template "userHostsRelationshipsAdminManage.html"
 * Contains 2 functions : addToGroup( $item ) and removeFromGroup( $item ) to add a relationship or delete it
 * @function
 * @param {Number} idUser The user id
 */
function manageHostsOfUser(idUser) {
	deletePeriodicGetStatus();
	$.ajax({
 		url: "?ajax=true&action=managehostsofuser",
 		type: 'post',
 		data : "&idUser=" + idUser,
 		success :function (data) { 				
 			var saveData = data;
			var i=0, j=0;
			saveData.others = new Array();
			saveData.associated = new Array();
			
			for (var ah in saveData.allhosts) {	
				// Add the host in the good list (associated with user or not)
				var asso = false;		
				for (var hou in saveData.hostsofuser) {
					if (saveData.hostsofuser[hou] === saveData.allhosts[ah].idH) {
						saveData.associated[i++]=saveData.allhosts[ah];
						asso = true;
					}
				}
				if (! asso) {
					saveData.others[j++]=saveData.allhosts[ah];
				}
				
				// Add the custom host name to the host if it exists
				for (var cn in saveData.customNames) {
					if (saveData.customNames[cn].idH === saveData.allhosts[ah].idH) {
						saveData.allhosts[ah].customName = saveData.customNames[cn].nameForUser;
					}
				}
			}
			
			$.get('templates/userHostsRelationshipsAdminManage.html', function(data) {
				$('#content').html( $.tmpl(data, saveData) );
				$("button").button();
				
				$("#buttonSearchHosts").button().click( function() {
					var toSearch = $("input#searchHosts").val();
					for (var oth in saveData.others) {
						$("#hosts li#"+saveData.others[oth].idH).show();
						if (saveData.others[oth].nameH.indexOf(toSearch) == -1) {
							$("#hosts li#"+saveData.others[oth].idH).hide();
						}
					}
				}); 
				
				$("#hosts li").dblclick( function () {
					addUserHost (this.id, saveData.id);
				});	

				$("#hostsinGroup li").dblclick( function () {
					removeUserHost (this.id, saveData.id);
				});
				
				// there's the hosts and the group
				var $hosts = $( "#hosts" ), $group = $( "#group" );

				// let the hosts items be draggable
				$( "li", $hosts ).draggable({
					cancel: "a.ui-icon", // clicking an icon won't initiate dragging
					revert: "invalid", // when not dropped, the item will revert back to its initial position
					containment: "document",
					helper: "clone",
					cursor: "move"
				});
				
				$( "li", $group ).draggable({
					cancel: "a.ui-icon", // clicking an icon won't initiate dragging
					revert: "invalid", // when not dropped, the item will revert back to its initial position
					containment: "document",
					helper: "clone",
					cursor: "move"
				});

				// let the group be droppable, accepting the hosts items
				$group.droppable({
					accept: "#hosts > li",
					activeClass: "ui-state-highlight",
					drop: function( event, ui ) {
						addToGroup( ui.draggable );
					}
				});

				// let the hosts be droppable as well, accepting items from the group
				$hosts.droppable({
					accept: "#group li",
					activeClass: "custom-state-active",
					drop: function( event, ui ) {
						removeFromGroup( ui.draggable );
					}
				});

				function addToGroup( $item ) {
					$item.fadeOut(function() {
						var $list = $( "ul", $group ).length ?
							$( "ul", $group ) :
							$( "<ul class='hosts ui-helper-reset'/>" ).appendTo( $group );
						$item.appendTo( $list ).fadeIn();
					});
					addUserHost ($item.context.id, saveData.id);
				}

				function removeFromGroup( $item ) {
					$item.fadeOut(function() {
						$item.css( "height", "96px" ).end().appendTo( $hosts ).fadeIn();
					});
					removeUserHost ($item.context.id, saveData.id) ;
				}
			});
			
 		},    
  		dataType: 'json'
	});		
}

/**
 * Creates and opens a dialog message with form to change or create the custom name given to the host for this user.
 * Calls the action changehostnameforuser
 * Uses the template "addHostNameForUserForm.html"
 * @function
 * @param {Number} idUser The user id
 * @param {String} idUser The username
 * @param {Number} idHost The host id
 * @param {String} idUser The hostname
 */
function nameHostForUser(idUser, login, idHost, hname) {
	$.get('templates/addHostNameForUserForm.html', function(data) {
		$("#addHostnameForUser").remove();
	  	$('#content').append( $.tmpl(data, {"hname" : hname, "login" : login}) );

 		$("#addHostnameForUser").dialog({
	   	autoOpen: false,
			resizable: false,
			height:280,
			width:300,
			modal: true,
			title: false,
			buttons: {
				"Set name": function() {
 	    			var newName = $("#newName").val();	    	
  	   			if ( (newName.length < 3) || (newName.length > 40) ) {
 	   		 	   $("#error").html("<p>Hostname must be between 3 and 40 chars</p>"); 
 	  		  		   return false; 
 				  	} else {
 				  		$("#error").html('');
 				  		if ( newName != hname) {
  		 			 	  $.ajax ({
 	    			 			url: "?ajax=true&action=changehostnameforuser",
					 			type: 'post',
					 			data : "idHost=" + idHost + "&idUser=" + idUser + "&newName=" + newName,
					 			success : function (data) { 
					 				manageHostsOfUser(idUser);
					 			},    
		  		  				dataType: 'json'
 	   					});
 	   				}   
 		  			} 
					$(this).dialog("close");
   				return false; 
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			}
		}).dialog( "open" );
	});	
}

/**
 * Asks the server with ajax request to import all users in LDAP database.
 * Calls the action importusersformldap
 * @function
 */
function importFromLdap() {
	$.ajax ({
		url: "?ajax=true&action=importusersformldap",
		type: 'post',
		success : function (data) {
			manageUsers();
		},    
		dataType: 'json'
	});
}
