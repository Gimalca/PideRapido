'use strict'; 
/*! main.js - v0.1.1
* http://admindesigns.com/
* Copyright (c) 2013 Admin Designs;*/


/* Demo theme functions. Required for
 * Settings Pane and misc functions */
var demoHighCharts = function () {

        // Define chart color patterns
        var highColors = [bgWarning, bgPrimary, bgInfo, bgAlert,
            bgDanger, bgSuccess, bgSystem, bgDark
        ];

        // Color Library we used to grab a random color
        var sparkColors = {
            "primary": [bgPrimary, bgPrimaryLr, bgPrimaryDr],
            "info": [bgInfo, bgInfoLr, bgInfoDr],
            "warning": [bgWarning, bgWarningLr, bgWarningDr],
            "success": [bgSuccess, bgSuccessLr, bgSuccessDr],
            "alert": [bgAlert, bgAlertLr, bgAlertDr]
        };


        // Sparklines Demo
        var demoSparklines = function() {

             var sparkLine = $('.inlinesparkline');
             // Init Sparklines
             if (sparkLine.length) {

                var sparklineInit = function() {
                        $('.inlinesparkline').each(function(i, e) {
                            var This = $(this);
                            var Color = sparkColors["primary"];
                            var Height = '35';
                            var Width = '70%';
                            This.children().remove();
                            // default color is "primary"
                            // Color[0] = default shade
                            // Color[1] = light shade
                            // Color[2] = dark shade
                            //alert('hi')
                            // User assigned color and height, else default
                            var userColor = This.data('spark-color');
                            var userHeight = This.data('spark-height');
                            if (userColor) {
                                Color = sparkColors[userColor];
                            }
                            if (userHeight) {
                                Height = userHeight;
                            }
                            $(e).sparkline('html', {
                                type: 'line',
                                width: Width,
                                height: Height,
                                enableTagOptions: true,
                                lineColor: Color[2], // Also tooltip icon color
                                fillColor: Color[1],
                                spotColor: Color[0],
                                minSpotColor: Color[0],
                                maxSpotColor: Color[0],
                                highlightSpotColor: bgWarningDr,
                                highlightLineColor: bgWarningLr
                            });
                        });
                }

                // Refresh Sparklines on Resize
                var refreshSparklines;

                $(window).resize(function(e) {
                    clearTimeout(refreshSparklines);
                    refreshSparklines = setTimeout(sparklineInit, 500);
                });

                sparklineInit();
            }

        }// End Sparklines Demo


        // Circle Graphs Demo
        var demoCircleGraphs = function() {
            var infoCircle = $('.info-circle');
            if (infoCircle.length) {
                // Color Library we used to grab a random color
                var colors = {
                    "primary": [bgPrimary, bgPrimaryLr,
                        bgPrimaryDr
                    ],
                    "info": [bgInfo, bgInfoLr, bgInfoDr],
                    "warning": [bgWarning, bgWarningLr,
                        bgWarningDr
                    ],
                    "success": [bgSuccess, bgSuccessLr,
                        bgSuccessDr
                    ],
                    "alert": [bgAlert, bgAlertLr, bgAlertDr]
                };
                // Store all circles
                var circles = [];
                infoCircle.each(function(i, e) {
                    // Define default color
                    var color = ['#DDD', bgPrimary];
                    // Modify color if user has defined one
                    var targetColor = $(e).data(
                        'circle-color');
                    if (targetColor) {
                        var color = ['#DDD', colors[
                            targetColor][0]]
                    }
                    // Create all circles
                    var circle = Circles.create({
                        id: $(e).attr('id'),
                        value: $(e).attr('value'),
                        radius: $(e).width() / 2,
                        width: 14,
                        colors: color,
                        text: function(value) {
                            var title = $(e).attr('title');
                            if (title) {
                                return '<h2 class="circle-text-value">' + value + '</h2><p>' + title + '</p>' 
                            } 
                            else {
                                return '<h2 class="circle-text-value mb5">' + value + '</h2>'
                            }
                        }
                    });
                    circles.push(circle);
                });

                // Add debounced responsive functionality
                var rescale = function() { 
                    infoCircle.each(function(i, e) {
                        var getWidth = $(e).width() / 2;
                        circles[i].updateRadius(
                            getWidth);
                    });
                    setTimeout(function() {
                        // Add responsive font sizing functionality
                        $('.info-circle').find('.circle-text-value').fitText(0.4);
                    },50);
                } 
                var lazyLayout = _.debounce(rescale, 300);
                $(window).resize(lazyLayout);
              
            }

        } // End Circle Graphs Demo



        // High Charts Demo
        var demoHighCharts = function() {


            // Column Charts
            var demoHighColumns = function() {

                 var column1 = $('#high-column');

                 if (column1.length) {
                    // Column Chart 1
                    $('#high-column').highcharts({
                        credits: false,
                        colors: highColors,
                        chart: {
                            backgroundColor: 'transparent',
                            type: 'column',
                            padding: 0,
                            margin: 0,
                            marginTop: 10
                        },
                        legend: {
                            enabled: false
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            lineWidth: 0,
                            tickLength: 0,
                            minorTickLength: 0,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: false
                            }
                        },
                        yAxis: {
                            gridLineWidth: 0,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: false
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                groupPadding: 0.05,
                                pointPadding: 0.25,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: 'Behance',
                            data: [30]
                        }, {
                            name: 'Twitter',
                            data: [60]
                        }, {
                            name: 'Facebook',
                            data: [90]
                        }, {
                            name: 'Dribble',
                            data: [120]
                        }]
                    });
                 }
                    
                 var column2 = $('#high-column2');
                 
                 if (column2.length) {

                    // Column Chart 2
                    $('#high-column2').highcharts({
                        credits: false,
                        colors: [bgPrimary, bgPrimary, bgWarning,
                            bgWarning, bgInfo, bgInfo
                        ],
                        chart: {
                            backgroundColor: 'transparent',
                            marginTop: 30,
                            marginBottom: 30,
                            type: 'column',
                        },
                        legend: {
                            enabled: false
                        },
                        title: {
                            text: null,
                        },
                        xAxis: {
                            lineWidth: 0,
                            tickLength: 6,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: true
                            }
                        },
                        yAxis: {
                            max: 20,
                            lineWidth: 0,
                            gridLineWidth: 0,
                            lineColor: '#EEE',
                            gridLineColor: '#EEE',
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: false,
                                style: {
                                    fontWeight: '400'
                                }
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                colorByPoint: true,
                            }
                        },
                        series: [{
                            name: 'Tokyo',
                            data: [12, 14, 20, 19, 8, 12,
                                14, 20, 5, 16, 8, 12,
                                14, 20, 19, 5, 16, 8,
                                12, 14, 20, 19, 5, 16,
                                8
                            ]
                        }]
                    });

                 }

                 var column3 = $('#high-column3');
                 
                 if (column3.length) {

                    // Column Chart3
                    $('#high-column3').highcharts({
                        credits: false,
                        colors: highColors,
                        chart: {
                            type: 'column',
                            padding: 0,
                            spacingTop: 10,
                            marginTop: 100,
                            marginLeft: 30,
                            marginRight: 30
                        },
                        legend: {
                            enabled: false
                        },
                        title: {
                            text: '30.8 hrs',
                            style: {
                                fontSize: 20,
                                fontWeight: 600
                            }
                        },
                        subtitle: {
                            text: 'Average First response time <br> in past 30 days',
                            style: {
                                color: '#AAA'
                            }
                        },
                        xAxis: {
                            lineWidth: 0,
                            tickLength: 0,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: true,
                                formatter: function() {
                                    return this.value + "-" + (
                                            this.value + 2) +
                                        "<br> hours"; // clean, unformatted number for year
                                }
                            },
                        },
                        yAxis: {
                            gridLineWidth: 0,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: false
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                colorByPoint: true,
                                colors: [bgPrimary, bgPrimary,
                                    bgInfo, bgInfo
                                ],
                                groupPadding: 0,
                                pointPadding: 0.24,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: 'Yahoo',
                            data: [7, 6, 9, 14, 18, 21, 25]
                        }, {
                            visible: false,
                            name: 'CNN',
                            data: [3, 4.2, 5.7, 8.5, 11.9, 15]
                        }, {
                            visible: false,
                            name: 'Yahoo',
                            data: [1, 5, 5, 11, 17, 22, 24]
                        }, {
                            visible: false,
                            name: 'CNN',
                            data: [1, 5, 5, 11, 17.0, 22, 24]
                        }],
                        dataLabels: {
                            enabled: true,
                            rotation: 0,
                            color: '#AAA',
                            align: 'center',
                            x: 0,
                            y: -8,
                        }
                    });
                }


            } // End High Columns

            var demoHighBars = function() {

                 var bars1 = $('#high-bars');
                 
                 if (bars1.length) {

                    // Bar Chart 1
                    $('#high-bars').highcharts({
                        colors: highColors,
                        credits: false,
                        legend: {
                            enabled: false,
                            y: -5,
                            verticalAlign: 'top',
                            useHTML: true
                        },
                        chart: {
                            spacingLeft: 30,
                            type: 'bar',
                            marginBottom: 0,
                            marginTop: 0
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            showEmpty: false,
                            tickLength: 80,
                            lineColor: '#EEE',
                            tickColor: '#EEE',
                            offset: 1,
                            categories: ['TV', 'Radio'],
                            title: {
                                text: null
                            },
                            labels: {
                                align: 'right',
                            }
                        },
                        yAxis: {
                            min: 0,
                            gridLineWidth: 0,
                            showEmpty: false,
                            title: {
                                text: null
                            },
                            labels: {
                                enabled: false,
                            }
                        },
                        tooltip: {
                            valueSuffix: ' millions'
                        },
                        plotOptions: {
                            bar: {}
                        },
                        series: [{
                            id: 0,
                            name: 'Viewers',
                            data: [100, 100]
                        }, {
                            id: 1,
                            name: 'Women',
                            data: [36, 55]
                        }, {
                            id: 2,
                            name: 'Men',
                            data: [65, 45]
                        }]
                    });
                }
            }

            var demoHighLines = function() {

                var line1 = $('#high-line');
                 
                if (line1.length) {

                    // High Line 1
                    $('#high-line').highcharts({
                        credits: false,
                        colors: highColors,
                        chart: {
                            type: 'column',
                            zoomType: 'x',
                            panning: true,
                            panKey: 'shift',
                            marginRight: 50,
                            marginTop: -5,
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            gridLineColor: '#EEE',
                            lineColor: '#EEE',
                            tickColor: '#EEE',
                            categories: ['Jan', 'Feb', 'Mar', 'Apr',
                                'May', 'Jun', 'Jul', 'Aug',
                                'Sep', 'Oct', 'Nov', 'Dec'
                            ]
                        },
                        yAxis: {
                            min: -2,
                            tickInterval: 5,
                            gridLineColor: '#EEE',
                            title: {
                                text: 'Rango',
                                style: {
                                    color: bgInfo,
                                    fontWeight: '600'
                                }
                            }
                        },
                        plotOptions: {
                            spline: {
                                lineWidth: 3,
                            },
                            area: {
                                fillOpacity: 0.2
                            }
                        },
                        legend: {
                            enabled: false,
                        },
                        series: [{
                            name: 'KFC',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                            color: '#A8454E'
                        }, {
                            name: 'Mc Donalds',
                            data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8],
                            color: "#f6bb42"
                        }, {
                            visible: false,
                            name: 'Subway',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5],
                            color: '#A8454E'
                        }, {
                            visible: false,
                            name: 'Church Chicken',
                            data: [3, 1, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0],
                            color: "#f6bb42"
                        }, {
                            visible: false,
                            name: 'Firdays',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3,13.9, 9.6],
                            color: '#A8454E'
                        }, {
                            visible: false,
                            name: 'CNN',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
                        }]
                    });

                }

                var line2 = $('#high-line2');
                 
                if (line2.length) {

                    // High Line 2
                    $('#high-line2').highcharts({
                        credits: false,
                        colors: highColors,
                        chart: {
                            type: 'column',
                            zoomType: 'x',
                            panning: true,
                            panKey: 'shift',
                            marginRight: 50,
                            marginTop: -5,
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            gridLineColor: '#EEE',
                            lineColor: '#EEE',
                            tickColor: '#EEE',
                            categories: ['Jan', 'Feb', 'Mar', 'Apr',
                                'May', 'Jun', 'Jul', 'Aug',
                                'Sep', 'Oct', 'Nov', 'Dec'
                            ]
                        },
                        yAxis: {
                            min: -2,
                            tickInterval: 5,
                            gridLineColor: '#EEE',
                            title: {
                                text: 'Traffic',
                                style: {
                                    color: bgInfo,
                                    fontWeight: '600'
                                }
                            }
                        },
                        plotOptions: {
                            spline: {
                                lineWidth: 3,
                            },
                            area: {
                                fillOpacity: 0.2
                            }
                        },
                        legend: {
                            enabled: false,
                        },
                        series: [{
                            name: 'KFC',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                            color: '#A8454E'
                        }, {
                            name: 'Mc Donals',
                            data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8],
                            color: '#A8454E'
                        }, {
                            visible: false,
                            name: 'Yahoo',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
                        }, {
                            visible: false,
                            name: 'Facebook',
                            data: [3, 1, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
                        }, {
                            visible: false,
                            name: 'Facebook',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3,13.9, 9.6]
                        }, {
                            visible: false,
                            name: 'CNN',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
                        }]
                    });

                }

                var line3 = $('#high-line3');
                 
                if (line3.length) {

                    // High Line 3
                    $('#high-line3').highcharts({
                        credits: false,
                        colors: highColors,
                        chart: {
                            backgroundColor: '#f9f9f9',
                            className: 'br-r',
                            type: 'line',
                            zoomType: 'x',
                            panning: true,
                            panKey: 'shift',
                            marginTop: 25,
                            marginRight: 1,
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            gridLineColor: '#EEE',
                            lineColor: '#EEE',
                            tickColor: '#EEE',
                            categories: ['Ene', 'Feb', 'Mar', 'Abr',
                                'May', 'Jun', 'Jul', 'Ago',
                                'Sep', 'Oct', 'Nov', 'Dic'
                            ]
                        },
                        yAxis: {
                            min: 0,
                            tickInterval: 5,
                            gridLineColor: '#EEE',
                            title: {
                                text: null,
                            }
                        },
                        plotOptions: {
                            spline: {
                                lineWidth: 3,
                            },
                            area: {
                                fillOpacity: 0.2
                            }
                        },
                        legend: {
                            enabled: false,
                        },
                        series: [{
                            name: 'Con Tarjeta PD',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                            color: '#A8454E'
                        }, {
                            name: 'Sin Tarjeta PD',
                            data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
                        }, {
                            visible: false,
                            name: 'Yahoo',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
                        }, {
                            visible: false,
                            name: 'Facebook',
                            data: [3, 1, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
                        }, {
                            visible: false,
                            name: 'Facebook',
                            data: [7.0, 6, 9, 14, 18, 21.5, 25.2, 26.5, 23.3, 18.3,13.9, 9.6]
                        }, {
                            visible: false,
                            name: 'CNN',
                            data: [1, 5, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
                        }]
                    });

                }

            } // End High Line Charts Demo

            // Pie Charts
            var demoHighPies = function() { 

                var pie1 = $('#high-pie');
                 
                if (pie1.length) {

                    // Pie Chart1
                    $('#high-pie').highcharts({
                        credits: false,
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false
                        },
                        title: {
                            text: null
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                center: ['30%', '50%'],
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },
                        colors: highColors,
                        legend: {
                            x: 90,
                            floating: true,
                            verticalAlign: "middle",
                            layout: "vertical",
                            itemMarginTop: 10
                        },
                        series: [{
                            type: 'pie',
                            name: 'Browser share',
                            data: [
                                ['Firefox', 35.0],
                                ['IE', 36.8], {
                                    name: 'Chrome',
                                    y: 15.8,
                                    sliced: true,
                                    selected: true
                                },
                                ['Safari', 18.5],
                            ]
                        }]
                    });
                }
            } // End High Pie Charts Demo

            // Demo High Area Charts
            var demoHighAreas = function() {

                var area1 = $('#high-area');
                 
                if (area1.length) {

                    // Area 1
                    $('#high-area').highcharts({
                        colors: highColors,
                        credits: false,
                        chart: {
                            type: 'areaspline',
                            spacing: 0,
                            margin: -10
                        },
                        title: {
                            text: null
                        },
                        legend: {
                            enabled: false
                        },
                        xAxis: {
                            allowDecimals: false,
                            tickColor: '#EEE',
                            labels: {
                                formatter: function() {
                                    return this.value; // clean, unformatted number for year
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            },
                            gridLineColor: 'transparent',
                            labels: {
                                enabled: false,
                            }
                        },
                        plotOptions: {
                            areaspline: {
                                fillOpacity: 0.25,
                                marker: {
                                    enabled: true,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            id: 0,
                            name: 'USA',
                            data: [150, 260, 80, 100, 150,200, 240]
                        }, {
                            id: 1,
                            name: 'Russia',
                            data: [10, 20, 40, 120, 240, 180, 160]
                        }, {
                            id: 2,
                            name: 'China',
                            data: [60, 100, 180, 110, 100, 20, 40]
                        }]
                    });
                }
            }

            // Init Chart Types
            demoHighColumns();
            demoHighLines();
            demoHighBars();
            demoHighPies();
            demoHighAreas();

        } // End Demo HighCharts


        // High Charts Demo
        var demoHighChartMenus = function() {

           // Create custom menus for charts associated
           // with the ".chart-legend" element
           var chartLegend = $('.chart-legend');
                 
            if (chartLegend.length) {

                $('.chart-legend').each(function(i, ele) {
                    var legendID = $(ele).data('chart-id');
                    $(ele).find('a.legend-item').each(function(
                        i, e) {
                        var This = $(e);
                        var itemID = This.data(
                            'chart-id');
                        // Use ID of menu to find what chart it belongs to
                        // Then use ID of its child menu items to find out what
                        // data on the chart it is connected to
                        var legend = $(legendID).highcharts()
                            .series[itemID];
                        // pull legend name from chart and populate menu buttons
                        var legendName = legend.name;
                        This.html(legendName);
                        // assign click handler which toggles legend data 
                        This.click(function(e) {
                            if (This.attr(
                                'href')) {
                                e.preventDefault();
                            }
                            if (legend.visible) {
                                legend.hide();
                                This.toggleClass(
                                    'active'
                                );
                            } else {
                                legend.show();
                                This.toggleClass(
                                    'active'
                                );
                            }
                        });
                    });
                });
            }

            // Create custom menus for table charts
            var tableLegend = $('.table-legend');
                 
            if (tableLegend.length) {

                $('.table-legend').each(function(i, e) {
                    var legendID = $(e).data('chart-id');
                    $(e).find('input.legend-switch').each(
                        function(i, e) {
                            var This = $(e);
                            var itemID = This.val();
                            // Use ID of menu to find what chart it belongs to
                            // Then use ID of its child menu items to find out what
                            // data on the chart it is connected to
                            var legend = $(legendID).highcharts()
                                .series[itemID];
                            // pull legend name from chart and populate menu buttons
                            var legendName = legend.name;
                            This.html(legendName);
                            // Toggle checkbox state based on series visability
                            if (legend.visible) {
                                This.attr('checked', true);
                            } else {
                                This.attr('checked', false);
                            }
                            // assign click handler which toggles legend data 
                            This.on('click', function(i, e) {
                                if (legend.visible) {
                                    legend.hide();
                                    This.attr(
                                        'checked',
                                        false);
                                } else {
                                    legend.show();
                                    This.attr(
                                        'checked',
                                        true);
                                }
                            });
                    });
                });
            }

        } // End Demo HighChart Menus


        // Advanced HighChart Demo
        var demoHighChartsAdvanced = function() {

            // High Chart Data Set 
            var data = [
                0.8446, 0.8445, 0.8444, 0.8451,    0.8418, 0.8264,    0.8258, 0.8232,    0.8233, 0.8258,
                0.8283, 0.8278, 0.8256, 0.8292,    0.8239, 0.8239,    0.8245, 0.8265,    0.8261, 0.8269,
                0.8273, 0.8244, 0.8244, 0.8172,    0.8139, 0.8146,    0.8164, 0.82,    0.8269, 0.8269,
                0.8269, 0.8258, 0.8247, 0.8286,    0.8289, 0.8316,    0.832, 0.8333,    0.8352, 0.8357,
                0.8355, 0.8354, 0.8403, 0.8403,    0.8406, 0.8403,    0.8396, 0.8418,    0.8409, 0.8384,
                0.8386, 0.8372, 0.839, 0.84, 0.8389, 0.84, 0.8423, 0.8423, 0.8435, 0.8422,
                0.838, 0.8373, 0.8316, 0.8303,    0.8303, 0.8302,    0.8369, 0.84, 0.8385, 0.84,
                0.8401, 0.8402, 0.8381, 0.8351,    0.8314, 0.8273,    0.8213, 0.8207,    0.8207, 0.8215,
                0.8242, 0.8273, 0.8301, 0.8346,    0.8312, 0.8312,    0.8312, 0.8306,    0.8327, 0.8282,
                0.824, 0.8255, 0.8256, 0.8273, 0.8209, 0.8151, 0.8149, 0.8213, 0.8273, 0.8273,
                0.8261, 0.8252, 0.824, 0.8262, 0.8258, 0.8261, 0.826, 0.8199, 0.8153, 0.8097,
                0.8101, 0.8119, 0.8107, 0.8105,    0.8084, 0.8069,    0.8047, 0.8023,    0.7965, 0.7919,
                0.7921, 0.7922, 0.7934, 0.7918,    0.7915, 0.787, 0.7861, 0.7861, 0.7853, 0.7867,
                0.7827, 0.7834, 0.7766, 0.7751, 0.7739, 0.7767, 0.7802, 0.7788, 0.7828, 0.7816,
                0.7829, 0.783, 0.7829, 0.7781, 0.7811, 0.7831, 0.7826, 0.7855, 0.7855, 0.7845,
                0.7798, 0.7777, 0.7822, 0.7785, 0.7744, 0.7743, 0.7726, 0.7766, 0.7806, 0.785,
                0.7907, 0.7912, 0.7913, 0.7931, 0.7952, 0.7951, 0.7928, 0.791, 0.7913, 0.7912,
                0.7941, 0.7953, 0.7921, 0.7919, 0.7968, 0.7999, 0.7999, 0.7974, 0.7942, 0.796,
                0.7969, 0.7862, 0.7821, 0.7821, 0.7821, 0.7811, 0.7833, 0.7849, 0.7819, 0.7809,
                0.7809, 0.7827, 0.7848, 0.785, 0.7873, 0.7894, 0.7907, 0.7909, 0.7947, 0.7987,
                0.799, 0.7927, 0.79, 0.7878, 0.7878, 0.7907, 0.7922, 0.7937, 0.786, 0.787,
                0.7838, 0.7838, 0.7837, 0.7836, 0.7806, 0.7825, 0.7798, 0.777, 0.777, 0.7772,
                0.7793, 0.7788, 0.7785, 0.7832, 0.7865, 0.7865, 0.7853, 0.7847, 0.7809, 0.778,
                0.7799, 0.78, 0.7801, 0.7765, 0.7785, 0.7811, 0.782, 0.7835, 0.7845, 0.7844,
                0.782, 0.7811, 0.7795, 0.7794, 0.7806, 0.7794, 0.7794, 0.7778, 0.7793, 0.7808,
                0.7824, 0.787, 0.7894, 0.7893, 0.7882, 0.7871, 0.7882, 0.7871, 0.7878, 0.79,
                0.7901, 0.7898, 0.7879, 0.7886, 0.7858, 0.7814, 0.7825, 0.7826, 0.7826, 0.786,
                0.7878, 0.7868, 0.7883, 0.7893, 0.7892, 0.7876, 0.785, 0.787, 0.7873, 0.7901,
                0.7936, 0.7939, 0.7938, 0.7956, 0.7975, 0.7978, 0.7972, 0.7995, 0.7995, 0.7994,
                0.7976, 0.7977, 0.796, 0.7922, 0.7928, 0.7929, 0.7948, 0.797, 0.7953, 0.7907,
                0.7872, 0.7852, 0.7852, 0.786, 0.7862, 0.7836, 0.7837, 0.784, 0.7867, 0.7867,
                0.7869, 0.7837, 0.7827, 0.7825, 0.7779, 0.7791, 0.779, 0.7787, 0.78, 0.7807,
                0.7803, 0.7817, 0.7799, 0.7799, 0.7795, 0.7801, 0.7765, 0.7725, 0.7683, 0.7641,
                0.7639, 0.7616, 0.7608, 0.759, 0.7582, 0.7539, 0.75, 0.75, 0.7507, 0.7505,
                0.7516, 0.7522, 0.7531, 0.7577, 0.7577, 0.7582, 0.755, 0.7542, 0.7576, 0.7616,
                0.7648, 0.7648, 0.7641, 0.7614, 0.757, 0.7587, 0.7588, 0.762, 0.762, 0.7617,
                0.7618, 0.7615, 0.7612, 0.7596, 0.758, 0.758, 0.758, 0.7547, 0.7549, 0.7613,
                0.7655, 0.7693, 0.7694, 0.7688, 0.7678, 0.7708, 0.7727, 0.7749, 0.7741, 0.7741,
                0.7732, 0.7727, 0.7737, 0.7724, 0.7712, 0.772, 0.7721, 0.7717, 0.7704, 0.769,
                0.7711, 0.774, 0.7745, 0.7745, 0.774, 0.7716, 0.7713, 0.7678, 0.7688, 0.7718,
                0.7718, 0.7728, 0.7729, 0.7698, 0.7685, 0.7681, 0.769, 0.769, 0.7698, 0.7699,
                0.7651, 0.7613, 0.7616, 0.7614, 0.7614, 0.7607, 0.7602, 0.7611, 0.7622, 0.7615,
                0.7598, 0.7598, 0.7592, 0.7573, 0.7566, 0.7567, 0.7591, 0.7582, 0.7585, 0.7613,
                0.7631, 0.7615, 0.76, 0.7613, 0.7627, 0.7627, 0.7608, 0.7583, 0.7575, 0.7562,
                0.752, 0.7512, 0.7512, 0.7517, 0.752, 0.7511, 0.748, 0.7509, 0.7531, 0.7531,
                0.7527, 0.7498, 0.7493, 0.7504, 0.75, 0.7491, 0.7491, 0.7485, 0.7484, 0.7492,
                0.7471, 0.7459, 0.7477, 0.7477, 0.7483, 0.7458, 0.7448, 0.743, 0.7399, 0.7395,
                0.7395, 0.7378, 0.7382, 0.7362, 0.7355, 0.7348, 0.7361, 0.7361, 0.7365, 0.7362,
                0.7331, 0.7339, 0.7344, 0.7327, 0.7327, 0.7336, 0.7333, 0.7359, 0.7359, 0.7372,
                0.736, 0.736, 0.735, 0.7365, 0.7384, 0.7395, 0.7413, 0.7397, 0.7396, 0.7385,
                0.7378, 0.7366, 0.74, 0.7411, 0.7406, 0.7405, 0.7414, 0.7431, 0.7431, 0.7438,
                0.7443, 0.7443, 0.7443, 0.7434, 0.7429, 0.7442, 0.744, 0.7439, 0.7437, 0.7437,
                0.7429, 0.7403, 0.7399, 0.7418, 0.7468, 0.748, 0.748, 0.749, 0.7494, 0.7522,
                0.7515, 0.7502, 0.7472, 0.7472, 0.7462, 0.7455, 0.7449, 0.7467, 0.7458, 0.7427,
                0.7427, 0.743, 0.7429, 0.744, 0.743, 0.7422, 0.7388, 0.7388, 0.7369, 0.7345,
                0.7345, 0.7345, 0.7352, 0.7341, 0.7341, 0.734, 0.7324, 0.7272, 0.7264, 0.7255,
                0.7258, 0.7258, 0.7256, 0.7257, 0.7247, 0.7243, 0.7244, 0.7235, 0.7235, 0.7235,
                0.7235, 0.7262, 0.7288, 0.7301, 0.7337, 0.7337, 0.7324, 0.7297, 0.7317, 0.7315,
                0.7288, 0.7263, 0.7263, 0.7242, 0.7253, 0.7264, 0.727, 0.7312, 0.7305, 0.7305,
                0.7318, 0.7358, 0.7409, 0.7454, 0.7437, 0.7424, 0.7424, 0.7415, 0.7419, 0.7414,
                0.7377, 0.7355, 0.7315, 0.7315, 0.732, 0.7332, 0.7346, 0.7328, 0.7323, 0.734,
                0.734, 0.7336, 0.7351, 0.7346, 0.7321, 0.7294, 0.7266, 0.7266, 0.7254, 0.7242,
                0.7213, 0.7197, 0.7209, 0.721, 0.721, 0.721, 0.7209, 0.7159, 0.7133, 0.7105,
                0.7099, 0.7099, 0.7093, 0.7093, 0.7076, 0.707, 0.7049, 0.7012, 0.7011, 0.7019,
                0.7046, 0.7063, 0.7089, 0.7077, 0.7077, 0.7077, 0.7091, 0.7118, 0.7079, 0.7053,
                0.705, 0.7055, 0.7055, 0.7045, 0.7051, 0.7051, 0.7017, 0.7, 0.6995, 0.6994,
                0.7014, 0.7036, 0.7021, 0.7002, 0.6967, 0.695, 0.695, 0.6939, 0.694, 0.6922,
                0.6919, 0.6914, 0.6894, 0.6891, 0.6904, 0.689, 0.6834, 0.6823, 0.6807, 0.6815,
                0.6815, 0.6847, 0.6859, 0.6822, 0.6827, 0.6837, 0.6823, 0.6822, 0.6822, 0.6792,
                0.6746, 0.6735, 0.6731, 0.6742, 0.6744, 0.6739, 0.6731, 0.6761, 0.6761, 0.6785,
                0.6818, 0.6836, 0.6823, 0.6805, 0.6793, 0.6849, 0.6833, 0.6825, 0.6825, 0.6816,
                0.6799, 0.6813, 0.6809, 0.6868, 0.6933, 0.6933, 0.6945, 0.6944, 0.6946, 0.6964,
                0.6965, 0.6956, 0.6956, 0.695, 0.6948, 0.6928, 0.6887, 0.6824, 0.6794, 0.6794,
                0.6803, 0.6855, 0.6824, 0.6791, 0.6783, 0.6785, 0.6785, 0.6797, 0.68, 0.6803,
                0.6805, 0.676, 0.677, 0.677, 0.6736, 0.6726, 0.6764, 0.6821, 0.6831, 0.6842,
                0.6842, 0.6887, 0.6903, 0.6848, 0.6824, 0.6788, 0.6814, 0.6814, 0.6797, 0.6769,
                0.6765, 0.6733, 0.6729, 0.6758, 0.6758, 0.675, 0.678, 0.6833, 0.6856, 0.6903,
                0.6896, 0.6896, 0.6882, 0.6879, 0.6862, 0.6852, 0.6823, 0.6813, 0.6813, 0.6822,
                0.6802, 0.6802, 0.6784, 0.6748, 0.6747, 0.6747, 0.6748, 0.6733, 0.665, 0.6611,
                0.6583, 0.659, 0.659, 0.6581, 0.6578, 0.6574, 0.6532, 0.6502, 0.6514, 0.6514,
                0.6507, 0.651, 0.6489, 0.6424, 0.6406, 0.6382, 0.6382, 0.6341, 0.6344, 0.6378,
                0.6439, 0.6478, 0.6481, 0.6481, 0.6494, 0.6438, 0.6377, 0.6329, 0.6336, 0.6333,
                0.6333, 0.633, 0.6371, 0.6403, 0.6396, 0.6364, 0.6356, 0.6356, 0.6368, 0.6357,
                0.6354, 0.632, 0.6332, 0.6328, 0.6331, 0.6342, 0.6321, 0.6302, 0.6278, 0.6308,
                0.6324, 0.6324, 0.6307, 0.6277, 0.6269, 0.6335, 0.6392, 0.64, 0.6401, 0.6396,
                0.6407, 0.6423, 0.6429, 0.6472, 0.6485, 0.6486, 0.6467, 0.6444, 0.6467, 0.6509,
                0.6478, 0.6461, 0.6461, 0.6468, 0.6449, 0.647, 0.6461, 0.6452, 0.6422, 0.6422,
                0.6425, 0.6414, 0.6366, 0.6346, 0.635, 0.6346, 0.6346, 0.6343, 0.6346, 0.6379,
                0.6416, 0.6442, 0.6431, 0.6431, 0.6435, 0.644, 0.6473, 0.6469, 0.6386, 0.6356,
                0.634, 0.6346, 0.643, 0.6452, 0.6467, 0.6506, 0.6504, 0.6503, 0.6481, 0.6451,
                0.645, 0.6441, 0.6414, 0.6409, 0.6409, 0.6428, 0.6431, 0.6418, 0.6371, 0.6349,
                0.6333, 0.6334, 0.6338, 0.6342, 0.632, 0.6318, 0.637, 0.6368, 0.6368, 0.6383,
                0.6371, 0.6371, 0.6355, 0.632, 0.6277, 0.6276, 0.6291, 0.6274, 0.6293, 0.6311,
                0.631, 0.6312, 0.6312, 0.6304, 0.6294, 0.6348, 0.6378, 0.6368, 0.6368, 0.6368,
                0.636, 0.637, 0.6418, 0.6411, 0.6435, 0.6427, 0.6427, 0.6419, 0.6446, 0.6468,
                0.6487, 0.6594, 0.6666, 0.6666, 0.6678, 0.6712, 0.6705, 0.6718, 0.6784, 0.6811,
                0.6811, 0.6794, 0.6804, 0.6781, 0.6756, 0.6735, 0.6763, 0.6762, 0.6777, 0.6815,
                0.6802, 0.678, 0.6796, 0.6817, 0.6817, 0.6832, 0.6877, 0.6912, 0.6914, 0.7009,
                0.7012, 0.701, 0.7005, 0.7076, 0.7087, 0.717, 0.7105, 0.7031, 0.7029, 0.7006,
                0.7035, 0.7045, 0.6956, 0.6988, 0.6915, 0.6914, 0.6859, 0.6778, 0.6815, 0.6815,
                0.6843, 0.6846, 0.6846, 0.6923, 0.6997, 0.7098, 0.7188, 0.7232, 0.7262, 0.7266,
                0.7359, 0.7368, 0.7337, 0.7317, 0.7387, 0.7467, 0.7461, 0.7366, 0.7319, 0.7361,
                0.7437, 0.7432, 0.7461, 0.7461, 0.7454, 0.7549, 0.7742, 0.7801, 0.7903, 0.7876,
                0.7928, 0.7991, 0.8007, 0.7823, 0.7661, 0.785, 0.7863, 0.7862, 0.7821, 0.7858,
                0.7731, 0.7779, 0.7844, 0.7866, 0.7864, 0.7788, 0.7875, 0.7971, 0.8004, 0.7857,
                0.7932, 0.7938, 0.7927, 0.7918, 0.7919, 0.7989, 0.7988, 0.7949, 0.7948, 0.7882,
                0.7745, 0.771, 0.775, 0.7791, 0.7882, 0.7882, 0.7899, 0.7905, 0.7889, 0.7879,
                0.7855, 0.7866, 0.7865, 0.7795, 0.7758, 0.7717, 0.761, 0.7497, 0.7471, 0.7473,
                0.7407, 0.7288, 0.7074, 0.6927, 0.7083, 0.7191, 0.719, 0.7153, 0.7156, 0.7158,
                0.714, 0.7119, 0.7129, 0.7129, 0.7049, 0.7095
                ],
                detailChart;
           
                // create the detail chart
                function createDetail(masterChart) {
                        // prepare the detail chart
                        var detailData = [],
                            detailStart = Date.UTC(2008, 7, 1);
                        $.each(masterChart.series[0].data, function() {
                            if (this.x >= detailStart) {
                                detailData.push(this.y);
                            }
                        });
                        // create a detail chart referenced by a global variable
                        detailChart = $('#high-datamap').highcharts({
                            chart: {
                                type: 'spline',
                                backgroundColor: 'transparent',
                                reflow: true,
                                marginTop: 25,
                                marginBottom: 0,
                                marginLeft: 35,
                                marginRight: 5,
                                style: {
                                    position: 'absolute'
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            title: {
                                text: null
                            },
                            subtitle: {
                                text: null
                            },
                            xAxis: {
                                type: 'datetime',
                                minorTickLength: 0,
                                tickLength: 0,
                                gridLineWidth: 0,
                                lineWidth: 0,
                                lineColor: '#ddd',
                                labels: {
                                    enabled: false
                                },
                            },
                            yAxis: {
                                gridLineColor: '#EEE',
                                lineColor: '#EEE',
                                tickColor: '#EEE',
                                tickLength: 10,
                                showFirstLabel: false,
                                title: {
                                    text: null
                                },
                                labels: {
                                    x: -5
                                },
                                maxZoom: 0.1
                            },
                            tooltip: {
                                formatter: function() {
                                    var point = this.points[
                                        0];
                                    return '<b>' +
                                        point.series.name +
                                        '</b><br/>' +
                                        Highcharts.dateFormat(
                                            '%A %B %e %Y',
                                            this.x) +
                                        ':<br/>' +
                                        '1 USD = ' +
                                        Highcharts.numberFormat(
                                            point.y, 2) +
                                        ' EUR';
                                },
                                shared: true
                            },
                            legend: {
                                enabled: false
                            },
                            plotOptions: {
                                areaspline: {
                                    fillOpacity: 0.3,
                                    marker: {
                                        enabled: true,
                                        symbol: 'circle',
                                        radius: 2,
                                        states: {
                                            hover: {
                                                enabled: true
                                            }
                                        }
                                    }
                                },
                                series: {
                                    marker: {
                                        enabled: false,
                                        states: {
                                            hover: {
                                                enabled: true,
                                                radius: 3
                                            }
                                        }
                                    }
                                }
                            },
                            series: [{
                                name: 'USD to EUR',
                                pointStart: detailStart,
                                pointInterval: 24 *
                                    3600 * 1000,
                                data: detailData,
                            }],
                            exporting: {
                                enabled: false
                            }
                        }).highcharts(); // return chart
                    }
                    // create the sibling chart

                function createMaster() {
                        $('#high-siblingmap').highcharts({
                            chart: {
                                reflow: true,
                                backgroundColor: 'transparent',
                                marginLeft: 0,
                                marginRight: 0,
                                marginBottom: 30,
                                zoomType: 'x',
                                events: {
                                    // listen to the selection event on the sibling chart to update the
                                    // extremes of the detail chart
                                    selection: function(
                                        event) {
                                        var
                                            extremesObject =
                                            event.xAxis[
                                                0],
                                            min =
                                            extremesObject
                                            .min,
                                            max =
                                            extremesObject
                                            .max,
                                            detailData = [],
                                            xAxis =
                                            this.xAxis[
                                                0];
                                        // reverse engineer the last part of the data
                                        $.each(this.series[
                                                0].data,
                                            function() {
                                                if (
                                                    this
                                                    .x >
                                                    min &&
                                                    this
                                                    .x <
                                                    max
                                                ) {
                                                    detailData
                                                        .push(
                                                            [
                                                                this
                                                                .x,
                                                                this
                                                                .y
                                                            ]
                                                        );
                                                }
                                            });
                                        // move the plot bands to reflect the new detail span
                                        xAxis.removePlotBand(
                                            'mask-before'
                                        );
                                        xAxis.addPlotBand({
                                            id: 'mask-before',
                                            from: min,
                                            to: max,
                                            color: 'rgba(0, 0, 0, 0.05)',
                                            borderColor: 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                        });
                                        xAxis.removePlotBand(
                                            'mask-after'
                                        );
                                        detailChart.series[
                                            0].setData(
                                            detailData
                                        );
                                        return false;
                                    }
                                }
                            },
                            title: {
                                text: null
                            },
                            xAxis: {
                                type: 'datetime',
                                showLastTickLabel: true,
                                maxZoom: 14 * 24 * 3600000, // fourteen days
                                plotBands: [{
                                    id: 'mask-before',
                                    from: Date.UTC(
                                        2008, 0,
                                        1),
                                    to: Date.UTC(
                                        2008, 5,
                                        1),
                                    color: 'rgba(0, 0, 0, 0.05)',
                                }],
                                title: {
                                    text: null
                                },
                                showFirstLabel: false,
                                showLastLabel: false
                            },
                            yAxis: {
                                gridLineWidth: 0,
                                // gridLineColor: '#EEE',
                                labels: {
                                    enabled: false
                                },
                                title: {
                                    text: null
                                },
                                min: 0.6,
                                showFirstLabel: false
                            },
                            tooltip: {
                                formatter: function() {
                                    return false;
                                }
                            },
                            legend: {
                                enabled: false
                            },
                            credits: {
                                enabled: false
                            },
                            plotOptions: {
                                series: {
                                    fillColor: {
                                        linearGradient: [
                                            '0%', '0%',
                                            '100%',
                                            '0%'
                                        ], // Left Top Right Bot
                                        stops: [
                                            [0,
                                                'rgba(74,137,220, 0.75)'
                                            ],
                                            [1,
                                                'rgba(74,137,220, 0.1)'
                                            ],
                                        ]
                                    },
                                    lineWidth: 1,
                                    marker: {
                                        enabled: false
                                    },
                                    shadow: false,
                                    states: {
                                        hover: {
                                            lineWidth: 1
                                        }
                                    },
                                    enableMouseTracking: false
                                }
                            },
                            series: [{
                                type: 'area',
                                name: 'USD to EUR',
                                pointInterval: 24 *
                                    3600 * 1000,
                                pointStart: Date.UTC(
                                    2006, 0, 1),
                                data: data
                            }],
                            exporting: {
                                enabled: false
                            }
                        }, function(masterChart) {
                            createDetail(masterChart);
                        }).highcharts(); // return chart instance
                    }
            // create master and in its callback, create the detail chart
            createMaster();

        } // end HighChartsAdvanced   

	return {
        init: function () {

            // Init Demo Charts 
            demoSparklines();
            demoCircleGraphs();
            demoHighCharts();
            demoHighChartMenus();

            if ($('#high-datamap').length) { 
                demoHighChartsAdvanced();
            }

        }
	} 
}();










