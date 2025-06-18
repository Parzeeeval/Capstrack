function updateTabsCount(delta) {
    let count = parseInt(localStorage.getItem("openTabsCount") || "0");
    count += delta;
    localStorage.setItem("openTabsCount", count);
    return count;
}

// Before unload, set a flag in sessionStorage indicating the tab is reloading
window.addEventListener("beforeunload", function() {
    sessionStorage.setItem("isReloading", "true");
});

// After the page has loaded, clear the reload flag
window.addEventListener("load", function() {
    sessionStorage.removeItem("isReloading");
});

// Decrement the count when the tab is closed (and not reloading)
window.addEventListener("unload", function() {
    if (sessionStorage.getItem("isReloading") !== "true") {
        let tabCount = updateTabsCount(-1);

        // If this was the last tab open, infer that the browser or last tab is closing
        if (tabCount === 0) {
            // Send data to the server to indicate that the last tab has been closed
            navigator.sendBeacon("pages/closetab.php", JSON.stringify({ action: "browserClose" }));
        }
    }
});