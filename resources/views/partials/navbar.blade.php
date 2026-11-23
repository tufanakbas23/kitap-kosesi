<nav class="main-nav">
    <div class="container">
        <ul>
            <li><a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.index') ? 'active' : '' }}">Tüm Kitaplar</a></li>
            @foreach ($navCategories->take(9) as $cat)
                <li>
                    <a href="{{ route('categories.show', $cat) }}"
                       class="{{ request()->route('category')?->id === $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
                </li>
            @endforeach
            <li><a href="{{ route('books.discounted') }}" class="highlight">🔥 İndirimler</a></li>
        </ul>
    </div>
</nav>
