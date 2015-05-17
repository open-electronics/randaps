<?php

	@session_start();
	if(!isset($_SESSION['User']) || trim($_SESSION['User']) == "") {
		header("Location: index.php");
	}

?>

<!DOCTYPE html>
<html>

	<head>
		<title>Control Panel</title>
		<!--Import materialize.css-->
		<link type="text/css" rel="stylesheet" href="common/materialize/css/materialize.min.css"  media="screen,projection"/>
		<!--Let browser know website is optimized for mobile-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<!--Import jQuery before materialize.js-->
		<script type="text/javascript" src="common/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="common/materialize/js/materialize.min.js"></script>
		<!--  Tiny MCE  -->
		<script type="text/javascript" src="common/tinymce/tinymce.min.js"></script>

		<script type = "text/javascript">
		
			$(function() {

				tinymce.init({
					mode : "specific_textareas",
					editor_selector : "mceEditor",
					menubar : false,
					language: "it",
					plugins: ["textcolor", "table", "fullscreen", "image", "link"],
					toolbar: [
						"fontselect fontsizeselect | bold italic | forecolor backcolor | alignleft aligncenter alignright | table | link image | undo redo | fullscreen"
					],
					height: 1
				 });
			
				$("#Save").click(function() {

					saveSettings();

				});
				
				$("#Logout").click(function() {

					$.post("RandA-PhotoSharing.php", {Command : "logout"}, function(response) {
						location.replace("index.php");
					});

				});
			
				$("#ExportData").click(function() {
				
					window.open("ExportData.php");

				});
			
				$("#DeleteAll").click(function() {
				
					if(confirm("Are you sure to delete all photos and relative data?") == true) {
						$.post("RandA-PhotoSharing.php", {Command : "deleteAll"}, function(response) {
							updatePhotos();
						});
					}

				});
				
				$("#AddTheme").click(function() {
				
					if($.trim($("#value").val()) != "" && $.trim($("#description").val()) != "") {
				
						$.post("RandA-PhotoSharing.php", {Command : "addTheme", Value : $("#value").val(), Description : $("#description").val()}, function(response) {
							$('#theme').append($('<option>', {
								value: $("#value").val(),
								text: $("#description").val()
							}));
							alert("New theme added!\n\nRemember to place: \n- " + $("#value").val() + "_screen.gif\n- " + $("#value").val() + "_end.gif\n- " + $("#value").val() + "_overlay.png\n in the 'randaps/data/' folder!");
							$("#value").val("");
							$("#description").val("");
						});
						
					} else {
					
						alert("You have to compile Value and Description.");
					
					}

				});
				
				$("#DeleteTheme").click(function() {
				
					$.post("RandA-PhotoSharing.php", {Command : "deleteTheme", Value : $("#theme").val()}, function(response) {
						refreshThemes();
						alert("The selected theme was removed.");
					});

				});
				
				function refreshThemes() {
				
					$.post("RandA-PhotoSharing.php", {Command : "loadThemes"}, function(response) {
						$('#theme').empty();
						for (var i=0; i<response.length; i++) {
							$('#theme').append($('<option>', {
								value: response[i].Value,
								text: response[i].Description
							}));
						}
					}, "json");
				
				}
				
				function saveSettings() {
				
					var settings = {};
					settings['theme'] = $("#theme").val();
					settings['standby'] = $("#standby").val();
					settings['logo_x'] = $("#logo_x").val();
					settings['logo_y'] = $("#logo_y").val();
					settings['overlay_x'] = $("#overlay_x").val();
					settings['overlay_y'] = $("#overlay_y").val();
					settings['result_x'] = $("#result_x").val();
					settings['result_y'] = $("#result_y").val();
					settings['photo_save'] = $("#photo_save").prop('checked') ? 1 : 0;
					settings['photo_cloud'] = $("#photo_cloud").prop('checked') ? 1 : 0;
					settings['photo_social'] = $("#photo_social").prop('checked') ? 1 : 0;
					settings['photo_email'] = $("#photo_email").prop('checked') ? 1 : 0;
					settings['sender_email'] = $("#sender_email").val();
					settings['email_customobject'] = $("#email_customobject").val();
					settings['email_object'] = $("#email_object").val();
					settings['email_body'] = tinyMCE.get("email_body").getContent();
					settings['text_start'] = $("#text_start").val();
					settings['text_preview'] = $("#text_preview").val();
					settings['text_measurement'] = $("#text_measurement").val();
					settings['text_photo'] = $("#text_photo").val();
					settings['text_social'] = $("#text_social").val();
					settings['text_email'] = $("#text_email").val();
					settings['text_yes'] = $("#text_yes").val();
					settings['text_no'] = $("#text_no").val();
					settings['text_end'] = $("#text_end").val();

					$.post("RandA-PhotoSharing.php", {Command : "saveSettings", Settings : settings}, function(response) {
						alert("Saved.");
					});
				
				}

				function loadSettings() {

					$.post("RandA-PhotoSharing.php", {Command : "loadSettings"}, function(response) {
						$("#theme").val(response['theme']);
						$("#standby").val(response['standby']);
						$("#logo_x").val(response['logo_x']);
						$("#logo_y").val(response['logo_y']);
						$("#overlay_x").val(response['overlay_x']);
						$("#overlay_y").val(response['overlay_y']);
						$("#result_x").val(response['result_x']);
						$("#result_y").val(response['result_y']);
						$("#photo_save").prop('checked', (response['photo_save'] == 1 ? true : false));
						$("#photo_cloud").prop('checked', (response['photo_cloud'] == 1 ? true : false));
						$("#photo_social").prop('checked', (response['photo_social'] == 1 ? true : false));
						$("#photo_email").prop('checked', (response['photo_email'] == 1 ? true : false));
						$("#sender_email").val(response['sender_email']);
						$("#email_object").val(response['email_object']);
						$("#email_body").val(response['email_body']);
						$("#email_customobject").val(response['email_customobject']);
						$("#text_start").val(response['text_start']);
						$("#text_preview").val(response['text_preview']);
						$("#text_measurement").val(response['text_measurement']);
						$("#text_photo").val(response['text_photo']);
						$("#text_social").val(response['text_social']);
						$("#text_email").val(response['text_email']);
						$("#text_yes").val(response['text_yes']);
						$("#text_no").val(response['text_no']);
						$("#text_end").val(response['text_end']);
					}, "json");
				
				}
				
				function updatePhotos() {

					$.post("RandA-PhotoSharing.php", {Command : "updatePhotos"}, function(response) {
						$("#PhotoBox").html(response);
					});
				
				}

				refreshThemes();
				loadSettings();
				updatePhotos();
				setInterval(function () {updatePhotos()}, 5000);

			});

		</script>

	</head>

	<body class = "grey lighten-5">

		<div class="container">
		
			<div class = "row">
		
				<div class="red-text col s12 center-align">
					<h3>Control Panel</h3>
				</div>
				
			</div>
		
			<div class="row">
			
				<div class="col s12">
				
					<ul class="tabs">
						<li class="tab col s6"><a href="#tab1" class="active">Photos</a></li>
						<li class="tab col s6"><a href="#tab2">Settings</a></li>
					</ul>

				</div>
				
				<div id="tab1" class="col s12">
				
					<br>
				
					<!--	Photos section	-->
					
					<div class="row">
						
						<div id = "PhotoBox" style = "height: 600px; overflow-y: scroll; max-height: 600px; border:1px solid white;">

						</div>
						
					</div>
					
					<!------------------->

					<div class="row">
						
						<div class="input-field col s6 center-align">
							<a class="waves-effect waves-light btn" id = "ExportData">Export data<i class="mdi-file-file-download right"></i></a>
						</div>
						
						<div class="input-field col s6 center-align">
							<a class="waves-effect waves-light btn" id = "DeleteAll">Delete all<i class="mdi-action-delete right"></i></a>
						</div>
						
					</div>
				
				</div>
				
				<div id="tab2" class="col s12">
				
					<br>
					
					<!--	Settings section	-->
				
					<div class="row">

						<div class="col s2">
							<b>Theme</b>
						</div>
						<div class="col s3">
							<select class="browser-default" id = "theme">
							</select>
						</div>
						<div class="col s1">
							<a class="waves-effect waves-light btn-floating red" id = "DeleteTheme"><i class="mdi-action-delete"></i></a>
						</div>
						
					</div>
					
					<!------------------->
					
					<div class="row">
					
						<div class="col s2">
							<b>Add new theme</b>
						</div>
						<div class="col s2">
							Value
						</div>
						<div class="col s2">
							<input placeholder = "Value" id="value" type="text">
						</div>
						<div class="col s2">
							Description
						</div>
						<div class="col s3">
							<input placeholder = "Description" id="description" type="text">
						</div>
						<div class="col s1">
							<a class="waves-effect waves-light btn-floating" id = "AddTheme"><i class="mdi-content-add"></i></a>
						</div>
					
					</div>
					
					<!------------------->

					<div class="row">

						<div class="col s2">
							<b>Standby</b>
						</div>
						<div class="col s3">
							<select class="browser-default" id = "standby">
								<option value="0">Start screen</option>
								<option value="1">Slideshow</option>
							</select>
						</div>
						
					</div>
					
					<!------------------->

					<div class="row">

						<div class="col s2">
							<b>Logo</b>
						</div>
						<div class="col s1">
							X
						</div>
						<div class="col s1">
							<input placeholder = "X" id="logo_x" type="text">
						</div>
						<div class="col s1">
							Y
						</div>
						<div class="col s1">
							<input placeholder = "Y" id="logo_y" type="text">
						</div>

					</div>
						
					<!------------------->
						
					<div class="row">
						
						<div class="col s2">
							<b>Overlay</b>
						</div>
						<div class="col s1">
							X
						</div>
						<div class="col s1">
							<input placeholder = "X" id="overlay_x" type="text">
						</div>
						<div class="col s1">
							Y
						</div>
						<div class="col s1">
							<input placeholder = "Y" id="overlay_y" type="text">
						</div>
						
					</div>
					
					<!------------------->
						
					<div class="row">

						<div class="col s2">
							<b>Result</b>
						</div>
						<div class="col s1">
							X
						</div>
						<div class="col s1">
							<input placeholder = "X" id="result_x" type="text">
						</div>
						<div class="col s1">
							Y
						</div>
						<div class="col s1">
							<input placeholder = "Y" id="result_y" type="text">
						</div>
						
					</div>
					
					<!------------------->
					
					<div class="row">

						<div class="col s2">
							<b>Photos</b>
						</div>
						<div class="col s9">
							<div class="switch">
								<label>
								Off
								<input type="checkbox" id = "photo_save">
								<span class="lever"></span>
								On
								</label>
								&nbsp;&nbsp;&nbsp;Save to SD card
							</div>
							<div class="switch">
								<label>
								Off
								<input type="checkbox" id = "photo_cloud">
								<span class="lever"></span>
								On
								</label>
								&nbsp;&nbsp;&nbsp;Save to cloud
							</div>
							<div class="switch">
								<label>
								Off
								<input type="checkbox" id = "photo_social">
								<span class="lever"></span>
								On
								</label>
								&nbsp;&nbsp;&nbsp;Send to social
							</div>
							<div class="switch">
								<label>
								Off
								<input type="checkbox" id = "photo_email">
								<span class="lever"></span>
								On
								</label>
								&nbsp;&nbsp;&nbsp;Send via eMail
							</div>
						</div>
						
					</div>
						
					<!------------------->
						
					<div class="row">

						<div class="col s1">
							<b>Sender eMail</b>
						</div>
						<div class="col s3">
							<input placeholder = "Sender eMail" id="sender_email" type="text">
						</div>
						
						<div class="col s1">
							<b>eMail object</b>
						</div>
						<div class="col s3">
							<input placeholder = "eMail object" id="email_customobject" type="text">
						</div>
						
						<div class="col s1">
							<b>IFTT object</b>
						</div>
						<div class="col s3">
							<input placeholder = "IFTT object" id="email_object" type="text">
						</div>
						
					</div>
					
					<!------------------->
					
					<div class="row">
						
						<div class="col s3">
							<b>eMail body</b>
						</div>
						<div class="col s9">
							<textarea id = "email_body" class = "mceEditor"></textarea>
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_start</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_start" id="text_start" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_preview</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_preview" id="text_preview" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_measurement</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_measurement" id="text_measurement" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_photo</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_photo" id="text_photo" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_social</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_social" id="text_social" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_email</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_email" id="text_email" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_yes</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_yes" id="text_yes" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_no</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_no" id="text_no" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="col s3">
							<b>text_end</b>
						</div>
						<div class="col s9">
							<input placeholder = "text_end" id="text_end" type="text">
						</div>

					</div>
					
					<!------------------->
						
					<div class="row">
						
						<div class="input-field col s12 center-align">
							<a class="waves-effect waves-light btn" id = "Save">Save<i class="mdi-content-send right"></i></a>
						</div>
					
					</div>
				
				</div>
				
			</div>
			
			<div class = "row">

				<div class="input-field col s12 center-align">
					<a class="waves-effect waves-light btn red" id = "Logout">Logout<i class="mdi-content-send right"></i></a>
				</div>

			</div>
		
		</div>
	
	</body>

</html>