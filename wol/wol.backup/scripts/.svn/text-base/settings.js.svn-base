/**
 * Gets the ldap config from the server, to show it to the user and allow him to change it.
 * Calls the actions getcurrentldapconfig, getdefaultldapconfig and setldapconfig
 * Uses the template "ldapConfig.html"
 * @function
 * @param {bool} current true to get the current config, false to get the default config.
 */
function ldapConfig( current ) {
	deletePeriodicGetStatus();
	$.ajax({
  		url: "?ajax=true&action=" + ( current ? 'getcurrentldapconfig' : 'getdefaultldapconfig' ),
 		type: 'post',
 		success: function(data){ 					
  			var saveData = data;		
			$.get('templates/ldapConfig.html', function(data) {
				$('#content').html( $.tmpl(data, saveData) );
				$( "#useOrNot" ).buttonset();
				
				var saveTemplate = data;
				
				$ldapConfig = $("#ldapConfig");
				if (saveData['useldap']=='no') {
					$ldapConfig.hide();
				}
				$("#dontuse").click( function (){
					$ldapConfig.hide();					
				});
				$("#use").click( function (){
					$ldapConfig.show();						
				});
				
				$("#buttonSaveLdapConfig").button().click( function () {
					var data2send = "";
					
					use = document.getElementById("use");
					dontuse = document.getElementById("dontuse");
					
					if (dontuse.checked) {
						data2send += "&useldap=no";
						data2send += "&port="+saveData['port'] + "&host="+saveData['host'] + "&basedn="+saveData['basedn'] + "&binddn="+saveData['binddn'] + 
							"&bindpw="+saveData['bindpw'] + "&usersdn="+saveData['usersdn'] + "&userloginattr="+saveData['userloginattr'] + 
							"&userpwdattr="+saveData['userpwdattr'] + "&usermailattr="+saveData['usermailattr'] + "&useridattr="+saveData['useridattr'];
							
					} else if (use.checked) {
						data2send += "&useldap=yes";
						
						var host = $("input#host").val();
						var port = $("input#port").val();
						var basedn = $("input#basedn").val();
						var binddn = $("input#binddn").val();
						var bindpw = $("input#bindpw").val();
						var usersdn = $("input#usersdn").val();
						var userloginattr = $("input#userloginattr").val();
						var userpwdattr = $("input#userpwdattr").val();
						var usermailattr = $("input#usermailattr").val();
						var useridattr = $("input#useridattr").val();

						if ( (port >= 65536) || (port < 1) || (''+parseInt(port) == 'NaN') ) {
							$("#error").html("<p>Please, enter a valid port number (between 1 and 65536, in general, 389 for LDAP) </p>");
							return false; 
						} else if (host == "") {
							$("#error").html("<p>Please, enter a valid host name, or IP </p>"); 
							return false; 
						} else if (basedn == "") {
							$("#error").html("<p>Please, enter a valid Base DN. Ex : dc=example,dc=com </p>"); 
							return false; 
						} else if (binddn == "") {
							$("#error").html("<p>Please, enter a valid Bind DN (admin login). Ex : cn=admin,dc=example,dc=com </p>"); 
							return false; 
						} else if (bindpw == "") {
							$("#error").html("<p>Please, enter a valid Bind password (admin password) </p>"); 
							return false; 
						} else if (usersdn == "") {
							$("#error").html("<p>Please, enter a valid Users DN, (where users are in your LDAP base). Ex : ou=Users,dc=example,dc=com </p>");
							return false;  
						} else if (userloginattr == "") {
							$("#error").html("<p>Please, enter the user's login attribute name (in general, cn) </p>");
							return false;  
						} else if (userpwdattr == "") {
							$("#error").html("<p>Please, enter the user's password attribute name (in general, userpassword) </p>");
							return false; 
						} else if (usermailattr == "") {
							$("#error").html("<p>Please, enter the user's email attribute name (in general, mail) </p>");
							return false;
						} else if (useridattr == "") {
							$("#error").html("<p>Please, enter the user's ID attribute name (in general, uid) </p>");
							return false;  
						} else {
							data2send += "&port="+port + "&host="+host + "&basedn="+basedn + "&binddn="+binddn + 
							"&bindpw="+bindpw + "&usersdn="+usersdn + "&userloginattr="+userloginattr + 
							"&userpwdattr="+userpwdattr + "&usermailattr="+usermailattr + "&useridattr="+useridattr;
						}
					} else {
						alert('error');
						return false;
					}					
					$.ajax ({
     	 				url: "?ajax=true&action=setldapconfig",
		 				type: 'post',
		 				data : data2send,
		 				success : function (data) { 
		 					alert("config saved !");
		 					ldapConfig(true);
		 				},    
		  				dataType: 'json'
 					});  
				});
				
				$("#buttonRestoreDefault").button().click( function () {
					ldapConfig(false);
				});

				$("#buttonSeeCurrentConfig").button().click( function () {
					ldapConfig(true);
				});

			});
		},
		dataType: 'json'
	});

	return false;
}

/**
 * Gets the stats config from the server, to show it to the user and allow him to change it.
 * Calls the actions getStatsSettings and setstatconfig
 * Uses the template "statsSettings.html"
 * @function
 */
function statSettings() {
	deletePeriodicGetStatus();
	$.ajax({
  		url: "?ajax=true&action=getStatsSettings",
 		type: 'post',
 		success: function(data){ 					
  			var saveData = data;		
			$.get('templates/statsSettings.html', function(data) {
				$('#content').html( $.tmpl(data, saveData) );

				$("#buttonSaveStatConfig").button().click( function () {				
					var nbWeeks = $("input#nbWeeks").val();
					var refresh = $("input#refresh").val();
					
					if ( (parseInt(nbWeeks) < 0)||(''+parseInt(nbWeeks) == 'NaN') ){
						$("#error").html("<p>Please, enter a positive number of weeks (or 0 if you want to keep all weeks)"); 
						return false; 
					} else if ( (parseInt(refresh) < 1)||(parseInt(refresh) > 20)||(''+parseInt(refresh) == 'NaN') ) {
						$("#error").html("<p>Please, enter a number between 1 and 20 for time between 2 updates </p>"); 
						return false; 
					} else {
						$.ajax ({
     	 					url: "?ajax=true&action=setstatconfig",
		 					type: 'post',
		 					data : "&nbWeeks="+nbWeeks + "&refresh="+refresh,
		 					success : function (data) { 
		 						alert("config saved !");
		 						statSettings();
		 					},    
		  					dataType: 'json'
 						});  
					}
							
				});
				
				$("#buttonCancelStatConfig").button().click( function () {
					statSettings();
				});
				
			});
		},
		dataType: 'json'
	});	
	
	return false;	
}

