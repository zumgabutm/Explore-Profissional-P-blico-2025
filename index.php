<?php
// Diretório atual
$baseDir = __DIR__;
$dir = isset($_GET['dir']) ? realpath($baseDir.'/'.$_GET['dir']) : $baseDir;
if(strpos($dir, $baseDir) !== 0) { $dir = $baseDir; }
$files = array_diff(scandir($dir), array('.', '..'));
$currentDir = str_replace($baseDir, '', $dir);    
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>inforcusto</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap');
body { 
    margin:0; padding:0; font-family:'Orbitron',sans-serif; 
    background: radial-gradient(circle at top left,#0a0a0a,#1a1a3d); 
    color:#fff; min-height:100vh; display:flex; flex-direction:column; align-items:center;
}
h1 { margin:20px; font-size:3em; text-align:center; color:#ffd700; text-shadow:0 0 15px #00f,0 0 30px #ff4500;}
.header { width:100%; background: rgba(0,0,0,0.7); padding:10px 0; display:flex; justify-content:center; gap:20px; position:sticky; top:0; z-index:100;}
.header a { color:#0ff; font-weight:bold; text-decoration:none; padding:8px 15px; border-radius:8px; background: rgba(0,0,0,0.5); transition: all 0.3s ease;}
.header a:hover { background: rgba(0,0,255,0.7); transform: scale(1.1);}
.navbar { width:90%; max-width:800px; display:flex; justify-content:flex-start; align-items:center; margin:10px 0; gap:10px;}
.navbar a { text-decoration:none; color:#0ff; font-weight:bold; padding:10px 20px; background: rgba(0,0,0,0.5); border-radius:10px; transition: all 0.3s ease; }
.navbar a:hover { background: rgba(0,0,255,0.7); transform: scale(1.1);}
.file-list { width:90%; display:grid; grid-template-columns: repeat(auto-fill, minmax(180px,1fr)); gap:20px;}
.file-card { border:3px solid #00f; border-radius:15px; padding:20px; text-align:center; cursor:pointer; transition: all 0.3s ease; box-shadow:0 0 10px #00f,0 0 20px #00f inset;}

/* Cores específicas por tipo */
.file-card.folder { background: rgba(0,128,255,0.5); border-color:#00f; }       /* Pasta azul */
.file-card.exe    { background: rgba(255,0,0,0.5); border-color:#ff0000; }       /* EXE vermelho */
.file-card.apk    { background: rgba(0,255,0,0.5); border-color:#00ff00; }       /* APK verde */
.file-card.zip    { background: rgba(255,165,0,0.5); border-color:#ffa500; }     /* ZIP laranja */
.file-card.txt    { background: rgba(128,0,128,0.5); border-color:#800080; }     /* TXT roxo */
.file-card.default{ background: rgba(0,0,0,0.6); border-color:#00f; }           /* Outros arquivos */

.file-card:hover { transform: scale(1.1) rotate(2deg); box-shadow:0 0 20px #00f,0 0 40px #ffd700 inset; border-color:#ff4500;}
a.file-link { color:#fff; text-decoration:none; pointer-events:none; display:block; font-weight:bold; word-break:break-word;}
.file-ext { margin-top:8px; font-size:0.8em; color:#ff4500;}
#dragonball { position:absolute; width:80px; height:80px; border-radius:50%; background: radial-gradient(circle,#ffd700,#ff8c00); box-shadow:0 0 20px #ff4500,0 0 40px #ffd700 inset; pointer-events:none; z-index:5; transition: transform 0.05s ease;}

/* Rodapé */
.footer {
    width:100%;
    text-align:center;
    padding:15px 0;
    margin-top:auto;
    background: rgba(0,0,0,0.8);
    color: #0ff;
    font-weight:bold;
    font-size:0.9em;
    box-shadow:0 -2px 10px rgba(0,0,0,0.5);
}
</style>
</head>
<body>

<div class="header">
    <a href="https://chat.whatsapp.com/HB7RrCFk2IP3dztuZ9BG6N" target="_blank">Grupo Zap</a>
    <a href="https://www.youtube.com/@inforcusto/videos" target="_blank">YouTube</a>
    <a href="https://github.com/zumgabutm" target="_blank">GitHub</a>
    <a href="tel:+55989301407">Zap do Suport</a>
</div>

<h1>Dragon Explorer</h1>

<div class="navbar">
<?php if($currentDir != ''): ?>
    <a href="?dir=<?php echo urlencode(dirname($currentDir)); ?>">⬅ Voltar</a>
<?php endif; ?>
<span>Caminho: /<?php echo $currentDir; ?></span>
</div>

<div class="file-list">
<?php
foreach($files as $file){
    $fullPath = $dir.'/'.$file;

    // Ignorar arquivos .php, .htaccess e .env
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if(is_file($fullPath) && ($ext === 'php' || $file === '.htaccess' || $file === '.env')){
        continue;
    }

    // Determinar classe CSS pelo tipo
    $class = 'default';
    if(is_dir($fullPath)){
        $class = 'folder';
    } else {
        if($ext === 'exe') $class = 'exe';
        elseif($ext === 'apk') $class = 'apk';
        elseif($ext === 'zip') $class = 'zip';
        elseif($ext === 'txt') $class = 'txt';
    }

    if(is_dir($fullPath)){
        echo '<div class="file-card '.$class.'" onclick="navigateTo(\''.urlencode(trim($currentDir.'/'.$file,'/')).'\')">';
        echo '<a href="#" class="file-link">'.$file.'</a>';
        echo '<div class="file-ext">Pasta</div>';
        echo '</div>';
    } elseif(is_file($fullPath)){
        echo '<div class="file-card '.$class.'" onclick="confirmDownload(\''.$file.'\')">';
        echo '<a href="#" class="file-link">'.$file.'</a>';
        echo '<div class="file-ext">'.$ext.'</div>';
        echo '</div>';
    }
}
?>
</div>

<div id="dragonball"></div>

<script>
const dragon = document.getElementById('dragonball');
document.addEventListener('mousemove', e=>{
    dragon.style.left = e.pageX - 40 + 'px';
    dragon.style.top = e.pageY - 40 + 'px';
});

function navigateTo(path){
    window.location.href = '?dir=' + path;
}

function confirmDownload(file){
    if(confirm("Deseja baixar o arquivo: "+file+" ?")){
        const link = document.createElement('a');
        link.href = file;
        link.download = file;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}
</script>

<!-- Rodapé -->
<div class="footer">
    Explore Profissional Público 2025
</div>

</body>
</html>
