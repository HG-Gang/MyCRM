@extends("user.layout.main")

@section("css")
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}?ver={{ resource_version_number() }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap-reset.css') }}?ver={{ resource_version_number() }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome/font-awesome.css') }}?ver={{ resource_version_number() }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/style.css') }}?ver={{ resource_version_number() }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/main-style.css') }}?ver={{ resource_version_number() }}" />
@section("content")
    
    
    <style>
    .panel {
    margin-bottom: 0px;
    }
        .panel {
            background: #f1f2f7;
        }
        ul.summary-list > li {
            display: inline-block;
            width: 25%;
            height:120px;
            text-align: center;
            position: relative;
            margin-bottom: 15px;
            background: #fff;
            float: left;
        }
        ul.summary-list > li {
    border-right: 5px solid #eaeaea;
}
        ul.summary-list > li > a {
            padding: 0px 0;
            display: inline-block;
            color: #818181;
            margin-top: 45px;
        }
       ul.summary-list>.lh250{
            height:250px;
        }
       

          ul.summary-list>.lh250top{
            height:150px;
            width: 50%;
        }
        ul.summary-list>.lh250top>.box_lh_gg {
            height:150px;
            width: 100%;
        }
        .panel-body {
            padding: 15px;
            padding-top: 0px;
        }
       .box_lh_gg {
            width: 100%;
            height: 250px;
        }
        .box_lh_gg>p{
            height: 40px;
            line-height: 40px;
            text-align: left;
             width: 100%;
                                                               

        }
        .box_lh_gg>p>a{
            display: inline-block;
            width: 100%;
            height: 100%;
        }
        .box_lh_gg>p>a>span{
            display: inline-block;
            display: inline-block;
            width: 50%;
            float: left;
        }
        .box_lh_gg>p>a>span:nth-child(1){
            text-indent: 2em;

        }
        .box_lh_gg>p>a>span:nth-child(2){
                                                                    

        }
         @media screen and (max-width: 1200px){
                ul.summary-list>.lh250top {
                    height: 150px;
                    width: 100%;
                }
                ul.summary-list > li {
   
                    width: 50%;
   
                }
            .box_lh_gg>p>a {
                    line-height: 20px;
                }
        }
        @media screen and (max-width: 750px){
                ul.summary-list>.lh250top {
                    height: 150px;
                    width: 100%;
                }
                ul.summary-list > li {
   
                    width: 100%;
   
                }
            .box_lh_gg>p>a {
                    line-height: 20px;
                }
        }
        
    </style>
    <section id="container" class="">
        <section id="main-content" style="margin-top: 0px; margin-left: 0px; ">
            <section class="wrapper" style="margin-top:0px; padding: 0px;">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- <div class="panel" style="width: 100%; background: #fff;">
                            <div class="panel-body col-lg-6">
                                <div class="bio-desk "style=" background: #fff;width: 100%; padding: 0px;"  >
                                    <h4 class="terques" style="font-size: 18px;">尊贵的客户：</h4>
                                    <p style="margin-top: 10px;text-indent: 1em;">感谢您注册帕达控股！</p>
                                    <p style="margin-top: 10px;text-indent: 1em;"> 您的交易账户已激活，开始享受您的交易生活吧！</p>
                                
                                </div>
                            </div>
                            <div class="panel-body col-lg-6">
                                <div class="bio-desk" style=" background: #fff;width: 100%;padding: 0px;margin-left: 0px;" >
                                    <h4 class="terques" style="font-size: 18px;">  网址分享:</h4>
                                  
                                    <p style="margin-top: 10px;text-indent: 1em;">开立代理：<a href="{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}" target="_blank">{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}</a></p>
                                    <p style="margin-top: 10px;text-indent: 1em;">开立客户：<a href="{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}" target="_blank">{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}</a></p>
                                </div>
                            </div>
                        </div> -->

                        <div class="panel" style="    padding-top: 15px;">
                            <section class="panel" style="padding-top:0">
                                <div class="panel-body" id="logo_animtion" style="    padding: 0px 15px;">
                                    <ul class="summary-list">
                                         
                                            <li class="lh250top">
                                                <div class="box_lh_gg">
                                                     <h4 class="terques" style="font-size: 18px;    padding: 15px; color: #4CC5CD;text-align: left;">尊贵的客户：</h4>
                                                    <p style="text-indent: 1em; width:100%;">感谢您注册帕达控股！</p>
                                                    <p style="text-indent: 1em; width:100%;"> 您的交易账户已激活，开始享受您的交易生活吧！</p>
                                                </div>  
                                                
                                            </li>
                                        @if($_role == 'Agents')
                                             <li class="lh250top">
                                               <div class="box_lh_gg">
                                                     <h4 class="terques" style="font-size: 18px;    padding: 15px;color:#4CC5CD;text-align: left;">  网址分享:</h4>
                                                    <p style="text-indent: 1em; width:100%;"><a href="{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}" target="_blank">开立代理：{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}</a></p>
                                                    <p style="text-indent: 1em; width:100%;"><a href="{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}" target="_blank">开立客户：{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}</a></p>
                                                </div>  
    
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </section>
                        </div>






                        
                        <div class="panel">
                            <section class="panel">
                                <div class="panel-body" id="logo_animtion" style="padding-bottom: 0px;">
                                    <!-- <i class="icon-refresh" style="position: absolute;right: 23px;"></i> -->
                                    <ul class="summary-list ">
                                        <li>
                                            <a href="javascript:;">
                                                <i class=" text-primary  icon-exclamation-sign" style="color: #ff6c60">
                                                    {{ $_user_info['user_id'] }}
                                                </i>
                                                <span>
                                                <span style="color: #ff6c60 ;margin-left: 5px;">
                                                    @if($_user_info['user_status'] == '1' && $_user_info['IDcard_status'] == '2' && $_user_info['bank_status'] == '2')
                                                        已认证
                                                    @else
                                                        未认证
                                                    @endif
                                                </span>
                                                    {{--<p style="padding: 10px 0px 20px 30px;margin-top: 0px;text-align: center;">上次登录:{{ $_user_info['last_logindate'] }}</p>--}}
                                            </span>
                                                <p>
                                                    0.0
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="  text-primary  icon-exclamation-sign" style="">
                                                    账户姓名
                                                </i>
                                                <span style="">{{ $_user_info['user_name'] }}</span>
                                            
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="  text-primary  icon-exclamation-sign" style="">
                                                    账户余额
                                                </i>
                                                <span style="">{{ $_user_info['user_money'] }}美元</span>
                                            
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class=" text-primary icon-exclamation-sign" style="">
                                                    信用额
                                                </i>
                                                <span style=""> {{ number_format($_user_info['effective_cdt'], '2', '.', '')}}美元</span>
                                            
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="text-primary icon-exclamation-sign">身份证资料 </i>
                                                <span>
                                                <span style="color: #ff6c60 ;margin-left: 5px;">
                                                    @if($_user_info['IDcard_status'] == '0')
                                                        <span onclick="uploadIdCard()">待上传</span>
                                                    @elseif($_user_info['IDcard_status'] == '1')
                                                        正在审核中
                                                    @elseif($_user_info['IDcard_status'] == '2')
                                                        审核通过
                                                    @elseif($_user_info['IDcard_status'] == '4')
                                                        退回, 原因: {{ $_user_info['IDcard_remarks'] }}
                                                    @endif
                                                </span>
                                                    {{--<p style="padding: 10px 0px 20px 30px;margin-top: 0px;text-align: center;">上次登录:{{ $_user_info['last_logindate'] }}</p>--}}
                                            </span>
                                            
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="  text-primary  icon-exclamation-sign" style="">
                                                    净值
                                                </i>
                                                <span style="">{{ number_format($_user_info['cust_eqy'], '2', '.', '') }} 美元</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="text-primary  icon-exclamation-sign">
                                                    平仓总数
                                                </i>
                                                <span style="color: #89817f; margin-left: 5px;"> {{ $_closeTotal }} </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="text-primary  icon-exclamation-sign">未平总数 </i>
                                                <span style="color: #89817f; margin-left: 5px;"> {{ $_openTotal }} </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="icon-exclamation-sign  text-info">银行卡资料 </i>
                                                <span class="text-info" style="margin-left: 5px;">
                                                {{--审核成功--}}
                                                    @if($_user_info['bank_status'] == '0')
                                                        <span onclick="uploadBank()">待上传</span>
                                                    @elseif($_user_info['bank_status'] == '1')
                                                        正在审核中
                                                    @elseif($_user_info['bank_status'] === '2')
                                                        审核通过
                                                    @elseif($_user_info['bank_status'] == '3')
                                                        银行卡变更
                                                    @elseif($_user_info['bank_status'] == '4')
                                                        退回, 原因: {{ $_user_info['bank_remarks'] }}
                                                    @endif
                                            </span>
                                            
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="text-primary  icon-exclamation-sign">昨日入金</i>
                                                <span style="color: #89817f; margin-left: 5px;"> {{ $_ytdDepTotal }} </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="text-primary  icon-exclamation-sign">昨日出金 </i>
                                                <span style="color: #89817f; margin-left: 5px;"> {{ $_ytdDepTotal }} </span>
                                            </a>
                                        </li>
                                        @if($_user_info['trans_mode'] == '1')
                                            <li>
                                                <a href="javascript:;">
                                                    <i class="text-primary  icon-exclamation-sign">权益比例 </i>
                                                    <span style="color: #89817f; margin-left: 5px;"> {{ $_user_info['rights'] }} </span>
                                                </a>
                                            </li>
                                        @elseif($_user_info['trans_mode'] == '0')
                                            <li>
                                                <a href="javascript:;">
                                                    <i class="text-primary  icon-exclamation-sign">返佣比例 </i>
                                                    <span style="color: #89817f; margin-left: 5px;"> {{ $_user_info['comm_prop'] }} </span>
                                                </a>
                                            </li>
                                        @endif
                                          
                                    </ul>
                                </div>
                            </section>
                        </div>
                        <!--xiazai-->
                        <!-- <div class="panel">
                            <section class="panel">
                                <div class="panel">
                                    <div class="panel-body">
                                        @if($_role == 'Agents')
                                            <div class="col-lg-5" id="xm">
                                                <div class="panel-body link_">
                                                    <p>
                                                       公告
                                                    </p>
                                                    
                                                    <p>开立代理：<a href="{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}" target="_blank">{{ URL::asset('user/register/agents') }}/{{ $_user_info['user_id'] }}</a></p>
                                                    <p>开立客户：<a href="{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}" target="_blank">{{ URL::asset('user/register/user') }}/{{ $_user_info['user_id'] }}</a></p>
                                                    {{--<p>开立无佣金客户：<a href="{{ URL::asset('register/user') }}/{{ $_user_info['user_id'] }}/A" target="_blank">{{ URL::asset('register/user') }}/{{ $_user_info['user_id'] }}/A</a></p>--}}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-lg-7 img-xm-box" style="text-align: center">
                                            <div class="col-lg-4 xm_img">
                                                <img src="{{ URL::asset('img/software-downloads/android.jpg') }}?ver={{ resource_version_number() }}" alt="安卓下载" style="height: 145px;width:160px;">
                                                <p style="font-size: 13px; text-align: center; margin-top: 13px;">Android 版下载</p>
                                            </div>
                                            <div class="col-lg-4  xm_img">
                                                <img src="{{ URL::asset('img/software-downloads/ios.jpg') }}?ver={{ resource_version_number() }}" alt="IOS下载" style="height: 145px;width:160px;">
                                                <p style="font-size: 13px; text-align: center; margin-top: 13px;">IOS 版下载</p>
                                            </div>
                                            <div class="col-lg-4">
                                                
                                                <a class="btn btn-default "
                                                   style="width: 100%;  text-align: center;    margin-top: 35px;"
                                                   href="{{ MT4_download() }}"
                                                   download="{{ MT4_download() }}">
                                                    <i class=" icon-long-arrow-right"
                                                       style="padding: 0px 10px;font-size: 20px;">
                                                    </i>
                                                    MT4PC端下载
                                                </a>
                                                
                                                
                                                {{--<a class="btn btn-default "
												   style="width: 100%;  text-align: center;  margin-top: 35px;"
												   href="{{ MT4_download() }}" download="{{ MT4_download() }}">
		
													<i class=" icon-circle-arrow-down"
													   style="padding: 0px 10px;font-size: 20px;"></i>MT4&nbspP &nbspC端下载
												</a>--}}
                                            
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div> -->


                          <div class="panel">
                            <section class="panel" style="padding-top:0">
                                <div class="panel-body" id="logo_animtion" style="    padding: 0px 15px;">
                                    <ul class="summary-list ">
                                            <li class="lh250">
                                                <header class="panel-heading">
                                                    <span style="color: #4CC5CD;    margin-left: -26px;">最新公告</span>
                                                </header>
                                                <div class="box_lh_gg">
                                                    @foreach($_hotsNews as $v)
                                                        <p style="margin-top: 20px; ">
                                                            <a onclick="newsDetail('{{ $v['news_id'] }}')">
                                                                <span  >{{ $v['news_title'] }}</span>
                                                                <span style=" ">{{ $v['rec_upd_date'] }}</span>
                                                            </a>
                                                        </p>
                                                    @endforeach
                                                </div>  
                                            </li>
                                            <li class="lh250">
                                                <div class="box_lh_gg">
                                                     <img src="{{ URL::asset('img/software-downloads/android.jpg') }}?ver={{ resource_version_number() }}" alt="安卓下载" style="height: 145px;width:160px;    margin-top: 45px;">
                                                    <p style="text-align: center;">android移动版下载</p>
                                                </div>  
                                                
                                            </li>
                                             <li class="lh250">
                                               <div class="box_lh_gg">
                                                     <img src="{{ URL::asset('img/software-downloads/ios.jpg') }}?ver={{ resource_version_number() }}" alt="安卓下载" style="height: 145px;width:160px;    margin-top: 45px;">
                                                    <p style="text-align: center;">ios移动版下载</p>
                                                </div>  
    
                                            </li>
                                            <li class="lh250">
                                               <div class="box_lh_gg" style="line-height: 250px;font-size: 18px;font-weight: bold;">
                                                      <a href="https://download.mql5.com/cdn/web/12674/mt4/padaholding4setup.exe" download="https://download.mql5.com/cdn/web/12674/mt4/padaholding4setup.exe">
                                                            点击下载电脑版MT4
                                                        </a>
                                                   
                                                </div>  
                                             
                                            </li>
                                    </ul>
                                </div>
                            </section>
                        </div>
                    </div>
                    
                    
                    <!--banner-->
                <!-- <div class="col-lg-4">
                    <section class="panel">
                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" role="listbox" style="height:292px;">
                                <div class="item active">
                                    <img src="{{ URL::asset('img/logo/news01.jpg') }}?ver={{ resource_version_number() }}" alt=""
                                         style="height:290px;padding: 10px; width: 100%">
                                    <div class="carousel-caption">
    
                                        <p></p>
                                    </div>
                                </div>
                                <div class="item">
                                    <img src="{{ URL::asset('img/logo/news02.jpg') }}?ver={{ resource_version_number() }}" alt=""
                                         style="height:290px;padding: 10px; width: 100%">
                                    <div class="carousel-caption">
    
                                        <p></p>
                                    </div>
                                </div>
                                <div class="item">
                                    <img src="{{ URL::asset('img/logo/news03.jpg') }}?ver={{ resource_version_number() }}" alt=""
                                         style="height:290px;padding: 10px; width: 100%">
                                    <div class="carousel-caption">
    
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <a class="left carousel-control" href="#carousel-example-generic" role="button"
                               data-slide="prev">
                                <i class="icon-angle-left"></i>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" role="button"
                               data-slide="next">
                                <i class="icon-angle-right"></i>
                            </a>
                        </div>
                    </section>
                    <div class="panel col-lg-12">
                        <div class="panel-body">
                            <section class="panel pt-box" style="margin-bottom: 0;">
                                <header style="color: #4CC5CD;    margin-left: -11px;font-size: 15px;">
                                    平台优势：
                                </header>
                                <ul>
                                    <li>1.持有金融许可证并接受严格监管</li>
                                    <li>2.报价源自多家银行，极具点差优势</li>
                                    <li>3.现金高速的MT4平台</li>
                                    <li>4.出入金方便，专业客服服务</li>
                                </ul>
                            </section>
                        </div>
                    </div>
    
                    <div class="panel col-lg-12">
                        <div class="panel-body">
                            <section class="panel" style="margin-bottom: 0;">
                                <header class="panel-heading">
                                    <span style="color: #4CC5CD;    margin-left: -26px;">最新公告</span>
                                    {{--<span style="text-align: right;width: 100%;display: inline-block;">
                                        <a href="javascript:;" data-url="/user/news" style="color:#009688;" id="moreNews" title="more"><cite>》》</cite></a>
                                    </span>--}}
                        </header>


						<table class="table" style="font-size: 14px;">
							<tbody>
@foreach($_hotsNews as $v)
                    <tr>
						<td><a href="javascript:void(0);" onclick="newsDetail('{{ $v['news_id'] }}')">{{ $v['news_title'] }}</a></td>
                                            <td>{{ $v['rec_upd_date'] }}</td>
                                        </tr>
                                    @endforeach
                        </tbody>
					</table>
				</section>

			</div>

		</div>
	</div> -->
                </div>
                <!--state overview end-->
                {{--<div class="row">
					<div class="col-sm-12" style="text-align: center">
						Copyright 2018 帕达控股版权所有，不得转载，投资有风险，入市需谨慎
					</div>
				</div>--}}
            </section>
        </section>
    </section>
@endsection

@section("js")
    <script src="{{ URL::asset('js/jquery-1.11.3/jquery.scrollTo.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script src="{{ URL::asset('js/bootstrap/bootstrap.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script src="{{ URL::asset('js/jquery-1.11.3/jquery.nicescroll.js') }}?ver={{ resource_version_number() }}"></script>
    <script src="{{ URL::asset('js/jquery-1.11.3/common-scripts.js') }}?ver={{ resource_version_number() }}"></script>
    {{--<script src="{{ URL::asset('js/email_autocomplete/email_autocomplete.js') }}"></script>--}}
    <script src="{{ URL::asset('js/formevent/form.core.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript">
		
		$(function () {
			//    审核失败 开启动画
			//statrlogo();
		});
		function statrlogo() {
			$("#logo_animtion").addClass("panelbodys");
		}
		//    审核成功 关闭动画
		function statrlogo_off() {
			$("#logo_animtion").removeClass("panelbodys");
		}
		
		/*$(".panel-heading a").on("click",function(){alert('1111');
			window.parent.addTab($(this));
		});*/
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息!");
		}
    </script>
@endsection
