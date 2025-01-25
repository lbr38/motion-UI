/**
 *  Add an Intersection Observer to load videos only when they are visible
 */
document.addEventListener("DOMContentLoaded", function () {
    // Create the IntersectionObserver to observe video visibility
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadVideo(entry.target); // Load the video when it becomes visible
            } else {
                unloadVideo(entry.target); // Unload the video when it is not visible
            }
        });
    });

    // Function to load video sources when visible
    const loadVideo = (video) => {
        const sources = video.querySelectorAll('source[data-src]');
        sources.forEach(source => {
            source.src = source.dataset.src; // Set the source URL
            source.removeAttribute('data-src'); // Remove data attribute to avoid reloading
        });
        video.load(); // Trigger video loading
    };

    // Function to unload video sources when not visible
    const unloadVideo = (video) => {
        const sources = video.querySelectorAll('source');
        sources.forEach(source => {
            source.dataset.src = source.src; // Save the source URL in data attribute
            source.removeAttribute('src'); // Remove the source URL
        });
        video.pause(); // Pause the video
        video.load(); // Trigger video unloading
    };

    // Observe all videos that haven't been observed yet
    const observeVideos = () => {
        document.querySelectorAll('video:not([data-observed])').forEach(video => {
            observer.observe(video); // Start observing the video
            video.dataset.observed = true; // Mark it as observed
        });
    };

    // Initial observation of existing videos
    observeVideos();

    // Use MutationObserver to detect dynamic additions to the DOM
    const config = { childList: true, subtree: true };
    const mutationObserver = new MutationObserver(() => {
        observeVideos(); // Observe newly added videos
    });

    // Start observing DOM changes
    mutationObserver.observe(document.body, config);
});