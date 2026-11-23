<?php

namespace App\Http\Controllers\Admin;

use App\Models\Publisher;

class PublisherController extends TaxonomyController
{
    protected function model(): string
    {
        return Publisher::class;
    }

    protected function routeName(): string
    {
        return 'publishers';
    }

    protected function labels(): array
    {
        return ['tekil' => 'Yayınevi', 'cogul' => 'Yayınevleri'];
    }

    protected function fields(): array
    {
        return [
            'name'        => ['label' => 'Yayınevi Adı', 'type' => 'text', 'rules' => 'required|string|max:100'],
            'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
        ];
    }
}
