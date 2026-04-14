jQuery(document).ready(function($){
    var config = window.editPostLinkConfig || {};
    var linkTypes = $.extend({
        button: 'Button',
        circle: 'Circle',
        plainText: 'Plain Text',
        link: 'Link'
    }, config.linkTypes || {});
    var hoverAnimations = $.extend({
        none: 'None',
        lift: 'Lift',
        grow: 'Grow',
        pulse: 'Pulse',
        glow: 'Glow'
    }, config.hoverAnimations || {});
    var positions = $.extend({
        above: 'Above Content',
        below: 'Below Content'
    }, config.positions || {});

    var BGoptions = {
	    defaultColor: false,
	    change: function(event, ui){},
	    clear: function() {},
	    hide: true,
	    palettes: true
	};
    $('#edit-post-link-bg-color').wpColorPicker( BGoptions );

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

    function isButtonSelected() {
        return $('#edit-post-link-type').val() === linkTypes.button;
    }

    function supportsHoverColors() {
        var linkType = $('#edit-post-link-type').val();
        return linkType === linkTypes.button || linkType === linkTypes.circle;
    }

    function supportsDefaultColors() {
        var linkType = $('#edit-post-link-type').val();
        return linkType !== linkTypes.plainText && linkType !== linkTypes.link;
    }

    function getHoverAnimationClass() {
        var value = $('#edit-post-link-hover-animation').val();

        if (value === hoverAnimations.lift) {
            return 'epl-anim-lift';
        }
        if (value === hoverAnimations.grow) {
            return 'epl-anim-grow';
        }
        if (value === hoverAnimations.pulse) {
            return 'epl-anim-pulse';
        }
        if (value === hoverAnimations.glow) {
            return 'epl-anim-glow';
        }
        return 'epl-anim-none';
    }

    function buildLinkClass() {
        var linkType = $('#edit-post-link-type').val();
        var className = '';

        if (linkType === linkTypes.circle) {
            className = 'edit-post-link epl-circle';
        } else if (linkType === linkTypes.button) {
            className = 'edit-post-link epl-button ' + getHoverAnimationClass();
        }

        return $.trim(className);
    }

    function syncPreviewHoverStyles() {
        var hoverBg = $('#edit-post-link-hover-bg-color').val() || '';
        var hoverFont = $('#edit-post-link-hover-font-color').val() || '';
        var css = '';

        if (hoverBg) {
            css += '#edit-post-link-live-preview .edit-post-link.epl-button:hover,#edit-post-link-live-preview .edit-post-link.epl-circle:hover{background-color:' + hoverBg + ' !important;}';
        }
        if (hoverFont) {
            css += '#edit-post-link-live-preview .edit-post-link.epl-button:hover,#edit-post-link-live-preview .edit-post-link.epl-circle:hover{color:' + hoverFont + ' !important;}';
        }

        var styleTagId = 'epl-preview-hover-styles';
        var existing = document.getElementById(styleTagId);
        if (!existing) {
            existing = document.createElement('style');
            existing.id = styleTagId;
            document.head.appendChild(existing);
        }
        existing.textContent = css;
    }

    function refreshPreview() {
        var linkType = $('#edit-post-link-type').val();
        var position = $('#edit-post-link-position').val();
        var linkClass = buildLinkClass();
        var bgColor = $('#edit-post-link-bg-color').val() || '';
        var fontColor = $('#edit-post-link-font-color').val() || '';
        var $previewTop = $('#epl-preview-link-above');
        var $previewBottom = $('#epl-preview-link-below');
        var $previewLinks = $('#epl-preview-link, #epl-preview-link-duplicate');

        if (position === positions.above) {
            $previewTop.show();
            $previewBottom.hide();
        } else {
            $previewTop.hide();
            $previewBottom.show();
        }

        if (linkType === linkTypes.plainText || linkType === linkTypes.link) {
            $previewLinks.attr('class', '').css({
                backgroundColor: '',
                color: '',
                borderColor: '',
                textDecoration: 'underline'
            });
        } else {
            $previewLinks.attr('class', linkClass).css({
                backgroundColor: bgColor || '',
                color: fontColor || '',
                borderColor: '',
                textDecoration: 'none'
            });
        }

        syncPreviewHoverStyles();
    }

    function toggleHoverAnimationField() {
        var $linkType = $('#edit-post-link-type');
        var $hoverAnimation = $('#edit-post-link-hover-animation');
        var $hoverBgColor = $('#edit-post-link-hover-bg-color');
        var $hoverFontColor = $('#edit-post-link-hover-font-color');
        var $defaultBgColor = $('#edit-post-link-bg-color');
        var $defaultFontColor = $('#edit-post-link-font-color');

        if (! $linkType.length || ! $hoverAnimation.length) {
            return;
        }

        var showHoverAnimation = isButtonSelected();
        var showHoverColors = supportsHoverColors();
        var showDefaultColors = supportsDefaultColors();
        toggleRowWithAnimation($defaultBgColor.closest('tr'), showDefaultColors);
        toggleRowWithAnimation($defaultFontColor.closest('tr'), showDefaultColors);
        toggleRowWithAnimation($hoverAnimation.closest('tr'), showHoverAnimation);
        toggleRowWithAnimation($hoverBgColor.closest('tr'), showHoverColors);
        toggleRowWithAnimation($hoverFontColor.closest('tr'), showHoverColors);
    }

    function toggleRowWithAnimation($row, shouldShow) {
        if (! $row.length) {
            return;
        }

        if (shouldShow) {
            $row.stop(true, true).slideDown(160);
        } else {
            $row.stop(true, true).slideUp(140);
        }
    }

    function toggleGroupTitles() {
        var $defaultColorsGroup = $('.epl-group[data-epl-group="normal_colors"]');
        var $hoverColorsGroup = $('.epl-group[data-epl-group="hover_colors"]');

        if ($defaultColorsGroup.length) {
            $defaultColorsGroup.toggle(supportsDefaultColors());
        }

        if ($hoverColorsGroup.length) {
            $hoverColorsGroup.toggle(supportsHoverColors());
        }
    }

    toggleHoverAnimationField();
    refreshPreview();

    $('#edit-post-link-type, #edit-post-link-position, #edit-post-link-hover-animation').on('change', function() {
        toggleHoverAnimationField();
        toggleGroupTitles();
        refreshPreview();
    });

    $('#edit-post-link-bg-color, #edit-post-link-font-color, #edit-post-link-hover-bg-color, #edit-post-link-hover-font-color').on('change input', refreshPreview);

    toggleGroupTitles();
});