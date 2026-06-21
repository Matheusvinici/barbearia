@extends('layouts.app')
@section('title', 'Papéis e Permissões')
@section('breadcrumb', 'Papéis')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Novo Papel</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Nome do Papel</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: supervisor" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tipo</label>
                    <select name="guard_name" class="form-control" id="guardSelect">
                        <option value="web">Administração</option>
                        <option value="barbeiro">Barbeiro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Permissões (<span id="permCount">0</span> selecionadas)</label>
                    <div class="d-flex gap-2 mb-1">
                        <button type="button" class="btn btn-xs btn-outline-success" onclick="marcarTodas(true)">Marcar todas</button>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="marcarTodas(false)">Desmarcar todas</button>
                    </div>
                    <div id="permList" class="border rounded p-2" style="max-height:200px;overflow-y:auto">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Criar Papel</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Papéis Existentes</h5></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nome</th><th>Tipo</th><th>Permissões</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($roles as $r)
                <tr>
                    <td><strong>{{ $r->name }}</strong></td>
                    <td><span class="badge {{ $r->guard_name === 'web' ? 'bg-primary' : 'bg-info' }}">{{ $r->guard_name }}</span></td>
                    <td>
                        @foreach($r->permissions as $p)
                        <span class="badge bg-secondary">{{ $p->name }}</span>
                        @endforeach
                        @if($r->permissions->isEmpty())
                        <small class="text-muted">Nenhuma permissão</small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        @if($r->name !== 'admin')
                        <button onclick="confirmarExclusao('{{ route('admin.roles.destroy', $r) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
const allPermissions = {
    @foreach($guards as $g)
    '{{ $g }}': {!! json_encode(\Spatie\Permission\Models\Permission::where('guard_name', $g)->orderBy('name')->get()->map(fn($p) => ['id' => $p->id, 'name' => $p->name])) !!},
    @endforeach
};

function renderPerms(guard) {
    const list = document.getElementById('permList');
    const perms = allPermissions[guard] || [];
    list.innerHTML = perms.map(p =>
        `<div class="form-check form-check-inline">
            <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="${p.id}" id="perm${p.id}">
            <label class="form-check-label small" for="perm${p.id}">${p.name}</label>
        </div>`
    ).join('');
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.perm-check:checked').length;
    document.getElementById('permCount').textContent = checked;
}

function marcarTodas(marcar) {
    document.querySelectorAll('.perm-check').forEach(c => c.checked = marcar);
    updateCount();
}

document.getElementById('guardSelect').addEventListener('change', function() {
    renderPerms(this.value);
});

renderPerms('web');
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('perm-check')) updateCount();
});
</script>
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}
</script>
@endpush
@endsection
