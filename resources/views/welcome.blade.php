<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">

        <!-- CSS -->
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">
        <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css" type="text/css">
        <link rel="stylesheet" href="../css/app.css" type="text/css">

        <!-- mapbox scripts -->
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>

        <!-- Promise polyfill script required to use Mapbox GL Geocoder in IE 11 -->
        <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>
        <style>
            body { margin: 0; padding: 0; }
            #map { position: absolute; top: 0; bottom: 0; width: 100%; }
        </style>

        <title>Weather App</title>
    </head>
    <body>
        <div id="container">


            <style>
                #search_bar {
                    z-index: 1;
                    margin: 25px;
                }
                .mapboxgl-ctrl-geocoder {
                    min-width: 50%;
                    background-color: #90A8C3;
                }
            </style>

            <div id="search_bar"></div>


            <pre id="result">

            </pre>

            <div id="info">
                <h1><i style="color: white; margin-left: 4rem;">Please type the city to check the forecast.</i></h1>
            </div>

            <div id="weather_card" style="visibility:hidden;"></div>


            <div id="forecast">

            </div>



            <script>
                mapboxgl.accessToken = '{!! env('MAPBOX_APP_KEY') !!}'
                var search_bar = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    types: 'place'
                });

                search_bar.addTo('#search_bar');
                var results = document.getElementById('result');

                var daily_forecast = document.getElementById('daily_forecast');
                var forecast = document.getElementById('forecast');

                var weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                var months = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                search_bar.on('result', function (e) {
                    const location = JSON.parse(JSON.stringify(e.result, null, 2));
                    console.log(location.text);
                    console.log(location.center);

                    let url ='/weather?lat=' + location.center[1] +
                        '&lon=' + location.center[0];

                    async function getData(url) {
                        const response = await fetch(url);
                        const weather = await response.json();

                        console.log(weather);
                        const weather_info =
                        {
                            temp: (weather.current.temp - 273).toFixed(1),
                            feels_like: weather.current.feels_like - 273,
                            pressure: weather.current.pressure,
                            humidity: weather.current.humidity,
                            wind_speed: weather.current.wind_speed,
                            clouds: weather.current.clouds,
                            icon: weather.current.weather[0].icon,
                            description: weather.current.weather[0].description,
                            sunrise: new Date(weather.current.sunrise).toLocaleTimeString("en-US"),
                        };

                        let date = new Date();
                        let today = date.getDay();
                        console.log();
                        document.getElementById('weather_card').style.visibility = "visible";
                        document.getElementById('info').style.display = "none";
                        document.getElementById('weather_card').innerHTML =
                            `<h3>` + location.text + `, ` + weekdays[today] + `, ` + date.getDay() + ` ` + months[date.getMonth()] +`</h3>
                            <h1 class="temperature">` + weather_info['temp'] + ` &#8451;</h1>
                            <img class="icon_main" src='http://openweathermap.org/img/wn/` + weather_info['icon'] + `@2x.png' alt="Weather icon">
                            <h3><i>` + weather_info['description'] + `</i></h3>

                            <div class="parameters">
                                <div class="parameters">
                                    Pressure: <b>` + weather_info['pressure'] + ` hPa</b><br>
                                    Humidity: <b>` + weather_info['humidity'] + `%</b>
                                </div>
                                <div class="parameters">
                                    Wind Speed: <b>` + weather_info['wind_speed'] + ` m/s</b><br>
                                    Clouds: <b>` + weather_info['clouds'] + `%</b>
                                </div>
                            </div>`;

                        forecast.innerText = '';
                        for(let i = 0; i<8; i++)
                        {
                            let temp_day = (weather.daily[i].temp.day - 273).toFixed(1);
                            let temp_night = (weather.daily[i].temp.night - 273).toFixed(1);

                            document.getElementById('forecast').innerHTML +=
                                `<div id="daily_forecast">
                                    <h3>` + weekdays[(today+i)%7] +`</h3>
                                    ` + temp_day + ` &#8451;/`+ temp_night + ` &#8451;
                                    <figure>
                                        <img class="icon_main" src='http://openweathermap.org/img/wn/` + weather.daily[i].weather[0].icon + `@2x.png' alt="Weather icon">
                                        <figcaption>` + weather.daily[i].weather[0].description + `</figcaption>
                                    </figure>
                                </div>`;
                        }
                    }

                    getData(url)
                });

                // Clear results container when search is cleared.
                search_bar.on('clear', function () {
                    results.innerText = '';
                    daily_forecast.innerText = '';
                });

            </script>
        </div>
    </body>
</html>
