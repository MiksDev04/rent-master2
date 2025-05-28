setTimeout(function () {
    var alertEl = document.getElementById('addSuccess');
    if (alertEl) {
        var bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
        bsAlert.close();
    }
}, 2500); // 2.5 seconds