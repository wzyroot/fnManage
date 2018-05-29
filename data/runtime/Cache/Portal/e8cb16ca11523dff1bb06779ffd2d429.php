<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>富农管理</title>
    <link rel="stylesheet" href="/themes/simplebootx/Public/assets/css/weui/weuix.min.css">
    <link href="/themes/simplebootx/Public/assets/css/weui/weui2.css" rel="stylesheet">
    <link href="/themes/simplebootx/Public/assets/css/weui/weui3.css" rel="stylesheet">
    <link rel="stylesheet" href="/themes/simplebootx/Public/assets/css/commen.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
    
    <style>
        *{
            padding: 0;
            margin: 0;
        }
        body{
            background-color: #fff;
        }
        .weui-header .weui-header-title, .weui-header h1{
            color:#000;
        }
        .weui-header .weui-header-left, .weui-header .weui-header-right{
            color:#666;
        }
        .f-white {
            color: #000 !important;
        }
        .bg-orange {
            background-color: white;
            border-bottom:1px solid #ccc;
        }
        .bg-orange:not(.weui_btn_disabled):active {
            color: rgba(255, 255, 255, 0.4);
            background-color: #fff;
        }
        .search-select{
            /* height: 8%; */
            border-bottom:1px solid #ccc;
        }
        .num-money{
            border-bottom:1px solid #ccc;
        }
        .num-money li{
            float:left;
            margin:1.2rem auto;
            width: 49%;
            text-align: center;
            list-style: none;            
        }
        .num-money li:first-child{
            /*border-right:1px solid #ccc;*/
        }
        .num-money li p:first-child{
            font-size: 1.2rem;
        }
        .num-money li p:nth-child(2){
            font-size: 0.8rem;
            color:#666;
        }
        .datenum{
            padding:5% 0 3% 0;
            height: 8%;
        }
        .datenum li{
            width: 33.3%;
            height: 80%;
            float:left;
            list-style: none;
        }
        .red{
            border-color: red;
        }
        .datenum a{
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            display:inline-block;
            width: 83%;
            height: 100%;
            color:black;
            background-color: #fff;
            border:1px solid #ccc;
            border-radius: 10px;
            text-align: center;
            margin-left:8.5%;
            padding-top:5%;
        }
        .weui-table{
            width: 94%;
            border:1px solid #ccc;
            margin:0 auto;
        }
        input{
            text-align: center;
            height: 1rem;
        }
        .datenum a.red{
            border-color:red;
        }
        .weui_cell{
            position: relative;
        }
        .weui_cell i{
            position: absolute;
            right:30%;
            top:22%;
        }
        .timebox{
            height: 9%;
            display:none;
            margin-bottom:0.5rem;
        }
        .timepicker{
            border-radius: 8px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            padding-bottom:2%;
            padding-top:2%;
            height: 75%;
            width: 90%;
            margin:0 auto;
            background-color: #eee;
            padding-left:6%;
        }
        .weui_cell_primary{
            background-color: #fff;
            width: 38%;
            height: 70%;
            border-radius: 6px;
        }
        .timebox i{
            margin-left:5%;
        }
        i.icon.icon-prev {
            background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%2015%2015'%3E%3Cg%3E%3Cpath%20fill%3D'%2304BE02'%20d%3D'M14%2C1.6v11.8L2.2%2C7.6L14%2C1.6%20M15%2C0L0%2C7.6L15%2C15V0L15%2C0z'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E")
        }
        i.icon.icon-next{
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%2015%2015'%3E%3Cg%3E%3Cpath%20fill%3D'%2304BE02'%20d%3D'M1%2C1.6l11.8%2C5.8L1%2C13.4V1.6%20M0%2C0v15l15-7.6L0%2C0L0%2C0z'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E")
        }
        i.icon.icon-next, i.icon.icon-prev {
            width: 0.75rem;
            height: 0.75rem;
        }

        i.icon.icon-next, i.icon.icon-prev {
            display: inline-block;
            vertical-align: middle;
            background-size: 100% auto;
            background-position: center;
            background-repeat: no-repeat;
            font-style: normal;
            position: relative;
        }
        .day {
                padding-bottom: 0.7rem;
                padding-top: 0.7rem;
                padding-left:0.5rem;
                padding-right:0.5rem;
            }
            
            .day li {
                width: 33.33%;
                float: left;
                text-align: center;
            }
            
            .day li a {
                width: 4.3rem;
                margin: 0 auto;
                display: block;
                border: 1.3px solid #ccc;
                border-radius: 0.3rem;
                padding: 2px 0;
                color: #ccc;
            }
            
            .red {
                background-color: #27c674 !important;
                color: #fff !important;
                border: 1.3px solid #27c674;
            }

            .num-money{
                padding:0 2% 0 3%;
                border-bottom:1px solid #ccc;
            }
            .num-money li{
                float:left;
                margin:1rem auto;
                width: 24%;
                text-align: center;
                list-style: none;            
            }
            .num-money span{
                margin-top:1rem;
                display: inline-block;
                padding:0.1rem 0.2rem 0.1rem 0.3rem;
                color:#fff;
                background-color: #ef4f4f;
                border-radius: 0.3rem;
                letter-spacing: 0.1rem;
            }
            .num-money .all{
                background-color: #27c674;
            }
            .num-money li p:first-child{
                font-size: 1.1rem;
            }
            .num-money li p:nth-child(2){
                font-size: 0.8rem;
                color:#666;
            }
            .user-table-head{
                box-sizing: border-box;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                width: 100%;
                height: 2.5rem;
                background-color: #efefef;
                padding:0 1rem 0 1rem;
            }
            .user-table-head li{
                box-sizing: border-box;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                width: 25%;
                text-align:center;
                line-height: 2.5rem;
                color:#868686;
            }
            .user-table-body{
                box-sizing: border-box;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                width: 100%;
                height: 3.2rem;
                padding:0 1rem 0 1rem;
            }
            .user-table-body li{
                box-sizing: border-box;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                /*width: 25%;*/
                text-align:center;
                line-height: 3.2rem;
                border-bottom:1px solid #ccc;
            }
            .user-table-body li:first-child{
                width: 31%;
            }
            .user-table-body li:nth-child(2){
                width: 23%;
            }
            .user-table-body li:nth-child(3){
                width: 23%;
            }
            .user-table-body li:nth-child(4){
                width: 23%;
            }
            .user-table-head li:first-child{
                width: 31%;
            }
            .user-table-head li:nth-child(2){
                width: 23%;
            }
            .user-table-head li:nth-child(3){
                width: 23%;
            }
            .user-table-head li:nth-child(4){
                width: 23%;
            }
    </style>
</head>
<body>
     <div class="bg-orange weui-header "> 
        <div class="weui-header-left"><a class="icon icon-95 f-white" href="javascript:history.go(-1)">每日用户注册数统计</a></div>
        <h1 class="weui-header-title"></h1>
    </div>
    <!-- <div class="search-select">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" value="全部用户" id="cate" readonly="" data-values="0">
            </div>
        </div>
    </div> -->
        <input class="" type="hidden" value="7" id="daysval">
        <!-- <ul class="num-money clearfix">
            <li><p class="todayadd"></p><p>今日新增用户数</p></li>
            <li><p class="allcount"></p><p>用户总数</p></li>
        </ul> -->
        <ul class="num-money clearfix">
        <li><span>今日</span></li>
        <li><p class="todaywx"></p ><p>微信</p ></li>
        <li><p class="todayap"></p><p>App</p ></li>
        <li><p class="todayall"></p><p>用户总数</p ></li>
        </ul>
        <ul class="num-money clearfix"> 
        <li><span class="all">累计</span></li>
        <li><p class="allwx"></p><p>微信</p ></li>
        <li><p class="allap"></p><p>App</p ></li>
        <li><p class="allcount"></p><p>用户总数</p ></li>
        </ul>
    
        <div class="marketing-content">
            <div class="day">
                <ul id="datenum" class="clearfix timeselect">
                    <li>
                        <a class="red" href="javascript:void(0)">七天</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">30天</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">自定义</a>
                    </li>
                </ul>
        </div>
        <div class="timebox">
                <div class="timepicker clearfix">
                    <div class="weui_cell_bd weui_cell_primary fl">
                        <input class="weui_input" type="text" id="datetime-picker1">    
                    </div>
                    <span class="fl">&nbsp; 至 &nbsp;</span>
                    <div class="weui_cell_bd weui_cell_primary fl">
                        <input class="weui_input" type="text" id="datetime-picker2">   
                    </div>
                    <i class="icon icon-4"></i>
                </div>
            </div>
        </div>
        <!-- <table class="weui-table weui-border-tb">
                <thead>
                <tr><th>日期</th><th>每日新注册数</th><th>用户总数</th></tr>
                </thead>
                <tbody id="chart">

                </tbody>
            </table> -->
            <div>
                <ul class="user-table-head">
                <li class="fl">日期</li>
                <li class="fl">微信注册</li>
                <li class="fl">app注册</li>
                <li class="fl">用户总数</li>
                </ul>
                <div id="chart">

                </div>
                
            </div>
            <!-- <div>
                <ul class="user-table-head">
                    <li class="fl">日期</li>
                    <li class="fl">新注册数</li>
                    <li class="fl">用户总数</li>
                    <li class="fl">新注册数</li>
                </ul>
                <ul id="chart" class="user-table-body">
                    
                </ul>
            </div> -->
    
        <script src="/themes/simplebootx/Public/assets/js/datajs/zepto.min.js"></script>
		<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
		<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
        <script src="/themes/simplebootx/Public/assets/js/datajs/select.js"></script>
        <script src="/themes/simplebootx/Public/assets/js/weui/updown.js"></script>
		<script>
            getinfo(7)
			$('.icon-4').on('click',function(){
                var days = getdate()
                console.log(days)
                if (days !== undefined) {
                    $("#daysval").val(days)
                    var start = new Date($("#datetime-picker2").val());
                    var end_time = start/1000
                    console.log(end_time)
                    getinfo(days,end_time)
                }
            })
            function getdate(){
				var date1 = new Date($("#datetime-picker1").val());
				var date2 = new Date($("#datetime-picker2").val());
				console.log(date2.getTime())
				console.log(date1.getTime())
				if(date2.getTime()<date1.getTime()){
                    // alert('结束日期必须大于开始日期')
                    $.toast("开始时间不得大于结束时间", "text");
                    // $.toast("结束日期必须大于开始日期", "forbidden");
                    //  $.toast("禁止操作", "forbidden");
                    return
				}
				if(date2.getTime()>=date1.getTime()){
					if(date2.getTime()-date1.getTime() > 60*24*60*60*1000){
                        // alert('日期差必须小于60天')
                        $.toast("日期差必须小于60天", "text");
                        return
				    }
				}
                var days = (date2.getTime() - date1.getTime())/(24*60*60*1000)+1
                return days
			}
			$("#datetime-picker1").calendar({
				minDate:'2015-01-01',
                dateFormat:'yyyy-mm-dd',
                maxDate:new Date().toString()
			});
			
			$("#datetime-picker2").calendar({
				minDate:'2015-01-01',
                dateFormat:'yyyy-mm-dd',
                maxDate:new Date().toString()
			});

            $(".timeselect").on('click','li',function(){
                $(this).find('a').addClass('red');
                $(this).siblings().find('a').removeClass('red');
                if($(this).index() == 2){
                    $(".timebox").show();
                }else{
                    $(".timebox").hide();
                }
                if ($(this).index() == 0) {
                    $("#daysval").val(7)
                    getinfo(7)
                }
                if ($(this).index() == 1) {
                    $("#daysval").val(30)
                    getinfo(30)
                }
            })

            function toMoney(num){
                // num = num.toFixed(2);
                num = parseFloat(num)
                num = num.toLocaleString();
                return num;//返回的是字符串23,245.12保留2位小数
            }
            //获取数据
            function getinfo(days,start){
                console.log(days)
                var end_time = ''
                if (start != undefined) {
                    end_time = start
                }
                
                // console.log(info)
                // console.log(source)
                $.ajax({
                    type: 'POST',
                    url: 'index.php?g=portal&m=database&a=datainfo',
                    data:{'days':days,'end_time':end_time},
                    success: function(data) {
                        console.log(data)
                        if (data['daily_count'] != undefined) {
                            console.log(data)
                            var arrlen = data['daily_count'].length
                            $(".todaywx").text("")
                            $(".todayap").text("")
                            $(".todayall").text("")
                            $(".allwx").text("")
                            $(".allap").text("")
                            $(".allcount").text("")

                            $(".todaywx").append(data['count']['new_wechat_user'])
                            $(".todayap").append(data['count']['new_android_user'])
                            $(".todayall").append(data['count']['new_user'])
                            $(".allwx").append(data['count']['all_wechat_user'])
                            $(".allap").append(data['count']['all_android_user'])
                            $(".allcount").append(data['count']['all_user'])
                            
                            var res = ''
                            for (var i = arrlen-1; i >= 0; i--) {
                                res += '<ul class="user-table-body"><li class="fl" style="">'+data['daily_count'][i].time+'</li><li class="fl">'+data['daily_count'][i]['daily_add'].wechat+'</li><li class="fl">'+toMoney(data['daily_count'][i]['daily_add'].android)+'</li><li class="fl">'+data['daily_count'][i]['daily_add'].all+'</li></ul>'
                
                
                            }
                            $("#chart").text("")
                            $("#chart").html(res)
                        }else{
                            $("#chart").text("")
                            $(".todaywx").text("0")
                            $(".todayap").text("0")
                            $(".todayall").text("0")
                            $(".allwx").text("0")
                            $(".allap").text("0")
                            $(".allcount").text("0")
                        }
                        
                        
                    },
                    error: function(xhr, type) {
                       console.log('获取失败')
                    }
                });
            }
            $("#cate").select({
                title: "选择品种",
                // items: ["全部用户", "微信", "app"],
                items: [
                    {
                      title: "全部用户",
                      value: 0,
                    },
                    {
                      title: "微信",
                      value: 1,
                    },
                    {
                      title: "app",
                      value: 2,
                    }
                ],
                onChange: function(d){
                    console.log(d.values);
                    var day = $("#daysval").val()
                    getinfo(day)
                },
            });
            // $(".timeselect").on('click','li',function(){
            //     $(this).find('a').addClass('red');
            //     $(this).siblings().find('a').removeClass('red');
            //     if($(this).index() == 2){
            //         $(".timebox").show();
            //     }else{
            //         $(".timebox").hide();
            //     }
            // })
        </script>
</body>
</html>