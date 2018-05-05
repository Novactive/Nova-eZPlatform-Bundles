<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class AjaxGuard.
 */
class AjaxGuard
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AjaxGuard constructor.
     *
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param EntityManager             $entityManager
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, EntityManager $entityManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager    = $entityManager;
    }

    /**
     * @param mixed $class
     *
     * @return bool
     */
    private function isEntity($class): bool
    {
        if (\is_object($class)) {
            $class = ($class instanceof Proxy)
                ? get_parent_class($class)
                : \get_class($class);
        }

        return !$this->entityManager->getMetadataFactory()->isTransient($class);
    }

    /**
     * @param Request  $request
     * @param mixed    $subject
     * @param callable $callback
     *
     * @return array
     */
    public function execute(Request $request, $subject, callable $callback): array
    {
        $token = $request->request->get('token');
        if (!$request->isXmlHttpRequest() || null === $token ||
            !$this->isEntity($subject) ||
            !method_exists($subject, 'getId') ||
            !$this->csrfTokenManager->isTokenValid(new CsrfToken($subject->getId(), $token))) {
            throw new AccessDeniedHttpException('Not Allowed');
        }
        $results = $callback($subject);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        return ['token' => $this->csrfTokenManager->getToken($subject->getId())->getValue()] + $results;
    }
}
