<!DOCTYPE html>
<html>
<head>
    <title>Patient Queue Display</title>
    <script src="../../js/jquery/jquery-3.6.3.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fefaf8;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #c68e75;
            color: white;
            padding: 20px 10px;
            text-align: left;
            display: flex;
            align-items: left;
            justify-content: left;
        }
        header img {
            height: 60px;
            margin-right: 15px;
        }
        .queue-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .panel {
            width: 25%;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
        }
        .center-panel {
            width: 48%;
            display: flex;
            flex-direction: column;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            min-height: 500px;
        }
        .center-controls {
            text-align: center;
            margin-bottom: 10px;
        }
        .center-controls button {
            margin: 0 10px;
            padding: 10px 16px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            background: #c68e75;
            color: #fff;
            cursor: pointer;
        }
        .center-controls button.active {
            background: #8a5b47;
        }
        .view-container {
            flex: 1;
            overflow: hidden;
        }
        .video-view,
        .queue-view {
            height: 100%;
            overflow-y: auto;
            display: none;
        }
        .video-view.active,
        .queue-view.active {
            display: block;
        }
        .queue-view table {
            margin-bottom: 20px;
        }
        .pic-section {
            width: 45%;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #c68e75;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f4f2;
        }
        tr:hover {
            background-color: #f1e5e0;
        }
        .marquee {
            background: #c68e75;
            color: white;
            font-size: 40px;
            padding: 12px;
            position: fixed;
            bottom: 0;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
        }
        .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: scroll 40s linear infinite;
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body onKeyPress="videoPlay();" onClick="videoPlay();" onLoad="play()">

<header>
    <div style="float: left;" ><img src="img/long_rank2.png" alt="Rank Derma Clinic Logo"/></div>
    
</header>

<div class="queue-container">
    

    <div class="panel" style="height: 500px; overflow: hidden;" id="appointmentContainer">
       
        <div id="appointmentContent" class="today_appointment">
            <h2>የዛሬ ቀጠሮ (0 ታካሚዎች)</h2>
            <table width="100%" class="today_appointment">
            
            </table>	
        </div>
    </div>

    <div class="center-panel">
        <div class="center-controls" style="display: none;">
            <button type="button" id="btnVideoView" class="active" onclick="toggleCenterView('video')">Video</button>
            <button type="button" id="btnQueueView" onclick="toggleCenterView('queue')">Doctor Queue</button>
        </div>
        <div class="view-container">
            <div id="videoView" class="video-view active">
                <video width="100%" height="350" controls autoplay muted id="video1">
                    <source src="/displays/skin.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video><br>
                <?php
                if($_ENV['BRANCH']=='Wollosefer'){
                    $msg = "በቀጠሮ ለሚመጡ ታካሚዎች ቅድሚያ እንሰጣለን!";
                }
                else{
                    $msg = "ውድ ታካሚዎች ህክምናዎትን ጨርሰው ሲወጡ
                 የህክምና ማዘዣ ከሪሴፕሽን ይውሰዱ፡፡";
                }
                ?>
                <span style="font-size: 40px;color:crimson"><?=$msg?></span>
            </div>
            <div id="queueView" class="queue-view">
                <div id="doctorQueueContent" style="padding-right: 5px;"></div>
            </div>
        </div>
    </div>

    
    <div class="panel" style="height: 500px; overflow: hidden;" id="waitingListContainer">
            
        <div class="today_waiting" id="waitingListContent">
            <h2>ተጠባባቂ (0 ታካሚዎች)</h2>;
            <table width="100%" class="today_waiting">
            
                
             
            </table>	

        
        </div>
    </div>
    
</div>

<div class="marquee">
    <span>ራንክ ልዩ የቆዳ ክሊኒክ | በቀጠሮ ለሚመጡ፡ ለድንገተኛ፡ ለአካል ጉዳተኛ፡ ሶስት ወር ላልሞላቸው ህጻናት ታካሚዎች ቅድሚያ እንሰጣለን! ቀጠሮዎትን በ 6797 በመደወል እንዲሁም በድረ ገጻችን "www.rankderma.com" ላይ ይያዙ፡፡ | አድራሻ፡ - ቁጥር 1 ወሎ ሰፈር አደባባይ ቲኬ ህንጻ 3ኛ ፎቅ "0943505050" - ቁጥር 2 ለቡ ቫርኔሮ ሳቤህ ስፕሪንግ ህንጻ 3ኛ ፎቅ "0943515151" - ቁጥር 3 ጋዜቦ አደባባይ ለቤንዝ ታወር 1ኛ ፎቅ "0947515151"</span>
   
</div>

</body>
</html>


<script>
         $(document).ready(function () {
        loadAppointments();
        loadWaitings();
        loadDoctorQueues();
        setInterval(toggleCenterAuto, 60000);
         });
    function loadAppointments(){
       //alert("Loading Appointments");
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
    function loadWaitings(){
       //alert("Loading Appointments");
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/gettodaywaitings",
            'type': 'GET', 
            'success': function(data) {
                var container = $(".today_waiting");
                container.html(data);
                //console.log(data);
            }
            });
        };
        setInterval("loadWaitings()",5000)
    function loadDoctorQueues(){
         $.ajax({ 
            'url': "<?php echo base_url(); ?>hospital/ajax/getdoctorqueues",
            'type': 'GET', 
            'success': function(data) {
                $("#doctorQueueContent").html(data);
            }
            });
        };
        setInterval("loadDoctorQueues()",15000)
    function toggleCenterAuto() {
        if ($("#videoView").hasClass('active')) {
            toggleCenterView('queue');
        } else {
            toggleCenterView('video');
        }
    }
    function toggleCenterView(view) {
        if (view === 'queue') {
            $("#videoView").removeClass('active');
            $("#queueView").addClass('active');
            $("#btnVideoView").removeClass('active');
            $("#btnQueueView").addClass('active');
            loadDoctorQueues();
        } else {
            $("#queueView").removeClass('active');
            $("#videoView").addClass('active');
            $("#btnQueueView").removeClass('active');
            $("#btnVideoView").addClass('active');
        }
    }
   
</script>	
<script> 

    var myVideo=document.getElementById("video1"); 
    var videoList=[<?php

            $videos = array();
            $dir = 'displays';
            $files = scandir($dir);
            shuffle($files);
            $first = true;

                foreach($files as $file) {
                    
                $filepath = $dir . '/' . $file;
                    if(is_file($filepath)) {
                        $contentType = mime_content_type($filepath);
                        if(stripos($contentType, 'video') !== false) {
                            if(!$first)
                                echo ',';
                            echo $x= "'/displays/". $videos[] = $file . "'";
                            $first = false;
                        }
                    }
                }
                //echo  "'img/skin.mp4'";
        ?>
    
    ];
    var index = videoList.indexOf(window.currentVideoName);

    //Next button\\
    function play(){
        //alert("hi");
        myVideo.autoplay = true;
        myVideo.currentTime=0;
        index = index + 1;
        if(index==videoList.length)
        index = 0;
        //alert(videoList[index]);
        myVideo.src = videoList[index];
        window.currentVideoName = videoList[index];
        myVideo.play();
    }

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
        document.getElementById("myVideo").muted = false;
    }
        function playPause(){ 
            if (myVideo.paused) 
                myVideo.play(); 
            else 
                myVideo.pause(); 
        } 
    
    var aud = document.getElementById("video1");
        aud.onended = function() {
        play();
    };
    

</script> 
<script>
  function autoScroll(containerId, contentId) {
    const container = document.getElementById(containerId);
    const content = document.getElementById(contentId);

    if (content.scrollHeight <= container.clientHeight) return;

    let direction = 1;
    const scrollSpeed = 1;

    setInterval(() => {
      container.scrollTop += direction * scrollSpeed;

      if (container.scrollTop + container.clientHeight >= content.scrollHeight || container.scrollTop <= 0) {
        direction *= -1;
      }
    }, 60);
  }

  window.onload = () => {
    autoScroll('appointmentContainer', 'appointmentContent');
    autoScroll('waitingListContainer', 'waitingListContent');
  };
</script>
