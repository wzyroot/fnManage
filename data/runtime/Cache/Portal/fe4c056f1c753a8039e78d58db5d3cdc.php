<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>富农管理</title>
    <link rel="stylesheet" href="/themes/simplebootx/Public/assets/css/weui/weuix.min.css">
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
            border-right:1px solid #ccc;
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
            /*height: 70%;*/
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
            width: 33.3%;
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
            width: 33.3%;
            text-align:center;
            line-height: 3.2rem;
            border-bottom:1px solid #ccc;
        }
        .user-table-head li:first-child{
           padding-left:1rem;
        }
        .user-table-body li:first-child{
           /*padding-left:1rem;*/
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
    </style>
</head>
<body>
     <div class="bg-orange weui-header "> 
        <div class="weui-header-left"><a class="icon icon-95 f-white" href="javascript:history.go(-1)">销售开单统计</a></div>
        <h1 class="weui-header-title"></h1>
    </div>
    <div class="search-select">
        <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" value="全部品种" id='text2'/>
                </div>
        </div>
    </div>
        <input class="" type="hidden" value="7" id="daysval">
        <input class="" type="hidden" value="" id="selectoption">
        <ul class="num-money clearfix">
            <li><p class="allsale"></p><p>销售开单数</p></li>
            <li><p class="allmoney"></p><p>开单金额</p></li>
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
            <div>
                <ul class="user-table-head">
                    <li class="fl">日期</li>
                    <li class="fl">开单数</li>
                    <li class="fl">开单金额</li>
                </ul>
                <ul id="chart" class="user-table-body">
                    
                </ul>
            </div>
            <script src="/themes/simplebootx/Public/assets/js/datajs/zepto.min.js"></script>
            <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
            <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
            <script src="/themes/simplebootx/Public/assets/js/datajs/select.js"></script>
            <script>
                getinfo(7)//获取数据
                $('.icon-4').on('click',function(){
                    var days = getdate()
                    console.log(days)
                    if (days !== undefined) {
                        $("#daysval").val(days)
                        var start = new Date($("#datetime-picker2").val());
                        var end_time = start/1000
                        console.log(end_time)
                        getinfo(days,end_time)//获取数据
                    }
                })
                function getdate(){
    				var date1 = new Date($("#datetime-picker1").val());
    				var date2 = new Date($("#datetime-picker2").val());
    				console.log(date2.getTime())
    				console.log(date1.getTime())
    				if(date2.getTime()<date1.getTime()){
                        $.toast("开始时间不得大于结束时间", "text");
                        return
    				}
    				if(date2.getTime()>=date1.getTime()){
    					if(date2.getTime()-date1.getTime() > 60*24*60*60*1000){
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
        

                // $("#text2").picker({
                //     title: "选择品种",
                //     toolbarCloseText:'确定',
                //     cols: [
                //     {
                //         textAlign: 'center',
                //         values: ["全部品种", "南通一德", "南通嘉吉", "南通来宝", "泰州汇福", "泰州益海", "泰州振华", "泰州五岳", "张家港四海", "张家港达孚", "日照恒隆", "日照凌云海", "日照邦基", "济宁嘉冠", "天津邦基", "武汉中海", "北京汇福"],
                //         displayValues:["全部品种", "南通一德", "南通嘉吉", "南通来宝", "泰州汇福", "泰州益海", "泰州振华", "泰州五岳", "张家港四海", "张家港达孚", "日照恒隆", "日照凌云海", "日照邦基", "济宁嘉冠", "天津邦基", "武汉中海", "北京汇福"],
                //     }
                //     ],
                //     onChange: function(d){
                //         console.log(d.values);
                //         var day = $("#daysval").val()
                //         getinfo(day)//获取数据
                //     },
                // });
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
                        getinfo(7)//获取数据
                    }
                    if ($(this).index() == 1) {
                        $("#daysval").val(30)
                        getinfo(30)//获取数据
                    }
                })

                function toMoney(num){
                    // num = num.toFixed(2);
                    num = parseFloat(num)
                    num = Math.round(num)
                    num = num.toLocaleString();
                    return num;//返回的是字符串23,245.12保留2位小数
                }
                
                $('#text2').on("change",function(){ 
                    console.log($(this).val())
                    var day = $("#daysval").val()
                    getinfo(day)
                });
                //获取数据
                function getinfo(days,start){
                    // console.log(days)
                    var end_time = ''
                    if (start != undefined) {
                        end_time = start
                    }
                    var type = $(".weui_input").val()
                    console.log(days)
                    console.log(type)
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?g=portal&m=database&a=saleopenorder',
                        data:{'days':days,'kind':type,'end_time':end_time},
                        success: function(data) {
                            console.log(data)
                            // aaaa(data.kinds)
                            $("#selectoption").val(data.kinds)
                            
                            // console.log(ave)
                            //根据具体数据进行判断
                            if (data['daily_count'] != undefined) {
                                $(".allsale").text("")
                                $(".allmoney").text("")
                                $(".allsale").append(data['count']['total_number'])
                                $(".allmoney").append(Math.round(data['count']['total_money']))
                                var arrlen = data['daily_count'].length
                                var res = ''
                                for (var i = arrlen-1; i >= 0; i--) {
                                    if (data['daily_count'][i]['result'].daile_money === null) {
                                        data['daily_count'][i]['result'].daile_money = 0
                                    }
                                    res += '<li class="fl">'+data['daily_count'][i].time+'</li><li class="fl">'+data['daily_count'][i]['result'].daily_number+'</li><li class="fl">'+toMoney(data['daily_count'][i]['result'].daile_money)+'</li>'
                                }
                                $("#chart").text("")
                                $("#chart").html(res)
                            }else{
                                $("#chart").text("")
                                $(".allsale").text("0")
                                $(".allmoney").text("0")
                            }
                            $("#text2").picker({
                                title: "选择品种",
                                toolbarCloseText:'确定',
                                cols: [
                                {
                                    textAlign: 'center',
                                    values: data.kinds,
                                    displayValues:data.kinds,
                                }
                                ],
                                // onChange: function(d){
                                //     // console.log($(this).val())
                                //     console.log(d.values);
                                //     var day = $("#daysval").val()
                                //     getinfo(day)//获取数据
                                // },
                            });
                        },
                        error: function(xhr, type) {
                           console.log('获取失败')
                        }
                    });
                    
                }
                
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