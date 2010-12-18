<?php if (count($locations)) {?>

<div class="location-locations-wrapper">
<h3 class="location-locations-header">
<a href="http://stupino/map/node"><?php echo count($locations) > 1 ? t('Locations') : t('Location');?></a>
</h3>
<?php
  foreach ($locations as $location) {
    echo $location;
  }
  echo '</div>';
} ?>
