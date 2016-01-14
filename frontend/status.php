<?php
$screen_name = filter_input(INPUT_GET,'screen_name');
if (!preg_match("/^[a-zA-Z0-9_]+$/", $screen_name)) { exit; }
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Follow and Followers Daily Analytics</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
    <script src="http://code.highcharts.com/adapters/mootools-adapter.js"></script>
    <script src="http://code.highcharts.com/adapters/standalone-framework.js"></script>
</head>
<body>


<div id="container" style="width:100%; height:400px;"></div>

<script>
    function utc2dateString(utc_msec) {
        d=new Date(utc_msec);
        var month = d.getMonth() + 1;
        var day  = d.getDate();
        var hour = ( d.getHours()   < 10 ) ? '0' + d.getHours()   : d.getHours();
        var min  = ( d.getMinutes() < 10 ) ? '0' + d.getMinutes() : d.getMinutes();
        var sec   = ( d.getSeconds() < 10 ) ? '0' + d.getSeconds() : d.getSeconds();

        return (month + '/' + day + ' ' + hour + ':' + min + ':' + sec );
    }

    var chart;
    $(document).ready(function() {


        var options = {
            title: {
                text: 'Follow and Follower'
            },
            subtitle: {
                text: '-'
            },

            global : {
                useUTC : false
            },

            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%m月%e日%H時%M分 ', this.x) +
                        ': ' + this.y;
                }
            },


            chart: {
                renderTo: 'container',
                type: 'line'
            },

            xAxis: {
                type:"datetime",

                plotOptions: {  // プロットオプション
                    series: {
                        dataGrouping: {
                            dateTimeLabelFormats: {
                                millisecond: ['%Y/%m/%d %H:%M:%S.%L', '%Y/%m/%d %H:%M:%S.%L', '-%H:%M:%S.%L'],
                                second: ['%Y/%m/%d %H:%M:%S', '%Y/%m/%d %H:%M:%S', '-%H:%M:%S'],
                                minute: ['%Y/%m/%d %H:%M', '%Y/%m/%d %H:%M', '-%H:%M'],
                                hour: ['%Y/%m/%d %H:%M', '%Y/%m/%d %H:%M', '-%H:%M'],
                                day: ['%Y/%m/%d', '%Y/%m/%d', '-%Y/%m/%d'],
                                week: ['%Y/%m/%d', '%Y/%m/%d', '-%Y/%m/%d'],
                                month: ['%B %Y', '%B', '-%B %Y'],
                                year: ['%Y', '%Y', '-%Y']
                            }
                        }
                    }
                },


                labels: {
                    formatter: function(){ return utc2dateString(this.value); }
                }

                //tickInterval: 24 * 3600 * 1000,

            },
            yAxis: {
            },
            series: [{
                name: 'Follower',
                data: []
            }, {
                name: 'Follow',
                data: []
            }]
        };
        $.getJSON('api.php?screen_name=<?php echo $screen_name; ?>', function(json) {

            val1 = [];
            val2 = [];
            console.log(json);

            if(json.code == null ){ alert(" server error "); }
            switch(json.code) {

                case 200:
                    $.each(json.data, function (key, value) {
                        val1.push([value['fetched_date'], value['follower']]);
                        val2.push([value['fetched_date'], value['following']]);
                    });

                    //options.title = "hello :)";
                    options.title['text'] = options.title['text'] + " (@" + json.screen_name + ")"
                    options.subtitle['text'] = json.between['A'] + " ~ " + json.between['B'];
                    options.series[0].data = val1;
                    options.series[1].data = val2;
                    chart = new Highcharts.Chart(options);
                    Highcharts.setOptions({
                      global: {
                        useUTC: false
                      }
                    })

                    break;

                case 403:
                    $('#container').text('Not found data');

                    break;

                case 500:

                    $('#container').text('Failed to get data');

                    break;

            }


        });
    });
</script>



</body>
</html>
