<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
        <style>
            body { margin: 0; padding: 0; }
            #map { position: absolute; top: 0; bottom: 0; width: 100%; }
        </style>

        <title>Weather App</title>
    </head>
    <body>

    <div id="container">
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>
        <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css" type="text/css">
        <!-- Promise polyfill script required to use Mapbox GL Geocoder in IE 11 -->
        <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>

        <style>
            #geocoder {
                z-index: 1;
                margin: 20px;
            }
            .mapboxgl-ctrl-geocoder {
                min-width: 100%;
            }
        </style>

        <div id="geocoder"></div>

        <div id="weather">

        </div>
        <pre id="result"></pre>

        <script>
            mapboxgl.accessToken = '{!! env('MAPBOX_APP_KEY') !!}'
            var geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                types: 'place'
            });

            geocoder.addTo('#geocoder');

            // Get the geocoder results container.
            var results = document.getElementById('result');

            // Add geocoder result to container.
            geocoder.on('result', function (e) {
                const obj = JSON.parse(JSON.stringify(e.result, null, 2));
                console.log(obj.text);
                console.log(obj.center);



                let url = 'https://api.openweathermap.org/data/2.5/onecall?lat='  + obj.center[1] +
                    '&lon=' + obj.center[0] + '&exclude=hourly,daily&appid=' + '{!! env('OPEN_WEATHER_APP_KEY') !!}';

                fetch(url)
                    .then(response => response.json())
                    .then(data => results.innerText = JSON.stringify(data, null, 2));
                
            });

            // Clear results container when search is cleared.
            geocoder.on('clear', function () {
                results.innerText = '';
            });

        </script>

    </div>


    </body>
</html>
