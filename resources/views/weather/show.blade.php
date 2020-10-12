@extends('layouts.master')

@section('title', 'City')

@section('content')
    <div class="row">
        <div class="col">

            <div class="row mb-3">
                <div class="col-sm-6 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <a class="collapsed d-block" data-toggle="collapse" href="#current-body-container" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                Current 
                            </a>
                        </div>
                        <div id="current-body-container" class="collapse show" aria-labelledby="heading-collapsed">
                            <div class="card-body py-2 px-0" style="min-height:150px;">
                                <div id="progress-container-current" class="progress col" style="height: 1;">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div id="weather-current-container" style="opacity: 0;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <a class="collapsed d-block" data-toggle="collapse" href="#alerts-outter-container" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                Alerts 
                            </a>
                        </div>
                        <div id="alerts-outter-container" class="collapse" aria-labelledby="heading-collapsed">
                            <div class="card-body py-2 px-0" style="min-height:150px;">
                                <div id="progress-container-alerts" class="progress col" style="height: 1;">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div id="alerts-container" class="pl-1" style="opacity: 0;"></div>
                            </div>
                        </div>
                      </div>
                </div>

                <div class="col-sm-6 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <a class="collapsed d-block" data-toggle="collapse" href="#next-hours-body-container" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                Next hours 
                            </a>
                        </div>
                        <div id="next-hours-body-container" class="collapse" aria-labelledby="heading-collapsed">
                            <div class="card-body py-2 px-0" style="min-height:150px;">
                                <div id="progress-container-next-hours" class="progress col" style="height: 1;">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <canvas id="weather-48hours-canvas" style="opacity: 0;" width="400" height="400"></canvas>
                            </div>
                        </div>
                      </div>
                </div>

                <div class="col-sm-6 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <a class="collapsed d-block" data-toggle="collapse" href="#next-days-body-container" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                Next 7 days 
                            </a>
                        </div>
                        <div id="next-days-body-container" class="collapse show" aria-labelledby="heading-collapsed">
                            <div class="card-body py-2 px-0" style="min-height:150px;">
                                <div id="progress-container-next-days" class="progress col" style="height: 1;">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div id="daily-weather-table-container-1" style="opacity: 0;"></div>
                            </div>
                        </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
<script>
    let city = @json($city);

    (function() {
        setProgress('progress-container-current', 20);
        setProgress('progress-container-alerts', 20);
        setProgress('progress-container-next-hours', 20);
        setProgress('progress-container-next-days', 20);

        let urlCurrent = window.location.origin + `/weather/${city}/getWeather`;
        let urlDetailed = window.location.origin + `/weather/${city}/detailedForecast`;

        fetch(urlCurrent).then((response) => {
            if (response.ok) {
                return response;
            } else {
                throw new Error('Something went wrong');
            }
        })
        .then(response => response.json())
        .then(data => displayCurrent(data))
        .catch((error) => {
            console.log(error)
        });

        fetch(urlDetailed).then((response) => {
            if (response.ok) {
                return response;
            } else {
                throw new Error('Something went wrong');
            }
        })
        .then(response => response.json())
        .then(data => displayDetailed(data))
        .catch((error) => {
            console.log(error)
        });

        function displayCurrent(data) {
            console.log(data);
            setProgress('progress-container-current', 60);

            displayCurrentWeatherData(data);
        }

        function displayDetailed(data) {
            console.log(data);
            setProgress('progress-container-next-hours', 60);
            setProgress('progress-container-alerts', 60);
            setProgress('progress-container-next-days', 60);
    
            display48HoursWeatherDiagram(data);
            displayDailyWeatherTable(data);
            displayAlerts(data);
        }

        function displayCurrentWeatherData(data)
        {
            data = processTimes(data);

            let row = document.createElement('div');
            let col = document.createElement('div');

            row.setAttribute('class', 'row');
            col.setAttribute('class', 'col');

            col.innerHTML = `
            <div class="pl-1">
                <div class="row">
                    <div class="col-5">
                        <h4>${data.name}</h4>
                    </div>
                    <div class="col-3" id="current-icon-container"></div>
                    <div class="col-4">
                        <b>${data.main.temp}</b> <small>&deg;C</small><br>
                        (${data.main.feels_like} <small>&deg;C</small>)
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col text-center">
                        <small><small>HUMIDITY:</small></small><br>
                        ${data.main.humidity} <small>%</small>
                    </div>
                    <div class="col text-center">
                        <small><small>SUNRISE:</small></small><br>
                        ${data.sys.sunrise}
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <small><small>WIND:</small></small><br>
                        ${data.wind.speed * 3.6} <small>km/h</small>
                    </div>
                    <div class="col text-center">
                        <small><small>SUNSET:</small></small><br>
                        ${data.sys.sunset}
                    </div>
                </div>
            </div>    
            `;

            row.appendChild(col);
            
            let weatherDetailContainer = document.getElementById("weather-current-container");
            weatherDetailContainer.appendChild(row);

            let currentIconContainer = document.getElementById('current-icon-container');
            currentIconContainer.appendChild(getWeatherIcon(data.weather));

            setProgress('progress-container-current', 100);
            hideProgress('progress-container-current');
            fadeInEffect(weatherDetailContainer);
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

            setProgress('progress-container-next-hours', 100);
            hideProgress('progress-container-next-hours');
            fadeInEffect(canvas);
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
            table.setAttribute('class', 'table table-striped');

            data.daily.forEach(dailyData => {
                let tr = document.createElement('tr');

                let td1 = getTdElement();
                let td2 = getTdElement();
                let td3 = getTdElement();
                let td7 = getTdElement();
                let tds = getTdElement();

                td1.setAttribute('class', 'pl-1');

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
            setProgress('progress-container-next-days', 100);
            hideProgress('progress-container-next-days');
            fadeInEffect(weatherDataContainer);
        }

        function displayAlerts(data) {
            let alertsContainer = document.getElementById('alerts-container');

            data.alerts.forEach(alertData => {
                let table = document.createElement('table');
                table.setAttribute('class', 'pb-1');

                let tr1 = document.createElement('tr');
                let tr2 = document.createElement('tr');

                let tdIcon = getTdElement();
                let tdDate = getTdElement();
                let tdEvent = getTdElement();
                let tdDesc = getTdElement();

                tdIcon.setAttribute('class', 'pr-2');

                let i = document.createElement('i');
                i.setAttribute('class', 'material-icons');
                i.innerHTML = 'warning';

                let smallDateElement = document.createElement('small');
                smallDateElement.setAttribute('class', 'font-weight-bold')
                smallDateElement.innerHTML = `${convertDate(alertData.start)} - ${convertDate(alertData.end)}`;

                tdIcon.appendChild(i);
                tdDate.appendChild(smallDateElement);
                tdEvent.innerHTML = alertData.event;
                tdDesc.innerHTML = `${alertData.description}<br><small><small>${alertData.sender_name}</small></small>`;

                tr1.appendChild(tdIcon);
                tr1.appendChild(tdDate);
                tr2.appendChild(tdEvent);
                tr2.appendChild(tdDesc);

                table.appendChild(tr1);
                table.appendChild(tr2);

                alertsContainer.appendChild(table);
                alertsContainer.appendChild(document.createElement('hr'));
            });

            setProgress('progress-container-alerts', 100);
            hideProgress('progress-container-alerts');

            fadeInEffect(alertsContainer);
        }

        function getTdElement() {
            let td = document.createElement('td');
            td.setAttribute('class', 'p-0')

            return td;
        }

        function getWeatherIcon(weather) {
            let div = document.createElement('div');

            weather.forEach(weatherData => {
                let divInner = document.createElement('div');

                divInner.innerHTML = `
                    <div id="icon"><img id="wicon" src="http://openweathermap.org/img/wn/${weatherData.icon}.png" alt="Weather icon"></div>
                `;

                div.appendChild(divInner);
            });

            return div;
        }

        function setProgress(containerId, value) {
            let container = document.getElementById(containerId);
            let progressBar = container.childNodes[1];

            progressBar.style.width = value + '%';
        }

        function hideProgress(containerId) 
        {
            let container = document.getElementById(containerId);

            fadeOutEffect(container);
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

            console.log(unixTime, dt, h, m, t);

            return t
        }

        function convertDate(unixTime) {
            let months_arr = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            let date = new Date(unixTime*1000);
            let year = date.getFullYear();
            let month = months_arr[date.getMonth()];
            let day = date.getDate();
            let hours = date.getHours();
            let minutes = "0" + date.getMinutes();
            let seconds = "0" + date.getSeconds();

            let convdataTime = month+'-'+day+'-'+year+' '+hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

            return convdataTime;
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
