<style>
	.container { width: min(1100px, 92vw); margin: 0 auto; }
	footer { border-top: 1px solid #e5e7eb; margin-top: 32px; }
    .foot { padding: 22px 0; display: grid; gap: 10px; text-align: center; }
    .foot-links { flex-wrap: wrap; gap: 12px; font-size: 14px; color: var(--muted); }
    .copyright { color: var(--muted); font-size: 14px; }
</style>
<footer>
    <div class="container foot">
      <nav class="foot-links">
        <a href="#">Home</a>
        <span>•</span>
        <a href="#">About Us</a>
        <span>•</span>
        <a href="#">Contact Us</a>
        <span>•</span>
        <a href="#">Terms and Conditions</a>
        <span>•</span>
        <a href="#">Privacy Policy</a>
        <span>•</span>
        <a href="#">Request Delete Account</a>
      </nav>
      <div class="copyright">© 2025 ClubGo. All rights reserved.</div>
    </div>
  </footer>
<?php 
 include 'include/eventconfig.php';
 echo $validate['data'];
 ?>
<script>
window.postForm = function() {
    if (window.jQuery && $.fn.summernote) {
        $('#cdesc, #disclaimer, .summernote').each(function() {
            var $el = $(this);
            if ($el.next('.note-editor').length) {
                $el.val($el.summernote('code'));
            }
        });
    }
    return true;
};

function notifyAdmin(type, title, message) {
    try {
        var opts = {
            timeOut: 3000,
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            showDuration: '300',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
        if (window.toastr) {
            if (type === 'success' && toastr.success) {
                toastr.success(message || '', title || 'Success', opts);
                return;
            } else if (toastr.error) {
                toastr.error(message || 'Operation failed', title || 'Error', opts);
                return;
            }
        }
        if (window.iziToast) {
            if (type === 'success') {
                iziToast.success({ title: title || 'Success', message: message || '', position: 'topRight' });
            } else {
                iziToast.error({ title: title || 'Error', message: message || 'Operation failed', position: 'topRight' });
            }
            return;
        }
    } catch (e) {}
    if (type !== 'success') {
        alert((title || 'Error') + ': ' + (message || 'Operation failed'));
    }
}

function bindAdminHandlers() {
    if (!window.jQuery) return;
    $(document).off('submit', 'form').on('submit', 'form', function(e) {
        var method = ($(this).attr('method') || '').toLowerCase();
        if (method === 'get') {
            return true;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        var $form = $(this);
        var $submitBtns = $form.find(':input[type="submit"]');
        $submitBtns.prop('disabled', true);

        // Sync Summernote editors into textareas before serializing
        if ($.fn.summernote) {
            $('#cdesc, #disclaimer, .summernote').each(function() {
                var $el = $(this);
                if ($el.next('.note-editor').length) {
                    $el.val($el.summernote('code'));
                }
            });
        }

        var formData = new FormData();
        var serialized = $form.serialize();
        var b64 = btoa(unescape(encodeURIComponent(serialized)));
        formData.append('_b64_form', b64);

        $form.find('input[type="file"]').each(function() {
            var name = $(this).attr('name');
            if (name && this.files && this.files.length > 0) {
                for (var i = 0; i < this.files.length; i++) {
                    formData.append(name, this.files[i]);
                }
            }
        });

        $.ajax({
            type: 'POST',
            url: 'include/event.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                var res = null;
                try {
                    res = (typeof data === 'object') ? data : JSON.parse(data);
                } catch (err) {
                    $submitBtns.prop('disabled', false);
                    notifyAdmin('error', 'Error!', 'Unexpected server response. Please try again.');
                    return;
                }
                if (res && res.Result === 'true') {
                    notifyAdmin('success', res.title || 'Success', res.message || '');
                    setTimeout(function() {
                        window.location.href = res.action || window.location.href;
                    }, 1000);
                } else {
                    $submitBtns.prop('disabled', false);
                    notifyAdmin('error', (res && res.title) || 'Error', (res && res.message) || 'Operation failed');
                }
            },
            error: function(xhr) {
                $submitBtns.prop('disabled', false);
                notifyAdmin('error', 'Request Failed (' + xhr.status + ')', 'Could not save changes. Please check your input and try again.');
            }
        });
        return false;
    });
}

bindAdminHandlers();
$(document).ready(bindAdminHandlers);
</script>
