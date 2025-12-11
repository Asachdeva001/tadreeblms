<!DOCTYPE html>
<html>
<head>
<title>Installer</title>
<style>
body{font-family:Arial;background:#f2f2f2;padding:20px;}
.container{max-width:700px;margin:50px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
h2{margin-bottom:20px;}
.progress{background:#eee;border-radius:20px;height:20px;margin-bottom:20px;overflow:hidden;}
.bar{height:100%;width:0;background:#4caf50;text-align:center;color:#fff;line-height:20px;transition:0.4s;}
.output{background:#000;color:#0f0;padding:10px;height:300px;overflow:auto;font-family:monospace;}
.button{padding:10px 20px;background:#4caf50;color:#fff;border-radius:5px;cursor:pointer;margin-top:10px;}
.hidden{display:none;}
input{width:100%;padding:8px;margin-bottom:10px;}
</style>
</head>
<body>
<div class="container">
    <h2>Installer</h2>

    <div class="progress"><div id="bar" class="bar">0%</div></div>

    <div id="log" class="output"></div>

    <!-- DB Form -->
    <div id="dbform" class="hidden">
        <h3>Database Settings</h3>
        <input type="text" id="db_host" placeholder="DB Host" value="127.0.0.1">
        <input type="text" id="db_name" placeholder="Database Name">
        <input type="text" id="db_user" placeholder="DB Username">
        <input type="password" id="db_pass" placeholder="DB Password">
        <button onclick="saveDB()" class="button">Save & Continue</button>
    </div>

    <button id="startBtn" class="button" onclick="runStep('check')">Start Installation</button>
</div>

<script>
let steps = ["check","composer","db_config","env","key","migrate","seed","permissions","finish"];
let currentIndex = 0;

function runStep(step){
    document.getElementById("startBtn").classList.add("hidden");

    fetch("install_ajax.php", {
        method: "POST",
        body: new URLSearchParams({step: step})
    })
    .then(res => res.json())
    .then(res => {
        appendLog(res.output);
        updateBar(res.percent);

        if(res.show_db_form){
            document.getElementById("dbform").classList.remove("hidden");
            return; // wait for DB input
        }

        if(step !== "finish"){
            setTimeout(()=>runStep(res.next),500);
        }
    })
    .catch(err=>{
        appendLog("❌ AJAX error: " + err);
    });
}

function saveDB(){
    let formData = new URLSearchParams({
        step: "db_save",
        db_host: document.getElementById("db_host").value,
        db_name: document.getElementById("db_name").value,
        db_user: document.getElementById("db_user").value,
        db_pass: document.getElementById("db_pass").value,
    });

    fetch("install_ajax.php", {method:"POST", body:formData})
    .then(res => res.json())
    .then(res => {
        appendLog(res.output);
        document.getElementById("dbform").classList.add("hidden");
        runStep(res.next);
    })
    .catch(err=>{
        appendLog("❌ AJAX error: " + err);
    });
}

function appendLog(msg){
    let log = document.getElementById("log");
    log.innerHTML += msg + "<br>";
    log.scrollTop = log.scrollHeight;
}

function updateBar(percent){
    let bar = document.getElementById("bar");
    bar.style.width = percent+"%";
    bar.innerHTML = percent+"%";
}
</script>

</body>
</html>
