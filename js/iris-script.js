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
});