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
$(document).ready(function() {
    $(document).off('submit', 'form').on('submit', 'form', function(e) {
        var method = ($(this).attr('method') || '').toLowerCase();
        if (method === 'get') {
            return true;
        }
        e.preventDefault();
        var $form = $(this);
        var $submitBtns = $form.find(':input[type="submit"]');
        $submitBtns.prop('disabled', true);

        // Sync Summernote editors into textareas before serializing
        if ($.fn.summernote) {
            $('#cdesc, #disclaimer').each(function() {
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
                try {
                    var res = (typeof data === 'object') ? data : JSON.parse(data);
                    if (res.Result === 'true') {
                        iziToast.success({
                            title: res.title || 'Success',
                            message: res.message || '',
                            position: 'topRight'
                        });
                        setTimeout(function() {
                            window.location.href = res.action || window.location.href;
                        }, 1500);
                    } else {
                        $submitBtns.prop('disabled', false);
                        iziToast.error({
                            title: res.title || 'Error',
                            message: res.message || 'Operation failed',
                            position: 'topRight'
                        });
                    }
                } catch (err) {
                    $submitBtns.prop('disabled', false);
                    iziToast.error({
                        title: 'Error!',
                        message: 'Unexpected server response. Please try again.',
                        position: 'topRight'
                    });
                }
            },
            error: function(xhr) {
                $submitBtns.prop('disabled', false);
                iziToast.error({
                    title: 'Request Failed (' + xhr.status + ')',
                    message: 'Could not save changes. Please check your input and try again.',
                    position: 'topRight'
                });
            }
        });
        return false;
    });
});
</script>
