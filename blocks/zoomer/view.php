<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$ih = Loader::helper("image");
$img = File::getByID($fID);
if(is_object($img)){
    $thumb = $ih->getThumbnail($img,$maxThumbWidth,$maxThumbHeight,true);	
    $large = $ih->getThumbnail($img,$maxImageWidth,$maxImageHeight,false);
}
$c = Page::getCurrentPage();
?>

<?php if($zoomType=="zoom"){ ?>
<?php if (!$c->isEditMode()) { ?>
<script type="text/javascript">
$(function(){
	$("#zoomer-<?=$bID?>").elevateZoom();
});
</script>
<?php } ?>
<img src="<?=$thumb->src?>" id="zoomer-<?=$bID?>" data-zoom-image="<?=$large->src?>">
<?php } 

else if($zoomType=="innerzoom"){ ?>
<?php if (!$c->isEditMode()) { ?>
<script type="text/javascript">
$(function(){
	$("#zoomer-<?=$bID?>").elevateZoom({
		zoomType: "inner", 
		cursor: "crosshair"
	});
});
</script>
<?php } ?>
<img src="<?=$thumb->src?>" id="zoomer-<?=$bID?>" data-zoom-image="<?=$large->src?>">
<?php }

else if($zoomType=="lenszoom"){ ?>
<?php if (!$c->isEditMode()) { ?>
<script type="text/javascript">
$(function(){
	$("#zoomer-<?=$bID?>").elevateZoom({
		zoomType: "lens", 
		lensShape: "round", 
		lensSize : 100 
	});
});
</script>
<?php } ?>
<img src="<?=$thumb->src?>" id="zoomer-<?=$bID?>" data-zoom-image="<?=$large->src?>">
<?php } 

else if($zoomType=="lightbox"){ ?>
<a href="<?=$large->src?>" data-featherlight="image">
<img src="<?=$thumb->src?>" id="zoomer-<?=$bID?>">
</a>
<?php } ?>