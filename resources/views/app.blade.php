<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <title>{{env('APP_NAME')}}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{asset('css/app.css')}}">
    <link rel="icon" type="image/png" href="{{asset('favicon.jpg')}}"/>
</head>
<body>
<div id="app">
    <app></app>
</div>
<script src="{{asset('js/app.js')}}"></script>
<script>
    (function () { // make vuetify dialogs movable
        const d = {};
        document.addEventListener("mousedown", e => {
            const closestDialog = e.target.closest(".v-dialog.v-dialog--active.draggable");
            if (e.button === 0 && closestDialog != null && e.target.classList.contains("v-card__title")) { // element which can be used to move element
                d.el = closestDialog; // element which should be moved
                d.mouseStartX = e.clientX;
                d.mouseStartY = e.clientY;
                d.elStartX = d.el.getBoundingClientRect().left;
                d.elStartY = d.el.getBoundingClientRect().top;
                d.el.style.position = "fixed";
                d.el.style.margin = 0;
                d.oldTransition = d.el.style.transition;
                d.el.style.transition = "none"
            }
        });
        document.addEventListener("mousemove", e => {
            if (d.el === undefined) return;
            d.el.style.left = Math.min(
                Math.max(d.elStartX + e.clientX - d.mouseStartX, 0),
                window.innerWidth - d.el.getBoundingClientRect().width
            ) + "px";
            d.el.style.top = Math.min(
                Math.max(d.elStartY + e.clientY - d.mouseStartY, 0),
                window.innerHeight - d.el.getBoundingClientRect().height
            ) + "px";
        });
        document.addEventListener("mouseup", () => {
            if (d.el === undefined) return;
            d.el.style.transition = d.oldTransition;
            d.el = undefined
        });
        setInterval(() => { // prevent out of bounds
            const dialog = document.querySelector(".v-dialog.v-dialog--active.draggable");
            if (dialog === null) return;
            dialog.style.left = Math.min(parseInt(dialog.style.left), window.innerWidth - dialog.getBoundingClientRect().width) + "px";
            dialog.style.top = Math.min(parseInt(dialog.style.top), window.innerHeight - dialog.getBoundingClientRect().height) + "px";
        }, 100);
    })();
</script>
</body>
<style>
    html {
        overflow-y: hidden;
        overflow-x: hidden;
    }
</style>
</html>
