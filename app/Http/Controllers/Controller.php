<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Authorize a permission for the current user.
     * 
     * @param string $permission
     * @param string|null $message
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizePermission(string $permission, ?string $message = null)
    {
        if (!auth()->user()->can($permission)) {
            abort(403, $message ?? 'You do not have permission to perform this action.');
        }
    }
}
