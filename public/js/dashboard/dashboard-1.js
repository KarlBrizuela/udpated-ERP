(function ($) {
	/* "use strict" */


	var dzChartlist = function () {

		var screenWidth = $(window).width();
		var chartTimeline = function () {

			var optionsTimeline = {
				chart: {
					type: "bar",
					height: 200,
					toolbar: {
						show: false
					}
				},
				series: [{
					name: "Revenue",
					data: [180000, 145000, 120000, 98000, 75000]
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
						"Product A",
						"Product B",
						"Product C",
						"Product D",
						"Product E"
					],
					labels: {
						formatter: function (val) {
							return "$" + (val / 1000) + "k";
						},
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
							return "$" + val.toLocaleString();
						}
					}
				}
			};

			var chartTimelineRender =
				new ApexCharts(document.querySelector("#chartTimeline"), optionsTimeline);

			chartTimelineRender.render();
		}


		var widgetChart1 = function () {
			var options = {
				series: [{
					name: "Sales",
					data: [30000, 40000, 30000, 50000, 40000]
				}],
				chart: {
					height: 270,
					type: 'line',
					zoom: {
						enabled: true
					},
					toolbar: {
						show: false
					}
				},
				dataLabels: {
					enabled: false
				},
				stroke: {
					curve: 'smooth'
				},
				colors: ['#ff0000'],
				title: {
					text: undefined,
					align: 'left'
				},
				grid: {
					strokeDashArray: 5,
					row: {
						colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
						opacity: 0
					},
				},
				yaxis: {
					show: false,
				},
				xaxis: {
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
					axisBorder: {
						show: false
					},
					axisTicks: {
						show: false
					},
					labels: {
						style: {
							colors: '#828690',
							fontSize: '14px',
							fontFamily: 'Poppins',
							fontWeight: 100,

						}
					}
				}
			};

			var chart = new ApexCharts(document.querySelector("#widgetChart1"), options);
			chart.render();
		}

		var radialChart = function () {
			var options = {
				series: [60],
				chart: {
					height: 230,
					type: 'radialBar',
					toolbar: {
						show: false
					}
				},
				plotOptions: {
					radialBar: {
						/* hollow: {
						 margin: 0,
						 size: '70%',
						 background: '#fff',
						 image: undefined,
						 imageOffsetX: 0,
						 imageOffsetY: 0,
						 position: 'front',
					   }, */
						hollow: {
							margin: 20,
							size: '65%',
							background: '#fff',
							image: undefined,
							imageOffsetX: 0,
							imageOffsetY: 0,
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
							margin: 0, // margin is in pixels
						},

						dataLabels: {
							show: true,
							value: {
								offsetY: -7,
								color: '#111',
								fontSize: '20px',
								show: true,
							}
						}
					}
				},
				fill: {
					colors: ['#ff0000'],
				},
				stroke: {
				},
				labels: [''],
			};

			var chart = new ApexCharts(document.querySelector("#radialChart"), options);
			chart.render();
		}

		var widgetChart2 = function () {
			var options = {
				series: [
					{
						name: 'Net Profit',
						data: [500, 500, 400, 400, 600, 600, 300, 300, 500, 500, 700, 700],
						//radius: 12,	
					},
				],
				chart: {
					type: 'area',
					height: 80,
					toolbar: {
						show: false,
					},
					zoom: {
						enabled: false
					},
					sparkline: {
						enabled: true
					}

				},

				colors: ['#ff0000'],
				dataLabels: {
					enabled: false,
				},
				markers: {
					shape: "circle",
				},

				legend: {
					show: false,
				},
				stroke: {
					show: true,
					width: 4,
					curve: 'smooth',
					colors: ['#ff0000'],
				},

				grid: {
					show: false,
					borderColor: '#eee',
					padding: {
						top: 0,
						right: 0,
						bottom: 0,
						left: 0

					}
				},
				states: {
					normal: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					hover: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					active: {
						allowMultipleDataPointsSelection: false,
						filter: {
							type: 'none',
							value: 0
						}
					}
				},
				xaxis: {
					categories: ['Jan', 'feb', 'Mar', 'Apr', 'May', 'Jun', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',],
					axisBorder: {
						show: false,
					},
					axisTicks: {
						show: false
					},
					labels: {
						show: false,
						style: {
							fontSize: '12px',
						}
					},
					crosshairs: {
						show: false,
						position: 'front',
						stroke: {
							width: 1,
							dashArray: 3
						}
					},
					tooltip: {
						enabled: true,
						formatter: undefined,
						offsetY: 0,
						style: {
							fontSize: '12px',
						}
					}
				},
				yaxis: {
					show: false,
				},
				fill: {
					opacity: 0.0,
					type: 'solid',
					colors: '#FAC7B6'
				},
				tooltip: {
					style: {
						fontSize: '12px',
					},
					y: {
						formatter: function (val) {
							return "$" + val + " thousands"
						}
					}
				}
			};

			var chartBar1 = new ApexCharts(document.querySelector("#widgetChart2"), options);
			chartBar1.render();
		}

		var donutChart1 = function () {
			$("span.donut1").peity("donut", {
				width: "81",
				height: "81"
			});
		}

		/* Function ============ */
		return {
			init: function () {
			},


			load: function () {
				chartTimeline();
				widgetChart1();
				radialChart();
				widgetChart2();
				donutChart1();
			},

			resize: function () {
			}
		}

	}();

	jQuery(document).ready(function () {
	});

	jQuery(window).on('load', function () {
		setTimeout(function () {
			dzChartlist.load();
		}, 1000);

	});

	jQuery(window).on('resize', function () {


	});

})(jQuery);