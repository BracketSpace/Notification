<?php
/**
 * Triggerable interface class
 *
 * @package notification
 */

namespace underDEV\Notification\Interfaces;

interface Triggerable extends Nameable {

    public function attach( Sendable $notification );
    public function detach( Sendable $notification );

}
