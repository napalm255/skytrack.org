$(function () {
  $.getJSON( "service/weather.out.php", function( data ) {
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
          //categories: ['12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
          //    '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM']
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
          //categories: ['12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
          //    '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM']
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
