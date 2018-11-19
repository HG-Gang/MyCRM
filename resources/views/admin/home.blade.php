<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>帕达控股-后台管理中心</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="content-type" content="no-cache, must-revalidate" />
    <meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
    <script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin/lib/echarts/3.4.0/echarts.common.min.js') }}?ver={{ resource_version_number() }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}?ver={{ resource_version_number() }}"/>
    <link rel="stylesheet" href="{{ URL::asset('ico/demo-files/demo.css') }}?ver={{ resource_version_number() }}"/>
</head>
<style>
    @font-face {
        font-family: 'icomoon';
        src:  url('/ico/fonts/icomoon.eot?ceksse');
        src:  url('/ico/fonts/icomoon.eot?ceksse#iefix') format('embedded-opentype'),
        url('/ico/fonts/icomoon.ttf?ceksse') format('truetype'),
        url('/ico/fonts/icomoon.woff?ceksse') format('woff'),
        url('/ico/fonts/icomoon.svg?ceksse#icomoon') format('svg');
        font-weight: normal;
        font-style: normal;
    }
</style>
<body style="height: 99%; width: 100%; background: #f1f2f7;">

<div class="home_top clearfix ">
    <div class="home_list_left">
        <div class="row">
            <div class=" col-md-3 ">
                <div class="border_row">
                    <p>
                        <span ><i class="icon-ico-dls"></i>代理商总数</span>
                        <span >{{ $data['agentsTotal'] }}</span>
                    </p>
                    <p>
                        <span><i class="icon-ico-rz"></i>已认证</span>
                        <span>{{ $data['agentsAuth'] }}</span>
                    </p>
                </div>

            </div>
            <div class=" col-md-3">
                <div class="border_row">
                    <p>
                        <span ><i class="icon-ico-kh"></i>普通客户总数</span>
                         <span>{{ $data['userTotal'] }}</span>
                        
                    </p>
                    <p>
                        <span><i class="icon-ico-rz"></i>已认证</span>
                        <span>{{ $data['userAuth'] }}</span>
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border_row">
                    <p>
                        <span><i class="icon-ico-zc"></i>共注册</span>
                        <span>{{ $data['allTotal'] }}</span>
                    </p>
                    <p>
                        <span><i class="icon-ico-rz"></i>共认证</span>
                        <span>{{ $data['authTotal'] }}</span>
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border_row">
                    <!-- <p>
                        <span ><i class="icon-ico-hysh"></i>待审核人数</span>
                        <span>11111</span>
                    </p> -->
                      <p>
                        <span id="min_odd"><i class="icon-ico-hysh"></i>待审核人数</span>
                        <span id="min_evens">{{ $data['pendingTotal'] }}</span>
                    </p>
                    <!-- <p>
                        <span><i class="icon-ico-rz"></i>无无无</span>
                        <span>11111</span>
                    </p> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="border_row">
                    <p>
                        <span id="min_odd"><i class="icon-ico-sr"></i>今日入金</span>
                        <span id="min_evens">{{ $data['todayDepWithdraw']['todaydep'] }} 万</span>
                    </p>
                    <!-- <p>
                        <span><i class="icon-ico-rz"></i>无无无</span>
                        <span>11111</span>

                    </p> -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="border_row">
                    <p>
                        <span id="min_odd"> <i class="icon-ico-cj"></i>今日出金</span>
                        <span id="min_evens">{{ $data['todayDepWithdraw']['todaywithdraw'] }} 万</span>
                    </p>
                    <!-- <p>
                        <span><i class="icon-ico-rz"></i>无无无</span>
                        <span>11111</span>
                    </p> -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="border_row">
                    <p>
                        <span><i class="icon-ico-sr"></i>累计入金</span>
                        <span>{{ $data['depositTotal'] }} 万</span>
                    </p>
                    <p>
                        <span><i class="icon-ico-cj"></i>累计出金</span>
                        <span>{{ $data['withdrawTotal'] }} 万</span>
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border_row">
                    {{--<p>
                        <span><i class="icon-ico-sr"></i>今日返佣</span>
                        <span>{{ $data['todayFanYong'] }} 万</span>
                    </p>
                    <p>
                        <span><i class="icon-ico-cj"></i>累计返佣</span>
                        <span>{{ $data['rebateTotal'] }} 万</span>
                    </p>--}}
                    <p>
                        <span id="min_odd"><i class="icon-ico-sr"></i>累计返佣</span>
                        <span id="min_evens">{{ $data['rebateTotal'] }} 万</span>
                    </p>
                    {{--<p>
                        <span><i class="icon-ico-rz"></i>无无无</span>
                        <span>11111</span>
                    </p>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="home_list_rigth">
        <div class="row">
            <div class="col-md-12">
                <div class="Img_ridov">
                    <img src="{{ URL::asset('img/timg.jpg') }}?ver={{ resource_version_number() }}" alt="">
                </div>
                <div class="Img_text">
                    <p>
                        <span>用户名：{{ $user['username'] }}</span>
                        <span>用户组：{{ $role }}</span>
                    </p>
                    {{--<p class="mt">
                        <span class="ml20">上次登录时间：</span>
                        <input></input>
                    </p>--}}
                    <p>
                        <span class="ml20">登录IP：</span>
                        <input id="loginIp" value="{{ $loginIP }}"></input>
                    </p>
                    <p>

                        <span class="ml20">注册时间：</span>
                        <input id="recDate" value="{{ $user['created_at'] }}"></input>
                    </p>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="border_row">
                    <p>
                        <span><i class="icon-ico-sr"></i>今日持仓笔数 / 单量</span>
                        <span>{{ $data['todayVol']['openCount'] }} 笔 / {{ $data['todayVol']['openVol'] }} 手</span>

                    </p>
                    <p>
                        <span> <i class="icon-ico-sr"></i>昨日持仓单量</span>
                        <span>{{ $data['ytdayVol']['openVol'] }} 手</span>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border_row">
                    <p>
                        <span> <i class="icon-ico-kc"></i>今日平仓笔数 / 单量</span>
                        <span>{{ $data['todayVol']['closeCount'] }} 笔 / {{ $data['todayVol']['closeVol'] }} 手</span>

                    </p>
                    <p>
                        <span><i class="icon-ico-kc"></i> 昨日平仓单量</span>
                        <span>{{ $data['ytdayVol']['closeVol'] }} 手</span>

                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border_row">
                    <p>
                        <span><i class="icon-ico-pc"></i>累计平仓单量</span>
                        <span>{{ $data['volTotal']['closeVolTotal'] }} 手</span>

                    </p>
                    <p>
                        <span><i class="icon-ico-pc"></i>总持仓单量</span>
                        <span>{{ $data['volTotal']['openVolTotal'] }} 手</span>

                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="home_top_mt" >
    <div class="home_list_left_echart">
        <div id="main"></div>
    </div>
    <div class="home_list_rigth_echart">
        <div id="volum"></div>
    </div>
</div>


<!-- 
 -->
<script src="{{ URL::asset('js/formevent/form.core.js') }}?ver={{ resource_version_number() }}"></script>
<script>

   
    function dataGraphTableInit() {
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));
        var colors = ['#5793f3', '#d14a61', '#675bba'];
        var myoption = {
            color: colors,
            title: {
                text: "7日出入金及返佣概览",
                textStyle: {
                    fontSize: 12,
                },
            },
            tooltip: {
                trigger: "axis",
                axisPointer: {
                    type: "cross",
                },
            },
            grid: {
                show: true,
                right: '20%',
            },
            legend: {
                data:['出金','入金'/*, '返佣'*/],
            },
            xAxis: [
                {
                    type: "category",
                    axisTick: {
                        alignWithLabel: true
                    },
                    data: [
                        "{{ $days[6] }}" + "日",
                        "{{ $days[5] }}" + "日",
                        "{{ $days[4] }}" + "日",
                        "{{ $days[3] }}" + "日",
                        "{{ $days[2] }}" + "日",
                        "{{ $days[1] }}" + "日",
                        "{{ $days[0] }}" + "日",
                    ],
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: '出金',
                    min: 0,
                    max: 'dataMax',
                    position: 'left',
                    //min: 'dataMin',
                    // max: 'dataMax',
                    axisLine: {
                        lineStyle: {
                            color: colors[0]
                        }
                    },
                    axisLabel: {
                        formatter: '{value} 万'
                    }
                },
                {
                    type: 'value',
                    name: '入金',
                    min: 0,
                    max: 'dataMax',
                    //min: 'dataMin',
                    // max: 'dataMax',
                    position: 'right',
                    //offset: 10,
                    axisLine: {
                        lineStyle: {
                            color: colors[1]
                        }
                    },
                    axisLabel: {
                        formatter: '{value} 万'
                    }
                },
                /*{
                    type: 'value',
                    name: '返佣',
                    min: 0,
                    max: 'dataMax',
                    //min: 'dataMin',
                    // max: 'dataMax',
                    position: 'right',
                    offset: 60,
                    axisLine: {
                        lineStyle: {
                            color: colors[2]
                        }
                    },
                    axisLabel: {
                        formatter: '{value} 万'
                    }
                },*/
            ],
            series: [
                {
                    name:'出金',
                    type:'line',
                    data:[
                        "{{ $amount[6]['yuecjQK'] }}",
                        "{{ $amount[5]['yuecjQK'] }}",
                        "{{ $amount[4]['yuecjQK'] }}",
                        "{{ $amount[3]['yuecjQK'] }}",
                        "{{ $amount[2]['yuecjQK'] }}",
                        "{{ $amount[1]['yuecjQK'] }}",
                        "{{ $amount[0]['yuecjQK'] }}",
                    ],
                },
                {
                    name:'入金',
                    type:'line',
                    yAxisIndex: 1,
                    data:[
                        "{{ $amount[6]['yuerjCZ'] }}",
                        "{{ $amount[5]['yuerjCZ'] }}",
                        "{{ $amount[4]['yuerjCZ'] }}",
                        "{{ $amount[3]['yuerjCZ'] }}",
                        "{{ $amount[2]['yuerjCZ'] }}",
                        "{{ $amount[1]['yuerjCZ'] }}",
                        "{{ $amount[0]['yuerjCZ'] }}",
                    ],
                },
                /*{
                    name:'返佣',
                    type:'line',
                    yAxisIndex: 2,
                    data:[
                        "{{ $amount[6]['fanYong'] }}",
                        "{{ $amount[5]['fanYong'] }}",
                        "{{ $amount[4]['fanYong'] }}",
                        "{{ $amount[3]['fanYong'] }}",
                        "{{ $amount[2]['fanYong'] }}",
                        "{{ $amount[1]['fanYong'] }}",
                        "{{ $amount[0]['fanYong'] }}",
                    ],
                },*/
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(myoption);
    }

    function dataGraphTableInitVolum() {
        var myChart = echarts.init(document.getElementById('volum'));
        var colors = ['#5793f3', '#d14a61'];
        var myoption2 = {
            color: colors,
            title: {
                text: "7日持平仓单概览",
                textStyle: {
                    fontSize: 12,
                },
            },
            tooltip: {
                trigger: "axis",
                axisPointer: {
                    type: "cross",
                },
            },
            grid: {
                show: true,
            },
            legend: {
                data:['平仓','持仓'],
            },
            xAxis: [
                {
                    type: "category",
                    axisTick: {
                        alignWithLabel: true
                    },
                    data: [
                        "{{ $days[6] }}" + "日",
                        "{{ $days[5] }}" + "日",
                        "{{ $days[4] }}" + "日",
                        "{{ $days[3] }}" + "日",
                        "{{ $days[2] }}" + "日",
                        "{{ $days[1] }}" + "日",
                        "{{ $days[0] }}" + "日",
                    ],
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: '持 / 平仓单数',
                    min: 0,
                    max: 'dataMax',
                    axisLine: {
                        lineStyle: {
                            color: colors[0]
                        }
                    },
                    axisLabel: {
                        formatter: '{value} 手'
                    }
                },
            ],
            series: [
                {
                    name:'平仓',
                    type:'line',
                    data:[
                        "{{ $amount[6]['close_volume'] }}",
                        "{{ $amount[5]['close_volume'] }}",
                        "{{ $amount[4]['close_volume'] }}",
                        "{{ $amount[3]['close_volume'] }}",
                        "{{ $amount[2]['close_volume'] }}",
                        "{{ $amount[1]['close_volume'] }}",
                        "{{ $amount[0]['close_volume'] }}",
                    ],
                },
                {
                    name:'持仓',
                    type:'line',
                    data:[
                        "{{ $amount[6]['open_volume'] }}",
                        "{{ $amount[5]['open_volume'] }}",
                        "{{ $amount[4]['open_volume'] }}",
                        "{{ $amount[3]['open_volume'] }}",
                        "{{ $amount[2]['open_volume'] }}",
                        "{{ $amount[1]['open_volume'] }}",
                        "{{ $amount[0]['open_volume'] }}",
                    ],
                },
            ]
        };
        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(myoption2);
    }

    dataGraphTableInit();
    dataGraphTableInitVolum();
</script>
</body>
</html>