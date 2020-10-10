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
            
            <div class="row mt-3">
                <div id="weather-48hours-container" style="opacity: 0;" class="col"></div>

                <canvas id="weather-48hours-canvas" style="opacity: 0;" width="400" height="400"></canvas>
            </div>

            <div class="row mt-3">
                <div id="weather-daily-container" style="opacity: 0;" class="col"></div>

                {{-- <canvas id="weather-daily-canvas" style="opacity: 0;" width="400" height="400"></canvas> --}}
            </div>
            <div class="row">
                <div id="daily-weather-table-container-1" class="col"></div>
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
            displayDailyWeatherHeader();
            // displayDailyWeatherDiagram(data);
            displayDailyWeatherTable(data);
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
            let times = [];
            let temps = [];
            let backgroundColors = [];
            let borderColors = [];

            data.hourly.forEach(hourlyData => {
                times.push(convertTime(hourlyData.dt));
                temps.push(hourlyData.temp);
                backgroundColors.push('rgba(255, 99, 132, 0.2)');
                borderColors.push('rgba(255, 99, 132, 1)');
            });

            let canvas = document.getElementById('weather-48hours-canvas');
            let ctx = canvas.getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: times,
                    datasets: [
                        {
                            data: temps,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    legend : {
                        display: false
                    }
                }
            });

            fadeInEffect(canvas);
        }

        function displayDailyWeatherHeader()
        {
            let titleBlock = document.createElement('div');
            titleBlock.innerHTML = `
                <div class="row">
                    <div class="col">
                        Next 7 days:
                    </div>
                </div>
            `;
        
            let weatherDataContainer = document.getElementById("weather-daily-container");
            weatherDataContainer.appendChild(titleBlock);
            fadeInEffect(weatherDataContainer);
        }

        function displayDailyWeatherDiagram(data)
        {
            let labels = [];
            let maxes = [];
            let mins = [];
            let backgroundColorsMaxes = [];
            let borderColorsMaxes = [];
            let backgroundColorsMins = [];
            let borderColorsMins = [];

            data.daily.forEach(dailyData => {
                maxes.push(dailyData.temp.max);
                mins.push(dailyData.temp.min);

                labels.push(
                    getWeekDay(dailyData.dt) + '\n' + 
                    getRain(dailyData.rain || 0) + '\n' + 
                    getPrecipitationPercent(dailyData.pop)
                );

                backgroundColorsMaxes.push('rgba(255, 99, 132, 0.2)');
                borderColorsMaxes.push('rgba(255, 99, 132, 1)');
                backgroundColorsMins.push('rgba(174, 200, 242, 0.8)');
                borderColorsMins.push('rgba(174, 200, 242, 1)');
            });
            
            let canvas = document.getElementById('weather-daily-canvas');
            let ctx = canvas.getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: maxes,
                            backgroundColor: backgroundColorsMaxes,
                            borderColor: borderColorsMaxes,
                            borderWidth: 1
                        },
                        {
                            data: mins,
                            backgroundColor: backgroundColorsMins,
                            borderColor: borderColorsMins,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            stacked: true
                        }]
                    },
                    legend : {
                        display: false
                    },
                    animation: {
                        duration: 1,
                        onComplete: function () {
                            var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            this.data.datasets.forEach(function (dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function (bar, index) {
                                    var data = dataset.data[index];                            
                                    ctx.fillText(data, bar._model.x, bar._model.y - 5);
                                });
                            });
                        }
                    }
                },
                plugins: [{
                    beforeInit: function(chart) {
                        chart.data.labels.forEach(function(e, i, a) {
                            if (/\n/.test(e)) {
                                a[i] = e.split(/\n/);
                            }
                        });
                    }
                }]
            });

            fadeInEffect(canvas);
        }

        function displayDailyWeatherTable(data) {
            let table = document.createElement('table');

            data.daily.forEach(dailyData => {
                let tr = document.createElement('tr');

                let td1 = document.createElement('td');
                let td2 = document.createElement('td');
                let td3 = document.createElement('td');
                let td7 = document.createElement('td');
                let tds = document.createElement('td');

                td1.innerHTML = getWeekDay(dailyData.dt);
                td2.appendChild(getWeatherIcon(dailyData.weather));
                td3.innerHTML = ` ${dailyData.temp.max}  <small>&deg;C</small><br>${dailyData.temp.min} <small>&deg;C</small>`;
                td7.innerHTML = ` ${getRain(dailyData.rain)}<br>${getPrecipitationPercent(dailyData.pop)}`;

                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(tds)
                tr.appendChild(td3);
                tr.appendChild(td7);

                table.appendChild(tr);
            });
            
            
            let weatherDataContainer = document.getElementById("daily-weather-table-container-1");
            weatherDataContainer.appendChild(table);
            fadeInEffect(weatherDataContainer);
        }

        function getWeatherIcon(weather) {
            let div = document.createElement('div');

            weather.forEach(weatherData => {
                let divInner = document.createElement('div');

                divInner.innerHTML = `
                    <div id="icon"><img id="wicon" src="http://openweathermap.org/img/wn/${weatherData.icon}@2x.png" alt="Weather icon"></div>
                `;

                div.appendChild(divInner);
            });

            return div;
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
            }, 200);
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

        function getWeekDay(dt) {
            let weekDays= ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            let d = new Date(dt * 1000);

            return weekDays[d.getDay()];
        }

        function getPrecipitationPercent(popValue) {
            let percent = Math.floor(popValue * 100);

            return percent == 0 ? '' : '(' + percent + ' %)';
        }

        function getRain(rain) {
            if (rain !== undefined) {
                return rain + ' mm';
            }
            
            return '';
        }
    })();
    
</script>   
@endsection
