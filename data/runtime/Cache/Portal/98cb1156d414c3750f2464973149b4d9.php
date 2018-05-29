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
        .container {
            padding-top: 0.7rem;
        }
        
        .day {
            padding-bottom: 0.7rem;
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
            padding: 0.2rem 0;
            color: #ccc;
        }
        
        .active {
            background-color: #27c674 !important;
            color: #fff !important;
            border: 1.3px solid #27c674;
        }
        
        .table-box table {
            border-collapse: collapse;
            border: none;
            width: 100%;
        }
        
        .thead{
            padding: 0.6rem 0.5rem;
            color:#868686;
            background-color: #efefef;
        }
        
        .table-box table th,
        .table-box table td {
            border: none;
        }
        .table-box .table1 th:first-child{
            width: 34%;
        }
        .table-box .table1 th:nth-child(2){
            width: 10%;
        }
        .table-box .table1 th:nth-child(3){
             text-align:right;
             padding-right: 1rem; 
            width: 26%;
        }
        .table-box .table1 th:nth-child(4){
             text-align:right;
             padding-right: 1rem; 
            width: 30%;
        }
        /* .table-box table tr{
            height: 1rem;
        } */
        
        .table-box table td {
            text-align: center;
            height: 2rem;
        }
        
        .tbody{
            /* padding: 0 1rem; */
            color:#666;
        }
        .tbody img{
            width: 0.8rem;
            height: 1.1rem;
        }
        .tbody .table{
            border-bottom: 1px solid #ccc !important;
            padding:0.5rem 0.5rem;
        }
        .tbody tr:first-child td:first-child{
            width: 34%;
        }
        .tbody tr:first-child td:nth-child(2){
            width: 10%;
        }
        .tbody tr:first-child td:nth-child(3){
            width: 26%;
        }
        .tbody tr:first-child td:nth-child(4){
            width: 30%;
        }
        .tbody tr:nth-child(2) td:first-child{
            width: 34%;
        }
        .tbody tr:nth-child(2) td:nth-child(2){
            width: 10%;
        }
        .tbody tr:nth-child(2) td:nth-child(3){
            width: 26%;
        }
        .tbody tr:nth-child(2) td:nth-child(4){
            width: 30%;
        }
        .add{
            color:#27c674;
        }   
        .reduce{
            color:red;
        }   
        .equal{
            color:#000;
        }
        .timebox{
            /* height: 9%; */
            display:none;
            margin-bottom:2%;           
        }
        .timepicker {
            border-radius: 8px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            padding: 2% 0;
            height: 75%;
            width: 90%;
            margin: 0 auto;
            background-color: #eee;
            padding-left: 4%;
            padding-bottom: 2%;
        }
        .weui_cell_primary{
            background-color: #fff;
            width: 40%;
            height: 70%;
            border-radius: 6px;
        }
        .timebox i{
            margin-left:4%;
        }
        input{
            text-align: center;
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
        .picker-calendar-week-days{
            height: 1.4rem;
        }
        .weui-picker-calendar{
            height: 15.4rem;
        }
        .table-box table .leftval{
            text-align: right;
            padding-right: 1rem;

        }
        

        .num-money{
            padding:0 2% 0 3%;
            border-bottom:1px solid #ccc;
        }
        .num-money li{
            float:left;
            margin:1rem auto;
            width: 22%;
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
    </style>
</head>
<body>
     <div class="bg-orange weui-header "> 
        <div class="weui-header-left"><a class="icon icon-95 f-white" href="javascript:history.go(-1)">现货采销平衡表</a></div>
        <h1 class="weui-header-title"></h1>
    </div>
    <input class="" type="hidden" value="7" id="daysval">
    <ul class="num-money clearfix">
    <li><span>采</span></li>
    <li><p class="purschcount">0</p ><p>数量/吨</p ></li>
    <li><p class="purschmoney">0</p><p>金额/元</p ></li>
    <li><p class="purschorder">0</p><p>合同数</p ></li>
    </ul>
    <ul class="num-money clearfix"> 
    <li><span class="all">销</span></li>
    <li><p class="salecount">0</p><p>数量/吨</p ></li>
    <li><p class="salemoney">0</p><p>金额/元</p ></li>
    <li><p class="saleorder">0</p><p>合同数</p ></li>
    </ul>
    <div class="container">
            <div class="empty"></div>
            <div class="marketing-content">
                <div class="day">
                    <ul id="datenum" class="clearfix timeselect">
                        <li>
                            <a class="active" href="javascript:void(0)">今天</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)">七天</a>
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
                <div class="table-box">
                    <div class="thead">
                        <table border="" cellspacing="" cellpadding="" class="table1">
                            <tr>
                                <th>品种</th>
                                <th></th>
                                <th>数量/吨</th>
                                <th>金额/元</th>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="tbody">
                            <!-- <table border="" cellspacing="" cellpadding="">
                                <tr>
                                    <td class="borderR" rowspan="2">南通一德yide</td>
                                    <td><img src="/themes/simplebootx/Public/assets/images/dataimg/img/cai.png" alt=""></td>
                                    <td>1000</td>
                                    <td>31</td>
                                </tr>
                                <tr>
                                    <td><img src="/themes/simplebootx/Public/assets/images/dataimg/img/xiao.png" alt=""></td>
                                    <td>2000</td>
                                    <td>6,400,222</td>
                                </tr>
                            </table> -->
                    </div>
                </div>
            </div>
        </div>

        <script src="/themes/simplebootx/Public/assets/js/datajs/zepto.min.js"></script>
        <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
        <script>
            getinfo(1)
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

            $(".timeselect").on('click','li',function(){
                $(this).find('a').addClass('active');
                $(this).siblings().find('a').removeClass('active');
                if($(this).index() == 2){
                    $(".timebox").show();
                }else{
                    $(".timebox").hide();
                }
                if ($(this).index() == 0) {
                    $("#daysval").val(1)
                    getinfo(1)//获取数据
                }
                if ($(this).index() == 1) {
                    $("#daysval").val(7)
                    getinfo(7)//获取数据
                }
            })

            function toMoney(num){
                if (num == '') {

                }else{
                    //小数的分位符
                    // num = parseFloat(num)
                    // num = num.toFixed(2);
                    // var len = num.indexOf(".")
                    // var numfloat = num.substring(len,num.length)
                    // var numzheng = num.substring(0,len)
                    // numzheng = parseFloat(numzheng)
                    // num = numzheng.toLocaleString();
                    // num += numfloat
                    
                    num = parseFloat(num)
                    num = Math.round(num)
                    num = num.toLocaleString()
                }
                return num;//返回的是字符串23,245.12保留2位小数
            }
            function tofloat(num){
                if (num == '') {

                }else{
                    num = parseFloat(num)
                    num = num.toLocaleString()
                }
                return num;//返回的是字符串23,245.12保留2位小数
            }
            //获取数据
            function getinfo(days,start){
                console.log(days)
                var end_time = ''
                if (start != undefined) {
                    end_time = start
                }
                $.ajax({
                    type: 'POST',
                    url: 'index.php?g=portal&m=database&a=salepurchase',
                    data:{'days':days,'end_time':end_time},
                    success: function(data) {
                        console.log(data)
                        

                        if (data['data']['offer_list'] != undefined) {
                            $(".purschcount").text("")
                            $(".purschmoney").text("")
                            $(".salecount").text("")
                            $(".salemoney").text("")
                            $(".purschorder").text("")
                            $(".saleorder").text("")
                            $(".purschcount").append(data['data']['purchase_count'].purchase_total_num)
                            $(".purschmoney").append(data['data']['purchase_count'].purchase_total_money)
                            $(".purschorder").append(data['data']['purchase_count'].deal_num)
                            $(".saleorder").append(data['data']['sale_count'].deal_num)
                            $(".salecount").append(data['data']['sale_count'].sale_total_num)
                            $(".salemoney").append(data['data']['sale_count'].sale_total_money)
                            var arrlen = data['data']['offer_list'].length
                            console.log(arrlen)
                            var res = ''
                            for (var i = 0; i < arrlen; i++) {
                                if (data['data']['offer_list'][i].saleamount_num === undefined || data['data']['offer_list'][i].saleamount_num == null) {
                                    data['data']['offer_list'][i].saleamount_num = 0
                                }
                                if (data['data']['offer_list'][i].saleamount_money === undefined || data['data']['offer_list'][i].saleamount_money == null) {
                                    data['data']['offer_list'][i].saleamount_money = 0
                                }
                                if (data['data']['offer_list'][i].purchaseamount_num === undefined || data['data']['offer_list'][i].purchaseamount_num == null) {
                                    data['data']['offer_list'][i].purchaseamount_num = 0
                                }
                                if (data['data']['offer_list'][i].purchaseamount_money === undefined || data['data']['offer_list'][i].purchaseamount_money == null) {
                                    data['data']['offer_list'][i].purchaseamount_money = 0
                                }
                                var wan_purchaseamount_money = toMoney((data['data']['offer_list'][i].purchaseamount_money).toFixed(2))
                                var wan_saleamount_money = toMoney((data['data']['offer_list'][i].saleamount_money).toFixed(2))
                                var add = data['data']['offer_list'][i].purchaseamount_num - data['data']['offer_list'][i].saleamount_num
                                var temp = ''
                                if (add > 0) {
                                    temp = '+' + tofloat(add)
                                }else{
                                    temp = tofloat(add)
                                }
                                temp += '吨'
                                
                                var name = data['data']['offer_list'][i].delivery_address;
                                data['data']['offer_list'][i].delivery_address = name.substring(0,name.length-2)
                                res += '<div class="table"><table border="" cellspacing="" cellpadding=""><tr><td>'+data['data']['offer_list'][i].delivery_address+'</td><td><img src="/themes/simplebootx/Public/assets/images/dataimg/img/cai.png" alt=""></td><td class="leftval">'+tofloat(data['data']['offer_list'][i].purchaseamount_num)+'</td><td class="leftval">'+wan_purchaseamount_money+'</td></tr><tr><td class="addvalue">'+temp+'</td><td><img src="/themes/simplebootx/Public/assets/images/dataimg/img/xiao.png" alt=""></td><td class="leftval">'+tofloat(data['data']['offer_list'][i].saleamount_num)+'</td><td class="leftval">'+wan_saleamount_money+'</td></tr></table></div>'
                            }
                            $(".tbody").text("")
                            $(".tbody").html(res)
                            
                            var judge = $(".table").length
                            for (var j = 0; j < judge; j++) {
                                var redu = $(".addvalue").eq(j).text()
                                // console.log(redu.indexOf('11'))
                                // console.log(redu)
                                if (redu.indexOf('-') != -1) {
                                    $(".addvalue").eq(j).addClass('reduce').removeClass('add').removeClass('equal')
                                }else if (redu.indexOf('+') != -1) {
                                    $(".addvalue").eq(j).addClass('add').removeClass('equal').removeClass('reduce')
                                }else{
                                    
                                    $(".addvalue").eq(j).addClass('equal').removeClass('add').removeClass('reduce')
                                }

                            }
                            
                        }else{
                            $(".tbody").text("")
                            $(".purschcount").text("0")
                            $(".purschmoney").text("0")
                            $(".salecount").text("0")
                            $(".salemoney").text("0")
                            $(".purschorder").text("0")
                            $(".saleorder").text("0")
                        }

                    },
                    error: function(xhr, type) {
                       console.log('获取失败')
                    }
                });
            }
        </script>
</body>
</html>