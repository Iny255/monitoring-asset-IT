<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleSettingController extends Controller
{
    /**
     * Display the module settings and permission matrix.
     */
    public function index()
    {
        $roles = Role::orderBy('is_system', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        $modulesGrouped = Module::orderBy('order', 'asc')
            ->get()
            ->groupBy('group');

        // Ambil pemetaan role_module: [role_id => [module_id_1, module_id_2, ...]]
        $roleModules = DB::table('role_module')
            ->get()
            ->groupBy('role_id')
            ->map(function ($items) {
                return $items->pluck('module_id')->toArray();
            })
            ->toArray();

        return view('content.dashboard.settings.modules.index', compact('roles', 'modulesGrouped', 'roleModules'));
    }

    /**
     * Update the permission matrix.
     */
    public function updateMatrix(Request $request)
    {
        $matrix = $request->input('matrix', []);
        $allModules = Module::all();
        $allRoles = Role::all();

        DB::transaction(function () use ($matrix, $allModules, $allRoles) {
            foreach ($allRoles as $role) {
                // Super Admin selalu memiliki semua akses modul
                if ($role->name === 'super_admin') {
                    $role->modules()->sync($allModules->pluck('id'));
                    continue;
                }

                // Ambil module ID yang dicentang untuk role ini
                $checkedModuleIds = isset($matrix[$role->id]) ? array_keys($matrix[$role->id]) : [];
                $role->modules()->sync($checkedModuleIds);
            }
        });

        return redirect()->route('settings.modules.index')
            ->with('success', 'Matriks perizinan modul dan role berhasil diperbarui.');
    }

    /**
     * Toggle the active status of a module.
     */
    public function toggleModuleStatus(Request $request, string $id)
    {
        $module = Module::findOrFail($id);

        $module->is_active = !$module->is_active;
        $module->save();

        return response()->json([
            'success' => true,
            'is_active' => $module->is_active,
            'message' => "Modul \"{$module->name}\" berhasil " . ($module->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.',
        ]);
    }

    /**
     * Update roles that are authorized to manage settings.
     */
    public function updateSettingManagers(Request $request)
    {
        $authorizedRoleIds = $request->input('setting_roles', []);

        DB::transaction(function () use ($authorizedRoleIds) {
            $roles = Role::all();

            foreach ($roles as $role) {
                if ($role->name === 'super_admin') {
                    $role->can_manage_settings = true;
                } else {
                    $role->can_manage_settings = in_array($role->id, $authorizedRoleIds);
                }
                $role->save();
            }
        });

        return redirect()->route('settings.modules.index')
            ->with('success', 'Daftar role pengelola pengaturan sistem berhasil diperbarui.');
    }
}
