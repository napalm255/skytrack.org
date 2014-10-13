$(function () {
  $.getJSON( "json/weather.json", function( data ) {
    $('#container_location').html(data.city + ', ' + data.state)
      .css('text-align', 'center')
      .css('font-family', "'Roboto', sans-serif")
      .css('font-weight', 'bold')
      .css('font-size', '20px')
      .css('background-color', '#c0c0c0')
      .css('padding', '2px')
      .css('width', '100%')
    ;
    $('#container_date').html(data.date)
      .css('text-align', 'center')
      .css('font-family', "'Roboto', sans-serif")
      .css('font-weight', 'bold')
      .css('background-color', '#c0c0c0')
      .css('padding', '2px')
      .css('width', '100%')
    ;
    $('#container_temp').highcharts({
      title: {
          text: 'Hourly Temperature',
          x: -20 //center
      },
      //subtitle: {
      //    text: data.city + ', ' + data.state + '<br>' + data.date,
      //    x: -20
      //},
      xAxis: {
          allowDecimals: false,
          title: {
              text: 'Hours'
          },
          categories: data.temp.categories
      },
      yAxis: {
          title: {
              text: 'Temperature (°F)'
          },
          plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
          }]
      },
      tooltip: {
          valueSuffix: '°F'
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle',
          borderWidth: 0
      },
      series: data.temp.hourly
    });

    $('#container_pop').highcharts({
      title: {
          text: 'Hourly Precipitation Percentage',
          x: -20 //center
      },
      //subtitle: {
      //    text: data.city + ', ' + data.state + '<br>' + data.date,
      //    x: -20
      //},
      xAxis: {
          allowDecimals: false,
          title: {
              text: 'Hours'
          },
          categories: data.pop.categories
      },
      yAxis: {
          title: {
              text: 'Percent Chance of Precipitation (%)'
          },
          plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
          }]
      },
      tooltip: {
          valueSuffix: '%'
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle',
          borderWidth: 0
      },
      series: data.pop.hourly
    });
  });
});
