<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" href="{{ asset('imagens/LogoKixiUtilitario3.png') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
  </head>
  <body>
    @inertia
    <script src="https://kit.fontawesome.com/792e0fbc8a.js" crossorigin="anonymous"></script>
  </body>
</html>
