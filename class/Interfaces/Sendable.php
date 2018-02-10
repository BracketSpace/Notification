<?php

namespace underDEV\Notification\Interfaces;

interface Sendable extends Nameable {

	/**
	 * Sends the notification
     *
	 * @param  \underDEV\Notification\Abstracts\Trigger $trigger trigger objecy
	 * @return void
	 */
    public function send( \underDEV\Notification\Abstracts\Trigger $trigger );

    /**
     * Generates an unique hash for notification instance
     *
     * @return string
     */
    public function hash();

}
