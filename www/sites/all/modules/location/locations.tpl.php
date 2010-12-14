<?php if (count($locations)) {?>

<div class="location-locations-wrapper">
<h3 class="location-locations-header">
<?php echo count($locations) > 1 ? t('Locations') : t('Location');?>
</h3>
<?php
  foreach ($locations as $location) {
    echo $location;
  }
  echo '</div>';
} ?>
