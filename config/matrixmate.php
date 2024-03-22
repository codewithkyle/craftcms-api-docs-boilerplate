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
