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
                <div id="weather-current-container" style="opacity: 0;" class="col"></div>
            </div>
            
            <div class="row">
                <div id="weather-48hours-container" style="opacity: 0;" class="col"></div>

                <canvas id="weather-48hours-canvas" style="opacity: 0;" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
<script>
    let city = @json($city);

    (function() {
        setProgress(50);
        let url = window.location.origin + `/weather/${city}/detailedForecast`;

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
            console.log(data);
            setProgress(100);
            hideProgress();
    
            displayCurrentWeatherData(data);
            display48HoursWeatherHeader(data);
            display48HoursWeatherDiagram(data);
        }

        function displayCurrentWeatherData(data)
        {
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
                            <div class="row">
                                <div class="col">
                                    Current:
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><small><small>TEMP:</small></small></div>
                                    </div>
                                    <div class="row">
                                        <div class="col">${data.main.temp} <small>&deg;C</small></div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <small><small>Feels like:</small></small>
                                            <br>
                                            ${data.main.feels_like} <small>&deg;C</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="row">
                                        <div class="col"><small><small>HUMIDITY:</small></small></div>
                                    </div>
                                    <div class="row">
                                        <div class="col">${data.main.humidity} <small>%</small></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="row">
                                        <div class="col"><small><small>WIND:</small></small></div>
                                    </div>
                                    <div class="row">
                                        <div class="col">${data.wind.speed} <small>meter/sec</small></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="row">
                                        <div class="col"><small><small>SUN:</small></small></div>
                                    </div>
                                    <div class="row">
                                        <small><small>Rise:</small></small>
                                        <br>
                                        <div class="col">${data.sys.sunrise}</div>
                                    </div>
                                    <div class="row">
                                        <small><small>Set:</small></small>
                                        <br>
                                        <div class="col">${data.sys.sunset}</div>
                                    </div>
                                </div>
                            </div>
                        <div>&nbsp;</div>
                    </div>
                </div>
            `;

            row.appendChild(col);
            
            let weatherDetailContainer = document.getElementById("weather-current-container");
            weatherDetailContainer.appendChild(row);
            fadeInEffect(weatherDetailContainer);
        }

        function display48HoursWeatherHeader(data)
        {
            let titleBlock = document.createElement('div');
            titleBlock.innerHTML = `
                <div class="row">
                    <div class="col">
                        Next hours:
                    </div>
                </div>
            `;
        
            let weatherDataContainer = document.getElementById("weather-48hours-container");
            weatherDataContainer.appendChild(titleBlock);
            fadeInEffect(weatherDataContainer);
        }

        function display48HoursWeatherDiagram(data)
        {
            let canvas = document.getElementById('weather-48hours-canvas');
            var ctx = canvas.getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: '# of Votes',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            fadeInEffect(canvas);
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

        function fadeInEffect(element)
        {
            let fadeEffect2 = setInterval(function () {
                if (!element.style.opacity) {
                    element.style.opacity = 0;
                }
                if (element.style.opacity < 1) {
                    let oldValue = element.style.opacity;
                    let addValue = 0.1;
                    let newVaue = parseFloat(oldValue) + parseFloat(addValue);

                    element.style.opacity = newVaue;
                } else {
                    clearInterval(fadeEffect2);
                }
            }, 100);
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
            }, 100);
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