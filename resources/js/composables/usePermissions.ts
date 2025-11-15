import { usePage } from '@inertiajs/vue3';
import { computed, ComputedRef } from 'vue';
// Assuming 'index.d.ts' is aliased or available via a path like '@/types'
// Please adjust this import path to match your project's setup.
import { AppPageProps, User } from '@/types';

// --- Type Definitions ---

/**
 * Defines the return type of the usePermissions composable.
 */
interface UsePermissionsReturn {
    user: ComputedRef<User | {}>;
    roles: ComputedRef<string[]>;
    permissions: ComputedRef<string[]>;
    hasRole: (roleName: string) => boolean;
    hasPermission: (permissionName: string) => boolean;
    hasAnyPermission: (permissionNames: string[]) => boolean;
    hasAllPermissions: (permissionNames: string[]) => boolean;
}

// --- Composable Implementation ---

/**
 * A Vue composable to easily check user roles and permissions
 * from Inertia's shared page props.
 */
export function usePermissions(): UsePermissionsReturn {
    // Use the imported AppPageProps for strong typing
    const page = usePage<AppPageProps>();

    /**
     * The authenticated user object. Defaults to an empty object if not logged in.
     */
    const user = computed<User | {}>(() => page.props.auth?.user || {});

    /**
     * A list of roles assigned to the current user.
     * Reads from page.props.auth.roles
     */
    const roles = computed<string[]>(() => page.props.auth?.roles || []);

    /**
     * A list of permissions assigned to the current user.
     * Reads from page.props.auth.permissions
     */
    const permissions = computed<string[]>(
        () => page.props.auth?.permissions || [],
    );

    /**
     * Checks if the user has a specific role.
     * @param roleName - The name of the role to check.
     * @returns True if the user has the role, false otherwise.
     */
    const hasRole = (roleName: string): boolean => {
        return roles.value.includes(roleName);
    };

    /**
     * Checks if the user has a specific permission.
     * @param permissionName - The name of the permission to check.
     * @returns True if the user has the permission, false otherwise.
     */
    const hasPermission = (permissionName: string): boolean => {
        return permissions.value.includes(permissionName);
    };

    /**
     * Checks if the user has at least one of the specified permissions.
     * @param permissionNames - An array of permission names to check.
     * @returns True if the user has any of the permissions, false otherwise.
     */
    const hasAnyPermission = (permissionNames: string[]): boolean => {
        return permissionNames.some((permission) =>
            permissions.value.includes(permission),
        );
    };

    /**
     * Checks if the user has all of the specified permissions.
     * @param permissionNames - An array of permission names to check.
     * @returns True if the user has all of the permissions, false otherwise.
     */
    const hasAllPermissions = (permissionNames: string[]): boolean => {
        return permissionNames.every((permission) =>
            permissions.value.includes(permission),
        );
    };

    return {
        user,
        roles,
        permissions,
        hasRole,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
    };
}
