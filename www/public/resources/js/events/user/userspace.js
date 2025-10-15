/**
 *  Event: edit user personnal informations
 */
$(document).on('submit','#user-edit-info',function (e) {
    e.preventDefault();

    var firstName = $('#user-edit-info').find('input[type=text][name=first-name]').val();
    var lastName = $('#user-edit-info').find('input[type=text][name=last-name]').val();
    var email = $('#user-edit-info').find('input[type=email][name=email]').val();

    ajaxRequest(
        // Controller:
        'userspace',
        // Action:
        'edit',
        // Data:
        {
            firstName: firstName,
            lastName: lastName,
            email: email
        },
        // Print success alert:
        true,
        // Print error alert:
        true
    );

    return false;
});

/**
 *  Event: change password
 */
$(document).on('submit','#new-password-form',function () {
    event.preventDefault();

    var id = $(this).attr('user-id');
    var currentPassword = $(this).find('input[name=actual-password]').val();
    var newPassword = $(this).find('input[name=new-password]').val();
    var newPasswordRetype = $(this).find('input[name=new-password-retype]').val();

    ajaxRequest(
        // Controller:
        'userspace',
        // Action:
        'change-password',
        // Data:
        {
            id: id,
            currentPassword: currentPassword,
            newPassword: newPassword,
            newPasswordRetype: newPasswordRetype
        },
        // Print success alert:
        true,
        // Print error alert:
        true
    );

    return false;
});

/**
 *  Event: create a new user
 */
$(document).on('submit','#new-user-form',function () {
    event.preventDefault();

    var username = $(this).find('input[name=username]').val();
    var role = $(this).find('select[name=role]').val();

    ajaxRequest(
        // Controller:
        'userspace',
        // Action:
        'create-user',
        // Data:
        {
            username: username,
            role: role
        },
        // Print success alert:
        false,
        // Print error alert:
        true
    ).then(function () {
        // Reload userspace panel
        mypanel.reload('general/user/userspace');
    });

    return false;
});

/**
 *  Event: reset user password
 */
$(document).on('click','.reset-password-btn',function () {
    var username = $(this).attr('username');
    var id = $(this).attr('user-id');

    myconfirmbox.print(
        {
            'title': 'Reset password',
            'message': 'Reset password of user ' + username + '?',
            'buttons': [
            {
                'text': 'Reset',
                'color': 'red',
                'callback': function () {
                    ajaxRequest(
                        // Controller:
                        'userspace',
                        // Action:
                        'reset-password',
                        // Data:
                        {
                            id: id
                        },
                        // Print success alert:
                        false,
                        // Print error alert:
                        true
                    ).then(function () {
                        // Print new generated password
                        $('#users-settings-container').find('#user-settings-generated-passwd').html('<p class="note margin-top-5">New password generated for <b>' + username + '</b>:<br><span class="greentext copy">' + jsonValue.message.password + '</span></p>');
                    });
                }
            }]
        }
    );
});

/**
 *  Event: delete user
 */
$(document).on('click','.delete-user-btn',function () {
    var username = $(this).attr('username');
    var id = $(this).attr('user-id');

    myconfirmbox.print(
        {
            'title': 'Delete user',
            'message': 'Delete user ' + username + '?',
            'buttons': [
            {
                'text': 'Delete',
                'color': 'red',
                'callback': function () {
                    ajaxRequest(
                        // Controller:
                        'userspace',
                        // Action:
                        'delete-user',
                        // Data:
                        {
                            id: id
                        },
                        // Print success alert:
                        true,
                        // Print error alert:
                        true
                    ).then(function () {
                        // Reload userspace panel
                        mypanel.reload('general/user/userspace');
                    });
                }
            }]
        }
    );
});
