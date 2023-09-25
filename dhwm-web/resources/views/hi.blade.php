<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>hi</title>
    @vite([
        'resources/js/lib/index.js',
        'resources/js/app.js',
    ])
</head>
<body>
<div class="xxx" id="app">
    <div v-cloak>
        <el-button>@{{ message }}</el-button>
    </div>
</div>
<script type="module">
    import { createApp, http } from "lib/index.js";
    const app = createApp({
        data() {
            return {
                message: "Hello Element Plus",
            };
        },
    }, '#app');
    window.http = http
</script>
</body>
</html>
