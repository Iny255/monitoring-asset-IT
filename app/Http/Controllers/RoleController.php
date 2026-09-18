<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $roles = Role::withCount('users')
            ->with('modules')
            ->orderBy('is_system', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        $modules = Module::where('is_active', true)
            ->orderBy('group', 'asc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('group');

        return view('content.dashboard.settings.roles.index', compact('roles', 'modules'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        // Otomatis bersihkan dan slugify name sebelum validasi
        $rawName = trim((string) $request->input('name', ''));
        $slug = Str::slug($rawName, '_');
        $request->merge(['name' => $slug]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:roles,name'
            ],
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'can_manage_settings' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ], [
            'name.required' => 'Kode role wajib diisi.',
            'name.unique' => "Kode role '{$slug}' sudah digunakan.",
            'name.regex' => 'Kode role hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'display_name.required' => 'Nama tampilan role wajib diisi.',
        ]);

        $role = Role::create([
            'name' => $slug,
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'can_manage_settings' => $request->boolean('can_manage_settings'),
            'is_system' => false,
        ]);

        if (!empty($validated['modules'])) {
            $role->modules()->sync($validated['modules']);
        }

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil ditambahkan.");
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'can_manage_settings' => 'nullable|boolean',
        ]);

        $role->display_name = $validated['display_name'];
        $role->description = $validated['description'] ?? null;

        // Super Admin wajib selalu true
        if ($role->name === 'super_admin') {
            $role->can_manage_settings = true;
        } else {
            $role->can_manage_settings = $request->boolean('can_manage_settings');
        }

        $role->save();

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            return redirect()->route('settings.roles.index')
                ->with('error', "Role sistem \"{$role->display_name}\" terkunci dan tidak dapat dihapus.");
        }

        $userCount = User::where('role', $role->name)->count();
        if ($userCount > 0) {
            return redirect()->route('settings.roles.index')
                ->with('error', "Role \"{$role->display_name}\" masih digunakan oleh {$userCount} pengguna. Ubah role pengguna terkait terlebih dahulu.");
        }

        $displayName = $role->display_name;
        $role->modules()->detach();
        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$displayName}\" berhasil dihapus.");
    }

    /**
     * Toggle can_manage_settings for a role.
     */
    public function toggleSettingAccess(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hak akses pengaturan Super Admin tidak dapat dinonaktifkan.',
            ], 422);
        }

        $role->can_manage_settings = !$role->can_manage_settings;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => "Hak akses pengaturan untuk \"{$role->display_name}\" berhasil " . ($role->can_manage_settings ? 'diaktifkan' : 'dinonaktifkan') . '.',
            'can_manage_settings' => $role->can_manage_settings,
        ]);
    }
}
