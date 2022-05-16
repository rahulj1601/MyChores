<!-- Generates the header content to be included in the index.php file -->
<!-- This includes the javascript links and CSS links which are needed for the website -->

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
    <title>Chore Organiser</title>
    <link rel="icon" type="image/png" href="images/favicon.png"/>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="css/main.css" type="text/css" charset="utf-8">
    <link rel="stylesheet" href="css/header.css" type="text/css" charset="utf-8">
    <link rel="stylesheet" href="css/footer.css" type="text/css" charset="utf-8">
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <script src="javascript/script.js"></script>
  </head>
  <body onload="activateLoader()">

  <div id="loader"><img src="images/favicon.png" alt="loader"></div>

  <div id="page" style="display:none;">

  <nav class="navbarContainer">
      <a class="navbarLogo" href="index.php"><img src="images/mychoreslogothin.png" alt="myChores Logo"></a>
      <div id="myNavbar"><a href="processlogout.php">Logout</a></div>
  </nav>