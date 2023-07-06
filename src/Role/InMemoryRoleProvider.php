<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LmcRbacMvc\Role;

use Laminas\Permissions\Rbac\RoleInterface;

/**
 * Simple role providers that store them in memory (ideal for small websites)
 *
 * This provider expects role to be specified using string only. The format is as follow:
 *
 *  [
 *      'myRole' => [
 *          'children'    => ['subRole1', 'subRole2'], // OPTIONAL
 *          'permissions' => ['permission1'] // OPTIONAL
 *      ]
 *  ]
 *
 * For maximum performance, this provider DOES NOT do a lot of type check, so you must closely
 * follow the format :)
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class InMemoryRoleProvider implements RoleProviderInterface
{
    /**
     * Role storage
     *
     * @var array
     */
    private $roles = [];

    /**
     * Roles config
     *
     * @var array
     */
    private $rolesConfig = [];

    /**
     * @param array
     */
    public function __construct(array $rolesConfig)
    {
        $this->rolesConfig = $rolesConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(array $roleNames)
    {
        $roles = [];

        foreach ($roleNames as $roleName) {
            $roles[] = $this->getRole($roleName);
        }
        return $roles;
    }

    /**
     * Get role by role name
     *
     * @param $roleName
     * @return RoleInterface
     */
    protected function getRole($roleName)
    {
        if (isset($this->roles[$roleName])) {
            return $this->roles[$roleName];
        }

        // If no config, we create a simple role with no permission
        if (!isset($this->rolesConfig[$roleName])) {
            $role = new Role($roleName);
            $this->roles[$roleName] = $role;
            return $role;
        }

        $roleConfig = $this->rolesConfig[$roleName];

        if (isset($roleConfig['children'])) {
            $role = new Role($roleName);
            $childRoles = (array)$roleConfig['children'];
            foreach ($childRoles as $childRole) {
                $childRole = $this->getRole($childRole);
                $role->addChild($childRole);
            }
        } else {
            $role = new Role($roleName);
        }

        $permissions = isset($roleConfig['permissions']) ? $roleConfig['permissions'] : [];
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }

        $this->roles[$roleName] = $role;

        return $role;
    }
}
