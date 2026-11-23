{{--
    Ortak form alanı: etiket + girdi + hata mesajı
    Kullanım:
      <x-field name="email" label="E-posta" type="email" :value="$user->email" required />
      <x-field name="city" label="İl" type="select" :options="['Ankara' => 'Ankara']" />
      <x-field name="bio" label="Biyografi" type="textarea" />
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'options' => [],
    'placeholder' => '',
    'hint' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id', $name);
    $current = old($name, $value);
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" @required($required)
            {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ $current }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" @required($required)
            {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
            <option value="">{{ $placeholder ?: 'Seçiniz' }}</option>
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected((string) $current === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : $current }}"
            placeholder="{{ $placeholder }}" @required($required)
            {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
    @endif

    @if ($hint)
        <small class="form-hint">{{ $hint }}</small>
    @endif
    @error($name)
        <small class="form-error">{{ $message }}</small>
    @enderror
</div>
