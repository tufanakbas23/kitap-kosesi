@props(['rating' => 0])

<span class="stars" title="{{ $rating }} / 5">
    @for ($i = 1; $i <= 5; $i++)
        <span class="{{ $i <= round($rating) ? '' : 'off' }}">★</span>
    @endfor
</span>
