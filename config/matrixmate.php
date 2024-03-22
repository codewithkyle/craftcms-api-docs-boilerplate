<?php

return [
    'fields' => [
        'contentBuilder' => [
            'groups' => [
                [
                    'label' => 'Content',
                    'types' => ['heading', 'richText', 'link', 'code', 'info', 'quickAttributeList'],
                ],
                [
                    'label' => 'Code',
                    'types' => ['code', 'curlRequest'],
                ],
                [
                    'label' => 'External Content',
                    'types' => ['endpoint', 'model'],
                ],
                [
                    'label' => 'Other',
                    'types' => ['spacer', 'line'],
                ],
            ],
        ],
    ],
];
