<?php

return [
    'fields' => [
        'ticket' => [
            'title' => 'Título',
            'description' => 'Descrição',
            'ticketStatus' => [
                'name' => __('Status'),
            ],
            'unit' => [
                'name' => __('Unit'),
            ],
            'owner' => [
                'name' => __('Owner'),
            ],
            'category' => [
                'name' => __('Category'),
            ],
            'priority' => [
                'name' => __('Priority'),
            ],
            'responsible' => [
                'name' => __('Responsible'),
            ],
        ],

        'comment' => [
            'comment' => 'Comentário',
            'attachments' => 'Anexos',
            'tiket_id' => 'Ticket #',
        ]
    ],
];