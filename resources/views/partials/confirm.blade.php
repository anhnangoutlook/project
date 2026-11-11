<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">ការបញ្ជាក់ទំនិញ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="confirmMessage" style="margin:0;">តើអ្នកចង់លុបកំណត់ត្រានេះ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="confirmNoBtn">No</button>
        <button type="button" class="btn btn-danger" id="confirmYesBtn">Yes</button>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    var confirmModalEl = document.getElementById('confirmModal');
    var bsConfirm = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    var resolveFn = null;
    var msgEl = document.getElementById('confirmMessage');
    var yesBtn = document.getElementById('confirmYesBtn');
    var noBtn = document.getElementById('confirmNoBtn');

    function cleanup() {
      resolveFn = null;
    }

    if (yesBtn) yesBtn.addEventListener('click', function(){
      if (bsConfirm) bsConfirm.hide();
      if (resolveFn) resolveFn(true);
      cleanup();
    });
    if (noBtn) noBtn.addEventListener('click', function(){
      if (bsConfirm) bsConfirm.hide();
      if (resolveFn) resolveFn(false);
      cleanup();
    });

    // expose global showConfirm(msg) -> Promise<boolean>
    window.showConfirm = function(message){
      return new Promise(function(resolve){
        resolveFn = resolve;
        if (msgEl) msgEl.textContent = message || 'Are you sure?';
        if (bsConfirm) bsConfirm.show();
        else resolve(true);
      });
    };
  })();
</script>
