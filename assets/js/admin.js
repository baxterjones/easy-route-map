(function () {
    function setupShortcodeCopyButtons() {
        var buttons = document.querySelectorAll('.erm-copy-shortcode');

        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-copy-target');
                var target = targetId ? document.getElementById(targetId) : null;

                if (!target) {
                    return;
                }

                var text = target.textContent || target.innerText || '';

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        button.textContent = 'Copied';
                        window.setTimeout(function () {
                            button.textContent = 'Copy';
                        }, 1600);
                    });
                    return;
                }

                var textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', 'readonly');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                button.textContent = 'Copied';
                window.setTimeout(function () {
                    button.textContent = 'Copy';
                }, 1600);
            });
        });
    }


    function setupMarkerLabelField() {
        var select = document.getElementById('erm_marker_label');
        var custom = document.getElementById('erm_custom_marker_label');

        if (!select || !custom) {
            return;
        }

        var row = custom.closest('tr');

        function toggleCustomLabel() {
            if (!row) {
                return;
            }

            row.style.display = select.value === 'Custom' ? '' : 'none';
        }

        select.addEventListener('change', toggleCustomLabel);
        toggleCustomLabel();
    }

    function disableGutenbergFullscreen() {
        if (typeof wp === 'undefined' || !wp.domReady || !wp.data) {
            return;
        }

        wp.domReady(function () {
            var preferences = wp.data.select('core/preferences');
            var editor = wp.data.dispatch('core/edit-post');

            if (!preferences || !editor || typeof editor.toggleFeature !== 'function') {
                return;
            }

            var isFullscreen = preferences.get('core/edit-post', 'fullscreenMode');

            if (isFullscreen) {
                editor.toggleFeature('fullscreenMode');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setupShortcodeCopyButtons();
            setupMarkerLabelField();
        });
    } else {
        setupShortcodeCopyButtons();
        setupMarkerLabelField();
    }

    disableGutenbergFullscreen();
}());
