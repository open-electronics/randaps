<!DOCTYPE html>
<html>

	<head>
		<title>Control panel</title>
		<!--Import materialize.css-->
		<link type="text/css" rel="stylesheet" href="common/materialize/css/materialize.min.css"  media="screen,projection"/>
		<!--Let browser know website is optimized for mobile-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<!--Import jQuery before materialize.js-->
		<script type="text/javascript" src="common/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="common/materialize/js/materialize.min.js"></script>
		
		<script type = "text/javascript">
		
			$(function() {

				$("#Login").click(function() {
					if($.trim($("#User").val()) != "" && $.trim($("#Password").val()) != "") {
						$.post("RandA-PhotoSharing.php", {Command : "login", User : $("#User").val(), Password : $("#Password").val()}, function(response) {
							if(response == 0) {
								location.replace("admin.php");
							} else {
								displayError("Wrong User or Password.");
							}
						});
					} else {
						displayError("All fields are required.");
					}
				});
				
				$("#User").keypress(function( event ) {
					if(event.which == 13 ) {
						$("#Login").click();
					}
				});
				
				$("#Password").keypress(function( event ) {
					if(event.which == 13 ) {
						$("#Login").click();
					}
				});
				
				function displayError(message) {
					$("#Error").html(message);
					$("#ErrorBox").fadeIn();
				}

			});	
			
		</script>
		
	</head>

	<body class = "grey lighten-5">

	
		<div class = "row">
	
			<div class="red-text col s12 center-align">
				<h2>Control Panel</h2>
			</div>
			
		</div>
		
		<div class = "row">

			<div class="col s5">&nbsp;</div>
			<div class="input-field col s2 center-align">
				<i class="mdi-action-account-circle prefix"></i>
				<input id="User" type="text" class="validate">
				<label for="User">User</label>
			</div>
			<div class="col s5">&nbsp;</div>

		</div>
		
		<div class = "row">
			
			<div class="col s5">&nbsp;</div>
			<div class="input-field col s2 center-align">
				<i class="mdi-action-lock prefix"></i>
				<input id="Password" type="password" class="validate">
				<label for="Password">Password</label>
			</div>
			<div class="col s5">&nbsp;</div>
			
		</div>

		<div class = "row">

			<div class="col s5">&nbsp;</div>
			<div class="input-field col s2 center-align">
				<a class="waves-effect waves-light btn" id = "Login">Login<i class="mdi-content-send right"></i></a>
			</div>
			<div class="col s5">&nbsp;</div>

		</div>
		
		<div class = "row">

			<div class="col s4">&nbsp;</div>
			<div class="col s4 center-align">
				<div class="card-panel red" style = "display: none;" id = "ErrorBox">
					<span class="white-text" id = "Error"></span>
				</div>
			</div>
			<div class="col s4">&nbsp;</div>

		</div>

	</body>

</html>