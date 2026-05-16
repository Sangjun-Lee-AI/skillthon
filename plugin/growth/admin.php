<?php
define('_GNUBOARD_', true);
define('G5_IS_ADMIN', true);

$g5_path = dirname(dirname(dirname(__FILE__)));
require_once($g5_path.'/common.php');

if ($is_admin !== 'super') {
    echo '<script>alert("관리자만 접근할 수 있습니다.");history.back();</script>';
    exit;
}

if (!defined('GROWTH_TABLE') || !function_exists('growth_get_config')) {
    die('Growth 플러그인 확장 파일(extend/growth.extend.php)이 필요합니다.');
}

// Save
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['growth_save'])) {
    $token = isset($_POST['_token']) ? $_POST['_token'] : '';
    if (!isset($_SESSION['growth_token']) || $token !== $_SESSION['growth_token']) {
        $msg = 'error:잘못된 요청입니다.';
    } else {
        $gc_use             = isset($_POST['gc_use']) ? 1 : 0;
        $gc_text            = strip_tags(trim($_POST['gc_text'] ?? ''));
        $gc_color           = preg_replace('/[^#0-9a-fA-F]/', '', $_POST['gc_color'] ?? '#3a8afd');
        $gc_bounce          = isset($_POST['gc_bounce']) ? 1 : 0;
        $gc_top_settle      = strip_tags(trim($_POST['gc_top_settle'] ?? ''));
        $gc_badge_use       = isset($_POST['gc_badge_use']) ? 1 : 0;
        $gc_badge_text      = strip_tags(trim($_POST['gc_badge_text'] ?? ''));
        $gc_badge_color     = preg_replace('/[^#0-9a-fA-F]/', '', $_POST['gc_badge_color'] ?? '#ff006c');
        $gc_layout_vertical = isset($_POST['gc_layout_vertical']) ? 1 : 0;

        sql_query("UPDATE `".GROWTH_TABLE."` SET
            gc_use             = '{$gc_use}',
            gc_text            = '".sql_real_escape_string($gc_text)."',
            gc_color           = '".sql_real_escape_string($gc_color)."',
            gc_bounce          = '{$gc_bounce}',
            gc_top_settle      = '".sql_real_escape_string($gc_top_settle)."',
            gc_badge_use       = '{$gc_badge_use}',
            gc_badge_text      = '".sql_real_escape_string($gc_badge_text)."',
            gc_badge_color     = '".sql_real_escape_string($gc_badge_color)."',
            gc_layout_vertical = '{$gc_layout_vertical}'
        WHERE gc_id = 1");

        $msg = 'success:저장되었습니다.';
    }
}

$_SESSION['growth_token'] = md5(uniqid(mt_rand(), true));
$cf = growth_get_config();
if (!$cf) {
    $cf = array('gc_use'=>0,'gc_text'=>'계좌이체로 구매시 30% 소득공제','gc_color'=>'#3a8afd',
        'gc_bounce'=>1,'gc_top_settle'=>'','gc_badge_use'=>0,'gc_badge_text'=>'',
        'gc_badge_color'=>'#ff006c','gc_layout_vertical'=>1);
}

$settle_options = array(
    '' => '선택 안함',
    '계좌이체' => '계좌이체',
    '무통장'   => '무통장입금',
    '가상계좌' => '가상계좌',
    '신용카드' => '신용카드',
    '휴대폰'   => '휴대폰',
    'KAKAOPAY' => '카카오페이'
);
?><!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Growth - <?php echo $config['cf_title']; ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Malgun Gothic',-apple-system,sans-serif;background:#f5f7fa;color:#333;font-size:14px}
.wrap{max-width:800px;margin:40px auto;padding:0 20px}
.back{display:inline-block;margin-bottom:20px;color:#666;text-decoration:none;font-size:13px}
.back:hover{color:#3a8afd}
.hd{background:#3a8afd;color:#fff;padding:30px 40px;border-radius:12px 12px 0 0}
.hd h1{font-size:24px;font-weight:700}
.hd p{margin-top:8px;opacity:.85;font-size:13px}
.bd{background:#fff;border-radius:0 0 12px 12px;box-shadow:0 2px 12px rgba(0,0,0,.08);padding:30px 40px}
.sec{margin-bottom:30px;padding-bottom:30px;border-bottom:1px solid #eee}
.sec:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.sec h2{font-size:16px;font-weight:700;margin-bottom:20px;color:#222}
.row{display:flex;align-items:center;margin-bottom:16px}
.row label.lbl{width:160px;font-weight:600;font-size:13px;color:#555;flex-shrink:0}
.row .fld{flex:1}
.row input[type="text"],.row select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:6px;font-size:14px;transition:border-color .2s}
.row input[type="text"]:focus,.row select:focus{outline:none;border-color:#3a8afd;box-shadow:0 0 0 3px rgba(58,138,253,.1)}
.row input[type="color"]{width:50px;height:40px;border:1px solid #ddd;border-radius:6px;cursor:pointer;padding:2px}
.cg{display:flex;align-items:center;gap:10px}
.cp{width:100px;height:36px;border-radius:6px;border:1px solid #ddd}
.tgl{position:relative;display:inline-block;width:52px;height:28px}
.tgl input{opacity:0;width:0;height:0}
.tgl .sl{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#ccc;border-radius:28px;transition:.3s}
.tgl .sl:before{content:"";position:absolute;height:22px;width:22px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.tgl input:checked+.sl{background:#3a8afd}
.tgl input:checked+.sl:before{transform:translateX(24px)}
.btn{display:block;width:100%;padding:14px;background:#3a8afd;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer;transition:background .2s;margin-top:20px}
.btn:hover{background:#2176f1}
.msg{padding:12px 16px;border-radius:8px;margin-bottom:20px;font-weight:600}
.msg.s{background:#e8f5e9;color:#2e7d32}
.msg.e{background:#fce4ec;color:#c62828}
.pv{background:#f8f9fa;border-radius:8px;padding:20px;text-align:center;margin-top:10px}
@keyframes growthBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
.growth-balloon{transition:all .3s ease;display:inline-block}
.growth-bounce{animation:growthBounce 1.2s ease-in-out infinite}
</style>
</head>
<body>
<div class="wrap">
    <a href="<?php echo G5_ADMIN_URL; ?>/shop_admin/configform.php" class="back">&larr; 쇼핑몰 관리로 돌아가기</a>
    <div class="hd">
        <h1>Growth</h1>
        <p>결제수단 프로모션을 통해 매출을 성장시키세요</p>
    </div>
    <div class="bd">
<?php
if ($msg) {
    $parts = explode(':', $msg, 2);
    echo '<div class="msg '.($parts[0]==='success'?'s':'e').'">'.$parts[1].'</div>';
}
?>
        <form method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['growth_token']; ?>">
        <input type="hidden" name="growth_save" value="1">

        <div class="sec">
            <h2>기본 설정</h2>
            <div class="row">
                <label class="lbl">Growth 사용</label>
                <div class="fld">
                    <label class="tgl"><input type="checkbox" name="gc_use" value="1" <?php echo $cf['gc_use']?'checked':''; ?>><span class="sl"></span></label>
                </div>
            </div>
        </div>

        <div class="sec">
            <h2>말풍선 설정</h2>
            <div class="row">
                <label class="lbl">말풍선 문구</label>
                <div class="fld"><input type="text" name="gc_text" value="<?php echo htmlspecialchars($cf['gc_text']); ?>" placeholder="계좌이체로 구매시 30% 소득공제"></div>
            </div>
            <div class="row">
                <label class="lbl">말풍선 색상</label>
                <div class="fld">
                    <div class="cg">
                        <input type="color" name="gc_color" id="gc_color" value="<?php echo $cf['gc_color']?:'#3a8afd'; ?>">
                        <div class="cp" id="gc_color_pv" style="background:<?php echo $cf['gc_color']?:'#3a8afd'; ?>"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="lbl">바운스 효과</label>
                <div class="fld">
                    <label class="tgl"><input type="checkbox" name="gc_bounce" value="1" <?php echo $cf['gc_bounce']?'checked':''; ?>><span class="sl"></span></label>
                </div>
            </div>
            <div class="row">
                <label class="lbl">미리보기</label>
                <div class="fld">
                    <div class="pv">
                        <div id="pv_balloon" class="growth-balloon <?php echo $cf['gc_bounce']?'growth-bounce':''; ?>">
                            <span id="pv_text" style="display:inline-block;background:<?php echo $cf['gc_color']?:'#3a8afd'; ?>;color:#fff;font-size:12px;font-weight:bold;padding:6px 12px;border-radius:20px;position:relative;"><?php echo htmlspecialchars($cf['gc_text']?:'계좌이체로 구매시 30% 소득공제'); ?><span id="pv_arrow" style="position:absolute;bottom:-6px;left:50%;margin-left:-6px;width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:6px solid <?php echo $cf['gc_color']?:'#3a8afd'; ?>;"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sec">
            <h2>결제수단 설정</h2>
            <div class="row">
                <label class="lbl">세로 1열 레이아웃</label>
                <div class="fld">
                    <label class="tgl"><input type="checkbox" name="gc_layout_vertical" value="1" <?php echo $cf['gc_layout_vertical']?'checked':''; ?>><span class="sl"></span></label>
                </div>
            </div>
            <div class="row">
                <label class="lbl">최상단 결제수단</label>
                <div class="fld">
                    <select name="gc_top_settle">
                    <?php foreach ($settle_options as $val => $name) { ?>
                        <option value="<?php echo $val; ?>" <?php echo $cf['gc_top_settle']===$val?'selected':''; ?>><?php echo $name; ?></option>
                    <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="sec">
            <h2>뱃지 설정</h2>
            <div class="row">
                <label class="lbl">뱃지 사용</label>
                <div class="fld">
                    <label class="tgl"><input type="checkbox" name="gc_badge_use" value="1" <?php echo $cf['gc_badge_use']?'checked':''; ?>><span class="sl"></span></label>
                </div>
            </div>
            <div class="row">
                <label class="lbl">뱃지 문구</label>
                <div class="fld"><input type="text" name="gc_badge_text" value="<?php echo htmlspecialchars($cf['gc_badge_text']); ?>" placeholder="30% 소득공제"></div>
            </div>
            <div class="row">
                <label class="lbl">뱃지 색상</label>
                <div class="fld">
                    <div class="cg">
                        <input type="color" name="gc_badge_color" id="gc_badge_color" value="<?php echo $cf['gc_badge_color']?:'#ff006c'; ?>">
                        <div class="cp" id="gc_badge_color_pv" style="background:<?php echo $cf['gc_badge_color']?:'#ff006c'; ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn">저장</button>
        </form>
    </div>
</div>
<script>
document.getElementById('gc_color').addEventListener('input',function(){
    document.getElementById('gc_color_pv').style.background=this.value;
    document.getElementById('pv_text').style.background=this.value;
    document.getElementById('pv_arrow').style.borderTopColor=this.value;
});
document.getElementById('gc_badge_color').addEventListener('input',function(){
    document.getElementById('gc_badge_color_pv').style.background=this.value;
});
document.querySelector('input[name="gc_text"]').addEventListener('input',function(){
    var el=document.getElementById('pv_text');
    var arrow=document.getElementById('pv_arrow');
    el.textContent=this.value||'계좌이체로 구매시 30% 소득공제';
    el.appendChild(arrow);
});
document.querySelector('input[name="gc_bounce"]').addEventListener('change',function(){
    var b=document.getElementById('pv_balloon');
    this.checked?b.classList.add('growth-bounce'):b.classList.remove('growth-bounce');
});
</script>
</body>
</html>
