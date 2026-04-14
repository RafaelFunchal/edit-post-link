jQuery(document).ready(function($){
    var BGoptions = {
	    defaultColor: false,
	    change: function(event, ui){},
	    clear: function() {},
	    hide: true,
	    palettes: true
	};
    $('#edit-post-link-bg-color').wpColorPicker( BGoptions );

    var BORDERoptions = {
	    defaultColor: false,
	    change: function(event, ui){},
	    clear: function() {},
	    hide: true,
	    palettes: true
	};
    $('#edit-post-link-border-color').wpColorPicker( BORDERoptions );

    var FONToptions = {
	    defaultColor: false,
	    change: function(event, ui){},
	    clear: function() {},
	    hide: true,
	    palettes: true
	};
    $('#edit-post-link-font-color').wpColorPicker( FONToptions );
    $('#edit-post-link-hover-bg-color').wpColorPicker( BGoptions );
    $('#edit-post-link-hover-font-color').wpColorPicker( FONToptions );

    function toggleHoverAnimationField() {
        var $linkType = $('#edit-post-link-type');
        var $hoverAnimation = $('#edit-post-link-hover-animation');
        var $hoverBgColor = $('#edit-post-link-hover-bg-color');
        var $hoverFontColor = $('#edit-post-link-hover-font-color');

        if (! $linkType.length || ! $hoverAnimation.length) {
            return;
        }

        // Link Type choices are ordered as: Button, Circle, Plain Text.
        var isButtonSelected = $linkType.prop('selectedIndex') === 0;
        $hoverAnimation.closest('tr').toggle(isButtonSelected);
        $hoverBgColor.closest('tr').toggle(isButtonSelected);
        $hoverFontColor.closest('tr').toggle(isButtonSelected);
    }

    toggleHoverAnimationField();
    $('#edit-post-link-type').on('change', toggleHoverAnimationField);
});