<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['key' => 'dashboard.view', 'group' => 'Dashboard', 'label' => 'Voir le tableau de bord'],

            // Provinces
            ['key' => 'provinces.view', 'group' => 'Provinces', 'label' => 'Voir les provinces'],
            ['key' => 'provinces.create', 'group' => 'Provinces', 'label' => 'Créer une province'],
            ['key' => 'provinces.edit', 'group' => 'Provinces', 'label' => 'Modifier une province'],
            ['key' => 'provinces.delete', 'group' => 'Provinces', 'label' => 'Supprimer une province'],

            // Utilisateurs
            ['key' => 'users.view', 'group' => 'Utilisateurs', 'label' => 'Voir les utilisateurs'],
            ['key' => 'users.create', 'group' => 'Utilisateurs', 'label' => 'Créer un utilisateur'],
            ['key' => 'users.edit', 'group' => 'Utilisateurs', 'label' => 'Modifier un utilisateur'],
            ['key' => 'users.delete', 'group' => 'Utilisateurs', 'label' => 'Supprimer un utilisateur'],
            ['key' => 'users.manage-permissions', 'group' => 'Utilisateurs', 'label' => 'Gérer les permissions'],

            // Conducteurs
            ['key' => 'conducteurs.view', 'group' => 'Conducteurs', 'label' => 'Voir les conducteurs'],
            ['key' => 'conducteurs.create', 'group' => 'Conducteurs', 'label' => 'Enregistrer un conducteur'],
            ['key' => 'conducteurs.edit', 'group' => 'Conducteurs', 'label' => 'Modifier un conducteur'],
            ['key' => 'conducteurs.delete', 'group' => 'Conducteurs', 'label' => 'Supprimer un conducteur'],

            // Engins
            ['key' => 'engins.view', 'group' => 'Engins', 'label' => 'Voir les engins'],
            ['key' => 'engins.create', 'group' => 'Engins', 'label' => 'Enregistrer un engin'],
            ['key' => 'engins.edit', 'group' => 'Engins', 'label' => 'Modifier un engin'],
            ['key' => 'engins.delete', 'group' => 'Engins', 'label' => 'Supprimer un engin'],

            // Débardeurs
            ['key' => 'debardeurs.view', 'group' => 'Débardeurs', 'label' => 'Voir les débardeurs'],
            ['key' => 'debardeurs.create', 'group' => 'Débardeurs', 'label' => 'Enregistrer un débardeur'],
            ['key' => 'debardeurs.edit', 'group' => 'Débardeurs', 'label' => 'Modifier un débardeur'],
            ['key' => 'debardeurs.delete', 'group' => 'Débardeurs', 'label' => 'Supprimer un débardeur'],

            // Permis
            ['key' => 'permis.view', 'group' => 'Permis', 'label' => 'Voir les permis'],

            // Certificats
            ['key' => 'certificats.view', 'group' => 'Certificats', 'label' => 'Voir les certificats'],

            // Taxes
            ['key' => 'taxes.view', 'group' => 'Taxes', 'label' => 'Voir les taxes'],
            ['key' => 'taxes.create', 'group' => 'Taxes', 'label' => 'Créer une taxe'],
            ['key' => 'taxes.edit', 'group' => 'Taxes', 'label' => 'Modifier une taxe'],
            ['key' => 'taxes.delete', 'group' => 'Taxes', 'label' => 'Supprimer une taxe'],

            // Paiements
            ['key' => 'paiements.view', 'group' => 'Paiements', 'label' => 'Voir les paiements'],
            ['key' => 'paiements.create', 'group' => 'Paiements', 'label' => 'Créer un paiement'],
            ['key' => 'paiements.encaisser', 'group' => 'Paiements', 'label' => 'Encaisser un paiement'],
            ['key' => 'paiements.edit', 'group' => 'Paiements', 'label' => 'Modifier un paiement'],
            ['key' => 'paiements.delete', 'group' => 'Paiements', 'label' => 'Supprimer un paiement'],
            ['key' => 'paiements.print', 'group' => 'Paiements', 'label' => 'Imprimer un reçu'],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(
                ['key' => $data['key']],
                $data
            );
        }

        $this->seedRoleDefaults();
    }

    private function seedRoleDefaults(): void
    {
        $allPermissionKeys = Permission::pluck('key')->toArray();

        $roleMap = [
            'superadmin' => $allPermissionKeys,
            'admin' => $allPermissionKeys,
            'admin-region' => $allPermissionKeys,
            'manager' => [
                'dashboard.view',
                'provinces.view',
                'conducteurs.view', 'conducteurs.create', 'conducteurs.edit',
                'engins.view', 'engins.create', 'engins.edit',
                'debardeurs.view', 'debardeurs.create', 'debardeurs.edit',
                'permis.view',
                'certificats.view',
                'taxes.view', 'taxes.create', 'taxes.edit',
                'paiements.view', 'paiements.create', 'paiements.encaisser', 'paiements.edit', 'paiements.print',
            ],
            'secretaire' => [
                'dashboard.view',
                'conducteurs.view', 'conducteurs.create', 'conducteurs.edit',
                'engins.view', 'engins.create', 'engins.edit',
                'debardeurs.view', 'debardeurs.create', 'debardeurs.edit',
                'permis.view',
                'certificats.view',
                'taxes.view',
                'paiements.view', 'paiements.create', 'paiements.print',
            ],
            'user' => [
                'dashboard.view',
                'conducteurs.view',
                'engins.view',
                'debardeurs.view',
                'permis.view',
                'certificats.view',
            ],
        ];

        foreach ($roleMap as $role => $keys) {
            $permissionIds = Permission::whereIn('key', $keys)->pluck('id');

            foreach ($permissionIds as $permId) {
                RolePermission::firstOrCreate([
                    'role' => $role,
                    'permission_id' => $permId,
                ]);
            }
        }
    }
}
