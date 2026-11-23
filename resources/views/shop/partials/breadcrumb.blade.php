{{-- $items = [['Etiket', 'url veya null'], ...] --}}
<nav class="breadcrumb" aria-label="Konum">
    <a href="{{ route('home') }}">Ana Sayfa</a>
    @foreach ($items as [$label, $url])
        <span class="sep">›</span>
        @if ($url)
            <a href="{{ $url }}">{{ $label }}</a>
        @else
            <span>{{ $label }}</span>
        @endif
    @endforeach
</nav>
