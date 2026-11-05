<!DOCTYPE html>
<html>
<head>
<style>
canvas {
border:1px solid #c3c3c3;
}
</style>

</head>   
<body>   
<canvas id="cv1" width="400" height="300">    
</canvas>
<canvas id="cv2" width="400" height="300">
</canvas>
<hr>
<button onclick='merge();'>Merge</button>
<script>
var el1 = document.getElementById('cv1');
var el2 = document.getElementById('cv2');
    var ctx1 = el1.getContext('2d');
    var ctx2 = el2.getContext('2d');
    ctx1.lineJoin = "round"
    ctx1.strokeStyle = "#00FF00"; 
    ctx1.globalAlpha = "0.2"; 
    ctx1.lineWidth = 30; 
    ctx1.globalCompositeOperation = "source-over";
  ctx2.globalCompositeOperation = "source-over";
    var isDrawing, lastPoint;
el1.onmousedown = function(e) {
        isDrawing = true;
        lastPoint = { x: e.clientX, y: e.clientY };
    };  
    el1.onmousemove = function(e) {
    if (!isDrawing) return;
        var currentPoint = { x: e.clientX, y: e.clientY };
        ctx1.beginPath();
        ctx1.moveTo(lastPoint.x, lastPoint.y);
        ctx1.lineTo(currentPoint.x, currentPoint.y);
        ctx1.closePath();
        ctx1.stroke();
        lastPoint = currentPoint;
    };
    el1.onmouseup = function() {
        isDrawing = false;
    };
function merge() {
ctx2.putImageData(ctx1.getImageData(0,0,400,300),0,0);
}
</script>
</body>
</html>