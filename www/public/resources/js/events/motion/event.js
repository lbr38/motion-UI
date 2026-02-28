/**
 *  Initialize video lazy loading with Intersection Observer
 */
function initializeVideoLazyLoading() {
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
        const poster = video.getAttribute('poster-to-load') || video.getAttribute('poster');
        sources.forEach(source => {
            if (source.dataset.src) {
                source.src = source.dataset.src; // Set the source URL
                source.removeAttribute('data-src'); // Remove data attribute to avoid reloading
            } else {
                console.error('data-src attribute is missing for source element', source);
            }
        });
        video.setAttribute('poster', poster); // Trigger poster loading
        video.load(); // Trigger video loading
    };

    // Function to unload video sources when not visible
    const unloadVideo = (video) => {
        const sources = video.querySelectorAll('source');
        sources.forEach(source => {
            if (source.src) {
                source.dataset.src = source.src; // Save the source URL in data attribute
                source.removeAttribute('src'); // Remove the source URL
            }
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
}

/**
 *  Initialize video lazy loading on DOMContentLoaded and make it available globally
 */
document.addEventListener("DOMContentLoaded", function () {
    initializeVideoLazyLoading();
});

/**
 *  Event: select events date
 */
$(document).on('change','.event-date-input',function () {
    const date = $(this).val();

    // Delete tables/motion/events/offset cookie to reset pagination to the first page after reloading events list
    mycookie.delete('tables/motion/events/offset');

    // Save selected date in cookies for 15 minutes
    mycookie.set('event-date', date, 1/96); // 15 minutes

    // Reload events list
    mycontainer.reload('motion/events/list');
});

/**
 *  Event: filter events by camera
 */
$(document).on('change','select#events-filter-cameras',function () {
    const selectedCameras = $(this).val();

    // Delete tables/motion/events/offset cookie to reset pagination to the first page after reloading events list
    mycookie.delete('tables/motion/events/offset');

    // Save selected cameras in cookies for 7 days
    mycookie.set('tmp/events-filter-cameras', selectedCameras ? selectedCameras.join(',') : '', 1/96); // 15 minutes

    // Reload events list
    mycontainer.reload('motion/events/list');
});

/**
 *  Event: acquit all events
 */
$(document).on('click','.acquit-events-btn',function () {
    myalert.print('Acquitting all events, please wait...');

    ajaxRequest(
        // Controller:
        'motion',
        // Action:
        'acquit-events',
        // Data:
        {},
        // Print success alert:
        true,
        // Print error alert:
        true
    ).then(function () {
        mycontainer.reload('buttons/bottom');

        // Remove all 'New' labels from events list
        $('.new-event-label').remove();

        // Remove all 'unacquitted' classes from events list
        $('.acquit-events-btn').remove();
    });
});
