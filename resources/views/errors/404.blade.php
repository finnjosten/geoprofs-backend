<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>404 | Not Found</title>

        <style>
            * { margin: 0; padding: 0; font-family: Arial, sans-serif; }

            body{ background-color: #1a202c; display: flex; justify-content: center; align-items: center; flex-direction: column; gap: 12px; widows: 100%; height: 100vh; }

            .text { display: flex; }
            .text .error-code { padding-right: 20px; border-right: 1px solid #a0aec0; }
            .text .error-text { padding-left: 20px; border-left: 1px solid #a0aec0; }

            h1, p { color: #a0aec0; }
        </style>
    </head>

    <body>
        <div class="text">
            <h1 class="error-code">404</h1>
            <h1 class="error-text">Not Found</h1>
        </div>
        <div class="text">
            @if (request()->path() == "api")
                <p class="error-message"><u>/api/</u> is the api's home page and doesnt show any information</p>
            @else
                <p class="error-message"><u>/{{ request()->path() }}/</u> doesnt contain any information</p>
            @endif
        </div>
    </body>
</html>
