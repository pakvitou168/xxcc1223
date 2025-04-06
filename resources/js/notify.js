import { toast as toastify } from 'vue3-toastify';

function notify(message, type = 'info', position = toastify.POSITION.TOP_RIGHT) {
    switch (type) {
        case 'success':
            toastify.success(message, { position: position, autoClose: 3000, clearOnUrlChange: false });
            break;
        case 'error':
            toastify.error(message, { position: position, autoClose: 3000, clearOnUrlChange: false });
            break;
        case 'warn':
            toastify.warn(message, { position: position, autoClose: 3000, clearOnUrlChange: false });
            break;
        default:
            toastify.info(message, { position: position, autoClose: 3000, clearOnUrlChange: false });
            break;
    }
}

(function () {
    window.notify = notify;
})();