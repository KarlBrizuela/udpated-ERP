(function ($) {
    /* "use strict" */

    var dzChartlist = function () {

        var screenWidth = $(window).width();

        /* Stock Levels - Bar Chart (formerly chartTimeline) */
        var stockLevelsChart = function () {

            var options = {
                chart: {
                    type: "bar",
                    height: 200,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: "Stock Quantity",
                    data: [450, 320, 280, 190, 150]
                }],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: "55%",
                        borderRadius: 5
                    }
                },
                colors: ['#ff0000'],
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: false
                },
                xaxis: {
                    categories: [
                        "Electronics",
                        "Clothing",
                        "Home & Garden",
                        "Sports",
                        "Toys"
                    ],
                    labels: {
                        style: {
                            colors: '#787878',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    },
                    axisBorder: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#3e4954',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Units";
                        }
                    }
                }
            };

            var chartRender = new ApexCharts(document.querySelector("#chartTimeline"), options);
            chartRender.render();
        }


        /* Category Distribution - Donut Chart (formerly widgetChart1) */
        var categoryDistributionChart = function () {
            var options = {
                series: [42, 30, 18, 10],
                chart: {
                    height: 270,
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                labels: ['Electronics', 'Clothing', 'Home', 'Sports'],
                colors: ['#ff0000', '#3ECDFF', '#FFB930', '#FF6746'],
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    offsetY: -5,
                                },
                                value: {
                                    show: true,
                                    offsetY: 5,
                                    formatter: function (val) {
                                        return val + "%";
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#373d3f',
                                    formatter: function (w) {
                                        return "100%";
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    markers: {
                        width: 10,
                        height: 10,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#widgetChart1"), options);
            chart.render();
        }

        /* Inventory Turnover - Radial Chart (formerly radialChart) */
        var turnoverChart = function () {
            var options = {
                series: [75],
                chart: {
                    height: 230,
                    type: 'radialBar',
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            margin: 20,
                            size: '65%',
                            background: '#fff',
                            image: undefined,
                            position: 'front',
                            dropShadow: {
                                enabled: true,
                                top: 3,
                                left: 0,
                                blur: 10,
                                opacity: 0.1
                            }
                        },
                        track: {
                            background: '#F8F8F8',
                            strokeWidth: '100%',
                            margin: 0,
                        },
                        dataLabels: {
                            show: true,
                            name: {
                                offsetY: -10,
                                show: true,
                                color: '#888',
                                fontSize: '17px'
                            },
                            value: {
                                offsetY: 5,
                                color: '#111',
                                fontSize: '24px',
                                show: true,
                                formatter: function (val) {
                                    return val;
                                }
                            }
                        }
                    }
                },
                fill: {
                    colors: ['#FFB930'],
                },
                labels: ['Turnover Rate'],
            };

            var chart = new ApexCharts(document.querySelector("#radialChart"), options);
            chart.render();
        }


        /* Function ============ */
        return {
            init: function () {
            },


            load: function () {
                stockLevelsChart();
                categoryDistributionChart();
                turnoverChart();
            },

            resize: function () {
            }
        }

    }();

    jQuery(window).on('load', function () {
        setTimeout(function () {
            dzChartlist.load();
        }, 1000);

    });

})(jQuery);
