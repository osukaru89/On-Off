function manageMailing() {
	deletePeriodicGetStatus();
	$.ajax({
		url: "?ajax=true&action=getActionsAndLangs",
		type: 'post',
  		success: function(data) { 
			var saveData = data;
			$.get('templates/viewMailContent.html', function(data) {
				$('#content').html( $.tmpl(data, saveData) );
				$("#dataForms").hide();

				$("#viewEmailContent").button().click( function () {
					var action = $("select#action").val();
					var lang = $("select#lang").val();
					$("#active").buttonset();
				
					$.ajax({
  						url: "?ajax=true&action=getMailText",
 						type: 'post',
  						data: "&action="+action + "&lang="+lang,
 						success: function(data){
 							console.log(data);
 							$("#sender").replaceWith('<input type="text" id="sender" name="sender" value="'+ data['sender'] +'"/>');
 							$("#subject").replaceWith('<input type="text" id="subject" name="subject" value="'+ data['subject'] +'"/>'); 
  							$("textarea#textMail").val(data['text']);												
 							$("#listofTokens").text(data['tokens']);
							$('input#activeMail').attr('checked', data['isActive']);
							$('input#dontactiveMail').attr('checked', !(data['isActive']) );
							$("#dataForms").show();
							
							$("#active").buttonset();			
								
							$("#saveEmailContent").button().click( function () {
								var sender = $("input#sender").val();
								var subject = $("input#subject").val();
								var textmail = $("textarea#textMail").val();
								active = document.getElementById("activeMail");
									$.ajax({
  									url: "?ajax=true&action=setMailText",
 									type: 'post',
  									data: '&action='+encodeURIComponent(action) + '&lang='+encodeURIComponent(lang) + '&sender='+encodeURIComponent(sender) + '&subject='+encodeURIComponent(subject) + '&text='+encodeURIComponent(textmail) + '&isActive=' + ((active.checked)?'true':'false'),  
 									success: function(data){
 										alert('mail saved !');
 										manageMailing();
 									},
 									dataType: 'json'
 									});
 							});
 						},
 						dataType: 'json'
 					});
 				});
			});
		},
		dataType: 'json'
	});	
}