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

namespace LmcRbacMvc\Assertion;

use Laminas\ServiceManager\AbstractPluginManager;
use LmcRbacMvc\Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Plugin manager to create assertions
 *
 * @author  Aeneas Rekkas
 * @license MIT
 *
 * @method AssertionInterface get($name, $options=null)
 */
class AssertionPluginManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validate($instance): void
    {
        if ($instance instanceof AssertionInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Assertions must implement "LmcRbacMvc\Assertion\AssertionInterface", but "%s" was given',
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }

    /**
     * @param $instance
     * @return void
     * @throws ContainerExceptionInterface
     * @deprecated Use method validate instead
     */
    public function validatePlugin($instance): void
    {
        $this->validate($instance);
    }

    /**
     * {@inheritDoc}
     */
    protected function canonicalizeName($name)
    {
        return $name;
    }
}
