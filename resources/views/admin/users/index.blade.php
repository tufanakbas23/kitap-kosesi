@extends('layouts.admin')

@section('title', 'Kullanıcılar')

@section('content')
    <div class="card">
        <form method="GET" class="filter-bar">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ad, soyad veya e-posta...">
            <select name="rol" class="form-control">
                <option value="">Tüm roller</option>
                <option value="musteri" @selected(request('rol') === 'musteri')>Müşteri</option>
                <option value="admin" @selected(request('rol') === 'admin')>Yönetici</option>
            </select>
            <button class="btn btn-dark btn-sm">Filtrele</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>#</th><th>Ad Soyad</th><th>E-posta</th><th>Telefon</th><th>Sipariş</th><th>Harcama</th><th>Kayıt</th><th>Durum</th><th class="text-right">İşlemler</th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-muted">{{ $user->id }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="fw-bold">{{ $user->full_name }}</a>
                                @if ($user->is_admin) <span class="badge badge-primary">Yönetici</span> @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>{{ $user->orders_count }}</td>
                            <td>@money($user->spent ?? 0)</td>
                            <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline btn-sm">Detay</a>
                                    @if ($user->id != auth()->id())
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">{{ $user->is_active ? 'Pasif Yap' : 'Aktif Yap' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $users->links() }}
@endsection
