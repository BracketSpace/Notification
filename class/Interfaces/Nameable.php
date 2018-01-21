<?php

namespace underDEV\Notification\Interfaces;

interface Nameable {

	/**
     * Gets name
     * @return string name
     */
    public function get_name();

    /**
     * Gets slug
     * @return string slug
     */
    public function get_slug();

}
