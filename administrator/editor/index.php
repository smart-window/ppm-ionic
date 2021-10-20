<?php 
  require_once('../../assets/includes/core.php');
  if ($logged !== true || $sm['user']['admin'] == 0) {
    header('Location:'.$sm['config']['site_url'].'admin');
    exit;
  }
  $theme = '';
  $preset = '';
  $themeName = '';
  $pagesDropDown = '';
  $themeLivePreset = '';

  $profileShowId = getData('users','id','where admin = 0 order by popular desc limit 1');
  if($profileShowId == 'noData'){
    $profileShowId = $sm['user']['id'];
  }
  if(isset($_GET['theme']) && isset($_GET['preset'])){

    $theme = secureEncode($_GET['theme']);
    $preset = secureEncode($_GET['preset']);
    $alias = getData('theme_preset','preset_alias','WHERE preset = "'.$preset.'"');
    $checkPreset = checkIfExist('theme_preset','preset',$preset);
    if($checkPreset == 0){
      header('Location: '.$sm['config']['site_url']);
      exit;
    }    

    $checkThemeType = getData('config_themes','type','WHERE folder = "'.$theme.'"');
    if($checkThemeType == 1){
      $themeLivePreset = $sm['settings']['desktopThemePreset'];
      $themeType = 'Desktop';
      $liveDemoUrl = $sm['config']['site_url'].'index.php?preset='.$preset;
    }

    if($checkThemeType == 2){
      $themeLivePreset = $sm['settings']['landingThemePreset'];
      $themeType = 'Landing';
      $liveDemoUrl = $sm['config']['site_url'].'index.php?landing='.$theme.'&landingPreset='.$preset;
    }    

    $themeName = 'Preset '.$alias;
    $themeDesign = '';    

    $themeDesign = getData('theme_settings','setting_val','where theme = "'.$theme.'" and setting = "design_style"');
   
    $themeFilter = 'WHERE theme ="'.$theme.'" AND preset = "'.$preset.'"';
    $sm['editingTheme'] = json_decode(getData('theme_preset','theme_settings',$themeFilter),true);
    while($element = current($sm['editingTheme'])) {
        $key = key($sm['editingTheme']);
        if(isset($sm['editingTheme'][$key]['val'])){
          $val = $sm['editingTheme'][$key]['val'];
          $mysqli->query('UPDATE theme_settings set setting_val = "'.$val.'" 
            where setting = "'.$key.'" and theme = "'.$theme.'"');
        }
        next($sm['editingTheme']);
    } 
    
  } else {
    header('Location:'.$sm['config']['site_url'].'admin');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><title><?= $sm['config']['name']; ?> - <?= $theme; ?> Theme Editor Panel</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="theme-color" content="#ffffff">
<link rel="icon" type="image/png" href="<?= $sm['editingTheme']['favicon']['val']; ?>" sizes="32x32">
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="<?= $sm['config']['site_url']; ?>administrator/editor/css/ui.css">
<link rel="stylesheet" href="<?= $sm['config']['site_url']; ?>administrator/editor/css/global.css">
<link rel="stylesheet" href="<?= $sm['config']['site_url']; ?>administrator/editor/css/editor.css" />
<link rel="stylesheet" href="<?php echo $sm['config']['site_url']; ?>themes/default/css/crossplatform.css"/>
<link rel="stylesheet" type="text/css" href="<?= $sm['config']['site_url']; ?>administrator/editor/css/fontselect.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $sm['config']['site_url']; ?>themes/default/css/vendor/little-widgets.css"/>    
<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet" type="text/css"/>
<style type="text/css" media="screen">
body,
html {
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: #f0f2f4
}
iframe{

}
.box-shadow-iframe{
  box-shadow: 0 8px 14px 0 rgba(0, 0, 0, 0.1), 0 12px 40px 0 rgba(0, 0, 0, 0.09);
}
.box-shadow-btn{
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1), 0 6px 20px 0 rgba(0, 0, 0, 0.09) !important;
}
.header{
  box-shadow:none !important;
}
.selected{
  border:2px solid #5514ed !important;
}
</style>
<?php
$site_config = json_encode($sm['config']);
$site_plugins = json_encode($sm['plugins']);
$l = siteConfig('client');
echo 
'<script>
function request_source(){
    return \'' . $sm['config']['ajax_path'] . '\';
}
function site_title(){
    return \'' . $sm['config']['title'] . '\';
}   
function theme_source(){
    return \'' . $sm['config']['theme_url'] . '\';
}
function site_url(){
    return \'' . $sm['config']['site_url'] . '\';
}   
</script>';
?>
<style>
.liveDemo:before {
  background: #4b367c;
  border-radius: 50%;
  content: "";
  display: inline-block;
  height: 8px;
  margin: 0 8px 0 0;
  width: 8px;
}

</style>
</head>
<body class="store-page fr_main_background edit" style="max-width: 1280px;margin:0 auto;background: #fff;border-right: 1px solid #ddd">

<div >
<form class="editor-sidebar" style="position: absolute;left: 0px;width: 350px">
<div class="ui top attached tabular menu" style="border: 1px solid #ddd;background: #fff;" >
  <a class="item leave" href="#" onclick="close_window();return false;">
    <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg">
      <path fill="#333" d="M8 16c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm0-.9c3.9 0 7.1-3.2 7.1-7.1C15.1 4.1 11.9.9 8 .9 4.1.9.9 4.1.9 8c0 3.9 3.2 7.1 7.1 7.1zM8.7 8l1.4 1.4c.2.2.2.5 0 .7-.2.2-.5.2-.7 0L8 8.7l-1.4 1.4c-.2.2-.5.2-.7 0-.2-.2-.2-.5 0-.7L7.3 8 5.9 6.6c-.2-.2-.2-.5 0-.7.2-.2.5-.2.7 0L8 7.3l1.4-1.4c.2-.2.5-.2.7 0 .2.2.2.5 0 .7L8.7 8z" fill-rule="nonzero"/>
    </svg>
  </a>
  <?php
    $pages = getArrayDSelected('page','theme_settings','where theme = "'.$theme.'" and page <> ""');
    $page = 0;
    $pagesArr = array();
    $i=0; 
    foreach ($pages as $p) { 
      $i++;
      if($i == 1){
        $class = 'active';
      } else {
        $class = '';
      }
      if (strpos($p['page'], 'page') !== false) {
          $page++;
            $pageTab = str_replace('page', "", $p['page']);
            array_push($pagesArr, $p['page']);
            $pageLink = strtolower($pageTab);
            if($pageTab == 'ProOffline'){
              $pageTab = 'Profile Visitors';
            }            
            if($themeType == 'Landing'){
              $pageLink = 'index.php?landing='.$theme.'&landingPreset='.$preset;
            }
            $pagesDropDown.='<a href="#" data-tab="'.$p['page'].'" data-purl="'.$pageLink.'" class="row">
              <div class="text">'.$pageTab.'</div>
            </a>';
            ?>
            <?php if($page == 1){ ?>
              <label class="item <?= $class;?>" style="cursor: pointer;color: #222" data-tab="<?= $p['page']; ?>" data-tab-pages for="toggle">
                Pages  
              </label>
          <?php }
      } else {
        array_push($pagesArr, $p['page']); 
        if($themeType == 'Landing'){
          $pageLink = 'index.php?landing='.$theme.'&landingPreset='.$preset;
        }        
      ?>
        <a class="item <?= $class;?>" data-tab="<?= $p['page']; ?>" style="color: #111">
          <?= $p['page']; ?>
        </a>
        
  <?php } } ?>  

  <input type="checkbox" checked style="display: none" id="toggle"> 
  <div class="dropdown box-shadow-iframe">
    <div class="arrow"></div>
    <?= $pagesDropDown; ?>
  </div>
  <?php if($themeLivePreset != $preset){ ?>
    <a class="ui green tiny button autoSave" style="background: #21C459;color: #fff;cursor: default;">Auto Saving</a>   
    <a href="<?= $liveDemoUrl; ?>" target="_blank" class="ui green tiny button liveDemo box-shadow-btn" style="background: none;color: #4b367c;">Test live demo</a>    
    <a class="ui green tiny button save gradient32" data-live-site="1" style="background: #4b367c">Set as live theme</a>
    <a class="ui green tiny button save gradient32" data-live-site="2" style="background: #43a922;display: none;cursor: default;">Editing live theme</a>
  <?php } else { ?>
    <a class="ui green tiny button autoSave" style="background: #21C459;color: #fff;cursor: default;">Auto Saving</a>       
    <a href="<?= $liveDemoUrl; ?>" target="_blank" class="ui green tiny button liveDemo box-shadow-btn" style="background: none;color: #4b367c;">Test live demo</a>      
    <a class="ui green tiny button save gradient32" data-live-site="2" style="background: #43a922;cursor: default;">Editing live theme</a>
  <?php }?>
</div>

<?php
  $page = 0;
  $i=0;  
  foreach ($pagesArr as $p) {
      $prevBlock = '';
      $blockCount = 0;    
      $i++;
      if($i == 1){
        $class = 'active';
      } else {
        $class = '';
      }
      if(strpos($p, 'page') !== false) {      
        $dropDownPage = str_replace('page', "", $p);
      }
    ?>

    <div class="ui bottom attached tab segment <?= $class; ?>" style="padding: 8px;margin-top: 3.75rem;background: #f0f2f4" data-current-tab="<?= $p; ?>">
          <div class="ui form" >  
    <?php
      $settings = getDataArrayFull('theme_settings','where theme = "'.$theme.'" and page = "'.$p.'" ORDER BY block_orden ASC, setting_orden ASC');
    ?>            
    <?php foreach ($settings as $s) { ?>
      <?php $displayDesignStyle = ''; ?>
      <?php if($prevBlock == ''){ $prevBlock = $s['block']; } ?>
      <?php if($s['setting'] == 'design_style_wide' && $themeDesign != 'Top-Menu'){ $displayDesignStyle = 'none'; }?>
      <?php if($s['setting_require'] != '' && $s['setting_require'] != $themeDesign){ $displayDesignStyle = 'none'; }?>

      <?php if($prevBlock != $s['block']){ ?>
        </div>
      <?php } ?>   
      <?php if($prevBlock != $s['block'] || $blockCount == 0){ ?>
        <div class="ui segment" data-current-block="<?=$s['block']; ?>" data-segment-require="<?=$s['setting_require']; ?>" style="display: <?=$displayDesignStyle; ?>;">
          <h3 class="ui header"><?= $s['block_title']; ?></h3>
      <?php } ?>

          <?php if($s['setting_type'] == 'image'){ ?>
            <div class="ui equal height two column grid img-upload" style="width:88%;padding-top: 15px">
          <?php } else { ?>
            <div class="ui equal height two column grid" data-segment="<?=$s['setting']; ?>" data-block="<?= $s['block']; ?>" data-segment-require="<?=$s['setting_require']; ?>" style="display: <?=$displayDesignStyle; ?>">
          <?php } ?> 

          <?php if($s['setting_type'] != 'font'){ ?>              
          <div class="column">
            <div class="header" style="padding-top: 5px">
              <?= $s['header']; ?>
            </div>

            <?php if($s['subheader'] != ''){?>
              <div class="subheader">
                <?= $s['subheader']; ?>
              </div>
            <?php } ?>
            
            <?php if($s['setting_type'] == 'image'){ ?>
              <div class="subheader">
                <span class="upload" style="color: #7513e4" data-update-image-progress="<?= $s['setting'];?>"></span>
              </div>
            <?php } ?>
          </div>
        <?php } ?> 

          <?php if($s['setting_type'] == 'image'){ ?>
            <div class="column img-preview">
              
                <div class="image" data-update-image="<?= $s['setting'];?>" style="cursor: pointer;">
                  <img src="<?= $s['setting_val']; ?>">
                </div>
              
            </div>
          <?php } ?>  


          <?php if($s['setting_type'] == 'boolean'){ 
            $checked = '';
            if($s['setting_val'] == 'Yes'){
              $checked = 'checked';
            }?>
            <div class="column">
              <fieldset class="ui toggles">
                <div>
                  <input type="checkbox" <?= $checked; ?> id="<?= $s['setting'];?>"  
                  data-theme-setting="<?= $s['setting']; ?>" 
                  data-theme-type="<?= $s['setting_type']; ?>">
                  <label for="<?= $s['setting'];?>">&nbsp;</label>
                </div>
              </fieldset>              
            </div>
          <?php } ?>            

          <?php if($s['setting_type'] == 'color'){ ?>
            <div class="column color-picker cover-image-bg">
              <div class="ui labeled input">
                <?php if (strpos($s['setting_val'], 'gradient') !== false) { ?>
                  <div class="ui label pickr-elem <?= $s['setting_val']; ?>" data-gradient="<?= $s['gradient']; ?>" data-color="<?= $s['setting']; ?>"></div>
                <?php } else { ?> 
                  <div class="ui label pickr-elem" data-color="<?= $s['setting']; ?>" data-gradient="<?= $s['gradient']; ?>"  style="cursor:pointer;background: <?= $s['setting_val']; ?>"></div>
                <?php } ?>
                <input class="pickr-hex" type="text" placeholder=""
                        name="<?= $s['setting']; ?>" data-color-val="<?= $s['setting']; ?>"
                        value="<?= $s['setting_val']; ?>"
                        data-theme-setting="<?= $s['setting']; ?>" 
                        data-theme-gradient="<?= $s['gradient']; ?>" 
                        data-theme-type="<?= $s['setting_type']; ?>"
                >
              </div>
            </div>
          <?php } ?>

          <?php if($s['setting_type'] == 'font'){ ?>
            <div class="column" style="margin-bottom: 10px">
            <div class="header" style="padding-top: 5px;padding-bottom: 10px">
              <?= $s['header']; ?>
            </div>              
              <div class="ui labeled input">
                <input class="update-text-href"  data-font="<?= $s['setting']; ?>" type="text" placeholder=""
                        name="<?= $s['setting']; ?>" data-color-val="<?= $s['setting']; ?>"
                        value="<?= $s['setting_val']; ?>"
                        data-theme-setting="<?= $s['setting']; ?>" 
                        data-theme-type="<?= $s['setting_type']; ?>"
                >
              </div>
            </div>
          <?php } ?>   

          <?php if($s['setting_type'] == 'fontSize'){ ?>
            <div class="column">             
              <div class="ui labeled input">
                <input class="update-text-href" type="number" placeholder=""
                        name="<?= $s['setting']; ?>" data-color-val="<?= $s['setting']; ?>"
                        value="<?= $s['setting_val']; ?>"
                        data-theme-setting="<?= $s['setting']; ?>" 
                        data-theme-type="<?= $s['setting_type']; ?>"
                >
              </div>
            </div>
          <?php } ?>                    

          <?php if($s['setting_type'] == 'designStyle'){ ?>  
          <div class="column" style="padding-top: 15px;display: <?=$displayDesignStyle; ?>"> 
          <?php $select =  explode( ',', $s['setting_options'] );
          foreach ($select as $option) { $selected = ''; ?> 
            <?php if($option == $s['setting_val']){ $selected = 'selected';} ?>           
              <div class="style-icon" style="display: inline-table;margin-right: 5px;">
                <img class="<?= $selected; ?>" style="border-radius: 2px;cursor: pointer;width: 42px" src="<?= $sm['config']['site_url']; ?>administrator/editor/img/<?=$option; ?>.png" data-theme-setting-click
                        data-theme-setting="<?= $s['setting']; ?>" data-theme-setting-val="<?= $option; ?>">
              </div>
            <?php } ?>
          </div>
          <?php } ?> 


          <?php if($s['setting_type'] == 'cardDesign'){ ?>  
          <div class="column" style="padding-top: 15px;"> 
          <?php $select =  explode( ',', $s['setting_options'] );
          foreach ($select as $option) { $selected = ''; ?> 
            <?php if($option == $s['setting_val']){ $selected = 'selected';} ?>           
              <div class="style-icon" style="display: inline-table;margin-right: 8px;margin-bottom: 10px">
                <img class="<?= $selected; ?>" style="border-radius: 2px;cursor: pointer;width: 55px;border: 2px solid #eee" src="<?= $sm['config']['site_url']; ?>administrator/editor/img/<?=$option; ?>.png" data-theme-setting-click
                        data-theme-setting="<?= $s['setting']; ?>" data-theme-setting-val="<?= $option; ?>">
              </div>
            <?php } ?>
          </div>
          <?php } ?> 


        </div>
        <p data-footer-theme-update="<?= $s['setting']; ?>" style="position: absolute;top:18px;right:5px;width: 80px;height: 20px;background: #48ba16;color: #fff;font-weight: 500;border-radius:5px;text-align: center;line-height: 20px;font-size: 10px;display: none">Updated</p>
  
       
      <?php $prevBlock = $s['block'];$blockCount++; } ?>      


    </div>
  </div>
 
</div>
<?php } ?>

</form>
</div>
  <div class="color-picker-panel" style="display: none">
  
    <div class="panel-row" data-use-colors>
      <div class="swatches default-swatches"></div>
    </div>
    <div class="panel-row" data-use-colors>
      <div class="spectrum-map">
        <button id="spectrum-cursor" class="color-cursor"></button>
        <canvas id="spectrum-canvas"></canvas>
      </div>
      <div class="hue-map" data-use-colors>
        <button id="hue-cursor" class="color-cursor"></button>
        <canvas id="hue-canvas"></canvas>
      </div>
    </div>
    <div class="panel-row" data-use-colors>
      <div id="rgb-fields" class="field-group value-fields rgb-fields active">
        <div class="field-group">
          <label for="" class="field-label">R:</label>
          <input type="number" max="255" min="0" id="red" class="field-input rgb-input"/>
        </div>
        <div class="field-group">
          <label for="" class="field-label">G:</label>
          <input type="number" max="255" min="0" id="green" class="field-input rgb-input"/>
        </div>
        <div class="field-group">
          <label for="" class="field-label">B:</label>
          <input type="number" max="255" min="0" id="blue" class="field-input rgb-input"/>
        </div>
      </div>
      <div id="hex-field" class="field-group value-fields hex-field">
        <label for="" class="field-label">Hex:</label>
        <input type="text" id="hex" class="field-input"/>
      </div>
      <button id="mode-toggle" class="button mode-toggle">Mode</button>
    </div>
    <div class="panel-row" data-use-colors>
      <h2 class="panel-header">Select color</h2>
      <button class="button add-swatch selectColor" style="width: 60%;margin-left:7%;display: inline-table;background: #fff;">
        <span id="color-indicator" class="color-indicator"></span><span style="color: #333">Select color</span>
      </button>
      <button id="color-none" class="button add-swatch" style="width: 24%;display: inline-table;">
        <span>None</span>
      </button>         
      <button id="use-gradient" class="button add-swatch gradient5" style="">
        <span style="color: #fff">Use gradient</span>
      </button>    
        
    </div>
    <div class="panel-row" data-use-colors>    
      <div id="user-swatches" class="swatches custom-swatches"></div>       
    </div>    
    <div class="panel-row" data-use-gradients style="display: none">
      <h2 class="panel-header">Use Gradients</h2>
      <div class="swatches custom-swatches">
        <?php 
        foreach (range(1, $sm['settings']['gradients']) as $gradient) {
            echo '<button data-use-gradient="gradient'.$gradient.'" class="swatch gradient'.$gradient.'"></button>   ';
        }
        ?>        
      </div>
      <button id="use-colors" class="button add-swatch">
        <span>Back to colors</span>
      </button>      
    </div> 
  </div>
<div class="ui-card" style="background: #fafafa">


<div class="header" id="editor-header"><h3><i>live preview</i><br><span><?= $themeName; ?></span></h3></div>
 <div id="iframe-wrapper"  id="iframeWrapper">
  <?php 
  if($themeType == 'Landing'){
    $iframeUrl = $sm['config']['site_url'].'index.php?landing='.$sm['settings']['landingTheme'].'&landingPreset='.$preset;
  }
  if($themeType == 'Desktop'){
    $iframeUrl = $sm['config']['site_url'].'index.php?preset='.$preset;
  }  
  ?>
  <iframe 
    id="iframe"
    src="<?= $iframeUrl; ?>" 
    frameborder="0"
    scrolling="yes"
    
  ></iframe>
 </div>

<div class="editor-footer" style="background: #fff;">
  <p style="color: #222"><?= $sm['config']['name']; ?> Theme Editor - Powered by <a href="https://premiumdatingscript.com" target="_blank"><strong>Premium Dating Script</strong></a>
</div>
<div id="upload-area" style="display: none">
  <input type='file' id="uploadContent">
</div> 
</div>

<?php if($themeLivePreset == $preset){ ?>
<div class="lw-widget lw-widget_top lw-widget_width_full lw-widget_no_padding" data-lw-onload>
    <div class="lw-overlay" data-lw-close></div>
    <div class="lw-container lw-container_width_full" style="margin-top: 28%">
        <div class="lw-item lw-item_white lw-item_no_radius gradient12">
            <div class="lw-wrap lw-wrap_mw lw-p-sm" >
                <div class="lw-bar lw-bar_flex">
                  <div class="lw-title lw-title_sm" style="color:#fff;width:100%;text-align: center">
                    <center>Editing live design, be careful 🤗</center></div>
                  <div class="lw-actions">
                    
                  </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div id="fontOverlay"></div>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/jquery.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/popper.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/bootstrap.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/simplebar.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/dom-factory.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/vendor/material-design-kit.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/js/toggle-check-all.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/js/check-selected-row.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/js/dropdown.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/1.3.0/tinycolor.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/assets/js/sidebar-mini.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/editor/js/color.js"></script>
<script src="<?php echo $sm['config']['site_url']; ?>themes/default/js/vendor/jquery.dm-uploader.min.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/editor/js/jquery.fontselect.js"></script>
<script src="<?php echo $sm['config']['admin_url']; ?>/editor/js/html2canvas.min.js"></script>
<script src="<?php echo $sm['config']['site_url']; ?>themes/default/js/vendor/little-widgets.js"></script>
<script>

  var globalKey = '<?= siteConfig('client'); ?>';
  var previewUrl = 'meet';
  var theme = '<?= $theme; ?>';
  var preset = '<?= $preset; ?>';
  var uploadImage;
  var viewportwidth;
  var viewportheight;
  var editingColor;
  var gUrl = request_source()+'/rt.php';

  if (typeof window.innerWidth != 'undefined'){
      viewportwidth = window.innerWidth,
      viewportheight = window.innerHeight
  } else if (typeof document.documentElement != 'undefined'
     && typeof document.documentElement.clientWidth !=
     'undefined' && document.documentElement.clientWidth != 0){
       viewportwidth = document.documentElement.clientWidth,
       viewportheight = document.documentElement.clientHeight
  } else{
       viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
       viewportheight = document.getElementsByTagName('body')[0].clientHeight
  }

  console.log('Editor viewport '+viewportwidth+'x'+viewportheight); 

  $(document).ready(function(){

    $('[data-font]').fontselect();

    var iframe = $('#iframe');
    var pUrl;
    var themeType = '<?= $themeType; ?>';

    if(themeType == 'Landing'){
      pUrl = 'index.php?landing=<?= $theme; ?>&landingPreset=<?= $preset; ?>';
    }

    $('#use-gradient').click(function(){
      $('[data-use-colors]').hide();
      $('[data-use-gradients]').show();
    })
    $('[data-live-site=1]').click(function(){ 
      $(this).hide();
      $('[data-live-site=2]').fadeIn();
      $.ajax({
          url: request_source()+'/admin.php', 
          data: {
              action: 'updatePreset',
              theme: theme,
              themeType: themeType,
              preset: preset,
              globalKey: globalKey
          },  
          type: "post",
          dataType: 'JSON',           
          complete: function() {
            iframe.attr('src','<?= $sm['config']['site_url']; ?>'+pUrl);
          },
      });        
    }) 

    $('[data-live-site=2]').click(function(){ 
    
    })        
    


    $('#use-colors').click(function(){
      $('[data-use-gradients]').hide();
      $('[data-use-colors]').show();
      refreshElementRects();
    })    

    $('[data-tab]').click(function(){

      $('#toggle').prop("checked",true);
      var tab = $(this).attr('data-tab');
      pUrl = $(this).attr('data-purl');
      $('[data-tab]').removeClass('active');
      $('[data-tab-pages]').removeClass('active');
      if (tab.indexOf("page") >= 0){
        console.log(pUrl);
        $('[data-tab-pages]').addClass('active');
        if(pUrl.indexOf('profile') >= 0){

          pUrl = '@'+<?= $profileShowId; ?>;
        }
        if(pUrl.indexOf('prooffline') >= 0){
          pUrl = 'index.php?page=profile&id='+<?= $profileShowId; ?>+'&view=editor';
        }        
        if(pUrl.indexOf('default') >= 0){
          pUrl = 'matches';
        }        
      }else{
        $('[data-tab='+tab+']').addClass('active');
      }
      
      if(themeType == 'Landing'){
        pUrl = 'index.php?landing=<?= $theme; ?>&landingPreset=<?= $preset; ?>';
      }
      $('[data-current-tab]').removeClass('active');
      $('[data-current-tab='+tab+']').addClass('active');
      console.log('purl:'+pUrl);
      console.log('tab:'+tab);
      if(pUrl != previewUrl && tab != 'pages'){
        iframe.attr('src','<?= $sm['config']['site_url']; ?>'+pUrl);
        previewUrl = pUrl;
      }
      
    });

    if(viewportwidth >= 1440 ){
      $('.edit').css('width','1440px');
      $('.menu').css('width','1440px');
    } else {
      $('.edit').css('width',viewportwidth+'px');
      $('.menu').css('width',viewportwidth+'px');
    }

    //open color editor
    $('.pickr-elem').click(function(){
      var c = $(this).attr('data-color');
      var hasGradient = $(this).attr('data-gradient');
      editingColor = c;
      if (!hasGradient.trim()) {
        $('#use-gradient').hide();
      } else {
        $('#use-gradient').show();
      }
      $('.color-picker-panel').show();
      refreshElementRects();
    });

    $('.selectColor').click(function(){
      $('[data-color='+editingColor+']').attr('class','');
      $('[data-color='+editingColor+']').addClass('ui label pickr-elem');    
      $('[data-color='+editingColor+']').css('background','#'+selectedColor);
      $('[data-color-val='+editingColor+']').val('#'+selectedColor);
      var addSelectedColorSwatch = tinycolor('#'+selectedColor);
      createSwatch(userSwatches, addSelectedColorSwatch);      
      $('.color-picker-panel').hide();
      refreshElementRects();
      $('[data-theme-setting='+editingColor+']').trigger("change");
    });

    $('[data-use-gradient]').click(function(){
      var cg = $(this).attr('data-use-gradient');
      
      $('[data-color='+editingColor+']').attr('class','');
      $('[data-color='+editingColor+']').addClass('ui label pickr-elem');
      $('[data-color='+editingColor+']').addClass(cg);
      $('[data-color-val='+editingColor+']').val(cg);     
      $('.color-picker-panel').hide();
      $('#use-colors').click();
      refreshElementRects();
      $('[data-theme-setting='+editingColor+']').trigger("change");
    });    

    $('.ui-card').click(function(){
      if($('.color-picker-panel').is(":visible")){
        $('.color-picker-panel').hide();
      } 
    })    

  $(document).keyup(function(e) {
    if (e.keyCode === 27){
      if($('.color-picker-panel').is(":visible")){
        $('.color-picker-panel').hide();
      }       
    }
  });

  $('[data-theme-setting]').change(function(){    
      var setting = $(this).attr('data-theme-setting');
      var type = $(this).attr('data-theme-type');
      var gradient = $(this).attr('data-theme-gradient');
      var val = '';



      if(type == 'boolean'){
        var current = $(this).attr('data-theme-setting');
        var checked = $('[data-theme-setting='+current+']:checked').val();
        if(checked != 'on'){
            val = 'No';
        }else {
            val = 'Yes';
        }
        console.log(val);
      } else {
        val = $(this).val();
      }

      if(type == 'color'){
        $('[data-color='+setting+']').css('background',val);
      }
      
      val = val.split('+').join(' ');

      if(themeType == 'Landing'){
        pUrl = 'index.php?landing=<?= $theme; ?>&landingPreset=<?= $preset; ?>';
      }

      $.ajax({
          url: request_source()+'/admin.php', 
          data: {
              action: 'updateTheme',
              val: val,
              theme: theme,
              preset: preset,
              gradient: gradient,
              type: type,
              setting: setting,
              globalKey: globalKey
          },  
          type: "post",
          dataType: 'JSON',           
          complete: function() {          
          iframe.attr('src','<?= $sm['config']['site_url']; ?>'+pUrl);
          },
      });      
      $('[data-footer-theme-update]').fadeOut('fast');
      $('[data-footer-theme-update='+setting+']').fadeIn();
      $('[data-footer-theme-update]').fadeOut('slow');
    });

    $('[data-theme-setting-click]').click(function(){
      $('[data-theme-setting-click]').removeClass('selected');
      $(this).addClass('selected');
      var theme = '<?= $theme; ?>';    
      var setting = $(this).attr('data-theme-setting');
      var val = $(this).attr('data-theme-setting-val');
      var type = 'click';
      if(val == 'Top-Menu'){
        $('[data-segment="design_style_wide"]').show();
        $('[data-segment-require="Left-Menu"]').hide();
        $('[data-segment-require="'+val+'"]').show();
      } else {
        $('[data-segment="design_style_wide"]').hide();
        $('[data-segment-require="Top-Menu"]').hide();
        $('[data-segment-require="'+val+'"]').show();
      }

      var wide;
      var checked = $('[data-theme-setting="design_style_wide"]:checked').val();
      if(checked != 'on'){
          wide = 'No';
      }else {
          wide = 'Yes';
      }    

      if(themeType == 'Landing'){
        pUrl = 'index.php?landing=<?= $theme; ?>&landingPreset=<?= $preset; ?>';
      }        
      $.ajax({
          url: request_source()+'/admin.php', 
          data: {
              action: 'updateTheme',
              val: val,
              theme: theme,
              preset: preset,
              type: type,
              wide: wide,
              setting: setting,
              globalKey: globalKey
          },  
          type: "post",
          dataType: 'JSON',           
          complete: function() {
            iframe.attr('src','<?= $sm['config']['site_url']; ?>'+pUrl);
          },
      });      
      $('[data-footer-theme-update]').fadeOut('fast');
      $('[data-footer-theme-update='+setting+']').fadeIn();
      $('[data-footer-theme-update]').fadeOut('slow');
    });  

   
    $('[data-update-image]').click(function(){
      uploadImage = $(this).attr('data-update-image');
      document.getElementById("uploadContent").click();
    })

    var ph = 0;
    var upType = 5;
    var upphotos = [];
    $("#upload-area").dmUploader({
      url: site_url()+'/assets/sources/upload.php?fromEditor=true',
      multiple: false,
      extFilter: ["jpg", "jpeg", "png", "mp4", "ogg", "webm","gif","svg"],
      onFileExtError: function(file){
        alert(site_lang[596]['text']);
      }, 
      onNewFile: function(id, file){
        var fileUrl = URL.createObjectURL(file);
        createPreview(file, fileUrl,id);
        $('[data-update-image='+uploadImage+']').addClass('uploadingGray');
      },  
      onUploadProgress: function(id,percent){
      $('[data-update-image-progress='+uploadImage+']').text('Uploading '+percent+'%')
        if(percent == 100){
          $('[data-update-image='+uploadImage+']').removeClass('uploadingGray');
          $('[data-update-image-progress='+uploadImage+']').text('Upload complete');
          setTimeout(function(){
            $('[data-update-image-progress='+uploadImage+']').fadeOut('slow');
          },250);
        }
      },
      onComplete: function(){
        
      },
      onUploadSuccess: function(id, file){
        
        upphotos[ph] = file;
        var photoPath = file.path;
        var type = 'image';
        console.log(photoPath);
        $.ajax({
            url: request_source()+'/admin.php', 
            data: {
                action: 'updateTheme',
                val: photoPath,
                theme: theme,
                preset: preset,
                type: type,
                setting: uploadImage,
                globalKey: globalKey
            },  
            type: "post",
            dataType: 'JSON',           
            complete: function() {
              if(themeType == 'Landing'){
                pUrl = 'index.php?landing=<?= $theme; ?>&landingPreset=<?= $preset; ?>';
              }              
              iframe.attr('src','<?= $sm['config']['site_url']; ?>'+pUrl);
            },
        });      
        $('[data-footer-theme-update]').fadeOut('fast');
        $('[data-footer-theme-update='+uploadImage+']').fadeIn();
        $('[data-footer-theme-update]').fadeOut('slow');
      }
      
    });

      

  });

 
  function createPreview(file, fileContents,id) {
    var $previewElement = '';
    switch (file.type) {
      case 'image/png':
      case 'image/jpeg':
      case 'image/gif':
        $previewElement = $('<img src="' + fileContents + '" />');
        break;
      case 'video/mp4':
      case 'video/webm':
      case 'video/ogg':
        $previewElement = $('<video autoplay muted width="100%" height="100%"><source src="' + fileContents + '" type="' + file.type + '"></video>');
        break;
      default:
        break;
    }
    var $displayElement = $('<div class="preview">\
                <div class="contentSwitch">\
                                <div class="progress"><div class="determinate" id="upload'+id+'" style="width: 0%"></div></div>\
                                </div>\
                               <div class="preview__thumb uploadingGray" id="gray'+id+'"></div>\
                  <label class="switch">\
          <input type="checkbox">\
          <span>\
              <em></em>\
              <strong></strong>\
          </span>\
      </label>\
    </div>');
    $('[data-update-image='+uploadImage+']').html($previewElement);

  }


  const $iframeWrapper = $('#iframe-wrapper');
  const $iframe = $('#iframe');
  const $iframeLoading = $('.loading-iframe');

  function scaleIt() {
    const wrapperWidth = $iframeWrapper.width()
    const iframeWidth = $iframe.width()
    let scale = 1
    if (wrapperWidth <= iframeWidth) {
      scale = 1 - ((iframeWidth - wrapperWidth) / iframeWidth)
    } else {
      scale = 1 + ((wrapperWidth - iframeWidth) / wrapperWidth)
    }
    $iframe.css('transform', `scale(${scale})`);
    $iframeLoading.css('transform', `scale(${scale})`);
    $iframeLoading.css('width',iframeWidth+'px');
    $iframeLoading.css('height',$iframe.height()+'px');
  }

  scaleIt()

  window.addEventListener('resize', scaleIt)

  /* ================================ */

  const $iframeWrapperMobile = $('#iframe-wrapper-mobile')
  const $iframeMobile = $('#iframe-mobile')

  function scaleItMobile() {
    const wrapperWidth = $iframeWrapperMobile.width()
    const iframeWidth = $iframeMobile.width()
    let scale = 1
    if (wrapperWidth <= iframeWidth) {
      scale = 1 - ((iframeWidth - wrapperWidth) / iframeWidth)
    } else {
      scale = 1 + ((wrapperWidth - iframeWidth) / wrapperWidth)
    }
    $iframeMobile.css('transform', `scale(${scale})`)
  }

  scaleItMobile()

  window.addEventListener('resize', scaleItMobile);

  function close_window() {
    if (confirm("Leave theme editor?")) {
      close();
    }
  }

</script>

</body>
</html>