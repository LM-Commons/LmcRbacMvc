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

namespace Lmc\Rbac\Mvc\Role;

use ArrayIterator;
use Lmc\Rbac\Role\RoleInterface;
use RecursiveIterator;
use Traversable;

class RecursiveRoleIterator extends ArrayIterator implements RecursiveIterator
{
    /**
     * Override constructor to accept {@link Traversable} as well
     *
     * @param RoleInterface[]|Traversable $roles
     */
    public function __construct(iterable $roles)
    {
        if ($roles instanceof Traversable) {
            $roles = iterator_to_array($roles);
        }
        
        parent::__construct($roles);
    }
    
    /**
     * @return bool
     */
    public function valid() : bool
    {
        return ($this->current() instanceof RoleInterface);
    }
    
    /**
     * @return bool
     */
    public function hasChildren() : bool
    {
        $current = $this->current();
        
        if (!$current instanceof RoleInterface) {
            return false;
        }
        return !empty($current->getChildren());
    }

    /**
     * @return RecursiveRoleIterator|null
     */
    public function getChildren() :? RecursiveRoleIterator
    {
        return new RecursiveRoleIterator($this->current()->getChildren());
    }
}
