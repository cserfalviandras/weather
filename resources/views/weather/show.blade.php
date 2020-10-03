@extends('layouts.master')

@section('title', 'City')

@section('content')
    <div class="row">
        <div class="col">

            <div class="row mt-3">
                <div id="progress-container" class="progress col" style="height: 1;">
                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="row">
                <div id="weather-detail-container" class="col"></div>
            </div>
            
        </div>
    </div>
<script>
    let city = @json($city);

    (function() {
        setProgress(50);
        let url = window.location.origin + `/weather/${city}/getWeather`;

        fetch(url).then((response) => {
            if (response.ok) {
                return response;
            } else {
                throw new Error('Something went wrong');
            }
        })
        .then(response => response.json())
        .then(data => displayWeatherData(data))
        .catch((error) => {
            console.log(error)
        });

        function displayWeatherData(data) {
            setProgress(100);
            hideProgress();

            data = processTimes(data);

            let row = document.createElement('div');
            let col = document.createElement('div');

            row.setAttribute('class', 'row');
            col.setAttribute('class', 'col');

            col.innerHTML = `
                <div class="current">
                    <div class="info">
                        <div>&nbsp;</div>
                        <h4>${data.name}</h4>
                        <div class="temp">
                            <small><small>TEMP:</small></small>
                            <div class="row">
                                <div class="col ml-3">${data.main.temp} <small>&deg;C</small></div>
                            </div>
                            <div class="row">
                                <div class="col ml-3">${data.main.feels_like} <small>&deg;C (feels like)</small></div>
                            </div>

                            <small><small>HUMIDITY:</small></small>
                            <div class="row">
                                <div class="col ml-3">${data.main.humidity} <small>%</small></div>
                            </div>

                            <small><small>WIND:</small></small>
                            <div class="row">
                                <div class="col ml-3">${data.wind.speed} <small>meter/sec</small></div>
                            </div>

                            <small><small>SUNRISE:</small></small>
                            <div class="row">
                                <div class="col ml-3">${data.sys.sunrise}</div>
                            </div>

                            <small><small>SUNSET:</small></small>
                            <div class="row">
                                <div class="col ml-3">${data.sys.sunset}</div>
                            </div>
                        <div>&nbsp;</div>
                    </div>
                </div>
            `;

            row.appendChild(col);

            document.getElementById("weather-detail-container").appendChild(row);
        }

        function setProgress(value) {
            let progressBar = document.getElementById("progress-bar");

            progressBar.style.width = value + '%';
        }

        function hideProgress() 
        {
            let progressBar = document.getElementById("progress-bar");

            fadeOutEffect(progressBar);
        }

        function fadeOutEffect(element)
        {
            let fadeEffect = setInterval(function () {
                if (!element.style.opacity) {
                    element.style.opacity = 1;
                }
                if (element.style.opacity > 0) {
                    element.style.opacity -= 0.1;
                } else {
                    clearInterval(fadeEffect);
                }
            }, 200);
        }

        function processTimes(data)
        {
            data.sys.sunset = convertTime(data.sys.sunset);
            data.sys.sunrise = convertTime(data.sys.sunrise);

            return data;
        }

        function convertTime(unixTime){
            let dt = new Date(unixTime * 1000)
            let h = dt.getHours()
            let m = "0" + dt.getMinutes()
            let t = h + ":" + m.substr(-2)

            return t
        }

    })();
    
</script>   
@endsection