<?php
    $appointments = $this->data['appointments'];
    $no_of_appointments = $this->data['no_of_appointments'];
    //\BluelimeSystem::show_and_die($appointments);
?>
<head>

    <style {csp-style-nonce}>
        * {
            transition: background-color 300ms ease, color 300ms ease;
        }
        *:focus {
            background-color: rgba(221, 72, 20, .2);
            outline: none;
        }
        html, body {
            color: rgba(33, 37, 41, 1);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
            font-size: 16px;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        header {
            background-color: rgba(247, 248, 249, 1);
            padding: .4rem 0 0;
        }
        .menu {
            padding: .4rem 2rem;
        }
        header ul {
            border-bottom: 1px solid rgba(242, 242, 242, 1);
            list-style-type: none;
            margin: 0;
            overflow: hidden;
            padding: 0;
            text-align: right;
        }
        header li {
            display: inline-block;
        }
        header li a {
            border-radius: 5px;
            color: rgba(0, 0, 0, .5);
            display: block;
            height: 44px;
            text-decoration: none;
        }
        header li.menu-item a {
            border-radius: 5px;
            margin: 5px 0;
            height: 38px;
            line-height: 36px;
            padding: .4rem .65rem;
            text-align: center;
        }
        header li.menu-item a:hover,
        header li.menu-item a:focus {
            background-color: rgba(221, 72, 20, .2);
            color: rgba(221, 72, 20, 1);
        }
        header .logo {
            float: left;
            height: 44px;
            padding: .4rem .5rem;
        }
        header .menu-toggle {
            display: none;
            float: right;
            font-size: 2rem;
            font-weight: bold;
        }
        header .menu-toggle button {
            background-color: rgba(221, 72, 20, .6);
            border: none;
            border-radius: 3px;
            color: rgba(255, 255, 255, 1);
            cursor: pointer;
            font: inherit;
            font-size: 1.3rem;
            height: 36px;
            padding: 0;
            margin: 11px 0;
            overflow: visible;
            width: 40px;
        }
        header .menu-toggle button:hover,
        header .menu-toggle button:focus {
            background-color: rgba(221, 72, 20, .8);
            color: rgba(255, 255, 255, .8);
        }
        header .heroe {
            margin: 0 auto;
            max-width: 1100px;
            padding: 1rem 1.75rem 1.75rem 1.75rem;
        }
        header .heroe h1 {
            font-size: 2.5rem;
            font-weight: 500;
        }
        header .heroe h2 {
            font-size: 1.5rem;
            font-weight: 300;
        }
        section {
            margin: 0 auto;
            max-width: 1100px;
            padding: 2.5rem 1.75rem 3.5rem 1.75rem;
        }
        section h1 {
            margin-bottom: 2.5rem;
        }
        section h2 {
            font-size: 120%;
            line-height: 2.5rem;
            padding-top: 1.5rem;
        }
        section pre {
            background-color: rgba(247, 248, 249, 1);
            border: 1px solid rgba(242, 242, 242, 1);
            display: block;
            font-size: .9rem;
            margin: 2rem 0;
            padding: 1rem 1.5rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        section code {
            display: block;
        }
        section a {
            color: rgba(221, 72, 20, 1);
        }
        section svg {
            margin-bottom: -5px;
            margin-right: 5px;
            width: 25px;
        }
        .further {
            background-color: rgba(247, 248, 249, 1);
            border-bottom: 1px solid rgba(242, 242, 242, 1);
            border-top: 1px solid rgba(242, 242, 242, 1);
        }
        .further h2:first-of-type {
            padding-top: 0;
        }
        footer {
            background-color: rgba(221, 72, 20, .8);
            text-align: center;
        }
        footer .environment {
            color: rgba(255, 255, 255, 1);
            padding: 2rem 1.75rem;
        }
        footer .copyrights {
            background-color: rgba(62, 62, 62, 1);
            color: rgba(200, 200, 200, 1);
            padding: .25rem 1.75rem;
        }
        @media (max-width: 629px) {
            header ul {
                padding: 0;
            }
            header .menu-toggle {
                padding: 0 1rem;
            }
            header .menu-item {
                background-color: rgba(244, 245, 246, 1);
                border-top: 1px solid rgba(242, 242, 242, 1);
                margin: 0 15px;
                width: calc(100% - 30px);
            }
            header .menu-toggle {
                display: block;
            }
            header .hidden {
                display: none;
            }
            header li.menu-item a {
                background-color: rgba(221, 72, 20, .1);
            }
            header li.menu-item a:hover,
            header li.menu-item a:focus {
                background-color: rgba(221, 72, 20, .7);
                color: rgba(255, 255, 255, .8);
            }
        }
    </style>
    <script src="../../js/jquery/jquery-3.6.3.js" type="text/javascript"></script>
    <script>
    //$(document).ready(function(){
    //	setInterval(function(){
    //		$("#appointment").load('refresh_appointment.php')
    //   }, 2000);
    //});
    </script>
    <script>
        var elem = document.documentElement;

        function videoPlay(videoNum) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) { /* Firefox */
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE/Edge */
                elem.msRequestFullscreen();
            }
        }
	
	</script>	
    </head>
    <body onKeyPress="videoPlay();" onClick="videoPlay();" onLoad="play()">
    
        <div class="main-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-8">
                        <div class="logo">
                            <a href="#"><h1><img src="img/logo.png" height="110px"></h1></a>
                        </div> <!-- /.logo -->
                    </div> <!-- /.col-md-4 -->
                    
                </div> <!-- /.row -->
            </div> <!-- /.container -->
        </div> <!-- /.main-header -->
        <hr style="border-top: .1px solid #145477;">
        <div class="main-nav" style="height:40px">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-sm-7">
                        <div class="">
                        <h1>Appointment(<?php  echo $no_of_appointments ?> Customers)</h1>
                        </div> <!-- /.list-menu -->
                    </div> <!-- /.col-md-6 -->
                    
                </div> <!-- /.row -->
            </div> <!-- /.container -->
        </div> <!-- /.main-nav -->

     <!-- /.site-header -->
    
	

    



<div name="appointment"  class="today_appointment" id="appointment" style="margin-left:0px; width:45%;  float: left">

<table width="100%" class="today_appointment">
    <tr style="background-color:#ccc; border-color:#fff; border-bottom-style:solid; padding:12px 12px 12px 12px; font-size: 30px">
        <th style="border-right-style:solid; border-color:#FFFFFF;  padding:12px 12px 12px 12px">Full Name</th>
        <th style="border-right-style:solid; border-color:#FFFFFF;  padding:12px 12px 12px 12px">Time</th>
        <th style="border-right-style:solid; border-color:#FFFFFF;  padding:12px 12px 12px 12px">Doctor</th>
    </tr>
    
	
</table>	

	
</div>



<div style="float: left; width:55%; ">
	
	
	<video width="100%" min-height="900px" controls autoplay="true" id="video1" >

		
</video>


	
</div>

<div name="notice" id="notice">	
	
<table width="100%"  style="float:top";>
<tr style="background-color: #145477; color: white">
	<th style="padding:5px 5px 5px 5px; font-size:25px">Notice</th>
</tr>	
</table>
<table width=100% >
		
			    <tr style="background-color:white; height: 10px; color:#330405">
					<td style="padding:5px 5px 5px 5px; height: 5px; font-size:30px; font-size-adjust: auto">
						<marquee width="100%" direction="left" style="padding-top: 5px">
                        <img alt="" src="img/bullet.jpg" width="22px">&nbsp;ክዚህ በታች ከተዘረዘሩተ ድረጅቶች ለምትመጡ ውድ ደንበኞቻችን ለህከምና በምትመጡ ወቅት የድርጅታችሁን መታወቂያ እንዲሁም የሪፈራል ወረቀት መያዛችሁን እርግጠኛ እንድትሆኑ በትህትና እንጠይቃለን፡፡ 1. የኢትዮጵያ አየር መንገድ  2. የኢትዮጵያ ኤሌትሪክ ሃይል  3. የኢትዮጵያ ኤሌትሪክ አገልግሎት  4. ኤስ.አይ.ኤም  5. ቢንግሀም አካዳሚ
</marquee>
					</td>
				</tr>
                <tr style="background-color:white; height: 10px; color:#330405">
					<td style="padding:5px 5px 5px 5px; height: 5px; font-size:30px; font-size-adjust: auto">
						<marquee width="100%" direction="left" style="padding-top: 5px">
                        <img alt="" src="img/bullet.jpg" width="22px">&nbsp;Dear customers coming from the following listed organizations, you are kindly requested to bring your organization's ID card and referral letter on your visit to our clinic. 1. Ethiopian Airlines 2. Ethiopian electric power 3. Ethiopian electric utility 4. S.I.M 5. Bingham acadamy
</marquee>
					</td>
				</tr>
			
		
</table>
	
</div>

<script>
    function loadAppointments(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/gettodatappointments",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".today_appointment");
                container.html(data);
                //console.log(data);
            }
            });
        };
        setInterval("loadAppointments()",5000)
   
</script>	