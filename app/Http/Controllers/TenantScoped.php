<?php

namespace App\Http\Controllers;

use App\Models\Barbearia;
use Illuminate\Support\Facades\Request;

trait TenantScoped
{
    protected function getTenant(): ?Barbearia
    {
        return request()->route('barbearia');
    }

    protected function tenantIds(): array
    {
        $tenant = $this->getTenant();
        if (!$tenant) {
            return [];
        }
        return $tenant->tenantTreeIds();
    }

    protected function tenantId(): ?int
    {
        $tenant = $this->getTenant();
        return $tenant?->id;
    }

    protected function applyTenantScope($query, string $column = 'barbearia_id')
    {
        $ids = $this->tenantIds();
        if (!empty($ids)) {
            return $query->whereIn($column, $ids);
        }
        return $query;
    }

    protected function isTenantContext(): bool
    {
        return (bool) $this->getTenant();
    }
}
