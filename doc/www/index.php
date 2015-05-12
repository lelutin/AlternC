<?php
header("Content-Type: text/html; charset=UTF-8");

require_once("head.php");

$f=fopen("../install.php","rb");
$headings=array();
$cur=array(); $id=""; $name="";
while ($s=fgets($f,1024)) {
  echo $s;
  if (preg_match('#<h1 id="([^"]*)">([^<]*)</h1>#',$s,$mat)) {
    if ($id) $headings[]=array("id"=>$id, "name"=>$name, "cur"=>$cur);
    $id=$mat[1]; $name=$mat[2];
    $cur=array();
  }
  if (preg_match('#<h2 id="([^"]*)">([^<]*)</h2>#',$s,$mat)) {
    $cur[]=array("id"=>$mat[1], "name"=>$mat[2]);
  }
}
if ($id) $headings[]=array("id"=>$id, "name"=>$name, "cur"=>$cur);

ob_start();
foreach($headings as $v) {
?>                
  <li>
    <a href="#<?php echo $v["id"]; ?>"><?php echo $v["name"]; ?></a>
<?php if (count($v["cur"])) { ?>
  <ul class="nav">
    <?php foreach($v["cur"] as $vv) { ?>
    <li><a href="#<?php echo $vv["id"]; ?>"><?php echo $vv["name"]; ?></a></li>
    <?php } ?>
  </ul>
  <?php } ?>
</li>
<?php
}
    $index=ob_get_clean();
require_once("foot.php");

