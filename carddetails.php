<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	carddetails.php
//			Use Braintree API and fetch card details
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------

?>
<!doctype html>
<html>
<head>
  <title>Book Sudbury Chamber Meeting</title>
  <meta charset="utf-8">
  <script src="https://js.braintreegateway.com/web/dropin/1.11.0/js/dropin.min.js"></script>
</head>
<body>
<?php
			// Provide a container for the Braintree form
			// Fetch and format the price
?>
	<div id="dropin-container"></div>
	<button id="submit-button">Pay now</button>
	<form id="checkout" method="post" action="bookingpaid.php">
	<input type='hidden' value='testnonce' name='nonce' id='nonceFld'>
<?php
	$price = $_GET['price'];
	echo "<input type='text' value='" . $price . "' name='amount' id='amount'>";
?>
	<br>
	</form>
	
	<div>
		<p><b>Notice</b></p>
		<p>Card information will be transmitted over a secure link to 
		Braintree, a service of PayPal.</p>
		
	</div>
  
<script>
// ---------------------------------------------------
//	Detect whether running on development PC or server
//	Set the tokenisation key (control panel > settings)
//
//	This is wrong - needs to be sandbox / production
// ---------------------------------------------------

	var domain = document.domain;
	var token;
	if (domain == "localhost")
		token = "sandbox_j7bz3bmj_w43wzttxmvmyrz8z";
	else
		token = "production_hcsjhmjj_rcy8fxhfbjfngsw4";

// ---------------------------------------------------
//	Script taken from Braintree
//
// ---------------------------------------------------
    var button = document.querySelector('#submit-button');
    var form = document.querySelector('#checkout');
    var nonce = document.querySelector('#nonceFld');
//    var clientToken = "eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiJjMzliNjY0MjBhZTI3MDdiMTE2NTA4NGE1MTkxMDcwYjRhMWUxMGYzYTg0NTk5OThkYjFhOGRkMGEyZGY5ZTU0fGNyZWF0ZWRfYXQ9MjAxOC0wNy0xNlQxNzoxMjoyNC43OTE4MjU5MTArMDAwMFx1MDAyNm1lcmNoYW50X2lkPXJjeThmeGhmYmpmbmdzdzRcdTAwMjZwdWJsaWNfa2V5PXpkcHZxajZ3N2hrenM4d2YiLCJjb25maWdVcmwiOiJodHRwczovL2FwaS5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL3JjeThmeGhmYmpmbmdzdzQvY2xpZW50X2FwaS92MS9jb25maWd1cmF0aW9uIiwiY2hhbGxlbmdlcyI6W10sImVudmlyb25tZW50IjoicHJvZHVjdGlvbiIsImNsaWVudEFwaVVybCI6Imh0dHBzOi8vYXBpLmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvcmN5OGZ4aGZiamZuZ3N3NC9jbGllbnRfYXBpIiwiYXNzZXRzVXJsIjoiaHR0cHM6Ly9hc3NldHMuYnJhaW50cmVlZ2F0ZXdheS5jb20iLCJhdXRoVXJsIjoiaHR0cHM6Ly9hdXRoLnZlbm1vLmNvbSIsImFuYWx5dGljcyI6eyJ1cmwiOiJodHRwczovL29yaWdpbi1hbmFseXRpY3MtcHJvZC5wcm9kdWN0aW9uLmJyYWludHJlZS1hcGkuY29tL3JjeThmeGhmYmpmbmdzdzQifSwidGhyZWVEU2VjdXJlRW5hYmxlZCI6ZmFsc2UsInBheXBhbEVuYWJsZWQiOmZhbHNlLCJtZXJjaGFudElkIjoicmN5OGZ4aGZiamZuZ3N3NCIsInZlbm1vIjoib2ZmIn0";

    var result = braintree.dropin.create(
    {
//      authorization: 'eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiI3MzQxYjcxNTI4NmUwYTYyMWE1NDQzZGJhZjExMTBmMTM4OTkyZWU4ZjQ2Yjc2MzgxMTI1NTkxNTZlMGFjMzU2fGNyZWF0ZWRfYXQ9MjAxOC0wNy0xMVQxMDo1Njo0My45MzExNTM0MjErMDAwMFx1MDAyNm1lcmNoYW50X2lkPXc0M3d6dHR4bXZteXJ6OHpcdTAwMjZwdWJsaWNfa2V5PW1uemc1NnN6N2IzNmY3cjQiLCJjb25maWdVcmwiOiJodHRwczovL2FwaS5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvdzQzd3p0dHhtdm15cno4ei9jbGllbnRfYXBpL3YxL2NvbmZpZ3VyYXRpb24iLCJjaGFsbGVuZ2VzIjpbXSwiZW52aXJvbm1lbnQiOiJzYW5kYm94IiwiY2xpZW50QXBpVXJsIjoiaHR0cHM6Ly9hcGkuc2FuZGJveC5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL3c0M3d6dHR4bXZteXJ6OHovY2xpZW50X2FwaSIsImFzc2V0c1VybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXV0aFVybCI6Imh0dHBzOi8vYXV0aC52ZW5tby5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIiwiYW5hbHl0aWNzIjp7InVybCI6Imh0dHBzOi8vb3JpZ2luLWFuYWx5dGljcy1zYW5kLnNhbmRib3guYnJhaW50cmVlLWFwaS5jb20vdzQzd3p0dHhtdm15cno4eiJ9LCJ0aHJlZURTZWN1cmVFbmFibGVkIjp0cnVlLCJwYXlwYWxFbmFibGVkIjp0cnVlLCJwYXlwYWwiOnsiZGlzcGxheU5hbWUiOiJTdWRidXJ5IENoYW1iZXIgb2YgQ29tbWVyY2UiLCJjbGllbnRJZCI6bnVsbCwicHJpdmFjeVVybCI6Imh0dHA6Ly9leGFtcGxlLmNvbS9wcCIsInVzZXJBZ3JlZW1lbnRVcmwiOiJodHRwOi8vZXhhbXBsZS5jb20vdG9zIiwiYmFzZVVybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXNzZXRzVXJsIjoiaHR0cHM6Ly9jaGVja291dC5wYXlwYWwuY29tIiwiZGlyZWN0QmFzZVVybCI6bnVsbCwiYWxsb3dIdHRwIjp0cnVlLCJlbnZpcm9ubWVudE5vTmV0d29yayI6dHJ1ZSwiZW52aXJvbm1lbnQiOiJvZmZsaW5lIiwidW52ZXR0ZWRNZXJjaGFudCI6ZmFsc2UsImJyYWludHJlZUNsaWVudElkIjoibWFzdGVyY2xpZW50MyIsImJpbGxpbmdBZ3JlZW1lbnRzRW5hYmxlZCI6dHJ1ZSwibWVyY2hhbnRBY2NvdW50SWQiOiJzdWRidXJ5Y2hhbWJlcm9mY29tbWVyY2UiLCJjdXJyZW5jeUlzb0NvZGUiOiJFVVIifSwibWVyY2hhbnRJZCI6Inc0M3d6dHR4bXZteXJ6OHoiLCJ2ZW5tbyI6Im9mZiJ9',
//		authorization: 'eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiJjMzliNjY0MjBhZTI3MDdiMTE2NTA4NGE1MTkxMDcwYjRhMWUxMGYzYTg0NTk5OThkYjFhOGRkMGEyZGY5ZTU0fGNyZWF0ZWRfYXQ9MjAxOC0wNy0xNlQxNzoxMjoyNC43OTE4MjU5MTArMDAwMFx1MDAyNm1lcmNoYW50X2lkPXJjeThmeGhmYmpmbmdzdzRcdTAwMjZwdWJsaWNfa2V5PXpkcHZxajZ3N2hrenM4d2YiLCJjb25maWdVcmwiOiJodHRwczovL2FwaS5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL3JjeThmeGhmYmpmbmdzdzQvY2xpZW50X2FwaS92MS9jb25maWd1cmF0aW9uIiwiY2hhbGxlbmdlcyI6W10sImVudmlyb25tZW50IjoicHJvZHVjdGlvbiIsImNsaWVudEFwaVVybCI6Imh0dHBzOi8vYXBpLmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvcmN5OGZ4aGZiamZuZ3N3NC9jbGllbnRfYXBpIiwiYXNzZXRzVXJsIjoiaHR0cHM6Ly9hc3NldHMuYnJhaW50cmVlZ2F0ZXdheS5jb20iLCJhdXRoVXJsIjoiaHR0cHM6Ly9hdXRoLnZlbm1vLmNvbSIsImFuYWx5dGljcyI6eyJ1cmwiOiJodHRwczovL29yaWdpbi1hbmFseXRpY3MtcHJvZC5wcm9kdWN0aW9uLmJyYWludHJlZS1hcGkuY29tL3JjeThmeGhmYmpmbmdzdzQifSwidGhyZWVEU2VjdXJlRW5hYmxlZCI6ZmFsc2UsInBheXBhbEVuYWJsZWQiOmZhbHNlLCJtZXJjaGFudElkIjoicmN5OGZ4aGZiamZuZ3N3NCIsInZlbm1vIjoib2ZmIn0',
		authorization: token,
      container: '#dropin-container'
    }, function (createErr, instance) 
     {
     	button.addEventListener('click', 
      	  function () 
      	{
        	instance.requestPaymentMethod(function (err, payload) 
        	{
          // Submit payload.nonce to your server
          nonce.value = payload.nonce;
          form.submit();
        	});
        });
     }
     );
</script>
</body>
</html>
