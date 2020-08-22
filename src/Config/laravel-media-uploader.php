<?php

return [
    /*
     * Regenerate uploaded media after assign to model
     */
    'regenerate-after-assigning' => true,

    'documents_mime_types' => [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .doc & .docx
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .ppt & .pptx
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xls & .xlsx
        'text/plain',
        'application/pdf',
        'application/zip',
        'application/x-rar',
        'application/x-rar-compressed',
        'application/octet-stream',
    ],
];
