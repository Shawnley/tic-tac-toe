<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-900 flex items-center justify-center min-h-screen">
    <script type="module">
        var global = window
        import RefreshRuntime from "http://localhost:5175/@react-refresh"
        RefreshRuntime.injectIntoGlobalHook(window)
        window.$RefreshReg$ = () => {}
        window.$RefreshSig$ = () => (type) => type
        window.__vite_plugin_react_preamble_installed__ = true
    </script>

    <div class="container mx-auto p-4 max-w-lg">
        <div id="app"></div>
    </div>
    @vite('resources/js/app.jsx')
</body>

</html>