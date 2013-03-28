jQuery(document).ready(function() {
var f = jQuery.farbtastic('#picker');
var selected;
jQuery('.colorwell')
	.each(function () {
		jQuery(this).keyup(function () {
			var _hex = jQuery(this).val(), hex = _hex;
			if ( hex[0] != '#' )
				hex = '#' + hex;
			hex = hex.replace(/[^#a-fA-F0-9]+/, '');
			if ( hex != _hex )
				jQuery(this).val(hex);
		})
	})	
  .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
  .focus(function() {
	if (selected) {
	  jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
	}
	f.linkTo(this);
	p.css('opacity', 1);
	jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
  });
});	