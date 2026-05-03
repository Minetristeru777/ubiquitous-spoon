<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Ви не увійшли у систему!");
}

require "config.php"; // підключає $conn

# --- Обробка запитів ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $filename = $_POST['filename'] ?? '';
    $content = $_POST['content'] ?? '';
    $uid = intval($_SESSION['user_id']);

    if ($action === "save" && $filename) {
        $stmt = $conn->prepare("INSERT INTO vfs (user_id, filename, content) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE content=?");
        $stmt->bind_param("isss", $uid, $filename, $content, $content);
        $stmt->execute();
        echo "saved"; exit;
    }

    if ($action === "open" && $filename) {
        $stmt = $conn->prepare("SELECT content FROM vfs WHERE user_id=? AND filename=?");
        $stmt->bind_param("is", $uid, $filename);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) echo $row['content'];
        exit;
    }

    if ($action === "list") {
        $stmt = $conn->prepare("SELECT filename FROM vfs WHERE user_id=?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        $files = [];
        while ($r = $res->fetch_assoc()) $files[] = $r['filename'];
        echo json_encode($files);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title>Віртуальний ПК</title>
<style>
    body { margin:0; background:#1e90ff; font-family:sans-serif; overflow:hidden; }
    .taskbar { position:absolute; bottom:0; left:0; right:0; height:40px; background:#333; color:#fff; display:flex; align-items:center; padding:0 10px; }
    .start-btn { background:#555; padding:5px 10px; cursor:pointer; margin-right:10px; }
    .start-menu { position:absolute; bottom:40px; left:0; width:200px; background:#222; color:#fff; display:none; flex-direction:column; border:2px solid #444; }
    .start-menu div { padding:10px; cursor:pointer; }
    .start-menu div:hover { background:#444; }
    .icon { width:64px; text-align:center; color:white; cursor:pointer; margin:10px; display:inline-block; }
    .window { position:absolute; top:100px; left:100px; width:400px; height:300px; background:#eee; border:2px solid #333; display:none; flex-direction:column; resize:both; overflow:auto; }
    .titlebar { background:#444; color:#fff; padding:5px; cursor:move; }
    .content { flex:1; padding:5px; background:white; overflow:auto; }
    .close { float:right; cursor:pointer; }
</style>
</head>
<body>

<!-- Робочий стіл -->
<div id="desktop"></div>

<!-- Вікна -->
<div id="notepad" class="window">
    <div class="titlebar">Блокнот <span class="close" onclick="closeWindow('notepad')">❌</span></div>
    <div class="content">
        <input id="noteFile" placeholder="назва файлу"><br>
        <textarea id="noteArea" style="width:100%;height:70%;"></textarea><br>
        <button onclick="saveNote()">💾 Зберегти</button>
        <button onclick="loadNote()">📂 Відкрити</button>
    </div>
</div>

<div id="browser" class="window">
    <div class="titlebar">Браузер <span class="close" onclick="closeWindow('browser')">❌</span></div>
    <div class="content">
        <input id="url" type="text" value="https://example.com" style="width:80%">
        <button onclick="go()">▶</button>
        <iframe id="frame" src="https://example.com" style="width:100%;height:90%;"></iframe>
    </div>
</div>

<div id="explorer" class="window">
    <div class="titlebar">Файли <span class="close" onclick="closeWindow('explorer')">❌</span></div>
    <div class="content">
        <ul id="fileList"></ul>
        <button onclick="listFiles()">🔄 Оновити</button>
    </div>
</div>

<!-- Панель задач -->
<div class="taskbar">
    <div class="start-btn" onclick="toggleStart()">Пуск</div>
    <div class="start-menu" id="startMenu">
        <div onclick="openWindow('notepad')">📝 Блокнот</div>
        <div onclick="openWindow('browser')">🌐 Браузер</div>
        <div onclick="openWindow('explorer')">📂 Файли</div>
    </div>
    <div style="margin-left:auto;">Віртуальний ПК користувача #<?=$_SESSION['user_id']?></div>
</div>

<script>
// --- Вікна ---
function openWindow(id){ document.getElementById(id).style.display='flex'; }
function closeWindow(id){ document.getElementById(id).style.display='none'; }

// --- Старт ---
function toggleStart(){
    let menu = document.getElementById("startMenu");
    menu.style.display = (menu.style.display==='flex')?'none':'flex';
}

// --- AJAX ---
async function fs(action, filename="", content=""){
    let form = new FormData();
    form.append("action", action);
    if(filename) form.append("filename", filename);
    if(content) form.append("content", content);
    let r = await fetch("desktop.php", {method:"POST", body:form});
    return await r.text();
}

// --- Блокнот ---
async function saveNote(){
    let f = document.getElementById("noteFile").value;
    let c = document.getElementById("noteArea").value;
    if(f) await fs("save", f, c);
}
async function loadNote(){
    let f = document.getElementById("noteFile").value;
    if(f){
        let c = await fs("open", f);
        document.getElementById("noteArea").value = c;
    }
}

// --- Провідник ---
async function listFiles(){
    let res = await fs("list");
    let files = JSON.parse(res);
    let list = document.getElementById("fileList");
    list.innerHTML = "";
    files.forEach(f=>{
        let li = document.createElement("li");
        li.innerText = f;
        li.onclick=()=>{document.getElementById("noteFile").value=f; openWindow('notepad');};
        list.appendChild(li);
    });
}

// --- Браузер ---
function go(){
    let url = document.getElementById("url").value;
    if (!url.startsWith("http")) url = "https://" + url;
    document.getElementById("frame").src = url;
}

// --- Перетягування ---
document.querySelectorAll(".window").forEach(win=>{
    const bar = win.querySelector(".titlebar");
    let offsetX, offsetY, dragging=false;
    bar.addEventListener("mousedown", e=>{
        dragging=true; offsetX=e.offsetX; offsetY=e.offsetY;
    });
    document.addEventListener("mousemove", e=>{
        if(dragging){
            win.style.left = (e.pageX-offsetX)+"px";
            win.style.top = (e.pageY-offsetY)+"px";
        }
    });
    document.addEventListener("mouseup", ()=>dragging=false);
});
</script>

</body>
</html>
