<?php
define('DB_HOST','localhost');define('DB_USER','root');define('DB_PASS','');
define('DB_NAME','career_craft');define('SITE_URL','http://localhost/naveen_resume/');
session_start();
function db(){return new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);}

// AJAX Handler
if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['ajax'])){
    header('Content-Type:application/json');$c=db();
    
    if($_POST['ajax']=='create'){
        $t=$c->real_escape_string($_POST['template']);
        $ti=$c->real_escape_string($_POST['title']);
        $c->query("INSERT INTO resumes(title,template)VALUES('$ti','$t')");
        $rid=$c->insert_id;
        $c->query("INSERT INTO personal_info(resume_id)VALUES($rid)");
        echo json_encode(['s'=>1,'id'=>$rid]);exit();
    }
    
    $rid=(int)$_POST['resume_id'];
    
    if($_POST['ajax']=='save_personal'){
        $f=['full_name','email','phone','location','linkedin','job_title','summary'];
        $v=[];foreach($f as $x)$v[]="'".$c->real_escape_string($_POST[$x]??'')."'";
        $c->query("UPDATE personal_info SET ".implode(',',array_map(function($x)use($v,$f){$i=array_search($x,$f);return"$x=$v[$i]";},$f))." WHERE resume_id=$rid");
        echo json_encode(['s'=>1]);exit();
    }
    
    if($_POST['ajax']=='add_exp'){
        $co=$c->real_escape_string($_POST['company']);$p=$c->real_escape_string($_POST['position']);
        $l=$c->real_escape_string($_POST['location']);$sd=$c->real_escape_string($_POST['start_date']);
        $ed=$c->real_escape_string($_POST['end_date']);$cj=isset($_POST['current_job'])?1:0;
        $d=$c->real_escape_string($_POST['description']);
        $c->query("INSERT INTO experience(resume_id,company,position,location,start_date,end_date,current_job,description)VALUES($rid,'$co','$p','$l','$sd','$ed',$cj,'$d')");
        echo json_encode(['s'=>1]);exit();
    }
    
    if($_POST['ajax']=='del_exp'){$c->query("DELETE FROM experience WHERE id=".(int)$_POST['id']);echo json_encode(['s'=>1]);exit();}
    
    if($_POST['ajax']=='add_edu'){
        $s=$c->real_escape_string($_POST['school']);$d=$c->real_escape_string($_POST['degree']);
        $f=$c->real_escape_string($_POST['field']);$sd=$c->real_escape_string($_POST['start_date']);
        $ed=$c->real_escape_string($_POST['end_date']);$g=$c->real_escape_string($_POST['gpa']);
        $c->query("INSERT INTO education(resume_id,school,degree,field,start_date,end_date,gpa)VALUES($rid,'$s','$d','$f','$sd','$ed','$g')");
        echo json_encode(['s'=>1]);exit();
    }
    
    if($_POST['ajax']=='del_edu'){$c->query("DELETE FROM education WHERE id=".(int)$_POST['id']);echo json_encode(['s'=>1]);exit();}
    
    if($_POST['ajax']=='add_skill'){
        $n=$c->real_escape_string($_POST['skill_name']);$l=$c->real_escape_string($_POST['level']);
        $c->query("INSERT INTO skills(resume_id,skill_name,level)VALUES($rid,'$n','$l')");
        echo json_encode(['s'=>1]);exit();
    }
    
    if($_POST['ajax']=='del_skill'){$c->query("DELETE FROM skills WHERE id=".(int)$_POST['id']);echo json_encode(['s'=>1]);exit();}
    
    if($_POST['ajax']=='update_style'){
        $font=$c->real_escape_string($_POST['font']);$color=$c->real_escape_string($_POST['color']);
        $c->query("UPDATE resumes SET font='$font',color='$color' WHERE id=$rid");
        echo json_encode(['s'=>1]);exit();
    }
}

$page=$_GET['page']??'home';$rid=(int)($_GET['id']??0);
if($page=='builder'&&$rid>0){
    $c=db();$resume=$c->query("SELECT*FROM resumes WHERE id=$rid")->fetch_assoc();
    $personal=$c->query("SELECT*FROM personal_info WHERE resume_id=$rid")->fetch_assoc();
    $exp=$c->query("SELECT*FROM experience WHERE resume_id=$rid ORDER BY id");
    $edu=$c->query("SELECT*FROM education WHERE resume_id=$rid ORDER BY id");
    $skills=$c->query("SELECT*FROM skills WHERE resume_id=$rid ORDER BY id");
    $accent=$resume['color']??'#2563eb';$font=$resume['font']??'inter';
}
if($page=='home'){$c=db();$saved=$c->query("SELECT*FROM resumes ORDER BY updated_at DESC");}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>CareerCraft — ATS Resume Builder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Merriweather:wght@400;700&family=Lato:wght@300;400;700;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--bg:#f8fafc;--s:#fff;--t:#0f172a;--t2:#64748b;--b:#e2e8f0;--r:16px;--sh:0 1px 3px rgba(0,0,0,0.05)}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--t);min-height:100vh}
        
        /* Landing */
        .nav{background:rgba(255,255,255,0.9);backdrop-filter:blur(20px);border-bottom:1px solid var(--b);padding:14px 0;position:sticky;top:0;z-index:100}
        .nav-inner{max-width:1200px;margin:0 auto;padding:0 24px;display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:22px;font-weight:800;color:var(--t);text-decoration:none;display:flex;align-items:center;gap:8px}
        .logo-dot{width:32px;height:32px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px}
        .btn{padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;font-family:'Inter',sans-serif;transition:all .2s;text-decoration:none}
        .btn-p{background:#2563eb;color:#fff}.btn-p:hover{background:#1d4ed8;transform:translateY(-1px)}
        .btn-o{background:transparent;border:1.5px solid var(--b);color:var(--t)}.btn-o:hover{border-color:#2563eb;color:#2563eb}
        .btn-d{background:transparent;color:#ef4444;padding:4px 8px;font-size:11px}.btn-d:hover{background:#fef2f2}
        .btn-sm{padding:5px 10px;font-size:11px}
        .hero{text-align:center;padding:60px 20px 40px;max-width:700px;margin:0 auto}
        .hero h1{font-size:42px;font-weight:900;letter-spacing:-1px;margin-bottom:12px;line-height:1.2}
        .hero span{background:linear-gradient(135deg,#2563eb,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .hero p{color:var(--t2);font-size:16px;margin-bottom:30px;line-height:1.6}
        
        .section-title{text-align:center;font-size:24px;font-weight:800;margin:30px 0 20px}
        .template-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;max-width:1100px;margin:0 auto;padding:0 24px 40px}
        .tcard{background:var(--s);border-radius:var(--r);padding:24px;border:2px solid var(--b);cursor:pointer;transition:all .3s;text-align:center}
        .tcard:hover{border-color:#2563eb;transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,0.1)}
        .tcard.sel{border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,0.1)}
        .tpreview{height:280px;border-radius:10px;margin-bottom:16px;border:1px solid var(--b);position:relative;overflow:hidden}
        .tpreview.harvard{background:linear-gradient(180deg,#fff 0%,#fff 30%,#f1f5f9 30%,#f1f5f9 32%,#fff 32%,#fff 100%)}
        .tpreview.mckinsey{background:linear-gradient(180deg,#1e293b 0%,#1e293b 20%,#fff 20%,#fff 100%)}
        .tpreview.google{background:linear-gradient(180deg,#fff 0%,#fff 85%,#2563eb 85%,#2563eb 100%)}
        .tpreview.stanford{background:linear-gradient(180deg,#fff 0%,#fff 25%,#e2e8f0 25%,#e2e8f0 27%,#fff 27%,#fff 100%)}
        .tname{font-size:16px;font-weight:700;margin-bottom:4px}
        .tdesc{font-size:12px;color:var(--t2);margin-bottom:10px}
        .tag{display:inline-block;padding:3px 10px;background:#f0fdf4;color:#166534;border-radius:12px;font-size:10px;font-weight:600;border:1px solid #86efac}
        
        .saved-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;max-width:1100px;margin:0 auto 40px;padding:0 24px}
        .saved-card{background:var(--s);border-radius:var(--r);padding:20px;border:1px solid var(--b);text-decoration:none;color:inherit;display:flex;align-items:center;gap:14px;transition:all .3s}
        .saved-card:hover{border-color:#2563eb;box-shadow:0 4px 20px rgba(0,0,0,0.08);transform:translateY(-2px)}
        .saved-icon{width:44px;height:44px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
        .saved-info h4{font-size:14px;font-weight:600}.saved-info p{font-size:11px;color:var(--t2)}
        .empty{padding:40px;text-align:center;color:var(--t2)}
        
        /* Builder */
        .btop{background:var(--s);border-bottom:1px solid var(--b);padding:10px 0;position:sticky;top:0;z-index:100}
        .blayout{max-width:1400px;margin:0 auto;padding:20px 24px;display:grid;grid-template-columns:400px 1fr;gap:20px;min-height:calc(100vh - 50px)}
        .editor{display:flex;flex-direction:column;gap:12px;overflow-y:auto;max-height:calc(100vh - 90px);position:sticky;top:70px}
        .editor::-webkit-scrollbar{width:4px}.editor::-webkit-scrollbar-thumb{background:var(--b);border-radius:10px}
        .card{background:var(--s);border-radius:var(--r);padding:18px;border:1px solid var(--b);box-shadow:var(--sh)}
        .card-h{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;cursor:pointer;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
        .card-b{display:none}.card-b.open{display:block}
        .fg{margin-bottom:10px}.fl{display:block;font-size:10px;font-weight:600;color:var(--t2);margin-bottom:3px;text-transform:uppercase;letter-spacing:.4px}
        .fi,.ft{width:100%;padding:8px 10px;border:1.5px solid var(--b);border-radius:6px;font-size:12px;font-family:'Inter',sans-serif;background:#f8fafc;transition:all .2s}
        .fi:focus,.ft:focus{outline:none;border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.08)}
        .ft{resize:vertical;min-height:50px}.fr{display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .ir{display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:#f8fafc;border-radius:6px;margin-bottom:4px;font-size:11px;border:1px solid var(--b)}
        .add-btn{width:100%;padding:7px;border:1.5px dashed var(--b);border-radius:6px;background:transparent;color:#2563eb;font-weight:600;font-size:11px;cursor:pointer;margin-top:4px}.add-btn:hover{border-color:#2563eb;background:rgba(37,99,235,0.04)}
        
        /* Preview */
        .preview-wrap{background:var(--s);border-radius:var(--r);box-shadow:0 8px 40px rgba(0,0,0,0.08);overflow:hidden}
        .ptool{padding:10px 16px;border-bottom:1px solid var(--b);display:flex;justify-content:space-between;align-items:center;background:#fafbfc;font-size:11px;color:var(--t2)}
        .style-opts{display:flex;gap:8px;align-items:center}
        .cswatch{width:20px;height:20px;border-radius:50%;cursor:pointer;border:2px solid transparent;transition:all .2s}
        .cswatch:hover,.cswatch.act{border-color:var(--t);transform:scale(1.2)}
        .fpick{padding:4px 6px;border:1px solid var(--b);border-radius:5px;font-size:10px;cursor:pointer}
        .resume-doc{padding:40px 48px;background:#fff;font-family:'<?=$font??'Inter'?>',sans-serif;font-size:10.5px;line-height:1.5;color:#1e293b;min-height:1000px}
        .rname{font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-.5px;margin-bottom:2px}
        .rtitle{font-size:12px;color:#64748b;font-weight:500;margin-bottom:5px}
        .rcontact{display:flex;gap:14px;flex-wrap:wrap;font-size:10px;color:#64748b;margin-bottom:12px;padding-bottom:10px;border-bottom:1.5px solid <?=$accent??'#2563eb'?>}
        .rcontact i{color:<?=$accent??'#2563eb'?>;margin-right:2px;font-size:9px}
        .rsec{margin-bottom:14px}.rsectitle{font-size:11px;font-weight:700;color:<?=$accent??'#2563eb'?>;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;padding-bottom:2px;border-bottom:1px solid #e5e7eb}
        .rsum{font-size:10.5px;color:#475569;line-height:1.6}
        .ritem{margin-bottom:10px}.ritem-head{display:flex;justify-content:space-between;margin-bottom:2px}
        .ritem-title{font-weight:700;font-size:11.5px}.ritem-sub{font-weight:500;color:#64748b;font-size:11px}.ritem-date{font-size:10px;color:#94a3b8;white-space:nowrap}
        .ritem-desc{font-size:10px;color:#475569;line-height:1.5;padding-left:12px;list-style-type:disc}.ritem-desc li{margin-bottom:1px}
        .rskills{display:flex;flex-wrap:wrap;gap:6px}.rskill{padding:3px 9px;background:<?=$accent??'#2563eb'?>0D;border:1px solid <?=$accent??'#2563eb'?>20;border-radius:10px;font-size:9.5px;font-weight:500;color:<?=$accent??'#2563eb'?>}
        .rproject .ritem-title{font-size:11px}.rproject .ritem-sub{font-size:10px}
        .empty-preview{text-align:center;padding:60px;color:#94a3b8;font-size:13px}
        
        @media print{body{background:#fff}.btop,.editor,.ptool{display:none!important}.blayout{display:block;padding:0}.preview-wrap{box-shadow:none;border:none;border-radius:0}.resume-doc{padding:0;min-height:auto}}
        @media(max-width:1000px){.blayout{grid-template-columns:1fr}.editor{position:static;max-height:none}}
    </style>
</head>
<body>

<?php if($page=='home'):?>
<nav class="nav"><div class="nav-inner">
    <a href="<?=SITE_URL?>" class="logo"><div class="logo-dot">C</div>CareerCraft</a>
    <a href="#templates" class="btn btn-p btn-sm">Create Resume</a>
</div></nav>

<div class="hero">
    <h1>Build an <span>ATS-Friendly</span> Resume in Minutes</h1>
    <p>4 professionally designed templates optimized for Applicant Tracking Systems. Get past the robots and land interviews.</p>
    <a href="#templates" class="btn btn-p" style="font-size:15px;padding:14px 28px;">Choose a Template ↓</a>
</div>

<div id="templates"><div class="section-title">Select Your Template</div>
<div class="template-grid">
    <div class="tcard" onclick="selectTemplate('harvard',this)">
        <div class="tpreview harvard"></div>
        <div class="tname">Harvard</div>
        <div class="tdesc">Clean, academic style. Perfect for consulting & finance.</div>
        <span class="tag">ATS Optimized</span>
    </div>
    <div class="tcard" onclick="selectTemplate('mckinsey',this)">
        <div class="tpreview mckinsey"></div>
        <div class="tname">McKinsey</div>
        <div class="tdesc">Bold header. Stands out for leadership roles.</div>
        <span class="tag">ATS Optimized</span>
    </div>
    <div class="tcard" onclick="selectTemplate('google',this)">
        <div class="tpreview google"></div>
        <div class="tname">Google</div>
        <div class="tdesc">Modern tech style. Ideal for engineering & product.</div>
        <span class="tag">ATS Optimized</span>
    </div>
    <div class="tcard" onclick="selectTemplate('stanford',this)">
        <div class="tpreview stanford"></div>
        <div class="tname">Stanford</div>
        <div class="tdesc">Elegant with separator lines. Great for research & academia.</div>
        <span class="tag">ATS Optimized</span>
    </div>
</div>
<div style="text-align:center;margin-bottom:40px;display:none" id="createWrap">
    <button class="btn btn-p" style="font-size:16px;padding:16px 36px" onclick="createResume()">Start Building →</button>
</div></div>

<div style="max-width:1100px;margin:0 auto;padding:0 24px">
    <div class="section-title">Your Resumes</div>
    <?php if($saved&&$saved->num_rows>0):?>
    <div class="saved-grid">
        <?php while($r=$saved->fetch_assoc()):?>
        <a href="?page=builder&id=<?=$r['id']?>" class="saved-card">
            <div class="saved-icon">📄</div>
            <div class="saved-info"><h4><?=htmlspecialchars($r['title'])?></h4><p><?=ucfirst($r['template'])?> · <?=date('M d',strtotime($r['created_at']))?></p></div>
        </a>
        <?php endwhile;?>
    </div>
    <?php else:?>
    <div class="empty"><div style="font-size:40px;margin-bottom:10px">📂</div><h3>No resumes yet</h3><p>Select a template above to get started!</p></div>
    <?php endif;?>
</div>

<script>
let sel='';function selectTemplate(t,el){sel=t;document.querySelectorAll('.tcard').forEach(c=>c.classList.remove('sel'));el.classList.add('sel');document.getElementById('createWrap').style.display='block';document.getElementById('createWrap').scrollIntoView({behavior:'smooth'})}
function createResume(){if(!sel)return alert('Select a template!');const t=prompt('Resume name:','My Resume');if(!t)return;fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=create&template='+sel+'&title='+encodeURIComponent(t)}).then(r=>r.json()).then(d=>{if(d.s)window.location.href='?page=builder&id='+d.id})}
</script>

<?php elseif($page=='builder'&&$rid>0):?>
<div class="btop"><div class="nav-inner">
    <a href="<?=SITE_URL?>" class="logo"><div class="logo-dot">C</div>CareerCraft</a>
    <div style="display:flex;gap:8px;align-items:center">
        <span style="font-size:11px;color:#166534;background:#f0fdf4;padding:4px 10px;border-radius:12px;border:1px solid #86efac;font-weight:600">✓ ATS Ready</span>
        <a href="<?=SITE_URL?>" class="btn btn-o btn-sm">← Back</a>
        <button class="btn btn-o btn-sm" onclick="window.print()">Print</button>
        <button class="btn btn-p btn-sm" onclick="window.print()">Download PDF</button>
    </div>
</div></div>

<div class="blayout">
<div class="editor">
    <div class="card"><div class="card-h" onclick="this.nextElementSibling.classList.toggle('open')">👤 Personal Info</div>
    <div class="card-b open"><form onsubmit="savePersonal(event)">
        <div class="fr"><div class="fg"><input class="fi" name="full_name" value="<?=htmlspecialchars($personal['full_name']??'')?>" placeholder="Full Name" required></div><div class="fg"><input class="fi" name="job_title" value="<?=htmlspecialchars($personal['job_title']??'')?>" placeholder="Job Title"></div></div>
        <div class="fr"><div class="fg"><input class="fi" type="email" name="email" value="<?=htmlspecialchars($personal['email']??'')?>" placeholder="Email"></div><div class="fg"><input class="fi" name="phone" value="<?=htmlspecialchars($personal['phone']??'')?>" placeholder="Phone"></div></div>
        <div class="fr"><div class="fg"><input class="fi" name="location" value="<?=htmlspecialchars($personal['location']??'')?>" placeholder="Location"></div><div class="fg"><input class="fi" name="linkedin" value="<?=htmlspecialchars($personal['linkedin']??'')?>" placeholder="LinkedIn URL"></div></div>
        <div class="fg"><textarea class="ft" name="summary" rows="2" placeholder="Professional summary..."><?=htmlspecialchars($personal['summary']??'')?></textarea></div>
        <button class="btn btn-p" style="width:100%;justify-content:center;font-size:11px">Save</button>
    </form></div></div>
    
    <div class="card"><div class="card-h" onclick="this.nextElementSibling.classList.toggle('open')">💼 Experience</div>
    <div class="card-b open"><div id="expList">
        <?php if($exp):while($e=$exp->fetch_assoc()):?>
        <div class="ir"><div><strong><?=htmlspecialchars($e['position'])?></strong><br><small><?=htmlspecialchars($e['company'])?></small></div><button class="btn btn-d" onclick="delExp(<?=$e['id']?>)">×</button></div>
        <?php endwhile;endif;?>
    </div><form onsubmit="addExp(event)" style="margin-top:8px">
        <div class="fr"><input class="fi" name="company" placeholder="Company" required><input class="fi" name="position" placeholder="Position" required></div>
        <div class="fr"><input class="fi" name="location" placeholder="Location"><input class="fi" type="month" name="start_date"></div>
        <div class="fr"><input class="fi" type="month" name="end_date" id="ee"><label style="font-size:10px"><input type="checkbox" name="current_job" onchange="document.getElementById('ee').disabled=this.checked"> Current</label></div>
        <textarea class="ft" name="description" rows="2" placeholder="• Achievement one&#10;• Achievement two"></textarea>
        <button class="add-btn">+ Add Experience</button>
    </form></div></div>
    
    <div class="card"><div class="card-h" onclick="this.nextElementSibling.classList.toggle('open')">🎓 Education</div>
    <div class="card-b"><div id="eduList">
        <?php if($edu):while($ed=$edu->fetch_assoc()):?>
        <div class="ir"><div><strong><?=htmlspecialchars($ed['school'])?></strong><br><small><?=htmlspecialchars($ed['degree'])?></small></div><button class="btn btn-d" onclick="delEdu(<?=$ed['id']?>)">×</button></div>
        <?php endwhile;endif;?>
    </div><form onsubmit="addEdu(event)" style="margin-top:8px">
        <div class="fr"><input class="fi" name="school" placeholder="School" required><input class="fi" name="degree" placeholder="Degree" required></div>
        <div class="fr"><input class="fi" name="field" placeholder="Field of Study"><input class="fi" name="gpa" placeholder="GPA"></div>
        <div class="fr"><input class="fi" type="month" name="start_date"><input class="fi" type="month" name="end_date"></div>
        <button class="add-btn">+ Add Education</button>
    </form></div></div>
    
    <div class="card"><div class="card-h" onclick="this.nextElementSibling.classList.toggle('open')">🛠️ Skills</div>
    <div class="card-b"><div id="skillList">
        <?php if($skills):while($sk=$skills->fetch_assoc()):?>
        <div class="ir"><span><strong><?=htmlspecialchars($sk['skill_name'])?></strong> <small><?=$sk['level']?></small></span><button class="btn btn-d" onclick="delSkill(<?=$sk['id']?>)">×</button></div>
        <?php endwhile;endif;?>
    </div><form onsubmit="addSkill(event)" style="margin-top:8px">
        <div class="fr"><input class="fi" name="skill_name" placeholder="Skill" required><select class="fi" name="level"><option>Beginner</option><option selected>Intermediate</option><option>Advanced</option><option>Expert</option></select></div>
        <button class="add-btn">+ Add Skill</button>
    </form></div></div>
</div>

<div class="preview-wrap">
    <div class="ptool"><span>Preview</span>
        <div class="style-opts">
            <span class="cswatch <?=$accent=='#2563eb'?'act':''?>" style="background:#2563eb" onclick="changeColor('#2563eb')"></span>
            <span class="cswatch <?=$accent=='#059669'?'act':''?>" style="background:#059669" onclick="changeColor('#059669')"></span>
            <span class="cswatch <?=$accent=='#7c3aed'?'act':''?>" style="background:#7c3aed" onclick="changeColor('#7c3aed')"></span>
            <span class="cswatch <?=$accent=='#dc2626'?'act':''?>" style="background:#dc2626" onclick="changeColor('#dc2626')"></span>
            <select class="fpick" onchange="changeFont(this.value)"><option value="inter" <?=$font=='inter'?'selected':''?>>Inter</option><option value="lato" <?=$font=='lato'?'selected':''?>>Lato</option><option value="merriweather" <?=$font=='merriweather'?'selected':''?>>Merriweather</option></select>
        </div>
    </div>
    <div class="resume-doc">
        <div class="rname"><?=htmlspecialchars($personal['full_name']??'Your Name')?></div>
        <div class="rtitle"><?=htmlspecialchars($personal['job_title']??'Professional Title')?></div>
        <div class="rcontact">
            <?php if(!empty($personal['email'])):?><span><i class="fas fa-envelope"></i><?=htmlspecialchars($personal['email'])?></span><?php endif?>
            <?php if(!empty($personal['phone'])):?><span><i class="fas fa-phone"></i><?=htmlspecialchars($personal['phone'])?></span><?php endif?>
            <?php if(!empty($personal['location'])):?><span><i class="fas fa-map-marker-alt"></i><?=htmlspecialchars($personal['location'])?></span><?php endif?>
            <?php if(!empty($personal['linkedin'])):?><span><i class="fab fa-linkedin"></i><?=htmlspecialchars($personal['linkedin'])?></span><?php endif?>
        </div>
        <?php if(!empty($personal['summary'])):?><div class="rsec"><div class="rsectitle">Summary</div><div class="rsum"><?=nl2br(htmlspecialchars($personal['summary']))?></div></div><?php endif?>
        
        <?php if($exp&&$exp->num_rows>0):?><div class="rsec"><div class="rsectitle">Experience</div>
        <?php $exp->data_seek(0);while($e=$exp->fetch_assoc()):?>
        <div class="ritem"><div class="ritem-head"><div><span class="ritem-title"><?=htmlspecialchars($e['position'])?></span> | <span class="ritem-sub"><?=htmlspecialchars($e['company'])?></span></div><div class="ritem-date"><?=htmlspecialchars($e['start_date'])?> - <?=$e['current_job']?'Present':htmlspecialchars($e['end_date'])?></div></div>
        <?php if(!empty($e['description'])):?><ul class="ritem-desc"><?php foreach(explode("\n",$e['description']) as $l):?><?php if(trim($l)):?><li><?=htmlspecialchars(ltrim($l,'• '))?></li><?php endif?><?php endforeach?></ul><?php endif?></div>
        <?php endwhile?></div><?php endif?>
        
        <?php if($edu&&$edu->num_rows>0):?><div class="rsec"><div class="rsectitle">Education</div>
        <?php $edu->data_seek(0);while($ed=$edu->fetch_assoc()):?>
        <div class="ritem"><div class="ritem-head"><div><span class="ritem-title"><?=htmlspecialchars($ed['school'])?></span> | <span class="ritem-sub"><?=htmlspecialchars($ed['degree'])?></span></div><div class="ritem-date"><?=htmlspecialchars($ed['start_date'])?> - <?=htmlspecialchars($ed['end_date'])?></div></div>
        <?php if(!empty($ed['gpa'])):?><div style="font-size:9px;color:#64748b">GPA: <?=htmlspecialchars($ed['gpa'])?></div><?php endif?></div>
        <?php endwhile?></div><?php endif?>
        
        <?php if($skills&&$skills->num_rows>0):?><div class="rsec"><div class="rsectitle">Skills</div><div class="rskills">
        <?php $skills->data_seek(0);while($sk=$skills->fetch_assoc()):?>
        <span class="rskill"><?=htmlspecialchars($sk['skill_name'])?></span>
        <?php endwhile?></div></div><?php endif?>
    </div>
</div>
</div>

<script>
const RID=<?=$rid?>;
function savePersonal(e){e.preventDefault();const f=new FormData(e.target);f.append('ajax','save_personal');f.append('resume_id',RID);fetch('',{method:'POST',body:new URLSearchParams(f)}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function addExp(e){e.preventDefault();const f=new FormData(e.target);f.append('ajax','add_exp');f.append('resume_id',RID);fetch('',{method:'POST',body:new URLSearchParams(f)}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function delExp(id){if(!confirm('Delete?'))return;fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=del_exp&id='+id+'&resume_id='+RID}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function addEdu(e){e.preventDefault();const f=new FormData(e.target);f.append('ajax','add_edu');f.append('resume_id',RID);fetch('',{method:'POST',body:new URLSearchParams(f)}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function delEdu(id){if(!confirm('Delete?'))return;fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=del_edu&id='+id+'&resume_id='+RID}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function addSkill(e){e.preventDefault();const f=new FormData(e.target);f.append('ajax','add_skill');f.append('resume_id',RID);fetch('',{method:'POST',body:new URLSearchParams(f)}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function delSkill(id){if(!confirm('Delete?'))return;fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=del_skill&id='+id+'&resume_id='+RID}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function changeColor(c){fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=update_style&font=<?=$font?>&color='+c+'&resume_id='+RID}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
function changeFont(f){fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'ajax=update_style&font='+f+'&color=<?=$accent?>&resume_id='+RID}).then(r=>r.json()).then(d=>{if(d.s)location.reload()})}
</script>
<?php endif?>
</body>
</html>