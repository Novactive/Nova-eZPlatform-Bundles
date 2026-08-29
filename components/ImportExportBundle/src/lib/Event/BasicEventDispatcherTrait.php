<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use Closure;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\Debug\WrappedListener;

trait BasicEventDispatcherTrait
{
    /**
     * @var array<string, array<int, array<int, callable>>>
     */
    protected array $listeners = [];
    /**
     * @var array<string, array<int, callable>>
     */
    protected array $optimizedListeners = [];

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->optimizedListeners[$eventName]);
    }

    protected function dispatchEvent(object $event, string $eventName = null): void
    {
        $listeners = $this->optimizedListeners[$eventName] ??
                     (empty($this->listeners[$eventName]) ? [] :
                         $this->optimizeListeners($eventName));
        $stoppable = $event instanceof StoppableEventInterface;
        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            $listener($event, $eventName, $this);
        }
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedLocalVariable")
     *
     * @return array<int, callable>
     */
    private function optimizeListeners(string $eventName): array
    {
        krsort($this->listeners[$eventName]);
        $this->optimizedListeners[$eventName] = [];

        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {
                $closure = &$this->optimizedListeners[$eventName][];
                if (
                    \is_array($listener) &&
                    isset($listener[0]) && $listener[0] instanceof Closure && 2 >= \count($listener)
                ) {
                    $closure = static function (...$args) use (&$listener, &$closure) {
                        if ($listener[0] instanceof Closure) {
                            $listener[0] = $listener[0]();
                            $listener[1] = $listener[1] ?? '__invoke';
                        }
                        ($closure = Closure::fromCallable($listener))(...$args);
                    };
                } else {
                    $closure = $listener instanceof Closure || $listener instanceof WrappedListener ?
                        $listener :
                        Closure::fromCallable($listener);
                }
            }
        }

        return $this->optimizedListeners[$eventName];
    }
}
