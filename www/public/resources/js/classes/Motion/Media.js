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
            files.push({ fileId: $(this).attr('file-id'), filename: $(this).attr('file-name') });
        });

        // Append a temporary <a> element to download files
        var temporaryDownloadLink = document.createElement('a');
        temporaryDownloadLink.style.display = 'none';

        document.body.appendChild(temporaryDownloadLink);

        for (var n = 0; n < files.length; n++) {
            var download = files[n];

            // Set the href attribute to the file path, also include the filename for the android app to make sure it downloads the file with the correct name
            temporaryDownloadLink.setAttribute('href', '/media?id=' + download.fileId + '&filename=' + download.filename);
            // Set the download attribute to force download
            temporaryDownloadLink.setAttribute('download', download.filename);
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