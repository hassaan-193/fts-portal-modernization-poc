<?php

return [
    'singular' => 'Letter',
    'plural' => 'Letters',
    'fields' => [
        'id' => 'ID',
        'title' => 'Title',
        'staff_profile_id' => 'Staff Profile',
        'type' => 'Type',
        'issued_by' => 'Issued By',
        'issued_at' => 'Issued At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],
    'types' => [
        'warning' => 'Warning',
        'appreciation' => 'Appreciation',
    ],
    'actions' => [
        'create' => 'Create Letter',
        'edit' => 'Edit Letter',
        'delete' => 'Delete Letter',
        'view' => 'View Letter',
        'back_to_list' => 'Back to List',
    ],
    'messages' => [
        'created' => 'Letter has been successfully created.',
        'updated' => 'Letter has been successfully updated.',
        'deleted' => 'Letter has been successfully deleted.',
        'not_found' => 'Letter not found.',
    ],
];
