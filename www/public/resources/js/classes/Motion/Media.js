class Media
{
    /**
     *  Download selected media(s)
     */
    download(checkboxes)
    {
        var files = [];

        // Get selected media Id
        $(checkboxes).each(function () {
            files.push($(this).attr('file-name'));
        });

        // Append a temporary <a> element to download files
        var temporaryDownloadLink = document.createElement('a');
        temporaryDownloadLink.style.display = 'none';

        document.body.appendChild(temporaryDownloadLink);

        for (var n = 0; n < files.length; n++) {
            var path = files[n];
            var filename = path.split('/').pop();

            // Set the href attribute to the file path, also include the filename as a query parameter for the Android app
            temporaryDownloadLink.setAttribute('href', '/media/' + path + '?filename=' + encodeURIComponent(filename));

            // Set the download attribute to force download
            temporaryDownloadLink.setAttribute('download', filename);

            // Trigger click on the temporary <a> element to start download
            temporaryDownloadLink.click();
        }

        // Remove temporary <a> element
        document.body.removeChild(temporaryDownloadLink);
    }

    /**
     *  Delete selected media(s)
     */
    delete(checkboxes)
    {
        var mediaId = [];

        // Get selected media Id
        $(checkboxes).each(function () {
            var id = $(this).attr('file-id');
            mediaId.push(id);
        });

        // Print confirm box to delete selected media(s)
        setTimeout(function () {
            myconfirmbox.print(
                {
                    'id': 'delete-media',
                    'title': 'Delete medias',
                    'message': 'Are you sure you want to delete ' + mediaId.length + ' media' + (mediaId.length > 1 ? 's' : '') + ' ?',
                    'buttons': [
                    {
                        'text': 'Delete',
                        'color': 'red',
                        'callback': function () {
                            ajaxRequest(
                                // Controller:
                                'motion',
                                // Action:
                                'deleteFile',
                                // Data:
                                {
                                    mediaId: mediaId
                                },
                                // Print success alert:
                                true,
                                // Print error alert:
                                true
                            ).then(function () {
                                mycontainer.reload('motion/events/list');
                            });
                        }
                    }]
                }
            );
        }, 10);
    }
}