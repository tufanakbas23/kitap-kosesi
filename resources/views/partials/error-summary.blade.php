@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <span>✖</span>
        <div>
            <strong>Lütfen aşağıdaki hataları düzeltin:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
