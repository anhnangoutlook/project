<style>
    /* Global toast styles (bottom-right) */
    #toastContainer {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
        pointer-events: none;
        font-family: inherit;
    }
    .toast-item {
        min-width: 220px;
        max-width: 420px;
        background: #fff;
        color: #222;
        border-radius: 6px;
        padding: 10px 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        pointer-events: auto;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }
    .toast-item.success { border-left: 4px solid #28a745; }
    .toast-item.error { border-left: 4px solid #dc3545; }
    .toast-item.warning { border-left: 4px solid #ffc107; }
    .toast-item.info { border-left: 4px solid #0d6efd; }
    .toast-close {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #666;
        font-size: 16px;
        line-height: 1;
    }
</style>

<div id="toastContainer" aria-live="polite" aria-atomic="true"></div>

<script>
    (function(){
        function showToast(message, type) {
            type = type || 'info';
            var container = document.getElementById('toastContainer');
            if (!container) return;
            var item = document.createElement('div');
            item.className = 'toast-item ' + type;
            item.setAttribute('role', 'status');
            item.setAttribute('aria-live', 'polite');
            var text = document.createElement('div');
            text.style.flex = '1 1 auto';
            text.textContent = message;
            var close = document.createElement('button');
            close.className = 'toast-close';
            close.setAttribute('aria-label', 'Close');
            close.innerHTML = '&times;';
            close.addEventListener('click', function() {
                if (item && item.parentNode) item.parentNode.removeChild(item);
            });
            item.appendChild(text);
            item.appendChild(close);
            container.appendChild(item);
            // auto remove after 5s
            setTimeout(function() { if (item && item.parentNode) item.parentNode.removeChild(item); }, 5000);
        }

        // expose globally
        window.showToast = showToast;

        // override native alert to show read-only popup
        window.alert = function(message){
            try { showToast(String(message), 'info'); } catch(e) { /* fallback */ }
        };
    })();
</script>
