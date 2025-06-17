jQuery(function ($) {
    /* GOOGLE MAP SCROLLING FIX */
	   $(".gmap-fix iframe").after('<div class="map_overlay" onClick="style.pointerEvents=\'none\'"></div>');

    /* OPEN SOCIAL IN NEW TAB */
        $(".et-social-icon a").attr('target', '_blank');
});