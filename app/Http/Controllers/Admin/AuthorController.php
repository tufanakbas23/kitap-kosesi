<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;

class AuthorController extends TaxonomyController
{
    protected function model(): string
    {
        return Author::class;
    }

    protected function routeName(): string
    {
        return 'authors';
    }

    protected function labels(): array
    {
        return ['tekil' => 'Yazar', 'cogul' => 'Yazarlar'];
    }

    protected function fields(): array
    {
        return [
            'name' => ['label' => 'Yazar Adı', 'type' => 'text', 'rules' => 'required|string|max:100'],
            'bio'  => ['label' => 'Biyografi', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
        ];
    }
}
