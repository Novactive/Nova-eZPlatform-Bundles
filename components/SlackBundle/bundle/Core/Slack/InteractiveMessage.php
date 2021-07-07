<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Slack;

class InteractiveMessage
{
    /**
     * An array of actions that were clicked, including the name and value of the actions, as you prepared when
     * creating your message buttons. Though presented as an array, at this time you'll only receive a single action
     * per incoming invocation.
     */
    private array $actions;

    /**
     * This is the same string you received when configuring your application for interactive message support,
     * presented to you on an app details page. Validate this to ensure the request is coming to you from Slack.
     */
    private string $token;

    /**
     * The array of blocks sent with initial slack message.
     */
    private array $blocks;

    /**
     * A string containing a URL, used to respond to this invocation independently from the triggering of your action
     * URL.
     */
    private string $responseURL;

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function getAction(): array
    {
        return $this->actions[0];
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getResponseURL(): string
    {
        return $this->responseURL;
    }

    public function setResponseURL(string $responseURL): self
    {
        $this->responseURL = $responseURL;

        return $this;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;

        return $this;
    }
}
