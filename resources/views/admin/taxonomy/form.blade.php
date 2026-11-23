{{-- Kategori / Yazar / Yayınevi ekleme-düzenleme için ORTAK form --}}
@extends('layouts.admin')

@section('title', ($item->exists ? 'Düzenle: ' . $item->name : 'Yeni ' . $labels['tekil']))

@section('content')
    <div class="card" style="max-width: 720px">
        <div class="card-body">
            <form action="{{ $item->exists ? route("admin.$route.update", $item->id) : route("admin.$route.store") }}" method="POST" novalidate>
                @csrf
                @if ($item->exists) @method('PUT') @endif

                @foreach ($fields as $name => $field)
                    @if ($field['type'] === 'checkbox')
                        <label class="checkbox mb-2">
                            <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $item->exists ? $item->$name : true))>
                            {{ $field['label'] }}
                        </label>
                    @else
                        <x-field :name="$name" :label="$field['label']" :type="$field['type']" :value="$item->$name"
                                 :required="str_contains($field['rules'], 'required')" />
                    @endif
                @endforeach

                <div class="form-actions">
                    <a href="{{ route("admin.$route.index") }}" class="btn btn-outline">Vazgeç</a>
                    <button class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
