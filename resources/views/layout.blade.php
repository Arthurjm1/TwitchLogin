<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://bootswatch.com/4/pulse/bootstrap.min.css">
  <title>Twitch Login</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Twitch Login</a>

    @if(session('user_logged'))

    <a class="btn btn-lg btn-primary ml-auto" href="logout" style="background-color: #6441a5; color: white">Logout</a>

    @endif
  </nav>

  @yield('content')
</body>

</html>