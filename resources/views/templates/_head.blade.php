<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Use asset() to link the favicon -->
    <link rel="shortcut icon" href="{{ asset('resources/images/favicon.svg') }}" type="image/x-icon" />
    <title>Family Finance App</title>

    <!-- ========== All CSS files linkup using Vite or Laravel asset pipeline ========== -->
    @vite([
        'resources/css/bootstrap.min.css',
        'resources/css/lineicons.css',
        'resources/css/materialdesignicons.min.css',
        'resources/css/fullcalendar.css',
        'resources/css/main.css'
    ])
  </head>
